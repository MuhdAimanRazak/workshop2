<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyHostel – Login</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function togglePassword() {
            const p = document.getElementById("password");
            p.type = p.type === "password" ? "text" : "password";
        }
    </script>
</head>
<body>

<!-- OUTER DEVICE BOX -->
<div class="device">

    <!-- TOP BAR -->
    <div class="top-bar">
        <span>myHostel</span>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page">

        <!-- BANNER -->
        <div class="banner">
            <img src="../banner.png" alt="Banner">
        </div>

        <!-- LOGIN CARD -->
        <div class="card">

            <div class="card-left">
                <img src="../hostel.png" alt="Hostel">
            </div>

            <div class="card-right">
                <img src="../logo.png" class="login-logo" alt="Logo">

                <form method="post" action="log/login.php">

                    <div class="field">
                        <input type="text" name="username" required>
                        <span>Username</span>
                    </div>

                    <div class="field">
                        <input type="password" name="password" id="password" required>
                        <span>Password</span>
                        <i onclick="togglePassword()">👁️</i>
                    </div>

                    <div class="row">
                        <label><input type="checkbox" name="remember"> Remember Me</label>
                        <a href="#">Forgot Password?</a>
                    </div>

                    <button type="submit">Log In</button>
                </form>
            </div>

        </div>

        <!-- DECORATIVE CORNER IMAGES -->
        <img src="../bg-left.png" class="decor-left" alt="">
        <img src="../bg-right.png" class="decor-right" alt="">

    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    Copyright © UTEM 2025 · Privacy Policy · Terms & Conditions
</div>

</body>
</html>
