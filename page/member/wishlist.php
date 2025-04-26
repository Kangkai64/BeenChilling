<?php
require '../../_base.php';

auth('Member');

// Handle AJAX requests
if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    // Get or create wishlist
    $wishlist = get_or_create_wishlist();
    
    $product_id = isset($_POST['id']) ? $_POST['id'] : null;
    $quantity = isset($_POST['unit']) ? (int)$_POST['unit'] : 0;
    
    if ($product_id) {
        update_wishlist_item($product_id, $quantity);
    }
    
    // Get updated wishlist info
    $wishlist_items = get_wishlist_items($wishlist->wishlist_id);
    $wishlist_summary = get_wishlist_summary($wishlist->wishlist_id);
    
    // Calculate subtotal for updated product
    $subtotal = '0.00';
    foreach ($wishlist_items as $item) {
        if ($item->product_id == $product_id) {
            $subtotal = number_format($item->price * $item->quantity, 2);
            break;
        }
    }
    
    // Return JSON response
    if (!headers_sent()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'product_id' => $product_id,
            'subtotal' => $subtotal,
            'total' => number_format($wishlist_summary->total_price ?? 0, 2),
            'wishlist_count' => $wishlist_summary->total_items ?? 0,
            'message' => 'Wishlist updated successfully!'
        ]);
        exit;
    }
}

// Get or create wishlist
$wishlist = get_or_create_wishlist();

// Handle form submissions
if (is_post()) {
    $btn = req('btn');
    if ($btn == 'clear') {
        clear_wishlist($wishlist->wishlist_id);
        redirect('wishlist.php');
    } elseif ($btn == 'addtocart') {
        add_wishlist_to_cart($wishlist->wishlist_id);
        redirect('cart.php');
    }
}

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

// Get wishlist items if wishlist exists
$wishlist_items = $wishlist ? get_wishlist_items($wishlist->wishlist_id) : [];
$wishlist_summary = $wishlist ? get_wishlist_summary($wishlist->wishlist_id) : null;

topics_text("My Wishlist", "250px", "wishlist-button");
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

    <?php if ($wishlist_items): ?>
        <?php foreach ($wishlist_items as $item): ?>
            <tr>
                <td><?= $item->product_id ?></td>
                <td><?= $item->product_name ?></td>
                <td class="right"><?= number_format($item->price, 2) ?></td>
                <td>
                    <button class="product-button" data-get="product_details.php?id=<?= $item->product_id ?>&context=wishlist">
                        Details
                    </button>
                </td>
                <td>
                    <form method="post" class="unit-form">
                        <input type="hidden" name="product_id" value="<?= $item->product_id ?>">
                        <?= html_select('unit', $_units, $item->quantity) ?>
                        <input type="hidden" name="ajax" value="true">
                    </form>
                </td>
                <td class="right subtotal" data-product-id="<?= $item->product_id ?>">
                    <?= number_format($item->price * $item->quantity, 2) ?>
                    <div class="popup">
                        <img src="../../images/product/<?= $item->product_image ?>">
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr class="right">
            <th colspan="4">Total:</th>
            <th id="wishlist-total-item"><?= $wishlist_summary->total_items ?? 0 ?></th>
            <th id="wishlist-total-price"><?= number_format($wishlist_summary->total_price ?? 0, 2) ?></th>
        </tr>
    <?php else: ?>
        <tr>
            <td colspan="6" class="center">Your wishlist is empty</td>
        </tr>
    <?php endif; ?>
</table>

<section class="button-group">
    <button class="button" data-get="/page/member/products.php">Back</button>
    <?php if ($wishlist_items && count($wishlist_items) > 0): ?>
        <button class="button" data-post="?btn=clear" data-confirm>Clear</button>
        <button class="button" data-post="?btn=addtocart">Add to Cart</button>
    <?php endif; ?>
</section>

<?php
include '../../_foot.php';