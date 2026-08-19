<?php
require('../config/autoload.php');
require_once('../config/db_update.php');

$dao = new DataAccess();

if(!isset($_SESSION['wid']))
{
    header("Location: ../index.php?view=login&role=worker");
    exit();
}

$wid = $_SESSION['wid'];
$msg = "";
$msgtype = "";

/* Handle Worker Payment Submission */
if(isset($_POST['process_worker_payment']))
{
    $plan = $_POST['plan_choice'] ?? 'monthly';
    $pay_method = $_POST['pay_method'] ?? 'UPI';
    $amount = ($plan === 'annual') ? 2499.00 : 299.00;
    
    $today = date('Y-m-d');
    if($plan === 'annual') {
        $expiry = date('Y-m-d', strtotime('+1 year'));
    } else {
        $expiry = date('Y-m-d', strtotime('+1 month'));
    }
    
    $tx_id = "WTXN" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
    
    // Update Worker Plan
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if(!$conn->connect_error) {
        $sql = "UPDATE wregistration SET w_plan_type='$plan', w_plan_expires='$expiry' WHERE wid=$wid";
        $conn->query($sql);
        
        $sql_pay = "INSERT INTO platform_payments (user_id, user_type, plan_type, amount, payment_method, transaction_id, payment_date, expiry_date, status)
                    VALUES ($wid, 'worker', '$plan', $amount, '$pay_method', '$tx_id', NOW(), '$expiry', 'completed')";
        $conn->query($sql_pay);
        $conn->close();
        
        $msg = "Payment of ₹" . number_format($amount, 2) . " successful! Your " . ucfirst($plan) . " Subscription is active until " . date('d M Y', strtotime($expiry)) . ".";
        $msgtype = "success";
    } else {
        $msg = "Database connection error. Please try again.";
        $msgtype = "error";
    }
}

/* Fetch Worker Info */
$worker_data = $dao->getData("*", "wregistration", "wid=".$wid);
$wname = $worker_data[0]['wname'] ?? "";
$w_plan_type = $worker_data[0]['w_plan_type'] ?? 'none';
$w_plan_expires = $worker_data[0]['w_plan_expires'] ?? null;

$today_date = date('Y-m-d');
$is_subscribed = ($w_plan_expires && $w_plan_expires >= $today_date);

if (!$is_subscribed && empty($msg)) {
    $msg = "Subscription Plan Required! Please select and subscribe to a platform plan below to unlock all worker portal activities.";
    $msgtype = "error";
}

/* Include Worker Header Sidebar */
include("workerheader.php");
?>

<style>
    .worker-main {
        margin-left: 270px;
        padding: 40px;
        min-height: 100vh;
        background: #f8fafc;
        animation: fadeIn 0.4s ease-out;
    }

    @media (max-width: 992px) {
        .worker-main {
            margin-left: 0;
            padding: 20px;
        }
    }

    .plans-container {
        max-width: 1050px;
        margin: 0 auto;
    }

    .plans-hero {
        text-align: center;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        padding: 45px 30px;
        border-radius: 20px;
        margin-bottom: 35px;
        box-shadow: 0 10px 30px rgba(2, 132, 199, 0.25);
    }

    .plans-hero h1 {
        font-size: 34px;
        font-weight: 800;
        margin-bottom: 10px;
        color: #ffffff;
    }

    .plans-hero p {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.9);
        max-width: 600px;
        margin: 0 auto;
    }

    .status-banner {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px 25px;
        margin-bottom: 35px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .status-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .status-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(2, 132, 199, 0.1);
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .plan-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 35px 30px;
        border: 2px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(2, 132, 199, 0.15);
    }

    .plan-card.featured {
        border-color: #0284c7;
        background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%);
    }

    .popular-badge {
        position: absolute;
        top: -14px;
        right: 25px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .plan-header h3 {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .plan-price {
        font-size: 40px;
        font-weight: 800;
        color: #0284c7;
        margin: 15px 0;
        display: flex;
        align-items: baseline;
        gap: 5px;
    }

    .plan-price span {
        font-size: 16px;
        color: #64748b;
        font-weight: 500;
    }

    .plan-features {
        list-style: none;
        padding: 0;
        margin: 25px 0;
        flex-grow: 1;
    }

    .plan-features li {
        padding: 10px 0;
        color: #475569;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .plan-features li i {
        color: #10b981;
    }

    .btn-plan {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        border: none;
        background: #0284c7;
        color: #ffffff;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        box-shadow: 0 4px 15px rgba(2, 132, 199, 0.3);
        text-decoration: none;
        display: inline-block;
    }

    .btn-plan:hover {
        background: #0369a1;
        transform: translateY(-2px);
    }

    .btn-plan.outline {
        background: transparent;
        border: 2px solid #0284c7;
        color: #0284c7;
        box-shadow: none;
    }

    .btn-plan.outline:hover {
        background: rgba(2, 132, 199, 0.05);
    }

    /* Modal Styling */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(6px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal-card {
        background: #ffffff;
        width: 90%;
        max-width: 500px;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        animation: modalSlide 0.3s ease-out;
    }

    @keyframes modalSlide {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 15px;
    }

    .modal-header h3 {
        font-size: 20px;
        margin: 0;
        color: #0f172a;
    }

    .close-modal {
        cursor: pointer;
        font-size: 20px;
        color: #94a3b8;
    }

    .pay-option {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pay-option:hover, .pay-option.selected {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .pay-option input {
        accent-color: #0284c7;
    }

    .alert-banner {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-banner.success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-banner.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
</style>

<div class="worker-main">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="plans-container">

        <?php if($msg): ?>
            <div class="alert-banner <?php echo $msgtype; ?>">
                <i class="fas <?php echo $msgtype === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <div><?php echo $msg; ?></div>
            </div>
        <?php endif; ?>

        <div class="plans-hero">
            <h1>Worker Platform Plans</h1>
            <p>Subscribe to a platform plan in Rupees (₹) to access customer job requests, receive direct bookings, and grow your income.</p>
        </div>

        <!-- Status Banner -->
        <div class="status-banner">
            <div class="status-info">
                <div class="status-icon">
                    <i class="fas <?php echo $is_subscribed ? 'fa-user-check' : 'fa-lock'; ?>"></i>
                </div>
                <div class="status-text">
                    <h4 style="margin:0 0 4px 0;">
                        <?php 
                        if ($is_subscribed) {
                            echo "Active Worker Subscription: " . ucfirst($w_plan_type) . " Plan";
                        } else {
                            echo "Subscription Required to Access Worker Portal";
                        }
                        ?>
                    </h4>
                    <p style="margin:0; color:#64748b; font-size:14px;">
                        <?php 
                        if ($is_subscribed) {
                            echo "Your worker plan is active until <strong>" . date('d M Y', strtotime($w_plan_expires)) . "</strong>. You have full access to customer bookings.";
                        } else {
                            echo "To log in and accept customer bookings, please select and subscribe to a platform plan below in Rupees (₹).";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <?php if($is_subscribed): ?>
                <a href="home.php" class="btn-plan" style="width: auto; padding: 10px 24px;">Go to Dashboard</a>
            <?php endif; ?>
        </div>

        <!-- Pricing Grid -->
        <div class="plans-grid">
            <!-- Monthly Plan -->
            <div class="plan-card">
                <div class="plan-header">
                    <h3>Monthly Plan</h3>
                    <p style="color: #64748b; font-size: 14px; margin: 0;">Flexible monthly access for active workers</p>
                </div>
                <div class="plan-price">
                    ₹299 <span>/ month</span>
                </div>
                <ul class="plan-features">
                    <li><i class="fas fa-check-circle"></i> Receive Customer Job Bookings</li>
                    <li><i class="fas fa-check-circle"></i> Manage & Approve Bookings</li>
                    <li><i class="fas fa-check-circle"></i> Direct Customer Contact Info</li>
                    <li><i class="fas fa-check-circle"></i> 30 Days Full Access</li>
                </ul>
                <button class="btn-plan outline" onclick="openWorkerPaymentModal('monthly', 299)">Subscribe Monthly (₹299)</button>
            </div>

            <!-- Annual Plan -->
            <div class="plan-card featured">
                <div class="popular-badge">Best Value (Save over 30%)</div>
                <div class="plan-header">
                    <h3>Annual Plan</h3>
                    <p style="color: #64748b; font-size: 14px; margin: 0;">Year-round unlimited platform access</p>
                </div>
                <div class="plan-price">
                    ₹2,499 <span>/ year</span>
                </div>
                <ul class="plan-features">
                    <li><i class="fas fa-check-circle"></i> 365 Days Full Worker Access</li>
                    <li><i class="fas fa-check-circle"></i> Top Priority Listing to Customers</li>
                    <li><i class="fas fa-check-circle"></i> Verified Worker Badge</li>
                    <li><i class="fas fa-check-circle"></i> Save ₹1,089 compared to monthly</li>
                </ul>
                <button class="btn-plan" onclick="openWorkerPaymentModal('annual', 2499)">Subscribe Annual (₹2,499)</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal-overlay" id="payModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Worker Payment (INR ₹)</h3>
            <span class="close-modal" onclick="closeWorkerPaymentModal()">&times;</span>
        </div>
        
        <form method="POST" id="paymentForm">
            <input type="hidden" name="plan_choice" id="selectedPlanInput" value="monthly">
            <input type="hidden" name="process_worker_payment" value="1">
            
            <div style="background: #f0f9ff; padding: 15px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
                <div style="font-size: 14px; color: #64748b;">Selected Worker Plan</div>
                <div id="planSummaryTitle" style="font-size: 18px; font-weight: 700; color: #0f172a;">Monthly Plan</div>
                <div id="planSummaryPrice" style="font-size: 26px; font-weight: 800; color: #0284c7; margin-top: 4px;">₹299.00</div>
            </div>

            <div style="font-weight: 600; margin-bottom: 10px; color: #334155; font-size: 14px;">Select Payment Method:</div>

            <label class="pay-option">
                <input type="radio" name="pay_method" value="UPI (GPay / PhonePe / Paytm)" checked>
                <i class="fas fa-mobile-alt" style="font-size: 18px; color: #0284c7;"></i>
                <div>
                    <strong style="font-size: 14px;">UPI / QR Code</strong>
                    <div style="font-size: 12px; color: #64748b;">Google Pay, PhonePe, Paytm, BHIM</div>
                </div>
            </label>

            <label class="pay-option">
                <input type="radio" name="pay_method" value="Credit/Debit Card">
                <i class="fas fa-credit-card" style="font-size: 18px; color: #10b981;"></i>
                <div>
                    <strong style="font-size: 14px;">Credit / Debit Card</strong>
                    <div style="font-size: 12px; color: #64748b;">Visa, MasterCard, RuPay</div>
                </div>
            </label>

            <label class="pay-option">
                <input type="radio" name="pay_method" value="Net Banking">
                <i class="fas fa-university" style="font-size: 18px; color: #f59e0b;"></i>
                <div>
                    <strong style="font-size: 14px;">Net Banking</strong>
                    <div style="font-size: 12px; color: #64748b;">SBI, HDFC, ICICI, Axis & others</div>
                </div>
            </label>

            <button type="submit" class="btn-plan" style="margin-top: 15px;">
                <i class="fas fa-lock"></i> Pay Now & Activate Worker Subscription
            </button>
        </form>
    </div>
</div>

<script>
function openWorkerPaymentModal(plan, price) {
    document.getElementById('selectedPlanInput').value = plan;
    document.getElementById('planSummaryTitle').innerText = plan === 'annual' ? 'Worker Annual Plan (365 Days)' : 'Worker Monthly Plan (30 Days)';
    document.getElementById('planSummaryPrice').innerText = '₹' + price.toLocaleString('en-IN') + '.00';
    document.getElementById('payModal').style.display = 'flex';
}

function closeWorkerPaymentModal() {
    document.getElementById('payModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('payModal');
    if (event.target === modal) {
        closeWorkerPaymentModal();
    }
}
</script>

</body>
</html>
