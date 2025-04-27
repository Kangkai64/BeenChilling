<?php
include '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

$product_name = req('product_name');
$type_id = req('type_id');
$min_price = req('min_price');
$max_price = req('max_price');

$fields = [
    'product_id'        => 'Product ID',
    'product_name'      => 'Product Name',
    'stock'             => 'Stock',
    'product_status'    => 'Status'
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'product_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);

// Get product types for buttons
$product_types = $_db->query('SELECT type_id, type_name FROM product_type ORDER BY type_name')
    ->fetchAll(PDO::FETCH_OBJ);

try {
    // Build SQL query
    $sql = "SELECT p.*, t.type_name FROM product p 
             LEFT JOIN product_type t ON p.type_id = t.type_id 
             WHERE 1";
    $params = [];

    if ($product_name) {
        $sql .= " AND p.product_name LIKE ?";
        $params[] = "%$product_name%";
    }

    if ($type_id && $type_id !== 'ALL') {
        $sql .= " AND p.type_id = ?";
        $params[] = $type_id;
    }

    if ($min_price !== '') {
        $sql .= " AND p.price >= ?";
        $params[] = $min_price;
    }

    if ($max_price !== '') {
        $sql .= " AND p.price <= ?";
        $params[] = $max_price;
    }

    $sql .= " ORDER BY p.$sort $dir";

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

// Get low stock products
$low_stock_products = [];
try {
    $low_stock_sql = "SELECT p.*, t.type_name 
                       FROM product p 
                       LEFT JOIN product_type t ON p.type_id = t.type_id 
                       WHERE p.stock <= 10 AND p.product_status = 'Active'
                       ORDER BY p.stock ASC";
    $low_stock_products = $_db->query($low_stock_sql)->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    $_err['db'] = 'Database error: ' . $e->getMessage();
}
?>

<form>
    <div class="search-div">
        <label class="page-nav" for="product_name">Product Name:</label>
        <?= html_search('product_name') ?>

        <div class="search-bar">
            <label class="page-nav" for="min_price">Price Range:</label>
            <input class="price-bar" type="number" name="min_price" placeholder="Min Price" step="0.01" min="0" value="<?= htmlspecialchars($min_price) ?>">
            <span>to</span>
            <input class="price-bar" type="number" name="max_price" placeholder="Max Price" step="0.01" min="0" value="<?= htmlspecialchars($max_price) ?>">
            <button class="search-bar">Search</button>
        </div>
    </div>
    <div class="filter-buttons">
        <button type="button" class="search-bar <?= (!$type_id || $type_id === 'ALL') ? 'active' : '' ?>"
            onclick="window.location.href='?typeid=ALL<?= $name ? '&name=' . urlencode($name) : '' ?><?= $min_price ? '&min_price=' . $min_price : '' ?><?= $max_price ? '&max_price=' . $max_price : '' ?>'">
            All
        </button>
        <?php foreach ($product_types as $type): ?>
            <button type="button" class="search-bar <?= $type_id == $type->type_id ? 'active' : '' ?>"
                onclick="window.location.href='?typeid=<?= $type->type_id ?><?= $name ? '&name=' . urlencode($name) : '' ?><?= $min_price ? '&min_price=' . $min_price : '' ?><?= $max_price ? '&max_price=' . $max_price : '' ?>'">
                <?= htmlspecialchars($type->type_name) ?>
            </button>
        <?php endforeach ?>
    </div>
</form>

<?php if (!empty($low_stock_products)): ?>
    <div class="low-stock-alert">
        <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h3>
        <div class="alert-content">
            <table class="alert-table">
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Type</th>
                    <th>Current Stock</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($low_stock_products as $product): ?>
                    <tr>
                        <td><?= $product->product_id ?></td>
                        <td><?= $product->product_name ?></td>
                        <td><?= $product->type_name ?></td>
                        <td class="stock-warning"><?= $product->stock ?></td>
                        <td>
                            <button class="product-button" data-get="product_update.php?id=<?= $product->product_id ?>">Restock</button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
<?php endif ?>

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
            <th>Type</th>
            <th>Action</th>
        </tr>

        <?php foreach ($arr as $s): ?>
            <tr>
                <td><?= $s->product_id ?></td>
                <td><?= $s->product_name ?></td>
                <td><?= $s->stock ?></td>
                <td><?= $s->product_status ?></td>
                <td><?= $s->type_name ?></td>
                <td>
                    <button class="product-button" data-get="product_details.php?id=<?= $s->product_id ?>">Detail</button>
                    <button class="product-button" data-get="product_update.php?id=<?= $s->product_id ?>">Update</button>
                    <?php if ($s->stock == 0): ?>
                        <button class="product-button" data-get="product_update.php?id=<?= $s->product_id ?>" data-confirm>Restock</button>
                    <?php elseif ($s->product_status === 'Active'): ?>
                        <button class="product-button" data-post="product_deactivate.php?id=<?= $s->product_id ?>" data-confirm>Deactivate</button>
                    <?php else: ?>
                        <button class="product-button" data-post="product_activate.php?id=<?= $s->product_id ?>" data-confirm>Activate</button>
                    <?php endif; ?>
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
    <div class="filter-class">
        <span>Sort by:</span>
        <?php foreach ($fields as $field => $label): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => $field, 'dir' => $sort === $field && $dir === 'asc' ? 'desc' : 'asc'])) ?>"
                class="sort-link <?= $sort === $field ? 'active' : '' ?>">
                <?= $label ?>
                <?php if ($sort === $field): ?>
                    <i class="fas fa-chevron-<?= $dir === 'asc' ? 'up' : 'down' ?>"></i>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="container">
        <div class='product-container'>
            <?php foreach ($arr as $s): ?>
                <?php photo_view($s->product_id, $s->product_name, "/images/product/" . $s->product_image, "product_details.php?id=" . $s->product_id, "product_update.php?id=" . $s->product_id, "product_delete.php?id=" . $s->product_id); ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<br>
<?= $p->html("product_name=$product_name&type_id=$type_id&min_price=$min_price&max_price=$max_price&sort=$sort&dir=$dir") ?>

<section class="button-group">
    <button class="button" data-get="product_insert.php">Add New Product</button>
    <button class="button" data-get="batch_operation.php?table=product">Batch Operation</button>
</section>

<?php
include '../../_foot.php';