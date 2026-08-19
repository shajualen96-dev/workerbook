<?php
require('../config/autoload.php');

$dao = new DataAccess();

/* ---------------------------
   LOGIN CHECK
----------------------------*/
if(!isset($_SESSION['wid']))
{
    header("Location: ../index.php?view=login&role=worker");
    echo "<script>location.replace('../index.php?view=login&role=worker');</script>";
    exit();
}

$wid = $_SESSION['wid'];

/* WORKER PLATFORM SUBSCRIPTION CHECK */
$worker = $dao->getData("*", "wregistration", "wid=".$wid);
$w_plan_expires = $worker[0]['w_plan_expires'] ?? null;
$today_str = date('Y-m-d');
if (!$w_plan_expires || $w_plan_expires < $today_str) {
    header("Location: plans.php");
    echo "<script>location.replace('plans.php');</script>";
    exit();
}

include("workerheader.php");

/* ---------------------------
   FETCH FEEDBACK DETAILS
----------------------------*/

$sql = "SELECT 
            feedback.*, 
            cregistration.cname,
            cregistration.cgmail
        FROM feedback
        INNER JOIN cregistration
        ON feedback.crid = cregistration.crid
        WHERE feedback.wid='$wid'
        AND feedback.fstatus='1'
        ORDER BY feedback.fid DESC";

$feedback = $dao->query($sql);

?>

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

        .review-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 30px;
            margin-bottom: 25px;
            transition: var(--transition);
        }

        .review-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            padding-bottom: 15px;
        }

        .review-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .review-user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            font-size: 18px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review-user-info h4 {
            font-size: 16px;
            color: var(--secondary);
            margin-bottom: 2px;
        }

        .review-user-info span {
            font-size: 12px;
            color: var(--text-muted);
        }

        .review-stars {
            color: #fbbf24;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .review-content {
            background: rgba(241, 245, 249, 0.4);
            border-left: 4px solid var(--primary);
            padding: 20px;
            border-radius: var(--radius-md);
            font-style: italic;
            color: var(--text-main);
            line-height: 1.6;
            font-size: 15px;
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
        <h1>Reviews & Feedback</h1>
        <p>View customer feedback, reviews, and satisfaction ratings left by users</p>
    </div>

    <div class="animate-fade-up">

        <?php
        if($feedback)
        {
            foreach($feedback as $row)
            {
                $starsStr = str_repeat("★", $row['frating']) . str_repeat("☆", 5 - $row['frating']);
        ?>
            <!-- REVIEW CARD -->
            <div class="review-card">
                
                <div class="review-header">
                    
                    <div class="review-user">
                        <div class="review-user-avatar">
                            <?php echo strtoupper(substr($row['cname'], 0, 1)); ?>
                        </div>
                        <div class="review-user-info">
                            <h4><?php echo htmlspecialchars($row['cname']); ?></h4>
                            <span><?php echo htmlspecialchars($row['cgmail']); ?></span>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <div class="review-stars">
                            <?php echo $starsStr; ?>
                            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600; margin-left: 5px;">(<?php echo $row['frating']; ?>/5)</span>
                        </div>
                        <span style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($row['fdate']); ?></span>
                    </div>

                </div>

                <div class="review-content">
                    "<?php echo htmlspecialchars($row['fmessage']); ?>"
                </div>

            </div>
        <?php
            }
        }
        else
        {
        ?>
            <div class="glass-card text-center" style="margin-top: 50px; padding: 50px;">
                <i class="fas fa-comment-slash fa-3x" style="color: var(--text-muted); margin-bottom: 15px;"></i>
                <h3 style="color: var(--text-muted);">No Reviews Yet</h3>
                <p style="color: var(--text-muted); margin-top: 5px;">Once you complete bookings, customer reviews will appear here.</p>
            </div>
        <?php
        }
        ?>

    </div>

</div>

</body>
</html>
<?php
exit();
?>
