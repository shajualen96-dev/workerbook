<?php
// Gmail SMTP Configuration for WorkerBook Password Verification

return [
    // Gmail SMTP Server Settings
    'smtp_host'   => 'smtp.gmail.com',
    'smtp_port'   => 587, // 587 for TLS, 465 for SSL
    'smtp_secure' => 'tls', // 'tls' or 'ssl'

    // Your Gmail Credentials
    'smtp_username' => 'shajualen96@gmail.com',
    'smtp_password' => 'kogc wppj nhhg zirq',

    // Sender Info
    'from_email' => 'shajualen96@gmail.com',
    'from_name'  => 'WorkerBook Support',

    // Local Development Fallback
    'debug_fallback' => true
];
