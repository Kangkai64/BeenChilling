<?php
include '../_base.php';

setcookie('remember_token', '', time() - 3600, '/');
temp('info', 'Logout successfully');
logout();