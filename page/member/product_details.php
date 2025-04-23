<?php
require '../../_base.php';

// Determine the context (cart or wishlist)
$context = isset($_GET['context']) ? $_GET['context'] : 'cart';

// Handle AJAX request
if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $product_id = isset($_POST['id']) ? $_POST['id'] : null;
    $quantity = isset($_POST['unit']) ? (int)$_POST['unit'] : 0;
    $action_context = isset($_POST['context']) ? $_POST['context'] : $context;

    if ($product_id) {
        $success = false;
        $message = '';
        
        if ($action_context === 'cart') {
            // Handle cart update
            $cart = get_or_create_cart();
            $success = update_cart_item($cart->cart_id, $product_id, $quantity);
            $cart_summary = get_cart_summary($cart->cart_id, $cart->type);
            $message = 'Cart updated successfully!';
            
            // Get product price for subtotal calculation
            $stm = $_db->prepare('SELECT Price FROM product WHERE ProductID = ?');
            $stm->execute([$product_id]);
            $product = $stm->fetch(PDO::FETCH_OBJ);
            $subtotal = $product ? number_format($product->Price * $quantity, 2) : '0.00';
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'product_id' => $product_id,
                'subtotal' => $subtotal,
                'total' => number_format($cart_summary['total_price'], 2),
                'cart_count' => $cart_summary['total_items'],
                'message' => $message
            ]);
        } else {
            // Handle wishlist update
            $success = update_wishlist_item($product_id, $quantity);
            $message = 'Wishlist updated successfully!';
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'product_id' => $product_id,
                'message' => $message
            ]);
        }
        exit;
    }
}

$_title = 'BeenChilling';
include '../../_head.php';

// Handle non-AJAX form submission
if (is_post()) {
    $product_id = req('id');
    $quantity = (int)req('unit');
    $post_context = req('context', $context);
    
    if ($post_context === 'cart') {
        // Get or create the cart
        $cart = get_or_create_cart();
        
        // Update cart based on type
        update_cart_item($cart->cart_id, $product_id, $quantity);
        
        redirect('cart.php');
    } else {
        // Update wishlist
        update_wishlist_item($product_id, $quantity);
        
        redirect('wishlist.php');
    }
}

// Get product details
$product_id = req('id');
$stm = $_db->prepare('SELECT * FROM product WHERE ProductID = ?');
$stm->execute([$product_id]);
$product = $stm->fetch(PDO::FETCH_OBJ);
if (!$product) redirect($context === 'cart' ? 'cart.php' : 'wishlist.php');

// Get current quantity (for cart or wishlist)
$current_quantity = 0;

if ($context === 'cart') {
    $cart = get_or_create_cart();
    
    if ($cart->type === 'session') {
        // Check session cart
        if (isset($_SESSION['cart']['items'])) {
            foreach ($_SESSION['cart']['items'] as $item) {
                if ($item['product_id'] == $product_id) {
                    $current_quantity = $item['quantity'];
                    break;
                }
            }
        }
    } else {
        // Check database cart
        $stm = $_db->prepare('SELECT quantity FROM cart_item WHERE cart_id = ? AND product_id = ?');
        $stm->execute([$cart->cart_id, $product_id]);
        $item = $stm->fetch(PDO::FETCH_OBJ);
        if ($item) {
            $current_quantity = $item->quantity;
        }
    }
} else {
    // Check wishlist quantity
    $wishlist = get_or_create_wishlist();
    if ($wishlist && isset($wishlist->wishlist_id)) {
        $stm = $_db->prepare('SELECT quantity FROM wishlist_item WHERE wishlist_id = ? AND product_id = ?');
        $stm->execute([$wishlist->wishlist_id, $product_id]);
        $item = $stm->fetch(PDO::FETCH_OBJ);
        if ($item) {
            $current_quantity = $item->quantity;
        }
    }
}

// Create back button URL
$back_url = $context === 'cart' ? 'cart.php' : 'wishlist.php';
?>

<style>
    .unit-form > select {
        width: 30%;
    }
</style>

<div class="product-details-container">
    <div>
        <img class="product-image" src="../../images/product/<?= $product->ProductImage ?>" alt="Product photo">
    </div>
    <div class="product-details">
        <h1><?= $product->ProductName ?></h1>
        <h2>
            RM<?= $product->Price ?>
        </h2>
        <h3>
            <?= $product->Description ?>
        </h3>
        <form method="post" class="unit-form" id="update-form">
            <input type="hidden" name="id" value="<?= $product->ProductID ?>">
            <input type="hidden" name="context" value="<?= $context ?>">
            <label for="unit">Unit: </label>
            <?= html_select('unit', $_units, $current_quantity) ?>
            <input type="hidden" name="ajax" value="true">
        </form>
    </div>
</div>

<button class="button" data-get="<?= $back_url ?>">Back</button>

<?php
include '../../_foot.php';