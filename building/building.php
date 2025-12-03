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
            <li class="breadcrumb-item active">Building List</li>
        </ol>

        <div class="row">

            <!-- Lestari (Man) -->
            <div class="col-xl-3 col-md-6">
                <a href="lestari_man.php">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">Lestari (Man)</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Lestari (Woman) -->
            <div class="col-xl-3 col-md-6">
                <a href="lestari_woman.php">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">Lestari (Woman)</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Tuah (Man) -->
            <div class="col-xl-3 col-md-6">
                <a href="tuah_man.php">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">Tuah (Man)</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Kasturi (Man) -->
            <div class="col-xl-3 col-md-6">
                <a href="kasturi_man.php">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body">Kasturi (Man)</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Lekir (Woman) -->
            <div class="col-xl-3 col-md-6">
                <a href="lekir_woman.php">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">Lekir (Woman)</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Lekiu (Woman) -->
            <div class="col-xl-3 col-md-6">
                <a href="lekiu_woman.php">
                    <div class="card bg-secondary text-white mb-4">
                        <div class="card-body">Lekiu (Woman)</div>
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
