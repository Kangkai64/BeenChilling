<?php
require '../../_base.php';

auth();

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    die("Order ID not provided.");
}

// Get the order (but only if it belongs to this user)
$stmt = $_db->prepare("
    SELECT * FROM `order`
    WHERE order_id = ? AND member_id = ?
");
$stmt->execute([$order_id, $_user->id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found or you don't have permission to view it.");
}

// Get the items for this order
$stmt = $_db->prepare("
    SELECT order_item.*, product.product_name, product.price AS product_price
    FROM order_item
    JOIN product ON order_item.product_id = product.product_id
    WHERE order_item.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$_title = "Order #$order_id";
include '../../_head.php';
?>

<h2 class="page-nav">Order Detail</h2>

<table class="product-list-table member-details">
    <tr>
        <th>Order ID</th>
        <td><?= $order['order_id'] ?></td>
    </tr>
    <tr>
        <th>Order Date</th>
        <td><?= $order['order_date'] ?></td>
    </tr>
    <tr>
        <th>Total Amount</th>
        <td>$<?= number_format($order['total_amount'], 2) ?></td>
    </tr>
    <tr>
        <th>Payment Method</th>
        <td><?= $order['payment_method'] ?> (<?= $order['payment_status'] ?>)</td>
    </tr>
    <tr>
        <th>Payment Status</th>
        <td><?= ucwords(str_replace('_', ' ', strtolower($order['payment_status']))) ?></td>
    </tr>
    <tr>
        <th>Shipping Address</th>
        <td><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></td>
    </tr>
    <tr>
        <th>Billing Address</th>
        <td><?= nl2br(htmlspecialchars($order['billing_address'])) ?></td>
    </tr>
</table>

<br>
<h3 class="page-nav">Items in Order</h3>
<table class="product-list-table">
    <tr>
        <th>Product</th>
        <th>Price Each</th>
        <th>Quantity</th>
        <th>Subtotal</th>
    </tr>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td>$<?= number_format($item['product_price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format($item['product_price'] * $item['quantity'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<button class="button" data-get="order_history.php">Back to Orders</button>

<?php 
include '../../_foot.php';