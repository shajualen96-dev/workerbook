<?php
require('../config/autoload.php');
// include("adminheader.php"); // commented out - was causing login redirect

$dao = new DataAccess();

/* -------- OPTIONAL STATUS FILTER (?status=0/2/6/cancelled) -------- */
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : "";

$where = "1=1";
if($statusFilter !== "" && in_array($statusFilter, ["0","2","6","cancelled"]))
{
    if($statusFilter === "cancelled")
    {
        $where .= " AND bstatus NOT IN (0,2,6)";
    }
    else
    {
        $where .= " AND bstatus=".intval($statusFilter);
    }
}

/* -------- FETCH BOOKINGS (with filter applied) -------- */
$bookings = $dao->getData(
    "*",
    "booking",
    $where." ORDER BY bid DESC"
);

/* -------- FETCH ALL BOOKINGS FOR SUMMARY COUNTS -------- */
$allBookings = $dao->getData("*", "booking", "1=1");

$totalCount     = 0;
$pendingCount   = 0;
$approvedCount  = 0;
$completedCount = 0;
$cancelledCount = 0;

if($allBookings)
{
    $totalCount = count($allBookings);
    foreach($allBookings as $b)
    {
        if($b['bstatus'] == 0)      $pendingCount++;
        elseif($b['bstatus'] == 2)  $approvedCount++;
        elseif($b['bstatus'] == 6)  $completedCount++;
        else                        $cancelledCount++;
    }
}

/* Human-readable label for the currently active filter, used in the page subtitle */
$filterLabels = [
    ""          => "All Bookings",
    "0"         => "Pending Bookings",
    "2"         => "Approved Bookings",
    "6"         => "Completed Bookings",
    "cancelled" => "Rejected / Cancelled Bookings"
];
$activeLabel = $filterLabels[$statusFilter] ?? "All Bookings";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Booking Reports &mdash; Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #1e293b;
        --text-muted: #64748b;
        --bg-soft: #f8fafc;
        --border-soft: #e2e8f0;

        --pending: #d97706;
        --pending-bg: rgba(217, 119, 6, 0.12);
        --approved: #16a34a;
        --approved-bg: rgba(22, 163, 74, 0.12);
        --completed: #2563eb;
        --completed-bg: rgba(37, 99, 235, 0.12);
        --cancelled: #dc2626;
        --cancelled-bg: rgba(220, 38, 38, 0.12);
    }

    * { box-sizing: border-box; }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background: linear-gradient(180deg, #f1f5f9 0%, #f8fafc 300px, #f8fafc 100%);
        color: var(--secondary);
        min-height: 100vh;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .top-banner {
        background: linear-gradient(120deg, var(--primary-dark), var(--primary) 60%, #818cf8);
        padding: 48px 20px 90px;
        text-align: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .top-banner::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.10) 0, transparent 45%),
                           radial-gradient(circle at 85% 70%, rgba(255,255,255,0.08) 0, transparent 40%);
    }

    .top-banner h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 36px;
        font-weight: 800;
        margin: 0 0 8px;
        position: relative;
        letter-spacing: -0.5px;
    }

    .top-banner p {
        margin: 0;
        font-size: 15px;
        opacity: 0.92;
        position: relative;
    }

    .main-container {
        max-width: 1300px;
        margin: -60px auto 0;
        padding: 0 20px 60px;
        position: relative;
    }

    /* Summary stat cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 18px;
        margin-bottom: 34px;
        animation: fadeInUp 0.5s ease-out;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 22px 18px;
        text-align: center;
        box-shadow: 0 10px 25px -8px rgba(30, 41, 59, 0.15);
        border: 1px solid var(--border-soft);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px -10px rgba(30, 41, 59, 0.2);
    }

    .stat-card .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 17px;
    }

    .stat-card.total .stat-icon      { background: #eef2ff; color: var(--primary-dark); }
    .stat-card.pending .stat-icon    { background: var(--pending-bg); color: var(--pending); }
    .stat-card.approved .stat-icon   { background: var(--approved-bg); color: var(--approved); }
    .stat-card.completed .stat-icon  { background: var(--completed-bg); color: var(--completed); }
    .stat-card.cancelled .stat-icon  { background: var(--cancelled-bg); color: var(--cancelled); }

    .stat-card .stat-number {
        font-family: 'Poppins', sans-serif;
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
    }

    .stat-card .stat-label {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-top: 6px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        font-weight: 600;
    }

    .stat-card.total .stat-number     { color: var(--secondary); }
    .stat-card.pending .stat-number   { color: var(--pending); }
    .stat-card.approved .stat-number  { color: var(--approved); }
    .stat-card.completed .stat-number { color: var(--completed); }
    .stat-card.cancelled .stat-number { color: var(--cancelled); }

    /* Status menu - tab style */
    .status-menu {
        display: flex;
        gap: 6px;
        background: #fff;
        padding: 6px;
        border-radius: 14px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 6px 18px -10px rgba(30, 41, 59, 0.15);
        margin-bottom: 24px;
        flex-wrap: wrap;
        animation: fadeInUp 0.55s ease-out;
    }

    .status-menu a {
        flex: 1;
        min-width: 130px;
        text-align: center;
        padding: 11px 18px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-muted);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .status-menu a:hover {
        background: var(--bg-soft);
        color: var(--secondary);
    }

    .status-menu a.active {
        background: linear-gradient(120deg, var(--primary-dark), var(--primary));
        color: #fff;
        box-shadow: 0 6px 14px -4px rgba(99, 102, 241, 0.5);
    }

    /* Table card */
    .table-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 10px 30px -12px rgba(30, 41, 59, 0.15);
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out;
    }

    .table-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-soft);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .table-card-header h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: var(--secondary);
    }

    .table-card-header .count-pill {
        background: var(--bg-soft);
        color: var(--text-muted);
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 600;
    }

    .table-scroll {
        overflow-x: auto;
    }

    table.table-modern {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    table.table-modern thead th {
        background: var(--bg-soft);
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        text-align: left;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-soft);
        white-space: nowrap;
    }

    table.table-modern tbody td {
        padding: 16px 20px;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--secondary);
        vertical-align: middle;
    }

    table.table-modern tbody tr {
        transition: background 0.15s ease;
    }

    table.table-modern tbody tr:hover {
        background: #f8fafc;
    }

    table.table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .cell-bid {
        font-weight: 700;
        color: var(--primary-dark);
    }

    .cell-name {
        font-weight: 600;
        color: var(--secondary);
    }

    .cell-phone {
        color: var(--text-muted);
        font-variant-numeric: tabular-nums;
    }

    /* Status badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 13px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    .badge-status-pending   { background: var(--pending-bg);   color: var(--pending); }
    .status-dot-pending     { background: var(--pending); }

    .badge-status-approved  { background: var(--approved-bg);  color: var(--approved); }
    .status-dot-approved    { background: var(--approved); }

    .badge-status-completed{ background: var(--completed-bg); color: var(--completed); }
    .status-dot-completed  { background: var(--completed); }

    .badge-status-cancelled{ background: var(--cancelled-bg); color: var(--cancelled); }
    .status-dot-cancelled  { background: var(--cancelled); }

    .empty-state {
        text-align: center;
        padding: 70px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 40px;
        margin-bottom: 14px;
        display: block;
        color: #cbd5e1;
    }

    .empty-state p {
        font-size: 15px;
        font-weight: 500;
        margin: 0;
    }

    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .btn-back-report {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 10;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 8px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    .btn-back-report:hover {
        background: #ffffff;
        color: var(--primary-dark);
        border-color: #ffffff;
        transform: translateX(-3px);
    }

    @media (max-width: 560px) {
        .top-banner h1 { font-size: 26px; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .status-menu a { min-width: 100px; font-size: 12px; padding: 10px; }
    }
</style>
</head>
<body>

<div class="top-banner">
    <a href="javascript:void(0);" onclick="window.history.length > 1 ? window.history.back() : window.location.href='category.php';" class="btn-back-report">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <h1><i class="fas fa-clipboard-list"></i>&nbsp; Booking Reports</h1>
    <p>Overview of all customer bookings across the platform</p>
</div>

<div class="main-container">

    <!-- SUMMARY CARDS -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="stat-number"><?php echo $totalCount; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card pending">
            <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-number"><?php echo $pendingCount; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card approved">
            <div class="stat-icon"><i class="fas fa-check"></i></div>
            <div class="stat-number"><?php echo $approvedCount; ?></div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card completed">
            <div class="stat-icon"><i class="fas fa-check-double"></i></div>
            <div class="stat-number"><?php echo $completedCount; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card cancelled">
            <div class="stat-icon"><i class="fas fa-xmark"></i></div>
            <div class="stat-number"><?php echo $cancelledCount; ?></div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    <!-- STATUS MENU -->
    <div class="status-menu">
        <a href="adminbookingreport.php" class="<?php echo $statusFilter === '' ? 'active' : ''; ?>">
            <i class="fas fa-list"></i> All
        </a>
        <a href="adminbookingreport.php?status=2" class="<?php echo $statusFilter === '2' ? 'active' : ''; ?>">
            <i class="fas fa-check"></i> Approved
        </a>
        <a href="adminbookingreport.php?status=0" class="<?php echo $statusFilter === '0' ? 'active' : ''; ?>">
            <i class="fas fa-hourglass-half"></i> Pending
        </a>
        <a href="adminbookingreport.php?status=cancelled" class="<?php echo $statusFilter === 'cancelled' ? 'active' : ''; ?>">
            <i class="fas fa-xmark"></i> Rejected
        </a>
        <a href="adminbookingreport.php?status=6" class="<?php echo $statusFilter === '6' ? 'active' : ''; ?>">
            <i class="fas fa-check-double"></i> Completed
        </a>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <div class="table-card-header">
            <h2><?php echo htmlspecialchars($activeLabel); ?></h2>
            <span class="count-pill"><?php echo $bookings ? count($bookings) : 0; ?> record<?php echo ($bookings && count($bookings) == 1) ? '' : 's'; ?></span>
        </div>

        <div class="table-scroll">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Customer Phone</th>
                    <th>Worker Name</th>
                    <th>Worker Phone</th>
                    <th>Job Profile</th>
                    <th>Service Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($bookings)
                {
                    foreach($bookings as $booking)
                    {
                        /* Worker + job info */
                        $worker = $dao->getData("*", "wregistration", "wid=".$booking['wid']);
                        $wname  = $worker[0]['wname'] ?? "";
                        $wphone = $worker[0]['wphone'] ?? "";
                        $jid    = $worker[0]['jid'] ?? "";

                        $job = $dao->getData("*", "job", "jid=".$jid);
                        $jobname = $job[0]['jname'] ?? "";

                        /* Customer info -- adjust table/column names to match your schema */
                        $customer = $dao->getData("*", "cregistration", "crid=".$booking['crid']);
                        $cname  = $customer[0]['cname'] ?? "";
                        $cphone = $customer[0]['cphone'] ?? "";

                        if($booking['bstatus'] == 0)
                        {
                            $status = "<span class='badge-status badge-status-pending'><span class='status-dot status-dot-pending'></span>Pending</span>";
                        }
                        elseif($booking['bstatus'] == 2)
                        {
                            $status = "<span class='badge-status badge-status-approved'><span class='status-dot status-dot-approved'></span>Approved</span>";
                        }
                        elseif($booking['bstatus'] == 6)
                        {
                            $status = "<span class='badge-status badge-status-completed'><span class='status-dot status-dot-completed'></span>Completed</span>";
                        }
                        else
                        {
                            $status = "<span class='badge-status badge-status-cancelled'><span class='status-dot status-dot-cancelled'></span>Rejected</span>";
                        }
                ?>
                        <tr>
                            <td class="cell-bid">#<?php echo htmlspecialchars($booking['bid']); ?></td>
                            <td class="cell-name"><?php echo htmlspecialchars($cname); ?></td>
                            <td class="cell-phone"><?php echo htmlspecialchars($cphone); ?></td>
                            <td class="cell-name"><?php echo htmlspecialchars($wname); ?></td>
                            <td class="cell-phone"><?php echo htmlspecialchars($wphone); ?></td>
                            <td><?php echo htmlspecialchars($jobname); ?></td>
                            <td><?php echo htmlspecialchars($booking['bdate']); ?></td>
                            <td><?php echo $status; ?></td>
                        </tr>
                <?php
                    }
                }
                else
                {
                ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>No bookings found for this filter</p>
                            </div>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        </div>
    </div>

</div>

</body>
</html>
