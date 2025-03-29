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

<button class="button" data-get="productlist.php">Back</button>

<?php
include '../../_foot.php';