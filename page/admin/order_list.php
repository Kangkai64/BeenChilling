<?php
include '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

$order_id = req('order_id');
$member_id = req('member_id');
$total_amount = req('total_amount');
$payment_status = req('payment_status');

$fields = [
    'order_id'       => 'Order ID',
    'member_id'      => 'Ordered by',
    'total_amount'   => 'Total Amount',
    'payment_status' => 'Payment Status',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'order_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);

$sql = "SELECT * FROM `order` WHERE 1";
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

        <select class=search-bar name="payment_status">
            <option value="ALL" <?= $payment_status === 'ALL' ? 'selected' : '' ?>>All</option>
            <option value="pending" <?= $payment_status === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="awaiting_payment" <?= $payment_status === 'awaiting_payment' ? 'selected' : '' ?>>Awaiting Payment</option>
            <option value="paid" <?= $payment_status === 'paid' ? 'selected' : '' ?>>Paid</option>
            <option value="failed" <?= $payment_status === 'failed' ? 'selected' : '' ?>>Failed</option>
        </select>

        <button class="search-bar">Search</button>
    </div>
</form>



<p class="page-nav">
    <?= $p->count ?> of <?= $p->item_count ?> record(s) | Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<!-- Table View -->
<div id="table-view">
    <table class="product-list-table">
        <tr>
            <?= table_headers($fields, $sort, $dir, "page=$page") ?>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($arr as $s): ?>
            <?php $isPaid = ($s->payment_status === 'paid'); ?>
            <tr>
                <td><?= $s->order_id ?></td>
                <td><?= $s->member_id ?></td>
                <td><?= $s->total_amount ?></td>
                <td><?= ucwords(str_replace('_', ' ', $s->payment_status)) ?></td>

                <td>
                    <?php
                        switch ($s->payment_status) {
                            case 'pending':
                            case 'awaiting_payment':
                                echo 'Active';
                                break;
                            case 'paid':
                                echo 'Completed';
                                break;
                            case 'failed':
                                echo 'Failed';
                                break;
                            default:
                                echo 'Unknown';
                        }
                    ?>
                </td>
                <td>
                    <button class="product-button" data-get="order_detail.php?order_id=<?= $s->order_id ?>">Detail</button>
                    <form method="post" action="order_paid.php" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $s->order_id ?>">
                        <button type="submit" class="product-button">Mark as paid</button>
                    </form>
                    <form method="post" action="order_delete.php" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="order_id" value="<?= $s->order_id ?>">
                        <button type="submit" class="product-button red">Delete</button>
                    </form>
                </td>


            </tr>
        <?php endforeach; ?>
    </table>
</div>



<br>
<?= $p->html("order_id=$order_id&member_id=$member_id&payment_status=$payment_status&sort=$sort&dir=$dir") ?>

<?php include '../../_foot.php'; ?>
