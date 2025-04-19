<?php
require '../../_base.php';

if(isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $ProductID = isset($_POST['id']) ? $_POST['id'] : null;
    $unit = isset($_POST['unit']) ? $_POST['unit'] : 0;

    if ($ProductID) {
        update_cart($ProductID, $unit);
    }
    
    $cart = get_cart();
    $total_items = array_sum($cart);
    
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

$type_ids = [
    'Sundae' => 1,
    'Dessert' => 2,
    'Ice-Cream' => 3
];

$product_arr = [];

$stm = $_db->prepare('SELECT * FROM product WHERE TypeID = ?');

// Loop through each TypeID and fetch results
foreach ($type_ids as $product_type => $type_id) {
    $stm->execute([$type_id]);
    $product_arr[$product_type] = $stm->fetchAll(PDO::FETCH_OBJ);
}

topics_text("Get a BeenChilling like John Cena."); 
?>

    <div class="container">
        <?php 
            product_container("Sundaes", $product_arr['Sundae']); 
            product_container("Dessert", $product_arr['Dessert']); 
            product_container("Ice-Cream",$product_arr['Ice-Cream']);
        ?>
    </div>

<?php
include '../../_foot.php';