<?php
require('../config/autoload.php');

$dao = new DataAccess();

/* LOGIN CHECK */
if(!isset($_SESSION['crid']))
{
    header("Location: login.php");
    echo "<script>location.replace('login.php');</script>";
    exit();
}

$crid = $_SESSION['crid'];

/* CHECK PLATFORM FEE SUBSCRIPTION & 1ST BOOKING FREE RULE */
$c_bookings = $dao->getData("*", "booking", "crid=".$crid);
$c_booking_count = is_array($c_bookings) ? count($c_bookings) : 0;

$c_info = $dao->getData("c_plan_expires", "cregistration", "crid=".$crid);
$c_plan_expires = $c_info[0]['c_plan_expires'] ?? null;
$today_str = date('Y-m-d');
$is_subscribed = ($c_plan_expires && $c_plan_expires >= $today_str);

if ($c_booking_count >= 1 && !$is_subscribed) {
    header("Location: plans.php");
    echo "<script>location.replace('plans.php');</script>";
    exit();
}

include("customerheader.php");

/* GET WORKER ID */
if(!isset($_GET['wid']))
{
    echo "Invalid Worker";
    exit();
}

$wid = intval($_GET['wid']);

$msg = "";
$msgtype = "";

/* FETCH WORKER DETAILS */
$worker = $dao->getData("*","wregistration","wid=".$wid);

if(!$worker)
{
    echo "Worker Not Found";
    exit();
}

$row = $worker[0];

$jid = $row['jid'];
$cid = isset($row['cid']) ? $row['cid'] : 0;

/* GET ALREADY BOOKED DATES */
$bookedDates = array();

$bookings = $dao->getData(
    "bdate",
    "booking",
    "wid=".$wid
);

if($bookings)
{
    foreach($bookings as $b)
    {
        $bookedDates[] = $b['bdate'];
    }
}

/* BOOK WORKER */
if(isset($_POST['book']))
{
    $bdate = $_POST['bdate'];
    $today = date("Y-m-d");

    if($bdate < $today)
    {
        $msg = "Old dates are not allowed.";
        $msgtype = "error";
    }
    else
    {
        $existing = $dao->getData(
            "*",
            "booking",
            "wid=".$wid." AND bdate='".$bdate."'"
        );

        if($existing)
        {
            $msg = "This worker is already booked on the selected date.";
            $msgtype = "error";
        }
        else
        {
            $data = array(
                "cid"     => $cid,
                "jid"     => $jid,
                "crid"    => $crid,
                "wid"     => $wid,
                "bdate"   => $bdate,
                "cbdate"  => date("Y-m-d H:i:s"),
                "bstatus" => 0
            );

            if($dao->insert($data,"booking"))
            {
                $msg = "Worker Booked Successfully";
                $msgtype = "success";

                $bookedDates[] = $bdate;
            }
            else
            {
                $msg = "Booking Failed";
                $msgtype = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Book Worker</title>

<style>
        .booking-container {
            max-width: 650px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .booking-card-custom {
            padding: 40px;
        }

        .booking-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            padding-bottom: 20px;
        }

        .booking-header h2 {
            font-size: 30px;
            color: var(--secondary);
            font-weight: 800;
        }

        .booking-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 5px;
        }

        .worker-meta-list {
            list-style: none;
            margin-bottom: 30px;
            background: rgba(241, 245, 249, 0.5);
            padding: 20px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        .worker-meta-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.4);
            font-size: 15px;
        }

        .worker-meta-item:last-child {
            border-bottom: none;
            flex-direction: column;
            gap: 5px;
        }

        .worker-meta-item span.label {
            font-weight: 700;
            color: var(--secondary);
        }

        .worker-meta-item span.val {
            color: var(--text-main);
        }
    </style>
</head>
<body>

<div class="booking-container animate-fade-up">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='workers.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="glass-card booking-card-custom">
        
        <div class="booking-header">
            <h2>Book Specialist</h2>
            <p>Select your preferred service date to confirm appointment</p>
        </div>

        <?php if($msg != "") { 
            $alertClass = ($msgtype == 'success') ? 'alert-success-modern' : 'alert-error-modern';
            $icon = ($msgtype == 'success') ? 'fa-check-circle' : 'fa-exclamation-circle';
        ?>
            <div class="alert-modern <?php echo $alertClass; ?>">
                <i class="fas <?php echo $icon; ?>"></i>
                <?php echo $msg; ?>
            </div>
        <?php } ?>

        <ul class="worker-meta-list">
            
            <li class="worker-meta-item">
                <span class="label">Worker Name:</span>
                <span class="val"><?php echo htmlspecialchars($row['wname']); ?></span>
            </li>

            <li class="worker-meta-item">
                <span class="label">Email Address:</span>
                <span class="val"><?php echo htmlspecialchars($row['wgmail']); ?></span>
            </li>

            <li class="worker-meta-item">
                <span class="label">Contact Phone:</span>
                <span class="val"><?php echo htmlspecialchars($row['wphone']); ?></span>
            </li>

            <li class="worker-meta-item">
                <span class="label">Work Description:</span>
                <span class="val" style="margin-top: 5px; line-height: 1.5; color: var(--text-muted);">
                    <?php echo htmlspecialchars($row['wdescription']); ?>
                </span>
            </li>

        </ul>

        <form method="post">

            <div class="form-group-modern">
                <label class="form-label-modern">Select Booking Date</label>
                <input
                    type="date"
                    name="bdate"
                    id="bdate"
                    class="form-input-modern"
                    min="<?php echo date('Y-m-d'); ?>"
                    required
                >
            </div>

            <button type="submit" name="book" class="btn-modern btn-success-modern w-100" style="margin-top: 10px;">
                <i class="fas fa-check"></i> Confirm Booking
            </button>

        </form>

    </div>

</div>

<script>

let bookedDates = <?php echo json_encode($bookedDates); ?>;

document.getElementById("bdate").addEventListener("change", function(){

    let selectedDate = this.value;

    if(bookedDates.includes(selectedDate))
    {
        alert("This worker is already booked on this date.");
        this.value = "";
    }

});

</script>

</body>
</html>