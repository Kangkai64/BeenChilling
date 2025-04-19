<?php
require '../../_base.php';

$pdo = new PDO('mysql:host=localhost;dbname=beenchilling;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Corrected joins and field names
$stmt = $pdo->query("
    SELECT order_item.*, user.name, `order`.order_date, `order`.order_status, `order`.total_amount 
    FROM order_item
    JOIN `order` ON order_item.order_id = `order`.order_id
    JOIN user ON `order`.member_id = user.id
    ORDER BY `order`.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$_title = "All Orders";
include '../../_head.php';
?>

<h2>All Orders</h2>
<ul>
<?php foreach ($orders as $order): ?>
    <li>
        <a href="order_detail.php?id=<?= $order['order_id'] ?>">Order #<?= $order['order_id'] ?></a> 
        - <?= $order['name'] ?> - <?= $order['order_date'] ?> - <?= $order['order_status'] ?> - $<?= $order['total_amount'] ?>
    </li>
<?php endforeach; ?>
</ul>

<?php include '../../_foot.php'; ?>