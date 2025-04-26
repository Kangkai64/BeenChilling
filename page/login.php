<?php
require '../_base.php';
if (is_logged_in()) {
    if ($_SESSION['role'] == 'Admin') {
        redirect('/page/admin/product_list.php');
    } else {
        redirect('/index.php');
    }
    exit;
}

$email = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : '';
if (is_post()) {
    $email = isset($_POST['email']) ? $_POST['email'] : $email;
    $password = req('password');
    $ip       = getIpAddr();
    $login_time = time() - 30;

    // Count recent failed login attempts for this IP in the last 30 seconds
    $stmt = $_db->prepare("SELECT COUNT(*) AS total_count FROM ip_details WHERE ip = ? AND login_time > ?");
    $stmt->execute([$ip, $login_time]);
    $res = $stmt->fetch();
    $count = $res->total_count;

    // CAPTCHA verification
    $captcha = $_POST['g-recaptcha-response'];
    if (!$captcha) {
        $_err['captcha'] = 'Please complete the CAPTCHA.';
    } else {
        $secret = '6LfpJCIrAAAAABH4hKoKWnXNSC8euh1Q8__8kCJQ'; //  reCAPTCHA secret key
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");
        $captcha_response = json_decode($verify);
        if (!$captcha_response->success) {
            $_err['captcha'] = 'CAPTCHA verification failed. Please try again.';
        }
    }

    if ($count >= 3) {
        $_err['password'] = "Too many failed attempts. Please try again after 30 seconds.";
    } else {
        // Validate: email
        if ($email == '') {
            $_err['email'] = 'Required';
        } else if (!is_email($email)) {
            $_err['email'] = 'Invalid email';
        }

        // Validate: password
        if ($password == '') {
            $_err['password'] = 'Required';
        }

        // Attempt login
        if (!$_err) {
            $stm = $_db->prepare('SELECT * FROM user WHERE email = ? AND password = SHA1(?)');
            $stm->execute([$email, $password]);
            $u = $stm->fetch();

            if ($u) {
                $_db->prepare("DELETE FROM ip_details WHERE ip = ?")->execute([$ip]);

                if ($u->status == 0) {
                    $_err['email'] = 'This account has been deactivated. Please contact administrator.';
                } elseif ($u->status == 1) {
                    $_err['email'] = 'Please verify your email first.';
                } else {
                    if (isset($_POST['remember'])) {
                        setcookie('remember_email', $email, time() + (86400 * 30), '/'); // 30 days
                    } else {
                        setcookie('remember_email', '', time() - 3600, '/'); // remove cookie
                    }

                    $_SESSION['role'] = $u->role;

                    temp('info', 'Login successfully');
                    login($u, $u->role == 'Admin' ? 'admin/product_list.php' : '/index.php');
                }
            } else {
                $_err['password'] = 'Invalid Email or Password';
                $_db->prepare("INSERT INTO ip_details (ip, login_time) VALUES (?, ?)")->execute([$ip, time()]);

                $count++;
                if ($count >= 3) {
                    $_err['password'] = "Too many failed attempts. Please try again after 30 seconds.";
                }
            }
        }
    }
}

$_title = 'BeenChilling';
include '../_head.php';
?>

<form method="post" class="form" data-title="Login">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100" value="' . htmlspecialchars($email) . '"') ?>
    <?= err('email') ?>
    
    <label for="password">Password</label>
    <?= html_password('password', 'maxlength="100"') ?>
    <?= err('password') ?>

    <!-- Google reCAPTCHA -->
    <div class="g-recaptcha" data-sitekey="6LfpJCIrAAAAAItDzjXMnBIu4s-QVpNd5R2V3U6H"></div> <!--site key -->
    <?= isset($_err['captcha']) ? '<p style="color:red">'.$_err['captcha'].'</p>' : '' ?>
    
    <label>
        <input type="checkbox" name="remember" value="1" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>> Remember Email
    </label>


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

<!-- Load reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php include '../_foot.php'; ?>
