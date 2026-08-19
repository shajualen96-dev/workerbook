<?php require('../config/autoload.php'); ?>
<?php
$dao = new DataAccess();
include('header.php');
?>

<div id="page-wrapper" class="animate-fade-in">

    <div class="glass-card">

        <h2 style="margin-bottom: 25px;">
            <i class="fas fa-user-tie" style="color: var(--primary);"></i> Worker Registration & Approval List
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

        <div class="table-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px;">WID</th>
                        <th style="width: 50px;">JID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width: 50px;">Age</th>
                        <th style="width: 70px;">Gender</th>
                        <th>Description</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="table-action-sticky" style="text-align: center; min-width: 170px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $workers = $dao->query("SELECT * FROM wregistration ORDER BY wid DESC");

                    if ($workers):
                        foreach ($workers as $row):
                    ?>
                    <tr>
                        <td><strong>#<?php echo $row['wid']; ?></strong></td>
                        <td><?php echo $row['jid']; ?></td>
                        <td style="font-weight: 600; color: var(--secondary); white-space: nowrap;"><?php echo htmlspecialchars($row['wname']); ?></td>
                        <td style="max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($row['wgmail']); ?>">
                            <?php echo htmlspecialchars($row['wgmail']); ?>
                        </td>
                        <td><?php echo $row['wage']; ?></td>
                        <td><?php echo strtoupper($row['wgender']); ?></td>
                        <td style="color: var(--text-muted); max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($row['wdescription']); ?>">
                            <?php echo htmlspecialchars($row['wdescription']); ?>
                        </td>
                        <td style="white-space: nowrap;"><?php echo htmlspecialchars($row['wphone']); ?></td>

                        <!-- STATUS BADGE -->
                        <td style="white-space: nowrap;">
                            <?php if ($row['wstatus'] == 2): ?>
                                <span class="badge-status badge-status-approved">
                                    <span class="status-dot status-dot-approved"></span>Approved
                                </span>
                            <?php elseif ($row['wstatus'] == 3): ?>
                                <span class="badge-status badge-status-cancelled">
                                    <span class="status-dot status-dot-cancelled"></span>Rejected
                                </span>
                            <?php else: ?>
                                <span class="badge-status badge-status-pending">
                                    <span class="status-dot status-dot-pending"></span>Pending
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- ACTION BUTTONS (STICKY COLUMN) -->
                        <td class="table-action-sticky" style="white-space: nowrap; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                            <?php if ($row['wstatus'] == 2): ?>
                                <a href="rejcet.php?id=<?php echo $row['wid']; ?>&ref=viewworkers.php"
                                   class="btn-modern btn-danger-modern"
                                   style="padding: 5px 12px; font-size: 11.5px; display: inline-flex; text-decoration: none;"
                                   onclick="return confirm('Change status to REJECTED for <?php echo htmlspecialchars(addslashes($row['wname'])); ?>?')">
                                    <i class="fas fa-times-circle"></i>&nbsp;Reject
                                </a>
                            <?php elseif ($row['wstatus'] == 3): ?>
                                <a href="approve.php?id=<?php echo $row['wid']; ?>&ref=viewworkers.php"
                                   class="btn-modern btn-success-modern"
                                   style="padding: 5px 12px; font-size: 11.5px; display: inline-flex; text-decoration: none;"
                                   onclick="return confirm('Change status to APPROVED for <?php echo htmlspecialchars(addslashes($row['wname'])); ?>?')">
                                    <i class="fas fa-check-circle"></i>&nbsp;Approve
                                </a>
                            <?php else: ?>
                                <a href="approve.php?id=<?php echo $row['wid']; ?>&ref=viewworkers.php"
                                   class="btn-modern btn-success-modern"
                                   style="padding: 5px 12px; font-size: 11.5px; display: inline-flex; text-decoration: none;"
                                   onclick="return confirm('Approve worker <?php echo htmlspecialchars(addslashes($row['wname'])); ?>?')">
                                    <i class="fas fa-check-circle"></i>&nbsp;Approve
                                </a>
                                <a href="rejcet.php?id=<?php echo $row['wid']; ?>&ref=viewworkers.php"
                                   class="btn-modern btn-danger-modern"
                                   style="padding: 5px 12px; font-size: 11.5px; display: inline-flex; text-decoration: none;"
                                   onclick="return confirm('Reject worker <?php echo htmlspecialchars(addslashes($row['wname'])); ?>?')">
                                    <i class="fas fa-times-circle"></i>&nbsp;Reject
                                </a>
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                        endforeach;
                    else:
                    ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-users fa-2x" style="margin-bottom: 10px; display: block;"></i>
                            No workers registered yet.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>