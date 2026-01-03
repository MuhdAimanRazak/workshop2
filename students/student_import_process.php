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
    "IC",
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
    $h = preg_replace('/\xEF\xBB\xBF/', '', $h); // remove BOM
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
    4 => "student_ic",
    5 => "faculty",
    6 => "course",
    7 => "parent_contact",
    8 => "address"
];

$success = [];
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
    if ($data['student_id'] === '' || $data['full_name'] === '') {
        $skipped[] = [
            'student_id'     => $data['student_id'],
            'full_name'      => $data['full_name'],
            'email'          => $data['email'],
            'phone_no'       => $data['phone_no'],
            'student_ic'     => $data['student_ic'],
            'faculty'        => $data['faculty'],
            'course'         => $data['course'],
            'parent_contact' => $data['parent_contact'],
            'address'        => $data['address'],
            'reason'         => 'Missing required fields'
        ];
        continue;
    }

    /* =========================
       DUPLICATE CHECK
    ========================= */
    $check = $conn->prepare("SELECT 1 FROM student WHERE student_id = ?");
    $check->bind_param("s", $data['student_id']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $skipped[] = [
            'student_id'     => $data['student_id'],
            'full_name'      => $data['full_name'],
            'email'          => $data['email'],
            'phone_no'       => $data['phone_no'],
            'student_ic'     => $data['student_ic'],
            'faculty'        => $data['faculty'],
            'course'         => $data['course'],
            'parent_contact' => $data['parent_contact'],
            'address'        => $data['address'],
            'reason'         => 'Duplicate student ID'
        ];
        continue;
    }

    /* =========================
       INSERT STUDENT
    ========================= */
    $stmt = $conn->prepare("
        INSERT INTO student
        (student_id, full_name, email, phone_no, student_ic,
         faculty, course, parent_contact, address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssssssss",
        $data['student_id'],
        $data['full_name'],
        $data['email'],
        $data['phone_no'],
        $data['student_ic'],
        $data['faculty'],
        $data['course'],
        $data['parent_contact'],
        $data['address']
    );

    if ($stmt->execute()) {
        $success[] = [
            'student_id' => $data['student_id'],
            'full_name'  => $data['full_name'],
            'email'      => $data['email']
        ];
    } else {
        $skipped[] = [
            'student_id'     => $data['student_id'],
            'full_name'      => $data['full_name'],
            'email'          => $data['email'],
            'phone_no'       => $data['phone_no'],
            'student_ic'     => $data['student_ic'],
            'faculty'        => $data['faculty'],
            'course'         => $data['course'],
            'parent_contact' => $data['parent_contact'],
            'address'        => $data['address'],
            'reason'         => 'Database error'
        ];
    }
}

fclose($file);

/* =========================
   SAVE RESULT
========================= */
$_SESSION['import_success'] = $success;
$_SESSION['import_skipped'] = $skipped;

/* =========================
   REDIRECT
========================= */
header("Location: student_import_result.php");
exit;
