<?php
require('../config/autoload.php');
include("customerheader.php");

$dao = new DataAccess();

/* ---------------------------
   GET JOB ID
----------------------------*/
$jid = "";

if(isset($_GET['jid']))
{
    $jid = $_GET['jid'];
}

/* ---------------------------
   FETCH APPROVED WORKERS
   STATUS = 2
----------------------------*/
if($jid != "")
{
    $condition = "jid=".$jid." AND wstatus=2";

    $rows = $dao->getData(
        "*",
        "wregistration",
        $condition
    );
}
else
{
    $rows = $dao->getData(
        "*",
        "wregistration",
        "wstatus=2"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Available Workers</title>

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

        .worker-card-custom {
            border: 1px solid var(--border-color);
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: 30px;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .worker-card-custom:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .profile-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            font-size: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 2px solid rgba(79, 70, 229, 0.2);
        }

        .worker-name-custom {
            font-size: 22px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 15px;
            font-family: 'Outfit', sans-serif;
        }

        .info-list {
            list-style: none;
            margin-bottom: 25px;
            flex-grow: 1;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            font-size: 14px;
            color: var(--text-main);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item i {
            color: var(--primary);
            width: 20px;
            text-align: center;
        }

        .info-item span.label {
            font-weight: 600;
            color: var(--text-muted);
            min-width: 90px;
        }
    </style>
</head>

<body>

<div class="main-container">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='category.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="page-header">
        <h1>Available Workers</h1>
        <p>Book a qualified specialist to get the job done right</p>
    </div>

    <?php
    if($rows)
    {
    ?>

    <div class="grid-modern animate-fade-up">

        <?php
        foreach($rows as $row)
        {
        ?>

        <div class="worker-card-custom">
            
            <div class="profile-avatar">
                <i class="fas fa-user-tie"></i>
            </div>

            <div class="worker-name-custom">
                <?php echo htmlspecialchars($row['wname']); ?>
            </div>

            <ul class="info-list">
                
                <li class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span class="label">Email:</span>
                    <?php echo htmlspecialchars($row['wgmail']); ?>
                </li>

                <li class="info-item">
                    <i class="fas fa-birthday-cake"></i>
                    <span class="label">Age:</span>
                    <?php echo htmlspecialchars($row['wage']); ?> yrs
                </li>

                <li class="info-item">
                    <i class="fas fa-venus-mars"></i>
                    <span class="label">Gender:</span>
                    <?php echo ($row['wgender'] == 'm') ? 'Male' : (($row['wgender'] == 'f') ? 'Female' : htmlspecialchars($row['wgender'])); ?>
                </li>

                <li class="info-item">
                    <i class="fas fa-phone"></i>
                    <span class="label">Phone:</span>
                    <?php echo htmlspecialchars($row['wphone']); ?>
                </li>

                <li class="info-item">
                    <i class="fas fa-info-circle"></i>
                    <span class="label">Bio:</span>
                    <?php echo htmlspecialchars($row['wdescription']); ?>
                </li>

            </ul>

            <?php if (isset($_SESSION['crid'])): ?>
                <a href="booking.php?wid=<?php echo $row['wid']; ?>" class="btn-modern btn-success-modern w-100">
                    <i class="fas fa-calendar-check"></i> Book Worker
                </a>
            <?php else: ?>
                <a href="../index.php?view=login&role=customer&redirect=customer/booking.php?wid=<?php echo $row['wid']; ?>" class="btn-modern btn-primary-modern w-100">
                    <i class="fas fa-lock"></i> Select to Book (Login Required)
                </a>
            <?php endif; ?>

        </div>

        <?php
        }
        ?>

    </div>

    <?php
    }
    else
    {
    ?>

    <div class="glass-card text-center" style="margin-top: 50px; padding: 50px;">
        <i class="fas fa-user-slash fa-3x" style="color: var(--text-muted); margin-bottom: 15px;"></i>
        <h3 style="color: var(--text-muted);">No Workers Available</h3>
        <p style="color: var(--text-muted); margin-top: 5px;">Currently there are no approved workers assigned to this job.</p>
    </div>

    <?php
    }
    ?>

</div>

</body>
</html>