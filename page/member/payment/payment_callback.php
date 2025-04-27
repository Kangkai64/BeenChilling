<?php
require '../../../_base.php';

// Billplz API credentials
$api_key = 'c4829771-97fd-40ee-a49f-10385d8f587b';
$x_signature_key = '08251797c8178a8bd90a55eb721f622cb59b5f17424e25280b278a6bc9b09365350c95aa6faac986fd01c2d282d7539bcd79b4075377e9a74ffdb856f7175810'; 

// Create the log directory if it doesn't exist
$log_dir = __DIR__;
$log_file = $log_dir . '/payment_debug.log';

// Detailed logging function - write to file with proper path
function log_payment_debug($message, $data = []) {
    global $log_file;
    
    // Make sure directory is writable
    if (!is_writable(dirname($log_file))) {
        // Try to make it writable
        chmod(dirname($log_file), 0755);
    }
    
    // Fallback to system temp directory if we can't write to current directory
    if (!is_writable(dirname($log_file))) {
        $log_file = sys_get_temp_dir() . '/billplz_payment_debug.log';
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$message}" . (empty($data) ? '' : ': ' . json_encode($data, JSON_PRETTY_PRINT)) . PHP_EOL;
    
    // Write to file
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    // Also log to PHP error log as backup
    error_log("BILLPLZ: " . $message . (empty($data) ? '' : ' - ' . json_encode($data)));
}

// Log the script execution start and file path
log_payment_debug("Script started", ['log_file' => $log_file, 'script_path' => __FILE__]);

// Function to verify order exists before updating
function verify_order_exists($order_id) {
    global $_db;
    try {
        $stm = $_db->prepare('SELECT order_id FROM `order` WHERE order_id = ?');
        $stm->execute([$order_id]);
        return $stm->fetch(PDO::FETCH_OBJ) ? true : false;
    } catch (Exception $e) {
        log_payment_debug("Database error checking order", ['error' => $e->getMessage()]);
        return false;
    }
}

// Direct database update function for debugging
function direct_update_order($order_id) {
    global $_db;
    try {
        $stm = $_db->prepare('
            UPDATE `order` 
            SET payment_status = "paid", 
                order_status = "processing",
                payment_date = NOW()
            WHERE order_id = ?
        ');
        $stm->execute([$order_id]);
        
        // Also create log entry
        $stm = $_db->prepare('
            INSERT INTO payment_logs (order_id, status, raw_data, created_at)
            VALUES (?, ?, ?, NOW())
        ');
        $stm->execute([$order_id, 'paid', json_encode(['direct_update' => true])]);
        
        // Get order details and add points
        try {
            $stm = $_db->prepare('
                SELECT o.member_id, o.cart_id, o.total_amount, u.reward_point 
                FROM `order` o
                JOIN user u ON o.member_id = u.id
                WHERE o.order_id = ?
            ');
            $stm->execute([$order_id]);
            $order = $stm->fetch(PDO::FETCH_OBJ);
            
            if ($order) {
                // Update the cart status
                $stm = $_db->prepare('UPDATE cart SET status = "completed" WHERE cart_id = ?');
                $stm->execute([$order->cart_id]);
                
                // Create a new cart for the member
                $stm = $_db->prepare('
                    INSERT INTO cart (member_id, created_at, status) 
                    VALUES (?, NOW(), "active")
                ');
                $stm->execute([$order->member_id]);
                
                // Calculate reward points (RM1 = 1 point, no rounding)
                $earned_points = floor($order->total_amount);
                $new_total_points = ($order->reward_point ?? 0) + $earned_points;
                
                // Update user's reward points
                $stm = $_db->prepare('
                    UPDATE user 
                    SET reward_point = ? 
                    WHERE id = ?
                ');
                $stm->execute([$new_total_points, $order->member_id]);
                
                // Log reward points transaction
                try {
                    $stm = $_db->prepare('
                        INSERT INTO reward_points_log (member_id, order_id, points_earned, transaction_type, description, created_at)
                        VALUES (?, ?, ?, "earned", ?, NOW())
                    ');
                    $stm->execute([$order->member_id, $order_id, $earned_points, "Points earned from order #" . $order_id]);
                } catch (Exception $e) {
                    log_payment_debug("Failed to log reward points, but points were added", ['error' => $e->getMessage()]);
                }
                
                log_payment_debug("Reward points added successfully in test mode", [
                    'member_id' => $order->member_id,
                    'points_earned' => $earned_points,
                    'new_total' => $new_total_points
                ]);
            }
        } catch (Exception $e) {
            log_payment_debug("Error processing reward points in test mode", ['error' => $e->getMessage()]);
        }
        
        log_payment_debug("Direct update successful", ['order_id' => $order_id]);
        return true;
    } catch (Exception $e) {
        log_payment_debug("Direct update error", ['error' => $e->getMessage()]);
        return false;
    }
}

// Function to update order payment status
function update_order_payment($order_id, $status, $transaction_id = null) {
    global $_db;
    
    log_payment_debug("Attempting to update order", [
        'order_id' => $order_id,
        'status' => $status,
        'transaction_id' => $transaction_id
    ]);
    
    // First check if order exists
    if (!verify_order_exists($order_id)) {
        log_payment_debug("Order ID does not exist", ['order_id' => $order_id]);
        return false;
    }
    
    // Map Billplz status to our system status
    $payment_status = 'pending';
    $order_status = 'pending';
    
    if ($status === 'paid' || $status === 'completed' || $status === 'successful') {
        $payment_status = 'paid';
        $order_status = 'processing';
    } else if ($status === 'failed' || $status === 'cancelled' || $status === 'refunded') {
        $payment_status = 'failed';
        $order_status = 'cancelled';
    }
    
    try {
        // Begin transaction
        $_db->beginTransaction();
        
        log_payment_debug("Updating order", [
            'order_id' => $order_id,
            'payment_status' => $payment_status,
            'order_status' => $order_status
        ]);
        
        // Update order with payment information
        $stm = $_db->prepare('
            UPDATE `order` 
            SET payment_status = ?, 
                order_status = ?,
                transaction_id = ?,
                payment_date = NOW()
            WHERE order_id = ?
        ');
        $stm->execute([$payment_status, $order_status, $transaction_id, $order_id]);
        
        // If payment successful, create a new empty cart for the member and add reward points
        if ($payment_status === 'paid') {
            $stm = $_db->prepare('
                SELECT o.member_id, o.cart_id, o.total_amount, u.reward_point 
                FROM `order` o
                JOIN user u ON o.member_id = u.id
                WHERE o.order_id = ?
            ');
            $stm->execute([$order_id]);
            $order = $stm->fetch(PDO::FETCH_OBJ);
            
            if ($order) {
                // Update the old cart to completed
                $stm = $_db->prepare('UPDATE cart SET status = "completed" WHERE cart_id = ?');
                $stm->execute([$order->cart_id]);
                
                // Create a new cart for the member
                $stm = $_db->prepare('
                    INSERT INTO cart (member_id, created_at, status) 
                    VALUES (?, NOW(), "active")
                ');
                $stm->execute([$order->member_id]);
                
                // Calculate reward points (RM1 = 1 point, no rounding)
                $earned_points = floor($order->total_amount);
                $new_total_points = ($order->reward_point ?? 0) + $earned_points;
                
                // Update user's reward points
                $stm = $_db->prepare('
                    UPDATE user 
                    SET reward_point = ? 
                    WHERE id = ?
                ');
                $stm->execute([$new_total_points, $order->member_id]);
                
                // Log reward points transaction
                try {
                    $stm = $_db->prepare('
                        INSERT INTO reward_points_log (member_id, order_id, points_earned, transaction_type, description, created_at)
                        VALUES (?, ?, ?, "earned", ?, NOW())
                    ');
                    $stm->execute([$order->member_id, $order_id, $earned_points, "Points earned from order #" . $order_id]);
                } catch (Exception $e) {
                    log_payment_debug("Failed to log reward points, but points were added", ['error' => $e->getMessage()]);
                }
                
                log_payment_debug("Reward points added successfully", [
                    'member_id' => $order->member_id,
                    'points_earned' => $earned_points,
                    'new_total' => $new_total_points
                ]);
            }
        }
        
        // Log the payment callback to match your table structure
        try {
            $stm = $_db->prepare('
                INSERT INTO payment_logs (order_id, status, raw_data, created_at)
                VALUES (?, ?, ?, NOW())
            ');
            $stm->execute([$order_id, $payment_status, json_encode($_POST)]);
        } catch (Exception $e) {
            // Continue even if log entry fails
            log_payment_debug("Failed to create log entry but continuing", ['error' => $e->getMessage()]);
        }
        
        // Commit transaction
        $_db->commit();
        log_payment_debug("Payment updated successfully", [
            'order_id' => $order_id,
            'payment_status' => $payment_status,
            'order_status' => $order_status
        ]);
        return true;
        
    } catch (Exception $e) {
        // Rollback in case of error
        $_db->rollBack();
        log_payment_debug("Payment callback error", ['message' => $e->getMessage()]);
        return false;
    }
}

// This section updates the latest order for testing
if (isset($_GET['test_update'])) {
    log_payment_debug("Test mode activated");
    try {
        $stm = $_db->prepare('SELECT order_id FROM `order` ORDER BY order_date DESC LIMIT 1');
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_OBJ);
        
        if ($result) {
            $order_id = $result->order_id;
            if (direct_update_order($order_id)) {
                echo "Test update successful for order: " . $order_id;
            } else {
                echo "Test update failed";
            }
        } else {
            echo "No orders found";
        }
        exit;
    } catch (Exception $e) {
        log_payment_debug("Test mode error", ['error' => $e->getMessage()]);
        echo "Test error: " . $e->getMessage();
        exit;
    }
}

// Move this outside the previous if block to make it independent
if (isset($_GET['test_failed'])) {
    log_payment_debug("Test mode activated - failed payment");
    try {
        $stm = $_db->prepare('SELECT order_id FROM `order` ORDER BY order_date DESC LIMIT 1');
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_OBJ);
        
        if ($result) {
            $order_id = $result->order_id;
            // Use the update_order_payment function with 'failed' status
            if (update_order_payment($order_id, 'failed', 'test_failed_' . time())) {
                echo "Test failed payment successful for order: " . $order_id;
            } else {
                echo "Test failed payment update error";
            }
        } else {
            echo "No orders found";
        }
        exit;
    } catch (Exception $e) {
        log_payment_debug("Test failed mode error", ['error' => $e->getMessage()]);
        echo "Test error: " . $e->getMessage();
        exit;
    }
}

// Process the callback from Billplz
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log entire request for debugging
    log_payment_debug("Billplz callback received", [
        'post_data' => $_POST,
        'server' => $_SERVER
    ]);
    
    // Verify X-Signature if present
    if (isset($_SERVER['HTTP_X_SIGNATURE'])) {
        $x_signature = $_SERVER['HTTP_X_SIGNATURE'];
        
        // Data to sign (based on Billplz docs)
        $data = $_POST['id'] . '|' . $_POST['collection_id'] . '|' . $_POST['paid'] . '|' . $_POST['state'];
        
        // Generate expected signature
        $expected_signature = hash_hmac('sha256', $data, $x_signature_key);
        
        if ($expected_signature !== $x_signature) {
            log_payment_debug("X-Signature validation failed", [
                'received' => $x_signature,
                'expected' => $expected_signature
            ]);
            http_response_code(401);
            exit('Invalid signature');
        }
        
        log_payment_debug("X-Signature validated successfully");
    } else {
        log_payment_debug("No X-Signature header found");
    }

    // Variable to store the extracted order ID
    $order_id = '';
    $status = 'pending';
    $transaction_id = null;
    
    // Extract data based on the different possible formats
    if (isset($_POST['reference_id'])) {
        // Format 1: New format
        $order_id = $_POST['reference_id'];
        $status = $_POST['status'] ?? 'pending';
        $transaction_id = $_POST['id'] ?? null;
        log_payment_debug("Format 1 detected", [
            'order_id' => $order_id,
            'status' => $status
        ]);
    } 
    else if (isset($_POST['reference_1'])) {
        // Format 2: Old format with reference_1
        $order_id = $_POST['reference_1'];
        $paid = ($_POST['paid'] ?? '') === 'true';
        $status = $paid ? 'paid' : 'failed';
        $transaction_id = $_POST['transaction_id'] ?? $_POST['id'] ?? null;
        log_payment_debug("Format 2 detected", [
            'order_id' => $order_id,
            'paid' => $paid,
            'status' => $status
        ]);
    }
    else if (isset($_POST['id'])) {
        // Format 3: Try to use the bill_id
        $bill_id = $_POST['id'];
        log_payment_debug("Format 3 detected - trying to find order by bill_id", [
            'bill_id' => $bill_id
        ]);
        
        try {
            // First check if we have a direct reference to the bill_id in the order table
            $stm = $_db->prepare('SELECT order_id FROM `order` WHERE billplz_bill_id = ? OR transaction_id = ? LIMIT 1');
            $stm->execute([$bill_id, $bill_id]);
            $result = $stm->fetch(PDO::FETCH_OBJ);
            
            if ($result) {
                $order_id = $result->order_id;
                log_payment_debug("Found order by bill_id", [
                    'order_id' => $order_id,
                    'bill_id' => $bill_id
                ]);
            } else {
                // Alternative approach: Find the most recent order with pending status
                $stm = $_db->prepare('SELECT order_id FROM `order` WHERE payment_status = "pending" ORDER BY order_date DESC LIMIT 1');
                $stm->execute();
                $result = $stm->fetch(PDO::FETCH_OBJ);
                
                if ($result) {
                    $order_id = $result->order_id;
                    log_payment_debug("Using most recent pending order as fallback", [
                        'order_id' => $order_id,
                        'bill_id' => $bill_id
                    ]);
                } else {
                    log_payment_debug("No pending orders found in database");
                }
            }
            
            // Try to determine payment status
            if (isset($_POST['paid'])) {
                $paid = $_POST['paid'] === 'true';
                $status = $paid ? 'paid' : 'failed';
            } else if (isset($_POST['status'])) {
                $status = $_POST['status'];
            }
            
            $transaction_id = $_POST['transaction_id'] ?? $bill_id;
            
        } catch (Exception $e) {
            log_payment_debug("Error looking up order by bill_id", ['error' => $e->getMessage()]);
        }
    }
    
    // FALLBACK FOR TESTING - REMOVE IN PRODUCTION
    if (empty($order_id)) {
        // Get the latest pending order from your database for testing
        try {
            $stm = $_db->prepare('SELECT order_id FROM `order` WHERE payment_status = "pending" ORDER BY order_date DESC LIMIT 1');
            $stm->execute();
            $result = $stm->fetch(PDO::FETCH_OBJ);
            
            if ($result) {
                $order_id = $result->order_id;
                $status = 'paid'; // Force status to paid for testing
                log_payment_debug("TESTING MODE: Using latest pending order", [
                    'order_id' => $order_id,
                    'force_status' => $status
                ]);
            }
        } catch (Exception $e) {
            log_payment_debug("Error in testing fallback", ['error' => $e->getMessage()]);
        }
    }
    
    // Now process the payment with the extracted information
    if (!empty($order_id)) {
        if (update_order_payment($order_id, $status, $transaction_id)) {
            // Respond to Billplz
            echo 'OK';
            log_payment_debug("Successfully processed payment", [
                'order_id' => $order_id,
                'status' => $status
            ]);
        } else {
            log_payment_debug("Failed to update order", [
                'order_id' => $order_id
            ]);
            http_response_code(500);
            exit('Failed to update order');
        }
    } else {
        log_payment_debug("Invalid order reference - could not determine order ID", $_POST);
        http_response_code(400);
        exit('Invalid order reference');
    }
} else {
    // For GET requests, show a simple status page
    log_payment_debug("GET request received", [
        'method' => $_SERVER['REQUEST_METHOD'],
        'query' => isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''
    ]);
    
    if (!isset($_GET['test_update'])) {
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Billplz Payment Handler</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                .container { max-width: 800px; margin: 0 auto; }
                .status { padding: 20px; background: #f5f5f5; border-radius: 5px; }
                .button { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; 
                          text-decoration: none; border-radius: 4px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Been Chilling</h2>
                <h1>Billplz Payment Handler</h1>
                <div class="status">
                <p>This endpoint is used to process Billplz payment callbacks.</p>
                <p>Log file: ' . htmlspecialchars($log_file) . '</p>
                <p>The following buttons are backups for testing in case of tunnel failures.</p>
                <p>Set the latest payment callback status:</p>
                <a href="?test_update=1" class="button">Payment Success</a>
                <a href="?test_failed=1" class="button" style="background: #f44336; margin-left: 10px;">Payment Failed</a>
                </div>
            </div>
        </body>
        </html>';
    }
}
?>