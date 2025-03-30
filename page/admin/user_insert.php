<?php
require '../../_base.php';

$_title = 'BeenChilling';
include '../../_head.php';

if (is_get()) {
    // Retrieve the last inserted ID
    $stm = $_db->prepare('SELECT id FROM user ORDER BY id DESC LIMIT 1');
    $stm->execute();
    $last_id = $stm->fetchColumn();

    // Generate the new ID
    $new_id = ++$last_id;
    $GLOBALS['id'] = $new_id;

    if (!isset($_GET['id']) || (int)$_GET['id'] !== $new_id) {
        redirect("user_insert.php?id=$new_id");
    }
}

if (is_post()) {
    $id         = req('id');
    $email      = req('email');
    $password   = req('password');
    $name       = req('name');
    $photo      = get_file('photo');
    $phone_number = req('phone_number');
    $shipping_address = req('shipping_address');
    $role       = req('role');
    
    // Validate email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = 'Invalid email format';
    }

    // Validate password
    if ($password == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum length 100';
    }
    else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $_err['password'] = 'Password must be at least 8 characters long and include one uppercase letter, one lowercase letter, one digit, and one special character.';
    }

    // Validate name
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum length 100';
    }

    // Validate photo
    if ($photo) {
        if (!str_starts_with($photo->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
        else if ($photo->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }
    else{
        $photo_name = 'default_avatar.png';
    }


    // Validate phone_number
    if ($phone_number == '') {
        $_err['phone_number'] = 'Required';
    }
    else if (!preg_match('/^0\d{2}-\d{7,8}$/',$phone_number)) {
        $_err['phone_number'] = 'Invalid phone number';
    }

    // Validate shipping_address
    if ($shipping_address == '') {
        $_err['shipping_address'] = 'Required';
    }

    // Validate role
    if ($role == '') {
        $_err['role'] = 'Required';
    }
    else if (!array_key_exists($role, $_role)) {
        $_err['role'] = 'Invalid value';
    }

    // Output
    if (!$_err) {
        if (isset($photo) && $photo->name != 'default_avatar.png') {
            $photo_name = save_photo($photo, "../../images/photo");
        }

        $stm = $_db->prepare('INSERT INTO user (id, email, password, name, photo, phone_number, shipping_address, role) VALUES (?, ?, SHA1(?), ?, ?, ?, ?, ?)');
        $stm->execute([$id, $email, $password, $name, $photo_name, $phone_number, $shipping_address, $role]);

        temp('info', 'Record updated');
        redirect('user_list.php');
    }
}

?>

<form method="post" class="form" data-title="Insert User" enctype="multipart/form-data">
    <label for="id">Id</label>
    <?= html_text('id', 'maxlength="10"') ?>
    <?= err('id') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="email">Email</label>
    <?= html_text('email') ?>
    <?= err('email') ?>

    <label for="password">Password</label>
    <?= html_password('password', 'maxlength="100"') ?>
    <?= err('password') ?>

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/images/photo.jpg">
    </label>
    <?= err('photo') ?>

    <label for="phone_number">Phone Number</label>
    <?= html_text('phone_number') ?>
    <?= err('phone_number') ?>

    <label for="shipping_address">Shipping Address</label>
    <?= html_text('shipping_address') ?>
    <?= err('shipping_address') ?>

    <label for="role">Role</label>
    <?= html_select('role', $_role) ?>
    <?= err('role') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="user_list.php">Back</button>

<?php
include '../../_foot.php';