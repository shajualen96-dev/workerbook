<?php
require('../config/autoload.php');
require_once('../config/db_update.php');
include("customerheader.php");

$dao = new DataAccess();

if(!isset($_SESSION['crid']))
{
    header("Location: ../index.php?view=login&role=customer");
    exit();
}

$crid = $_SESSION['crid'];
$msg = "";
$msgtype = "";

/* Handle Payment Submission */
if(isset($_POST['process_payment']))
{
    $plan = $_POST['plan_choice'] ?? 'monthly';
    $pay_method = $_POST['pay_method'] ?? 'UPI';
    $amount = ($plan === 'annual') ? 1499.00 : 199.00;
    
    $today = date('Y-m-d');
    if($plan === 'annual') {
        $expiry = date('Y-m-d', strtotime('+1 year'));
    } else {
        $expiry = date('Y-m-d', strtotime('+1 month'));
    }
    
    $tx_id = "TXN" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
    
    // Update Customer Plan
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if(!$conn->connect_error) {
        $sql = "UPDATE cregistration SET c_plan_type='$plan', c_plan_expires='$expiry' WHERE crid=$crid";
        $conn->query($sql);
        
        $sql_pay = "INSERT INTO platform_payments (user_id, user_type, plan_type, amount, payment_method, transaction_id, payment_date, expiry_date, status)
                    VALUES ($crid, 'customer', '$plan', $amount, '$pay_method', '$tx_id', NOW(), '$expiry', 'completed')";
        $conn->query($sql_pay);
        $conn->close();
        
        $msg = "Payment of ₹" . number_format($amount, 2) . " successful! Your " . ucfirst($plan) . " Plan is active until " . date('d M Y', strtotime($expiry)) . ".";
        $msgtype = "success";
    } else {
        $msg = "Database connection error. Please try again.";
        $msgtype = "error";
    }
}

/* Fetch Customer Info & Bookings Count */
$customer_data = $dao->getData("*", "cregistration", "crid=".$crid);
$c_plan_type = $customer_data[0]['c_plan_type'] ?? 'none';
$c_plan_expires = $customer_data[0]['c_plan_expires'] ?? null;

$bookings = $dao->getData("*", "booking", "crid=".$crid);
$booking_count = is_array($bookings) ? count($bookings) : 0;

$today_date = date('Y-m-d');
$is_subscribed = ($c_plan_expires && $c_plan_expires >= $today_date);
$first_booking_free = ($booking_count == 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Platform Fee Plans</title>
<style>
    .plans-container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 20px;
        font-family: 'Outfit', sans-serif;
    }

    .plans-hero {
        text-align: center;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #ffffff;
        padding: 50px 30px;
        border-radius: var(--radius-lg, 16px);
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(79, 70, 229, 0.25);
        position: relative;
        overflow: hidden;
    }

    .plans-hero h1 {
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 12px;
        color: #ffffff;
    }

    .plans-hero p {
        font-size: 17px;
        color: rgba(255, 255, 255, 0.9);
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .status-banner {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px 25px;
        margin-bottom: 35px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
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
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .status-text h4 {
        font-size: 18px;
        margin: 0 0 4px 0;
        color: #1e293b;
    }

    .status-text p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
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
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.12);
    }

    .plan-card.featured {
        border-color: #4f46e5;
        background: linear-gradient(180deg, #ffffff 0%, #f5f3ff 100%);
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
        letter-spacing: 0.5px;
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
        color: #4f46e5;
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
        background: #4f46e5;
        color: #ffffff;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }

    .btn-plan:hover {
        background: #4338ca;
        transform: translateY(-2px);
    }

    .btn-plan.outline {
        background: transparent;
        border: 2px solid #4f46e5;
        color: #4f46e5;
        box-shadow: none;
    }

    .btn-plan.outline:hover {
        background: rgba(79, 70, 229, 0.05);
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
        border-color: #4f46e5;
        background: #f5f3ff;
    }

    .pay-option input {
        accent-color: #4f46e5;
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
</head>
<body>

<div class="plans-container">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <?php if($msg): ?>
        <div class="alert-banner <?php echo $msgtype; ?>">
            <i class="fas <?php echo $msgtype === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <div><?php echo $msg; ?></div>
        </div>
    <?php endif; ?>

    <div class="plans-hero">
        <h1>Customer Platform Plans</h1>
        <p>Your 1st booking is completely FREE! Subscribe to a platform plan in Rupees (₹) for unlimited worker bookings anytime.</p>
    </div>

    <!-- Status Banner -->
    <div class="status-banner">
        <div class="status-info">
            <div class="status-icon">
                <i class="fas <?php echo $is_subscribed ? 'fa-shield-alt' : ($first_booking_free ? 'fa-gift' : 'fa-exclamation-triangle'); ?>"></i>
            </div>
            <div class="status-text">
                <h4>
                    <?php 
                    if ($is_subscribed) {
                        echo "Active Subscription: " . ucfirst($c_plan_type) . " Plan";
                    } elseif ($first_booking_free) {
                        echo "🎁 1st Booking is FREE!";
                    } else {
                        echo "1st Booking Used - Subscription Required";
                    }
                    ?>
                </h4>
                <p>
                    <?php 
                    if ($is_subscribed) {
                        echo "Your plan is valid until <strong>" . date('d M Y', strtotime($c_plan_expires)) . "</strong>. You have unlimited worker bookings.";
                    } elseif ($first_booking_free) {
                        echo "You haven't booked any worker yet. Your very 1st booking requires <strong>no platform fee</strong>!";
                    } else {
                        echo "You have completed 1 free booking. Please purchase a Monthly or Annual plan to continue booking workers.";
                    }
                    ?>
                </p>
            </div>
        </div>
        <?php if($is_subscribed): ?>
            <a href="workers.php" class="btn-plan" style="width: auto; padding: 10px 24px;">Book a Worker Now</a>
        <?php elseif($first_booking_free): ?>
            <a href="category.php" class="btn-plan" style="width: auto; padding: 10px 24px; background: #10b981;">Use Free Booking</a>
        <?php endif; ?>
    </div>

    <!-- Pricing Grid -->
    <div class="plans-grid">
        <!-- Monthly Plan -->
        <div class="plan-card">
            <div class="plan-header">
                <h3>Monthly Plan</h3>
                <p style="color: #64748b; font-size: 14px; margin: 0;">Great for short term & flexible bookings</p>
            </div>
            <div class="plan-price">
                ₹199 <span>/ month</span>
            </div>
            <ul class="plan-features">
                <li><i class="fas fa-check-circle"></i> Unlimited Worker Bookings</li>
                <li><i class="fas fa-check-circle"></i> Priority Worker Matching</li>
                <li><i class="fas fa-check-circle"></i> Instant Cancellation & Support</li>
                <li><i class="fas fa-check-circle"></i> Valid for 30 Days</li>
            </ul>
            <button class="btn-plan outline" onclick="openPaymentModal('monthly', 199)">Choose Monthly Plan</button>
        </div>

        <!-- Annual Plan -->
        <div class="plan-card featured">
            <div class="popular-badge">Best Value (Save 35%)</div>
            <div class="plan-header">
                <h3>Annual Plan</h3>
                <p style="color: #64748b; font-size: 14px; margin: 0;">Best value for year-round hassle-free services</p>
            </div>
            <div class="plan-price">
                ₹1,499 <span>/ year</span>
            </div>
            <ul class="plan-features">
                <li><i class="fas fa-check-circle"></i> Unlimited Worker Bookings for 365 Days</li>
                <li><i class="fas fa-check-circle"></i> Highest Priority Worker Access</li>
                <li><i class="fas fa-check-circle"></i> 24/7 Dedicated Support</li>
                <li><i class="fas fa-check-circle"></i> Save ₹889 compared to monthly</li>
            </ul>
            <button class="btn-plan" onclick="openPaymentModal('annual', 1499)">Choose Annual Plan</button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal-overlay" id="payModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Complete Payment (INR ₹)</h3>
            <span class="close-modal" onclick="closePaymentModal()">&times;</span>
        </div>
        
        <form method="POST" id="paymentForm">
            <input type="hidden" name="plan_choice" id="selectedPlanInput" value="monthly">
            <input type="hidden" name="process_payment" value="1">
            
            <div style="background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
                <div style="font-size: 14px; color: #64748b;">Selected Plan</div>
                <div id="planSummaryTitle" style="font-size: 18px; font-weight: 700; color: #0f172a;">Monthly Plan</div>
                <div id="planSummaryPrice" style="font-size: 26px; font-weight: 800; color: #4f46e5; margin-top: 4px;">₹199.00</div>
            </div>

            <div style="font-weight: 600; margin-bottom: 10px; color: #334155; font-size: 14px;">Select Payment Method:</div>

            <label class="pay-option">
                <input type="radio" name="pay_method" value="UPI (GPay / PhonePe / Paytm)" checked>
                <i class="fas fa-mobile-alt" style="font-size: 18px; color: #4f46e5;"></i>
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
                <i class="fas fa-lock"></i> Pay Now & Activate Plan
            </button>
        </form>
    </div>
</div>

<script>
function openPaymentModal(plan, price) {
    document.getElementById('selectedPlanInput').value = plan;
    document.getElementById('planSummaryTitle').innerText = plan === 'annual' ? 'Annual Plan (365 Days)' : 'Monthly Plan (30 Days)';
    document.getElementById('planSummaryPrice').innerText = '₹' + price.toLocaleString('en-IN') + '.00';
    document.getElementById('payModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('payModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('payModal');
    if (event.target === modal) {
        closePaymentModal();
    }
}
</script>

</body>
</html>
