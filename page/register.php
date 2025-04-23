<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $name     = req('name');

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

    // Validate: password
    if (!$password) {
        $_err['password'] = 'Required';
    } else if (strlen($password) < 8 || strlen($password) > 100) {
        $_err['password'] = 'Password length between 8-100';
    } else if (!is_password($password)) {
        $_err['password'] = 'Password must be at least 8 characters long and include one uppercase letter, one lowercase letter, one digit, and one special character.';
    }

    // Validate: confirm
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    }
    else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    // Validate: name
    if (!$name) {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }


    // DB operation
    if (!$_err) {
        // 1. Insert user with status = 1 (unverified)
        $stm = $_db->prepare('
        INSERT INTO user (email, password, name, status, role)
        VALUES (?, SHA1(?), ?, 1, "Member")
        ');
        $stm->execute([$email, $password, $name]);
        
        // Get user ID
        $user_id = $_db->lastInsertId();

        // Redirect to email sender
        redirect("member/send_verify.php?user_id=$user_id");
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Register Member';
include '../_head.php';
?>

<form method="post" class="form" data-title="Register" enctype="multipart/form-data">
    <div class="form-group">
        <label for="email">Email</label>
        <?= html_text('email', 'maxlength="100"') ?>
        <?= err('email') ?>
    </div>
    
    <div class="form-group">
        <label for="password">Password</label>
        <?= html_password('password', 'maxlength="100"') ?>
        <?= err('password') ?>
    </div>

    <div class="form-group">
        <label for="confirm">Confirm Password</label>
        <?= html_password('confirm', 'maxlength="100"') ?>
        <?= err('confirm') ?>
    </div>

    <div class="form-group">
        <label for="name">Name</label>
        <?= html_text('name', 'maxlength="100"') ?>
        <?= err('name') ?>
    </div>

    <div class="recover">
        <a href="login.php">Already have account?</a>
    </div>

    <section>
        <button class="register-button">Register</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';