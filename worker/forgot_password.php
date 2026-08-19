<?php
session_start();
include("dbcon.php");
require_once("../classes/MailerHelper.php");

$message = "";
$message_type = "";
$step = isset($_SESSION['w_reset_step']) ? $_SESSION['w_reset_step'] : 1;

// Reset flow if requested
if (isset($_GET['action']) && $_GET['action'] == 'reset') {
    unset($_SESSION['w_reset_gmail']);
    unset($_SESSION['w_reset_code']);
    unset($_SESSION['w_reset_time']);
    unset($_SESSION['w_reset_step']);
    unset($_SESSION['w_mail_error']);
    unset($_SESSION['w_mail_sent']);
    $step = 1;
    header("Location: forgot_password.php");
    exit();
}

// Step 1: Send Gmail Code
if (isset($_POST['send_code'])) {
    $wgmail = trim($_POST['wgmail']);
    $wgmailEsc = mysqli_real_escape_string($conn, $wgmail);

    $sql = "SELECT * FROM wregistration WHERE wgmail='$wgmailEsc' AND wstatus=2";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $code = sprintf("%06d", mt_rand(100000, 999999));

        $_SESSION['w_reset_gmail'] = $wgmail;
        $_SESSION['w_reset_code'] = $code;
        $_SESSION['w_reset_time'] = time();
        $_SESSION['w_reset_step'] = 2;
        $step = 2;

        // Send email via Gmail SMTP / PHPMailer
        $mail_res = MailerHelper::sendGmailVerificationCode($wgmail, $row['wname'], $code, 'Worker');

        if (!empty($mail_res['success'])) {
            $_SESSION['w_mail_sent'] = true;
            unset($_SESSION['w_mail_error']);
            $message = "Verification code has been sent to your Gmail address <strong>" . htmlspecialchars($wgmail) . "</strong>! Please check your inbox or spam folder.";
            $message_type = "success";
        } else {
            $_SESSION['w_mail_sent'] = false;
            $_SESSION['w_mail_error'] = $mail_res['error'] ?? '';
            $message = "Verification code generated for <strong>" . htmlspecialchars($wgmail) . "</strong>.";
            $message_type = "info";
        }
    } else {
        $message = "No approved worker account found with this Gmail address.";
        $message_type = "error";
    }
}

// Step 2: Verify Code & Change Password
if (isset($_POST['reset_password'])) {
    $entered_code = trim($_POST['code']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $wgmail = $_SESSION['w_reset_gmail'] ?? '';
    $wgmailEsc = mysqli_real_escape_string($conn, $wgmail);

    if (empty($wgmail) || !isset($_SESSION['w_reset_code'])) {
        $message = "Session expired. Please request a new verification code.";
        $message_type = "error";
        $step = 1;
    } else if ($entered_code !== $_SESSION['w_reset_code']) {
        $message = "Invalid verification code! Please check the code sent to your Gmail.";
        $message_type = "error";
    } else if ((time() - $_SESSION['w_reset_time']) > 900) {
        $message = "Verification code has expired. Please request a new code.";
        $message_type = "error";
    } else if (empty($new_password)) {
        $message = "Please enter a new password.";
        $message_type = "error";
    } else if ($new_password !== $confirm_password) {
        $message = "Passwords do not match!";
        $message_type = "error";
    } else {
        $new_password_esc = mysqli_real_escape_string($conn, $new_password);
        $update_sql = "UPDATE wregistration SET wpass='$new_password_esc' WHERE wgmail='$wgmailEsc'";
        if (mysqli_query($conn, $update_sql)) {
            unset($_SESSION['w_reset_gmail']);
            unset($_SESSION['w_reset_code']);
            unset($_SESSION['w_reset_time']);
            unset($_SESSION['w_reset_step']);
            unset($_SESSION['w_mail_error']);
            unset($_SESSION['w_mail_sent']);
            $step = 3;
            $message = "Your password has been reset successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to update password. Please try again.";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Forgot Password - WorkerBook</title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #1f2937;
            --text-muted: #6b7280;
            --bg-color: #f5f3ff;
        }

        body {
            background: linear-gradient(135deg, #f5f3ff 0%, #e0e7ff 50%, #dbeafe 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            position: relative;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            top: -10%;
            left: -10%;
            opacity: 0.15;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            bottom: -15%;
            right: -10%;
            opacity: 0.1;
            pointer-events: none;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            z-index: 10;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .login-box-custom {
            padding: 40px;
            text-align: center;
        }

        .login-logo {
            font-size: 36px;
            color: var(--secondary);
            font-weight: 800;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .login-logo i {
            color: var(--primary);
            filter: drop-shadow(0 0 10px rgba(79, 70, 229, 0.2));
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .alert-modern {
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            text-align: center;
        }

        .alert-error-modern {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .alert-success-modern {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-info-modern {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .form-group-modern {
            margin-bottom: 20px;
        }

        .form-label-modern {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .form-input-modern {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 14px;
            background: #fff;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .form-input-modern:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .btn-modern {
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary-modern {
            background: var(--primary);
            color: white;
        }

        .btn-primary-modern:hover {
            background: var(--primary-hover);
        }

        .w-100 { width: 100%; }

        .code-banner {
            padding: 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 22px;
            text-align: center;
            line-height: 1.5;
            border: 1px dashed;
        }

        .code-banner-success {
            background: rgba(16, 185, 129, 0.08);
            border-color: #10b981;
            color: #065f46;
        }

        .code-banner-notice {
            background: #fffbeb;
            border-color: #f59e0b;
            color: #92400e;
        }

        .code-badge {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 4px;
        }

        .register-link {
            margin-top: 25px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .register-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .step-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: var(--primary);
            width: 24px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<a href="javascript:void(0);" onclick="window.history.length > 1 ? window.history.back() : window.location.href='login.php';" class="btn-back-global btn-back-floating">
    <i class="fas fa-arrow-left"></i> Back
</a>

<div class="login-container">

    <div class="login-logo">
        <i class="fas fa-tools"></i> WorkerBook
    </div>
    <div class="login-subtitle">Worker Account Recovery</div>

    <div class="glass-card login-box-custom">

        <div class="step-indicator">
            <div class="step-dot <?php echo $step >= 1 ? 'active' : ''; ?>"></div>
            <div class="step-dot <?php echo $step >= 2 ? 'active' : ''; ?>"></div>
            <div class="step-dot <?php echo $step == 3 ? 'active' : ''; ?>"></div>
        </div>

        <?php if ($message != ""): ?>
            <div class="alert-modern <?php 
                if ($message_type == 'error') echo 'alert-error-modern';
                elseif ($message_type == 'info') echo 'alert-info-modern';
                else echo 'alert-success-modern'; 
            ?>" style="margin-bottom: 20px;">
                <i class="fas <?php 
                    if ($message_type == 'error') echo 'fa-exclamation-circle';
                    elseif ($message_type == 'info') echo 'fa-info-circle';
                    else echo 'fa-check-circle'; 
                ?>"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <!-- STEP 1: ENTER GMAIL -->
            <h2 style="color: var(--secondary); margin-top: 0; margin-bottom: 10px; text-align: center;">Forgot Password?</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 25px; text-align: center;">Enter your registered worker Gmail address to receive a 6-digit verification code.</p>

            <form method="POST">
                <div class="form-group-modern" style="text-align: left;">
                    <label class="form-label-modern"><i class="fas fa-envelope" style="margin-right: 6px; color: var(--primary);"></i> Gmail Address</label>
                    <input type="email" name="wgmail" class="form-input-modern" placeholder="worker@example.com" required>
                </div>

                <button type="submit" name="send_code" class="btn-modern btn-primary-modern w-100" style="margin-top: 10px;">
                    <i class="fas fa-paper-plane"></i> Send Verification Code
                </button>
            </form>

        <?php elseif ($step == 2): ?>
            <!-- STEP 2: ENTER CODE & NEW PASSWORD -->
            <h2 style="color: var(--secondary); margin-top: 0; margin-bottom: 10px; text-align: center;">Verify Gmail Code</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px; text-align: center;">Enter the 6-digit code sent to your Gmail and set a new password.</p>

            <?php if (!empty($_SESSION['w_mail_sent'])): ?>
                <div class="code-banner code-banner-success">
                    <i class="fas fa-envelope-open-text" style="font-size: 22px; color: #10b981; margin-bottom: 6px;"></i><br>
                    Verification code sent to <strong><?php echo htmlspecialchars($_SESSION['w_reset_gmail'] ?? ''); ?></strong><br>
                    <span style="font-size: 12px; opacity: 0.9;">Please check your Gmail inbox (and Spam folder) for the 6-digit verification code.</span>
                </div>
            <?php else: ?>
                <div class="code-banner code-banner-notice">
                    <i class="fas fa-info-circle" style="font-size: 18px; color: #f59e0b; margin-bottom: 4px;"></i><br>
                    Verification code generated for <strong><?php echo htmlspecialchars($_SESSION['w_reset_gmail'] ?? ''); ?></strong><br>
                    <?php if (!empty($_SESSION['w_mail_error'])): ?>
                        <div style="font-size: 12px; margin-top: 6px; text-align: left; background: #fef3c7; padding: 8px; border-radius: 6px;">
                            <?php echo $_SESSION['w_mail_error']; ?>
                        </div>
                    <?php endif; ?>
                  
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group-modern" style="text-align: left;">
                    <label class="form-label-modern"><i class="fas fa-key" style="margin-right: 6px; color: var(--primary);"></i> 6-Digit Verification Code</label>
                    <input type="text" name="code" class="form-input-modern" placeholder="123456" maxlength="6" pattern="[0-9]{6}" required style="letter-spacing: 4px; font-weight: bold; text-align: center; font-size: 18px;">
                </div>

                <div class="form-group-modern" style="text-align: left;">
                    <label class="form-label-modern"><i class="fas fa-lock" style="margin-right: 6px; color: var(--primary);"></i> New Password</label>
                    <input type="password" name="new_password" class="form-input-modern" placeholder="Enter new password" required>
                </div>

                <div class="form-group-modern" style="text-align: left;">
                    <label class="form-label-modern"><i class="fas fa-shield-alt" style="margin-right: 6px; color: var(--primary);"></i> Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-input-modern" placeholder="Confirm new password" required>
                </div>

                <button type="submit" name="reset_password" class="btn-modern btn-primary-modern w-100" style="margin-top: 10px;">
                    <i class="fas fa-check-circle"></i> Reset Password & Sign In
                </button>
            </form>

            <div style="margin-top: 15px; text-align: center;">
                <a href="forgot_password.php?action=reset" style="font-size: 13px; color: var(--text-muted); text-decoration: none;">
                    <i class="fas fa-redo"></i> Resend code or try different Gmail
                </a>
            </div>

        <?php elseif ($step == 3): ?>
            <!-- STEP 3: SUCCESS -->
            <div style="text-align: center; padding: 20px 0;">
                <i class="fas fa-check-circle" style="font-size: 64px; color: #10b981; margin-bottom: 20px;"></i>
                <h2 style="color: var(--secondary); margin-bottom: 10px;">Password Reset Done!</h2>
                <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 30px;">Your password has been successfully updated. You can now access your worker account.</p>

                <a href="login.php" class="btn-modern btn-primary-modern w-100" style="display: block; text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i> Proceed to Worker Sign In
                </a>
            </div>
        <?php endif; ?>

        <div class="register-link" style="text-align: center;">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

</div>

</body>
</html>
