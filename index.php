<!DOCTYPE html>
<html>
  <head>
    <title>MyHostel – Log Masuk</title>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
<script>
function togglePassword() {
    const passwordField = document.getElementById("katalaluan");

    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
</script>

<body>

  <div class="wrapper">

    <!-- Header Box -->
    <div class="header-box">
      <h1>MyHostel</h1>
    </div>

    <!-- Login Box -->
    <div class="login-box">
      <h2>Login</h2>

      <form method="post" action="login.php">

        <div class="user-box">
          <input type="text" id="idpengguna" name="idpengguna" required>
          <label for="idpengguna">Username</label>
        </div>

        <div class="user-box password-box">
          <input type="password" id="katalaluan" name="katalaluan" required>
          <label for="katalaluan">Password</label>
          <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <button type="submit" name="login-btn">Login</button>

        <!-- Cancel button MUST be inside the .login-box but OUTSIDE the form -->
        <a href="../index.php" class="btn1">Cancel</a>

      </form>

    </div>
<div class="footer-info">
    Copyright &copy; UTEM 2025  
    &middot;  
    <a href="#">Privacy Policy</a>
    &middot;  
    <a href="#">Terms &amp; Conditions</a>
</div>

  </div>



</body>

</html>
