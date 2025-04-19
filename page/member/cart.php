<?php
require '../../_base.php';

// Function to get or create a cart for the current logged-in member
function get_or_create_cart() {
    global $_db, $_user;
    
    // Only proceed if user is logged in as member
    if (!$_user || $_user->role != 'Member') {
        return null;
    }
    
    // Check if member has an active cart
    $stm = $_db->prepare('SELECT cart_id FROM cart WHERE member_id = ? AND status = "active" LIMIT 1');
    $stm->execute([$_user->id]);
    $cart = $stm->fetch(PDO::FETCH_OBJ);
    
    if ($cart) {
        return $cart->cart_id;
    }
    
    // Create a new cart for the member
    $stm = $_db->prepare('INSERT INTO cart (member_id) VALUES (?)');
    $stm->execute([$_user->id]);
    
    // Get the generated cart_id
    $stm = $_db->prepare('SELECT cart_id FROM cart WHERE member_id = ? AND status = "active" ORDER BY created_at DESC LIMIT 1');
    $stm->execute([$_user->id]);
    $cart = $stm->fetch(PDO::FETCH_OBJ);
    
    return $cart->cart_id;
}

// Function to update cart item quantity
function update_cart_item($cart_id, $product_id, $quantity) {
    global $_db;
    
    // Get product price
    $stm = $_db->prepare('SELECT Price FROM product WHERE ProductID = ?');
    $stm->execute([$product_id]);
    $product = $stm->fetch(PDO::FETCH_OBJ);
    
    if (!$product) {
        return false;
    }
    
    // Check if item already exists in cart
    $stm = $_db->prepare('SELECT cart_item_id FROM cart_item WHERE cart_id = ? AND product_id = ?');
    $stm->execute([$cart_id, $product_id]);
    $item = $stm->fetch(PDO::FETCH_OBJ);
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0
        if ($item) {
            $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_item_id = ?');
            $stm->execute([$item->cart_item_id]);
        }
    } else if ($item) {
        // Update existing item
        $stm = $_db->prepare('UPDATE cart_item SET quantity = ?, price = ? WHERE cart_item_id = ?');
        $stm->execute([$quantity, $product->Price, $item->cart_item_id]);
    } else {
        // Add new item
        $stm = $_db->prepare('INSERT INTO cart_item (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        $stm->execute([$cart_id, $product_id, $quantity, $product->Price]);
    }
    
    return true;
}

// Function to clear cart
function clear_cart($cart_id) {
    global $_db;
    
    $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id = ?');
    $stm->execute([$cart_id]);
    
    return true;
}

// Function to get cart items and summary
function get_cart_items($cart_id) {
    global $_db;
    
    $stm = $_db->prepare('
        SELECT ci.*, p.ProductName, p.ProductImage 
        FROM cart_item ci
        JOIN product p ON ci.product_id = p.ProductID
        WHERE ci.cart_id = ?
    ');
    $stm->execute([$cart_id]);
    
    return $stm->fetchAll(PDO::FETCH_OBJ);
}

// Function to get cart summary (total items and price)
function get_cart_summary($cart_id) {
    global $_db;
    
    $stm = $_db->prepare('
        SELECT SUM(quantity) as total_items, SUM(quantity * price) as total_price
        FROM cart_item
        WHERE cart_id = ?
    ');
    $stm->execute([$cart_id]);
    
    return $stm->fetch(PDO::FETCH_OBJ);
}

// Handle AJAX requests
if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    // Get or create cart
    $cart_id = get_or_create_cart();
    
    if (!$cart_id) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'You must be logged in to update your cart.'
            ]);
            exit;
        }
    }
    
    $product_id = isset($_POST['id']) ? $_POST['id'] : null;
    $quantity = isset($_POST['unit']) ? (int)$_POST['unit'] : 0;
    
    if ($product_id) {
        update_cart_item($cart_id, $product_id, $quantity);
    }
    
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
    
    // Return JSON response
    if (!headers_sent()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'ProductID' => $product_id,
            'subtotal' => $subtotal,
            'total' => number_format($cart_summary->total_price ?? 0, 2),
            'cart_count' => $cart_summary->total_items ?? 0,
            'message' => 'Cart updated successfully!'
        ]);
        exit;
    }
}

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

// Get or create cart
$cart_id = get_or_create_cart();

// Handle form submissions
if (is_post()) {
    $btn = req('btn');
    if ($btn == 'clear' && $cart_id) {
        clear_cart($cart_id);
    }
}

// Get cart items if cart exists
$cart_items = $cart_id ? get_cart_items($cart_id) : [];
$cart_summary = $cart_id ? get_cart_summary($cart_id) : null;

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

    <?php if ($cart_items): ?>
        <?php foreach ($cart_items as $item): ?>
            <tr>
                <td><?= $item->product_id ?></td>
                <td><?= $item->ProductName ?></td>
                <td class="right"><?= number_format($item->price, 2) ?></td>
                <td>
                    <button class="product-button" data-get="product_details.php?id=<?= $item->product_id ?>">
                        Details
                    </button>
                </td>
                <td>
                    <form method="post" class="unit-form">
                        <input type="hidden" name="ProductID" value="<?= $item->product_id ?>">
                        <?= html_select('unit', $_units) ?>
                        <input type="hidden" name="ajax" value="true">
                    </form>
                </td>
                <td class="right subtotal" data-product-id="<?= $item->product_id ?>">
                    <?= number_format($item->price * $item->quantity, 2) ?>
                    <div class="popup">
                        <img src="../../images/product/<?= $item->ProductImage ?>">
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
    <?php if ($cart_items): ?>
        <button class="button" data-post="?btn=clear">Clear</button>

        <?php if ($_user && $_user->role == 'Member'): ?>
            <button class="button" data-post="/page/member/checkout.php">Checkout</button>
        <?php else: ?>
            <button class="button" data-get="/page/login.php">Login</button>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php
include '../../_foot.php';