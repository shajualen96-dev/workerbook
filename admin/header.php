<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>

    <!-- BOOTSTRAP STYLES -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />

    <!-- FONTAWESOME STYLES -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- GLOBAL STYLES -->
    <link rel="stylesheet" href="../css/professional.css">

    <!-- CUSTOM STYLES -->
    <link href="assets/css/custom.css" rel="stylesheet" />

<style>
        body {
            background-color: var(--background);
            font-family: 'Inter', sans-serif;
        }

        /* Adjust page-wrapper for admin dashboard pages */
        #page-wrapper {
            margin-left: 270px;
            padding: 100px 35px 35px 35px;
            min-height: 100vh;
            background: var(--background);
            transition: var(--transition);
        }

        .admin-topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 270px;
            height: 75px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 90;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: var(--transition);
        }

        .admin-topbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 22px;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-topbar-brand i {
            color: var(--primary);
        }

        .sidebar-dropdown-caret {
            transition: transform 0.2s ease;
        }

        .sidebar-dropdown.open .sidebar-dropdown-caret {
            transform: rotate(180deg);
        }

        .sidebar-submenu .sidebar-item a {
            padding-top: 6px;
            padding-bottom: 6px;
        }

        @media (max-width: 1024px) {
            #page-wrapper {
                margin-left: 80px;
                padding: 100px 20px 20px 20px;
            }
            .admin-topbar {
                left: 80px;
            }
        }
    </style>
</head>

<body>

<div id="wrapper">

    <!-- TOP NAVBAR -->
    <div class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global" title="Go Back">
                <i class="fas fa-arrow-left"></i> Back
            </button>
           
            <div class="admin-topbar-brand">
                <i class="fas fa-sliders-h"></i>
                <span>System Administration</span>
            </div>
        </div>
        <div>
            <a href="logout.php" class="btn-modern btn-danger-modern" style="padding: 8px 16px; font-size: 13px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- SIDEBAR -->
    <div class="dashboard-sidebar">
        
        <div class="sidebar-logo">
            <i class="fas fa-shield-alt" style="color: var(--primary);"></i>
            <span>Admin Panel</span>
        </div>

        <ul class="sidebar-menu">
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>
            
            <li class="sidebar-item <?php echo $current_page == 'category.php' ? 'active' : ''; ?>">
                <a href="category.php">
                    <i class="fas fa-folder-open"></i>
                    <span>Categories</span>
                </a>
            </li>

            <li class="sidebar-item <?php echo $current_page == 'jobs.php' ? 'active' : ''; ?>">
                <a href="jobs.php">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Jobs</span>
                </a>
            </li>

            <li class="sidebar-item <?php echo $current_page == 'viewworkers.php' ? 'active' : ''; ?>">
                <a href="viewworkers.php">
                    <i class="fas fa-user-tie"></i>
                    <span>View Workers</span>
                </a>
            </li>

            <li class="sidebar-item <?php echo $current_page == 'viewcustomers.php' ? 'active' : ''; ?>">
                <a href="viewcustomers.php">
                    <i class="fas fa-users"></i>
                    <span>View Customers</span>
                </a>
            </li>

            <?php
            $report_pages = array('categoryreport.php', 'categoryworkerreport.php');
            $report_active = in_array($current_page, $report_pages);
            ?>
            <li class="sidebar-item sidebar-dropdown <?php echo $report_active ? 'active open' : ''; ?>">
                <a href="#" onclick="toggleSidebarDropdown(event, this)">
                    <i class="fas fa-user-plus"></i>
                    <span>Report</span>
                    <i class="fas fa-chevron-down sidebar-dropdown-caret" style="margin-left: auto; font-size: 11px;"></i>
                </a>
                <ul class="sidebar-submenu" style="<?php echo $report_active ? 'display:block;' : 'display:none;'; ?> list-style: none; padding-left: 34px; margin: 4px 0 0 0;">
                    <li class="sidebar-item <?php echo $current_page == 'categoryreport.php' ? 'active' : ''; ?>" style="margin-bottom: 2px;">
                        <a href="categoryreport.php" style="font-size: 13.5px;">
                            <i class="fas fa-briefcase" style="font-size: 11px;"></i>
                            <span>Customer Report</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo $current_page == 'categoryworkerreport.php' ? 'active' : ''; ?>">
                        <a href="categoryworkerreport.php" style="font-size: 13.5px;">
                            <i class="fas fa-user-tie" style="font-size: 11px;"></i>
                            <span>Worker Report</span>
                        </a>
                    </li>
                     <li class="sidebar-item <?php echo $current_page == 'adminbookingreport.php' ? 'active' : ''; ?>">
                        <a href="adminbookingreport.php" style="font-size: 13.5px;">
                            <i class="fas fa-user-tie" style="font-size: 11px;"></i>
                            <span>Booking Report</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="sidebar-item" style="margin-top: auto;">
                <a href="logout.php" style="color: #f87171;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>

    </div>

</div>

<!-- JQUERY -->
<script src="assets/js/jquery-1.10.2.js"></script>
<!-- BOOTSTRAP JS -->
<script src="assets/js/bootstrap.min.js"></script>
<!-- CUSTOM JS -->
<script src="assets/js/custom.js"></script>

<script>
function toggleSidebarDropdown(e, el) {
    e.preventDefault();
    var li = el.closest('.sidebar-dropdown');
    var submenu = li.querySelector('.sidebar-submenu');
    var isOpen = li.classList.contains('open');

    if (isOpen) {
        li.classList.remove('open');
        submenu.style.display = 'none';
    } else {
        li.classList.add('open');
        submenu.style.display = 'block';
    }
}
</script>

</body>
</html>
