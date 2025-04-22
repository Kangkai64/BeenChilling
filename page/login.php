<?php
require '../_base.php';

if (is_post()) {
    $email    = req('email');
    $password = req('password');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    // Validate: password
    if ($password == '') {
        $_err['password'] = 'Required';
    }

    // Login user
    if (!$_err) {
        
        $stm = $_db->prepare('
            SELECT * FROM user
            WHERE email = ? AND password = SHA1(?)
        ');
        $stm->execute([$email, $password]);
        $u = $stm->fetch();

        if ($u) {
            // Check if account is active
            if ($u->status == 0) {
                $_err['email'] = 'This account has been deactivated. Please contact administrator.';
            } 
            else if($u->status == 1){
                $_err['email'] = 'Please verify your email first.';
            }
            else {
                temp('info', 'Login successfully');
                if($u->role == 'Admin'){
                    login($u, 'admin/productlist.php');
                }
                else{
                    login($u);
                }
            }
        }
        else {
            $_err['password'] = 'Invalid Email or Password';
        }
    }
}


$_title = 'BeenChilling';
include '../_head.php';
?>

<form method="post" class="form" data-title="Login">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>
    
    <label for="password">Password</label>
    <?= html_password('password', 'maxlength="100"') ?>
    <?= err('password') ?>
    
    <div class="recover">
        <a href="member/reset.php">Forgot Password?</a>
    </div>

    <div class="recover">
        <a href="member/verify_email.php">Verify Gmail</a>
    </div>
    
    <section>
        <button class="login-button">Login</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';