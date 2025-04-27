<?php
require '../../_base.php';

auth('Admin');

$id = req('id');

// Get filter parameters
$filter_params = [];
if (isset($_GET['product_name'])) $filter_params['product_name'] = $_GET['product_name'];
if (isset($_GET['type_id'])) $filter_params['type_id'] = $_GET['type_id'];
if (isset($_GET['min_price'])) $filter_params['min_price'] = $_GET['min_price'];
if (isset($_GET['max_price'])) $filter_params['max_price'] = $_GET['max_price'];
if (isset($_GET['sort'])) $filter_params['sort'] = $_GET['sort'];
if (isset($_GET['dir'])) $filter_params['dir'] = $_GET['dir'];
if (isset($_GET['page'])) $filter_params['page'] = $_GET['page'];

try {
    // Check if the product exists
    $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
    $stm->execute([$id]);
    $product = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        temp('info', 'Product not found');
        redirect('product_list.php?' . http_build_query($filter_params));
    }

    // Check if the product is already inactive
    if ($product['product_status'] == 'Inactive') {
        temp('info', 'Product is already inactive');
        redirect('product_list.php?' . http_build_query($filter_params));
    }

    // Deactivate the product
    $stm = $_db->prepare('UPDATE product SET product_status = "Inactive" WHERE product_id = ?');
    $stm->execute([$id]);

    temp('info', 'Product deactivated successfully');
    redirect('product_list.php?' . http_build_query($filter_params));
} catch (PDOException $e) {
    temp('error', 'Database error: ' . $e->getMessage());
    redirect('product_list.php?' . http_build_query($filter_params));
}