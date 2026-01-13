<?php
require 'conn.php';
$idpengguna = $_POST['idpengguna'];
$katalaluan = $_POST['katalaluan'];

// Check if the user ID exists in the staff table
$sql = "SELECT staff_ic, password FROM staff WHERE staff_ic = '$idpengguna'";
$result = mysqli_query($conn, $sql);

// If the user ID is found, check if the password is correct
if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  $storedPassword = $row['password'];
  if (password_verify($katalaluan, $storedPassword))  {
    // If the password is correct, start a session and redirect the user to the staff/ page
    $_SESSION['staff_ic'] = $idpengguna;
    header("Location: ../dash/index.php");
    exit();
  } else {
    // If the password is incorrect, show an error message and go back to the previous page
    echo "<script>alert('Wrong password!'); history.back();</script>";
    exit();
  }
} else {
  // If the user ID is not found, show an error message and go back to the previous page
  echo "<script>alert('IC Number is not valid!'); history.back();</script>";
  exit();
}
?>
