<?php
include '../../_base.php';
auth('Admin');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // SQL query that sets the status to 0 to indicate deactivation
    $stm = $_db->prepare('UPDATE user SET status = 0 WHERE id = ?');
    $stm->execute([$user_id]); // Fixed variable name and removed extra parameter
    temp('info', 'Account deactivated successfully'); // Fixed typo
}

redirect('user_list.php');
?>