<?php
require_once 'DBconn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['message'] = 'Only admins can delete users.';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentID = $_SESSION['id'];
    $deleteID = $_POST['id'];
    $stmt = $dbh->prepare("DELETE FROM users WHERE id = ?");

    if ($currentID == $deleteID) {
        if ($stmt->execute([$id])) {
            header('Location: index.php');
            session_destroy();
            exit;
        } else {
            $_SESSION['message'] = 'Deletion failed.';
        }
    } else {
        if ($stmt->execute([$id])) {
            $_SESSION['message'] = 'User deleted.';
        } else {
            $_SESSION['message'] = 'Deletion failed.';
        }
    }
}
header('Location: index.php');
exit;
