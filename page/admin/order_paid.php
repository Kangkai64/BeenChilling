<?php
require '../../_base.php';

auth('Admin');

$order_id = $_POST['order_id'] ?? null;

if (!$order_id) {
    temp('error', 'Order ID not provided.');
    redirect('order_list.php');
}

// Update the payment status to "paid"
$stm = $_db->prepare("UPDATE `order` SET payment_status = 'paid' WHERE order_id = ?");
$stm->execute([$order_id]);

if ($stm->rowCount() > 0) {
    temp('info', "Order #$order_id marked as paid.");
} else {
    temp('error', "No matching order found or already marked as paid.");
}

redirect('order_list.php');
