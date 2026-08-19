<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/config/database.php');

// Database Connection
$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);

$message = "";
$message_type = "";

// Active role tab (customer, worker)
$active_role = isset($_GET['role']) && in_array($_GET['role'], ['customer', 'worker']) ? $_GET['role'] : 'customer';

// Determine view mode: 'home' (default common website viewing) or 'login'
$view = isset($_GET['view']) ? $_GET['view'] : (isset($_GET['role']) ? 'login' : 'home');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $view = 'login';
    $submitted_role = $_POST['role_type'] ?? 'customer';
    $active_role = $submitted_role;

    if ($conn && !$conn->connect_error) {
        if ($submitted_role === 'customer') {
            $cgmail = trim($_POST['email']);
            $cpass  = trim($_POST['password']);

            $cgmailEsc = mysqli_real_escape_string($conn, $cgmail);
            $cpassEsc  = mysqli_real_escape_string($conn, $cpass);

            $sql = "SELECT * FROM cregistration WHERE cgmail='$cgmailEsc' AND cpassword='$cpassEsc' AND cstatus='1'";
            $res = mysqli_query($conn, $sql);

            if ($res && mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $_SESSION['crid']  = $row['crid'];
                $_SESSION['cname'] = $row['cname'];
                
                // If redirect parameter exists (e.g. from clicking book worker)
                $redirect = $_GET['redirect'] ?? 'customer/home.php';
                header("Location: " . $redirect);
                exit();
            } else {
                $message = "Invalid Customer credentials or account disabled.";
                $message_type = "error";
            }
        }
        elseif ($submitted_role === 'worker') {
            $wgmail = trim($_POST['email']);
            $wpass  = trim($_POST['password']);

            $wgmailEsc = mysqli_real_escape_string($conn, $wgmail);
            $wpassEsc  = mysqli_real_escape_string($conn, $wpass);

            // Step 1: Verify credentials (Gmail and Password)
            $sql = "SELECT * FROM wregistration WHERE wgmail='$wgmailEsc' AND wpass='$wpassEsc'";
            $res = mysqli_query($conn, $sql);

            if ($res && mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);

                // Step 2: Verify admin approval status (wstatus = 2)
                if (intval($row['wstatus']) === 2) {
                    $_SESSION['wid']   = $row['wid'];
                    $_SESSION['wname'] = $row['wname'];

                    /* CHECK WORKER PLATFORM SUBSCRIPTION */
                    $today_date = date('Y-m-d');
                    $w_plan_expires = $row['w_plan_expires'] ?? null;
                    $is_subscribed = ($w_plan_expires && $w_plan_expires >= $today_date);

                    if (!$is_subscribed) {
                        header("Location: worker/plans.php");
                        exit();
                    }

                    header("Location: worker/home.php");
                    exit();
                } else {
                    $message = "Worker is not approved by admin. Please try again or login after a few minutes.";
                    $message_type = "error";
                }
            } else {
                $message = "Invalid Worker Email or Password.";
                $message_type = "error";
            }
        }
    } else {
        $message = "Database connection error. Please try again later.";
        $message_type = "error";
    }
}

// Fetch categories for public viewing home page
$categories = [];
$workers = [];
if ($conn && !$conn->connect_error) {
    $cat_res = mysqli_query($conn, "SELECT * FROM category ORDER BY cid DESC LIMIT 6");
    if ($cat_res) {
        while ($r = mysqli_fetch_assoc($cat_res)) {
            $categories[] = $r;
        }
    }

    $wrk_res = mysqli_query($conn, "SELECT w.*, j.jname FROM wregistration w LEFT JOIN job j ON w.jid = j.jid WHERE w.wstatus = 2 LIMIT 6");
    if ($wrk_res) {
        while ($r = mysqli_fetch_assoc($wrk_res)) {
            $workers[] = $r;
        }
    }
}

// Helper to check logged-in status
$is_customer = isset($_SESSION['crid']);
$is_worker = isset($_SESSION['wid']);
$is_logged_in = $is_customer || $is_worker;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $view === 'login' ? 'Sign In Portal - WorkerBook' : 'WorkerBook - Professional On-Demand Skilled Worker Booking Platform'; ?></title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Global CSS -->
    <link rel="stylesheet" href="css/professional.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #1e293b;
            --accent: #06b6d4;
            --background: #f8fafc;
            --surface: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #334155;
            --text-muted: #64748b;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: var(--background);
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAVBAR STYLES */
        .site-navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 14px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .brand-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 26px;
            color: var(--secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo i {
            color: var(--primary);
            font-size: 28px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 15px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a:hover {
            color: var(--primary);
            background: rgba(79, 70, 229, 0.06);
        }

        .nav-links a.active {
            color: #ffffff;
            background: var(--primary);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-nav-login {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid rgba(79, 70, 229, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav-login:hover {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }

        .btn-nav-register {
            background: var(--secondary);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav-register:hover {
            background: #0f172a;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.3);
        }

        /* HERO BANNER */
        .public-hero {
            background: linear-gradient(135deg, #e0e7ff 0%, #f5f3ff 50%, #dbeafe 100%);
            padding: 90px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid var(--border-color);
        }

        .public-hero::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.12) 0%, transparent 70%);
            top: -20%;
            left: -10%;
            pointer-events: none;
        }

        .public-hero::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.12) 0%, transparent 70%);
            bottom: -20%;
            right: -10%;
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(79, 70, 229, 0.3);
            color: var(--primary);
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }

        .hero-title {
            font-size: 52px;
            font-weight: 800;
            color: var(--secondary);
            font-family: 'Outfit', sans-serif;
            max-width: 850px;
            margin: 0 auto 20px auto;
            line-height: 1.15;
            letter-spacing: -0.5px;
        }

        .hero-title span {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 20px;
            color: var(--text-muted);
            max-width: 680px;
            margin: 0 auto 35px auto;
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: var(--primary);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 16px;
            padding: 14px 32px;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-hero-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.45);
        }

        .btn-hero-secondary {
            background: #ffffff;
            color: var(--secondary);
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 16px;
            padding: 14px 32px;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-hero-secondary:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            border-color: rgba(79, 70, 229, 0.3);
            color: var(--primary);
        }

        /* STATS STRIP */
        .stats-strip {
            background: #ffffff;
            padding: 30px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .stats-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 36px;
            font-weight: 800;
            color: var(--primary);
            font-family: 'Outfit', sans-serif;
            margin: 0 0 4px 0;
        }

        .stat-item p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
        }

        /* SECTION STYLES */
        .public-section {
            padding: 70px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .section-title-wrap {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title-wrap h2 {
            font-size: 36px;
            font-weight: 800;
            color: var(--secondary);
            font-family: 'Outfit', sans-serif;
            margin-bottom: 12px;
        }

        .section-title-wrap p {
            font-size: 16px;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .public-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .public-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(79, 70, 229, 0.25);
        }

        .public-card-img-wrap {
            height: 200px;
            width: 100%;
            background: #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .public-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .public-card:hover .public-card-img {
            transform: scale(1.05);
        }

        .public-card-body {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .public-card-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--secondary);
            font-family: 'Outfit', sans-serif;
            margin: 0 0 10px 0;
        }

        .public-card-desc {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .btn-card-action {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 15px;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-card-action:hover {
            background: var(--primary-hover);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }

        /* HOW IT WORKS */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        }

        .step-card {
            background: #ffffff;
            padding: 35px 25px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .step-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .step-number {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            font-size: 24px;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
        }

        .step-card h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary);
            font-family: 'Outfit', sans-serif;
            margin-bottom: 10px;
        }

        .step-card p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        /* LOGIN PORTAL VIEW WRAPPER */
        .login-portal-body {
            background: linear-gradient(135deg, #f5f3ff 0%, #e0e7ff 50%, #dbeafe 100%);
            min-height: calc(100vh - 75px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
        }

        .portal-wrapper {
            width: 100%;
            max-width: 480px;
            z-index: 10;
        }

        .role-tabs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            padding: 6px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
        }

        .role-tab {
            padding: 10px 8px;
            text-align: center;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            border: none;
            background: transparent;
        }

        .role-tab.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }

        .card-custom {
            padding: 35px;
            border-radius: 24px;
        }

        .role-header-banner {
            text-align: center;
            margin-bottom: 25px;
        }

        .role-header-banner h2 {
            font-size: 22px;
            margin-bottom: 4px;
            color: var(--secondary);
        }

        .role-header-banner p {
            font-size: 13.5px;
            color: var(--text-muted);
            margin: 0;
        }

        /* FOOTER */
        .site-footer {
            background: var(--secondary);
            color: #94a3b8;
            padding: 50px 20px 30px 20px;
            margin-top: auto;
            border-top: 1px solid #334155;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col h4 {
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            margin: 0 0 20px 0;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            transition: var(--transition);
        }

        .footer-col ul li a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            padding-top: 25px;
            border-top: 1px solid #334155;
            text-align: center;
            font-size: 14px;
        }

        @media(max-width: 768px) {
            .site-navbar {
                padding: 14px 20px;
                flex-direction: column;
                gap: 15px;
            }
            .hero-title {
                font-size: 34px;
            }
            .hero-subtitle {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION BAR WITH HOME BUTTON -->
    <header class="site-navbar">
        <a href="index.php" class="brand-logo">
            <i class="fas fa-briefcase"></i>
            <span>WorkerBook</span>
        </a>

        <ul class="nav-links">
            <li>
                <a href="index.php" class="<?php echo $view === 'home' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li>
                <a href="customer/category.php">
                    <i class="fas fa-th-large"></i> Service Categories
                </a>
            </li>
            <li>
                <a href="customer/jobs.php">
                    <i class="fas fa-tools"></i> Available Jobs
                </a>
            </li>
            <li>
                <a href="customer/login.php">
                    <i class="fas fa-users"></i> Find Workers
                </a>
            </li>
        </ul>

        <div class="nav-actions">
            <?php if ($is_logged_in): ?>
                <?php if ($is_customer): ?>
                    <a href="customer/home.php" class="btn-nav-login">
                        <i class="fas fa-user-circle"></i> Customer Dashboard
                    </a>
                    <a href="customer/logout.php" class="btn-nav-register" style="background: #ef4444;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php elseif ($is_worker): ?>
                    <a href="worker/home.php" class="btn-nav-login">
                        <i class="fas fa-tools"></i> Worker Dashboard
                    </a>
                    <a href="worker/logout.php" class="btn-nav-register" style="background: #ef4444;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                
                <?php endif; ?>
            <?php else: ?>
                <a href="index.php?view=login&role=customer" class="btn-nav-login">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
                <a href="customer/customerregistration.php" class="btn-nav-register">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            <?php endif; ?>
        </div>
    </header>

<?php if ($view === 'home'): ?>

    <!-- HERO BANNER SECTION (COMMON VIEWING HOME PAGE) -->
    <section class="public-hero">
        <div class="hero-badge animate-fade-up">
            <i class="fas fa-check-circle"></i> 100% Verified Skilled Professionals
        </div>
        <h1 class="hero-title animate-fade-up">
            Hire Skilled Workers for Any Home Job, <span>Fast & Hassle-Free</span>
        </h1>
        <p class="hero-subtitle animate-fade-up">
            Explore verified electricians, plumbers, carpenters, painters, and more. Browse services freely without registration—select any option to get started!
        </p>

        <div class="hero-actions animate-fade-up">
            <a href="customer/category.php" class="btn-hero-primary">
                <i class="fas fa-search"></i> Browse Categories
            </a>
            <a href="index.php?view=login&role=customer" class="btn-hero-secondary">
                <i class="fas fa-calendar-check"></i> Book a Specialist
            </a>
            <a href="worker/registration1.php" class="btn-hero-secondary" style="border-color: var(--accent); color: var(--accent);">
                <i class="fas fa-briefcase"></i> Join as Worker
            </a>
        </div>
    </section>

    <!-- SYSTEM STATS STRIP -->
    <section class="stats-strip">
        <div class="stats-grid">
            <div class="stat-item">
                <h3>100+</h3>
                <p>Verified Workers</p>
            </div>
            <div class="stat-item">
                <h3>50+</h3>
                <p>Service Categories</p>
            </div>
            <div class="stat-item">
                <h3>1,000+</h3>
                <p>Successful Bookings</p>
            </div>
            <div class="stat-item">
                <h3>4.9 <i class="fas fa-star" style="font-size: 18px; color: #f59e0b;"></i></h3>
                <p>Customer Rating</p>
            </div>
        </div>
    </section>

    <!-- FEATURED CATEGORIES SECTION -->
    <section class="public-section">
        <div class="section-title-wrap">
            <h2>Explore Popular Categories</h2>
            <p>Select any category to view available specialized roles and qualified service professionals</p>
        </div>

        <div class="cards-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="public-card">
                        <div class="public-card-img-wrap">
                            <img src="uploads/<?php echo htmlspecialchars($cat['cimage']); ?>" 
                                 class="public-card-img" 
                                 alt="<?php echo htmlspecialchars($cat['cname']); ?>"
                                 onerror="this.src='https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=600&q=80';">
                        </div>
                        <div class="public-card-body">
                            <h3 class="public-card-title"><?php echo htmlspecialchars($cat['cname']); ?></h3>
                            <p class="public-card-desc"><?php echo htmlspecialchars($cat['cdescription']); ?></p>
                            <a href="customer/jobs.php?cid=<?php echo $cat['cid']; ?>" class="btn-card-action">
                                <i class="fas fa-eye"></i> View Jobs in Category
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- FALLBACK CARDS IF DB HAS NO CATEGORIES -->
                <div class="public-card">
                    <div class="public-card-img-wrap">
                        <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=600&q=80" class="public-card-img" alt="Electrical Services">
                    </div>
                    <div class="public-card-body">
                        <h3 class="public-card-title">Electrical Repairs</h3>
                        <p class="public-card-desc">Wiring, appliance setup, circuit breaker repairs by certified electricians.</p>
                        <a href="customer/category.php" class="btn-card-action">
                            <i class="fas fa-arrow-right"></i> Explore Category
                        </a>
                    </div>
                </div>

                <div class="public-card">
                    <div class="public-card-img-wrap">
                        <img src="https://images.unsplash.com/photo-1585704032915-c3400ca199e7?auto=format&fit=crop&w=600&q=80" class="public-card-img" alt="Plumbing Services">
                    </div>
                    <div class="public-card-body">
                        <h3 class="public-card-title">Plumbing & Sanitary</h3>
                        <p class="public-card-desc">Leakage fixing, pipe fitting, water tank maintenance & bathroom installations.</p>
                        <a href="customer/category.php" class="btn-card-action">
                            <i class="fas fa-arrow-right"></i> Explore Category
                        </a>
                    </div>
                </div>

                <div class="public-card">
                    <div class="public-card-img-wrap">
                        <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80" class="public-card-img" alt="Carpentry & Furniture">
                    </div>
                    <div class="public-card-body">
                        <h3 class="public-card-title">Carpentry & Woodwork</h3>
                        <p class="public-card-desc">Custom furniture crafting, door & window repairs, kitchen cabinets setup.</p>
                        <a href="customer/category.php" class="btn-card-action">
                            <i class="fas fa-arrow-right"></i> Explore Category
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- FEATURED VERIFIED WORKERS -->
    <?php if (!empty($workers)): ?>
    <section class="public-section" style="background: rgba(241, 245, 249, 0.5); border-radius: var(--radius-lg); margin-bottom: 60px;">
        <div class="section-title-wrap">
            <h2>Top Verified Specialists</h2>
            <p>Browse qualified workers ready to assist with your home project</p>
        </div>

        <div class="cards-grid">
            <?php foreach ($workers as $w): ?>
                <div class="public-card">
                    <div class="public-card-body" style="text-align: center;">
                        <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(79, 70, 229, 0.1); color: var(--primary); font-size: 28px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="public-card-title" style="margin-bottom: 4px;"><?php echo htmlspecialchars($w['wname']); ?></h3>
                        <div style="font-size: 13px; font-weight: 700; color: var(--primary); margin-bottom: 12px;">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($w['jname'] ?? 'Specialist'); ?>
                        </div>
                        <p class="public-card-desc"><?php echo htmlspecialchars($w['wdescription']); ?></p>

                        <!-- If logged in, go to booking directly; if guest, redirect to login -->
                        <?php if ($is_customer): ?>
                            <a href="customer/booking.php?wid=<?php echo $w['wid']; ?>" class="btn-card-action">
                                <i class="fas fa-calendar-check"></i> Book Worker
                            </a>
                        <?php else: ?>
                            <a href="index.php?view=login&role=customer&redirect=customer/booking.php?wid=<?php echo $w['wid']; ?>" class="btn-card-action">
                                <i class="fas fa-lock"></i> Select to Book (Login Required)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- HOW IT WORKS -->
    <section class="public-section">
        <div class="section-title-wrap">
            <h2>How WorkerBook Works</h2>
            <p>Simple 3-step process to get professional help at your doorstep</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Browse Services</h3>
                <p>Explore categories, jobs, and worker profiles directly without needing registration.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Select Your Option</h3>
                <p>Choose your desired worker or service action—if not logged in, you'll be prompted to sign in.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Confirm & Track</h3>
                <p>Set appointment date, receive confirmation from worker, and monitor service status.</p>
            </div>
        </div>
    </section>

<?php else: ?>

    <!-- SINGLE SIGN-IN PORTAL VIEW -->
    <div class="login-portal-body">

        <div class="portal-wrapper animate-fade-up">

            <!-- BRAND LOGO & BACK TO HOME -->
            <div style="text-align: center; margin-bottom: 20px;">
                <a href="index.php" style="display: inline-flex; align-items: center; gap: 8px; color: var(--primary); font-weight: 700; text-decoration: none; background: #ffffff; padding: 8px 18px; border-radius: 9999px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); margin-bottom: 15px;">
                    <i class="fas fa-arrow-left"></i> Back to Public Home
                </a>
                <div style="font-family: 'Outfit', sans-serif; font-size: 32px; font-weight: 800; color: var(--secondary);">
                    <i class="fas fa-briefcase" style="color: var(--primary);"></i> WorkerBook
                </div>
                <div style="color: var(--text-muted); font-size: 14.5px; font-weight: 500; margin-top: 4px;">
                    Single Sign-In Access Portal
                </div>
            </div>

            <!-- ROLE TABS SELECTOR -->
            <div class="role-tabs">
                <button type="button" class="role-tab <?php echo $active_role === 'customer' ? 'active' : ''; ?>" onclick="switchRole('customer')">
                    <i class="fas fa-user"></i>
                    <span>Customer</span>
                </button>
                <button type="button" class="role-tab <?php echo $active_role === 'worker' ? 'active' : ''; ?>" onclick="switchRole('worker')">
                    <i class="fas fa-tools"></i>
                    <span>Worker</span>
                </button>
            </div>

            <!-- MAIN SIGN-IN CARD -->
            <div class="glass-card card-custom">

                <!-- DYNAMIC ROLE BANNER -->
                <div class="role-header-banner" id="roleBanner">
                    <?php if ($active_role === 'customer'): ?>
                        <h2><i class="fas fa-user" style="color: var(--primary); margin-right: 6px;"></i> Customer Sign In</h2>
                        <p>Book skilled workers & track your home service requests</p>
                    <?php else: ?>
                        <h2><i class="fas fa-tools" style="color: var(--primary); margin-right: 6px;"></i> Worker Sign In</h2>
                        <p>Access your worker dashboard & manage client bookings</p>
                    <?php endif; ?>
                </div>

                <!-- ERROR NOTIFICATION -->
                <?php if (!empty($message)): ?>
                    <div class="alert-modern alert-error-modern" style="margin-bottom: 20px;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- SIGN IN FORM -->
                <form method="POST" id="loginForm">
                    <input type="hidden" name="role_type" id="roleInput" value="<?php echo htmlspecialchars($active_role); ?>">

                    <div class="form-group-modern" style="text-align: left;">
                        <label class="form-label-modern" id="emailLabel">
                            <i class="fas fa-envelope" style="margin-right: 6px; color: var(--primary);"></i> Email Address
                        </label>
                        <input type="email" name="email" id="emailInput" class="form-input-modern" 
                               placeholder="<?php 
                                   if ($active_role === 'customer') echo 'customer@example.com';
                                   else echo 'worker@example.com'; 
                               ?>" required>
                    </div>

                    <div class="form-group-modern" style="text-align: left;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label class="form-label-modern" style="margin-bottom: 0;">
                                <i class="fas fa-lock" style="margin-right: 6px; color: var(--primary);"></i> Password
                            </label>
                            <a href="customer/forgot_password.php" id="forgotLink" style="font-size: 13px; color: var(--primary); font-weight: 600; text-decoration: none;">Forgot Password?</a>
                        </div>
                        <input type="password" name="password" class="form-input-modern" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-modern btn-primary-modern w-100" style="margin-top: 10px;">
                        <i class="fas fa-sign-in-alt"></i> Sign In to Account
                    </button>
                </form>

                <!-- REGISTRATION FOOTER LINK -->
                <div id="registerBox" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center; font-size: 14px; color: var(--text-muted);">
                    <?php if ($active_role === 'customer'): ?>
                        New Customer? <a href="customer/customerregistration.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Create Account</a>
                    <?php else: ?>
                        Want to work with us? <a href="worker/registration1.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Register as Worker</a>
                    <?php endif; ?>
                </div>

            </div>

        </div>

    </div>

    <script>
    const roleData = {
        customer: {
            title: '<i class="fas fa-user" style="color: var(--primary); margin-right: 6px;"></i> Customer Sign In',
            subtitle: 'Book skilled workers & track your home service requests',
            placeholder: 'customer@example.com',
            forgotUrl: 'customer/forgot_password.php',
            showForgot: true,
            regText: 'New Customer? <a href="customer/customerregistration.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Create Account</a>'
        },
        worker: {
            title: '<i class="fas fa-tools" style="color: var(--primary); margin-right: 6px;"></i> Worker Sign In',
            subtitle: 'Access your worker dashboard & manage client bookings',
            placeholder: 'worker@example.com',
            forgotUrl: 'worker/forgot_password.php',
            showForgot: true,
            regText: 'Want to work with us? <a href="worker/registration1.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Register as Worker</a>'
        }
    };

    function switchRole(role) {
        document.getElementById('roleInput').value = role;
        
        document.querySelectorAll('.role-tab').forEach(tab => tab.classList.remove('active'));
        if(event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        }

        const data = roleData[role];
        document.getElementById('roleBanner').innerHTML = `<h2>${data.title}</h2><p>${data.subtitle}</p>`;
        document.getElementById('emailInput').placeholder = data.placeholder;
        
        const forgotLink = document.getElementById('forgotLink');
        if (data.showForgot) {
            forgotLink.style.display = 'inline';
            forgotLink.href = data.forgotUrl;
        } else {
            forgotLink.style.display = 'none';
        }

        document.getElementById('registerBox').innerHTML = data.regText;
        
        const url = new URL(window.location);
        url.searchParams.set('view', 'login');
        url.searchParams.set('role', role);
        window.history.pushState({}, '', url);
    }
    </script>

<?php endif; ?>

    <!-- FOOTER SECTION -->
    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-col">
                <h4 style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-briefcase" style="color: var(--primary);"></i> WorkerBook
                </h4>
                <p style="font-size: 14px; line-height: 1.6;">
                    The premier on-demand platform connecting homeowners with certified, top-rated local skilled workers for any home maintenance & repair job.
                </p>
            </div>
            <div class="footer-col">
                <h4>Quick Navigation</h4>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home" style="margin-right: 6px;"></i> Home Page</a></li>
                    <li><a href="customer/category.php"><i class="fas fa-th-large" style="margin-right: 6px;"></i> Service Categories</a></li>
                    <li><a href="customer/jobs.php"><i class="fas fa-tools" style="margin-right: 6px;"></i> Available Jobs</a></li>
                    <li><a href="customer/workers.php"><i class="fas fa-users" style="margin-right: 6px;"></i> Find Workers</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>User Portals</h4>
                <ul>
                    <li><a href="index.php?view=login&role=customer"><i class="fas fa-user" style="margin-right: 6px;"></i> Customer Login</a></li>
                    <li><a href="customer/customerregistration.php"><i class="fas fa-user-plus" style="margin-right: 6px;"></i> Customer Registration</a></li>
                    <li><a href="index.php?view=login&role=worker"><i class="fas fa-hard-hat" style="margin-right: 6px;"></i> Worker Portal</a></li>
                    <li><a href="worker/registration1.php"><i class="fas fa-id-card" style="margin-right: 6px;"></i> Register as Worker</a></li>
                    
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> WorkerBook Platform. All rights reserved. Common Viewing Home Page enabled without forced login.
        </div>
    </footer>

</body>
</html>
