<?php
require '../../_base.php';

if (is_post()) {
    $order_id = req('order_id');

    $stm = $_db->prepare('UPDATE `order` SET order_status = "cancelled" WHERE order_id = ?');
    $stm->execute([$order_id]);

    temp('info', 'Order cancelled successfully. The refund will be processed within 3 working days.');
    redirect('order_history.php');
}

