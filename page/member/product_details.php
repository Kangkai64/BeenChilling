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
            $stm = $_db->prepare('SELECT price FROM product WHERE product_id = ?');
            $stm->execute([$product_id]);
            $product = $stm->fetch(PDO::FETCH_OBJ);
            $subtotal = $product ? number_format($product->price * $quantity, 2) : '0.00';

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
$id = req('id');

$stm = $_db->prepare('
     SELECT p.*, t.type_name
     FROM product p
     LEFT JOIN product_type t ON p.type_id = t.type_id
     WHERE p.product_id = ? AND p.product_status = "Active"
 ');
$stm->execute([$id]);
$product = $stm->fetch();

if (!$product) {
    redirect('product.php');
}

// Get additional product images
$stm = $_db->prepare('
     SELECT image_path
     FROM product_images
     WHERE product_id = ?
     ORDER BY created_at ASC
 ');
$stm->execute([$id]);
$additionalImages = $stm->fetchAll();

// Get current quantity (for cart or wishlist)
$current_quantity = 0;

extract((array)$product);

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
    .unit-form>select {
        width: 30%;
    }
</style>

<div class="product-details">
    <div class="product-details-container">
        <div class="product-images-section">
            <div class="image-slider">
                <div class="slider-container">
                    <!-- Primary Image -->
                    <div class="slide active">
                        <img class="product-image" src="../../images/product/<?= $product->product_image ?>" alt="Product photo">
                    </div>
                    <!-- Additional Images -->
                    <?php foreach ($additionalImages as $image): ?>
                        <div class="slide">
                            <img src="../../images/product/<?= $image->image_path ?>" alt="Additional product photo">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="slider-controls">
                    <button class="prev-btn">&lt;</button>
                    <div class="slider-dots">
                        <span class="dot active"></span>
                        <?php for ($i = 1; $i < count($additionalImages) + 1; $i++): ?>
                            <span class="dot"></span>
                        <?php endfor; ?>
                    </div>
                    <button class="next-btn">&gt;</button>
                </div>
            </div>
        </div>
        <div class="product-details">
            <table class="product-info-table">
                <tr>
                    <th>Product Name</th>
                    <td><?= $product->product_name ?></td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>RM<?= number_format($product->price, 2) ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= $product->description ?></td>
                </tr>
                <tr>
                    <th>Stock</th>
                    <td><?= $product->stock ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= $product->product_status ?></td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td>
                        <?php if ($stock > 0): ?>
                            <div class="quantity-control">
                                <form method="post" class="unit-form" id="update-form">
                                    <input type="hidden" name="id" value="<?= $product->product_id ?>">
                                    <input type="hidden" name="context" value="<?= $context ?>">
                                    <?= html_select('unit', $_units, $current_quantity) ?>
                                    <input type="hidden" name="ajax" value="true">
                                </form>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<section class="button-group">
    <button class="button" data-get="<?= $back_url ?>">Back</button>
    <button class="button" data-get="/page/member/product.php">Continue Shopping</button>
</section>

<?php
include '../../_foot.php';