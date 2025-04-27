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
        LEFT JOIN product_type t ON p.type_id = t.type_id
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
    $id = req('id');
    $product_name = req('product_name');
    $price = req('price');
    $description = req('description');
    $type_id = req('type_id');
    $f = get_file('product_image');
    $stock = req('stock');
    $product_status = ($stock > 0) ? 'Active' : 'Out of Stock';
    $additional_images = isset($_FILES['additional_images']) ? $_FILES['additional_images'] : null;
    $deleted_images = isset($_POST['deleted_images']) ? json_decode($_POST['deleted_images'], true) : [];

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

    if (!is_numeric($stock)) {
        $_err['stock'] = 'Stock must be a number';
    }

    // Validate additional images
    if ($additional_images && !empty($additional_images['name'][0])) {
        $fileCount = 0;
        foreach ($additional_images['name'] as $index => $filename) {
            if (!empty($filename)) {
                $fileCount++;
                if ($additional_images['error'][$index] !== UPLOAD_ERR_OK) {
                    $_err['additional_images'] = 'Error uploading file: ' . $filename;
                    break;
                }
                if (!str_starts_with($additional_images['type'][$index], 'image/')) {
                    $_err['additional_images'] = 'All files must be images';
                    break;
                }
                if ($additional_images['size'][$index] > 1 * 1024 * 1024) {
                    $_err['additional_images'] = 'Each image must be less than 1MB';
                    break;
                }
            }
        }

        // Get current count of additional images
        $stm = $_db->prepare('SELECT COUNT(*) FROM product_images WHERE product_id = ?');
        $stm->execute([$id]);
        $currentCount = $stm->fetchColumn();
        
        // Calculate how many images will be deleted
        $deletedCount = count($deleted_images);
        
        // Check if total images would exceed limit (4 additional images)
        if (($currentCount - $deletedCount + $fileCount) > 4) {
            $_err['additional_images'] = 'Maximum 4 additional images allowed';
        }
    }

    if (empty($_err)) {
        try {
            $_db->beginTransaction();

            $photo = $_SESSION['photo'];
            if ($f) {
                if (!str_starts_with($f->type, 'image/')) {
                    $_err['product_image'] = 'Must be image';
                } else if ($f->size > 1 * 1024 * 1024) {
                    $_err['product_image'] = 'Maximum 1MB';
                } else {
                    $photo = save_photo($f, "../../images/product");
                }
            }

            if (empty($_err)) {
                $stm = $_db->prepare('
                    UPDATE product
                    SET product_name = ?,
                        price = ?,
                        description = ?,
                        type_id = ?,
                        product_image = ?,
                        stock = ?,
                        product_status = ?
                    WHERE product_id = ?
                ');
                $stm->execute([$product_name, $price, $description, $type_id, $photo, $stock, $product_status, $id]);
                
                // Handle deleted additional images
                if (!empty($deleted_images)) {
                    // Get image paths before deletion
                    $stm = $_db->prepare('SELECT image_path FROM product_images WHERE image_id IN (' . implode(',', array_fill(0, count($deleted_images), '?')) . ')');
                    $stm->execute($deleted_images);
                    $imagesToDelete = $stm->fetchAll(PDO::FETCH_COLUMN);
                    
                    // Delete from database
                    $stm = $_db->prepare('DELETE FROM product_images WHERE image_id IN (' . implode(',', array_fill(0, count($deleted_images), '?')) . ')');
                    $stm->execute($deleted_images);
                    
                    // Delete files from server
                    foreach ($imagesToDelete as $imagePath) {
                        $filePath = "../../images/product/" . $imagePath;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
                
                // Handle new additional images
                if ($additional_images && !empty($additional_images['name'][0])) {
                    $stm = $_db->prepare('
                        INSERT INTO product_images (product_id, image_path) 
                        VALUES (?, ?)
                    ');
                    
                    foreach ($additional_images['name'] as $index => $filename) {
                        if (!empty($filename) && $additional_images['error'][$index] === UPLOAD_ERR_OK) {
                            $fileData = [
                                'name' => $additional_images['name'][$index],
                                'type' => $additional_images['type'][$index],
                                'tmp_name' => $additional_images['tmp_name'][$index],
                                'error' => $additional_images['error'][$index],
                                'size' => $additional_images['size'][$index]
                            ];
                            
                            $additionalPhoto = save_photo((object)$fileData, "../../images/product");
                            $stm->execute([$id, $additionalPhoto]);
                        }
                    }
                }
                
                temp('info', 'Record updated');
                $_db->commit();
                redirect('product_list.php');
            }
        } catch (PDOException $e) {
            $_db->rollBack();
            $_err['db'] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Get product data for form
$stm = $_db->prepare('
SELECT p.*, t.type_name
FROM product p
LEFT JOIN product_type t ON p.type_id = t.type_id
WHERE p.product_id = ?
');
$stm->execute([$id]);
$product = $stm->fetch();

if (!$product) {
    redirect('product_list.php');
}

extract((array)$product);
$_SESSION['photo'] = $product_image;
?>

<form method="post" class="form" data-title="Update Product" enctype="multipart/form-data" novalidate>

    <label for="id">Product ID</label>
    <b class="form-unchange"><?= $id ?></b>
    <input type="hidden" name="id" value="<?= $id ?>">

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
    <b class="form-unchange"><?= $type_name ?></b>
    <input type="hidden" name="type_id" value="<?= $type_id ?>">

    <label for="stock">Stock</label>
    <?= html_number('stock', 0, 9999, 1) ?>
    <?= err('stock') ?>

    <label for="product_status">Status</label>
    <b class="form-unchange"><?= ($stock > 0) ? 'Active' : 'Out of Stock' ?></b>
    <input type="hidden" name="product_status" value="<?= ($stock > 0) ? 'Active' : 'Out of Stock' ?>">

    <label for="product_image">Product Image</label>
    <label class="upload" tabindex="0">
        <?= html_file('product_image', 'image/*', 'hidden') ?>
        <img src="../../images/product/<?= $product_image ?>">
    </label>
    <?= err('product_image') ?>

    <label for="additional_images">Additional Images (Max 4)</label>
    <div class="image-upload-zone" id="additionalImagesUploadZone">
        <div class="upload-instructions">
            <i class="fas fa-cloud-upload-alt"></i>
            <p class="upload-hint">Drag & drop additional images here or click to browse</p>
            <p class="upload-hint">(Maximum 4 additional images, JPG/PNG only, max 1MB each)</p>
        </div>
        <input type="file" name="additional_images[]" id="additionalImages" multiple accept="image/*" style="display: none;">
        <div class="image-preview-container" id="additionalImagesPreviewContainer">
            <?php
            // Show existing additional images
            $stm = $_db->prepare('SELECT image_id, image_path FROM product_images WHERE product_id = ?');
            $stm->execute([$id]);
            $additionalImages = $stm->fetchAll();
            
            foreach ($additionalImages as $image) {
                echo '<div class="image-preview" data-image-id="' . $image->image_id . '">
                         <img src="../../images/product/' . $image->image_path . '" alt="Additional product photo">
                         <button class="remove-image" type="button" data-image-id="' . $image->image_id . '">×</button>
                      </div>';
            }
            ?>
        </div>
    </div>
    <input type="hidden" name="deleted_images" id="deletedImages" value="[]">
    <?= err('additional_images') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="product_list.php">Back</button>

<?php
include '../../_foot.php';