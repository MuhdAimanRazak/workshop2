<?php
session_start();
require 'conn.php';

$idpengguna = $_POST['idpengguna'];
$katalaluan = $_POST['katalaluan'];

/* =========================
   GET USER DATA
========================= */
$sql = "SELECT staff_ic, password, full_name, role 
        FROM staff 
        WHERE staff_ic = '$idpengguna'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    if (password_verify($katalaluan, $row['password'])) {

        /* =========================
           SAVE SESSION (IMPORTANT)
        ========================= */
        $_SESSION['staff_ic'] = $row['staff_ic'];
        $_SESSION['fullname'] = $row['full_name'];
        $_SESSION['role'] = $row['role']; // admin / staff

        /* =========================
           REDIRECT
        ========================= */
        header("Location: ../dashboard/dashboard.php");
        exit();

    } else {
        echo "<script>alert('Wrong password!'); history.back();</script>";
        exit();
    }

} else {
    echo "<script>alert('IC Number is not valid!'); history.back();</script>";
    exit();
}
