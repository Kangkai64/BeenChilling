<?php
require '../../_base.php';
auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';

$id = req('id');

try {
    // Get product details
    $stm = $_db->prepare('
        SELECT p.*, t.type_name
        FROM product p
        LEFT JOIN product_type t ON p.type_id = t.type_id
        WHERE p.product_id = ?
    ');
    $stm->execute([$id]);
    $product = $stm->fetch();

    if (!$product) {
        $_err['product'] = 'Product not found';
    }

    // Get additional product images
    $stm = $_db->prepare('
         SELECT image_path
         FROM product_images
         WHERE product_id = ?
         ORDER BY created_at ASC
     ');
    $stm->execute([$id]);
    $additionalImages = $stm->fetchAll();
} catch (PDOException $e) {
    $_err['db'] = 'Database error: ' . $e->getMessage();
}
?>

<?php if (!$_err): ?>
    <div class="product-details-container">
        <div class="product-images-section">
            <div class="image-slider">
                <div class="slider-container">
                    <!-- Primary Image -->
                    <div class="slide active">
                        <img class="product-image" src="../../images/product/<?= $product->product_image ?>" alt="Product photo">
                    </div>
                    <!-- Additional Images -->
                    <?php foreach ($additionalImages as $image): ?>
                        <div class="slide">
                            <img src="../../images/product/<?= $image->image_path ?>" alt="Additional product photo">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="slider-controls">
                    <button class="prev-btn">&lt;</button>
                    <div class="slider-dots">
                        <span class="dot active"></span>
                        <?php for ($i = 1; $i < count($additionalImages) + 1; $i++): ?>
                            <span class="dot"></span>
                        <?php endfor; ?>
                    </div>
                    <button class="next-btn">&gt;</button>
                </div>
            </div>
        </div>
        <div class="product-details">   
            <table class="product-info-table">
                <tr>
                    <th>Product Name</th>
                    <td><?= $product->product_name ?></td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>RM<?= number_format($product->price, 2) ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= $product->description ?></td>
                </tr>
                <tr>
                    <th>Stock</th>
                    <td><?= $product->stock ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= $product->product_status ?></td>
                </tr>
            </table>
            <div class="button-container">
                <button class="button" data-get="product_update.php?id=<?= $product->product_id ?>" data-confirm>Update</button>
            </div>
        </div>
    </div>

    <button class="button" data-get="product_list.php">Back</button>

    <?php if (isset($_err['product']) || isset($_err['db'])): ?>
    <div class="error-message">
        <?= err('product') ?>
        <?= err('db') ?>
    </div>
    <?php endif; ?>

<?php endif; ?>

<?php
include '../../_foot.php';