<?php
include '../_base.php';

// ----------------------------------------------------------------------------

// Authenticated users
auth();

if (is_post()) {
    $password     = req('password');
    $new_password = req('new_password');
    $confirm      = req('confirm');

    // Validate: password
    if (!$password) {
        $_err['password'] = 'Required';
    } else if (strlen($password) < 8 || strlen($password) > 100) {
        $_err['password'] = 'Password length between 8-100';
    } 
    else {
        $stm = $_db->prepare('
            SELECT COUNT(*) FROM user
            WHERE id = ? AND password = SHA1(?)
        ');
        $stm->execute([$_user->id, $password]);
        
        if ($stm->fetchColumn() == 0) {
            $_err['password'] = 'Not matched';
        }
    }

    // Validate: new_password
    if (!$new_password) {
        $_err['new_password'] = 'Required';
    } else if (strlen($new_password) < 8 || strlen($new_password) > 100) {
        $_err['new_password'] = 'Password length between 8-100';
    } else if (!is_password($new_password)) {
        $_err['new_password'] = 'Password must be at least 8 characters long and include one uppercase letter, one lowercase letter, one digit, and one special character.';
    }

    // Validate: confirm
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    }
    else if ($confirm != $new_password) {
        $_err['confirm'] = 'Not matched';
    }

    // DB operation
    if (!$_err) {

        // Update user (password)
        $stm = $_db->prepare('
            UPDATE user
            SET password = SHA1(?)
            WHERE id = ?
        ');
        $stm->execute([$new_password, $_user->id]);

        temp('info', 'Record updated');
        redirect('/');
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Password';
include '../_head.php';
?>

<form method="post" class="form">
    <div class="form-group">
        <label for="password">Password</label>
        <?= html_password('password', 'maxlength="100"') ?>
        <?= err('password') ?>
    </div>

    <div class="form-group">
        <label for="new_password">New Password</label>
        <?= html_password('new_password', 'maxlength="100"') ?>
        <?= err('new_password') ?>
    </div>

    <div class="form-group">
        <label for="confirm">Confirm Password</label>
        <?= html_password('confirm', 'maxlength="100"') ?>
        <?= err('confirm') ?>
    </div>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';