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

<table class="product-list-table">
    <tr>
        <th>Id</th>
        <td><?= $s->id ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= $s->email ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->name ?></td>
    </tr>
    <tr>
        <th>Photo</th>
        <td><img src="../../images/photo/<?= $s->photo ?>" alt="User profile photo"></td>
    </tr>
    <tr>
        <th>Phone Number</th>
        <td><?= $s->phone_number ?></td>
    </tr>
    <tr>
        <th>Address</th>
        <td><?= $s->address ?></td>
    </tr>
    <tr>
        <th>Reward Point</th>
        <td><?= $s->reward_point ?></td>
    </tr>
    <tr>
        <th>Role</th>
        <td><?= $s->role ?></td>
    </tr>
</table>

<button class="button" data-get="memberlist.php">Back</button>

<?php
include '../../_foot.php';