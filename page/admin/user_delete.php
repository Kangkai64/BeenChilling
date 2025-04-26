<?php
require '../../_base.php';

auth('Admin');

// Check if we're in batch mode with multiple IDs
if (isset($_GET['batch']) && isset($_GET['ids'])) {
    $ids_string = $_GET['ids'];
    $ids = explode(',', $ids_string);
    
    $success_count = 0;
    $error_count = 0;
    $admin_count = 0;
    
    // Begin transaction to ensure atomicity for the entire batch
    $_db->beginTransaction();
    
    try {
        foreach ($ids as $id) {
            $id = trim($id);
            if (empty($id)) continue;
            
            // Check if the user is an admin
            $stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
            $stm->execute([$id]);
            $user = $stm->fetch(PDO::FETCH_OBJ);
            
            if (!$user) {
                $error_count++;
                continue;
            }
            
            if ($user->role == 'Admin') {
                $admin_count++;
                continue;
            }
            
            // Perform the cascading deletes
            $stm = $_db->prepare('DELETE FROM order_item WHERE order_id IN (SELECT order_id FROM `order` WHERE member_id = ?)');
            $stm->execute([$id]);
            
            $stm = $_db->prepare('DELETE FROM `order` WHERE member_id = ?');
            $stm->execute([$id]);
            
            $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id IN (SELECT cart_id FROM cart WHERE member_id = ?)');
            $stm->execute([$id]);
            
            $stm = $_db->prepare('DELETE FROM cart WHERE member_id = ?');
            $stm->execute([$id]);
            
            $stm = $_db->prepare('DELETE FROM wishlist_item WHERE wishlist_id IN (SELECT wishlist_id FROM wishlist WHERE member_id = ?)');
            $stm->execute([$id]);
            
            $stm = $_db->prepare('DELETE FROM wishlist WHERE member_id = ?');
            $stm->execute([$id]);
            
            $stm = $_db->prepare('DELETE FROM shipping_address WHERE user_id = ?');
            $stm->execute([$id]);
            
            $stm = $_db->prepare('DELETE FROM review WHERE member_id = ?');
            $stm->execute([$id]);
            
            $stm = $_db->prepare('DELETE FROM user WHERE id = ?');
            $stm->execute([$id]);
            
            $success_count++;
        }
        
        // Commit the transaction
        $_db->commit();
        
        $message = "$success_count users deleted successfully";
        if ($admin_count > 0) {
            $message .= ", $admin_count admin users skipped";
        }
        if ($error_count > 0) {
            $message .= ", $error_count users not found";
        }
        
        temp('info', $message);
    } catch (Exception $e) {
        // Rollback the transaction on error
        $_db->rollBack();
        temp('error', 'Error deleting users: ' . $e->getMessage());
    }
    
    redirect('batch_operation.php');
}
// If not batch mode, handle a single ID as before
else if (is_post()) {
    $id = req('id');
    
    // Original code for single user deletion
    $_db->beginTransaction();

    try {
        // Check if the user is a admin
        $stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
        $stm->execute([$id]);
        $user = $stm->fetch(PDO::FETCH_OBJ);
        if ($user->role == 'Admin') {
            temp('info', 'Admin user cannot be deleted');
            redirect('user_list.php');
        }

        // First delete all order items for this user
        $stm = $_db->prepare('DELETE FROM order_item WHERE order_id IN (SELECT order_id FROM `order` WHERE member_id = ?)');
        $stm->execute([$id]);

        // Then delete all order for this user
        $stm = $_db->prepare('DELETE FROM `order` WHERE member_id = ?');
        $stm->execute([$id]);

        // Then delete all cart items for this user
        $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id IN (SELECT cart_id FROM cart WHERE member_id = ?)');
        $stm->execute([$id]);

        // Then delete all cart for this user
        $stm = $_db->prepare('DELETE FROM cart WHERE member_id = ?');
        $stm->execute([$id]);

        // Then delete all wishlist items for this user
        $stm = $_db->prepare('DELETE FROM wishlist_item WHERE wishlist_id IN (SELECT wishlist_id FROM wishlist WHERE member_id = ?)');
        $stm->execute([$id]);

        // Then delete all wishlist for this user
        $stm = $_db->prepare('DELETE FROM wishlist WHERE member_id = ?');
        $stm->execute([$id]);

        // Then delete all shipping addresses for this user
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
        temp('info', 'Error deleting user: ' . $e->getMessage());
    }
    
    redirect('user_list.php');
}
else {
    redirect('user_list.php');
}