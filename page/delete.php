<?php
require '../_base.php';

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare('DELETE FROM product WHERE ProductID = ?');
    $stm->execute([$id]);

    temp('info', 'Record deleted');
}

redirect('/');