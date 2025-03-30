<?php
require '../../_base.php';

$_title = 'BeenChilling';
include '../../_head.php';

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('/');
    }

    extract((array)$s);
}

if (is_post()) {
    $id         = req('id');
    $name       = req('name');
    $email      = req('email');
    $phone_number = req('phone_number');
    $address    = req('address');
    $role       = req('role');
    
    // Validate name
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum length 100';
    }

    // Validate email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = 'Invalid email format';
    }

    // Validate phone_number
    if ($phone_number == '') {
        $_err['phone_number'] = 'Required';
    }
    else if (!preg_match('/^0\d{2}-\d{7,8}$/',$phone_number)) {
        $_err['phone_number'] = 'Invalid phone number';
    }

    // Validate address
    if ($address == '') {
        $_err['address'] = 'Required';
    }

    // Validate role
    if ($role == '') {
        $_err['role'] = 'Required';
    }
    else if (!array_key_exists($role, $_roles)) {
        $_err['role'] = 'Invalid value';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE user
                              SET name = ?, email = ?, phone_number = ?, address = ?, role = ?
                              WHERE id = ?');
        $stm->execute([$name, $email, $phone_number, $address, $role, $id]);

        temp('info', 'Record updated');
        redirect('/');
    }
}

?>

<form method="post" class="form">
    <label for="id">Id</label>
    <b><?= $id ?></b>
    <?= err('id') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="email">Email</label>
    <?= html_text('email') ?>
    <?= err('email') ?>

    <label for="phone_number">Phone Number</label>
    <?= html_text('phone_number') ?>
    <?= err('phone_number') ?>

    <label for="address">Address</label>
    <?= html_text('address') ?>
    <?= err('address') ?>

    <label for="role">Role</label>
    <?= html_select('role', $_role) ?>
    <?= err('role') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="memberlist.php">Back</button>

<?php
include '../../_foot.php';