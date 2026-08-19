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

$cancellations = $dao->getData(
    "*",
    "cancellation",
    "crid=".$crid
);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cancellation History</title>

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
    </style>
</head>
<body>

<div class="main-container">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='viewbookings.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="page-header">
        <h1>Cancellation History</h1>
        <p>Review cancelled service appointments and refund statuses</p>
    </div>

    <?php
    if($cancellations)
    {
    ?>
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
                <?php
                foreach($cancellations as $row)
                {
                    $worker = $dao->getData(
                        "*",
                        "wregistration",
                        "wid=".$row['wid']
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
                ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($row['canid']); ?></td>
                        <td>#<?php echo htmlspecialchars($row['bid']); ?></td>
                        <td style="font-weight: 600; color: var(--secondary);"><?php echo htmlspecialchars($wname); ?></td>
                        <td><?php echo htmlspecialchars($wphone); ?></td>
                        <td><?php echo htmlspecialchars($jobname); ?></td>
                        <td style="font-weight: 500; color: var(--error);"><?php echo htmlspecialchars($row['candate']); ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

    </div>
    <?php
    }
    else
    {
    ?>
        <div class="glass-card text-center animate-fade-up" style="margin-top: 50px; padding: 50px;">
            <i class="fas fa-trash-alt fa-3x" style="color: var(--text-muted); margin-bottom: 15px;"></i>
            <h3 style="color: var(--text-muted);">No Cancellations Found</h3>
            <p style="color: var(--text-muted); margin-top: 5px;">You have no cancelled service records in this account.</p>
        </div>
    <?php
    }
    ?>

</div>

</body>
</html>