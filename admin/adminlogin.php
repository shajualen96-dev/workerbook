<?php
session_start();

// If already logged in as admin, redirect to category page
if (isset($_SESSION['admin'])) {
    header("Location: category.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_email = trim($_POST['email'] ?? '');
    $admin_pass  = trim($_POST['password'] ?? '');

    if (($admin_email === "shajualen96@gmail.com" || $admin_email === "shajualen@gmail.com") && $admin_pass === "alen2005") {
        $_SESSION['admin'] = $admin_email;
        header("Location: category.php");
        exit();
    } else {
        $message = "Invalid Admin Email or Password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Sign In - WorkerBook</title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Global CSS -->
    <link rel="stylesheet" href="../css/professional.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #0f172a;
            --background: #f8fafc;
            --surface: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #334155;
            --text-muted: #64748b;
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .admin-login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .admin-login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            border-radius: 24px;
            padding: 40px 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .admin-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .admin-badge {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);
            color: #ffffff;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 16px;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
        }

        .admin-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: var(--secondary);
            margin: 0 0 6px 0;
        }

        .admin-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin: 0;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 16px;
        }

        .form-input {
            width: 100%;
            padding: 13px 16px 13px 46px;
            font-size: 14.5px;
            font-family: 'Inter', sans-serif;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            background: #ffffff;
            color: var(--secondary);
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 16px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.45);
        }

        .back-home-wrap {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .btn-back-home {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back-home:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>

    <div class="admin-login-wrapper">
        <div class="admin-login-card">

            <div class="admin-header">
                <div class="admin-badge">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1>Admin Control Panel</h1>
                <p>WorkerBook Administration Sign In</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="adminlogin.php">
                <div class="form-group">
                    <label class="form-label">Admin Email Address</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-input" placeholder="admin@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Admin Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Sign In to Admin Panel
                </button>
            </form>

            

        </div>
    </div>

</body>
</html>