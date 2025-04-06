<?php
include '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

$id = req('id');
$name = req('name');
$role = req('role');

$fields = [
    'id'    => 'User ID',
    'name'  => 'Full Name'
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);

$sql = "SELECT * FROM user WHERE 1";
$params = [];

if ($name) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$name%";
}

if ($role && $role !== 'ALL') {
    $sql .= " AND role = ?";
    $params[] = $role;
}

$sql .= " ORDER BY $sort $dir";
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;

?>

<form>
    <div class=search-div>
        <?= html_search('name') ?>
        <?= html_select('role', $role_options, 'All') ?>
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
                <td>
                    <button class="product-button" data-get="user_details.php?id=<?= $s->id ?>">Detail</button>
                    <button class="product-button" data-get="user_update.php?id=<?= $s->id ?>">Update</button>
                    <button class="product-button" data-post="user_delete.php?id=<?= $s->id ?>" data-confirm>Delete</button>
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
                <?php photo_view($s->id, $s->name, "/images/photo/".$s->photo, "user_details.php", "user_update.php", "user_delete.php");?>
            <?php endforeach ?>
        </div>
    </div>
</div>

<button class="button" data-get="user_insert.php?id=<?= $s->id ?>">Add New User</button>

<br>
<?= $p->html("id=$id&name=$name&sort=$sort&dir=$dir") ?>

<?php
include '../../_foot.php';
