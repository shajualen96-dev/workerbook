<?php
require_once(__DIR__ . '/../config/db_update.php');

$current_page = basename($_SERVER['PHP_SELF']);
$wh_wid = $_SESSION['wid'] ?? null;
$wh_is_subscribed = false;

if ($wh_wid) {
    $wh_conn = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$wh_conn->connect_error) {
        $wh_res = $wh_conn->query("SELECT w_plan_expires FROM wregistration WHERE wid=" . intval($wh_wid));
        if ($wh_res && $wh_row = $wh_res->fetch_assoc()) {
            $wh_expiry = $wh_row['w_plan_expires'];
            $wh_today = date('Y-m-d');
            $wh_is_subscribed = ($wh_expiry && $wh_expiry >= $wh_today);
        }
        $wh_conn->close();
    }
}

if ($wh_wid && !$wh_is_subscribed && $current_page !== 'plans.php' && $current_page !== 'logout.php') {
    header("Location: plans.php");
    echo "<script>location.replace('plans.php');</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Panel</title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Global CSS stylesheet -->
    <link rel="stylesheet" href="../css/professional.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="dashboard-sidebar">
    <div style="padding: 15px 20px 0 20px; display: flex; gap: 8px; margin-bottom: 15px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global" style="flex: 1; justify-content: center;" title="Go Back">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <a href="home.php" class="btn-back-global" style="flex: 1; justify-content: center;" title="Worker Dashboard">
            <i class="fas fa-home"></i> Home
        </a>
    </div>
    
    <div class="sidebar-logo">
        <i class="fas fa-user-cog" style="color: var(--accent);"></i>
        <span>Worker Portal</span>
    </div>

    <ul class="sidebar-menu">
        <li class="sidebar-item <?php echo $current_page == 'home.php' ? 'active' : ''; ?>">
            <a href="home.php">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
            <a href="profile.php">
                <i class="fas fa-id-card"></i>
                <span>My Profile</span>
            </a>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'bookingapproval.php' ? 'active' : ''; ?>">
            <a href="bookingapproval.php">
                <i class="fas fa-calendar-alt"></i>
                <span>Bookings</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo $current_page == 'bookingreport.php' ? 'active' : ''; ?>">
            <a href="bookingreport.php">
                <i class="fas fa-star-half-alt"></i>
                <span>Reports</span>
            </a>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'plans.php' ? 'active' : ''; ?>">
            <a href="plans.php" style="<?php echo !$wh_is_subscribed ? 'color: #ef4444; font-weight: 700;' : ''; ?>">
                <i class="fas fa-tags" style="<?php echo !$wh_is_subscribed ? 'color: #ef4444;' : ''; ?>"></i>
                <span>Subscription Plans</span>
                <?php if(!$wh_is_subscribed): ?>
                    <span style="background: #ef4444; color: #ffffff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 10px; margin-left: auto;">REQUIRED</span>
                <?php endif; ?>
            </a>
        </li>

        <li class="sidebar-item <?php echo $current_page == 'viewfeedback.php' ? 'active' : ''; ?>">
            <a href="viewfeedback.php">
                <i class="fas fa-star-half-alt"></i>
                <span>Reviews</span>
            </a>
        </li>

        <li class="sidebar-item" style="margin-top: auto;">
            <a href="logout.php" style="color: #f87171;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>

</div>

</body>
</html>