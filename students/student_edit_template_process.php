<?php
session_start();
include("../config/config.php");

/* =========================
   FILE CHECK
========================= */
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    die("No CSV file uploaded.");
}

$file = fopen($_FILES['csv_file']['tmp_name'], "r");

/* =========================
   EXPECTED CSV HEADER
========================= */
$expectedHeaders = [
    "Student ID",
    "Full Name",
    "Email",
    "Phone",
    "Faculty",
    "Course",
    "Parent Contact",
    "Address"
];

/* =========================
   READ & CLEAN HEADER
========================= */
$header = fgetcsv($file);
$header = array_map(function ($h) {
    $h = trim($h);
    $h = preg_replace('/\xEF\xBB\xBF/', '', $h);
    return $h;
}, $header);

foreach ($expectedHeaders as $i => $expected) {
    if (!isset($header[$i]) || $header[$i] !== $expected) {
        die("Invalid CSV format. Please use the provided template.");
    }
}

/* =========================
   COLUMN MAPPING
========================= */
$map = [
    0 => "student_id",
    1 => "full_name",
    2 => "email",
    3 => "phone_no",
    4 => "faculty",
    5 => "course",
    6 => "parent_contact",
    7 => "address"
];

$updated = [];
$skipped = [];

/* =========================
   PROCESS CSV ROWS
========================= */
while (($row = fgetcsv($file)) !== false) {

    $data = [];
    foreach ($map as $i => $dbField) {
        $data[$dbField] = trim($row[$i] ?? '');
    }

    /* =========================
       REQUIRED FIELD CHECK
    ========================= */
    if ($data['student_id'] === '') {
        $skipped[] = array_merge($data, [
            'reason' => 'Missing student ID'
        ]);
        continue;
    }

    /* =========================
       CHECK IF STUDENT EXISTS
    ========================= */
    $check = $conn->prepare("SELECT 1 FROM student WHERE student_id = ?");
    $check->bind_param("s", $data['student_id']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $skipped[] = array_merge($data, [
            'reason' => 'Student ID not found'
        ]);
        continue;
    }

    /* =========================
       UPDATE STUDENT
    ========================= */
    $stmt = $conn->prepare("
        UPDATE student SET
            full_name = ?,
            email = ?,
            phone_no = ?,
            faculty = ?,
            course = ?,
            parent_contact = ?,
            address = ?
        WHERE student_id = ?
    ");

    $stmt->bind_param(
        "ssssssss",
        $data['full_name'],
        $data['email'],
        $data['phone_no'],
        $data['faculty'],
        $data['course'],
        $data['parent_contact'],
        $data['address'],
        $data['student_id']
    );

    if ($stmt->execute()) {
        $updated[] = [
            'student_id' => $data['student_id'],
            'full_name'  => $data['full_name'],
            'action'     => 'Updated'
        ];
    } else {
        $skipped[] = array_merge($data, [
            'reason' => 'Update failed'
        ]);
    }
}

fclose($file);

/* =========================
   SAVE RESULT
========================= */
$_SESSION['import_updated'] = $updated;
$_SESSION['import_skipped'] = $skipped;

/* =========================
   REDIRECT
========================= */
header("Location: student_edit_template_result.php");
exit;
