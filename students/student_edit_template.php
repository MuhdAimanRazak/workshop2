<?php
include("../config/config.php");

/* =========================
   CSV HEADERS
========================= */
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=student_list.csv');

/* =========================
   OUTPUT STREAM
========================= */
$output = fopen("php://output", "w");

/* =========================
   CSV COLUMN HEADERS
========================= */
fputcsv($output, [
    "Student ID",
    "Full Name",
    "Email",
    "Phone",
    "Faculty",
    "Course",
    "Parent Contact",
    "Address",
    "Status"
]);

/* =========================
   FETCH DATA FROM DATABASE
========================= */
$sql = "SELECT 
            student_id,
            full_name,
            email,
            phone_no,
            faculty,
            course,
            parent_contact,
            address,
            status
        FROM student
        ORDER BY full_name ASC";

$result = $conn->query($sql);

/* =========================
   WRITE DATA TO CSV
========================= */
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['student_id'],
            $row['full_name'],
            $row['email'],
            $row['phone_no'],
            $row['faculty'],
            $row['course'],
            $row['parent_contact'],
            $row['address'],
            ucfirst($row['status'])
        ]);
    }
}

/* =========================
   CLOSE OUTPUT
========================= */
fclose($output);
exit;
