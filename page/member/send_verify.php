<?php
include '../../_base.php';

$user_id = req('user_id');

// Fetch user data
$stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
$stm->execute([$user_id]);
$u = $stm->fetch(PDO::FETCH_OBJ);

if ($u && $u->status == 1) {
    $token_id = sha1(uniqid() . rand());
    $stm = $_db->prepare('
        DELETE FROM token WHERE user_id = ? AND type = "verify";
        INSERT INTO token (id, expire, user_id, type)
        VALUES (?, ADDTIME(NOW(), "00:30"), ?, "verify")
    ');
    $stm->execute([$user_id, $token_id, $user_id]);

    // Fetch the email and name from the user record
    $email = $u->email;
    $name = $u->name;

    $url = base("page/member/verify.php?id=$token_id");

    $m = get_mail();
    $m->addAddress($email, $name);
    $m->addEmbeddedImage("../../images/logo.png", 'logo');
    $m->isHTML(true);
    $m->Subject = 'Verify Your Email';
    $m->Body = "
        <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Helvetica, Arial, sans-serif; 
                    max-width: 600px; margin: 0 auto; padding: 20px; text-align: center; border: 1px solid #ddd; 
                    border-radius: 6px;'>
            <img src='cid:logo' alt='Logo' width='200' style='margin-bottom: 20px;'>
            <h2 style='margin: 0 0 20px;'>Verify Your Email</h2>
            
            <div style='background-color: #f9f9f9; padding: 20px; border-radius: 6px;'>
                <h3 style='margin-top: 0;'>Email Verification</h3>
                <p>Hi $name,</p>
                <p>Thank you for registering with us! Please verify your email by clicking the button below:</p>
            
                <a href='$url' style='display: inline-block; padding: 10px 20px; margin-top: 15px;
                    background-color: #d34f73; color: white; text-decoration: none; border-radius: 5px;
                    font-weight: bold;'>Verify Email</a>
            
                <p style='margin-top: 20px;'>This link will expire in 30 minutes.</p>
                <p>If you did not register, please ignore this email.</p>
            </div>
            
            <p style='margin-top: 20px;'>Thanks,<br>BeenChilling</p>
            
            <p style='font-size: 12px; color: #888; margin-top: 40px;'>
                You’re receiving this email because a registration request was made for your account.
            </p>
        </div>
    ";
    $m->send();
    // Show success message
    temp('info', 'Verification email sent. Please check your inbox.');
    if (!$_user) {
        redirect('../login.php');
    } else {
        redirect('/page/profile.php');
    }
}
else{
    temp('info', 'Invalid Access!');
    redirect('/');
}




