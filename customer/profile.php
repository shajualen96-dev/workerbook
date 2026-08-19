<?php

require('../config/autoload.php');
include("customerheader.php");

$dao = new DataAccess();

/* ---------------------------
   LOGIN CHECK
----------------------------*/
if(!isset($_SESSION['crid']))
{
    header("Location: login.php");
    exit();
}

$cid = $_SESSION['crid'];

$msg = "";

/* ---------------------------
   FETCH CUSTOMER DATA
----------------------------*/
$data = $dao->getData(
    "*",
    "cregistration",
    "crid=".$cid
);

$row = $data[0];

/* ---------------------------
   UPDATE PROFILE
----------------------------*/
if(isset($_POST['update']))
{

    $update = array(

        "cname" => $_POST['cname'],
        "cage" => $_POST['cage'],
        "caddress" => $_POST['caddress'],
        "cgender" => $_POST['cgender'],
        "cphone" => $_POST['cphone'],
        "cgmail" => $_POST['cgmail'],
        "cpassword" => $_POST['cpassword']

    );

    if($dao->update(
        $update,
        "cregistration",
        "crid=".$cid
    ))
    {
        $msg = "Profile Updated Successfully";

        $data = $dao->getData(
            "*",
            "cregistration",
            "crid=".$cid
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

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>My Profile</title>

<style>
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
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

<div class="profile-container animate-fade-up">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="profile-card-custom">

        <div class="profile-header-custom">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($row['cname'], 0, 1)); ?>
            </div>
            <h2><?php echo htmlspecialchars($row['cname']); ?></h2>
            <p><i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($row['cgmail']); ?></p>
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
                        <input type="text" name="cname" class="form-input-modern" value="<?php echo htmlspecialchars($row['cname']); ?>" required>
                    </div>

                    <!-- Age -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">Age</label>
                        <input type="number" name="cage" class="form-input-modern" value="<?php echo htmlspecialchars($row['cage']); ?>" required>
                    </div>

                    <!-- Email -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">Email Address</label>
                        <input type="email" name="cgmail" class="form-input-modern" value="<?php echo htmlspecialchars($row['cgmail']); ?>" required>
                    </div>

                    <!-- Phone -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">Phone Number</label>
                        <input type="text" name="cphone" class="form-input-modern" value="<?php echo htmlspecialchars($row['cphone']); ?>" required>
                    </div>

                    <!-- Address -->
                    <div class="form-group-modern grid-full-width">
                        <label class="form-label-modern">Home Address</label>
                        <input type="text" name="caddress" class="form-input-modern" value="<?php echo htmlspecialchars($row['caddress']); ?>" required>
                    </div>

                    <!-- Gender -->
                    <div class="form-group-modern grid-full-width">
                        <label class="form-label-modern">Gender</label>
                        <div class="gender-group-custom">
                            <label>
                                <input type="radio" name="cgender" value="m" <?php echo ($row['cgender'] == "m") ? "checked" : ""; ?>>
                                Male
                            </label>
                            <label>
                                <input type="radio" name="cgender" value="f" <?php echo ($row['cgender'] == "f") ? "checked" : ""; ?>>
                                Female
                            </label>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group-modern grid-full-width">
                        <label class="form-label-modern">Account Password</label>
                        <input type="text" name="cpassword" class="form-input-modern" value="<?php echo htmlspecialchars($row['cpassword']); ?>" required>
                    </div>

                </div>

                <button type="submit" name="update" class="btn-modern btn-primary-modern w-100" style="margin-top: 15px;">
                    <i class="fas fa-save"></i> Save Changes
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>