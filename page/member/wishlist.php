<?php
require '../../_base.php';

auth('Member');

// Handle AJAX requests
if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $product_id = isset($_POST['id']) ? $_POST['id'] : null;
    $unit = isset($_POST['unit']) ? (int)$_POST['unit'] : 1;
    
    if ($product_id) {
        // Try to update wishlist
        try {
            update_wishlist($product_id, $unit);
            
            // Get updated wishlist count
            $wishlist = get_or_create_wishlist();
            $total_items = get_wishlist_count($wishlist->wishlist_id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'wishlist_count' => $total_items,
                'message' => 'Product added to wishlist!'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    } else {
        // Return error for missing product ID
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Product ID is required'
        ]);
        exit;
    }
}

// Handle Add to Cart action
if (isset($_POST['btn']) && $_POST['btn'] == 'addtocart') {
    $wishlist = get_or_create_wishlist();
    
    // Add all wishlist items to cart
    foreach (get_object_vars($wishlist) as $product_id => $unit) {
        add_wishlist_to_cart($wishlist->wishlist_id);
    }
    
    // Optional: Clear wishlist after adding to cart
    // set_wishlist();
    
    redirect('cart.php');
}

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

topics_text("My Wishlist", "200px");
?>

<table class="product-list-table">
    <tr>
        <th>Product ID</th>
        <th>Name</th>
        <th>Price (RM)</th>
        <th>Details</th>
        <th>Unit</th>
        <th>Subtotal (RM)</th>
    </tr>

    <?php
    $count = 0;
    $total = 0;
    $wishlist = get_or_create_wishlist();
    $has_items = false;

    foreach (get_object_vars($wishlist) as $product_id => $unit):
        $stm = $_db->prepare('SELECT * FROM product WHERE ProductID = ?');
        $stm->execute([$product_id]);
        $p = $stm->fetch(PDO::FETCH_OBJ);

        if (!$p) continue;
        $has_items = true;

        $subtotal = $p->Price * $unit;
        $count += $unit;
        $total += $subtotal;
    ?>
    <tr>
        <td><?= $p->ProductID ?></td>
        <td><?= $p->ProductName ?></td>
        <td class="right"><?= $p->Price ?></td>
        <td>
            <button class="product-button" data-get="product_details.php?id=<?= $p->ProductID ?>">
                Details
            </button>
        </td>
        <td>
            <form method="post" class="unit-form">
                <input type="hidden" name="ProductID" value="<?= $p->ProductID ?>">
                <?= html_select('unit', $_units, $unit) ?>
                <input type="hidden" name="ajax" value="true">
            </form>
        </td>
        <td class="right subtotal" data-product-id="<?= $p->ProductID ?>">
            <?= sprintf('%.2f', $subtotal) ?>
            <div class="popup">
                <img src="../../images/product/<?= $p->ProductImage ?>">
            </div>
        </td>
    </tr>
    <?php endforeach; ?>

    <?php if ($has_items): ?>
    <tr class="right">
        <th colspan="4"></th>
        <th id="wishlist-total-item"><?= $count ?></th>
        <th id="wishlist-total-price"><?= sprintf('%.2f', $total) ?></th>
    </tr>
    <?php else: ?>
    <tr>
        <td colspan="6" class="center">Your wishlist is empty</td>
    </tr>
    <?php endif; ?>
</table>

<section class="button-group">
    <?php if ($has_items): ?>
        <button class="button" data-post="?btn=clear">Clear</button>
        <button class="button" data-post="?btn=addtocart">Add to Cart</button>
    <?php endif ?>
</section>

<?php 
include '../../_foot.php';