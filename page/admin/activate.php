<?php
include '../../_base.php';
auth('Admin');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // SQL query that sets the status to 1 to indicate activation
    $stm = $_db->prepare('UPDATE user SET status = 2 WHERE id = ?');
    $stm->execute([$user_id]);
    temp('info', 'Account activated successfully');
}

redirect('user_list.php');
?>