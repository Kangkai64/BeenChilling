<?php
require '../../_base.php';

// Handle AJAX requests - Must be at the top before any output
if(isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $product_id = isset($_POST['id']) ? $_POST['id'] : null;
    $quantity = isset($_POST['unit']) ? (int)$_POST['unit'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : 'cart'; // 'cart' or 'wishlist'
    $response = ['success' => false];
    
    if ($product_id) {
        if ($action === 'cart') {
            // Handle cart updates
            if (is_logged_in()) {
                $cart_id = get_or_create_cart();
                update_cart_item($cart_id, $product_id, $quantity);
                $cart_summary = get_cart_summary($cart_id);
                $cart_count = $cart_summary->total_items ?? 0;
                $total = number_format($cart_summary->total_price ?? 0, 2);
            } else {
                update_session_cart_item($product_id, $quantity);
                $cart_count = $_SESSION['temp_cart']['total_items'] ?? 0;
                $total = number_format($_SESSION['temp_cart']['total_price'] ?? 0, 2);
            }
            
            $response = [
                'success' => true,
                'product_id' => $product_id,
                'total' => $total,
                'cart_count' => $cart_count,
                'message' => 'Cart updated successfully!'
            ];
        } 
    } elseif ($action === 'wishlist') {
        // Handle wishlist updates
        if (is_logged_in()) {
            update_wishlist($product_id, 1); // Update with quantity 1
            
            // Get updated wishlist count
            $wishlist = get_or_create_wishlist();
            $wishlist_count = count($wishlist);
        }
        
        $response = [
            'success' => true,
            'product_id' => $product_id,
            'wishlist_count' => $wishlist_count,
            'message' => 'Wishlist updated successfully!'
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$_title = 'BeenChilling';
include '../../_head.php';

// Handle regular POST requests
if (is_post() && !isset($_POST['ajax'])) {
    $product_id = req('id');
    $quantity = req('unit');

    if (is_logged_in()) {
        $cart_id = get_or_create_cart();
        update_cart_item($cart_id, $product_id, $quantity);
    } else {
        update_session_cart_item($product_id, $quantity);
    }
    
    redirect();
}

$type_ids = [
    'Sundae' => 1,
    'Dessert' => 2,
    'Ice-Cream' => 3
];

$product_arr = [];

$stm = $_db->prepare('SELECT * FROM product WHERE TypeID = ?');

// Loop through each type_id and fetch results
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