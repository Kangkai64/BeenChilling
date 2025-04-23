<?php
include '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

$name = req('name');
$typeid = req('typeid');

$fields = [
    'ProductID'    => 'Product ID',
    'ProductName'  => 'Product Name'
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'ProductID';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);

$sql = "SELECT * FROM product WHERE 1";
$params = [];

if ($name) {
    $sql .= " AND ProductName LIKE ?";
    $params[] = "%$name%";
}

if ($typeid && $typeid !== 'ALL') {
    $sql .= " AND TypeID = ?";
    $params[] = $typeid;
}

$sql .= " ORDER BY $sort $dir";
$p = new SimplePager($sql, $params, 10, $page);

$arr = $p->result;

?>
    
<?php topics_text("Get a BeenChilling like John Cena."); ?>
<button class="button" data-get="product_insert.php">Insert</button>
<form >
    <div class = search-div>
        <?= html_search('name') ?> 
        <?= html_select('typeid', $_producttype, 'All') ?>
        <button class = search-bar>Search</button>
    </div>
</form>

<p class = page-nav>
    <?= $p->count ?> of <?= $p->item_count ?> record(s) | Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<table class = "product-list-table">
    <tr>
        <?= table_headers($fields, $sort, $dir, "page=$page") ?>
        <th>Action</th> 
    </tr>

    <?php foreach ($arr as $s): ?>
    
    <tr>
        <td><?=$s->ProductID ?></td>
        <td><?=$s->ProductName ?></td>
        <td>
            <button class="product-button" data-get="product_details.php?id=<?= $s->ProductID ?>">Detail</button>
            <button class="product-button" data-get="product_update.php?id=<?= $s->ProductID ?>">Update</button>
            <button class="product-button" data-post="delete.php?id=<?= $s->ProductID ?>" data-confirm>Delete</button>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<br>
<?= $p->html("name=$name&typeid=$typeid&sort=$sort&dir=$dir") ?>

<?php
include '../../_foot.php';