<?php
require '../../_base.php';
auth('Admin');

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT ProductImage FROM product WHERE ProductID = ?');
    $stm->execute([$id]);
    $photo = $stm->fetchColumn();
    unlink("../../images/product/$photo");

    $stm = $_db->prepare('DELETE FROM product WHERE ProductID = ?');
    $stm->execute([$id]);

    temp('info', 'Record deleted');
}

redirect('/page/admin/productlist.php');

