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
$offset = ($page - 1) * $limit;

/* =========================
   SEARCH (SERVER SIDE)
========================= */
$search = $_GET['search'] ?? '';
$whereSQL = '';

if ($search !== '') {
    $safe = $conn->real_escape_string($search);
    $whereSQL = "WHERE 
        full_name LIKE '%$safe%' OR 
        student_id  LIKE '%$safe%' OR 
        no_kediaman LIKE '%$safe%'";
}

/* =========================
   COUNT FOR PAGINATION
========================= */
$countRes = $conn->query(
    "SELECT COUNT(*) AS total 
     FROM hostel_registration_form
     $whereSQL"
);
$totalRows = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

/* =========================
   FETCH DATA
========================= */
$sql = "SELECT 
            no_kediaman,
            check_in_date,
            full_name,
            student_id ,
            phone_no
        FROM hostel_registration_form
        $whereSQL
        ORDER BY check_in_date DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$start = $totalRows > 0 ? $offset + 1 : 0;
$end   = min($offset + $limit, $totalRows);
?>

<main>
<style>
.table th, .table td { vertical-align: middle; }
.search-box { width:380px; }
</style>

<div class="container-fluid px-4">

    <!-- TITLE -->
    <h4 class="mb-3">Senarai Pemeriksaan Kediaman</h4>

    <!-- SEARCH -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form class="d-flex">
            <input type="text"
                   name="search"
                   class="form-control search-box rounded-pill me-2"
                   placeholder="Cari nama / no matrik / no kediaman"
                   value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="text-muted mb-2">
                Showing <?= $start ?> to <?= $end ?> of <b><?= $totalRows ?></b> records
            </div>

            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th>No Kediaman</th>
                        <th>Nama Pelajar</th>
                        <th>No Matrik</th>
                        <th>No Telefon</th>
                        <th>Tarikh Masuk</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($totalRows == 0) {
                    echo "<tr><td colspan='7'>Tiada rekod dijumpai</td></tr>";
                } else {
                    $i = $start;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>$i</td>
                            <td>{$row['no_kediaman']}</td>
                            <td class='text-start'>{$row['full_name']}</td>
                            <td>{$row['student_id ']}</td>
                            <td>{$row['phone_no']}</td>
                            <td>{$row['check_in_date']}</td>
                            <td>
                                <a href='kediaman_details.php?student_id ={$row['student_id']}'
                                   class='btn btn-info btn-sm rounded-pill px-3'>
                                   Details
                                </a>
                            </td>
                        </tr>";
                        $i++;
                    }
                }
                ?>
                </tbody>
            </table>

            <!-- PAGINATION -->
            <?php if ($totalPages > 1): ?>
            <nav class="d-flex justify-content-end mt-3">
                <ul class="pagination pagination-sm">
                    <li class="page-item <?= $page<=1?'disabled':'' ?>">
                        <a class="page-link"
                           href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">
                           Prev
                        </a>
                    </li>

                    <?php for ($p=1; $p<=$totalPages; $p++): ?>
                        <li class="page-item <?= $p==$page?'active':'' ?>">
                            <a class="page-link"
                               href="?page=<?= $p ?>&search=<?= urlencode($search) ?>">
                               <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
                        <a class="page-link"
                           href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">
                           Next
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
    </div>

</div>
</main>

<?php include("../page/footer.php"); ?>
