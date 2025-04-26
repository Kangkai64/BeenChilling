<?php
include '../_base.php';

setcookie('remember_email', '', time() - 3600, '/');
temp('info', 'Logout successfully');
logout();