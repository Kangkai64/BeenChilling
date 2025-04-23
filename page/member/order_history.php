<?php
require '../../_base.php';

auth('Member');

// Get all orders by this member
$stm = $_db->prepare("
    SELECT o.*, u.photo 
    FROM `order` o
    JOIN user u ON o.member_id = u.id
    WHERE o.member_id = ?
    ORDER BY o.order_date DESC
");

$stm->execute([$_user->id]);
$orders = $stm->fetchAll(PDO::FETCH_OBJ);

$_title = "BeenChilling";
include '../../_head.php';

topics_text("My Order History", "350px", "order-history-button");
?>

<table class="product-list-table">
    <tr>
        <th>Order ID</th>
        <th>Member ID</th>
        <th>Cart ID</th>
        <th>Order Date</th>
        <th>Status</th>
        <th>Details</th>
    </tr>

    <?php if ($orders): ?>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order->order_id ?></td>
                <td><?= $order->member_id ?></td>
                <td><?= $order->cart_id ?></td>
                <td><?= $order->order_date ?></td>
                <td><?= $order->order_status ?></td>
                <td>
                    <button class="product-button" data-get="order_detail.php?id=<?= $order->order_id ?>">
                        Details
                    </button>
                    <div class="popup">
                        <img src="../../images/photo/<?= $order->photo ?>">
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="center">You haven't placed any order yet.</td>
        </tr>
    <?php endif; ?>
</table>

<section class="button-group">
    <button class="button" data-get="/">Back</button>
</section>

<?php 
include '../../_foot.php';