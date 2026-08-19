<?php
require('../config/autoload.php');
include("customerheader.php");

$dao = new DataAccess();

if(!isset($_SESSION['crid']))
{
    header("Location: login.php");
    exit();
}

$crid = $_SESSION['crid'];

/* DATE RANGE FILTER (optional) */
function isValidDate($d)
{
    // Expect strict YYYY-MM-DD (what an <input type="date"> sends)
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return false;
    $parts = explode("-", $d);
    return checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]);
}

$fromDateRaw = $_GET['from_date'] ?? "";
$toDateRaw   = $_GET['to_date']   ?? "";

$fromDate = isValidDate($fromDateRaw) ? $fromDateRaw : "";
$toDate   = isValidDate($toDateRaw)   ? $toDateRaw   : "";

$where = "crid=".$crid;

if($fromDate !== "" && $toDate !== "")
{
    $where .= " AND bdate BETWEEN '".$fromDate."' AND '".$toDate."'";
}
elseif($fromDate !== "")
{
    $where .= " AND bdate >= '".$fromDate."'";
}
elseif($toDate !== "")
{
    $where .= " AND bdate <= '".$toDate."'";
}

/* GET BOOKINGS FOR THIS CUSTOMER (FILTERED BY DATE RANGE IF SET) */
$bookings = $dao->getData(
    "*",
    "booking",
    $where
);

/* STATUS LABEL HELPER */
function statusLabel($bstatus)
{
    if($bstatus == 0)      return "Pending";
    elseif($bstatus == 2)  return "Approved";
    elseif($bstatus == 6)  return "Completed";
    else                   return "Rejected";
}

/* BUILD REPORT ROWS: date-wise / month-wise / year-wise, grouped per worker */
$dateReport  = array();
$monthReport = array();
$yearReport  = array();

if($bookings)
{
    foreach($bookings as $booking)
    {
        $worker = $dao->getData("*","wregistration","wid=".$booking['wid']);
        $wname  = $worker[0]['wname'] ?? "Unknown";
        $jid    = $worker[0]['jid'] ?? "";

        $job     = $dao->getData("*","job","jid=".$jid);
        $jobname = $job[0]['jname'] ?? "Not Assigned";

        $bdate = $booking['bdate'];
        $ts    = strtotime($bdate);
        $day   = date("Y-m-d", $ts);
        $month = date("F Y", $ts);
        $year  = date("Y", $ts);

        $status = statusLabel($booking['bstatus']);

        /* ---- DATE WISE ---- */
        $dkey = $day."|".$booking['wid'];
        if(!isset($dateReport[$dkey]))
        {
            $dateReport[$dkey] = array(
                "period"=>$day, "worker"=>$wname, "job"=>$jobname,
                "total"=>0, "pending"=>0, "approved"=>0, "completed"=>0, "rejected"=>0
            );
        }
        $dateReport[$dkey]["total"]++;
        $dateReport[$dkey][strtolower($status)]++;

        /* ---- MONTH WISE ---- */
        $mkey = $month."|".$booking['wid'];
        if(!isset($monthReport[$mkey]))
        {
            $monthReport[$mkey] = array(
                "period"=>$month, "worker"=>$wname, "job"=>$jobname,
                "total"=>0, "pending"=>0, "approved"=>0, "completed"=>0, "rejected"=>0
            );
        }
        $monthReport[$mkey]["total"]++;
        $monthReport[$mkey][strtolower($status)]++;

        /* ---- YEAR WISE ---- */
        $ykey = $year."|".$booking['wid'];
        if(!isset($yearReport[$ykey]))
        {
            $yearReport[$ykey] = array(
                "period"=>$year, "worker"=>$wname, "job"=>$jobname,
                "total"=>0, "pending"=>0, "approved"=>0, "completed"=>0, "rejected"=>0
            );
        }
        $yearReport[$ykey]["total"]++;
        $yearReport[$ykey][strtolower($status)]++;
    }
}

/* SORT: most recent period first */
krsort($dateReport);
krsort($monthReport);
krsort($yearReport);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Booking Reports</title>

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

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .badge-status-completed {
            background: rgba(59, 130, 246, 0.12);
            color: #2563eb;
        }

        .status-dot-completed {
            background: #2563eb;
        }

        .report-tabs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 25px;
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
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .report-panel {
            display: none;
        }

        .report-panel.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        .count-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin: 2px;
        }

        .count-pending   { background: rgba(245, 158, 11, 0.12); color: #b45309; }
        .count-approved  { background: rgba(16, 185, 129, 0.12); color: #047857; }
        .count-completed { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
        .count-rejected  { background: rgba(239, 68, 68, 0.12);  color: #b91c1c; }

        .no-data-row td {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }

        .date-filter-bar {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 18px;
        }

        .date-filter-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .date-filter-field label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted, #64748b);
        }

        .date-filter-field input[type="date"] {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .date-filter-apply {
            padding: 10px 22px;
            border: none;
            cursor: pointer;
        }

        .date-filter-clear {
            font-size: 14px;
            color: #b91c1c;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            padding-bottom: 10px;
        }

        .date-filter-clear:hover {
            text-decoration: underline;
        }

        .date-filter-summary {
            text-align: center;
            color: var(--text-muted, #64748b);
            font-size: 14px;
            margin-bottom: 20px;
        }
</style>
</head>
<body>

<div class="main-container">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="page-header">
        <h1>Booking Reports</h1>
        <p>View your worker bookings by date, month, and year</p>
    </div>

    <form method="GET" action="bookingreports.php" class="date-filter-bar">
        <div class="date-filter-field">
            <label for="from_date">From Date</label>
            <input type="date" id="from_date" name="from_date"
                   value="<?php echo htmlspecialchars($fromDate); ?>"
                   max="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="date-filter-field">
            <label for="to_date">To Date</label>
            <input type="date" id="to_date" name="to_date"
                   value="<?php echo htmlspecialchars($toDate); ?>"
                   max="<?php echo date('Y-m-d'); ?>">
        </div>
        <button type="submit" class="btn-edit date-filter-apply">
            <i class="fas fa-filter"></i> Apply
        </button>
        <?php if($fromDate !== "" || $toDate !== "") { ?>
            <a href="bookingreports.php" class="date-filter-clear">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php } ?>
    </form>

    <?php if($fromDate !== "" || $toDate !== "") { ?>
        <p class="date-filter-summary">
            Showing bookings
            <?php if($fromDate !== "" && $toDate !== "") { ?>
                from <strong><?php echo date("d M Y", strtotime($fromDate)); ?></strong>
                to <strong><?php echo date("d M Y", strtotime($toDate)); ?></strong>
            <?php } elseif($fromDate !== "") { ?>
                from <strong><?php echo date("d M Y", strtotime($fromDate)); ?></strong> onwards
            <?php } else { ?>
                up to <strong><?php echo date("d M Y", strtotime($toDate)); ?></strong>
            <?php } ?>
        </p>
    <?php } ?>

    <div class="report-tabs">
        <button class="report-tab-btn active" onclick="showReport('date', this)">Date Wise</button>
        <button class="report-tab-btn" onclick="showReport('month', this)">Month Wise</button>
        <button class="report-tab-btn" onclick="showReport('year', this)">Year Wise</button>
    </div>

    <!-- DATE WISE REPORT -->
    <div id="report-date" class="report-panel active">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Worker Name</th>
                        <th>Job Profile</th>
                        <th style="text-align:center;">Total Bookings</th>
                        <th>Status Breakdown</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($dateReport) { ?>
                        <?php foreach($dateReport as $row) { ?>
                            <tr>
                                <td style="font-weight:500;"><?php echo htmlspecialchars(date("d M Y", strtotime($row['period']))); ?></td>
                                <td style="font-weight:600; color: var(--secondary);"><?php echo htmlspecialchars($row['worker']); ?></td>
                                <td><?php echo htmlspecialchars($row['job']); ?></td>
                                <td style="text-align:center; font-weight:600;"><?php echo $row['total']; ?></td>
                                <td>
                                    <?php if($row['pending'])   echo "<span class='count-pill count-pending'>Pending: ".$row['pending']."</span>"; ?>
                                    <?php if($row['approved'])  echo "<span class='count-pill count-approved'>Approved: ".$row['approved']."</span>"; ?>
                                    <?php if($row['completed']) echo "<span class='count-pill count-completed'>Completed: ".$row['completed']."</span>"; ?>
                                    <?php if($row['rejected'])  echo "<span class='count-pill count-rejected'>Rejected: ".$row['rejected']."</span>"; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data-row"><td colspan="5"><i class="fas fa-calendar-times fa-2x" style="margin-bottom:10px; display:block;"></i>No Booking Records Found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MONTH WISE REPORT -->
    <div id="report-month" class="report-panel">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Worker Name</th>
                        <th>Job Profile</th>
                        <th style="text-align:center;">Total Bookings</th>
                        <th>Status Breakdown</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($monthReport) { ?>
                        <?php foreach($monthReport as $row) { ?>
                            <tr>
                                <td style="font-weight:500;"><?php echo htmlspecialchars($row['period']); ?></td>
                                <td style="font-weight:600; color: var(--secondary);"><?php echo htmlspecialchars($row['worker']); ?></td>
                                <td><?php echo htmlspecialchars($row['job']); ?></td>
                                <td style="text-align:center; font-weight:600;"><?php echo $row['total']; ?></td>
                                <td>
                                    <?php if($row['pending'])   echo "<span class='count-pill count-pending'>Pending: ".$row['pending']."</span>"; ?>
                                    <?php if($row['approved'])  echo "<span class='count-pill count-approved'>Approved: ".$row['approved']."</span>"; ?>
                                    <?php if($row['completed']) echo "<span class='count-pill count-completed'>Completed: ".$row['completed']."</span>"; ?>
                                    <?php if($row['rejected'])  echo "<span class='count-pill count-rejected'>Rejected: ".$row['rejected']."</span>"; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data-row"><td colspan="5"><i class="fas fa-calendar-times fa-2x" style="margin-bottom:10px; display:block;"></i>No Booking Records Found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- YEAR WISE REPORT -->
    <div id="report-year" class="report-panel">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Worker Name</th>
                        <th>Job Profile</th>
                        <th style="text-align:center;">Total Bookings</th>
                        <th>Status Breakdown</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($yearReport) { ?>
                        <?php foreach($yearReport as $row) { ?>
                            <tr>
                                <td style="font-weight:500;"><?php echo htmlspecialchars($row['period']); ?></td>
                                <td style="font-weight:600; color: var(--secondary);"><?php echo htmlspecialchars($row['worker']); ?></td>
                                <td><?php echo htmlspecialchars($row['job']); ?></td>
                                <td style="text-align:center; font-weight:600;"><?php echo $row['total']; ?></td>
                                <td>
                                    <?php if($row['pending'])   echo "<span class='count-pill count-pending'>Pending: ".$row['pending']."</span>"; ?>
                                    <?php if($row['approved'])  echo "<span class='count-pill count-approved'>Approved: ".$row['approved']."</span>"; ?>
                                    <?php if($row['completed']) echo "<span class='count-pill count-completed'>Completed: ".$row['completed']."</span>"; ?>
                                    <?php if($row['rejected'])  echo "<span class='count-pill count-rejected'>Rejected: ".$row['rejected']."</span>"; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data-row"><td colspan="5"><i class="fas fa-calendar-times fa-2x" style="margin-bottom:10px; display:block;"></i>No Booking Records Found</td></tr>
                    <?php } ?>
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
