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

/* ---- DATE FILTER (From Date / To Date) ---- */
$fromDate = isset($_GET['from_date']) ? trim($_GET['from_date']) : "";
$toDate   = isset($_GET['to_date'])   ? trim($_GET['to_date'])   : "";

/* Basic validation: must be YYYY-MM-DD, else ignore */
if($fromDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) { $fromDate = ""; }
if($toDate   && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate))   { $toDate   = ""; }

$whereClause = "crid=".$crid;

if($fromDate !== "")
{
    $whereClause .= " AND candate >= '".$fromDate." 00:00:00'";
}
if($toDate !== "")
{
    $whereClause .= " AND candate <= '".$toDate." 23:59:59'";
}

/* GET CANCELLATIONS FOR THIS CUSTOMER (optionally filtered by date range) */
$cancellations = $dao->getData(
    "*",
    "cancellation",
    $whereClause
);

/* BUILD REPORT ROWS: cancelled data / date-wise / month-wise / year-wise, grouped per worker */
$cancelledData = array();
$dateReport    = array();
$monthReport   = array();
$yearReport    = array();

if($cancellations)
{
    foreach($cancellations as $row)
    {
        $worker = $dao->getData(
            "*",
            "wregistration",
            "wid=".$row['wid']
        );

        $wname  = $worker[0]['wname']  ?? "";
        $wphone = $worker[0]['wphone'] ?? "";
        $jid    = $worker[0]['jid']    ?? "";

        $job = $dao->getData(
            "*",
            "job",
            "jid=".$jid
        );

        $jobname = $job[0]['jname'] ?? "";

        $candate = $row['candate'];
        $ts      = strtotime($candate);
        $day     = date("Y-m-d", $ts);
        $month   = date("F Y", $ts);
        $year    = date("Y", $ts);

        /* ---- CANCELLED DATA (raw list) ---- */
        $cancelledData[] = array(
            "canid"  => $row['canid'],
            "bid"    => $row['bid'],
            "worker" => $wname,
            "wphone" => $wphone,
            "job"    => $jobname,
            "date"   => $candate,
            "ts"     => $ts
        );

        /* ---- DATE WISE ---- */
        $dkey = $day."|".$row['wid'];
        if(!isset($dateReport[$dkey]))
        {
            $dateReport[$dkey] = array(
                "period"=>$day, "worker"=>$wname, "job"=>$jobname, "total"=>0
            );
        }
        $dateReport[$dkey]["total"]++;

        /* ---- MONTH WISE ---- */
        $mkey = $month."|".$row['wid'];
        if(!isset($monthReport[$mkey]))
        {
            $monthReport[$mkey] = array(
                "period"=>$month, "worker"=>$wname, "job"=>$jobname, "total"=>0
            );
        }
        $monthReport[$mkey]["total"]++;

        /* ---- YEAR WISE ---- */
        $ykey = $year."|".$row['wid'];
        if(!isset($yearReport[$ykey]))
        {
            $yearReport[$ykey] = array(
                "period"=>$year, "worker"=>$wname, "job"=>$jobname, "total"=>0
            );
        }
        $yearReport[$ykey]["total"]++;
    }
}

/* SORT: most recent first */
usort($cancelledData, function($a, $b) {
    return $b['ts'] <=> $a['ts'];
});
krsort($dateReport);
krsort($monthReport);
krsort($yearReport);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cancellation Reports</title>

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
        <h1>Cancellation Reports</h1>
        <p>Review cancelled service appointments by date, month, and year</p>
    </div>

    <form method="GET" class="filter-bar" style="display:flex; gap:15px; justify-content:center; align-items:flex-end; flex-wrap:wrap; margin-bottom:25px;">
        <div style="display:flex; flex-direction:column; gap:5px;">
            <label for="from_date" style="font-size:13px; font-weight:600; color: var(--secondary, #334155);">From Date</label>
            <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>"
                   style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; font-size:14px;">
        </div>
        <div style="display:flex; flex-direction:column; gap:5px;">
            <label for="to_date" style="font-size:13px; font-weight:600; color: var(--secondary, #334155);">To Date</label>
            <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>"
                   style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; font-size:14px;">
        </div>
        <button type="submit" style="padding:11px 26px; border:none; border-radius:10px; background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); color:#fff; font-weight:600; font-size:14px; cursor:pointer;">
            Apply Filter
        </button>
        <?php if($fromDate !== "" || $toDate !== "") { ?>
            <a href="?" style="padding:11px 22px; border-radius:10px; background:#f1f5f9; color: var(--secondary, #334155); font-weight:600; font-size:14px; text-decoration:none; display:inline-block;">
                Clear
            </a>
        <?php } ?>
    </form>

    <div class="report-tabs">
        <button class="report-tab-btn active" onclick="showReport('cancelled', this)">Cancelled Data</button>
        <button class="report-tab-btn" onclick="showReport('date', this)">Date Wise</button>
        <button class="report-tab-btn" onclick="showReport('month', this)">Month Wise</button>
        <button class="report-tab-btn" onclick="showReport('year', this)">Year Wise</button>
    </div>

    <!-- CANCELLED DATA (RAW LIST) -->
    <div id="report-cancelled" class="report-panel active">
        <?php if($cancelledData) { ?>
            <div class="table-container animate-fade-up">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Cancellation ID</th>
                            <th style="width: 120px;">Booking ID</th>
                            <th>Worker Name</th>
                            <th>Phone</th>
                            <th>Job Profile</th>
                            <th>Cancelled Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cancelledData as $row) { ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($row['canid']); ?></td>
                                <td>#<?php echo htmlspecialchars($row['bid']); ?></td>
                                <td style="font-weight: 600; color: var(--secondary);"><?php echo htmlspecialchars($row['worker']); ?></td>
                                <td><?php echo htmlspecialchars($row['wphone']); ?></td>
                                <td><?php echo htmlspecialchars($row['job']); ?></td>
                                <td style="font-weight: 500; color: var(--error);"><?php echo htmlspecialchars(date("d M Y", $row['ts'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="glass-card text-center animate-fade-up" style="margin-top: 50px; padding: 50px;">
                <i class="fas fa-trash-alt fa-3x" style="color: var(--text-muted); margin-bottom: 15px;"></i>
                <h3 style="color: var(--text-muted);">No Cancellations Found</h3>
                <p style="color: var(--text-muted); margin-top: 5px;">You have no cancelled service records in this account.</p>
            </div>
        <?php } ?>
    </div>

    <!-- DATE WISE REPORT -->
    <div id="report-date" class="report-panel">
        <div class="table-container animate-fade-up">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Worker Name</th>
                        <th>Job Profile</th>
                        <th style="text-align:center;">Total Cancellations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($dateReport) { ?>
                        <?php foreach($dateReport as $row) { ?>
                            <tr>
                                <td style="font-weight:500;"><?php echo htmlspecialchars(date("d M Y", strtotime($row['period']))); ?></td>
                                <td style="font-weight:600; color: var(--secondary);"><?php echo htmlspecialchars($row['worker']); ?></td>
                                <td><?php echo htmlspecialchars($row['job']); ?></td>
                                <td style="text-align:center; font-weight:600; color: var(--error);"><?php echo $row['total']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data-row"><td colspan="4"><i class="fas fa-trash-alt fa-2x" style="margin-bottom:10px; display:block;"></i>No Cancellation Records Found</td></tr>
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
                        <th style="text-align:center;">Total Cancellations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($monthReport) { ?>
                        <?php foreach($monthReport as $row) { ?>
                            <tr>
                                <td style="font-weight:500;"><?php echo htmlspecialchars($row['period']); ?></td>
                                <td style="font-weight:600; color: var(--secondary);"><?php echo htmlspecialchars($row['worker']); ?></td>
                                <td><?php echo htmlspecialchars($row['job']); ?></td>
                                <td style="text-align:center; font-weight:600; color: var(--error);"><?php echo $row['total']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data-row"><td colspan="4"><i class="fas fa-trash-alt fa-2x" style="margin-bottom:10px; display:block;"></i>No Cancellation Records Found</td></tr>
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
                        <th style="text-align:center;">Total Cancellations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($yearReport) { ?>
                        <?php foreach($yearReport as $row) { ?>
                            <tr>
                                <td style="font-weight:500;"><?php echo htmlspecialchars($row['period']); ?></td>
                                <td style="font-weight:600; color: var(--secondary);"><?php echo htmlspecialchars($row['worker']); ?></td>
                                <td><?php echo htmlspecialchars($row['job']); ?></td>
                                <td style="text-align:center; font-weight:600; color: var(--error);"><?php echo $row['total']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data-row"><td colspan="4"><i class="fas fa-trash-alt fa-2x" style="margin-bottom:10px; display:block;"></i>No Cancellation Records Found</td></tr>
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
