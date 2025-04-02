<?php
require '../../_base.php';

auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('user_list.php');
    }

    $stm = $_db->prepare('SELECT * FROM shipping_address WHERE user_id = ?');
    $stm->execute([$id]);
    $shipping_addresses = $stm->fetchAll(PDO::FETCH_OBJ);

    extract((array)$s);
}

if (is_post()) {
    $id         = req('id');
    $name       = req('name');
    $photo      = get_file('photo');
    $email      = req('email');
    $phone_number = req('phone_number');
    $role       = req('role');
    $add_more   = req('add_more');
    
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

    // Validate email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = 'Invalid email format';
    }

    // Validate phone_number
    if ($phone_number == '') {
        $_err['phone_number'] = 'Required';
    } else if (!preg_match('/^0\d{2}-\d{7,8}$/', $phone_number)) {
        $_err['phone_number'] = 'Invalid phone number';
    }

    // Validate shipping addresses
    $shipping_addresses = req('shipping_addresses', []);
    foreach ($shipping_addresses as $index => $shipping_address) {
        if ($shipping_address['recipient_name'] != '' && strlen($shipping_address['recipient_name']) > 255) {
            $_err['shipping_addresses'][$index]['recipient_name'] = 'Maximum length 255';
        }
        if ($shipping_address['street_address'] == '') {
            $_err['shipping_addresses'][$index]['street_address'] = 'Required';
        } else if (strlen($shipping_address['street_address']) > 255) {
            $_err['shipping_addresses'][$index]['street_address'] = 'Maximum length 255';
        }
        if ($shipping_address['city'] == '') {
            $_err['shipping_addresses'][$index]['city'] = 'Required';
        } else if (strlen($shipping_address['city']) > 30) {
            $_err['shipping_addresses'][$index]['city'] = 'Maximum length 30';
        }
        if ($shipping_address['state'] == '') {
            $_err['shipping_addresses'][$index]['state'] = 'Required';
        } else if (strlen($shipping_address['state']) > 30) {
            $_err['shipping_addresses'][$index]['state'] = 'Maximum length 30';
        }
        if ($shipping_address['postal_code'] == '') {
            $_err['shipping_addresses'][$index]['postal_code'] = 'Required';
        } else if (strlen($shipping_address['postal_code']) > 5) {
            $_err['shipping_addresses'][$index]['postal_code'] = 'Maximum length 5';
        }
        if ($shipping_address['country'] == '') {
            $_err['shipping_addresses'][$index]['country'] = 'Required';
        } else if (strlen($shipping_address['country']) > 100) {
            $_err['shipping_addresses'][$index]['country'] = 'Maximum length 100';
        }
        if ($shipping_address['address_phone_number'] != '' && !preg_match('/^0\d{2}-\d{7,8}$/', $shipping_address['address_phone_number'])) {
            $_err['shipping_addresses'][$index]['address_phone_number'] = 'Invalid address phone number';
        }
    }

    // Validate role
    if ($role == '') {
        $_err['role'] = 'Required';
    } else if (!array_key_exists($role, $_role)) {
        $_err['role'] = 'Invalid value';
    }

    // Check if a file has been uploaded via Dropzone
    if (is_post() && !empty($_FILES)) {
        $uploaded_file = get_file('file'); // 'file' is the default name used by Dropzone

        if ($uploaded_file) {
            $folder = '/images/test'; // Specify the folder where photos will be saved
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            $saved_photo = save_photo($uploaded_file, $folder);

            // Respond with the saved photo path or any other information
            temp('info', 'File uploaded successfully');
        } else {
            temp('info', 'File upload failed');
        }
    }

    // Output
    if (!$_err && !$add_more) {
        if (isset($photo) && $photo->name != 'default_avatar.png') {
            $photo_name = save_photo($photo, "/images/photo");
        }

        // Update user
        $stm = $_db->prepare('UPDATE user
                              SET name = ?, email = ?, phone_number = ?, role = ?, photo = ?
                              WHERE id = ?');
        $stm->execute([$name, $email, $phone_number, $role, $photo_name, $id]);

        // Delete existing shipping addresses
        $stm = $_db->prepare('DELETE FROM shipping_address WHERE user_id = ?');
        $stm->execute([$id]);

        // Insert new shipping addresses
        $stm = $_db->prepare('INSERT INTO shipping_address (user_id, recipient_name, street_address, city, state, postal_code, country, address_phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($shipping_addresses as $shipping_address) {
            $stm->execute([$id, $shipping_address['recipient_name'], $shipping_address['street_address'], $shipping_address['city'], $shipping_address['state'], $shipping_address['postal_code'], $shipping_address['country'], $shipping_address['address_phone_number']]);
        }

        temp('info', 'Record updated');
        redirect('user_list.php');
    }

    if ($add_more) {
        array_push($shipping_addresses, [
            'recipient_name' => '',
            'street_address' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => '',
            'address_phone_number' => ''
        ]);
    }
}
?>

<form method="post" class="form" data-title="Update User" enctype="multipart/form-data">
    <label for="id">Id</label>
    <p><?= $id ?></p>
    <?= err('id') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="photo">Photo</label>
    <label class="upload dropzone-enabled" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/images/photo/<?= $photo ?? 'default_avatar.png' ?>">
    </label>
    <?= err('photo') ?>

    <label for="email">Email</label>
    <?= html_text('email') ?>
    <?= err('email') ?>

    <label for="phone_number">Phone Number</label>
    <?= html_text('phone_number') ?>
    <?= err('phone_number') ?>

    <label for="role">Role</label>
    <?= html_select('role', $_role) ?>
    <?= err('role') ?>

    <h3 class="section-separator">Shipping Addresses</h3>
    <section id="shipping_addresses_container">
    <?php
        if (isset($shipping_addresses)) {
            foreach ($shipping_addresses as $index => $shipping_address) {
                $shipping_address = (object) $shipping_address;
                ?>
                <h3>Address <?= $index + 1?></h3>
                <section class="shipping_address">
                    <label>Recipient Name</label>
                    <?= html_text("shipping_addresses[$index][recipient_name]", 'maxlength="255"', $shipping_address->recipient_name) ?>
                    <?= err("shipping_addresses[$index][recipient_name]") ?>

                    <label>Street Address</label>
                    <?= html_text("shipping_addresses[$index][street_address]", 'maxlength="255"', $shipping_address->street_address) ?>
                    <?= err("shipping_addresses[$index][street_address]") ?>

                    <label>City</label>
                    <?= html_text("shipping_addresses[$index][city]", 'maxlength="30"', $shipping_address->city) ?>
                    <?= err("shipping_addresses[$index][city]") ?>

                    <label>State</label>
                    <?= html_text("shipping_addresses[$index][state]", 'maxlength="30"', $shipping_address->state) ?>
                    <?= err("shipping_addresses[$index][state]") ?>

                    <label>Postal Code</label>
                    <?= html_text("shipping_addresses[$index][postal_code]", 'maxlength="5"', $shipping_address->postal_code) ?>
                    <?= err("shipping_addresses[$index][postal_code]") ?>

                    <label>Country</label>
                    <?= html_text("shipping_addresses[$index][country]", 'maxlength="100"', $shipping_address->country) ?>
                    <?= err("shipping_addresses[$index][country]") ?>

                    <label>Address Phone Number</label>
                    <?= html_text("shipping_addresses[$index][address_phone_number]", 'maxlength="20"', $shipping_address->address_phone_number) ?>
                    <?= err("shipping_addresses[$index][address_phone_number]") ?>
                </section>
                <?php
            }
        }
        ?>
    </section>

    <button type="submit" name="add_more" value="1" id="add_more_button">Add Shipping Address</button>
    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="user_list.php">Back</button>

<?php
include '../../_foot.php';