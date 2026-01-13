<?php
include("../config/config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

$message = '';
$message_type = '';

/* Get staff ID from URL */
$staff_id = $_GET['id'] ?? '';

if ($staff_id === '') {
    header("Location: /workshop2/staff/staff.php");
    exit;
}

/* Fetch staff data */
$stmt = $conn->prepare("SELECT staff_id, full_name, email, staff_ic FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h4>Staff not found</h4></div>";
    exit;
}

$staff_data = $result->fetch_assoc();

/* Generate random password */
function generateRandomPassword($length = 12) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%';
    return substr(str_shuffle($chars), 0, $length);
}

/* Mask email */
function maskEmail($email) {
    [$name, $domain] = explode("@", $email);
    return substr($name, 0, 3) . "***@" . $domain;
}

function s($arr, $key, $default = '') {
    return isset($arr[$key]) ? $arr[$key] : $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_reset'])) {
        $staff_id = $_POST['staff_id'];

        $stmt = $conn->prepare("SELECT full_name, email FROM staff WHERE staff_id = ?");
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $staff = $stmt->get_result()->fetch_assoc();

        if ($staff) {
            $new_password = generateRandomPassword();

            try {
                $mail = new PHPMailer(true);

                // SMTP configuration
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'utemmyhostel@gmail.com';
                $mail->Password   = 'nzxazsxwjuctxchr';
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;

                // Email content
                $mail->setFrom('utemmyhostel@gmail.com', 'MyHostel');
                $mail->addAddress($staff['email'], $staff['full_name']);
                $mail->isHTML(true);
                $mail->Subject = 'MyHostel - Password Reset';
                $mail->Body = "
                    <p>Dear {$staff['full_name']},</p>
                    <p>Your password has been reset by an administrator.</p>
                    <p><strong>Temporary Password:</strong> {$new_password}</p>
                    <p>Please log in and change it immediately.</p>
                ";

                // Send email
                $mail->send();

                // Update password if email sent successfully
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE staff SET password = ? WHERE staff_id = ?");
                $update->bind_param("ss", $hashed, $staff_id);
                $update->execute();

                $message = "Password reset successful. New password sent to " . maskEmail($staff['email']);
                $message_type = "success";

            } catch (Exception $e) {
                $message = "Failed to send reset email: " . $mail->ErrorInfo;
                $message_type = "danger";
            }
        }
    }
}

$back_url = "staff_details.php?id=" . $staff_id;
?>

<?php include("../page/header.php"); ?>

<main>
<style>
html, body {
    background: #ffffff !important;
    margin: 0;
    padding: 0;
}
.container-fluid { padding:2.5rem; }

.profile-shell {
    background:#fff;
    border-radius:14px;
    padding:2.25rem;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    max-width:700px;
    margin:0 auto;
}

.student-back {
    margin-bottom:1rem;
    max-width:700px;
    margin-left:auto;
    margin-right:auto;
}

.reset-header {
    text-align:center;
    margin-bottom:2rem;
}

.reset-header h2 {
    font-size:1.75rem;
    font-weight:800;
    color:#2a2a8c;
    margin-bottom:0.5rem;
}

.reset-header p {
    color:#666;
    font-size:0.95rem;
}

.staff-info-box {
    background:#f8f9fa;
    border-radius:10px;
    padding:1.5rem;
    margin-bottom:1.5rem;
}

.info-row {
    display:flex;
    padding:0.75rem 0;
    border-bottom:1px solid #e9ecef;
}

.info-row:last-child {
    border-bottom:none;
}

.info-label {
    font-weight:700;
    color:#2a2a8c;
    min-width:120px;
    font-size:0.9rem;
}

.info-value {
    color:#333;
    font-size:0.9rem;
}

.reminder-box {
    background:#fff3cd;
    border-left:4px solid #ffc107;
    padding:1rem 1.25rem;
    border-radius:8px;
    margin-bottom:1.5rem;
}

.reminder-box strong {
    display:block;
    color:#856404;
    margin-bottom:0.5rem;
    font-size:0.95rem;
}

.reminder-box p {
    color:#856404;
    font-size:0.875rem;
    margin:0;
    line-height:1.5;
}

.alert {
    padding:1rem;
    border-radius:8px;
    margin-bottom:1.5rem;
    font-size:0.9rem;
}

.alert-success {
    background:#d4edda;
    color:#155724;
    border:1px solid #c3e6cb;
}

.alert-danger {
    background:#f8d7da;
    color:#721c24;
    border:1px solid #f5c6cb;
}

.button-group {
    display:flex;
    gap:1rem;
    justify-content:center;
}

.btn-reset {
    background:#dc3545;
    color:#fff;
    padding:0.75rem 2rem;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
    font-size:0.95rem;
    transition:background 0.3s;
}

.btn-reset:hover {
    background:#c82333;
}

.btn-cancel {
    background:#6c757d;
    color:#fff;
    padding:0.75rem 2rem;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
    font-size:0.95rem;
    text-decoration:none;
    display:inline-block;
    transition:background 0.3s;
}

.btn-cancel:hover {
    background:#5a6268;
    color:#fff;
}

.btn-back {
    background:#007bff;
    color:#fff;
    padding:0.75rem 2rem;
    border:none;
    border-radius:8px;
    font-weight:600;
    text-decoration:none;
    display:inline-block;
    transition:background 0.3s;
}

.btn-back:hover {
    background:#0056b3;
    color:#fff;
}
</style>

<div class="container-fluid">

    <div class="student-back">
        <a href="<?= $back_url ?>" class="text-decoration-none text-dark">
            ← Back to Staff Profile
        </a>
    </div>

    <div class="profile-shell">

        <div class="reset-header">
            <h2>Reset Password</h2>
            <p>Generate and send a new temporary password</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($message_type !== 'success'): ?>

            <div class="staff-info-box">
                <div class="info-row">
                    <div class="info-label">Staff ID:</div>
                    <div class="info-value"><?= htmlspecialchars(s($staff_data, 'staff_id')) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Full Name:</div>
                    <div class="info-value"><?= htmlspecialchars(s($staff_data, 'full_name')) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?= maskEmail(s($staff_data, 'email')) ?></div>
                </div>
            </div>

            <div class="reminder-box">
                <strong>⚠️ Important Reminder</strong>
                <p>A temporary password will be generated and sent to the staff member's registered email address. They will need to log in and change their password immediately after receiving it.</p>
            </div>

            <form method="post" onsubmit="return confirm('Are you sure you want to reset this staff member\'s password? A new temporary password will be sent to their email.');">
                <input type="hidden" name="staff_id" value="<?= htmlspecialchars(s($staff_data, 'staff_id')) ?>">
                <div class="button-group">
                    <button type="submit" name="confirm_reset" class="btn-reset">Confirm Reset</button>
                    <a href="<?= $back_url ?>" class="btn-cancel">Cancel</a>
                </div>
            </form>

        <?php else: ?>

            <div class="button-group">
                <a href="<?= $back_url ?>" class="btn-back">Back to Staff Profile</a>
            </div>

        <?php endif; ?>

    </div>
</div>
</main>

<?php include("../page/footer.php"); ?>