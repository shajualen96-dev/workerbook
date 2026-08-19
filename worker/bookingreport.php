<?php

require('../config/autoload.php');

$dao = new DataAccess();

if(!isset($_SESSION['wid']))
{
    header("Location: ../index.php?view=login&role=worker");
    echo "<script>location.replace('../index.php?view=login&role=worker');</script>";
    exit();
}

$wid = $_SESSION['wid'];

/* WORKER PLATFORM SUBSCRIPTION CHECK */
$worker = $dao->getData("*","wregistration","wid=".$wid);
$w_plan_expires = $worker[0]['w_plan_expires'] ?? null;
$today_str = date('Y-m-d');
if (!$w_plan_expires || $w_plan_expires < $today_str) {
    header("Location: plans.php");
    echo "<script>location.replace('plans.php');</script>";
    exit();
}

include("workerheader.php");

/* ---- DATE FILTER (From Date / To Date) ---- */
$fromDate = isset($_GET['from_date']) ? trim($_GET['from_date']) : "";
$toDate   = isset($_GET['to_date'])   ? trim($_GET['to_date'])   : "";

/* Basic validation: must be YYYY-MM-DD, else ignore */
if($fromDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) { $fromDate = ""; }
if($toDate   && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate))   { $toDate   = ""; }

$whereClause = "wid=".$wid;

if($fromDate !== "")
{
    $whereClause .= " AND bdate >= '".$fromDate." 00:00:00'";
}
if($toDate !== "")
{
    $whereClause .= " AND bdate <= '".$toDate." 23:59:59'";
}

/* GET BOOKINGS FOR THIS WORKER (optionally filtered by date range) */
$bookings = $dao->getData("*","booking",$whereClause);

/* ---- BUILD REPORT ROWS, SPLIT BY STATUS ---- */
$allRows       = array();
$pendingRows   = array();
$approvedRows  = array();
$rejectedRows  = array();
$completedRows = array();

if($bookings)
{
    foreach($bookings as $booking)
    {
        $customer = $dao->getData("*","cregistration","crid=".$booking['crid']);
        $cname  = $customer[0]['cname']  ?? "";
        $cphone = $customer[0]['cphone'] ?? "";

        $worker = $dao->getData("*","wregistration","wid=".$booking['wid']);
        $jid    = $worker[0]['jid'] ?? "";

        $job     = $dao->getData("*","job","jid=".$jid);
        $jobname = $job[0]['jname'] ?? "Not Assigned";

        $bstatus = $booking['bstatus'];

        if($bstatus == 0){
            $statusLabel = "Pending";
            $badgeClass  = "badge-status-pending";
            $dotClass    = "status-dot-pending";
        } elseif($bstatus == 2){
            $statusLabel = "Approved";
            $badgeClass  = "badge-status-approved";
            $dotClass    = "status-dot-approved";
        } elseif($bstatus == 6){
            $statusLabel = "Completed";
            $badgeClass  = "badge-status-completed";
            $dotClass    = "status-dot-completed";
        } else {
            $statusLabel = "Rejected";
            $badgeClass  = "badge-status-cancelled";
            $dotClass    = "status-dot-cancelled";
        }

        $ts = strtotime($booking['bdate']);

        $rowData = array(
            "bid"    => $booking['bid'],
            "cname"  => $cname,
            "cphone" => $cphone,
            "job"    => $jobname,
            "bdate"  => $booking['bdate'],
            "ts"     => $ts,
            "status" => $statusLabel,
            "badge"  => $badgeClass,
            "dot"    => $dotClass
        );

        $allRows[] = $rowData;

        if($bstatus == 0)      { $pendingRows[]   = $rowData; }
        elseif($bstatus == 2)  { $approvedRows[]  = $rowData; }
        elseif($bstatus == 6)  { $completedRows[] = $rowData; }
        else                   { $rejectedRows[]  = $rowData; }
    }
}

/* SORT: most recent first */
$sortByTs = function($a, $b) { return $b['ts'] <=> $a['ts']; };
usort($allRows, $sortByTs);
usort($pendingRows, $sortByTs);
usort($approvedRows, $sortByTs);
usort($rejectedRows, $sortByTs);
usort($completedRows, $sortByTs);

$totalAll       = count($allRows);
$totalPending   = count($pendingRows);
$totalApproved  = count($approvedRows);
$totalRejected  = count($rejectedRows);
$totalCompleted = count($completedRows);

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Report</title>

<style>
        .page-header {
            margin-bottom: 40px;
            text-align: center;
            animation: fadeInUp 0.5s ease-out;
        }

        .page-header h1 {
            font-size: 38px;
            color: var(--secondary);
            font-weight: 800;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 16px;
        }

        .worker-main {
            margin-left: 270px;
            padding: 40px;
            animation: fadeIn 0.4s ease-out;
        }

        .summary-cards {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .summary-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px 26px;
            min-width: 140px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .summary-card .num {
            font-size: 30px;
            font-weight: 800;
            color: var(--secondary);
        }

        .summary-card .label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 4px;
        }

        .summary-card.pending .num   { color: #d97706; }
        .summary-card.approved .num  { color: var(--success, #10b981); }
        .summary-card.rejected .num  { color: var(--error, #ef4444); }
        .summary-card.completed .num{ color: #2563eb; }

        .filter-bar {
            display: flex;
            gap: 15px;
            justify-content: center;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .filter-bar label {
            font-size: 13px;
            font-weight: 600;
            color: var(--secondary, #334155);
        }

        .filter-bar input[type="date"] {
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
        }

        .filter-bar button, .filter-bar a {
            padding: 11px 26px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .filter-bar button {
            background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
            color: #fff;
        }

        .filter-bar a.clear-btn {
            background: #f1f5f9;
            color: var(--secondary, #334155);
        }

        .report-tabs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .report-tab-btn {
            padding: 12px 28px;
            border: none;
            border-radius: 30px;
            background: #f1f5f9;
            color: var(--secondary, #334155);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .report-tab-btn:hover {
            background: #e2e8f0;
        }

        .report-tab-btn.active {
            background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .report-panel {
            display: none;
        }

        .report-panel.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        .no-data-row td {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }

        .badge-status-completed {
            background: rgba(59, 130, 246, 0.12);
            color: #2563eb;
        }

        .status-dot-completed {
            background: #2563eb;
        }

        @media(max-width: 1024px) {
            .worker-main {
                margin-left: 80px;
                padding: 25px;
            }
        }
</style>
</head>

<body>

<div class="worker-main">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="page-header">
        <h1>Booking Report</h1>
        <p>Track your bookings by status — pending, approved, rejected, and completed</p>
    </div>

    <form method="GET" class="filter-bar">
        <div>
            <label for="from_date">From Date</label><br>
            <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>">
        </div>
        <div>
            <label for="to_date">To Date</label><br>
            <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>">
        </div>
        <button type="submit">Apply Filter</button>
        <?php if($fromDate !== "" || $toDate !== "") { ?>
            <a href="?" class="clear-btn">Clear</a>
        <?php } ?>
    </form>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="num"><?php echo $totalAll; ?></div>
            <div class="label">Total Bookings</div>
        </div>
        <div class="summary-card pending">
            <div class="num"><?php echo $totalPending; ?></div>
            <div class="label">Pending</div>
        </div>
        <div class="summary-card approved">
            <div class="num"><?php echo $totalApproved; ?></div>
            <div class="label">Approved</div>
        </div>
        <div class="summary-card rejected">
            <div class="num"><?php echo $totalRejected; ?></div>
            <div class="label">Rejected</div>
        </div>
        <div class="summary-card completed">
            <div class="num"><?php echo $totalCompleted; ?></div>
            <div class="label">Completed</div>
        </div>
    </div>

    <div class="report-tabs">
        <button class="report-tab-btn active" onclick="showReport('all', this)">All Bookings</button>
        <button class="report-tab-btn" onclick="showReport('pending', this)">Pending</button>
        <button class="report-tab-btn" onclick="showReport('approved', this)">Approved</button>
        <button class="report-tab-btn" onclick="showReport('rejected', this)">Rejected</button>
        <button class="report-tab-btn" onclick="showReport('completed', this)">Completed</button>
    </div>

    <?php
    /* Reusable renderer for each status panel */
    function renderBookingTable($rows)
    {
        if(!$rows)
        {
            echo '<tr class="no-data-row"><td colspan="6"><i class="fas fa-calendar-times fa-2x" style="margin-bottom:10px; display:block;"></i>No Booking Records Found</td></tr>';
            return;
        }
        foreach($rows as $row)
        {
            echo '<tr>';
            echo '<td>#'.htmlspecialchars($row['bid']).'</td>';
            echo '<td style="font-weight:600; color: var(--secondary);">'.htmlspecialchars($row['cname']).'</td>';
            echo '<td>'.htmlspecialchars($row['cphone']).'</td>';
            echo '<td>'.htmlspecialchars($row['job']).'</td>';
            echo '<td style="font-weight:500;">'.htmlspecialchars($row['bdate']).'</td>';
            echo '<td><span class="badge-status '.$row['badge'].'"><span class="status-dot '.$row['dot'].'"></span>'.$row['status'].'</span></td>';
            echo '</tr>';
        }
    }
    ?>

    <!-- ALL BOOKINGS -->
    <div id="report-all" class="report-panel active">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 100px;">Request ID</th>
                        <th>Customer Name</th>
                        <th>Contact Phone</th>
                        <th>Job Speciality</th>
                        <th>Service Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php renderBookingTable($allRows); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PENDING -->
    <div id="report-pending" class="report-panel">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 100px;">Request ID</th>
                        <th>Customer Name</th>
                        <th>Contact Phone</th>
                        <th>Job Speciality</th>
                        <th>Service Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php renderBookingTable($pendingRows); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- APPROVED -->
    <div id="report-approved" class="report-panel">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 100px;">Request ID</th>
                        <th>Customer Name</th>
                        <th>Contact Phone</th>
                        <th>Job Speciality</th>
                        <th>Service Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php renderBookingTable($approvedRows); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- REJECTED -->
    <div id="report-rejected" class="report-panel">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 100px;">Request ID</th>
                        <th>Customer Name</th>
                        <th>Contact Phone</th>
                        <th>Job Speciality</th>
                        <th>Service Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php renderBookingTable($rejectedRows); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- COMPLETED -->
    <div id="report-completed" class="report-panel">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 100px;">Request ID</th>
                        <th>Customer Name</th>
                        <th>Contact Phone</th>
                        <th>Job Speciality</th>
                        <th>Service Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php renderBookingTable($completedRows); ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function showReport(type, btn) {
    document.querySelectorAll('.report-panel').forEach(function(panel) {
        panel.classList.remove('active');
    });
    document.querySelectorAll('.report-tab-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    document.getElementById('report-' + type).classList.add('active');
    btn.classList.add('active');
}
</script>

</body>
</html>
