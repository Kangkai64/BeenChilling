<?php
include '../../_base.php';
auth('Admin');

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Update payment_status to 'awaiting_payment'
    $stm = $_db->prepare('UPDATE `order` SET payment_status = ? WHERE order_id = ?');
    $stm->execute(['awaiting_payment', $order_id]);

    temp('info', 'Payment status updated to "awaiting_payment" successfully.');
}

redirect('order_list.php');
?>
