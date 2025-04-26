<?php
require '../../_base.php';

auth('Member');

$_title = "BeenChilling";
include '../../_head.php';
require_once '../../lib/SimplePager.php';

$order_id = req('order_id');
$payment_status = req('payment_status');
$order_status = req('order_status');
$order_date = req('order_date');
$order_date_options = [
    'ALL' => 'All',
    'today' => 'Today',
    'this_week' => 'This Week',
    'this_month' => 'This Month',
    'this_year' => 'This Year',
];

$fields = [
    'order_id'       => 'Order ID',
    'payment_status' => 'Payment Status',
    'order_status'   => 'Order Status',
    'order_date'     => 'Order Date',
    'total_amount'   => 'Total Amount',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'order_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'desc';

$page = req('page', 1);

$sql = "SELECT `order`.*, oi.*, p.product_name, p.price, p.product_image, GROUP_CONCAT(CONCAT(p.product_name, '|', p.price, '|', p.product_image, '|', oi.quantity) SEPARATOR '||') as products
        FROM `order`
        JOIN order_item oi ON order.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.product_id
        WHERE order.member_id = ?";

$params = [$_user->id];

if ($order_id) {
    $sql .= " AND order_id LIKE ?";
    $params[] = "%$order_id%";
}

if ($payment_status && $payment_status !== 'ALL') {
    $sql .= " AND payment_status = ?";
    $params[] = $payment_status;
}

if ($order_status && $order_status !== 'ALL') {
    $sql .= " AND order_status = ?";
    $params[] = $order_status;
}

if ($order_date && $order_date !== 'ALL') {
    if ($order_date == 'today') {
        $sql .= " AND order_date = CURDATE()";
    } elseif ($order_date == 'this_week') {
        $sql .= " AND order_date BETWEEN CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY AND CURDATE() + INTERVAL 6 - WEEKDAY(CURDATE()) DAY";
    } elseif ($order_date == 'this_month') {
        $sql .= " AND order_date BETWEEN CURDATE() - INTERVAL DAYOFMONTH(CURDATE()) - 1 DAY AND LAST_DAY(CURDATE())";
    } elseif ($order_date == 'this_year') {
        $sql .= " AND order_date BETWEEN CURDATE() - INTERVAL (YEAR(CURDATE()) - 1) YEAR AND CURDATE()";
    } 
}

$sql .= " GROUP BY order.order_id";
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;

topics_text("My Order History", "350px", "order-history-button");
?>

<form>
    <div class="search-div">
        <label class="page-nav" for="order_id">Order ID:</label>
        <?= html_search('order_id') ?>

        <label class="page-nav" for="payment_status">Payment Status:</label>
        <?= html_select('payment_status', $payment_status_options, 'All') ?>
        <br>

        <label class="page-nav" for="order_status">Order Status:</label>
        <?= html_select('order_status', $order_status_options, 'All') ?>

        <label class="page-nav" for="order_date">Order Date:</label>
        <?= html_select('order_date', $order_date_options, 'All') ?>

        <button class="search-bar">Search</button>
    </div>
</form>

<p class="page-nav">
    <?= $p->count ?> of <?= $p->item_count ?> record(s) | Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<section class="view-button">
    <button id="table-view-button" class="active_link">Table View</button>
    <button id="photo-view-button">Photo View</button>
</section>

<!-- Table View -->
<div id="table-view">
    <table class="product-list-table">
        <tr>
            <?= table_headers($fields, $sort, $dir, "page=$page") ?>
            <th>Actions</th>
        </tr>
        <?php foreach ($arr as $order): ?>
            <tr>
                <td><?= $order->order_id ?></td>
                <td><?= ucwords(str_replace('_', ' ', $order->payment_status)) ?></td>
                <td><?= ucwords(str_replace('_', ' ', $order->order_status)) ?></td>
                <td><?= $order->order_date ?></td>
                <td>RM <?= number_format($order->total_amount, 2) ?></td>
                <td>
                    <button class="product-button" data-get="order_detail.php?order_id=<?= $order->order_id ?>">Details</button>
                    <button class="product-button" onclick="showOrderDetails(<?= htmlspecialchars(json_encode($order)) ?>)">
                        View Products
                    </button>
                    <?php if ($order->payment_status == 'awaiting_payment'): ?>
                        <button class="product-button" data-post="payment/checkout.php?order_id=<?= $order->order_id ?>">Continue payment</button>
                    <?php endif; ?>
                    <button class="product-button" data-post="cancel_order.php?order_id=<?= $order->order_id ?>" data-confirm>Cancel Order</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Photo View -->
<div id="photo-view">
    <div class="container">
        <div class='product-container'>
            <?php foreach ($arr as $order): ?>
                <?php photo_view($order->order_id, $order->product_name, "/images/product/" . $order->product_image, "order_detail.php?order_id=" . $order->order_id, "order_update.php?order_id=" . $order->order_id, "order_delete.php?order_id=" . $order->order_id); ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<br>
<?= $p->html("order_id=$order_id&payment_status=$payment_status&order_status=$order_status&order_date=$order_date&sort=$sort&dir=$dir") ?>

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
