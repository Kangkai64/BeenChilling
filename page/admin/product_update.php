<?php
require '../../_base.php';

$_title = 'BeenChilling';
include '../../_head.php';

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM product WHERE ProductID = ?');
    $stm->execute([$id]);
    $p = $stm->fetch();

    if (!$p) {
        redirect('productlist.php');
    }

    extract((array)$p);

    $_SESSION['photo'] = $p->ProductImage;
}

if (is_post()) {
    $id         = req('id');
    $name       = req('name');
    $price      = req('price');
    $descr      = req('descr');
    $f     = get_file('photo');
    $photo = $_SESSION['photo'];
    
    // Validate product name
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum length 100';
    }
    else if (!is_unique($name, 'product', 'ProductName')) {
        $_err['name'] = 'Duplicated';
    }

    // Validate price
    if ($price == '') {
        $_err['price'] = 'Required';
    }
    else if (!is_money($price)) {
        $_err['price'] = 'Must be money';
    }
    else if ($price < 0.01 || $price > 99.99) {
        $_err['price'] = 'Must be between 0.01-99.99';
    }

    // Validate description
    if ($descr == '') {
        $_err['descr'] = 'Required';
    }
    else if (strlen($descr) > 500) {
        $_err['descr'] = 'Maximum length 500';
    }

    // Validate: photo (file)
    // ** Only if a file is selected **
    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
        else if ($f->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }

    // DB operation
    if (!$_err) {

        if ($f) {
            unlink("../../images/product/$photo");
            $photo = save_photo($f, '../../images/product');
        }

        $stm = $_db->prepare('UPDATE product
                              SET ProductName = ?, Price = ?, Description = ?, ProductImage = ?
                              WHERE ProductID = ?');
        $stm->execute([$name, $price, $descr, $photo, $id]);

        temp('info', 'Record updated');
        redirect('productlist.php');
    }
}

?>

<form method="post" class="form" data-title="Update Product" enctype="multipart/form-data" novalidate>
 
    <label for="id">Product ID</label>
    <b class = form-unchange><?= $id ?></b>
    <label for="name">Product Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="price">Price</label>
    <?= html_number('price', 0.01, 99.99, 0.01) ?>
    <?= err('price') ?>

    <label for="descr">Description</label>
    <?= html_text('descr', 'maxlength="500"') ?>
    <?= err('descr') ?>

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/images/photo.jpg">
    </label>
    <?= err('photo') ?>

    <label for="typeid">Type ID</label>
  

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="productlist.php">Back</button>

<?php
include '../../_foot.php';