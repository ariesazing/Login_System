<?php
require_once 'DBconn.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $recaptchaResponse = $_POST['g-recaptcha-response']; // Get reCAPTCHA response

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } elseif (empty($recaptchaResponse)) {
        $error = 'Please complete the reCAPTCHA verification.';
    } else {
        // Verify reCAPTCHA
        $recaptchaSecret = "6LdXrgQrAAAAANQ4wtnWk6Xj0HIxO2ZpejC0kGbI"; // Your reCAPTCHA secret key
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
        $responseData = json_decode($verify);

        if (!$responseData->success) {
            $error = "reCAPTCHA verification failed. Please try again.";
        } else {
            // If reCAPTCHA is valid, check user credentials
            $stmt = $dbh->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['logged_in'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Load reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="bootstrap-icons-1.11.3/package/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="text-light" style="background-color: #0a0a0a;">

    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card p-4 bg-dark shadow-lg text-light border-secondary mx-auto" style="width: 100%; max-width: 440px;">
            <h3 class="mb-0 text-center"><i class="bi bi-people"></i> Login </h3>
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post" class="mt-3">
                    <div class="row m-2">
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                class="form-control"
                                placeholder="name@example.com"
                                required>
                        </div>
                    </div>
                    <div class="row m-2">
                        <div class="col-md-12 mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password"
                                    class="form-control"
                                    id="password"
                                    placeholder="••••••••"
                                    required>
                                <span class="input-group-text" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- reCAPTCHA v2 Checkbox -->
                    <div class="row m-2">
                        <div class="g-recaptcha" data-sitekey="6LdXrgQrAAAAANe-RL81IkGI6NAac7nDVx9OicSA"></div>
                    </div>

                    <div class="row m-2">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                            </button>
                        </div>
                    </div>

                    <div class="d-grid gap-2 ms-4">
                        <span>Don't have an account? <a href="register.php">Register</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="icon.js"></script>
    <script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.js"></script>
</body>

</html>