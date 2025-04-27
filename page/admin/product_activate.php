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

    // Check if the product is already active
    if ($product['product_status'] == 'Active') {
        temp('info', 'Product is already active');
        redirect('product_list.php');
    }

    // Update the product status to active  
    $stm = $_db->prepare('
         UPDATE product
         SET product_status = "Active"
         WHERE product_id = ?
     ');
    $stm->execute([$id]);
    temp('info', 'Product activated');
} catch (PDOException $e) {
    $_err['db'] = 'Database error: ' . $e->getMessage();
}

redirect('product_list.php');