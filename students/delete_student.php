<?php
include("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id = $_POST['student_id'];

    if ($student_id === '') {
        header("Location: student.php");
        exit;
    }

    $sql = "DELETE FROM student WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    header("Location: student.php");
    exit;
}
