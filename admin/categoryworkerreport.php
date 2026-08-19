<?php
require('../config/autoload.php');
include("header.php");

$dao = new DataAccess();

/* ---------------------------
   FETCH CATEGORIES FOR DROPDOWN
----------------------------*/
$categories = $dao->getData("*", "category");

/* ---------------------------
   SELECTED CATEGORY
----------------------------*/
$selectedCat = isset($_GET['cid']) && $_GET['cid'] != "" ? trim($_GET['cid']) : "";

$selectedCatName = "";
if ($selectedCat != "" && $categories) {
    foreach ($categories as $cat) {
        if ((string)$cat['cid'] === (string)$selectedCat) {
            $selectedCatName = $cat['cname'];
        }
    }
}

/* ---------------------------
   FETCH WORKERS JOINED TO THIS CATEGORY
   Path: category.cid -> job.cid -> wregistration.jid
----------------------------*/
$workers = array();

if ($selectedCat != "") {
    $sql = "SELECT w.*, j.jname
            FROM wregistration w
            INNER JOIN job j ON w.jid = j.jid
            WHERE j.cid = " . intval($selectedCat) . "
            ORDER BY w.wid DESC";

    $workers = $dao->query($sql);
}

/* ---------------------------
   STATUS BADGE
----------------------------*/
function workerStatusBadge($status) {
    switch ((int)$status) {
        case 2:
            return "<span class='badge-status badge-status-approved'><span class='status-dot status-dot-approved'></span>Approved</span>";
        case 3:
            return "<span class='badge-status badge-status-cancelled'><span class='status-dot status-dot-cancelled'></span>Rejected</span>";
        default:
            return "<span class='badge-status badge-status-pending'><span class='status-dot status-dot-pending'></span>Pending</span>";
    }
}
?>

<div id="page-wrapper" class="animate-fade-in">

    <!-- CATEGORY FILTER -->
    <div class="glass-card" style="margin-bottom: 30px;">

        <h2 style="margin-bottom: 25px;">
            <i class="fas fa-filter" style="color: var(--primary);"></i> Category-wise Worker Report
        </h2>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'approved'): ?>
                <div class="alert-modern alert-success-modern" style="margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> Worker status updated to <strong>Approved</strong>.
                </div>
            <?php elseif ($_GET['msg'] == 'rejected'): ?>
                <div class="alert-modern alert-error-modern" style="margin-bottom: 20px;">
                    <i class="fas fa-times-circle"></i> Worker status updated to <strong>Rejected</strong>.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form method="GET" action="categoryworkerreport.php">
            <div class="form-group-modern">
                <label class="form-label-modern">Select Category</label>
                <select name="cid"
                        class="form-input-modern"
                        onchange="this.form.submit()">
                    <option value="">-- Select Category --</option>
                    <?php
                    if ($categories) {
                        foreach ($categories as $cat) {
                    ?>
                        <option value="<?php echo $cat['cid']; ?>"
                            <?php echo ((string)$selectedCat === (string)$cat['cid']) ? "selected" : ""; ?>>
                            <?php echo htmlspecialchars($cat['cname']); ?>
                        </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </form>

    </div>

    <!-- WORKER RESULTS -->
    <?php if ($selectedCat != "") { ?>
    <div class="glass-card">

        <h2 style="margin-bottom: 20px;">
            <i class="fas fa-user-tie" style="color: var(--primary);"></i>
            Workers joined under "<?php echo htmlspecialchars($selectedCatName); ?>"
        </h2>

        <div class="table-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px;">WID</th>
                        <th>Job</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width: 50px;">Age</th>
                        <th style="width: 70px;">Gender</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="table-action-sticky" style="text-align: center; min-width: 170px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($workers) {
                        foreach ($workers as $w) {
                    ?>
                    <tr>
                        <td><strong>#<?php echo $w['wid']; ?></strong></td>
                        <td style="color: var(--text-muted); white-space: nowrap;"><?php echo htmlspecialchars($w['jname']); ?></td>
                        <td style="font-weight: 600; color: var(--secondary); white-space: nowrap;">
                            <?php echo htmlspecialchars($w['wname']); ?>
                        </td>
                        <td style="max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($w['wgmail']); ?>">
                            <?php echo htmlspecialchars($w['wgmail']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($w['wage']); ?></td>
                        <td><?php echo strtoupper(htmlspecialchars($w['wgender'])); ?></td>
                        <td style="white-space: nowrap;"><?php echo htmlspecialchars($w['wphone']); ?></td>
                        <td style="white-space: nowrap;"><?php echo workerStatusBadge($w['wstatus']); ?></td>

                        <!-- ACTION BUTTONS (STICKY COLUMN) -->
                        <td class="table-action-sticky" style="white-space: nowrap; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                            <?php if ($w['wstatus'] == 2): ?>
                                <a href="rejcet.php?id=<?php echo $w['wid']; ?>&ref=categoryworkerreport.php?cid=<?php echo $selectedCat; ?>"
                                   class="btn-modern btn-danger-modern"
                                   style="padding: 5px 12px; font-size: 11.5px; display: inline-flex; text-decoration: none;"
                                   onclick="return confirm('Change status to REJECTED for <?php echo htmlspecialchars(addslashes($w['wname'])); ?>?')">
                                    <i class="fas fa-times-circle"></i>&nbsp;Reject
                                </a>
                            <?php elseif ($w['wstatus'] == 3): ?>
                                <a href="approve.php?id=<?php echo $w['wid']; ?>&ref=categoryworkerreport.php?cid=<?php echo $selectedCat; ?>"
                                   class="btn-modern btn-success-modern"
                                   style="padding: 5px 12px; font-size: 11.5px; display: inline-flex; text-decoration: none;"
                                   onclick="return confirm('Change status to APPROVED for <?php echo htmlspecialchars(addslashes($w['wname'])); ?>?')">
                                    <i class="fas fa-check-circle"></i>&nbsp;Approve
                                </a>
                            <?php else: ?>
                                <a href="approve.php?id=<?php echo $w['wid']; ?>&ref=categoryworkerreport.php?cid=<?php echo $selectedCat; ?>"
                                   class="btn-modern btn-success-modern"
                                   style="padding: 5px 12px; font-size: 11.5px; display: inline-flex; text-decoration: none;"
                                   onclick="return confirm('Approve worker <?php echo htmlspecialchars(addslashes($w['wname'])); ?>?')">
                                    <i class="fas fa-check-circle"></i>&nbsp;Approve
                                </a>
                                <a href="rejcet.php?id=<?php echo $w['wid']; ?>&ref=categoryworkerreport.php?cid=<?php echo $selectedCat; ?>"
                                   class="btn-modern btn-danger-modern"
                                   style="padding: 5px 12px; font-size: 11.5px; display: inline-flex; text-decoration: none;"
                                   onclick="return confirm('Reject worker <?php echo htmlspecialchars(addslashes($w['wname'])); ?>?')">
                                    <i class="fas fa-times-circle"></i>&nbsp;Reject
                                </a>
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-user-slash fa-2x" style="margin-bottom: 10px; display: block;"></i>
                            No workers found for this category
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
    <?php } ?>

</div>
