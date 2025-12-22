<?php
session_start();

/* =========================
   VALIDATION
========================= */
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SESSION['import_skipped']) ||
    !is_array($_SESSION['import_skipped'])
) {
    die("No skipped records found. Please import a CSV file first.");
}

$skipped = $_SESSION['import_skipped'];

/* =========================
   CSV HEADERS
========================= */
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="skipped_students.csv"');

$output = fopen("php://output", "w");

/* =========================
   CSV COLUMN HEADER
========================= */
fputcsv($output, [
    "Student ID",
    "Full Name",
    "Email",
    "Phone",
    "IC",
    "Faculty",
    "Course",
    "Parent Contact",
    "Address",
    "Skip Reason"
]);

/* =========================
   CSV DATA
========================= */
foreach ($skipped as $row) {
    fputcsv($output, [
        $row['student_id'] ?? '',
        $row['full_name'] ?? '',
        $row['email'] ?? '',
        $row['phone_no'] ?? '',
        $row['student_ic'] ?? '',
        $row['faculty'] ?? '',
        $row['course'] ?? '',
        $row['parent_contact'] ?? '',
        $row['address'] ?? '',
        $row['reason'] ?? ''
    ]);
}

fclose($output);

/* =========================
   OPTIONAL: CLEAR SESSION
========================= */
// unset($_SESSION['import_skipped'], $_SESSION['import_success']);

exit;
