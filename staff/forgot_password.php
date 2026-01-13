<?php
include("../config/config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

$message = '';
$message_type = '';
$show_confirmation = false;
$staff_data = null;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Step 1 – Check IC */
    if (isset($_POST['check_ic'])) {
        $staff_ic = $_POST['staff_ic'];

        $stmt = $conn->prepare("SELECT staff_id, full_name, email, staff_ic FROM staff WHERE staff_ic = ?");
        $stmt->bind_param("s", $staff_ic);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $staff_data = $result->fetch_assoc();
            $show_confirmation = true;
        } else {
            $message = "No staff found with the provided IC Number.";
            $message_type = "error";
        }
    }

    /* Step 2 – Confirm Reset */
    if (isset($_POST['confirm_reset'])) {
        $staff_ic = $_POST['staff_ic'];

        $stmt = $conn->prepare("SELECT full_name, email FROM staff WHERE staff_ic = ?");
        $stmt->bind_param("s", $staff_ic);
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
                $mail->Username   = 'utemmyhostel@gmail.com';  // your Gmail
                $mail->Password   = 'nzxazsxwjuctxchr';            // App password
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;

                // Email content
                $mail->setFrom('utemmyhostel@gmail.com', 'MyHostel');
                $mail->addAddress($staff['email'], $staff['full_name']);
                $mail->isHTML(true);
                $mail->Subject = 'MyHostel - Password Reset';
                $mail->Body = "
                    <p>Dear {$staff['full_name']},</p>
                    <p>Your password has been reset.</p>
                    <p><strong>Temporary Password:</strong> {$new_password}</p>
                    <p>Please log in and change it immediately.</p>
                ";

                // Send email
                $mail->send();

                // Only update password if email sent successfully
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE staff SET password = ? WHERE staff_ic = ?");
                $update->bind_param("ss", $hashed, $staff_ic);
                $update->execute();

                $message = "Password reset successful. New password sent to <strong>" . maskEmail($staff['email']) . "</strong>";
                $message_type = "success";

            } catch (Exception $e) {
                $message = "Failed to send reset email: " . $mail->ErrorInfo;
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MyHostel – Forgot Password</title>
<link rel="stylesheet" href="../style_1.css">

<style>
.login-logo{
    width:70px;
    display:block;
    margin:0 auto 12px;
}
.message-box{
    padding:12px;
    border-radius:6px;
    text-align:center;
    font-size:14px;
    margin-bottom:15px;
}
.success{background:#d4edda;color:#155724;}
.error{background:#f8d7da;color:#721c24;}
.warning{background:#fff3cd;color:#856404;}

.confirm-box{
    background:#fff8e1;
    border:1px solid #f1c40f;
    padding:15px;
    border-radius:8px;
    font-size:14px;
    margin-top:10px;
}

.reminder-box{
    background:#e7f3ff;
    border-left:4px solid #2196f3;
    padding:12px 14px;
    border-radius:6px;
    font-size:13px;
    color:#0c5460;
    margin-top:18px;
    line-height:1.5;
}
.reminder-box strong{
    display:block;
    margin-bottom:4px;
}
</style>
</head>

<body>

<div class="device">

    <div class="top-bar">
        <span>myHostel</span>
    </div>

    <div class="page">

        <div class="banner">
            <img src="../images/banner.png" alt="">
        </div>

        <div class="card">

            <div class="card-left">
                <img src="../images/hostel.png" alt="">
            </div>

            <div class="card-right">

                <img src="../logo.png" class="login-logo" alt="Logo">

                <h2 style="text-align:center;">Forgot Password</h2>

                <?php if (!empty($message)): ?>
                    <div class="message-box <?= $message_type ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <?php if ($show_confirmation && $staff_data): ?>

                    <div class="confirm-box">
                        <p><strong>Name:</strong> <?= htmlspecialchars($staff_data['full_name']) ?></p>
                        <p><strong>Email:</strong> <?= maskEmail($staff_data['email']) ?></p>
                        <p><strong>IC:</strong> <?= htmlspecialchars($staff_data['staff_ic']) ?></p>

                        <form method="post">
                            <input type="hidden" name="staff_ic" value="<?= htmlspecialchars($staff_data['staff_ic']) ?>">
                            <button type="submit" name="confirm_reset">Confirm Reset</button>
                            <a href="forgot_password.php" class="btn1">Cancel</a>
                        </form>
                    </div>

                <?php elseif ($message_type !== 'success'): ?>

                    <form method="post">
                        <div class="field">
                            <input type="text" name="staff_ic" required>
                            <span>IC Number</span>
                        </div>

                        <button type="submit" name="check_ic">Continue</button>

                        <div style="text-align:center;margin-top:15px;">
                            <a href="../index.php">← Back to Login</a>
                        </div>
                    </form>

                    <!-- REMINDER -->
                    <div class="reminder-box">
                        <strong>🔔 Reminder</strong>
                        A temporary password will be generated and sent to your registered email address.
                        Please log in and change your password immediately after receiving it.
                    </div>

                <?php else: ?>

                    <div style="text-align:center;">
                        <a href="../index.php">← Go to Login</a>
                    </div>

                <?php endif; ?>

            </div>
        </div>

        <img src="../images/bg-left.png" class="decor-left" alt="">
        <img src="../images/bg-right.png" class="decor-right" alt="">

    </div>
</div>

<div class="footer">
    Copyright © UTEM 2025 · Privacy Policy · Terms & Conditions
</div>

</body>
</html>
