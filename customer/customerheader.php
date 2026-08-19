<?php
require_once(__DIR__ . '/../config/db_update.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Booking System</title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Global Style Sheet -->
    <link rel="stylesheet" href="../css/professional.css">
    
    <style>
        .custom-navbar {
            width: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 40px;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.02);
        }

        .custom-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 24px;
            color: var(--secondary);
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fadeIn 0.5s ease-out;
        }

        .custom-logo i {
            color: var(--primary);
            filter: drop-shadow(0 0 8px var(--primary-glow));
        }

        .custom-nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .custom-nav-links a {
            color: var(--text-muted);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .custom-nav-links a:hover {
            color: var(--primary);
            background: rgba(79, 70, 229, 0.05);
            transform: translateY(-1px);
        }

        .custom-nav-links a.active {
            color: #ffffff;
            background: var(--primary);
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .user-greeting {
            background: rgba(79, 70, 229, 0.08);
            border: 1px solid rgba(79, 70, 229, 0.2);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 6px;
            white-space: nowrap;
        }

        .plan-badge-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
            margin-left: 4px;
            white-space: nowrap;
        }

        .plan-badge-header.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 2px 8px rgba(245, 158, 11 0.3);
        }

        .plan-badge-header.subscribed {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        .custom-menu-btn {
            display: none;
            color: var(--secondary);
            font-size: 24px;
            cursor: pointer;
            transition: var(--transition);
        }

        /* ---- REPORTS DROPDOWN ---- */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown-toggle {
            cursor: pointer;
            user-select: none;
        }

        .nav-dropdown-toggle .fa-chevron-down {
            font-size: 11px;
            margin-left: 2px;
            transition: transform 0.2s ease;
        }

        .nav-dropdown.open .nav-dropdown-toggle .fa-chevron-down {
            transform: rotate(180deg);
        }

        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            min-width: 220px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            padding: 8px;
            z-index: 1001;
            flex-direction: column;
            gap: 2px;
        }

        .nav-dropdown.open .nav-dropdown-menu {
            display: flex;
            animation: fadeInUp 0.2s ease-out forwards;
        }

        .nav-dropdown-menu a {
            width: 100%;
            justify-content: flex-start;
            font-size: 14px;
            padding: 10px 14px;
        }

        @media(min-width: 993px) {
            .nav-dropdown:hover .nav-dropdown-menu {
                display: flex;
                animation: fadeInUp 0.2s ease-out forwards;
            }

            .nav-dropdown:hover .nav-dropdown-toggle .fa-chevron-down {
                transform: rotate(180deg);
            }
        }

        @media(max-width: 992px) {
            .custom-navbar {
                padding: 16px 24px;
            }

            .custom-menu-btn {
                display: block;
            }

            .custom-nav-links {
                position: absolute;
                top: 75px;
                left: 0;
                width: 100%;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                gap: 5px;
                display: none;
                padding: 20px;
                border-bottom: 1px solid var(--border-color);
            }

            .custom-nav-links.show {
                display: flex;
                animation: fadeInUp 0.3s ease-out forwards;
            }

            .custom-nav-links a {
                width: 100%;
                justify-content: flex-start;
            }

            .user-greeting {
                margin: 10px 0 0 0;
                width: 100%;
                justify-content: center;
            }

            .nav-dropdown {
                width: 100%;
            }

            .nav-dropdown-menu {
                position: static;
                width: 100%;
                box-shadow: none;
                border: none;
                background: rgba(79, 70, 229, 0.04);
                margin-top: 4px;
                padding: 4px;
            }
        }
    </style>
</head>
<body>

<div class="custom-navbar">
    <div style="display: flex; align-items: center; gap: 12px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global" title="Go Back">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <a href="home.php" class="custom-logo" style="text-decoration: none;">
            <i class="fas fa-briefcase"></i> WorkerBook
        </a>
    </div>

    <div class="custom-menu-btn" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>

    <div class="custom-nav-links" id="navLinks">
        <?php
        $current_page = basename($_SERVER['PHP_SELF']);
        $report_pages = ['bookingreports.php', 'cancellationreports.php'];
        $is_customer_logged = isset($_SESSION['crid']);
        ?>
        <a href="home.php" class="<?php echo $current_page == 'home.php' || $current_page == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="category.php" class="<?php echo $current_page == 'category.php' ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> Categories
        </a>

        <?php if ($is_customer_logged): 
            $header_crid = $_SESSION['crid'];
            $header_conn = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $h_count = 0;
            $h_plan_expires = null;
            if(!$header_conn->connect_error) {
                $b_res = $header_conn->query("SELECT COUNT(*) as total FROM booking WHERE crid=$header_crid");
                if($b_res && $b_row = $b_res->fetch_assoc()) {
                    $h_count = intval($b_row['total']);
                }
                $c_res = $header_conn->query("SELECT c_plan_expires FROM cregistration WHERE crid=$header_crid");
                if($c_res && $c_row = $c_res->fetch_assoc()) {
                    $h_plan_expires = $c_row['c_plan_expires'];
                }
                $header_conn->close();
            }
            $h_today = date('Y-m-d');
            $h_is_subscribed = ($h_plan_expires && $h_plan_expires >= $h_today);
        ?>
            <a href="plans.php" class="<?php echo $current_page == 'plans.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Platform Plans
            </a>

            <a href="viewbookings.php" class="<?php echo $current_page == 'viewbookings.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
            <a href="cancellation.php" class="<?php echo $current_page == 'cancellation.php' ? 'active' : ''; ?>">
                <i class="fas fa-times-circle"></i> Cancellations
            </a>

            <div class="nav-dropdown<?php echo in_array($current_page, $report_pages) ? ' open' : ''; ?>" id="reportsDropdown">
                <a href="javascript:void(0);"
                   class="nav-dropdown-toggle <?php echo in_array($current_page, $report_pages) ? 'active' : ''; ?>"
                   onclick="toggleDropdown(event)">
                    <i class="fas fa-chart-bar"></i> Reports <i class="fas fa-chevron-down"></i>
                </a>
                <div class="nav-dropdown-menu">
                    <a href="bookingreports.php" class="<?php echo $current_page == 'bookingreports.php' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-alt"></i> Booking Reports
                    </a>
                    <a href="cancellationreports.php" class="<?php echo $current_page == 'cancellationreports.php' ? 'active' : ''; ?>">
                        <i class="fas fa-times-circle"></i> Cancellation Reports
                    </a>
                </div>
            </div>

            <a href="profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="feedback.php" class="<?php echo $current_page == 'feedback.php' ? 'active' : ''; ?>">
                <i class="fas fa-star"></i> Feedback
            </a>

            <?php if($h_is_subscribed): ?>
                <a href="plans.php" class="plan-badge-header subscribed">
                    <i class="fas fa-crown"></i> Subscribed
                </a>
            <?php elseif($h_count == 0): ?>
                <a href="plans.php" class="plan-badge-header">
                    <i class="fas fa-gift"></i> 1st Booking FREE!
                </a>
            <?php else: ?>
                <a href="plans.php" class="plan-badge-header warning">
                    <i class="fas fa-lock"></i> Plan Required (₹)
                </a>
            <?php endif; ?>

            <div class="user-greeting">
                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['cname']); ?>
            </div>

            <a href="logout.php" style="color: #ef4444;" title="Logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        <?php else: ?>
            <a href="../index.php?view=login&role=customer" style="color: var(--primary); font-weight: 700;">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </a>
            <a href="customerregistration.php" style="background: var(--primary); color: #ffffff; padding: 8px 16px; border-radius: var(--radius-sm); font-weight: 700;">
                <i class="fas fa-user-plus"></i> Register
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleMenu() {
    const navLinks = document.getElementById("navLinks");
    navLinks.classList.toggle("show");
    const menuIcon = document.querySelector(".custom-menu-btn i");
    if(navLinks.classList.contains("show")) {
        menuIcon.classList.replace("fa-bars", "fa-xmark");
    } else {
        menuIcon.classList.replace("fa-xmark", "fa-bars");
    }
}

function toggleDropdown(event) {
    // Only intercept clicks on mobile/tablet where hover isn't available
    if (window.innerWidth <= 992) {
        event.preventDefault();
        document.getElementById("reportsDropdown").classList.toggle("open");
    }
}

// Close the dropdown if the user clicks anywhere outside it (mobile)
document.addEventListener("click", function(e) {
    const dropdown = document.getElementById("reportsDropdown");
    if (window.innerWidth <= 992 && dropdown && !dropdown.contains(e.target)) {
        dropdown.classList.remove("open");
    }
});
</script>

</body>
</html>