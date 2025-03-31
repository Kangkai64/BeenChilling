<?php
require '../../_base.php';

$_title = 'BeenChilling';
include '../../_head.php';

$id = req('id');

$stm = $_db->prepare("SELECT * FROM product WHERE ProductID = ?");
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) {
    redirect('productlist.php');
}
?>

<div class = product-details-container>
    <div>
        <img class = product-image src="../../images/product/<?= $s->ProductImage ?> " alt="Product photo">
    </div>
    <div class = product-details>
        <h1><?= $s-> ProductName ?></h1>
        <h2>
            RM<?= $s-> Price?>
        </h2>
        <h3>
            <?= $s-> Description?>
        </h3>
    </div>
</div>

<button class="button" data-get="productlist.php">Back</button>
<button class="button" data-get="product_update.php?id=<?= $s->ProductID ?>" data-confirm>Update</button>
<?php
include '../../_foot.php';