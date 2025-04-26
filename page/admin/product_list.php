<?php
include '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

$name = req('name');
$typeid = req('typeid');

$fields = [
    'product_id'    => 'Product ID',
    'product_name'  => 'Product Name'
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'product_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);

try {
    // Build SQL query
    $sql = "SELECT * FROM product WHERE 1";
    $params = [];

    if ($name) {
        $sql .= " AND product_name LIKE ?";
        $params[] = "%$name%";
    }

    if ($typeid && $typeid !== 'ALL') {
        $sql .= " AND type_id = ?";
        $params[] = $typeid;
    }

    $sql .= " ORDER BY $sort $dir";

    // Use SimplePager for pagination
    $p = new SimplePager($sql, $params, 10, $page);
    $arr = $p->result;
} catch (PDOException $e) {
    $_err['db'] = 'Database error: ' . $e->getMessage();
    $arr = [];
    $p = new stdClass();
    $p->count = 0;
    $p->item_count = 0;
    $p->page = 1;
    $p->page_count = 1;
}
?>

<?php topics_text("Get a BeenChilling like John Cena."); ?>
<button class="button" data-get="product_insert.php">Insert</button>
<form>
    <div class="search-div">
        <?= html_search('name') ?>
        <?= html_select('type_id', $_product_type, 'All') ?>
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
            <th>Action</th>
        </tr>

        <?php foreach ($arr as $s): ?>
            <tr>
                <td><?= $s->product_id ?></td>
                <td><?= $s->product_name ?></td>
                <td>
                    <button class="product-button" data-get="product_details.php?id=<?= $s->product_id ?>">Detail</button>
                    <button class="product-button" data-get="product_update.php?id=<?= $s->product_id ?>">Update</button>
                    <button class="product-button" data-post="delete.php?id=<?= $s->product_id ?>" data-confirm>Delete</button>
                    <div class="popup">
                        <img src="../../images/product/<?= $s->product_image ?>">
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
                <?php photo_view($s->product_id, $s->product_name, "/images/product/" . $s->product_image, "product_details.php?id=".$s->product_id, "product_update.php?id=".$s->product_id, "delete.php?id=".$s->product_id); ?>
            <?php endforeach ?>
        </div>
    </div>
</div>

<br>
<?= $p->html("name=$name&typeid=$typeid&sort=$sort&dir=$dir") ?>

<?php
include '../../_foot.php';
