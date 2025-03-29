<?php
require '../../_base.php';

$_title = 'BeenChilling';
include '../../_head.php';

if (is_post()) {
    $id     = req('id');
    $name   = req('name');
    $price  = req('price');
    $descr  = req('descr');
    $image  = req('image');
    $type   = req('type');


    //Validate id
    if ($id == '') {
        $_err['id'] = 'Required';
    }
    else if (is_unique($id, 'product', 'ProductID')) {
        $_err['id'] = 'Duplicated';
    }

    //Validate name
    if ($id == '') {
        $_err['id'] = 'Required';
    }
    else if (is_unique($id, 'product', 'ProductID')) {
        $_err['id'] = 'Duplicated';
    }

    if (!$s) {
        redirect('productlist.php');
    }

}
?>

<button class="button" data-get="productlist.php">Back</button>

<?php
include '../../_foot.php';