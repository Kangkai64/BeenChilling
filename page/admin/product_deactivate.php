<?php
require '../../_base.php';

auth('Admin');

$id = req('id');

try {
    // Check if the product exists
    $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
    $stm->execute([$id]);
    $product = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        temp('info', 'Product not found');
        redirect('product_list.php');
    }

    // Check if the product is already inactive
    if ($product['product_status'] == 'Inactive') {
        temp('info', 'Product is already inactive');
        redirect('product_list.php');
    }

    $stm = $_db->prepare('
         UPDATE product
         SET product_status = "Inactive"
         WHERE product_id = ?
     ');
    $stm->execute([$id]);
    temp('info', 'Product deactivated');
} catch (PDOException $e) {
    $_err['db'] = 'Database error: ' . $e->getMessage();
}

redirect('product_list.php');