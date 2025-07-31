<?php 
    session_start();
    if (!isset($_SESSION['logged_in']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
        header('Location: login.php');
        exit;
    }
    $DBhost = 'localhost';
    $DBuser = 'root';
    $DBpassword = '';
    $DBname = 'login_systemdb';

    try {
        $dbh = new PDO('mysql: host='. $DBhost. ';dbname='. $DBname , $DBuser, $DBpassword);
        $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        exit("Error: " . $e->getMessage());
    }   
?>