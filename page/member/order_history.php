<?php
require '../../_base.php';

auth('Member');

// Get all orders by this member
$stm = $_db->prepare("
    SELECT o.*, u.photo,
           GROUP_CONCAT(CONCAT(p.product_name, '|', p.price, '|', p.product_image, '|', oi.quantity) SEPARATOR '||') as products
    FROM `order` o
    JOIN user u ON o.member_id = u.id
    JOIN order_item oi ON o.order_id = oi.order_id
    JOIN product p ON oi.product_id = p.product_id
    WHERE o.member_id = ?
    GROUP BY o.order_id
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
        <th>Order Date</th>
        <th>Status</th>
        <th>Total Amount</th>
        <th>Details</th>
    </tr>

    <?php if ($orders): ?>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order->order_id ?></td>
                <td><?= $order->order_date ?></td>
                <td><?= $order->order_status ?></td>
                <td>RM <?= number_format($order->total_amount, 2) ?></td>
                <td>
                    <button class="product-button" data-get="order_detail.php?id=<?= $order->order_id ?>">Details</button>
                    <button class="product-button" onclick="showOrderDetails(<?= htmlspecialchars(json_encode($order)) ?>)">
                        View Products
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="center">You haven't placed any order yet.</td>
        </tr>
    <?php endif; ?>
</table>

<div id="order-details-popup" class="cart-popup">
    <div class="cart-popup-content">
        <span class="close-popup">&times;</span>
        <h2>Order Details</h2>
        <div id="order-products-grid" class="order-products-grid"></div>
    </div>
</div>

<section class="button-group">
    <button class="button" data-get="/">Back</button>
</section>

<?php 
include '../../_foot.php';