<?php
require '../../_base.php';

auth('Admin');

if (is_post()) {
    $id = req('id');
    
    // Begin transaction to ensure atomicity
    $_db->beginTransaction();
    
    try {
        // First delete all shipping addresses for this user
        $stm = $_db->prepare('DELETE FROM shipping_address WHERE user_id = ?');
        $stm->execute([$id]);
        
        // Then delete any reviews by this user
        $stm = $_db->prepare('DELETE FROM review WHERE member_id = ?');
        $stm->execute([$id]);
        
        // Finally delete the user
        $stm = $_db->prepare('DELETE FROM user WHERE id = ?');
        $stm->execute([$id]);
        
        // Commit the transaction
        $_db->commit();
        
        temp('info', 'User and related records deleted successfully');
    } catch (Exception $e) {
        // Rollback the transaction on error
        $_db->rollBack();
        temp('error', 'Error deleting user: ' . $e->getMessage());
    }
}

redirect('user_list.php');