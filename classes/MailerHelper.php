<?php
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailerHelper {
    /**
     * Send Gmail Verification Code for Password Reset
     * 
     * @param string $recipient_email
     * @param string $recipient_name
     * @param string $code
     * @param string $account_type ('Customer' or 'Worker')
     * @return array
     */
    public static function sendGmailVerificationCode($recipient_email, $recipient_name, $code, $account_type = 'Customer') {
        $config_file = __DIR__ . '/../config/mail_config.php';
        if (!file_exists($config_file)) {
            return [
                'success' => false,
                'error' => 'Mail configuration file not found.'
            ];
        }

        $config = include($config_file);

        // Check if user set actual credentials or left placeholder defaults
        $is_placeholder = (empty($config['smtp_username']) || 
                           strpos($config['smtp_username'], 'yourgmail@gmail.com') !== false ||
                           empty($config['smtp_password']) || 
                           strpos($config['smtp_password'], 'xxxx') !== false);

        if ($is_placeholder) {
            // Attempt standard PHP mail() function
            $to = $recipient_email;
            $subject = "WorkerBook " . $account_type . " Account Password Reset Code";
            $email_message = "Hello " . $recipient_name . ",\n\nYour password reset verification code for WorkerBook is: " . $code . "\n\nThis code is valid for 15 minutes.\n\nThank you.";
            $headers = "From: WorkerBook <noreply@workerbook.com>\r\n" .
                       "Reply-To: noreply@workerbook.com\r\n" .
                       "X-Mailer: PHP/" . phpversion();

            @mail($to, $subject, $email_message, $headers);

            return [
                'success' => false,
                'is_placeholder' => true,
                'error' => 'Gmail SMTP credentials are not configured in <code>config/mail_config.php</code>. Please update <code>config/mail_config.php</code> with your Gmail address and 16-character App Password to send real emails to inbox.',
                'code' => $code
            ];
        }

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $config['smtp_host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = trim($config['smtp_username']);
            $mail->Password   = trim(str_replace(' ', '', $config['smtp_password'])); // strip spaces from App Password
            
            $secure = strtolower($config['smtp_secure'] ?? 'tls');
            if ($secure === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->Port       = (int)($config['smtp_port'] ?? 587);
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 12; // 12 seconds timeout

            // Recipients
            $from_email = !empty($config['from_email']) ? $config['from_email'] : $config['smtp_username'];
            $from_name  = !empty($config['from_name'])  ? $config['from_name']  : 'WorkerBook Support';

            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($recipient_email, $recipient_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Your WorkerBook Password Reset Code: " . $code;

            $htmlBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <style>
                    body { font-family: \'Segoe UI\', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
                    .card { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
                    .header { background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); padding: 30px; text-align: center; color: white; }
                    .header h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px; }
                    .header p { margin: 6px 0 0 0; font-size: 14px; opacity: 0.9; }
                    .content { padding: 30px; color: #334155; line-height: 1.6; }
                    .code-box { background: #eef2ff; border: 2px dashed #6366f1; border-radius: 10px; padding: 20px; text-align: center; margin: 25px 0; }
                    .code-number { font-size: 32px; font-weight: 800; color: #4338ca; letter-spacing: 8px; font-family: monospace; }
                    .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="header">
                        <h1>WorkerBook</h1>
                        <p>' . htmlspecialchars($account_type) . ' Account Password Reset</p>
                    </div>
                    <div class="content">
                        <p>Hello <strong>' . htmlspecialchars($recipient_name) . '</strong>,</p>
                        <p>We received a request to reset the password for your WorkerBook ' . strtolower($account_type) . ' account associated with <strong>' . htmlspecialchars($recipient_email) . '</strong>.</p>
                        
                        <div class="code-box">
                            <div style="font-size: 12px; color: #475569; text-transform: uppercase; font-weight: bold; margin-bottom: 6px;">Your 6-Digit Verification Code</div>
                            <div class="code-number">' . htmlspecialchars($code) . '</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 8px;">Valid for 15 minutes</div>
                        </div>

                        <p>If you did not request this password reset, please ignore this email or secure your account.</p>
                    </div>
                    <div class="footer">
                        &copy; ' . date("Y") . ' WorkerBook Services. All rights reserved.
                    </div>
                </div>
            </body>
            </html>';

            $mail->Body    = $htmlBody;
            $mail->AltBody = "Hello " . $recipient_name . ",\n\nYour WorkerBook password reset verification code is: " . $code . "\n\nThis code is valid for 15 minutes.";

            $mail->send();
            return [
                'success' => true,
                'message' => 'Verification code sent to your Gmail address!'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Gmail SMTP Error: ' . $mail->ErrorInfo,
                'code' => $code
            ];
        }
    }
}
?>
