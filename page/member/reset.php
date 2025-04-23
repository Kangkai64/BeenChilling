<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

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
        $_err['email'] = 'Not exists';
    }

    // Send reset token (if valid)
    if (!$_err) {
        // Select user
        $stm = $_db->prepare('SELECT * FROM user WHERE email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch();

        // Generate token id
        $id = sha1(uniqid().rand());

        // Delete old and insert new token
        $stm = $_db->prepare('
            DELETE FROM token WHERE user_id = ? AND type = "reset";

            INSERT INTO token (id, expire, user_id, type)
            VALUES (?, ADDTIME(NOW(), "00:30"), ?, "reset")
        ');
        $stm->execute([$u->id, $id, $u->id]);

        // Generate token url
        $url = base("page/member/password_token.php?id=$id");

        // Redirect current url
        $currentUrl = base("page/member/reset.php");

        // Send email
        $m = get_mail();
        $m->addAddress($email, $u->name);
        $m->addEmbeddedImage("../../images/logo.png", 'logo');
        $m->isHTML(true);
        $m->Subject = 'Reset Password';
        $m->Body = "
        <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Helvetica, Arial, sans-serif; 
                    max-width: 600px; margin: 0 auto; padding: 20px; text-align: center; border: 1px solid #ddd; 
                    border-radius: 6px;'>
          <img src='cid:logo' alt='Logo' width='200' style='margin-bottom: 20px;'>
          <h2 style='margin: 0 0 20px;'>Reset your password</h2>
          
          <div style='background-color: #f9f9f9; padding: 20px; border-radius: 6px;'>
            <h3 style='margin-top: 0;'>Password Reset</h3>
            <p>We heard that you lost your password. Sorry about that!</p>
            <p>But don’t worry! You can use the button below to reset it:</p>
      
            <a href='$url' style='display: inline-block; padding: 10px 20px; margin-top: 15px;
               background-color: #d34f73; color: white; text-decoration: none; border-radius: 5px;
               font-weight: bold;'>Reset your password</a>
      
            <p style='margin-top: 20px;'>If you don’t use this link within 5 minutes, it will expire.</p>
            <p style='margin-top: 10px;'><a href='$currentUrl' style='color: #0366d6;'>Click here to get a new password reset link</a></p>
          </div>
      
          <p style='margin-top: 20px;'>Thanks,<br>BeenChilling</p>
      
          <p style='font-size: 12px; color: #888; margin-top: 40px;'>
            You’re receiving this email because a password reset was requested for your account.
          </p>
        </div>
      ";
      
        $m->send();
        temp('info', 'Email sent');
        redirect('../login.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Reset Password';
include '../../_head.php';
?>

<form method="post" data-title="Reset Your Password" class="form">

    <p>Enter your user account's verified email address and we will send you a password reset link.</p>
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