<?php
require_once 'DBconn.php';



//DATA POSTING---------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["name"])) {
        $errors[] = "Name is required";
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    if (empty($_POST["email"])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email Format";
    } else {

        $email = htmlspecialchars($_POST["email"]);
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $existingEmail = $stmt->fetchColumn();

        if ($existingEmail > 0) {
            $errors[] = "Email already exists. Please use a different one.";
        }
    }

    if ($_POST["role"] === "admin" && (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin")) {
        $errors[] = "Only admins can create other admin users.";
    } else {
        $role = $_POST["role"];
    }

    if (empty($_POST["password"])) {
        $errors[] = "Password is required";
    } else if ($_POST["cpassword"] !== $_POST["password"]) {
        $errors[] = "Password does not match!";
    } else {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    }

    if (empty($errors)) {
        $sql = "INSERT INTO users (name, email, role, password) 
                VALUES (:name, :email, :role, :password)";

        $query = $dbh->prepare($sql);
        $query->execute([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => $password
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
        echo "<h2>Errors: </h2>" . "<br>";
        foreach ($errors as $error) {
            echo $error;
            echo "<br>";
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

    <title>Document</title>
</head>

<body>
    <style>
        body {
            background: #1C6EA4;
            background: -moz-radial-gradient(center, rgb(41, 41, 41) 0%, #2388CB 0%, rgb(9, 24, 41) 100%);
            background: -webkit-radial-gradient(center, rgb(164, 165, 167) 0%, rgb(117, 117, 117) 0%, rgb(31, 56, 88) 100%);
            background: radial-gradient(ellipse at center, rgb(45, 48, 49) 0%, rgb(19, 19, 19) 0%, rgb(21, 38, 49) 100%);
            color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
    </style>
    <div class="alert aler-danger"><? $error ?></div>
    <div class="container d-flex align-items-center justify-content-center vh-100 ">
        <div class="card p-4 bg-dark shadow-lg shadow-lg text-light border-secondary mx-auto" style="width: 100%; max-width: 500px;">
            <h2 class="text-center">CREATE USER</h2>

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
                    <div class="col-md-12">
                        <label for="role">Role: </label>
                        <select class="form-select border-secondary" name="role">
                            <option value="" disabled selected>Choose a Role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
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

                <div class="row text-center">
                    <div class="col-md-6">
                        <button class="btn btn-primary mt-4" style="width: 175px;" name="submit" type="submit">Create</button>
                    </div>
                    <div class="col-md-6">
                        <a href="index.php" class="btn btn-secondary mt-4" style="width: 175px;">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="icon.js"></script>
    <script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.js"></script>

</body>

</html>