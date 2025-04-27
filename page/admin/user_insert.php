<?php
require '../../_base.php';

auth('Admin');

$_title = 'BeenChilling';
include '../../_head.php';

// Initialize shipping_addresses array
$shipping_addresses = [];

if (is_get()) {
    // Check if add_more button was clicked in GET request
    $add_more = isset($_GET['add_more']) ? $_GET['add_more'] : false;
    
    // If add_more is true, initialize with one empty shipping address
    if ($add_more) {
        $new_index = count($shipping_addresses);
        $address_name = 'Address ' . ($new_index + 1);
        
        // Add a new empty shipping address to the array
        $new_address = new stdClass();
        $new_address->address_name = $address_name;
        $new_address->recipient_name = '';
        $new_address->street_address = '';
        $new_address->city = '';
        $new_address->state = '';
        $new_address->postal_code = '';
        $new_address->country = 'Malaysia';
        $new_address->address_phone_number = '';
        
        $shipping_addresses[] = $new_address;

        // Set values in GLOBALS for form redisplay
        foreach ($shipping_addresses as $index => $address) {
            foreach ($address as $key => $value) {
                $GLOBALS["shipping_addresses[$index][$key]"] = $value;
            }
        }

        // Preserve all form fields
        $GLOBALS['name'] = $name;
        $GLOBALS['email'] = $email;
        $GLOBALS['phone_number'] = $phone_number;
        $GLOBALS['role'] = $role;

        // No validation needed when just adding a new address field
        $_err = true; // Force redisplay of form
    }
}

// Set default photo value
$current_photo = 'default_avatar.png';

if (is_post()) {
    $name = req('name');
    $photo = get_file('photo');
    $email = req('email');
    $phone_number = req('phone_number');
    $password = req('password');
    $confirm_password = req('confirm');
    $role = req('role');
    
    // Check if add_more button was clicked
    $add_more = req('add_more');
    
    if ($add_more) {
        // Get existing shipping addresses from the form
        $shipping_addresses = req('shipping_addresses', []);
        
        // Add a new empty address
        $new_index = count($shipping_addresses);
        $address_name = 'Address ' . ($new_index + 1);
        
        $shipping_addresses[$new_index] = [
            'address_name' => $address_name,
            'recipient_name' => '',
            'street_address' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => 'Malaysia',
            'address_phone_number' => ''
        ];
        
        // Set values in GLOBALS for form redisplay
        foreach ($shipping_addresses as $index => $address) {
            foreach ($address as $key => $value) {
                $GLOBALS["shipping_addresses[$index][$key]"] = $value;
            }
        }
        
        // Preserve other form data in GLOBALS
        $GLOBALS['name'] = $name;
        $GLOBALS['email'] = $email;
        $GLOBALS['phone_number'] = $phone_number;
        $GLOBALS['password'] = $password;
        $GLOBALS['confirm'] = $confirm_password;
        $GLOBALS['role'] = $role;
        
        // Handle photo for redisplay - store the filename
        if ($photo && !$photo->error) {
            // Temporarily save the photo
            $temp_photo = save_photo($photo, "../../images/photo");
            $GLOBALS['photo'] = $temp_photo;
            $current_photo = $temp_photo;
        } else {
            // Use hidden field value if provided
            $previous_photo = req('previous_photo');
            if ($previous_photo) {
                $GLOBALS['photo'] = $previous_photo;
                $current_photo = $previous_photo;
            }
        }
        
        // Force redisplay of form
        $_err = true;
    } else {
        // Validate name
        if ($name == '') {
            $_err['name'] = 'Required';
        } else if (strlen($name) > 100) {
            $_err['name'] = 'Maximum length 100';
        }

        // Validate photo
        if ($photo && !$photo->error) {
            if (!str_starts_with($photo->type, 'image/')) {
                $_err['photo'] = 'Must be image';
            } else if ($photo->size > 1 * 1024 * 1024) {
                $_err['photo'] = 'Maximum 1MB';
            }
        }

        // Validate email
        if ($email == '') {
            $_err['email'] = 'Required';
        } else if (!is_email($email)) {
            $_err['email'] = 'Invalid email format';
        } else {
            // Check if email already exists
            $stm = $_db->prepare('SELECT COUNT(*) FROM user WHERE email = ?');
            $stm->execute([$email]);
            if ($stm->fetchColumn() > 0) {
                $_err['email'] = 'Email already exists';
            }
        }

        // Validate phone_number
        if ($phone_number == '') {
            $_err['phone_number'] = 'Required';
        } else if (!is_phone_number($phone_number)) {
            $_err['phone_number'] = 'Invalid phone number';
        }

        // Validate password
        if ($password == '') {
            $_err['password'] = 'Required';
        } else if (strlen($password) < 8) {
            $_err['password'] = 'Minimum length 8';
        } else if (!is_password($password)) {
            $_err['password'] = 'Password must be at least 8 characters long and include one uppercase letter, one lowercase letter, one digit, and one special character.';
        }

        // Validate confirm_password
        if ($confirm_password == '') {
            $_err['confirm'] = 'Required';
        } else if ($confirm_password != $password) {
            $_err['confirm'] = 'Password does not match';
        }

        // Validate shipping addresses
        $shipping_addresses = req('shipping_addresses', []);
        foreach ($shipping_addresses as $index => $shipping_address) {
            if ($shipping_address['address_name'] != '' && strlen($shipping_address['address_name']) > 255) {
                $_err['shipping_addresses'][$index]['address_name'] = 'Maximum length 255';
            }
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
            if ($shipping_address['address_phone_number'] != '' && !is_phone_number($shipping_address['address_phone_number'])) {
                $_err['shipping_addresses'][$index]['address_phone_number'] = 'Invalid address phone number';
            }
        }

        // Validate role
        if ($role == '') {
            $_err['role'] = 'Required';
        } else if (!array_key_exists($role, $role_options)) {
            $_err['role'] = 'Invalid value';
        }

        // Output
        if (!$_err) {
            $role_value = $role_options[$role];
            if ($role_value == 'Member') {
                $status_value = 1;
            } else {
                $status_value = 2;
            }
            
            // Handle photo upload
            if ($photo && !$photo->error) {
                $photo_name = save_photo($photo, "../../images/photo");
            } else {
                $previous_photo = req('previous_photo');
                $photo_name = $previous_photo ? $previous_photo : 'default_avatar.png';
            }

            // Insert user
            $stm = $_db->prepare('INSERT INTO user (name, email, phone_number, password, role, photo, status) VALUES (?, ?, ?, SHA1(?), ?, ?, ?)');
            $stm->execute([$name, $email, $phone_number, $password, $role_value, $photo_name, $status_value]);
            
            $user_id = $_db->lastInsertId();

            // Insert shipping addresses
            if (!empty($shipping_addresses)) {
                $stm = $_db->prepare('INSERT INTO shipping_address (user_id, address_name, recipient_name, street_address, city, state, postal_code, country, address_phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                foreach ($shipping_addresses as $shipping_address) {
                    // Use recipient name if provided, otherwise use user's name
                    $recipient_name_value = !empty($shipping_address['recipient_name']) ? $shipping_address['recipient_name'] : $name;
                    
                    // Use address phone number if provided, otherwise use user's phone number
                    $address_phone_value = !empty($shipping_address['address_phone_number']) ? $shipping_address['address_phone_number'] : $phone_number;
                    
                    $stm->execute([
                        $user_id, 
                        $shipping_address['address_name'], 
                        $recipient_name_value, 
                        $shipping_address['street_address'], 
                        $shipping_address['city'], 
                        $shipping_address['state'], 
                        $shipping_address['postal_code'], 
                        $shipping_address['country'], 
                        $address_phone_value
                    ]);
                }
            }

            temp('info', 'User inserted successfully');
            redirect('user_list.php');
        } else {
            // Preserve form data in GLOBALS for redisplay
            $GLOBALS['name'] = $name;
            $GLOBALS['email'] = $email;
            $GLOBALS['phone_number'] = $phone_number;
            $GLOBALS['password'] = $password;
            $GLOBALS['confirm'] = $confirm_password;
            $GLOBALS['role'] = $role;
            
            // Handle photo for redisplay - store the filename
            if ($photo && !$photo->error) {
                // Temporarily save the photo
                $temp_photo = save_photo($photo, "../../images/photo");
                $GLOBALS['photo'] = $temp_photo;
                $current_photo = $temp_photo;
            } else {
                // Use hidden field value if provided
                $previous_photo = req('previous_photo');
                if ($previous_photo) {
                    $GLOBALS['photo'] = $previous_photo;
                    $current_photo = $previous_photo;
                }
            }
            
            // Preserve shipping addresses
            foreach ($shipping_addresses as $index => $address) {
                foreach ($address as $key => $value) {
                    $GLOBALS["shipping_addresses[$index][$key]"] = $value;
                }
            }
        }
    }
}
?>

<form method="post" class="form" data-title="Insert User" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Name</label>
        <?= html_text('name', 'maxlength="100"') ?>
        <?= err('name') ?>
    </div>

    <div class="form-group">
        <label for="photo">Photo</label>
        <label class="upload" tabindex="0">
            <?= html_file('photo', 'image/*', 'hidden') ?>
            <!-- Add hidden field to store the current photo name -->
            <input type="hidden" name="previous_photo" value="<?= $GLOBALS['photo'] ?? $current_photo ?>">
            <img src="/images/photo/<?= $GLOBALS['photo'] ?? $current_photo ?>">
        </label>
        <?= err('photo') ?>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <?= html_text('email') ?>
        <?= err('email') ?>
    </div>

    <div class="form-group">
        <label for="phone_number">Phone Number</label>
        <?= html_text('phone_number') ?>
        <?= err('phone_number') ?>
    </div>

    <div class="form-group">
        <label for="role">Role</label>
        <?= html_select('role', $role_options, null, $role) ?>
        <?= err('role') ?>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <?= html_password('password') ?>
        <?= err('password') ?>
    </div>

    <div class="form-group">
        <label for="confirm">Confirm Password</label>
        <?= html_password('confirm') ?>
        <?= err('confirm') ?>
    </div>

    <?php
        if (isset($shipping_addresses) && !empty($shipping_addresses)) {
            echo "<h3 class='section-separator'>Shipping Addresses</h3>";
            echo "<section id='shipping_addresses_container'>";
            foreach ($shipping_addresses as $index => $shipping_address) {
                ?>
                <h3>Address <?= $index + 1 ?></h3>
                <section class="shipping_address">
                    <div class="form-group">
                        <label>Address Name</label>
                        <?= html_text("shipping_addresses[$index][address_name]", 'maxlength="255"') ?>
                        <?= err("shipping_addresses[$index][address_name]") ?>
                    </div>

                    <div class="form-group">
                        <label>Recipient Name</label>
                        <?= html_text("shipping_addresses[$index][recipient_name]", 'maxlength="255" placeholder="Leave empty to use user\'s name"') ?>
                        <?= err("shipping_addresses[$index][recipient_name]") ?>
                    </div>

                    <div class="form-group">
                        <label>Street Address</label>
                        <?= html_text("shipping_addresses[$index][street_address]", 'maxlength="255"') ?>
                        <?= err("shipping_addresses[$index][street_address]") ?>
                    </div>

                    <div class="form-group">
                        <label>City</label>
                        <?= html_text("shipping_addresses[$index][city]", 'maxlength="30"') ?>
                        <?= err("shipping_addresses[$index][city]") ?>
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <?= html_text("shipping_addresses[$index][state]", 'maxlength="30"') ?>
                        <?= err("shipping_addresses[$index][state]") ?>
                    </div>

                    <div class="form-group">
                        <label>Postal Code</label>
                        <?= html_text("shipping_addresses[$index][postal_code]", 'maxlength="5"') ?>
                        <?= err("shipping_addresses[$index][postal_code]") ?>
                    </div>

                    <div class="form-group">
                        <label>Country</label>
                        <?= html_text("shipping_addresses[$index][country]", 'maxlength="100"') ?>
                        <?= err("shipping_addresses[$index][country]") ?>
                    </div>

                    <div class="form-group">
                        <label>Address Phone Number</label>
                        <?= html_text("shipping_addresses[$index][address_phone_number]", 'maxlength="20" placeholder="Leave empty to use user\'s phone number"') ?>
                        <?= err("shipping_addresses[$index][address_phone_number]") ?>
                    </div>
                </section>
                <?php
            }
        }
    ?>
    </section>

    <section>
        <button type="submit" name="add_more" value="1">Add Shipping Address</button>
        <button type="submit">Submit</button>
        <button type="reset" >Reset</button>
    </section>
</form>

<button class="button" data-get="user_list.php">Back</button>

<?php
include '../../_foot.php';