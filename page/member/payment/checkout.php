<?php
require '../../../_base.php';

// Ensure user is logged in as a member
auth('Member');

// Function to get active cart
function get_active_cart() {
    global $_db, $_user;
    
    // Check if we're retrying a failed order
    $order_id = req('order_id');
    if (!empty($order_id)) {
        // First try to find the original cart from the order
        $stm = $_db->prepare('
            SELECT o.cart_id, oi.product_id, oi.quantity, oi.price
            FROM `order` o
            JOIN order_item oi ON o.order_id = oi.order_id
            WHERE o.order_id = ? AND o.member_id = ? AND o.payment_status = "failed"
        ');
        $stm->execute([$order_id, $_user->id]);
        $order = $stm->fetchAll(PDO::FETCH_OBJ);
        
        if ($order) {
            // Get the original cart_id from the first item
            $cart_id = $order[0]->cart_id;
            
            // Check if the cart still exists
            $stm = $_db->prepare('SELECT cart_id FROM cart WHERE cart_id = ? AND member_id = ?');
            $stm->execute([$cart_id, $_user->id]);
            $cart = $stm->fetch(PDO::FETCH_OBJ);
            
            if ($cart) {
                // Cart exists, return it
                return $cart;
            }
            
            // If cart doesn't exist, create a new one and copy items
            $stm = $_db->prepare('
                INSERT INTO cart (member_id, created_at, status) 
                VALUES (?, NOW(), "active")
            ');
            $stm->execute([$_user->id]);
            $new_cart_id = $_db->lastInsertId();
            
            // Copy items from order to new cart
            foreach ($order as $item) {
                $stm = $_db->prepare('
                    INSERT INTO cart_item (cart_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ');
                $stm->execute([$new_cart_id, $item->product_id, $item->quantity, $item->price]);
            }
            
            return (object)['cart_id' => $new_cart_id];
        }
    }
    
    // If not retrying or no order found, get existing active cart
    $stm = $_db->prepare('SELECT cart_id FROM cart WHERE member_id = ? AND status = "active" LIMIT 1');
    $stm->execute([$_user->id]);
    return $stm->fetch(PDO::FETCH_OBJ);
}

// Function to get cart items
function checkout_get_cart_items($cart_id) {
    global $_db;
    
    $stm = $_db->prepare('
        SELECT ci.*, p.product_name, p.product_image 
        FROM cart_item ci
        JOIN product p ON ci.product_id = p.product_id
        WHERE ci.cart_id = ?
    ');
    $stm->execute([$cart_id]);
    
    return $stm->fetchAll(PDO::FETCH_OBJ);
}

// Function to get cart summary
function checkout_get_cart_summary($cart_id) {
    global $_db;
    
    $stm = $_db->prepare('
        SELECT SUM(quantity) as total_items, SUM(quantity * price) as total_price
        FROM cart_item
        WHERE cart_id = ?
    ');
    $stm->execute([$cart_id]);
    
    return $stm->fetch(PDO::FETCH_OBJ);
}

// Function to create an order from the cart
function create_order($cart_id)
{
    global $_db, $_user;

    try {
        $_db->beginTransaction();

        // Get cart summary
        $cart_summary = checkout_get_cart_summary($cart_id);
        $total_amount = $cart_summary->total_price ?? 0;
        
        // Process points deduction if points are being used
        $points_to_use = 0;
        $points_discount = 0;
        
        if (isset($_POST['points_to_use']) && is_numeric($_POST['points_to_use'])) {
            $points_to_use = intval($_POST['points_to_use']);
            
            // Validate points are multiple of 100
            if ($points_to_use % 100 !== 0) {
                throw new Exception("Points must be in multiples of 100");
            }
            
            // Get user's current reward points
            $stmt = $_db->prepare('SELECT reward_point FROM user WHERE id = ?');
            $stmt->execute([$_user->id]);
            $user_points = $stmt->fetchColumn();
            
            // Validate points don't exceed available points
            if ($points_to_use > $user_points) {
                throw new Exception("You don't have enough reward points");
            }
            
            // Calculate maximum points that can be used (based on total amount)
            $max_points = floor($total_amount) * 100;
            if ($points_to_use > $max_points) {
                throw new Exception("You can only use up to " . $max_points . " points for this order");
            }
            
            // Calculate discount (100 points = RM1)
            $points_discount = $points_to_use / 100;
            
            // Adjust total amount
            $total_amount = max(0, $total_amount - $points_discount);
        }

        // Fetch the latest order ID
        $stmt = $_db->query("SELECT order_id FROM `order` ORDER BY order_id DESC LIMIT 1");
        $lastId = $stmt->fetchColumn();

        if ($lastId && preg_match('/OR(\d+)/', $lastId, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1; // If no records exist yet
        }

        // Format the new order ID with leading zeroes (e.g., OR0004)
        $order_id = 'OR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Process shipping address
        $shipping_address = [];

        if (isset($_POST['shipping_address_option']) && $_POST['shipping_address_option'] === 'saved') {
            // Get saved address from database
            $shipping_address_id = $_POST['saved_shipping_address_id'];

            $stm = $_db->prepare('SELECT * FROM shipping_address WHERE shipping_address_id = ? AND user_id = ?');
            $stm->execute([$shipping_address_id, $_user->id]);
            $saved_address = $stm->fetch(PDO::FETCH_ASSOC);

            if ($saved_address) {
                $shipping_address = $saved_address;
            }
        } else {
            // Process new address
            $shipping_address = [
                'address_name' => $_POST['shipping_address_name'] ?? '',
                'recipient_name' => $_POST['shipping_recipient_name'] ?? '',
                'street_address' => $_POST['shipping_street_address'] ?? '',
                'city' => $_POST['shipping_city'] ?? '',
                'state' => $_POST['shipping_state'] ?? '',
                'postal_code' => $_POST['shipping_postal_code'] ?? '',
                'country' => $_POST['shipping_country'] ?? 'Malaysia',
                'address_phone_number' => $_POST['shipping_phone_number'] ?? ''
            ];

            // Save new address if requested
            if (isset($_POST['save_shipping_address']) && $_POST['save_shipping_address'] == '1') {
                // Fetch the latest shipping address ID
                $stmt = $_db->query("SELECT shipping_address_id FROM shipping_address ORDER BY shipping_address_id DESC LIMIT 1");
                $lastId = $stmt->fetchColumn();

                if ($lastId && preg_match('/SA(\d+)/', $lastId, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                } else {
                    $nextNumber = 1; // If no records exist yet
                }

                // Format the new shipping address ID with leading zeros (e.g., SA0004)
                $shipping_address_id = 'SA' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                $stm = $_db->prepare('
            INSERT INTO shipping_address (
                shipping_address_id, user_id, address_name, recipient_name,
               street_address, city, state, postal_code, country, address_phone_number
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
       ');
                $stm->execute([
                    $shipping_address_id,
                    $_user->id,
                    $shipping_address['address_name'],
                    $shipping_address['recipient_name'],
                    $shipping_address['street_address'],
                    $shipping_address['city'],
                    $shipping_address['state'],
                    $shipping_address['postal_code'],
                    $shipping_address['country'],
                    $shipping_address['address_phone_number']
                ]);
            }
        }

        // Process billing address
        if (isset($_POST['same_address']) && $_POST['same_address'] == '1') {
            // Use shipping address as billing address
            $billing_address = $shipping_address;
        } else {
            // Process billing address
            if (isset($_POST['billing_address_option']) && $_POST['billing_address_option'] === 'saved') {
                $billing_address_id = $_POST['saved_billing_address_id'];

                $stm = $_db->prepare('SELECT * FROM shipping_address WHERE shipping_address_id = ? AND user_id = ?');
                $stm->execute([$billing_address_id, $_user->id]);
                $saved_address = $stm->fetch(PDO::FETCH_ASSOC);

                if ($saved_address) {
                    $billing_address = $saved_address;
                }
            } else {
                // Process new billing address
                $billing_address = [
                    'address_name' => $_POST['billing_address_name'] ?? '',
                    'recipient_name' => $_POST['billing_recipient_name'] ?? '',
                    'street_address' => $_POST['billing_street_address'] ?? '',
                    'city' => $_POST['billing_city'] ?? '',
                    'state' => $_POST['billing_state'] ?? '',
                    'postal_code' => $_POST['billing_postal_code'] ?? '',
                    'country' => $_POST['billing_country'] ?? 'Malaysia',
                    'address_phone_number' => $_POST['billing_phone_number'] ?? ''
                ];

                // Save new billing address if requested
                if (isset($_POST['save_billing_address']) && $_POST['save_billing_address'] == '1') {
                    // Fetch the latest shipping address ID
                    $stmt = $_db->query("SELECT shipping_address_id FROM shipping_address ORDER BY shipping_address_id DESC LIMIT 1");
                    $lastId = $stmt->fetchColumn();

                    if ($lastId && preg_match('/SA(\d+)/', $lastId, $matches)) {
                        $nextNumber = intval($matches[1]) + 1;
                    } else {
                        $nextNumber = 1; // If no records exist yet
                    }

                    // Format the new shipping address ID with leading zeros (e.g., SA0004)
                    $billing_address_id = 'SA' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                    $stm = $_db->prepare('
                INSERT INTO shipping_address (
                    shipping_address_id, user_id, address_name, recipient_name,
                    street_address, city, state, postal_code, country, address_phone_number
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
                    $stm->execute([
                        $billing_address_id,
                        $_user->id,
                        $billing_address['address_name'],
                        $billing_address['recipient_name'],
                        $billing_address['street_address'],
                        $billing_address['city'],
                        $billing_address['state'],
                        $billing_address['postal_code'],
                        $billing_address['country'],
                        $billing_address['address_phone_number']
                    ]);
                }
            }
        }


        // Format addresses for database storage
        $formatted_shipping_address = implode(', ', array_filter([
            $shipping_address['recipient_name'] ?? '',
            $shipping_address['address_phone_number'] ?? '',
            $shipping_address['street_address'] ?? '',
            $shipping_address['city'] ?? '',
            $shipping_address['state'] ?? '',
            $shipping_address['postal_code'] ?? '',
            $shipping_address['country'] ?? 'Malaysia'
        ]));

        $formatted_billing_address = implode(', ', array_filter([
            $billing_address['recipient_name'] ?? '',
            $billing_address['address_phone_number'] ?? '',
            $billing_address['street_address'] ?? '',
            $billing_address['city'] ?? '',
            $billing_address['state'] ?? '',
            $billing_address['postal_code'] ?? '',
            $billing_address['country'] ?? 'Malaysia'
        ]));

        // Insert the order with address information - keep payment_status as "pending"
        $stm = $_db->prepare('
            INSERT INTO `order` (order_id, member_id, cart_id, order_date, total_amount, 
            shipping_address, billing_address, payment_method, payment_status, order_status)
            VALUES (?, ?, ?, NOW(), ?, ?, ?, "Billplz", "pending", "pending")
        ');
        $result = $stm->execute([
            $order_id,
            $_user->id,
            $cart_id,
            $total_amount,
            $formatted_shipping_address,
            $formatted_billing_address
        ]);

        if (!$result) {
            error_log("Error creating order: " . print_r($_db->errorInfo(), true)); // Log database error
            throw new Exception("Failed to create order");
        }

        // Update user's reward points if points were used
        if ($points_to_use > 0) {
            $stmt = $_db->prepare('UPDATE user SET reward_point = reward_point - ? WHERE id = ?');
            $stmt->execute([$points_to_use, $_user->id]);
        }

        // Mark the cart as ordered
        $stm = $_db->prepare('UPDATE cart SET status = "ordered" WHERE cart_id = ?');
        $stm->execute([$cart_id]);

        // Transfer cart items to order items
        $stm = $_db->prepare('
            SELECT * FROM cart_item
            WHERE cart_id = ?
            ');
        $stm->execute([$cart_id]);
        $cart_items = $stm->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cart_items as $item) {
            // Generate a unique order item ID
            $order_item_id = 'OI' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Insert the item into order_item table
            $stm = $_db->prepare('
            INSERT INTO order_item (
                order_item_id, order_id, product_id, quantity, price
            ) VALUES (?, ?, ?, ?, ?)
        ');

            $result = $stm->execute([
                $order_item_id,
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            if (!$result) {
                error_log("Error creating order item: " . print_r($_db->errorInfo(), true));
                throw new Exception("Failed to create order item");
            }
        }

        $_db->commit();

        // Return order information
        return [
            'order_id' => $order_id,
            'total_amount' => $total_amount,
            'points_used' => $points_to_use,
            'points_discount' => $points_discount
        ];
    } catch (Exception $e) {
        $_db->rollBack();
        throw $e;
    }
}

// Store Billplz payment information
function store_billplz_info($order_id, $bill_id, $collection_id)
{
    global $_db;

    // Update payment status to "awaiting_payment" when linking to Billplz
    $stm = $_db->prepare('
        UPDATE `order` 
        SET billplz_bill_id = ?, 
            billplz_collection_id = ?,
            payment_status = "awaiting_payment" 
        WHERE order_id = ?
    ');
    return $stm->execute([$bill_id, $collection_id, $order_id]);
}

// Process checkout - BEFORE ANY HTML OUTPUT
$error = null;
if (is_post() && isset($_POST['btn']) && $_POST['btn'] === 'confirm') {
    try {
        $cart = get_active_cart();
        
        if ($cart) {
            $cart_summary = checkout_get_cart_summary($cart->cart_id);
            
            // Check if cart is not empty
            if (!$cart_summary || $cart_summary->total_items < 1) {
                $error = "Your cart is empty. Please add products to your cart before checkout.";
            } else {
                // Check if we're retrying a failed order
                $order_id = req('order_id');
                if (!empty($order_id)) {
                    // Verify the order exists and belongs to the current user
                    $stm = $_db->prepare('SELECT * FROM `order` WHERE order_id = ? AND member_id = ? AND payment_status = "failed"');
                    $stm->execute([$order_id, $_user->id]);
                    $existing_order = $stm->fetch(PDO::FETCH_OBJ);
                    
                    if ($existing_order) {
                        // Use the existing order
                        $order_data = [
                            'order_id' => $existing_order->order_id,
                            'total_amount' => $existing_order->total_amount,
                            'points_used' => 0, // Reset points for retry
                            'points_discount' => 0
                        ];
                    } else {
                        throw new Exception("Order not found or not eligible for retry");
                    }
                } else {
                    // Create new order for fresh checkout
                    $order_data = create_order($cart->cart_id);
                }
                
                // Billplz API credentials
                $api_key = 'c4829771-97fd-40ee-a49f-10385d8f587b';
                $collection_id = 'racg2vr3';
                $x_signature_key = '08251797c8178a8bd90a55eb721f622cb59b5f17424e25280b278a6bc9b09365350c95aa6faac986fd01c2d282d7539bcd79b4075377e9a74ffdb856f7175810';
                
                // Get the absolute URLs for callback and redirect
                $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $callback_url = 'https://01ce-2001-f40-956-62a7-9563-fa2b-33a8-e54a.ngrok-free.app/page/member/payment/payment_callback.php';
                $redirect_url = $site_url . '/page/member/payment/payment_status.php?order_id=' . $order_data['order_id'];
                
                // Prepare API request to Billplz
                $params = [
                    'collection_id' => $collection_id,
                    'email' => $_user->email,
                    'name' => $_user->name,
                    'amount' => round($order_data['total_amount'] * 100), // Convert to cents
                    'callback_url' => $callback_url,
                    'redirect_url' => $redirect_url,
                    'description' => 'Payment for Order #' . $order_data['order_id'],
                    'reference_1_label' => 'Order ID',
                    'reference_1' => $order_data['order_id']
                ];
                
                // Make API call to create bill
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'https://www.billplz-sandbox.com/api/v3/bills');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_USERPWD, $api_key . ':');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                $response = curl_exec($curl);
                
                // Check for cURL errors
                if (curl_errno($curl)) {
                    curl_close($curl);
                    throw new Exception("Connection Error: " . curl_error($curl));
                } else {
                    $result = json_decode($response, true);
                    curl_close($curl);
                    
                    if (isset($result['id']) && isset($result['url'])) {
                        // Store Billplz bill ID and update status to "awaiting_payment"
                        $stored = store_billplz_info($order_data['order_id'], $result['id'], $collection_id);
                        
                        if ($stored) {
                            // Redirect to Billplz payment page
                            header("Location: " . $result['url']);
                            exit; // Make sure script stops here
                        } else {
                            throw new Exception("Error storing payment information. Please try again.");
                        }
                    } else {
                        // Handle API error
                        $error_message = isset($result['error']['message']) ? $result['error']['message'] : "Unknown error occurred";
                        if (is_array($error_message)) {
                            $error_message = json_encode($error_message);
                        }
                        throw new Exception("Payment gateway error: " . $error_message);
                    }
                }
            }
        } else {
            $error = "Your cart is empty. Please add products to your cart before checkout.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Checkout Error: " . $e->getMessage());
    }
}

// Get cart information
$cart = get_active_cart();
$cart_items = $cart ? checkout_get_cart_items($cart->cart_id) : [];
$cart_summary = $cart ? checkout_get_cart_summary($cart->cart_id) : null;

// Handle continuing order from order history
$order_id = req('order_id');
if ($order_id) {
    try {
        // Verify the order belongs to the current user
        $stm = $_db->prepare('SELECT order_id FROM `order` WHERE order_id = ? AND member_id = ?');
        $stm->execute([$order_id, $_user->id]);
        $order = $stm->fetch(PDO::FETCH_OBJ);
        
        if (!$order) {
            throw new Exception("Order not found or you don't have permission to access it");
        }
        
        // Get order items
        $stm = $_db->prepare('
            SELECT oi.product_id, oi.quantity, oi.price
            FROM order_item oi
            WHERE oi.order_id = ?
        ');
        $stm->execute([$order_id]);
        $order_items = $stm->fetchAll(PDO::FETCH_OBJ);
        
        if (empty($order_items)) {
            throw new Exception("No items found in the order");
        }
        
        // Get or create active cart
        if (!$cart) {
            // Create new cart if none exists
            $stm = $_db->prepare('INSERT INTO cart (member_id, created_at, status) VALUES (?, NOW(), "active")');
            $stm->execute([$_user->id]);
            $cart_id = $_db->lastInsertId();
        } else {
            $cart_id = $cart->cart_id;
        }
        
        // Clear existing cart items
        $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id = ?');
        $stm->execute([$cart_id]);
        
        // Add order items to cart
        foreach ($order_items as $item) {
            $stm = $_db->prepare('
                INSERT INTO cart_item (cart_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ');
            $stm->execute([
                $cart_id,
                $item->product_id,
                $item->quantity,
                $item->price
            ]);
        }
        
        // Get updated cart information
        $cart = get_active_cart();
        $cart_items = checkout_get_cart_items($cart->cart_id);
        $cart_summary = checkout_get_cart_summary($cart->cart_id);
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$_title = 'BeenChilling';
include '../../../_head.php';

topics_text("Checkout", "200px");
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="checkout-container">
    <h2>Order Summary</h2>
    
    <table class="checkout-table">
        <tr>
            <th>Product</th>
            <th>Price (RM)</th>
            <th>Quantity</th>
            <th>Subtotal (RM)</th>
        </tr>
        
        <?php if (!empty($cart_items)): ?>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->product_name) ?></td>
                    <td class="right"><?= number_format($item->price, 2) ?></td>
                    <td class="center"><?= $item->quantity ?></td>
                    <td class="right"><?= number_format($item->price * $item->quantity, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            
            <tr class="total-row">
                <th colspan="2">Total</th>
                <th><?= $cart_summary->total_items ?? 0 ?> items</th>
                <th class="right">RM <?= number_format($cart_summary->total_price ?? 0, 2) ?></th>
            </tr>
            <?php if (isset($_POST['points_to_use']) && intval($_POST['points_to_use']) > 0): ?>
                <?php 
                $points_to_use = intval($_POST['points_to_use']);
                $points_discount = $points_to_use / 100;
                $final_amount = max(0, ($cart_summary->total_price ?? 0) - $points_discount);
                ?>
                <tr class="discount-row">
                    <th colspan="2">Points Discount</th>
                    <th><?= $points_to_use ?> points</th>
                    <th class="right">-RM <?= number_format($points_discount, 2) ?></th>
                </tr>
                <tr class="final-total-row">
                    <th colspan="2">Final Total</th>
                    <th></th>
                    <th class="right">RM <?= number_format($final_amount, 2) ?></th>
                </tr>
            <?php endif; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="center">Your cart is empty</td>
            </tr>
        <?php endif; ?>
    </table>
    
    <form method="post" action="" id="checkout-form">
        <div class="customer-info">
            <br>
            <?php
            $total_amount = $cart_summary->total_price ?? 0;
            // Calculate potential reward points (RM1 = 1 point)
            $potential_points = floor($total_amount); // No rounding, simply floor value

            // Display this information on the checkout page
            echo '<div class="reward-points-info">';
            echo '<p><i class="fas fa-gift"></i> You will earn <strong>' . $potential_points . ' reward points</strong> with this purchase!</p>';
            // Fetch user's current reward points
            $stmt = $_db->prepare('SELECT reward_point FROM user WHERE id = ?');
            $stmt->execute([$_user->id]);
            $user_points = $stmt->fetchColumn();

            echo '<div class="points-discount-section">';
            echo '<p><i class="fas fa-coins"></i> You have <strong>' . $user_points . ' reward points</strong> available.</p>';
            echo '<div class="points-input-group">';
            echo '<label for="points_to_use">Use points for discount (100 points = RM1): </label>';
            echo '<input type="number" id="points_to_use" name="points_to_use" min="0" max="' . $user_points . '" step="100" value="0" class="form-control points-input" style="width: 10%;">';
            echo '<small class="form-text"> Enter points in multiples of 100 (e.g., 100, 200, 300)</small>';
            echo '<div id="points-error" class="text-danger" style="display:none;"></div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            ?>
            <hr>
            <h2>Customer Information</h2>
            <p><strong>Name:</strong> <?= htmlspecialchars($_user->name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_user->email) ?></p>
            <?php
            // Fetch all saved addresses for the user
            $stm = $_db->prepare('
                SELECT * FROM shipping_address 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ');
            $stm->execute([$_user->id]);
            $saved_addresses = $stm->fetchAll(PDO::FETCH_OBJ);
            ?>

            <div class="address-section">
                <hr>
                <h3>Shipping Address</h3>
        
                <div class="address-selection">
                    <?php if (count($saved_addresses) > 0): ?>
                        <div class="form-group">
                            <label>
                                <input type="radio" name="shipping_address_option" value="saved" checked> 
                                Use a saved address
                            </label>
                    
                            <div class="saved-addresses-container">
                                <select id="saved_shipping_address" name="saved_shipping_address_id" class="form-control">
                                    <?php foreach ($saved_addresses as $address): ?>
                                        <option value="<?= htmlspecialchars($address->shipping_address_id) ?>">
                                            <?= htmlspecialchars($address->address_name) ?> - 
                                            <?= htmlspecialchars($address->recipient_name) ?>, 
                                            <?= htmlspecialchars($address->street_address) ?>, 
                                            <?= htmlspecialchars($address->city) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                
                        <div class="form-group">
                            <label>
                                <input type="radio" name="shipping_address_option" value="new"> 
                                Enter a new address
                            </label>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="shipping_address_option" value="new">
                    <?php endif; ?>
            
                    <div id="new_shipping_address" class="new-address-form" <?= count($saved_addresses) > 0 ? 'style="display:none;"' : '' ?>>
                        <div class="form-group">
                            <label for="shipping_address_name">Address Name (e.g., Home, Office)</label>
                            <input type="text" id="shipping_address_name" name="shipping_address_name" class="form-control">
                        </div>
                
                        <div class="form-group">
                            <label for="shipping_recipient_name">Recipient Name</label>
                            <input type="text" id="shipping_recipient_name" name="shipping_recipient_name" class="form-control">
                        </div>
                
                        <div class="form-group">
                            <label for="shipping_street_address">Street Address</label>
                            <input type="text" id="shipping_street_address" name="shipping_street_address" class="form-control">
                        </div>
                
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="shipping_city">City</label>
                                <input type="text" id="shipping_city" name="shipping_city" class="form-control">
                            </div>
                    
                            <div class="form-group col-md-6">
                                <label for="shipping_state">State</label>
                                <input type="text" id="shipping_state" name="shipping_state" class="form-control">
                            </div>
                        </div>
                
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="shipping_postal_code">Postal Code</label>
                                <input type="text" id="shipping_postal_code" name="shipping_postal_code" class="form-control" pattern="[0-9]{5}" maxlength="5" title="Please enter a 5-digit postal code">
                            </div>
                    
                            <div class="form-group col-md-6">
                                <label for="shipping_country">Country</label>
                                <input type="text" id="shipping_country" name="shipping_country" class="form-control" value="Malaysia">
                            </div>
                        </div>
                
                        <div class="form-group">
                            <label for="shipping_phone_number">Phone Number</label>
                            <input type="tel" id="shipping_phone_number" name="shipping_phone_number" class="form-control">
                        </div>
                
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="save_shipping_address" value="1" checked> 
                                Save this address for future use
                            </label>
                        </div>
                    </div>
                </div>
        
                <h3>Billing Address</h3>
        
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="same_address" name="same_address" value="1" checked> 
                        Billing address is the same as shipping address
                    </label>
                </div>
        
                <div id="billing_address_section" style="display:none;">
                    <?php if (count($saved_addresses) > 0): ?>
                        <div class="form-group">
                            <label>
                                <input type="radio" name="billing_address_option" value="saved" checked> 
                                Use a saved address
                            </label>
                        
                            <div class="saved-addresses-container">
                                <select id="saved_billing_address" name="saved_billing_address_id" class="form-control">
                                    <?php foreach ($saved_addresses as $address): ?>
                                        <option value="<?= htmlspecialchars($address->shipping_address_id) ?>">
                                            <?= htmlspecialchars($address->address_name) ?> - 
                                            <?= htmlspecialchars($address->recipient_name) ?>, 
                                            <?= htmlspecialchars($address->street_address) ?>, 
                                            <?= htmlspecialchars($address->city) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <label>
                                <input type="radio" name="billing_address_option" value="new"> 
                                Enter a new address
                            </label>
                        </div>
                        
                        <div id="new_billing_address" class="new-address-form" style="display:none;">
                    <?php else: ?>
                        <input type="hidden" name="billing_address_option" value="new">
                        <div id="new_billing_address" class="new-address-form">
                    <?php endif; ?>
                    
                        <div class="form-group">
                            <label for="billing_address_name">Address Name (e.g., Home, Office)</label>
                            <input type="text" id="billing_address_name" name="billing_address_name" class="form-control">
                        </div>
                    
                        <div class="form-group">
                            <label for="billing_recipient_name">Recipient Name</label>
                            <input type="text" id="billing_recipient_name" name="billing_recipient_name" class="form-control">
                        </div>
                    
                        <div class="form-group">
                            <label for="billing_street_address">Street Address</label>
                            <input type="text" id="billing_street_address" name="billing_street_address" class="form-control">
                        </div>
                    
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="billing_city">City</label>
                                <input type="text" id="billing_city" name="billing_city" class="form-control">
                            </div>
                        
                            <div class="form-group col-md-6">
                                <label for="billing_state">State</label>
                                <input type="text" id="billing_state" name="billing_state" class="form-control">
                            </div>
                        </div>
                    
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="billing_postal_code">Postal Code</label>
                                <input type="text" id="billing_postal_code" name="billing_postal_code" class="form-control" pattern="[0-9]{5}" maxlength="5" title="Please enter a 5-digit postal code">
                            </div>
                        
                            <div class="form-group col-md-6">
                                <label for="billing_country">Country</label>
                                <input type="text" id="billing_country" name="billing_country" class="form-control" value="Malaysia">
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <label for="billing_phone_number">Phone Number</label>
                            <input type="tel" id="billing_phone_number" name="billing_phone_number" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="save_billing_address" value="1"> 
                                Save this address for future use
                            </label>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize saved addresses display
                    const savedShippingAddress = document.getElementById('saved_shipping_address');
                    const newShippingForm = document.getElementById('new_shipping_address');
                    
                    if (savedShippingAddress) {
                        console.log('Saved shipping address select found');
                        // Ensure the new address form is hidden initially if saved addresses exist
                        if (newShippingForm) {
                            newShippingForm.style.display = 'none';
                        }
                    } else {
                        console.log('No saved shipping address select found');
                    }
            
                    // Toggle shipping address form
                    const shippingRadios = document.querySelectorAll('input[name="shipping_address_option"]');
            
                    if (shippingRadios.length > 0) {
                        shippingRadios.forEach(radio => {
                            radio.addEventListener('change', function() {
                                if (this.value === 'new') {
                                    newShippingForm.style.display = 'block';
                                    // Make new shipping address fields required
                                    document.querySelectorAll('#new_shipping_address input:not([type="checkbox"])').forEach(input => {
                                        input.setAttribute('required', 'required');
                                    });
                                } else {
                                    newShippingForm.style.display = 'none';
                                    // Remove required from new shipping address fields
                                    document.querySelectorAll('#new_shipping_address input').forEach(input => {
                                        input.removeAttribute('required');
                                    });
                                }
                            });
                        });
                    }
            
                    // Toggle billing address section
                    const sameAddressCheckbox = document.getElementById('same_address');
                    const billingSection = document.getElementById('billing_address_section');
            
                    sameAddressCheckbox.addEventListener('change', function() {
                        billingSection.style.display = this.checked ? 'none' : 'block';
                        
                        // Handle required fields for billing
                        if (this.checked) {
                            // Remove required from all billing fields when using same address
                            document.querySelectorAll('#billing_address_section input:not([type="radio"])').forEach(input => {
                                input.removeAttribute('required');
                            });
                        } else {
                            // Add required to appropriate billing fields
                            const billingRadios = document.querySelectorAll('input[name="billing_address_option"]');
                            if (billingRadios.length > 0) {
                                const newBillingSelected = Array.from(billingRadios).find(radio => radio.value === 'new' && radio.checked);
                                if (newBillingSelected) {
                                    document.querySelectorAll('#new_billing_address input:not([type="checkbox"])').forEach(input => {
                                        input.setAttribute('required', 'required');
                                    });
                                }
                            } else {
                                // If no radio buttons (only new address option), make all fields required
                                document.querySelectorAll('#new_billing_address input:not([type="checkbox"])').forEach(input => {
                                    input.setAttribute('required', 'required');
                                });
                            }
                        }
                    });
            
                    // Toggle billing address form
                    const billingRadios = document.querySelectorAll('input[name="billing_address_option"]');
                    const newBillingForm = document.getElementById('new_billing_address');
            
                    if (billingRadios.length > 0) {
                        billingRadios.forEach(radio => {
                            radio.addEventListener('change', function() {
                                if (this.value === 'new') {
                                    newBillingForm.style.display = 'block';
                                    // Make new billing address fields required
                                    document.querySelectorAll('#new_billing_address input:not([type="checkbox"])').forEach(input => {
                                        input.setAttribute('required', 'required');
                                    });
                                } else {
                                    newBillingForm.style.display = 'none';
                                    // Remove required from new billing address fields
                                    document.querySelectorAll('#new_billing_address input').forEach(input => {
                                        input.removeAttribute('required');
                                    });
                                }
                            });
                        });
                    } else {
                        // If no radio buttons (only new address option), handle the case
                        sameAddressCheckbox.addEventListener('change', function() {
                            if (!this.checked) {
                                // When "same address" is unchecked and there are no saved addresses,
                                // make all billing fields required
                                document.querySelectorAll('#new_billing_address input:not([type="checkbox"])').forEach(input => {
                                    input.setAttribute('required', 'required');
                                });
                            }
                        });
                    }
                    
                    // Points validation
                    const pointsInput = document.getElementById('points_to_use');
                    const pointsError = document.getElementById('points-error');
                    const totalAmount = <?= $total_amount ?>;
                    const userPoints = <?= $user_points ?>;
                    const maxPoints = Math.floor(totalAmount - 1) * 100; // Ensure at least RM1 payment
                    
                    // Function to validate points
                    function validatePoints() {
                        const points = parseInt(pointsInput.value) || 0;
                        
                        // Reset error message
                        pointsError.style.display = 'none';
                        pointsInput.classList.remove('is-invalid');
                        
                        // Check if points is a multiple of 100
                        if (points % 100 !== 0) {
                            pointsError.textContent = 'Points must be in multiples of 100';
                            pointsError.style.display = 'block';
                            pointsInput.classList.add('is-invalid');
                            return false;
                        }
                        
                        // Check if points exceed available points
                        if (points > userPoints) {
                            pointsError.textContent = 'You don\'t have enough reward points';
                            pointsError.style.display = 'block';
                            pointsInput.classList.add('is-invalid');
                            return false;
                        }
                        
                        // Check if points exceed maximum allowed points (ensuring minimum RM1 payment)
                        if (points > maxPoints) {
                            pointsError.textContent = 'You can only use up to ' + maxPoints + ' points to ensure a minimum payment of RM1';
                            pointsError.style.display = 'block';
                            pointsInput.classList.add('is-invalid');
                            return false;
                        }
                        
                        return true;
                    }
                    
                    // Validate on input change
                    pointsInput.addEventListener('input', validatePoints);
                    
                    // Update displayed total when points change
                    pointsInput.addEventListener('change', function() {
                        if (!validatePoints()) return;
                        
                        const points = parseInt(pointsInput.value) || 0;
                        const discount = points / 100;
                        const finalAmount = Math.max(0, totalAmount - discount);
                        
                        // Update the displayed total
                        const discountRow = document.querySelector('.discount-row');
                        const finalTotalRow = document.querySelector('.final-total-row');
                        const totalRow = document.querySelector('.total-row');
                        
                        if (points > 0) {
                            if (!discountRow) {
                                // Create discount row
                                const newDiscountRow = document.createElement('tr');
                                newDiscountRow.className = 'discount-row';
                                newDiscountRow.style.backgroundColor = '#d69b68';
                                newDiscountRow.innerHTML = `
                                    <th colspan="2">Points Discount</th>
                                    <th>${points} points</th>
                                    <th class="right">-RM ${discount.toFixed(2)}</th>
                                `;
                                totalRow.after(newDiscountRow);
                                
                                // Create final total row
                                const newFinalTotalRow = document.createElement('tr');
                                newFinalTotalRow.className = 'final-total-row';
                                newFinalTotalRow.style.backgroundColor = '#d69b68';
                                newFinalTotalRow.innerHTML = `
                                    <th colspan="2">Final Total</th>
                                    <th></th>
                                    <th class="right">RM ${finalAmount.toFixed(2)}</th>
                                `;
                                newDiscountRow.after(newFinalTotalRow);
                            } else {
                                // Update existing rows
                                discountRow.innerHTML = `
                                    <th colspan="2">Points Discount</th>
                                    <th>${points} points</th>
                                    <th class="right">-RM ${discount.toFixed(2)}</th>
                                `;
                                finalTotalRow.innerHTML = `
                                    <th colspan="2">Final Total</th>
                                    <th></th>
                                    <th class="right">RM ${finalAmount.toFixed(2)}</th>
                                `;
                            }
                        } else if (discountRow) {
                            // Remove discount and final total rows if points are 0
                            discountRow.remove();
                            if (finalTotalRow) finalTotalRow.remove();
                        }
                    });
                    
                    // Add form validation
                    let isSubmitting = false;
                    const checkoutForm = document.getElementById('checkout-form');
                    const submitButton = document.querySelector('button[name="btn"][value="confirm"]');

                    checkoutForm.addEventListener('submit', function(e) {
                        if (isSubmitting) {
                            e.preventDefault();
                            return;
                        }

                        const isFormValid = validateCheckoutForm();
                        const isPointsValid = validatePoints();
                        
                        if (!isFormValid || !isPointsValid) {
                            e.preventDefault();
                            if (!isFormValid) {
                                alert('Please fill in all required fields correctly before proceeding.');
                            }
                            return;
                        }

                        isSubmitting = true;
                        submitButton.textContent = 'Processing...';
                        
                        // Reset flag after 5 seconds (in case submission fails)
                        setTimeout(() => {
                            isSubmitting = false;
                            submitButton.textContent = 'Confirm and Pay';
                        }, 5000);
                    });
                    
                    function validateCheckoutForm() {
                        let valid = true;
                        
                        // Check if using new shipping address
                        if (document.querySelector('input[name="shipping_address_option"][value="new"]:checked')) {
                            const shippingFields = document.querySelectorAll('#new_shipping_address input[required]');
                            shippingFields.forEach(field => {
                                if (!field.value.trim()) {
                                    valid = false;
                                    field.classList.add('error');
                                } else {
                                    field.classList.remove('error');
                                }
                            });
                        }
                        
                        // Check if using different billing address
                        if (!document.getElementById('same_address').checked) {
                            if (document.querySelector('input[name="billing_address_option"][value="new"]:checked')) {
                                const billingFields = document.querySelectorAll('#new_billing_address input[required]');
                                billingFields.forEach(field => {
                                    if (!field.value.trim()) {
                                        valid = false;
                                        field.classList.add('error');
                                    } else {
                                        field.classList.remove('error');
                                    }
                                });
                            }
                        }
                        
                        return valid;
                    }
                });
                </script>
            </div>
        
            <div class="payment-section">
                <h2>Payment Method</h2>
                <p>You will be redirected to Billplz to complete your payment securely.</p>
                
                <?php if (!empty($cart_items)): ?>
                    <div class="payment-info">
                        <p>By clicking 'Confirm and Pay', you agree to our terms and conditions.</p>
                        <p>Your payment will be processed securely through Billplz.</p>
                    </div>
                <?php endif; ?>
            </div>
        
            <section class="checkout-button-group">
                <a href="/page/member/cart.php" class="button">Back to Cart</a>
                
                <?php if (!empty($cart_items)): ?>
                    <button type="submit" name="btn" value="confirm" class="button">Confirm and Pay</button>
                <?php endif; ?>
            </section>
        </div>
    </form>
</div>

<?php
// Add debug statement to verify saved addresses
if (empty($saved_addresses)) {
    error_log('No saved addresses found for user ID: ' . $_user->id);
} else {
    error_log('Found ' . count($saved_addresses) . ' saved addresses for user ID: ' . $_user->id);
}
?>

<?php
include '../../../_foot.php';