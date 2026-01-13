<?php
session_start();
include("../config/config.php");

// =========================
// GET SESSION DATA
// =========================
$updated = $_SESSION['import_updated'] ?? [];
$skipped = $_SESSION['import_skipped'] ?? [];

// Clear session data after reading
unset($_SESSION['import_updated'], $_SESSION['import_skipped']);
?>

<main>
    <div class="container-fluid px-4">

        <h2 class="mt-4">Student CSV Update Result</h2>

        <!-- UPDATED STUDENTS -->
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-success text-white">
                Updated Students (<?= count($updated) ?>)
            </div>
            <div class="card-body">
                <?php if (!empty($updated)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Full Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($updated as $i => $row): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($row['student_id']) ?></td>
                                        <td class="text-start"><?= htmlspecialchars($row['full_name']) ?></td>
                                        <td><?= htmlspecialchars($row['action']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No students were updated.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SKIPPED STUDENTS -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <span>Skipped Students (<?= count($skipped) ?>)</span>
                <?php if (!empty($skipped)): ?>
                    <a href="student_edit_download_skipped.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-download me-1"></i> Download Skipped CSV
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($skipped)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Faculty</th>
                                    <th>Course</th>
                                    <th>Parent Contact</th>
                                    <th>Address</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($skipped as $i => $row): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($row['student_id']) ?></td>
                                        <td class="text-start"><?= htmlspecialchars($row['full_name']) ?></td>
                                        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['phone_no'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['faculty'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['course'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['parent_contact'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['reason']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">No skipped students.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- BACK BUTTON -->
        <div class="mt-4">
            <a href="students_edit_import_form.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Import
            </a>
        </div>
    </div>
</main>
