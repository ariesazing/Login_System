<?php
require_once 'DBconn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (!isset($_GET['id']) || $_SESSION['id'] != $_GET['id']) {
        $_SESSION['message'] = 'Only admins can update user details.';
        header('Location: index.php');
        exit;
    }
} 

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}


$id = $_GET['id'];
$stmt = $dbh->prepare("SELECT id, name FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['message'] = 'User not found.';
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "UPDATE users SET name = :name";
    $data = ['name' => $name, 'id' => $id];

    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } else {
            $stmt = $dbh->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                $error = 'Email already taken.';
            } else {
                $data['email'] = $email;
                $sql .= ", email = :email";
            }
        }
    }

    if ($_POST["role"] === "admin" && (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin")) {
        $error = "Only admins can create other admin users.";
    } else {
        $role = $_POST["role"];
    }

    if (!empty($role)) {
        $data['role'] = $role;
        $sql .= ", role = :role";

        if ($_SESSION['id'] == $id) {
            $_SESSION['role'] = $role;
        }
    }

    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = :password";
    }

    if (empty($error)) {
        $sql .= " WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        if ($stmt->execute($data)) {
            $_SESSION['message'] = 'User updated.';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Update failed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link href="bootstrap-icons-1.11.3/package/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="text-light">

    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card p-4 bg-dark shadow-lg text-light border-secondary mx-auto" style="width: 100%; max-width: 500px;">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center"><i class="bi bi-people"></i> Update User Details </h3>
                <?php if ($error): ?>
                    <div class="alert alert-dark alert-dismissible fade show text-dark border-0 ">
                        <?= $error ?>
                        <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label">Name</label>
                        <input type="text" name="name"
                            class="form-control  border-secondary"
                            value="<?= htmlspecialchars($user['name']) ?>"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                            class="form-control  border-secondary"
                            placeholder="example@gmail.com">
                        <small id="passwordHelp" class="form-text text-muted">
                            Leave blank to keep current email
                        </small>
                    </div>

                    <div class="mb-4">
                        <div class="col-md-12">
                            <label for="role">Role: </label>
                            <select class="form-select border-secondary" name="role">
                                <option value="" disabled selected>Choose a Role</option>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password"
                            class="form-control  border-secondary"
                            placeholder="••••••••"
                            aria-describedby="passwordHelp">
                        <small id="passwordHelp" class="form-text text-muted">
                            Leave blank to keep current password
                        </small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto ">
                            <i class="bi bi-save me-2"></i>Update
                        </button>
                        <a href="index.php" class="btn btn-secondary w-100 w-md-auto">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="icon.js"></script>
    <script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.js"></script>
</body>

</html>