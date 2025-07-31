<?php
$DBhost = 'localhost';
$DBuser = 'root';
$DBpassword = '';
$DBname = 'login_systemdb';

try {
    $dbh = new PDO('mysql: host=' . $DBhost . ';dbname=' . $DBname, $DBuser, $DBpassword);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}

// DATA POSTING ---------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    if (empty($_POST["name"])) {
        $errors[] = "Name is required";
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    if (empty($_POST["email"])) {
        $errors[] = "Email is required";
    } else if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email Format";
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    $role = 'user'; //default role for user registration
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $existingEmail = $stmt->fetchColumn();


    if ($existingEmail > 0) {
        $errors[] = "Email already exists. Please use a different one.";
    }

    if (empty($_POST["password"])) {
        $errors[] = "Password is required";
    } else if ($_POST["cpassword"] !== $_POST["password"]) {
        $errors[] = "Password does not match!";
    } else {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    }

    // reCAPTCHA validation
    $recaptchaSecret = "6LdXrgQrAAAAANQ4wtnWk6Xj0HIxO2ZpejC0kGbI";
    $recaptchaResponse = $_POST["g-recaptcha-response"];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
    $responseData = json_decode($verify);

    if (!$responseData->success) {
        $errors[] = "reCAPTCHA verification failed. Please try again.";
    }

    // Error handling
    if (empty($errors)) {
        $sql = "INSERT INTO users (name, email, role, password) 
                VALUES (:name, :email, :role, :password)";

        $query = $dbh->prepare($sql);
        $query->execute([
            ':name' => $name,
            ':email' => $email,
            ':role' => $role,
            ':password' => $password
        ]);

        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            $_SESSION['message'] = 'User added successfully.';
            echo "<script>window.location.href='index.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again'); </script>";
            echo "<script>window.location.href='index.php'</script>";
        }
    } else {
        echo "<div class='alert alert-danger'>";
        echo "<h2>Errors: </h2><br>";
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link href="bootstrap-icons-1.11.3\package\font\bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Document</title>
</head>

<body>

    <div class="alert aler-danger"><? $error ?></div>
    <div class="container d-flex align-items-center justify-content-center vh-100 ">
        <div class="card p-4 bg-dark shadow-lg shadow-lg text-light border-secondary mx-auto" style="width: 100%; max-width: 390px;">
            <h2 class="text-center">Sign Up</h2>

            <form class="py-3" method="post" action="">

                <div class="row m-2">
                    <div class="col-md-12">
                        <label for="name">Name: </label>
                        <input class="form-control border-secondary" type="text" name="name" placeholder="Example Name" required>
                    </div>
                </div>
                <div class="row m-2">
                    <div class="col-md-12">
                        <label for="email">Email: </label>
                        <input class="form-control border-secondary" type="email" name="email" placeholder="name@example.com" required>
                    </div>
                </div>
                <div class="row m-2">
                    <label for="password">Password: </label>
                    <div class="col-md-12">
                        <div class="input-group">
                            <input class="form-control border-secondary" type="password" name="password" id="password" placeholder="••••••••" required>
                            <span class="input-group-text" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row m-2">
                    <label for="cpassword">Confirm Password: </label>
                    <div class="col-md-12">
                        <div class="input-group">
                            <input class="form-control border-secondary" type="password" name="cpassword" id="cpassword" placeholder="••••••••" required>
                            <span class="input-group-text" id="toggleCPassword">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>



                <div class="row m-2 text-center">
                    <div class="col-md-12 mt-2">
                        <div class="g-recaptcha" data-sitekey="6LdXrgQrAAAAANe-RL81IkGI6NAac7nDVx9OicSA"></div>
                    </div>
                </div>

                <div class="row m-2 text-center">
                    <div class="col-md-12">
                        <button class="btn btn-primary mt-4" style="width: 175px;" name="submit" type="submit">Create</button>
                    </div>
                </div>
                <div class="row m-2">
                    <div class="col-md-12 text-center">
                        Already have an account? <a href="login.php">Login</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="icon.js"></script>
    <script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.js"></script>
</body>

</html>