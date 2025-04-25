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
        LEFT JOIN producttype t ON p.type_id = t.type_id
        WHERE p.product_id = ?
    ');
    $stm->execute([$id]);
    $product = $stm->fetch();

    if (!$product) {
        $_err['product'] = 'Product not found';
    }
} catch (PDOException $e) {
    $_err['db'] = 'Database error: ' . $e->getMessage();
}
?>

<?php if (!$_err): ?>
<div class="product-details-container">
    <div>
        <img class="product-image" src="../../images/product/<?= $product->product_image ?> " alt="Product photo">
    </div>
    <div class="product-details">
        <h1><?= $product->product_name ?></h1>
        <h2>
            RM<?= number_format($product->price, 2) ?>
        </h2>
        <h3>
            <?= $product->description ?>
        </h3>
    </div>
</div>

<button class="button" data-get="product_list.php">Back</button>
<button class="button" data-get="product_update.php?id=<?= $product->product_id ?>" data-confirm>Update</button>

<table>
    <tr>
        <th>Product ID</th>
        <td><?=$product->product_id ?></td>
    </tr>
    <tr>
        <th>Product Name</th>
        <td><?=$product->product_name ?></td>
    </tr>
    <tr>
        <th>Price</th>
        <td>RM <?=number_format($product->price, 2) ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?=$product->description ?></td>
    </tr>
    <tr>
        <th>Type</th>
        <td><?=$product->type_name ?></td>
    </tr>
    <tr>
        <th>Image</th>
        <td>
            <img src="../images/product/<?=$product->product_image ?>" alt="<?=$product->product_name ?>" style="max-width: 200px;">
        </td>
    </tr>
</table>
<?php else: ?>
    <div class="error-message">
        <?= err('product') ?>
        <?= err('db') ?>
    </div>
    <button class="button" data-get="product_list.php">Back</button>
<?php endif; ?>

<?php
include '../../_foot.php';