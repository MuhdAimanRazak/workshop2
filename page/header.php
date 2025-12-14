<?php
/*require '../log/conn.php';
if (!isset($_SESSION['staff_ic'])) {
    echo "<script>alert('Session variable staff_ic doesn't exist.'); window.location.href = '../log/index.php';</script>";
    exit;
} else {
    */?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - SB Admin</title>

        <link rel="stylesheet" type="text/css" href="../css/styles.css" />
        <link rel="stylesheet" href="../style1.css">
        <script src="https://code.jquery.com/jquery-3.5.0.js"> </script>
        <script src="../js/jquery-3.6.4.js"></script>
        <script src="../js/fontawesome-6.3.0/all.js"></script>


    </head>

    <body class="sb-nav-fixed">
        <div class="container">
            <nav class="sb-topnav navbar navbar-expand navbar-dark bg-purple">
                <a class="navbar-brand ps-0" href="../dash/index.php">MyHostel</a>
                <button class="btn btn-link btn-sm order-first order-lg-first  me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                        class="fas fa-bars"></i></button>
                <ul class="navbar-nav ms-auto ms-md-10 me-0 me-lg-0">

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../log/signup.php">Sign Up</a></li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li><a class="dropdown-item" href="../log/logout.php">Logout</a></li>
                        </ul>

                    </li>
                </ul>
            </nav>
            <div id="layoutSidenav">
                <div id="layoutSidenav_nav">
                    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                        <div class="sb-sidenav-menu">
                            <div class="nav">
                                <div class="sb-sidenav-menu-heading">Home</div>
                                <a class="nav-link" href="../dash/index.php">
                                    <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>Dashboard
                                </a>
                                <div class="sb-sidenav-menu-heading">Data</div>
                                <a class="nav-link" href="../building/building.php">
                                    <div class="sb-nav-link-icon"><i class="fa-regular fa-credit-card"></i></div>Building
                                </a>
                                <a class="nav-link" href="">
                                    <div class="sb-nav-link-icon"><i class="fa-regular fa-rectangle-list"></i></div>
                                    Reservation
                                </a>
                                <a class="nav-link" href="">
                                    <div class="sb-nav-link-icon"><i class="fa-solid fa-person-shelter"></i></div>Facilities
                                </a>
                                <a class="nav-link" href="../students/student.php">
                                    <div class="sb-nav-link-icon"><i class="fa-solid fa-user"></i></div>Student
                                </a>
                                <a class="nav-link" href="../staff/staff.php">
                                    <div class="sb-nav-link-icon"><i class="fa-solid fa-clipboard-user"></i></div>Staff
                                </a>
                                <div class="sb-sidenav-menu-heading">Report</div>
                                <a class="nav-link" href="">
                                    <div class="sb-nav-link-icon"><i class="fa-solid fa-file"></i></div>Montly Report
                                </a>
                            </div>
                        </div>
                        <div class="sb-sidenav-footer">
                            <?php
                            //$staff_ic = $_SESSION['staff_ic'];
                            //$sql = "SELECT * FROM staff WHERE staff_ic = $staff_ic";
                            //$row = $conn->query($sql)->fetch_object();
                            ?>
                            <div class="small">Logged in as: </div>
                            <?php //echo $row->staff_name; ?>
                        </div>
                    </nav>
                </div>
                <div id="layoutSidenav_content">
                    <?php
//}
?>
</body>

</html>