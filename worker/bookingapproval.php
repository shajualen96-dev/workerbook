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

/* APPROVE */
if(isset($_GET['approve']))
{
    $dao->update(["bstatus"=>2],"booking","bid=".$_GET['approve']);
    echo "<script>
        alert('Booking Approved');
        window.location='bookingapproval.php';
    </script>";
    exit();
}

/* REJECT */
if(isset($_GET['reject']))
{
    $dao->update(["bstatus"=>3],"booking","bid=".$_GET['reject']);
    echo "<script>
        alert('Booking Rejected');
        window.location='bookingapproval.php';
    </script>";
    exit();
}

/* COMPLETE */
if(isset($_GET['complete']))
{
    $dao->update(["bstatus"=>6],"booking","bid=".$_GET['complete']);
    echo "<script>
        alert('Booking Marked as Completed');
        window.location='bookingapproval.php';
    </script>";
    exit();
}

$bookings = $dao->getData("*","booking","wid=".$wid);

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Worker Bookings</title>

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

        .action-cell {
            display: flex;
            gap: 10px;
            justify-content: center;
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
        <h1>Customer Bookings</h1>
        <p>Manage pending booking approvals, worker schedule requests, and history log</p>
    </div>

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
                    <th style="text-align: center; width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if($bookings) { ?>
                    <?php foreach($bookings as $booking) { 
                        $customer = $dao->getData("*","cregistration","crid=".$booking['crid']);
                        $cname = $customer[0]['cname'] ?? "";
                        $cphone = $customer[0]['cphone'] ?? "";

                        $worker = $dao->getData("*","wregistration","wid=".$booking['wid']);
                        $jid = $worker[0]['jid'] ?? "";

                        $job = $dao->getData("*","job","jid=".$jid);
                        $jobname = $job[0]['jname'] ?? "Not Assigned";

                        if($booking['bstatus'] == 0){
                            $status = "<span class='badge-status badge-status-pending'><span class='status-dot status-dot-pending'></span>Pending</span>";
                        } elseif($booking['bstatus'] == 2){
                            $status = "<span class='badge-status badge-status-approved'><span class='status-dot status-dot-approved'></span>Approved</span>";
                        } elseif($booking['bstatus'] == 6){
                            $status = "<span class='badge-status badge-status-completed'><span class='status-dot status-dot-completed'></span>Completed</span>";
                        } else {
                            $status = "<span class='badge-status badge-status-cancelled'><span class='status-dot status-dot-cancelled'></span>Rejected</span>";
                        }
                    ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($booking['bid']); ?></td>
                            <td style="font-weight: 600; color: var(--secondary);"><?php echo htmlspecialchars($cname); ?></td>
                            <td><?php echo htmlspecialchars($cphone); ?></td>
                            <td><?php echo htmlspecialchars($jobname); ?></td>
                            <td style="font-weight: 500;"><?php echo htmlspecialchars($booking['bdate']); ?></td>
                            <td><?php echo $status; ?></td>
                            <td style="text-align: center;">

                                <?php if($booking['bstatus'] == 0) { ?>
                                    <!-- Pending: Show both Approve and Reject -->
                                    <div class="action-cell">
                                        <a href="bookingapproval.php?approve=<?php echo $booking['bid']; ?>" 
                                           class="btn-edit" 
                                           style="background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);"
                                           onclick="return confirm('Approve this booking?')">
                                            <i class="fas fa-check"></i> Approve
                                        </a>
                                        <a href="bookingapproval.php?reject=<?php echo $booking['bid']; ?>" 
                                           class="btn-delete"
                                           onclick="return confirm('Reject this booking request?')">
                                            <i class="fas fa-times"></i> Reject
                                        </a>
                                    </div>

                                <?php } elseif($booking['bstatus'] == 2) { ?>
                                    <!-- Approved: Show Mark Completed and Reject to undo -->
                                    <div class="action-cell">
                                        <a href="bookingapproval.php?complete=<?php echo $booking['bid']; ?>" 
                                           class="btn-edit"
                                           style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);"
                                           onclick="return confirm('Mark this booking as completed?')">
                                            <i class="fas fa-flag-checkered"></i> Mark Completed
                                        </a>
                                        <a href="bookingapproval.php?reject=<?php echo $booking['bid']; ?>" 
                                           class="btn-delete"
                                           onclick="return confirm('Are you sure you want to reject this approved booking?')">
                                            <i class="fas fa-times"></i> Reject
                                        </a>
                                    </div>

                                <?php } elseif($booking['bstatus'] == 3) { ?>
                                    <!-- Rejected: Show only Approve to undo -->
                                    <div class="action-cell">
                                        <a href="bookingapproval.php?approve=<?php echo $booking['bid']; ?>" 
                                           class="btn-edit"
                                           style="background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);"
                                           onclick="return confirm('Approve this booking?')">
                                            <i class="fas fa-check"></i> Approve
                                        </a>
                                    </div>

                                <?php } elseif($booking['bstatus'] == 6) { ?>
                                    <!-- Completed: Final state, no actions available -->
                                    <span style="color: var(--text-muted); font-size: 13px;">&mdash;</span>

                                <?php } ?>

                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-calendar-times fa-2x" style="margin-bottom: 10px; display: block;"></i>
                            No Booking Requests Found
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>

</body>
</html>
