<?php
require '../../_base.php';

auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';

$id = req('id');
$stm = $_db->prepare("
    SELECT u.*, 
           sa.street_address,
           sa.city,
           sa.state,
           sa.postal_code,
           sa.country,
           sa.recipient_name,
           sa.address_phone_number,
           DATE_FORMAT(sa.created_at, '%d/%m/%Y %H:%i:%s') AS address_created_at, 
           DATE_FORMAT(sa.updated_at, '%d/%m/%Y %H:%i:%s') AS address_updated_at
    FROM user u 
    LEFT JOIN shipping_address sa ON u.id = sa.user_id 
    WHERE u.id = ?
");

$stm->execute([$id]);
$s = $stm->fetchAll(PDO::FETCH_OBJ);

if (!$s) {
    redirect('user_list.php');
}

$user = $s[0];
$count = 0;

?>

<table class="product-list-table member-details">
    <tr>
        <th>Id</th>
        <td><?= $user->id ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= $user->email ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $user->name ?></td>
    </tr>
    <tr>
        <th>Photo</th>
        <td><img src="/images/photo/<?= $user->photo ?>" alt="User profile photo"></td>
    </tr>
    <tr>
        <th>Phone Number</th>
        <td><?= $user->phone_number ?? "No phone number found" ?></td>
    </tr>
    <tr>
        <th colspan="2">Shipping Address</th>
    </tr>

    <tr>
        <td colspan="2">
            <?php if ($s[0]->street_address) : ?>
                <?php foreach ($s as $address) : ?>
                    <table class="address-details">
                        <tr>
                            <td>Recipient Name</td>
                            <td>:</td>
                            <td><?= $address->recipient_name ?></td>
                        </tr>
                        <tr>
                            <td>Address <?= ++$count ?></td>
                            <td>:</td>
                            <td><?= $address->street_address . ', ' . $address->city . ', ' . $address->state . ' ' . $address->postal_code . ', ' . $address->country ?></td>
                        </tr>
                        <tr>
                            <td>Address Phone Number</td>
                            <td>:</td>
                            <td><?= $address->address_phone_number ?></td>
                        </tr>
                    </table>
                <?php endforeach; ?>
            <?php else : ?>
                No address found
            <?php endif ?>
        </td>
    </tr>

    <tr>
        <th>Reward Point</th>
        <td><?= $user->reward_point ?></td>
    </tr>
    <tr>
        <th>Role</th>
        <td><?= $user->role ?></td>
    </tr>
</table>

<button class="button" data-get="user_list.php">Back</button>

<?php
include '../../_foot.php';
