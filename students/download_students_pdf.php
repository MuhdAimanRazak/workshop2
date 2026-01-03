<?php
require("../fpdf/fpdf.php");
include("../config/config.php");

/* =========================
   CUSTOM PDF CLASS
========================= */
class PDF extends FPDF {

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(
            0,
            8,
            'Generated on '.date('d/m/Y').' | MyHostel System | Page '.$this->PageNo().'/{nb}',
            0,
            0,
            'R'
        );
    }
}

/* =========================
   GET PARAMS
========================= */
$building_id = (int)($_GET['building_id'] ?? 0);
$fields = $_GET['fields'] ?? ['name','matric'];

if ($building_id <= 0) die("Invalid building");
if (empty($fields)) die("No fields selected");

/* =========================
   GET BUILDING NAME
========================= */
$stmtB = $conn->prepare("
    SELECT building_name
    FROM building
    WHERE building_id = ?
    LIMIT 1
");
$stmtB->bind_param("i", $building_id);
$stmtB->execute();
$bRes = $stmtB->get_result()->fetch_assoc();
$buildingName = strtoupper($bRes['building_name'] ?? '');

/* =========================
   FIELD CONFIG
========================= */
$fieldMap = [
    'name'   => ['label'=>'Nama Pelajar','col'=>'s.full_name','w'=>70],
    'matric' => ['label'=>'No Matrik','col'=>'s.student_id','w'=>35],
    'ic'     => ['label'=>'No IC','col'=>'s.student_ic','w'=>45],
    'phone'  => ['label'=>'No Telefon','col'=>'s.phone_no','w'=>35],
];

$cols = [];
foreach ($fields as $f) {
    if (isset($fieldMap[$f])) $cols[] = $fieldMap[$f];
}

/* =========================
   FETCH DATA
========================= */
$sql = "
SELECT ".implode(",", array_column($cols,'col'))."
FROM student s
JOIN booking bkg ON s.student_id=bkg.student_id
JOIN room r ON bkg.room_id=r.room_id
JOIN block blk ON r.block_id=blk.block_id
WHERE blk.building_id=?
AND s.student_status='active'
ORDER BY s.full_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$result = $stmt->get_result();

/* =========================
   PDF SETUP
========================= */
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 25);

/* =========================
   HEADER
========================= */
$pdf->Image('../utem-logo.png', 90, 10, 30);
$pdf->Ln(28);

$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,12,'SENARAI PELAJAR',0,1,'C');

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,'Kolej Kediaman UTeM - '.$buildingName,0,1,'C');

$pdf->Ln(5);
$pdf->SetDrawColor(180,180,180);
$pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
$pdf->Ln(12);

/* =========================
   CENTER TABLE HELPER
========================= */
function setCenterX($pdf, $cols) {
    $tableWidth = 10; // No column
    foreach ($cols as $c) {
        $tableWidth += $c['w'];
    }
    $pageWidth = $pdf->GetPageWidth();
    $pdf->SetX(($pageWidth - $tableWidth) / 2);
}

/* =========================
   TABLE HEADER
========================= */
function drawTableHeader($pdf, $cols) {
    setCenterX($pdf, $cols);
    $pdf->SetFont('Arial','B',10);

    $pdf->Cell(10,8,'No.',1,0,'C');
    foreach ($cols as $c) {
        $pdf->Cell($c['w'],8,$c['label'],1,0,'C');
    }
    $pdf->Ln();
}

/* =========================
   FIRST HEADER
========================= */
drawTableHeader($pdf, $cols);

/* =========================
   TABLE DATA
========================= */
$pdf->SetFont('Arial','',10);
$no = 1;

while ($row = $result->fetch_row()) {

    if ($pdf->GetY() > 260) {
        $pdf->AddPage();
        drawTableHeader($pdf, $cols);
        $pdf->SetFont('Arial','',10);
    }

    setCenterX($pdf, $cols);

    $pdf->Cell(10,8,$no++,1,0,'C');
    foreach ($row as $i => $val) {
        $pdf->Cell($cols[$i]['w'],8,$val,1,0,'C');
    }
    $pdf->Ln();
}

/* =========================
   OUTPUT
========================= */
$pdf->Output("D","senarai_pelajar_".$buildingName.".pdf");
exit;
