<?php
include '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

$id = req('id');
$full_name = req('full_name');
$role = req('role');
$status = req('status');
$updated_at = req('updated_at');

$updated_at_options = [
    'today' => 'Today',
    'this_week' => 'This Week',
    'this_month' => 'This Month',
    'this_year' => 'This Year'
];

$fields = [
    'id'    => 'User ID',
    'name'  => 'Full Name',
    'status' => 'Account Status',
    'updated_at' => 'Updated At'
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);

$sql = "SELECT * FROM user WHERE 1";
$params = [];

if ($full_name) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$full_name%";
}

if ($role && $role !== 'ALL') {
    $sql .= " AND role = ?";
    $params[] = $role;
}

if ($status && $status !== 'ALL') {
    $sql .= " AND status = ?";
    $params[] = $status;
}

if ($updated_at && $updated_at !== 'ALL') {
    if ($updated_at == 'today') {
        $sql .= " AND updated_at = CURDATE()";
    } elseif ($updated_at == 'this_week') {
        $sql .= " AND updated_at BETWEEN CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY AND CURDATE() + INTERVAL 6 - WEEKDAY(CURDATE()) DAY";
    } elseif ($updated_at == 'this_month') {
        $sql .= " AND updated_at BETWEEN CURDATE() - INTERVAL DAYOFMONTH(CURDATE()) - 1 DAY AND LAST_DAY(CURDATE())";
    } elseif ($updated_at == 'this_year') {
        $sql .= " AND updated_at BETWEEN CURDATE() - INTERVAL (YEAR(CURDATE()) - 1) YEAR AND CURDATE()";
    } 
}

$sql .= " ORDER BY $sort $dir";
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;

?>

<form>
    <div class=search-div>
        <label class="page-nav" for="full_name">Full Name:</label>
        <?= html_search('full_name') ?>
        <label class="page-nav" for="role">Role:</label>
        <?= html_select('role', $role_options, 'All') ?>
        <br>
        <label class="page-nav" for="status">Account Status:</label>
        <?= html_select('status', $status_options, 'All') ?>
        <label class="page-nav" for="updated_at">Updated At:</label>
        <?= html_select('updated_at', $updated_at_options, 'All') ?>
        <button class=search-bar>Search</button>
    </div>
</form>

<p class=page-nav>
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
            <th>Action</th>
        </tr>

        <?php foreach ($arr as $s): ?>
            <tr>
                <td><?= $s->id ?></td>
                <td><?= $s->name ?></td>
                <td><?= $status_options[$s->status] ?></td>
                <td style="width: 15%;"><?= $s->updated_at ?></td>
                <td>
                    <button class="product-button" data-get="user_details.php?id=<?= $s->id ?>">Detail</button>
                    <button class="product-button" data-get="user_update.php?id=<?= $s->id ?>">Update</button>
                    <?php if($s->status == 2): ?>
                        <button class="product-button" data-post="user_deactivate.php?id=<?= $s->id ?>" data-confirm>Deactivate</button>
                    <?php elseif($s->status == 0 || $s->status == 1): ?>
                        <button class="product-button" data-post="user_activate.php?id=<?= $s->id ?>" data-confirm>Activate</button>
                    <?php endif; ?>
                    <button class="product-button" data-post="user_delete.php?id=<?= $s->id ?>" data-confirm>Delete</button>
                    <div class="popup">
                        <img src="../../images/photo/<?= $s->photo ?>">
                    </div>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>

<!-- Photo View -->
<div id="photo-view">
    <div class="container">
        <div class='product-container'>
            <?php foreach ($arr as $s): ?>
                <?php photo_view($s->id, $s->name, "/images/photo/".$s->photo, "user_details.php?id=".$s->id, "user_update.php?id=".$s->id, "user_delete.php?id=".$s->id);?>
            <?php endforeach ?>
        </div>
    </div>
</div>

<div class="button-group">
    <button class="button" data-get="user_insert.php">Add New User</button>
    <button class="button" data-get="batch_operation.php?table=user">Batch Operations</button>
</div>

<br>
<?= $p->html("id=$id&full_name=$full_name&role=$role&sort=$sort&dir=$dir") ?>

<?php
include '../../_foot.php';