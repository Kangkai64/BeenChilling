<?php
require '../../_base.php';
auth('Admin');

if (is_post()) {
    $order_id = req('order_id');

    // Debug: Log received order_id
    file_put_contents('debug_order.log', "Trying to delete order_id: $order_id\n", FILE_APPEND);

    $_db->beginTransaction();

    try {
        // Optional: delete related data here if needed
        // Example: $_db->prepare("DELETE FROM cart_item WHERE order_id = ?")->execute([$order_id]);

        // Delete from order table (wrapped in backticks)
        $stm = $_db->prepare('DELETE FROM `order` WHERE order_id = ?');
        $success = $stm->execute([$order_id]);

        // Debug: Log deletion result
        file_put_contents('debug_order.log', "Deleted rows: " . $stm->rowCount() . "\n", FILE_APPEND);

        if ($stm->rowCount() > 0) {
            $_db->commit();
            temp('info', 'Order deleted successfully');
        } else {
            $_db->rollBack();
            temp('error', 'No matching order found or deletion failed.');
        }
    } catch (Exception $e) {
        $_db->rollBack();
        temp('error', 'Error deleting order: ' . $e->getMessage());
    }
}

redirect('order_list.php');
