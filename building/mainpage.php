<?php
include("../page/header.php");
?>
<main>
    <style>
        li {
            list-style-type: none;
        }
        a {
            text-decoration: none;
        }
    </style>

    <div class="container-fluid px-4">
        <h1 class="mt-4">Building</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Main Page</li>
        </ol>

        <div class="row">

            <!-- View Hostel -->
            <div class="col-xl-4 col-md-6">
                <a href="block.php">
                    <div class="card bg-purple text-white mb-4">
                        <div class="card-body d-flex justify-content-between align-items-center">View Hostel
                            <img src="../logo02.png" alt="Logo" class="logo2-img">
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- View Booking Request -->
            <div class="col-xl-4 col-md-6">
                <a href="building.php">
                    <div class="card bg-purple text-white mb-4">
                        <div class="card-body d-flex justify-content-between align-items-center">View Booking Request
                            <img src="../logo03.png" alt="Logo" class="logo2-img">
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- View Report -->
            <div class="col-xl-4 col-md-6">
                <a href="building.php">
                    <div class="card bg-purple text-white mb-4">
                        <div class="card-body d-flex justify-content-between align-items-center">View Report
                            <img src="../logo04.png" alt="Logo" class="logo2-img">
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</main>
<?php
include("../page/footer.php");
?>
