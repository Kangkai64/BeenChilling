<?php
require '../../_base.php';

$_title = 'BeenChilling';
include '../../_head.php';

$id = req('id');

$stm = $_db->prepare("SELECT * FROM user WHERE id = ?");
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) {
    redirect('memberlist.php');
}
?>

<button class="button" data-get="memberlist.php">Back</button>

<?php
include '../../_foot.php';