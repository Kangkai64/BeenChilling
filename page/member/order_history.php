<?php
require '../../_base.php';

// Make sure user is logged in
if (!$_user) {
    header("Location: /page/login.php");
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=beenchilling;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get all orders by this member
$stmt = $pdo->prepare("
    SELECT order_id, order_date, order_status, total_amount 
    FROM `order`
    WHERE member_id = ?
    ORDER BY order_date DESC
");
$stmt->execute([$_user->id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$_title = "My Order History";
include '../../_head.php';
?>

<h2>My Order History</h2>

<?php if (empty($orders)): ?>
    <p>You haven't placed any orders yet.</p>
<?php else: ?>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= $order['order_date'] ?></td>
                <td><?= $order['order_status'] ?></td>
                <td>$<?= $order['total_amount'] ?></td>
                <td><a href="order_detail.php?id=<?= $order['order_id'] ?>">View</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../../_foot.php'; ?>
