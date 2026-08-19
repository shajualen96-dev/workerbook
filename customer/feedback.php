<?php
require('../config/autoload.php');
include("customerheader.php");

$dao = new DataAccess();

/* ---------------------------
   CUSTOMER LOGIN CHECK
----------------------------*/
if(!isset($_SESSION['crid']))
{
    header("Location: login.php");
    exit();
}

/* ---------------------------
   CUSTOMER ID
----------------------------*/
$crid = $_SESSION['crid'];

/* ---------------------------
   INSERT FEEDBACK
----------------------------*/
$msg = "";

if(isset($_POST['submit']))
{
    $data = array(

        "crid"      => $crid,
        "wid"       => $_POST['wid'],
        "bid"       => $_POST['bid'],
        "frating"   => $_POST['frating'],
        "fmessage"  => $_POST['fmessage'],
        "fdate"     => date('Y-m-d')

    );

    if($dao->insert($data,"feedback"))
    {
        $msg = "Feedback Added Successfully";
    }
    else
    {
        $msg = "Failed";
    }
}

/* ---------------------------
   FETCH CUSTOMER BOOKINGS
----------------------------*/
$bookings = $dao->getData(
    "*",
    "booking",
    "crid=".$crid
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Feedback</title>

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
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .feedback-card-custom {
            margin-bottom: 30px;
            padding: 30px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            background: #ffffff;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .feedback-card-custom:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .booking-summary {
            background: rgba(79, 70, 229, 0.05);
            border-left: 5px solid var(--primary);
            padding: 20px;
            border-radius: var(--radius-md);
            margin-bottom: 25px;
        }

        .booking-summary h3 {
            color: var(--primary);
            font-size: 20px;
            margin-bottom: 8px;
        }

        .booking-summary p {
            font-size: 14px;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .booking-summary p:last-child {
            margin-bottom: 0;
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
        <h1>Customer Feedback</h1>
        <p>Leave a review for your booked service professionals to share your experience</p>
    </div>

    <?php
    if($msg!="")
    {
        $alertClass = (strpos(strtolower($msg), 'success') !== false) ? 'alert-success-modern' : 'alert-error-modern';
        $icon = (strpos(strtolower($msg), 'success') !== false) ? 'fa-check-circle' : 'fa-exclamation-circle';
    ?>
        <div class="alert-modern <?php echo $alertClass; ?> animate-fade-in" style="max-width: 100%;">
            <i class="fas <?php echo $icon; ?>"></i>
            <?php echo $msg; ?>
        </div>
    <?php
    }
    ?>

    <?php
    if($bookings)
    {
        foreach($bookings as $booking)
        {
            /* WORKER */
            $worker = $dao->getData(
                "*",
                "wregistration",
                "wid=".$booking['wid']
            );

            $wname = "";
            if($worker)
            {
                $wname = $worker[0]['wname'];
            }

            /* JOB */
            $job = $dao->getData(
                "*",
                "job",
                "jid=".$booking['jid']
            );

            $jname = "";
            if($job)
            {
                $jname = $job[0]['jname'];
            }
    ?>
        <div class="feedback-card-custom animate-fade-up">
            
            <div class="booking-summary">
                <h3><?php echo htmlspecialchars($wname); ?></h3>
                <p><strong>Job Role:</strong> <?php echo htmlspecialchars($jname); ?></p>
                <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($booking['bdate']); ?></p>
            </div>

            <form method="POST">
                
                <input type="hidden" name="wid" value="<?php echo $booking['wid']; ?>">
                <input type="hidden" name="bid" value="<?php echo $booking['bid']; ?>">

                <div class="form-group-modern">
                    <label class="form-label-modern">Rating Stars</label>
                    <select name="frating" class="form-input-modern" required>
                        <option value="">Select Rating</option>
                        <option value="5">★★★★★ (5 Stars)</option>
                        <option value="4">★★★★☆ (4 Stars)</option>
                        <option value="3">★★★☆☆ (3 Stars)</option>
                        <option value="2">★★☆☆☆ (2 Stars)</option>
                        <option value="1">★☆☆☆☆ (1 Star)</option>
                    </select>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Feedback Message</label>
                    <textarea name="fmessage" class="form-input-modern" placeholder="Describe your experience with this worker..." required></textarea>
                </div>

                <button type="submit" name="submit" class="btn-modern btn-primary-modern">
                    <i class="fas fa-paper-plane"></i> Submit Feedback
                </button>

            </form>

        </div>
    <?php
        }
    }
    else
    {
    ?>
        <div class="glass-card text-center animate-fade-up" style="margin-top: 50px; padding: 50px;">
            <i class="fas fa-comment-slash fa-3x" style="color: var(--text-muted); margin-bottom: 15px;"></i>
            <h3 style="color: var(--text-muted);">No Bookings for Feedback</h3>
            <p style="color: var(--text-muted); margin-top: 5px;">Bookings are required in order to leave a rating or feedback.</p>
        </div>
    <?php
    }
    ?>

</div>

</body>
</html>
<!-- REMOVING DUPLICATED BODY -->
<?php
// Skip the duplicate body markup from parent files
exit();
?>