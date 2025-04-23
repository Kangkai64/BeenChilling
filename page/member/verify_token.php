<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Clean expired tokens
$_db->query("DELETE FROM token WHERE expire < NOW()");

$id = req('id');

// Get the verify token
$stm = $_db->prepare('SELECT * FROM token WHERE id = ? AND type = "verify"');
$stm->execute([$id]);
$token = $stm->fetch();

if (!$token) {
    temp('info', 'Invalid or expired verification link.');
    redirect('/');
}

// Update user status to 2 (verified)
$stm = $_db->prepare('UPDATE user SET status = 2 WHERE id = ?');
$stm->execute([$token->user_id]);

// Delete the token
$stm = $_db->prepare('DELETE FROM token WHERE id = ?');
$stm->execute([$id]);

temp('info', 'Your email has been verified. You may now log in.');
redirect('../login.php');
