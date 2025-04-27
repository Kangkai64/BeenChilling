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

    // Check if the user is already active
    if ($user['status'] == 2) {
        temp('info', 'Account is already active');
        redirect('user_list.php');
    }

    // SQL query that sets the status to 2 to indicate activation
    $stm = $_db->prepare('UPDATE user SET status = 2 WHERE id = ?');
    $stm->execute([$user_id]);
    temp('info', 'Account activated successfully');
}

redirect('user_list.php');
?>