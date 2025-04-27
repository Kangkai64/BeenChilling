<?php
require '../../_base.php';

auth('Admin');

$order_id = req('order_id');

$stm = $_db->prepare('SELECT * FROM `order` WHERE order_id = ?');
$stm->execute([$order_id]);
$order = $stm->fetch(PDO::FETCH_OBJ);

if (!$order) {
    temp('info', 'Order not found');
    redirect('order_list.php');
}

if ($order->order_status !== 'cancelled') {
    temp('info', 'Order is not cancelled');
    redirect('order_list.php');
}

if ($order->payment_status !== 'paid') {
    temp('info', 'Order is not paid');
    redirect('order_list.php');
}

$stm = $_db->prepare('UPDATE `order` SET order_status = ? WHERE order_id = ?');
$stm->execute(['refunded', $order_id]);

$stm = $_db->prepare('UPDATE `user` SET reward_point = reward_point + ? WHERE id = ?');
$stm->execute([$order->total_amount * 100, $order->member_id]);

temp('info', 'Order refunded successfully');
redirect('order_list.php');