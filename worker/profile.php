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
$data = $dao->getData("*", "wregistration", "wid=".$wid);
$row = $data[0] ?? [];
$w_plan_expires = $row['w_plan_expires'] ?? null;
$today_str = date('Y-m-d');
if (!$w_plan_expires || $w_plan_expires < $today_str) {
    header("Location: plans.php");
    echo "<script>location.replace('plans.php');</script>";
    exit();
}

include("workerheader.php");

$msg = "";

/* ---------------------------
   UPDATE PROFILE
----------------------------*/
if(isset($_POST['update']))
{

    $update = array(

        "wname" => $_POST['wname'],
        "wage" => $_POST['wage'],
        "wgender" => $_POST['wgender'],
        "wdescription" => $_POST['wdescription'],
        "wphone" => $_POST['wphone'],
        "wgmail" => $_POST['wgmail'],
        "wpass" => $_POST['wpass']

    );

    if($dao->update(
        $update,
        "wregistration",
        "wid=".$wid
    ))
    {
        $msg = "Profile Updated Successfully";

        $data = $dao->getData(
            "*",
            "wregistration",
            "wid=".$wid
        );

        $row = $data[0];
    }
    else
    {
        $msg = "Profile Update Failed";
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile</title>

    <style>
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .worker-main {
            margin-left: 270px;
            padding: 40px;
            animation: fadeIn 0.4s ease-out;
        }

        .profile-card-custom {
            padding: 0;
            overflow: hidden;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            background: #ffffff;
            box-shadow: var(--shadow-md);
        }

        .profile-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
            padding: 50px 30px;
            text-align: center;
            color: #ffffff;
        }

        .avatar-circle {
            width: 100px;
            height: 100px;
            background: #ffffff;
            color: var(--primary);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 44px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            margin-bottom: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            border: 3px solid rgba(255, 255, 255, 0.2);
            animation: floatAnim 4s ease-in-out infinite;
        }

        .profile-header-custom h2 {
            color: #ffffff;
            font-size: 32px;
            margin-bottom: 5px;
        }

        .profile-header-custom p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 15px;
        }

        .profile-body-custom {
            padding: 40px;
        }

        .form-grid-custom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .grid-full-width {
            grid-column: span 2;
        }

        .gender-group-custom {
            display: flex;
            gap: 25px;
            align-items: center;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background: #ffffff;
        }

        .gender-group-custom label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0;
        }

        .gender-group-custom input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        @media(max-width: 1024px) {
            .worker-main {
                margin-left: 80px;
                padding: 25px;
            }
        }

        @media(max-width: 768px) {
            .form-grid-custom {
                grid-template-columns: 1fr;
            }
            .grid-full-width {
                grid-column: span 1;
            }
            .profile-body-custom {
                padding: 25px 20px;
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

    <div class="profile-container animate-fade-up">

        <div class="profile-card-custom">

            <div class="profile-header-custom">
                <div class="avatar-circle">
                    <?php echo strtoupper(substr($row['wname'], 0, 1)); ?>
                </div>
                <h2><?php echo htmlspecialchars($row['wname']); ?></h2>
                <p><i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($row['wgmail']); ?></p>
            </div>

            <div class="profile-body-custom">

                <?php if($msg != "") { 
                    $alertClass = (strpos(strtolower($msg), 'success') !== false) ? 'alert-success-modern' : 'alert-error-modern';
                    $icon = (strpos(strtolower($msg), 'success') !== false) ? 'fa-check-circle' : 'fa-exclamation-circle';
                ?>
                    <div class="alert-modern <?php echo $alertClass; ?>">
                        <i class="fas <?php echo $icon; ?>"></i>
                        <?php echo $msg; ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="form-grid-custom">

                        <!-- Name -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">Full Name</label>
                            <input type="text" name="wname" class="form-input-modern" value="<?php echo htmlspecialchars($row['wname']); ?>" required>
                        </div>

                        <!-- Age -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">Age</label>
                            <input type="number" name="wage" class="form-input-modern" value="<?php echo htmlspecialchars($row['wage']); ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">Email Address</label>
                            <input type="email" name="wgmail" class="form-input-modern" value="<?php echo htmlspecialchars($row['wgmail']); ?>" required>
                        </div>

                        <!-- Phone -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">Phone Number</label>
                            <input type="text" name="wphone" class="form-input-modern" value="<?php echo htmlspecialchars($row['wphone']); ?>" required>
                        </div>

                        <!-- Description -->
                        <div class="form-group-modern grid-full-width">
                            <label class="form-label-modern">Worker Bio / Description</label>
                            <textarea name="wdescription" class="form-input-modern" rows="4" required><?php echo htmlspecialchars($row['wdescription']); ?></textarea>
                        </div>

                        <!-- Gender -->
                        <div class="form-group-modern grid-full-width">
                            <label class="form-label-modern">Gender</label>
                            <div class="gender-group-custom">
                                <label>
                                    <input type="radio" name="wgender" value="m" <?php echo ($row['wgender'] == "m") ? "checked" : ""; ?>>
                                    Male
                                </label>
                                <label>
                                    <input type="radio" name="wgender" value="f" <?php echo ($row['wgender'] == "f") ? "checked" : ""; ?>>
                                    Female
                                </label>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group-modern grid-full-width">
                            <label class="form-label-modern">Account Password</label>
                            <input type="text" name="wpass" class="form-input-modern" value="<?php echo htmlspecialchars($row['wpass']); ?>" required>
                        </div>

                    </div>

                    <button type="submit" name="update" class="btn-modern btn-primary-modern w-100" style="margin-top: 15px;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>
