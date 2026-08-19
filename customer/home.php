<?php
require('../config/autoload.php');
include("customerheader.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Home - Worker Booking</title>
    <!-- FontAwesome & Google Fonts are already in customerheader.php, but let's make sure we styling classes locally too -->
    <style>
        .hero-banner {
            background: linear-gradient(135deg, #f5f3ff 0%, #e0e7ff 100%);
            padding: 80px 20px;
            color: var(--secondary);
            text-align: center;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            margin-bottom: 50px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-top: none;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 80% 20%, rgba(79, 70, 229, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-content h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .hero-content p {
            font-size: 18px;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 30px auto;
            line-height: 1.6;
        }

        .quick-action-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 60px 20px;
        }

        .section-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .section-header h2 {
            font-size: 32px;
            color: var(--secondary);
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .action-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: 35px 25px;
            text-align: center;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .action-icon {
            width: 70px;
            height: 70px;
            border-radius: var(--radius-md);
            background: rgba(79, 70, 229, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 22px;
            transition: var(--transition);
        }

        .action-card:hover .action-icon {
            background: var(--primary);
            color: #ffffff;
            transform: scale(1.08) rotate(5deg);
        }

        .action-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 10px;
            font-family: 'Outfit', sans-serif;
        }

        .action-desc {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 24px;
            flex-grow: 1;
        }

        @media(max-width: 768px) {
            .hero-banner {
                padding: 60px 15px;
            }
            .hero-content h1 {
                font-size: 34px;
            }
            .hero-content p {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>

<!-- HERO SECTION -->
<section class="hero-banner animate-fade-in">
    <div class="hero-content animate-fade-up">
        <h1>Easy & Fast Worker Booking</h1>
        <p>Book verified, local professionals for electrical, plumbing, cleaning, carpentry, painting work, and more with instant updates.</p>
        <a href="category.php" class="btn-modern btn-primary-modern">
            <i class="fas fa-search"></i> Browse Categories
        </a>
    </div>
</section>

<!-- QUICK ACTIONS -->
<section class="quick-action-container">
    <div class="section-header animate-fade-up">
        <h2>Quick Dashboard Actions</h2>
    </div>

    <div class="actions-grid animate-fade-up">
        <!-- CATEGORIES -->
        <div class="action-card" onclick="window.location='category.php'">
            <div class="action-icon">
                <i class="fas fa-th-large"></i>
            </div>
            <div class="action-title">Browse Categories</div>
            <div class="action-desc">Explore different job profiles and find standard pricing and verified experts.</div>
            <span class="btn-modern btn-secondary-modern" style="padding: 8px 18px; font-size: 13px;">Explore</span>
        </div>

        <!-- MY BOOKINGS -->
        <div class="action-card" onclick="window.location='<?php echo isset($_SESSION['crid']) ? 'viewbookings.php' : '../index.php?view=login&role=customer'; ?>'">
            <div class="action-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="action-title">Manage Bookings</div>
            <div class="action-desc">Track status of active requests, view details, or send feedback to hired workers.</div>
            <span class="btn-modern btn-secondary-modern" style="padding: 8px 18px; font-size: 13px;"><?php echo isset($_SESSION['crid']) ? 'Manage' : 'Login Required'; ?></span>
        </div>

        <!-- PROFILE -->
        <div class="action-card" onclick="window.location='<?php echo isset($_SESSION['crid']) ? 'profile.php' : '../index.php?view=login&role=customer'; ?>'">
            <div class="action-icon">
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="action-title">My Profile</div>
            <div class="action-desc">Update registration details, phone number, address, and configure passwords.</div>
            <span class="btn-modern btn-secondary-modern" style="padding: 8px 18px; font-size: 13px;"><?php echo isset($_SESSION['crid']) ? 'Edit Profile' : 'Login Required'; ?></span>
        </div>
    </div>
</section>

</body>
</html>