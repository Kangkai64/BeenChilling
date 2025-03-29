<?php
require '../../_base.php';

$_title = 'BeenChilling';
include '../../_head.php';

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