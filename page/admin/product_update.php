<?php
require '../../_base.php';

auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('
        SELECT p.*, t.type_name
        FROM product p
        LEFT JOIN producttype t ON p.type_id = t.type_id
        WHERE p.product_id = ?
    ');
    $stm->execute([$id]);
    $product = $stm->fetch();

    if (!$product) {
        redirect('product_list.php');
    }

    extract((array)$product);

    $_SESSION['photo'] = $product_image;
}

if (is_post()) {
    $product_name = req('product_name');
    $price = req('price');
    $description = req('description');
    $type_id = req('type_id');
    $product_image = req('product_image');

    if (empty($product_name)) {
        $_err['product_name'] = 'Product name is required';
    } elseif (strlen($product_name) > 100) {
        $_err['product_name'] = 'Product name must be less than 100 characters';
    }

    if (empty($price)) {
        $_err['price'] = 'Price is required';
    } elseif (!is_numeric($price)) {
        $_err['price'] = 'Price must be a number';
    }

    if (empty($description)) {
        $_err['description'] = 'Description is required';
    }

    if (empty($type_id)) {
        $_err['type_id'] = 'Product type is required';
    }

    if (empty($_err)) {
        $stm = $_db->prepare('
            UPDATE product
            SET product_name = ?,
                price = ?,
                description = ?,
                type_id = ?,
                product_image = ?
            WHERE product_id = ?
        ');
        $stm->execute([$product_name, $price, $description, $type_id, $product_image, $id]);
        redirect('product_list.php');
    }
}

?>

<form method="post" class="form" data-title="Update Product" enctype="multipart/form-data" novalidate>
 
    <label for="id">Product ID</label>
    <b class = form-unchange><?= $id ?></b>
    <label for="product_name">Product Name</label>
    <?= html_text('product_name', 'maxlength="100"') ?>
    <?= err('product_name') ?>

    <label for="price">Price</label>
    <?= html_number('price', 0.01, 99.99, 0.01) ?>
    <?= err('price') ?>

    <label for="description">Description</label>
    <?= html_text('description', 'maxlength="500"') ?>
    <?= err('description') ?>

    <label for="type_id">Product Type</label>
    <select id="type_id" name="type_id">
        <option value="">Select Type</option>
        <?php
        $stm = $_db->query('SELECT * FROM product_type');
        while ($type = $stm->fetch()) {
            echo '<option value="' . $type->type_id . '"' . ($type->type_id == $type_id ? ' selected' : '') . '>' . $type->type_name . '</option>';
        }
        ?>
    </select>
    <?= err('type_id') ?>

    <label for="product_image">Product Image</label>
    <label class="upload" tabindex="0">
        <?= html_file('product_image', 'image/*', 'hidden') ?>
        <img src="/images/photo.jpg">
    </label>
    <?= err('product_image') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="product_list.php">Back</button>

<?php
include '../../_foot.php';