<?php
require '../../_base.php';

auth('Admin');
$_title = 'Order Detail';

include '../../_head.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("Order ID not provided.");
}

$pdo = new PDO('mysql:host=localhost;dbname=beenchilling;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get order + user info
$stmt = $pdo->prepare("
    SELECT `order`.*, user.name, user.email 
    FROM `order`
    JOIN user ON `order`.member_id = user.id
    WHERE `order`.order_id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_OBJ);

if (!$order) {
    die("Order not found.");
}

// Get items in this order
$stmt = $pdo->prepare("
    SELECT order_item.*, product.ProductName AS product_name, product.Price AS product_price 
    FROM order_item
    JOIN product ON order_item.product_id = product.ProductID
    WHERE order_item.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<h2 class="page-nav">Order Detail   </h2>

<table class="product-list-table member-details">
    <tr>
        <th>Order ID</th>
        <td><?= $order->order_id ?></td>
    </tr>
    <tr>
        <th>Order Date</th>
        <td><?= $order->order_date ?></td>
    </tr>
    <tr>
        <th>Customer Name</th>
        <td><?= $order->name ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= $order->email ?></td>
    </tr>
    <tr>
        <th>Total Amount</th>
        <td>$<?= $order->total_amount ?></td>
    </tr>
    <tr>
        <th>Payment Method</th>
        <td><?= $order->payment_method ?> (<?= $order->payment_status ?>)</td>
    </tr>
    <tr>
        <th>Payment Status</th>
        <td><?= ucwords(str_replace('_', ' ', strtolower($order->payment_status))) ?></td>
    </tr>

    <tr>
        <th>Shipping Address</th>
        <td><?= nl2br(htmlspecialchars($order->shipping_address)) ?></td>
    </tr>
    <tr>
        <th>Billing Address</th>
        <td><?= nl2br(htmlspecialchars($order->billing_address)) ?></td>
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
            <td><?= htmlspecialchars($item->product_name) ?></td>
            <td>$<?= number_format($item->product_price, 2) ?></td>
            <td><?= $item->quantity ?></td>
            <td>$<?= number_format($item->product_price * $item->quantity, 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<button class="button" data-get="order_list.php">Back</button>

<?php include '../../_foot.php'; ?>
