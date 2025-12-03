<?php
include("../page/header.php");
?>
<main>
    <style>
        li {
            list-style-type: none;
        }
    </style>
    

<?php
// Update the notifications to mark them as read
/*$sql = "UPDATE reservation SET noti = 1 WHERE noti = 0";
$conn->query($sql);*/
?>

    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-purple  text-white mb-4">
                    <div class="card-body">STAFF</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-purple  text-white mb-4">
                    <div class="card-body">ROOM</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-purple  text-white mb-4">
                    <div class="card-body">CUSTOMER</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-purple text-white mb-4">
                    <div class="card-body">RESERVATION</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    New Reservation that need action
                </div>
                <div class="card-body">
                </div>
            </div>
        </div>
    </div>
</main>
<?php
include("../page/footer.php");
?>