<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="student_template.csv"');

$output = fopen("php://output", "w");

fputcsv($output, [
    "Student ID",
    "Full Name",
    "Email",
    "Phone",
    "IC",
    "Faculty",
    "Course",
    "Parent Contact",
    "Address"
]);

fputcsv($output, [
    "S001",
    "Ali Bin Ahmad",
    "ali@example.com",
    "0123456789",
    "990101011234",
    "FTMK",
    "Bachelor of Computer Science",
    "0198765432",
    "No 1 Jalan Universiti"
]);

fclose($output);
exit;
