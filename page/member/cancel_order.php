<?php
require '../../_base.php';

if (is_post()) {
    $order_id = post('order_id');

    $stm = $_db->prepare('UPDATE orders SET status = "cancelled" WHERE id = ?');
    $stm->execute([$order_id]);

    temp('info', 'Order cancelled successfully');
    redirect('order_history.php');
}

