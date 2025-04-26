<?php
include '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

$order_id = req('order_id');
$member_id = req('member_id');
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
    'member_id'      => 'Ordered by',
    'total_amount'   => 'Total Amount',
    'payment_status' => 'Payment Status',
    'order_status'   => 'Order Status',
    'order_date'     => 'Order Date',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'order_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'desc';

$page = req('page', 1);

$sql = "SELECT `order`.*, user.name, user.email, user.phone_number, user.photo
        FROM `order`
        JOIN user ON order.member_id = user.id
        WHERE 1";
$params = [];

if ($order_id) {
    $sql .= " AND order_id LIKE ?";
    $params[] = "%$order_id%";
}

if ($member_id) {
    $sql .= " AND member_id LIKE ?";
    $params[] = "%$member_id%";
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

$sql .= " ORDER BY $sort $dir";
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;
?>

<form>
    <div class="search-div">
        <label class="page-nav" for="order_id">Order ID:</label>
        <?= html_search('order_id') ?>

        <label class="page-nav" for="member_id">Member ID:</label>
        <?= html_search('member_id') ?>
        <br>

        <label class="page-nav" for="payment_status">Payment Status:</label>
        <?= html_select('payment_status', $payment_status_options, 'All') ?>
        
        <label class="page-nav" for="order_status">Order Status:</label>
        <?= html_select('order_status', $order_status_options, 'All') ?>

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
    <table class="product-list-table" style="width: 90%; max-width: 1200px;">
        <tr>
            <?= table_headers($fields, $sort, $dir, "page=$page") ?>
            <th>Action</th>
        </tr>

        <?php foreach ($arr as $s): ?>
            <?php $isPaid = ($s->payment_status === 'paid'); ?>
            <tr>
                <td><?= $s->order_id ?></td>
                <td><?= $s->name ?></td>
                <td>RM<?= $s->total_amount ?></td>
                <td><?= ucwords(str_replace('_', ' ', $s->payment_status)) ?></td>
                <td>
                    <?= ucwords(str_replace('_', ' ', $s->order_status)) ?>
                </td>
                <td>
                    <?= $s->order_date ?>
                </td>
                <td>
                    <button class="product-button" data-get="order_detail.php?order_id=<?= $s->order_id ?>">Detail</button>
                    <button class="product-button" data-get="order_update.php?order_id=<?= $s->order_id ?>">Update</button>
                    <?php if ($s->order_status == 'cancelled'): ?>
                        <button class="product-button" data-get="order_refund.php?order_id=<?= $s->order_id ?>" data-confirm>Refund</button>
                    <?php endif; ?>
                    <button class="product-button" data-post="order_delete.php?order_id=<?= $s->order_id ?>" data-confirm>Delete</button>
                    <div class="popup" style="left:95%; top:-15%;">
                        <img src="../../images/photo/<?= $s->photo ?>">
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Photo View -->
<div id="photo-view">
    <div class="container">
        <div class='product-container'>
            <?php foreach ($arr as $s): ?>
                <?php photo_view($s->order_id, $s->name, "/images/photo/".$s->photo, "order_detail.php?order_id=".$s->order_id, "order_update.php?order_id=".$s->order_id, "order_delete.php?order_id=".$s->order_id);?>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<button class="button" data-get="batch_operation.php?table=order">Batch Operations</button>

<br>
<?= $p->html("order_id=$order_id&member_id=$member_id&payment_status=$payment_status&sort=$sort&dir=$dir") ?>

<?php 
include '../../_foot.php';