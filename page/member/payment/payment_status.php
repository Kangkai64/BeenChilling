<?php
require '../../../_base.php';

// Ensure user is logged in as a member
if (!$_user || $_user->role != 'Member') {
    redirect('/page/login.php');
}

$_title = 'BeenChilling - Payment Status';
include '../../../_head.php';

// Get order ID from URL parameter
$order_id = req('order_id');
$payment_status = '';
$order_details = null;

// Log access to the payment status page for debugging
error_log("Payment status page accessed for order: " . $order_id . " by user: " . $_user->id);

// Validate order belongs to logged-in user
if (!empty($order_id)) {
    $stm = $_db->prepare('
        SELECT o.*, 
               SUM(oi.quantity) as total_items,
               o.payment_status,
               o.order_status,
               o.transaction_id,
               o.payment_date
        FROM `order` o
        LEFT JOIN order_item oi ON o.order_id = oi.order_id
        WHERE o.order_id = ? AND o.member_id = ?
        GROUP BY o.order_id
    ');
    $stm->execute([$order_id, $_user->id]);
    $order_details = $stm->fetch(PDO::FETCH_OBJ);
    
    if ($order_details) {
        $payment_status = $order_details->payment_status;
        error_log("Found order: " . json_encode($order_details));
    } else {
        $error = "Order not found or unauthorized access.";
        error_log("Order not found or unauthorized: " . $order_id);
    }
} else {
    $error = "Invalid order reference.";
    error_log("Invalid order reference");
}

// Process Billplz redirect
if (isset($_GET['billplz'])) {
    error_log("Billplz redirect received: " . json_encode($_GET));
    
    // Check all possible payment status indicators
    $bill_id = $_GET['id'] ?? '';
    $paid_status = $_GET['paid'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $payment_successful = false;
    
    // Determine if payment was successful from various possible parameters
    if ($paid_status === 'true' || $status === 'paid' || $status === 'completed') {
        $payment_successful = true;
    }
    
    // Update order status if needed (as a backup to the callback)
    if (!empty($order_id) && $order_details && $order_details->payment_status === 'pending') {
        $new_status = $payment_successful ? 'paid' : 'failed';
        $new_order_status = $payment_successful ? 'processing' : 'cancelled';
        
        error_log("Updating order via redirect: " . $order_id . " to status: " . $new_status);
        
        $stm = $_db->prepare('
            UPDATE `order` 
            SET payment_status = ?, 
                order_status = ?,
                payment_date = NOW()
            WHERE order_id = ? AND member_id = ?
        ');
        $stm->execute([$new_status, $new_order_status, $order_id, $_user->id]);
        
        // Refresh order details
        $stm = $_db->prepare('
            SELECT o.*, 
                   SUM(oi.quantity) as total_items
            FROM `order` o
            LEFT JOIN order_item oi ON o.order_id = oi.order_id
            WHERE o.order_id = ? AND o.member_id = ?
            GROUP BY o.order_id
        ');
        $stm->execute([$order_id, $_user->id]);
        $order_details = $stm->fetch(PDO::FETCH_OBJ);
        $payment_status = $order_details->payment_status;
        
        error_log("Order updated via redirect: " . json_encode($order_details));
    }
}

topics_text("Payment Status", "200px");
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="payment-status-container">
    <?php if ($order_details): ?>
        <div class="order-summary">
            <h2>Order #<?= htmlspecialchars($order_details->order_id) ?></h2>
            
            <div class="status-info <?= $payment_status ?>">
                <?php if ($payment_status === 'paid'): ?>
                    <div class="status-icon success">✓</div>
                    <h3>Payment Successful</h3>
                    <p>Thank you for your purchase! Your payment has been received and your order is now being processed.</p>
                    <?php
                    // Display earned reward points
                    $earned_points = floor($order_details->total_amount);
                    ?>
                    <div class="reward-points-earned">
                    <p><i class="fas fa-gift"></i> You earned <strong><?= $earned_points ?> reward points</strong> with this purchase!</p>
                    </div>
                <?php elseif ($payment_status === 'failed'): ?>
                    <div class="status-icon failed">✗</div>
                    <h3>Payment Failed</h3>
                    <p>Unfortunately, your payment was not successful. Please try again or use a different payment method.</p>
                <?php else: ?>
                    <div class="status-icon pending">⟳</div>
                    <h3>Payment Pending</h3>
                    <p>We are still waiting for confirmation of your payment. This page will update once the payment is confirmed.</p>
                    <script>
                        // Auto-refresh the page every 10 seconds to check for payment status updates
                        setTimeout(function() {
                            window.location.reload();
                        }, 10000);
                    </script>
                <?php endif; ?>
            </div>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <ul>
                    <li><strong>Order Date:</strong> <?= date('d M Y, h:i A', strtotime($order_details->order_date)) ?></li>
                    <li><strong>Total Amount:</strong> RM <?= number_format($order_details->total_amount, 2) ?></li>
                    <li><strong>Items:</strong> <?= $order_details->total_items ?? 0 ?></li>
                    <li><strong>Payment Status:</strong> <?= ucfirst($order_details->payment_status ?? 'Unknown') ?></li>
                    <li><strong>Order Status:</strong> <?= ucfirst($order_details->order_status ?? 'Unknown') ?></li>
                    <?php if (!empty($order_details->transaction_id)): ?>
                    <li><strong>Transaction ID:</strong> <?= htmlspecialchars($order_details->transaction_id) ?></li>
                    <?php endif; ?>
                    <?php if (!empty($order_details->payment_date)): ?>
                    <li><strong>Payment Date:</strong> <?= date('d M Y, h:i A', strtotime($order_details->payment_date)) ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div class="button-group">
            <?php if ($payment_status === 'paid'): ?>
                <button class="button primary" data-get="/page/member/orders.php">View My Orders</button>
            <?php elseif ($payment_status === 'failed'): ?>
                <button class="button" data-get="/page/member/cart.php">Return to Cart</button>
                <button class="button primary" data-get="/page/member/checkout.php">Try Again</button>
            <?php else: ?>
                <button class="button" data-get="/page/member/cart.php">Return to Cart</button>
            <?php endif; ?>
            
            <button class="button" data-get="/page/member/products.php">Continue Shopping</button>
        </div>
    <?php else: ?>
        <div class="error-container">
            <h2>Order Not Found</h2>
            <p>We couldn't find the order you're looking for. Please check your order ID or contact customer support.</p>
            
            <div class="button-group">
                <button class="button" data-get="/page/member/orders.php">View My Orders</button>
                <button class="button primary" data-get="/page/member/products.php">Continue Shopping</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.payment-status-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.status-info {
    text-align: center;
    padding: 30px;
    margin: 20px 0;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.status-info.paid {
    background-color: #f0f7f0;
    border: 1px solid #d4e9d4;
}

.status-info.failed {
    background-color: #fdf1f0;
    border: 1px solid #f9d7d3;
}

.status-info.pending {
    background-color: #fef9e7;
    border: 1px solid #fcefbc;
}

.status-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.status-icon.success {
    color: #28a745;
}

.status-icon.failed {
    color: #dc3545;
}

.status-icon.pending {
    color: #ffc107;
}

.order-details {
    margin-top: 30px;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.order-details ul {
    list-style: none;
    padding: 0;
}

.order-details li {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.button-group {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    gap: 15px;
}

.error-container {
    text-align: center;
    padding: 40px 20px;
}
</style>

<?php
include '../../../_foot.php';
?>