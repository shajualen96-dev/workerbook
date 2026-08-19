<?php
require('../config/autoload.php');
include("header.php");

$dao = new DataAccess();

/* ---------------------------
   FETCH JOBS FOR DROPDOWN
----------------------------*/
$jobs = $dao->getData("*", "job");

/* ---------------------------
   SELECTED JOB
----------------------------*/
$selectedJob = isset($_GET['jid']) && $_GET['jid'] != "" ? trim($_GET['jid']) : "";

$selectedJobName = "";
$selectedJobCid  = "";
$selectedCatName = "";

if ($selectedJob != "" && $jobs) {
    foreach ($jobs as $j) {
        if ((string)$j['jid'] === (string)$selectedJob) {
            $selectedJobName = $j['jname'];
            $selectedJobCid  = $j['cid'];
        }
    }
}

/* Look up the category name for this job's cid */
if ($selectedJobCid != "") {
    $catResult = $dao->getData("*", "category", "cid=" . intval($selectedJobCid));
    if ($catResult) {
        foreach ($catResult as $c) {
            $selectedCatName = $c['cname'];
        }
    }
}

/* ---------------------------
   FETCH BOOKINGS FOR SELECTED JOB
   booking table: bid, cid, jid, crid (customer reg id),
                  wid, bstatus, bdate, cbdate
----------------------------*/
$bookings = array();

if ($selectedJob != "") {
    $bookings = $dao->getData("*", "booking", "jid=" . intval($selectedJob));
}

/* ---------------------------
   STATUS LABELS (match your badge-status style)
----------------------------*/
function bookingStatusBadge($status) {
    switch ((int)$status) {
        case 2:
            return "<span class='badge-status badge-status-approved'><span class='status-dot status-dot-approved'></span>Approved</span>";
        case 6:
            return "<span class='badge-status badge-status-approved'><span class='status-dot status-dot-approved'></span>Completed</span>";
        case 3:
            return "<span class='badge-status badge-status-cancelled'><span class='status-dot status-dot-cancelled'></span>Rejected</span>";
        default:
            return "<span class='badge-status badge-status-pending'><span class='status-dot status-dot-pending'></span>Pending</span>";
    }
}
?>

<div id="page-wrapper" class="animate-fade-in">

    <!-- JOB FILTER -->
    <div class="glass-card" style="margin-bottom: 30px;">

        <h2 style="margin-bottom: 25px;">
            <i class="fas fa-filter" style="color: var(--primary);"></i> Job-wise Customer Report
        </h2>

        <form method="GET" action="categoryreport.php">
            <div class="form-group-modern">
                <label class="form-label-modern">Select Job</label>
                <select name="jid"
                        class="form-input-modern"
                        onchange="this.form.submit()">
                    <option value="">-- Select Job --</option>
                    <?php
                    if ($jobs) {
                        foreach ($jobs as $j) {
                    ?>
                        <option value="<?php echo $j['jid']; ?>"
                            <?php echo ((string)$selectedJob === (string)$j['jid']) ? "selected" : ""; ?>>
                            <?php echo htmlspecialchars($j['jname']); ?>
                        </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </form>

    </div>

    <!-- JOB / CATEGORY INFO + CUSTOMER RESULTS -->
    <?php if ($selectedJob != "") { ?>

    <!-- JOB & CATEGORY DETAILS -->
    <div class="glass-card" style="margin-bottom: 30px;">
        <h2 style="margin-bottom: 15px;">
            <i class="fas fa-briefcase" style="color: var(--primary);"></i> Job Details
        </h2>
        <p style="margin-bottom: 8px;">
            <strong>Job:</strong> <?php echo htmlspecialchars($selectedJobName); ?>
        </p>
        <p>
            <strong>Category:</strong>
            <span class="badge-status badge-status-approved">
                <span class="status-dot status-dot-approved"></span>
                <?php echo htmlspecialchars($selectedCatName); ?>
            </span>
        </p>
    </div>

    <!-- CUSTOMER RESULTS -->
    <div class="glass-card">

        <h2 style="margin-bottom: 20px;">
            <i class="fas fa-users" style="color: var(--primary);"></i>
            Customers who booked "<?php echo htmlspecialchars($selectedJobName); ?>"
        </h2>

        <div class="table-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Booking Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($bookings) {
                        foreach ($bookings as $b) {

                            // Look up the customer for this booking via crid
                            $customer = array(
                                "cname"  => "",
                                "cgmail" => "",
                                "cphone" => ""
                            );

                            $custResult = $dao->getData("*", "cregistration", "crid=" . intval($b['crid']));

                            if ($custResult) {
                                foreach ($custResult as $c) {
                                    $customer = $c;
                                }
                            }
                    ?>
                    <tr>
                        <td><?php echo $b['bid']; ?></td>
                        <td style="font-weight: 600; color: var(--secondary);">
                            <?php echo htmlspecialchars($customer['cname']); ?>
                        </td>
                        <td style="color: var(--text-muted);">
                            <?php echo htmlspecialchars($customer['cgmail']); ?>
                        </td>
                        <td style="color: var(--text-muted);">
                            <?php echo htmlspecialchars($customer['cphone']); ?>
                        </td>
                        <td style="color: var(--text-muted);">
                            <?php echo htmlspecialchars($b['bdate']); ?>
                        </td>
                        <td>
                            <?php echo bookingStatusBadge($b['bstatus']); ?>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-user-slash fa-2x" style="margin-bottom: 10px; display: block;"></i>
                            No customers found for this job
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
    <?php } ?>

</div>
