<?php
require '../../_base.php';

auth('Admin');

$id = req('id');

try {
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