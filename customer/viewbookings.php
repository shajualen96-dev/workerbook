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

/* CANCEL BOOKING */
if(isset($_GET['cancel']))
{
    $bid = intval($_GET['cancel']);

    $booking = $dao->getData(
        "*",
        "booking",
        "bid=".$bid." AND crid=".$crid
    );

    if($booking)
    {
        $wid = $booking[0]['wid'];

        $cancelData = array(
            "bid"       => $bid,
            "wid"       => $wid,
            "crid"      => $crid,
            "candate"   => date("Y-m-d"),
            "canstatus" => 1
        );

        if($dao->insert($cancelData,"cancellation"))
        {
            $dao->delete(
                "booking",
                "bid=".$bid." AND crid=".$crid
            );

            echo "<script>
                    alert('Booking Cancelled Successfully');
                    window.location='viewbookings.php';
                  </script>";
            exit();
        }
    }
}

/* GET BOOKINGS */
$bookings = $dao->getData(
    "*",
    "booking",
    "crid=".$crid
);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>My Bookings</title>

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
        <h1>My Bookings</h1>
        <p>Monitor your service bookings, approvals, and cancellations</p>
    </div>

    <div class="table-container animate-fade-up">
        
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 120px;">Booking ID</th>
                    <th>Worker Name</th>
                    <th>Phone</th>
                    <th>Job Profile</th>
                    <th>Service Date</th>
                    <th>Status</th>
                    <th style="text-align: center; width: 140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($bookings)
                {
                    foreach($bookings as $booking)
                    {
                        $worker = $dao->getData(
                            "*",
                            "wregistration",
                            "wid=".$booking['wid']
                        );

                        $wname  = $worker[0]['wname'] ?? "";
                        $wphone = $worker[0]['wphone'] ?? "";
                        $jid    = $worker[0]['jid'] ?? "";

                        $job = $dao->getData(
                            "*",
                            "job",
                            "jid=".$jid
                        );

                        $jobname = $job[0]['jname'] ?? "";

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
                            <td>#<?php echo htmlspecialchars($booking['bid']); ?></td>
                            <td style="font-weight: 600; color: var(--secondary);"><?php echo htmlspecialchars($wname); ?></td>
                            <td><?php echo htmlspecialchars($wphone); ?></td>
                            <td><?php echo htmlspecialchars($jobname); ?></td>
                            <td style="font-weight: 500;"><?php echo htmlspecialchars($booking['bdate']); ?></td>
                            <td><?php echo $status; ?></td>
                            <td style="text-align: center;">
                                <?php if($booking['bstatus'] == 6) { ?>
                                    <span style="color: var(--text-muted); font-size: 13px;">&mdash;</span>
                                <?php } else { ?>
                                    <a class="btn-delete"
                                       href="viewbookings.php?cancel=<?php echo $booking['bid']; ?>"
                                       onclick="return confirm('Are you sure you want to cancel this booking?');">
                                       <i class="fas fa-times"></i> Cancel
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                <?php
                    }
                }
                else
                {
                ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-calendar-times fa-2x" style="margin-bottom: 10px; display: block;"></i>
                            No Active Bookings Found
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

    </div>

</div>

</body>
</html>
