<?php
require '../../_base.php';

auth('Admin');

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
    $id                   = req('id');
    $email                = req('email');
    $password             = req('password');
    $name                 = req('name');
    $photo                = get_file('photo');
    $phone_number         = req('phone_number');
    $role                 = req('role');
    $recipient_name       = req('recipient_name');
    $street_address       = req('street_address');
    $city                 = req('city');
    $state                = req('state');
    $postal_code          = req('postal_code');
    $country              = req('country');
    $address_phone_number = req('address_phone_number');
    
    // Validate: email
    if (!$email) {
        $_err['email'] = 'Required';
    }
    else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_unique($email, 'user', 'email')) {
        $_err['email'] = 'Email Address already exists';
    }


    // Validate password
    if (!$password) {
        $_err['password'] = 'Required';
    } else if (strlen($password) < 8 || strlen($password) > 100) {
        $_err['password'] = 'Password length between 8-100';
    } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $_err['password'] = 'Password must be at least 8 characters long and include one uppercase letter, one lowercase letter, one digit, and one special character.';
    }

    // Validate name
    if ($name == '') {
        $_err['name'] = 'Required';
    } else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum length 100';
    }

    // Validate photo
    if ($photo) {
        if (!str_starts_with($photo->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        } else if ($photo->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    } else {
        $photo_name = 'default_avatar.png';
    }

    // Validate phone_number
    if ($phone_number == '') {
        $_err['phone_number'] = 'Required';
    } else if (!preg_match('/^0\d{2}-\d{7,8}$/', $phone_number)) {
        $_err['phone_number'] = 'Invalid phone number';
    }

    // Validate shipping address fields
    if ($recipient_name != '' && strlen($recipient_name) > 255) {
        $_err['recipient_name'] = 'Maximum length 255';
    }

    if ($street_address == '') {
        $_err['street_address'] = 'Required';
    } else if (strlen($street_address) > 255) {
        $_err['street_address'] = 'Maximum length 255';
    }

    if ($city == '') {
        $_err['city'] = 'Required';
    } else if (strlen($city) > 30) {
        $_err['city'] = 'Maximum length 30';
    }

    if ($state == '') {
        $_err['state'] = 'Required';
    } else if (strlen($state) > 30) {
        $_err['state'] = 'Maximum length 30';
    }

    if ($postal_code == '') {
        $_err['postal_code'] = 'Required';
    } else if (strlen($postal_code) > 5) {
        $_err['postal_code'] = 'Maximum length 5';
    }

    if ($country == '') {
        $_err['country'] = 'Required';
    } else if (strlen($country) > 100) {
        $_err['country'] = 'Maximum length 100';
    }

    // Validate address_phone_number
    if ($address_phone_number != '' && !preg_match('/^0\d{2}-\d{7,8}$/', $address_phone_number)) {
        $_err['address_phone_number'] = 'Invalid address phone number';
    }

    // Validate role
    if ($role == '') {
        $_err['role'] = 'Required';
    } else if (!array_key_exists($role, $_role)) {
        $_err['role'] = 'Invalid value';
    }

    // Output
    if (!$_err) {
        if (isset($photo) && $photo->name != 'default_avatar.png') {
            $photo_name = save_photo($photo, "/images/photo");
        }

        // Insert user
        $stm = $_db->prepare('INSERT INTO user (id, email, password, name, photo, phone_number, role) VALUES (?, ?, SHA1(?), ?, ?, ?, ?)');
        $stm->execute([$id, $email, $password, $name, $photo_name, $phone_number, $role]);

        // Use recipient name if provided, otherwise use user's name
        $recipient_name_value = $recipient_name ?: $name;
        
        // Use address phone number if provided, otherwise use user's phone number
        $shipping_phone_number = $address_phone_number ?: $phone_number;

        // Insert shipping address
        $stm = $_db->prepare('INSERT INTO shipping_address (user_id, recipient_name, street_address, city, state, postal_code, country, address_phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stm->execute([$id, $recipient_name_value, $street_address, $city, $state, $postal_code, $country, $shipping_phone_number]);

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
        <img src="/images/photo/<?= $photo ?? 'default_avatar.png' ?>">
    </label>
    <?= err('photo') ?>

    <label for="phone_number">Phone Number</label>
    <?= html_text('phone_number') ?>
    <?= err('phone_number') ?>

    <label for="role">Role</label>
    <?= html_select('role', $_role) ?>
    <?= err('role') ?>

    <h3>Shipping Address</h3>

    <label for="recipient_name">Recipient Name (Optional)</label>
    <?= html_text('recipient_name') ?>
    <?= err('recipient_name') ?>

    <label for="street_address">Street Address</label>
    <?= html_text('street_address') ?>
    <?= err('street_address') ?>

    <label for="city">City</label>
    <?= html_text('city') ?>
    <?= err('city') ?>

    <label for="state">State</label>
    <?= html_text('state') ?>
    <?= err('state') ?>

    <label for="postal_code">Postal Code</label>
    <?= html_text('postal_code') ?>
    <?= err('postal_code') ?>

    <label for="country">Country</label>
    <?= html_text('country') ?>
    <?= err('country') ?>

    <label for="address_phone_number">Address Phone Number (Optional)</label>
    <?= html_text('address_phone_number') ?>
    <?= err('address_phone_number') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="user_list.php">Back</button>

<?php
include '../../_foot.php';