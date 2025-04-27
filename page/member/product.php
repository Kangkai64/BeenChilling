<?php
require '../../_base.php';
require_once '../../lib/SimplePager.php';

// Handle AJAX requests - Must be at the top before any output
if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $product_id = isset($_POST['id']) ? $_POST['id'] : null;
    $quantity = isset($_POST['unit']) ? (int)$_POST['unit'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : 'cart'; // 'cart' or 'wishlist'
    $response = ['success' => false];

    if ($product_id) {
        if ($action === 'cart') {
            // Handle cart updates
            if ($_user && $_user->role == 'Member') {
                $cart = get_or_create_cart();
                update_cart_item($cart->cart_id, $product_id, $quantity);
                $cart_summary = get_cart_summary($cart->cart_id);
                $cart_count = $cart_summary->total_items ?? 0;
                $total = number_format($cart_summary->total_price ?? 0, 2);

                $response = [
                    'success' => true,
                    'product_id' => $product_id,
                    'total' => $total,
                    'cart_count' => $cart_count,
                    'message' => 'Cart updated successfully!'
                ];
            } else {
                update_session_cart_item($product_id, $quantity);
                $cart_count = $_SESSION['cart']['total_items'] ?? 0;
                $total = number_format($_SESSION['cart']['total_price'] ?? 0, 2);

                $response = [
                    'success' => true,
                    'product_id' => $product_id,
                    'total' => $total,
                    'cart_count' => $cart_count,
                    'message' => 'Cart updated successfully!'
                ];
            }
        } else if ($action === 'wishlist') {
            // Handle wishlist updates
            if ($_user && $_user->role == 'Member') {
                update_wishlist_item($product_id, 1); // Update with quantity 1

                // Get updated wishlist count
                $wishlist_count = get_wishlist_count();

                $response = [
                    'success' => true,
                    'product_id' => $product_id,
                    'wishlist_count' => $wishlist_count,
                    'message' => 'Wishlist updated successfully!'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'You must be logged in to use the wishlist'
                ];
            }
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Invalid product ID'
        ];
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$_title = 'BeenChilling';
include '../../_head.php';


// Get all product types from database
$stm = $_db->query('SELECT * FROM product_type ORDER BY type_name');
$product_types = $stm->fetchAll(PDO::FETCH_OBJ);

// Get search term and type from query parameters
$name = req('name');
$typeid = req('typeid');

// Add sorting parameters
$fields = [
    'product_name'      => 'Product Name',
    'price'             => 'Price',
    'stock'             => 'Stock'
];
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'product_name';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);

// Get price range parameters
$min_price = req('min_price');
$max_price = req('max_price');

try {
    // Build the query based on search term and type
    $sql = "SELECT p.*, t.type_name FROM product p JOIN product_type t ON p.type_id = t.type_id WHERE p.product_status = 'Active'";

    $params = [];

    if ($name) {
        $sql .= " AND p.product_name LIKE ?";
        $params[] = "%$name%";
    }

    if ($typeid && $typeid !== 'ALL') {
        $sql .= " AND p.type_id = ?";
        $params[] = $typeid;
    }

    // Add price range conditions
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
    $p = new SimplePager($sql, $params, 12, $page);
    $all_products = $p->result;
} catch (PDOException $e) {
    $_err['db'] = 'Database error: ' . $e->getMessage();
    $all_products = [];
    $p = new stdClass();
    $p->count = 0;
    $p->item_count = 0;
    $p->page = 1;
    $p->page_count = 1;
}

// Group products by type
$product_arr = [];
foreach ($all_products as $product) {
    $product_arr[$product->type_name][] = $product;
}

$cart = get_or_create_cart();

topics_text("Get a BeenChilling like John Cena.");
?>

<div class="container">
    <form method="get">
        <div class="search-div">
            <?= html_search('name') ?>
            <div class="search-bar">
                <input class="price-bar" type="number" name="min_price" placeholder="Min Price" step="0.01" min="0" value="<?= htmlspecialchars($min_price) ?>">
                <span>to</span>
                <input class="price-bar" type="number" name="max_price" placeholder="Max Price" step="0.01" min="0" value="<?= htmlspecialchars($max_price) ?>">
                <button class="search-bar">Search</button>
            </div>
        </div>
    </form>

    <div class="filter-buttons">
        <button class="button" <?= (!$typeid || $typeid === 'ALL') ? 'active' : '' ?>"
            onclick="window.location.href='?typeid=ALL<?= $name ? '&name=' . urlencode($name) : '' ?><?= $sort ? '&sort=' . urlencode($sort) : '' ?><?= $dir ? '&dir=' . urlencode($dir) : '' ?><?= $min_price !== '' ? '&min_price=' . urlencode($min_price) : '' ?><?= $max_price !== '' ? '&max_price=' . urlencode($max_price) : '' ?>'">
            All Products
        </button>
        <?php foreach ($product_types as $type): ?>
            <button class="button <?= $typeid == $type->type_id ? 'active' : '' ?>"
                onclick="window.location.href='?typeid=<?= $type->type_id ?><?= $name ? '&name=' . urlencode($name) : '' ?><?= $sort ? '&sort=' . urlencode($sort) : '' ?><?= $dir ? '&dir=' . urlencode($dir) : '' ?><?= $min_price !== '' ? '&min_price=' . urlencode($min_price) : '' ?><?= $max_price !== '' ? '&max_price=' . urlencode($max_price) : '' ?>'">
                <?= htmlspecialchars($type->type_name) ?>
            </button>
        <?php endforeach; ?>
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

        <p class="page-nav">
            <?= $p->count ?> of <?= $p->item_count ?> product(s) | Page <?= $p->page ?> of <?= $p->page_count ?>
        </p>

        <?php if (empty($all_products)): ?>
            <div class="alert alert-info">
                No products found matching your search criteria.
            </div>
        <?php else: ?>
            <?php if ($typeid && $typeid !== 'ALL'): ?>
                <?php
                $selected_type_name = '';
                foreach ($product_types as $type) {
                    if ($type->type_id == $typeid) {
                        $selected_type_name = $type->type_name;
                        break;
                    }
                }
                ?>
                <?php if (!empty($product_arr[$selected_type_name])): ?>
                    <?php product_container($selected_type_name, $product_arr[$selected_type_name]); ?>
                <?php endif; ?>
            <?php else: ?>
                <?php foreach ($product_types as $type): ?>
                    <?php if (!empty($product_arr[$type->type_name])): ?>
                        <?php product_container($type->type_name, $product_arr[$type->type_name]); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>

        <br>
        <?= $p->html("name=$name&typeid=$typeid&sort=$sort&dir=$dir&min_price=$min_price&max_price=$max_price") ?>
    </div>
</div>

<?php
include '../../_foot.php';