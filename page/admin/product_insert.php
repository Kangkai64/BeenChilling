<?php
require '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';

if (is_post()) {
    $id         = req('id');
    $name       = req('name');
    $price      = req('price');
    $descr      = req('descr');
    $f          = get_file('photo');
    $typeid     = req('typeid');

    // Validate product id
    if ($id == '') {
        $_err['id'] = 'Required';
    }
    else if (strlen($id) > 10) {
        $_err['id'] = 'Maximum length 10';
    }
    else if (!is_unique($id, 'product', 'product_id')) {
        $_err['id'] = 'Duplicated';
    }

    // Validate product name
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum length 100';
    }
    else if (!is_unique($name, 'product', 'product_name')) {
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
    if (!$f) {
        $_err['photo'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    }
    else if ($f->size > 1 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 1MB';
    }

    // Validate product type
    if ($typeid == '') {
        $_err['typeid'] = 'Required';
    }
    else if (!in_array($typeid, [1, 2, 3])) {
        $_err['typeid'] = 'Invalid value (must be 1-3)';
    }

    // Database
    if (!$_err) {
        try {
            // Save photo
            $photo = save_photo($f, "../../images/product");

            // Insert product
            $stm = $_db->prepare('
                INSERT INTO product (product_id, product_name, price, description, product_image, type_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stm->execute([$id, $name, $price, $descr, $photo, $typeid]);

            temp('info', 'Record inserted');
            redirect('product_list.php');
        } catch (PDOException $e) {
            $_err['db'] = 'Database error: ' . $e->getMessage();
        }
    }
}

?>

<form method="post" class="form" enctype="multipart/form-data" data-title="Insert Product" novalidate>
    <label for="id">Product ID</label>
    <?= html_text('id', 'maxlength="10"') ?>
    <?= err('id') ?>

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

    <label for="typeid">Type</label>
    <?= html_select('typeid', $_producttype, '-- Select Type --') ?>
    <?= err('typeid') ?>
    
    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="product_list.php">Back</button>

<?php
include '../../_foot.php';