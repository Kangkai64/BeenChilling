<?php
include '../../_base.php';

if (is_post()) {
    $email = req('email');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_exists($email, 'user', 'email')) {
        $_err['email'] = 'Email not exists';
    }

    if (!$_err) {
        $stm = $_db->prepare('SELECT * FROM user WHERE email = ? AND status = 1');
        $stm->execute([$email]);
        $u = $stm->fetch();
        $user_id = $u->id;
        redirect("send_verify_token.php?user_id=$user_id");
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Verify Email';
include '../../_head.php';
?>

<form method="post" data-title="Verify Your Email" class="form">

    <p>Enter your user account's email address and we will send you a verification link.</p>
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../_foot.php';