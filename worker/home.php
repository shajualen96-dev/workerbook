<?php
require('../config/autoload.php');

$dao = new DataAccess();

/* WORKER LOGIN CHECK */
if(!isset($_SESSION['wid']))
{
    header("Location: ../index.php?view=login&role=worker");
    echo "<script>location.replace('../index.php?view=login&role=worker');</script>";
    exit();
}

$wid = $_SESSION['wid'];

/* Worker Data */
$worker = $dao->getData("*","wregistration","wid=".$wid);

/* WORKER PLATFORM SUBSCRIPTION CHECK */
$w_plan_expires = $worker[0]['w_plan_expires'] ?? null;
$today_str = date('Y-m-d');
if (!$w_plan_expires || $w_plan_expires < $today_str) {
    header("Location: plans.php");
    echo "<script>location.replace('plans.php');</script>";
    exit();
}

include("workerheader.php");

$wname = $worker[0]['wname'] ?? "";
$wgmail = $worker[0]['wgmail'] ?? "";
$wphone = $worker[0]['wphone'] ?? "";
$wdescription = $worker[0]['wdescription'] ?? "";
$jid = $worker[0]['jid'] ?? "";

/* Job Data */
$job = $dao->getData("*","job","jid=".$jid);
$jname = $job[0]['jname'] ?? "Not Assigned";

/* Booking Counts */
$total = count($dao->getData("*","booking","wid=".$wid)) ?? 0;
$approved = count($dao->getData("*","booking","wid=".$wid." AND bstatus=2")) ?? 0;
$pending = count($dao->getData("*","booking","wid=".$wid." AND bstatus=0")) ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Worker Home</title>

<style>
        .worker-main {
            margin-left: 270px;
            padding: 40px;
            animation: fadeIn 0.4s ease-out;
        }

        .worker-hero {
            background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
            color: #ffffff;
            padding: 45px;
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.2);
            margin-bottom: 35px;
            position: relative;
            overflow: hidden;
        }

        .worker-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 90% 10%, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }

        .worker-hero h1 {
            color: #ffffff;
            font-size: 38px;
            margin-bottom: 10px;
        }

        .worker-hero p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 16px;
            max-width: 600px;
            line-height: 1.5;
        }

        .metric-card {
            background: #ffffff;
            border-radius: var(--radius-md);
            padding: 25px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .metric-info h3 {
            font-size: 14px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .metric-info h1 {
            font-size: 38px;
            color: var(--secondary);
            font-weight: 800;
        }

        .metric-icon {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .metric-blue { border-left: 5px solid var(--primary); }
        .metric-blue .metric-icon { background: rgba(79, 70, 229, 0.1); color: var(--primary); }

        .metric-green { border-left: 5px solid var(--success); }
        .metric-green .metric-icon { background: rgba(16, 185, 129, 0.1); color: var(--success); }

        .metric-orange { border-left: 5px solid var(--warning); }
        .metric-orange .metric-icon { background: rgba(245, 158, 11, 0.1); color: var(--warning); }

        .worker-profile-card {
            margin-top: 35px;
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 35px;
        }

        .worker-profile-card h2 {
            font-size: 24px;
            color: var(--secondary);
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-grid-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .profile-grid-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
        }

        .profile-grid-item i {
            color: var(--primary);
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .profile-grid-item span.label {
            font-weight: 700;
            color: var(--text-muted);
            min-width: 120px;
        }

        @media(max-width: 1024px) {
            .worker-main {
                margin-left: 80px;
                padding: 25px;
            }
        }

        @media(max-width: 768px) {
            .profile-grid-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="worker-main">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='../index.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <!-- HERO -->
    <div class="worker-hero animate-fade-up">
        <h1>Welcome back, <?php echo htmlspecialchars($wname); ?>! </h1>
        <p>You can manage and view your active schedule, respond to user booking requests, and inspect ratings on your worker panel.</p>
    </div>

    <!-- METRICS -->
    <div class="grid-modern animate-fade-up" style="margin-top: 0;">
        
        <div class="metric-card metric-blue">
            <div class="metric-info">
                <h3>Total Bookings</h3>
                <h1><?php echo $total; ?></h1>
            </div>
            <div class="metric-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>

        <div class="metric-card metric-green">
            <div class="metric-info">
                <h3>Approved Jobs</h3>
                <h1><?php echo $approved; ?></h1>
            </div>
            <div class="metric-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="metric-card metric-orange">
            <div class="metric-info">
                <h3>Pending Actions</h3>
                <h1><?php echo $pending; ?></h1>
            </div>
            <div class="metric-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>

    </div>

    <!-- PROFILE DETAILS -->
    <div class="worker-profile-card animate-fade-up">
        
        <h2><i class="fas fa-id-card"></i> Profile Overview</h2>

        <ul class="profile-grid-list">
            
            <li class="profile-grid-item">
                <i class="fas fa-user"></i>
                <span class="label">Full Name:</span>
                <span class="val"><?php echo htmlspecialchars($wname); ?></span>
            </li>

            <li class="profile-grid-item">
                <i class="fas fa-envelope"></i>
                <span class="label">Email address:</span>
                <span class="val"><?php echo htmlspecialchars($wgmail); ?></span>
            </li>

            <li class="profile-grid-item">
                <i class="fas fa-phone"></i>
                <span class="label">Phone Contact:</span>
                <span class="val"><?php echo htmlspecialchars($wphone); ?></span>
            </li>

            <li class="profile-grid-item">
                <i class="fas fa-tools"></i>
                <span class="label">Job Category:</span>
                <span class="val" style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($jname); ?></span>
            </li>

            <li class="profile-grid-item" style="grid-column: 1 / -1; align-items: flex-start;">
                <i class="fas fa-info-circle" style="margin-top: 3px;"></i>
                <span class="label">Worker Bio:</span>
                <span class="val" style="color: var(--text-muted); line-height: 1.5;"><?php echo htmlspecialchars($wdescription); ?></span>
            </li>

        </ul>

        <div style="display: flex; gap: 15px;">
            <a href="bookingapproval.php" class="btn-modern btn-primary-modern">
                <i class="fas fa-calendar-alt"></i> Manage Bookings
            </a>
            <a href="profile.php" class="btn-modern btn-secondary-modern">
                <i class="fas fa-user-edit"></i> Edit Profile
            </a>
        </div>

    </div>

</div>

</body>
</html>