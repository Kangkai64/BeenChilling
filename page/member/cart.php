<?php
require '../../_base.php';

// Handle AJAX requests - Move this to the top before ANY output
if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $product_id = isset($_POST['id']) ? $_POST['id'] : null;
    $quantity = isset($_POST['unit']) ? (int)$_POST['unit'] : 0;
    
    if ($product_id) {
        if (is_logged_in()) {
            // User is logged in, use database cart
            $cart_id = get_or_create_cart();
            update_cart_item($cart_id, $product_id, $quantity);
            
            // Get updated cart info
            $cart_items = get_cart_items($cart_id);
            $cart_summary = get_cart_summary($cart_id);
            
            // Calculate subtotal for updated product
            $subtotal = '0.00';
            foreach ($cart_items as $item) {
                if ($item->product_id == $product_id) {
                    $subtotal = number_format($item->price * $item->quantity, 2);
                    break;
                }
            }
            
            $total = number_format($cart_summary->total_price ?? 0, 2);
            $cart_count = $cart_summary->total_items ?? 0;
        } else {
            // User is not logged in, use session cart
            update_session_cart_item($product_id, $quantity);
            
            // Calculate subtotal for updated product
            $subtotal = '0.00';
            foreach ($_SESSION['temp_cart']['items'] as $item) {
                if ($item['product_id'] == $product_id) {
                    $subtotal = number_format($item['price'] * $item['quantity'], 2);
                    break;
                }
            }
            
            $total = number_format($_SESSION['temp_cart']['total_price'], 2);
            $cart_count = $_SESSION['temp_cart']['total_items'];
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'ProductID' => $product_id,
            'subtotal' => $subtotal,
            'total' => $total,
            'cart_count' => $cart_count,
            'message' => 'Cart updated successfully!'
        ]);
        exit; // Important to prevent further execution
    }
}

// Only proceed with page rendering if not an AJAX request
$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

// Get cart based on login status
if (is_logged_in()) {
    $cart_id = get_or_create_cart();
    $cart_items = get_cart_items($cart_id);
    $cart_summary = get_cart_summary($cart_id);
} else {
    $cart_id = null;
    $cart_items = get_cart_items();
    $cart_summary = get_cart_summary();
}

// Handle form submissions
if (is_post()) {
    $btn = req('btn');
    if ($btn == 'clear') {
        clear_cart($cart_id);
        redirect('cart.php');
        exit;
    }
}

topics_text("My Cart", "200px");
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

    <?php if ($cart_items && count($cart_items) > 0): ?>
        <?php foreach ($cart_items as $item): ?>
            <tr>
                <td><?= $item->product_id ?></td>
                <td><?= $item->product_name ?? $item->ProductName ?></td>
                <td class="right"><?= number_format($item->price, 2) ?></td>
                <td>
                    <button class="product-button" data-get="product_details.php?id=<?= $item->product_id ?>">
                        Details
                    </button>
                </td>
                <td>
                    <form method="post" class="unit-form">
                        <input type="hidden" name="ProductID" value="<?= $item->product_id ?>">
                            <?= html_select('unit', $_units, $item->quantity) ?>
                        <input type="hidden" name="ajax" value="true">
                    </form>
                </td>
                <td class="right subtotal" data-product-id="<?= $item->product_id ?>">
                    <?= number_format($item->price * $item->quantity, 2) ?>
                    <div class="popup">
                        <img src="../../images/product/<?= $item->product_image ?? $item->ProductImage ?>">
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr class="right">
            <th colspan="4"></th>
            <th id="cart-total-items"><?= $cart_summary->total_items ?? 0 ?></th>
            <th id="cart-total-price"><?= number_format($cart_summary->total_price ?? 0, 2) ?></th>
        </tr>
    <?php else: ?>
        <tr>
            <td colspan="6" class="center">Your cart is empty</td>
        </tr>
    <?php endif; ?>
</table>

<section class="button-group">
    <?php if ($cart_items && count($cart_items) > 0): ?>
        <button class="button" data-post="?btn=clear">Clear</button>

        <?php if (is_logged_in() && $_user->role == 'Member'): ?>
            <button class="button" data-post="/page/member/checkout.php">Checkout</button>
        <?php else: ?>
            <button class="button" data-get="/page/login.php">Login to Checkout</button><br>
            <span class="cart-note">Your cart will be saved when you log in.</span>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php
include '../../_foot.php';