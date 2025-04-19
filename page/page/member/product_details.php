<?php
require '../../_base.php';

if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $ProductID = isset($_POST['id']) ? $_POST['id'] : null;
    $unit = isset($_POST['unit']) ? $_POST['unit'] : 0;

    if ($ProductID) {
        update_cart($ProductID, $unit);
    }

    $cart = get_cart();
    $total_items = array_sum($cart);

    // Initialize variables
    $subtotal = 0;
    $total = 0;

    // Only try to get product price if ProductID is valid
    if ($ProductID) {
        $stm = $_db->prepare('SELECT Price FROM product WHERE ProductID = ?');
        $stm->execute([$ProductID]);
        $product = $stm->fetch(PDO::FETCH_OBJ);
        
        // Check if product was found before accessing properties
        if ($product) {
            $subtotal = number_format($product->Price * $unit, 2);
        }
    }

    // Calculate cart total safely
    foreach ($cart as $id => $qty) {
        $stm = $_db->prepare('SELECT Price FROM product WHERE ProductID = ?');
        $stm->execute([$id]);
        $p = $stm->fetch(PDO::FETCH_OBJ);
        
        // Check if product was found
        if ($p) {
            $total += $p->Price * $qty;
        }
    }

    // Return JSON response
    if (!headers_sent()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'ProductID' => $ProductID,
            'subtotal' => ($subtotal ?: '0.00'),
            'total' => number_format($total, 2),
            'cart_count' => $total_items,
            'message' => 'Cart updated successfully!'
        ]);
        exit;
    }
}

$_title = 'BeenChilling';
include '../../_head.php';

if (is_post()) {
    $ProductID = req('id');
    $unit = req('unit');
    update_cart($ProductID, $unit);
    redirect();
}

$ProductID = req('id');
$stm = $_db->prepare('SELECT * FROM product WHERE ProductID = ?');
$stm->execute([$ProductID]);
$s = $stm->fetch();
if (!$s) redirect('cart.php');
?>

<div class="product-details-container">
    <div>
        <img class="product-image" src="../../images/product/<?= $s->ProductImage ?> " alt="Product photo">
    </div>
    <div class="product-details">
        <h1><?= $s-> ProductName ?></h1>
        <h2>
            RM<?= $s-> Price?>
        </h2>
        <h3>
            <?= $s-> Description?>
        </h3>
            <?php
                $cart = get_cart();
                $id = $s->ProductID;
                $unit = $cart[$s->ProductID] ?? 0;
            ?>
            <form method="post" class="unit-form">
                <?= html_hidden('ProductID', $s->ProductID) ?>
                <label for="unit">Unit : </label>
                <?= html_select('unit', $_units) ?>
                <input type="hidden" name="ajax" value="true">
            </form>
    </div>
</div>

<button class="button" data-get="cart.php">Back</button>

<?php
include '../../_foot.php';