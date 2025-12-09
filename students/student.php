<?php
include("../page/header.php");
?>

<main>
    <style>
        /* ===== Student Page Custom Styles ===== */
        .table th, .table td {
            vertical-align: middle;
        }
.student-banner {
    margin-top: -10rem;      /* bring image closer */
    margin-bottom: -26rem;
    text-align: center;

    overflow: hidden;
    display: flex;
    justify-content: center;
    
}

.student-banner img {
    max-width: 1600px;
    width: 130%;
    margin-left: -250px;
    height: auto;
}


        /* Back link */
        .student-back {
            margin-top: .5rem;
            margin-bottom: .5rem;
        }
        .student-back a {
            text-decoration: none;
            color: #000;
            font-size: 0.95rem;
        }

        /* Search wrapper */
        .student-search-wrapper {
            width: 70%;
            position: relative;
        }

        .student-search-input {
            border-radius: 50px;
            padding-right: 3.2rem;
            height: 48px;
        }

        .student-search-btn {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            border: none;
            background-color: #5f6dff;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .student-search-btn i {
            font-size: 1rem;
        }

        /* Radio filter line */
        .student-filters {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 0.75rem;
            margin-bottom: 1.25rem;
            font-size: 0.95rem;
        }

        .student-filters .form-check-input {
            margin-top: 0;
            margin-right: .35rem;
            accent-color: #6f63ff; /* modern browsers */
        }

        .student-filters label {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .student-filters .name-option {
            color: #6f63ff;
        }

        .student-filters .other-option {
            color: #6f63ff;
        }
    </style>

    <div class="container-fluid px-4">


        <!-- Banner image -->
        <div class="student-banner">
            <img src="../studentsearch.png" alt="Student Directory">
        </div>

        <!-- Search bar -->
        <div class="d-flex justify-content-center mb-1">
            <div class="student-search-wrapper">
                <input type="text" class="form-control student-search-input" placeholder="Search here">
                <button type="button" class="student-search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Radio filter options -->
        <div class="student-filters">
            <label class="form-check-label name-option">
                <input class="form-check-input" type="radio" name="searchType" value="name" checked>
                Name
            </label>
            <label class="form-check-label other-option">
                <input class="form-check-input" type="radio" name="searchType" value="matric">
                Matric Number
            </label>
            <label class="form-check-label other-option">
                <input class="form-check-input" type="radio" name="searchType" value="phone">
                Phone Number
            </label>
        </div>

        <!-- Student Table -->
        <div class="card shadow-sm mt-2">
            <div class="card-body">

                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Bil.</th>
                            <th>Name</th>
                            <th>Matric number</th>
                            <th>Phone Number</th>
                            <th>Details</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Nurain Farahin Syazmin</td>
                            <td>B032310690</td>
                            <td>014-9288458</td>
                            <td>
                                <a href="student_details.php" class="btn btn-primary btn-sm rounded-pill px-3">
                                    More Details
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</main>

<?php
include("../page/footer.php");
?>
