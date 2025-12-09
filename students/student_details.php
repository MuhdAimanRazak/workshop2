<?php 
include("../page/header.php");
?>

<main>
<style>
    .container-fluid { padding-left: 2.5rem; padding-right: 2.5rem; }

    .profile-shell {
        background: #ffffff;
        border-radius: 12px;
        padding: 2.25rem;
        margin: 1.5rem 0;
        box-shadow: 0 8px 25px rgba(0,0,0,0.03);
    }

    .profile-top {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
    }

    .avatar-wrap {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: #e6eef8;
        display:flex;
        align-items:center;
        justify-content:center;
        overflow: hidden;
    }

    .avatar-wrap img { width:100%; height:100%; object-fit:cover; }

    .profile-name {
        flex: 1;
        text-align: center;
        font-weight:800;
        font-size:1.3rem;
        margin-left: -15px;
    }

    .edit-button-wrap {
        position: absolute;
        right: 0;
        top: 10px;
    }

        .profile-name {
        flex: 1;
        text-align: center;
        font-weight:800;
        font-size:1.3rem;
        margin-left: -500px; /* pull name left toward avatar */
        margin-top: -6px;   /* lift a little for better vertical alignment */
    }


    .edit-button-wrap .btn {
        border-radius: 999px;
        padding: .45rem .9rem;
    }

    .profile-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 0.5rem 4rem;
        margin-top: 1.5rem;
    }

    .detail-title {
        text-transform: uppercase;
        font-weight: 800;
        color: #2a2a8c;
        font-size: 0.78rem;
        margin-bottom: 2px;
    }

    .detail-value {
        color: #111827;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    .status-area {
        grid-column: 1 / -1;
        margin-top: 0.5rem;
        padding-top: 0.9rem;
        border-top: 1px dashed rgba(0,0,0,0.1);
        display:flex;
        gap: 3rem;
    }

    .status-label { font-weight:700; font-size:0.95rem; }
    .status-value { font-weight:700; color:#16a34a; font-size:0.95rem; }

    a.detail-link { color:#2563eb; text-decoration:none; }

    @media (max-width:900px) {
        .profile-details { grid-template-columns: 1fr; }
        .edit-button-wrap { position: static; margin-top: .5rem; text-align:right; }
    }
</style>

<div class="container-fluid px-4">

    <!-- Back Button -->
    <div class="student-back ms-4" style="margin-bottom: 0.5rem;">
        <a href="student.php">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="profile-shell">

        <!-- Avatar + Name + Edit Button -->
        <div class="profile-top">
            <div class="avatar-wrap">
                <img src="/hostel_system/assets/avatar-default.png" alt="avatar">
            </div>

            <div class="profile-name">NURAIN FARAHIN SYAZMIN</div>

            <div class="edit-button-wrap">
                <a href="editstudentprofile.php?id=123" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Info
                </a>
            </div>
        </div>

        <!-- Details -->
        <div class="profile-details">

            <!-- LEFT COLUMN -->
            <div>
                <div class="detail-title">Matric Number</div>
                <div class="detail-value">B032310690</div>
            </div>

            <!-- RIGHT COLUMN -->
            <div>
                <div class="detail-title">Gender</div>
                <div class="detail-value">Female</div>
            </div>

            <div>
                <div class="detail-title">Phone Number</div>
                <div class="detail-value">0149288458</div>
            </div>

            <div>
                <div class="detail-title">IC Number</div>
                <div class="detail-value">990101-01-1234</div>
            </div>

            <div>
                <div class="detail-title">Emergency Contact</div>
                <div class="detail-value">Mother</div>
            </div>

            <div>
                <div class="detail-title">Guardian's Number</div>
                <div class="detail-value">012345678</div>
            </div>

            <div>
                <div class="detail-title">Building</div>
                <div class="detail-value">LEKIU, SATRIA</div>
            </div>

            <div>
                <div class="detail-title">Room</div>
                <div class="detail-value">SQ-12-4B</div>
            </div>

            <div>
                <div class="detail-title">Programme</div>
                <div class="detail-value">BIT (Bachelor of Information Technology)</div>
            </div>

            <div>
                <div class="detail-title">Year / Semester</div>
                <div class="detail-value">Year 2 / Semester 4</div>
            </div>

            <!-- EMAIL AT THE BOTTOM OF RIGHT COLUMN -->
            <div>
                <div class="detail-title">Email</div>
                <div class="detail-value">
                    <a class="detail-link" href="mailto:nurain.farahin@example.com">
                        nurain.farahin@example.com
                    </a>
                </div>
            </div>

            <!-- STATUS AREA -->
            <div class="status-area">
                <div>
                    <span class="status-label">Bill Registration:</span>
                    <span class="status-value">REGISTERED</span>
                </div>
                <div>
                    <span class="status-label">Bill Status:</span>
                    <span class="status-value">PAID</span>
                </div>
            </div>

        </div> <!-- end details -->

    </div> <!-- end profile shell -->

</div>

</main>

<?php include("../page/footer.php"); ?>
