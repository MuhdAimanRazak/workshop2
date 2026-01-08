<?php
include("../page/header.php");
include("../config/config.php");

if (!$conn) {
    die("<div class='alert alert-danger'>Database connection failed</div>");
}

/* =========================
   PAGINATION
========================= */
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

/* =========================
   COUNT FOR PAGINATION
========================= */
$countRes = $conn->query("SELECT COUNT(*) AS total FROM student");
$totalStudents = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalStudents / $limit);

/* =========================
   FETCH DATA
========================= */
$sql = "SELECT student_id, full_name, phone_no
        FROM student
        ORDER BY full_name ASC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$start = $totalStudents > 0 ? $offset + 1 : 0;
$end   = min($offset + $limit, $totalStudents);
?>

<main>

<style>
html, body {
    background:#fff !important;
    margin:0;
    padding:0;
}

.table th, .table td {
    vertical-align: middle;
    background:#fff !important;
}

.student-banner {
    margin-top:-6rem;
    margin-bottom:-7rem;
    display:flex;
    justify-content:center;
}

.student-banner img {
    max-width:650px;
    width:100%;
}

.student-search-wrapper {
    width:420px;
    position:relative;
}

.student-search-input {
    border-radius:50px;
    padding-right:3.2rem;
    height:48px;
}

.student-search-btn {
    position:absolute;
    right:6px;
    top:50%;
    transform:translateY(-50%);
    width:40px;
    height:40px;
    border-radius:50%;
    border:none;
    background:#5f6dff;
    color:#fff;
}

.student-filters {
    display:flex;
    justify-content:center;
    gap:1.5rem;
    margin:12px 0 20px;
}

.student-filters label {
    cursor:pointer;
}
</style>

<div class="container-fluid px-4">

    <!-- BANNER -->
    <div class="student-banner">
        <img src="../student-banner.png" alt="Student Directory">
    </div>

    <!-- SEARCH + ADD -->
    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">

        <!-- SEARCH (CLIENT SIDE) -->
        <div class="student-search-wrapper">
            <input id="studentSearch" type="text"
                   class="form-control student-search-input"
                   placeholder="Search by name"
                   onkeyup="applySearch()">
            <button class="student-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <!-- ADD -->
        <a href="add_student_choice.php" class="btn btn-success rounded-pill px-4 py-2">
            <i class="fas fa-plus me-1"></i> Add Student
        </a>
    </div>

    <!-- SEARCH TYPE -->
    <div class="student-filters">
        <label><input type="radio" name="searchType" value="name" checked> Name</label>
        <label><input type="radio" name="searchType" value="id"> Matric Number</label>
        <label><input type="radio" name="searchType" value="phone"> Phone Number</label>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="text-muted mb-2">
                Showing <?php echo $start; ?> to <?php echo $end; ?> of
                <b><?php echo $totalStudents; ?></b> students
            </div>

            <table id="studentTable" class="table table-bordered text-center align-middle">
                <thead>
                <tr>
                    <th>Bil.</th>
                    <th class="text-start">Name</th>
                    <th>Matric Number</th>
                    <th>Phone Number</th>
                    <th>Details</th>
                </tr>
                </thead>
                <tbody>

                <?php
                if ($totalStudents == 0) {
                    echo "<tr><td colspan='5'>No results found</td></tr>";
                } else {
                    $i = $start;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>$i</td>
                            <td class='text-start'>{$row['full_name']}</td>
                            <td>{$row['student_id']}</td>
                            <td>{$row['phone_no']}</td>
                            <td>
                                <a href='student_details.php?id={$row['student_id']}'
                                   class='btn btn-primary btn-sm rounded-pill px-4'>
                                   More Details
                                </a>
                            </td>
                        </tr>";
                        $i++;
                    }
                }
                ?>

                </tbody>
            </table>

            <!-- PAGINATION (SAME STYLE AS STAFF) -->
<?php if ($totalPages > 1): ?>
<nav class="d-flex justify-content-end mt-3">
    <ul class="pagination pagination-sm">

        <?php
        $range = 2; // pages before & after current
        $startPage = max(1, $page - $range);
        $endPage   = min($totalPages, $page + $range);
        ?>

        <!-- PREV -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link"
               href="?page=<?= max(1, $page - 1) ?>">
               Prev
            </a>
        </li>

        <!-- FIRST PAGE -->
        <?php if ($startPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1">1</a>
            </li>

            <?php if ($startPage > 2): ?>
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <!-- PAGE WINDOW -->
        <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
            <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $p ?>">
                    <?= $p ?>
                </a>
            </li>
        <?php endfor; ?>

        <!-- LAST PAGE -->
        <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            <?php endif; ?>

            <li class="page-item">
                <a class="page-link" href="?page=<?= $totalPages ?>">
                    <?= $totalPages ?>
                </a>
            </li>
        <?php endif; ?>

        <!-- NEXT -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link"
               href="?page=<?= min($totalPages, $page + 1) ?>">
               Next
            </a>
        </li>

    </ul>
</nav>
<?php endif; ?>

        </div>
    </div>
</div>

<script>
function applySearch() {
    const q = document.getElementById('studentSearch').value.toLowerCase();
    const type = document.querySelector('input[name="searchType"]:checked').value;
    const rows = document.querySelectorAll('#studentTable tbody tr');

    rows.forEach(row => {
        let text = '';
        if (type === 'name') text = row.cells[1].innerText.toLowerCase();
        else if (type === 'id') text = row.cells[2].innerText.toLowerCase();
        else text = row.cells[3].innerText.toLowerCase();

        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>

</main>

<?php include("../page/footer.php"); ?>
