<?php
include '../../_base.php';
auth('Admin');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Check if the user exists
    $stm = $_db->prepare('SELECT id FROM user WHERE id = ?');
    $stm->execute([$user_id]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        temp('info', 'User not found');
        redirect('user_list.php');
    }

    // Check if it is user's own account
    if ($user_id == $_user->id) {
        temp('info', 'You cannot deactivate your own account');
        redirect('user_list.php');
    }

    // Check if the user is active
    $stm = $_db->prepare('SELECT status FROM user WHERE id = ?');
    $stm->execute([$user_id]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);

    if ($user['status'] != 2) {
        temp('info', 'Only active accounts can be deactivated');
        redirect('user_list.php');
    }

    // SQL query that sets the status to 0 to indicate deactivation
    $stm = $_db->prepare('UPDATE user SET status = 0 WHERE id = ?');
    $stm->execute([$user_id]);
    temp('info', 'Account deactivated successfully');
}

redirect('user_list.php');
?>