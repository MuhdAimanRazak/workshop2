<?php
include("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_id = $_POST['facility_id'];
    
    if ($facility_id === '') {
        header("Location: facility.php");
        exit;
    }
    
    // Check if facility is referenced in other tables (e.g., booking, maintenance_log, etc.)
    $check_sql = "SELECT COUNT(*) as count FROM booking WHERE facility_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $facility_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo "<script>
            alert('Cannot delete this facility. It has related bookings or records in other tables.');
            window.location.href = 'facility_details.php?id=$facility_id';
        </script>";
        exit;
    }
    
    // If no references found, proceed with deletion
    $sql = "DELETE FROM facility WHERE facility_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $facility_id);
    $stmt->execute();
    
    echo "<script>
        alert('Facility successfully deleted!');
        window.location.href = 'facility.php';
    </script>";
    exit;
}