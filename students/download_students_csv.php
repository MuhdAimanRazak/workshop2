<?php
include("../config/config.php");

$building_id = (int)($_GET['building_id'] ?? 0);
$fields = $_GET['fields'] ?? ['name','matric'];

if ($building_id <= 0) die("Invalid building");

/* FIELD MAP */
$map = [
    'name'   => ['label' => 'Full Name', 'col' => 's.full_name'],
    'matric' => ['label' => 'Matric Number', 'col' => 's.student_id'],
    'ic'     => ['label' => 'IC Number', 'col' => 's.student_ic'],
    'phone'  => ['label' => 'Phone Number', 'col' => 's.phone_no']
];

$cols = [];
$headers = [];

foreach ($fields as $f) {
    if (isset($map[$f])) {
        $cols[] = $map[$f]['col'];
        $headers[] = $map[$f]['label'];
    }
}

$sql = "
SELECT ".implode(",", $cols)."
FROM student s
JOIN booking bkg ON s.student_id=bkg.student_id
JOIN room r ON bkg.room_id=r.room_id
JOIN block blk ON r.block_id=blk.block_id
WHERE blk.building_id=?
AND s.status='active'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$result = $stmt->get_result();

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=students.csv");

$out = fopen("php://output", "w");
fputcsv($out, $headers);

while ($row = $result->fetch_row()) {
    fputcsv($out, $row);
}
fclose($out);
exit;
