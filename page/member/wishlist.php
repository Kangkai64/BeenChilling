<?php
require '../../_base.php';

if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $ProductID = isset($_POST['id']) ? $_POST['id'] : null;
    $unit = isset($_POST['unit']) ? $_POST['unit'] : 0;

    if ($ProductID) {
        update_wishlist($ProductID, $unit);
    }

    $wishlist = get_wishlist();
    $total_items = array_sum($wishlist);

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

    // Calculate wishlist total safely
    foreach ($wishlist as $id => $qty) {
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
            'wishlist_count' => $total_items,
            'message' => 'Wishlist updated successfully!'
        ]);
        exit;
    }
}

$_title = 'BeenChilling';
include '../../_head.php';
require_once '../../lib/SimplePager.php';

if (is_post()) {
    $btn = req('btn');
    if ($btn == 'clear') {
        set_wishlist();
    }

    $ProductID = req('id');
    $unit = req('unit');
}

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
    $stm = $_db->prepare('SELECT * FROM product WHERE ProductID = ?');
    $wishlist = get_wishlist();

    foreach ($wishlist as $ProductID => $unit):
        $stm->execute([$ProductID]);
        $p = $stm->fetch();

        if (!$p) continue;

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
                    <?= html_hidden('ProductID', $p->ProductID) ?>
                    <?= html_select('unit', $_units) ?>
                    <input type="hidden" name="ajax" value="true">
                </form>
            </td>
            <td class="right subtotal" id="cart-subtotal" data-product-id="<?= $p->ProductID ?>">
                <?= sprintf('%.2f', $subtotal) ?>
                <div class="popup">
                    <img src="../../images/product/<?= $p->ProductImage ?>">
                </div>
            </td>
        </tr>
    <?php endforeach ?>

    <tr class="right">
        <th colspan="4"></th>
        <th id="cart-total-items"><?= $count ?></th>
        <th id="cart-total-price"><?= sprintf('%.2f', $total) ?></th>
    </tr>
</table>

<section class="button-group">
    <?php if ($wishlist): ?>
        <button class="button" data-post="?btn=clear">Clear</button>

        <?php if ($_user?->role == 'Member'): ?>
            <button class="button" data-post="/page/member/checkout.php">Checkout</button>
        <?php else: ?>
            <button class="button" data-get="/page/login.php">Login</button>
        <?php endif ?>
    <?php endif ?>
</section>

<?php
include '../../_foot.php';