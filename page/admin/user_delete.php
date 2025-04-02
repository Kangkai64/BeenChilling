<?php
require '../../_base.php';

auth('Admin');

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare('DELETE FROM user WHERE id = ?');
    $stm->execute([$id]);

    temp('info', 'Record deleted');
}

redirect('/');