<?php
require '../../_base.php';

// Make sure user is logged in
if (!$_user) {
    header("Location: /page/login.php");
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=beenchilling;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    die("Order ID not provided.");
}

// Get the order (but only if it belongs to this user)
$stmt = $pdo->prepare("
    SELECT * FROM `order`
    WHERE order_id = ? AND member_id = ?
");
$stmt->execute([$order_id, $_user->id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found or you don't have permission to view it.");
}

// Get the items for this order
$stmt = $pdo->prepare("
    SELECT order_item.*, product.name AS product_name, product.price AS product_price
    FROM order_item
    JOIN product ON order_item.product_id = product.product_id
    WHERE order_item.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$_title = "Order #$order_id";
include '../../_head.php';
?>

<h2>Order Detail - #<?= $order_id ?></h2>

<h3>Order Info</h3>
<p>
    Date: <?= $order['order_date'] ?><br>
    Status: <?= $order['order_status'] ?><br>
    Payment: <?= $order['payment_method'] ?> (<?= $order['payment_status'] ?>)<br>
    Total: $<?= $order['total_amount'] ?>
</p>

<h3>Shipping & Billing</h3>
<p>
    Shipping Address:<br>
    <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br><br>

    Billing Address:<br>
    <?= nl2br(htmlspecialchars($order['billing_address'])) ?>
</p>

<h3>Items</h3>
<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price Each</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td>$<?= number_format($item['product_price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format($item['product_price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../../_foot.php'; ?>
