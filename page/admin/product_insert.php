<?php
require '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';

// Get all product types from database
$product_types = $_db->query('SELECT type_id, type_name FROM product_type ORDER BY type_name')
    ->fetchAll(PDO::FETCH_KEY_PAIR);

// Get product details if editing
$product = null;
if (isset($_GET['id'])) {
    $stm = $_db->prepare('SELECT p.*, t.type_name FROM product p JOIN product_type t ON p.type_id = t.type_id WHERE p.product_id = ?');
    $stm->execute([$_GET['id']]);
    $product = $stm->fetch(PDO::FETCH_OBJ);
}

if (is_post()) {
    // Get form data
    $id = trim(req('id'));
    $name = trim(req('name'));
    $price = trim(req('price'));
    $descr = trim(req('descr'));
    $type_id = trim(req('type_id'));
    $stock = trim(req('stock'));
    $product_status = trim(req('product_status'));

    // Validate product id
    if (empty($id)) {
        $_err['id'] = 'Required';
    } else if (strlen($id) > 10) {
        $_err['id'] = 'Maximum length 10';
    } else if (!is_unique($id, 'product', 'product_id')) {
        $_err['id'] = 'Duplicated';
    }

    // Validate product name
    if (empty($name)) {
        $_err['name'] = 'Required';
    } else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum length 100';
    } else if (!is_unique($name, 'product', 'product_name')) {
        $_err['name'] = 'Duplicated';
    }

    // Validate price
    if (empty($price)) {
        $_err['price'] = 'Required';
    } else if (!is_money($price)) {
        $_err['price'] = 'Must be money';
    } else if ($price < 0.01 || $price > 99.99) {
        $_err['price'] = 'Must be between 0.01-99.99';
    }

    // Validate description
    if (empty($descr)) {
        $_err['descr'] = 'Required';
    } else if (strlen($descr) > 500) {
        $_err['descr'] = 'Maximum length 500';
    }

    // Validate product type
    if (empty($type_id)) {
        $_err['typeid'] = 'Required';
    } 
    // else if (!isset($product_types[$type_id])) {
    //     $_err['typeid'] = 'Invalid value (must be 1-3)';
    // }

    // Validate stock
    if (!is_numeric($stock)) {
        $_err['stock'] = 'Stock must be a number';
    }

    // Validate product_status
    if (!in_array($product_status, ['Active', 'Inactive', 'Out of Stock'])) {
        $_err['product_status'] = 'Invalid status';
    }

    // Validate product images
    if (!isset($_FILES['product_images']) || empty($_FILES['product_images']['name'][0])) {
        if (!isset($product)) { // Only require images for new products
            $_err['product_images'] = 'At least one product image is required';
        }
    } else {
        $fileCount = 0;
        foreach ($_FILES['product_images']['name'] as $index => $filename) {
            if (!empty($filename)) {
                $fileCount++;
                if ($_FILES['product_images']['error'][$index] !== UPLOAD_ERR_OK) {
                    $_err['product_images'] = 'Error uploading file: ' . $filename;
                    break;
                }
                if (!str_starts_with($_FILES['product_images']['type'][$index], 'image/')) {
                    $_err['product_images'] = 'All files must be images';
                    break;
                }
                if ($_FILES['product_images']['size'][$index] > 1 * 1024 * 1024) {
                    $_err['product_images'] = 'Each image must be less than 1MB';
                    break;
                }
            }
        }

        if ($fileCount > 5) {
            $_err['product_images'] = 'Maximum 5 images allowed';
        }
    }

    // Database
    if (!$_err) {
        try {
            // Begin transaction for multiple inserts
            $_db->beginTransaction();

            // Insert product with first image as primary
            $fileData = [
                'name' => $_FILES['product_images']['name'][0],
                'type' => $_FILES['product_images']['type'][0],
                'tmp_name' => $_FILES['product_images']['tmp_name'][0],
                'error' => $_FILES['product_images']['error'][0],
                'size' => $_FILES['product_images']['size'][0]
            ];
            $primaryPhoto = save_photo((object)$fileData, "../../images/product");

            $stm = $_db->prepare('
                 INSERT INTO product (product_id, product_name, price, description, product_image, type_id, stock, product_status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
             ');
            $stm->execute([$id, $name, $price, $descr, $primaryPhoto, $type_id, $stock, $product_status]);

            // Handle additional product images
            if ($fileCount > 1) {
                $stm = $_db->prepare('
                     INSERT INTO product_images (product_id, image_path) 
                     VALUES (?, ?)
                 ');

                for ($i = 1; $i < $fileCount; $i++) {
                    $fileData = [
                        'name' => $_FILES['product_images']['name'][$i],
                        'type' => $_FILES['product_images']['type'][$i],
                        'tmp_name' => $_FILES['product_images']['tmp_name'][$i],
                        'error' => $_FILES['product_images']['error'][$i],
                        'size' => $_FILES['product_images']['size'][$i]
                    ];

                    $additionalPhoto = save_photo((object)$fileData, "../../images/product");
                    $stm->execute([$id, $additionalPhoto]);
                }
            }

            // Commit all database changes
            $_db->commit();

            temp('info', 'Product added successfully');
            redirect('product_list.php');
        } catch (PDOException $e) {
            $_db->rollBack();
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

    <label for="product_images">Product Images</label>
    <div class="image-upload-zone" id="imageUploadZone">
        <div class="upload-instructions">
            <i class="fas fa-cloud-upload-alt"></i>
            <p class="upload-hint">Drag & drop images here or click to browse</p>
            <p class="upload-hint">(Maximum 5 images, JPG/PNG only, max 1MB each)</p>
        </div>
        <input type="file" name="product_images[]" id="productImages" multiple accept="image/*" style="display: none;" required>
        <div class="image-preview-container" id="imagePreviewContainer">
            <?php
            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                for ($i = 0; $i < count($_FILES['product_images']['name']); $i++) {
                    if ($_FILES['product_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['product_images']['tmp_name'][$i];
                        $name = $_FILES['product_images']['name'][$i];
                        $type = $_FILES['product_images']['type'][$i];

                        if (str_starts_with($type, 'image/')) {
                            $base64 = base64_encode(file_get_contents($tmp_name));
                            echo '<div class="image-preview">
                                     <img src="data:' . $type . ';base64,' . $base64 . '" alt="Preview">
                                     <button class="remove-image" type="button">×</button>
                                   </div>';
                        }
                    }
                }
            } else if (isset($product) && $product) {
                // Show existing images if editing
                echo '<div class="image-preview">
                         <img src="../../images/product/' . $product->product_image . '" alt="Preview">
                         <button class="remove-image" type="button">×</button>
                      </div>';
                
                // Show additional images
                $stm = $_db->prepare('SELECT image_path FROM product_images WHERE product_id = ?');
                $stm->execute([$product->product_id]);
                $additionalImages = $stm->fetchAll();
                
                foreach ($additionalImages as $image) {
                    echo '<div class="image-preview">
                             <img src="../../images/product/' . $image->image_path . '" alt="Preview">
                             <button class="remove-image" type="button">×</button>
                          </div>';
                }
            }
            ?>
        </div>
    </div>
    <?= err('product_images') ?>

    <div class="form-group">
        <label for="type_id">Type</label>
        <select id="type_id" name="type_id" required>
            <option value="">-- Select Type --</option>
            <?php foreach ($product_types as $id => $name): ?>
                <option value="<?= $id ?>" <?= isset($type_id) && $type_id == $id ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?= err('type_id') ?>

    <label for="stock">Stock</label>
    <?= html_number('stock', 0, 9999, 1) ?>
    <?= err('stock') ?>

    <div class="form-group">
        <label for="product_status">Status</label>
        <select id="product_status" name="product_status" required>
            <option value="">-- Select Status --</option>
            <option value="Active" <?= isset($product_status) && $product_status == 'Active' ? 'selected' : '' ?>>Active</option>
            <option value="Inactive" <?= isset($product_status) && $product_status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="Out of Stock" <?= isset($product_status) && $product_status == 'Out of Stock' ? 'selected' : '' ?>>Out of Stock</option>
        </select>
    </div>
    <?= err('product_status') ?>

    <section>
        <button type="submit">Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="product_list.php">Back</button>

<?php
include '../../_foot.php';