<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    if ($password === "nadhem123") {
        $_SESSION["admin"] = true;
        header("Location: ../view/admin_dashboard.php");
        exit;
    } else {
        header("Location: ../view/login.php?error=1");
        exit;
    }
}
