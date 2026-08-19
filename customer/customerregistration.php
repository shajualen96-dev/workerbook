<?php
require('../config/autoload.php');

$dao = new DataAccess();

$elements = array(

    "cname" => "",
    "cage" => "",
    "caddress" => "",
    "cgender" => "",
    "cphone" => "",
    "cgmail" => "",
    "cpassword" => "",
    "copassword" => ""

);

$form = new FormAssist($elements, $_POST);

$labels = array(

    "cname" => "Customer Name",
    "cage" => "Age",
    "caddress" => "Address",
    "cgender" => "Gender",
    "cphone" => "Phone",
    "cgmail" => "Gmail",
    "cpassword" => "Password",
    "copassword" => "Confirm Password"

);

$rules = array(

    "cname" => array(
        "required" => true,
        "minlength" => 3,
        "maxlength" => 30,
        "alphaspaceonly" => true
    ),

    "cage" => array(
        "required" => true
    ),

    "caddress" => array(
        "required" => true
    ),

    "cgmail" => array(
        "required" => true,
        "email" => true,
        "unique" => array(
            "field" => "cgmail",
            "table" => "cregistration"
        )
    ),

    "cgender" => array(
        "required" => true,
        "exist" => array("m", "f")
    ),

    "cphone" => array(
        "required" => true,
        "integeronly" => true,
        "minlength" => 10,
        "maxlength" => 10
    ),

    "cpassword" => array(
        "required" => true,
        "minlength" => 6
    ),

    "copassword" => array(
        "required" => true,
        "compare" => array(
            "comparewith" => "cpassword",
            "operator" => "="
        )
    )

);

$validator = new FormValidator($rules, $labels);

$msg = "";

if(isset($_POST['home']))
{
    header("Location: index.php");
    exit();
}

if(isset($_POST['register']))
{
    if($validator->validate($_POST))
    {

        $password = $_POST['cpassword'];

        $data = array(

            "cname" => $_POST['cname'],
            "cage" => $_POST['cage'],
            "caddress" => $_POST['caddress'],
            "cgender" => $_POST['cgender'],
            "cphone" => $_POST['cphone'],
            "cgmail" => $_POST['cgmail'],
            "cpassword" => $password,
            "cstatus" => 1

        );

        if($dao->insert($data,"cregistration"))
        {
            $msg = "Registered Successfully!";
        }
        else
        {
            $msg = "Registration Failed!";
        }

    }
    else
    {
        $msg = "Please fix the errors below.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Customer Registration</title>

<style>
        body {
            background: linear-gradient(135deg, #f5f3ff 0%, #e0e7ff 50%, #dbeafe 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            top: -20%;
            left: -10%;
            opacity: 0.1;
            animation: floatAnim 10s ease-in-out infinite alternate;
            pointer-events: none;
        }

        .register-container {
            width: 100%;
            max-width: 650px;
            z-index: 10;
        }

        .glass-card-custom {
            padding: 40px;
        }

        .title-area {
            text-align: center;
            margin-bottom: 30px;
        }

        .title-area h2 {
            font-size: 36px;
            color: var(--secondary);
            font-weight: 800;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .title-area p {
            color: var(--text-muted);
            font-size: 15px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full-width {
            grid-column: span 2;
        }

        .gender-box-custom {
            display: flex;
            gap: 20px;
            align-items: center;
            padding: 12px 16px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
        }

        .gender-box-custom label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
            margin-bottom: 0;
        }

        .gender-box-custom input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: flex-end;
        }

        @media(max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .full-width {
                grid-column: span 1;
            }
            .action-buttons {
                flex-direction: column;
            }
            .action-buttons button, 
            .action-buttons a {
                width: 100%;
            }
            .glass-card-custom {
                padding: 25px 20px;
            }
        }
    </style>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Global CSS -->
    <link rel="stylesheet" href="../css/professional.css">
</head>

<body>

<a href="javascript:void(0);" onclick="window.history.length > 1 ? window.history.back() : window.location.href='login.php';" class="btn-back-global btn-back-floating">
    <i class="fas fa-arrow-left"></i> Back
</a>

<div class="register-container animate-fade-up">
    
    <div class="title-area">
        <h2>Join Us Today</h2>
        <p>Create a customer account to start booking workers instantly</p>
    </div>

    <div class="glass-card glass-card-custom">
        
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
            <div class="form-grid">
                
                <!-- Name -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Full Name</label>
                    <?php echo $form->textBox('cname', array("class" => "form-input-modern", "placeholder" => "Name")); ?>
                    <div class="form-error"><?php echo $validator->error('cname'); ?></div>
                </div>

                <!-- Email -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Email Address</label>
                    <?php echo $form->textBox('cgmail', array("class" => "form-input-modern", "placeholder" => "gmail")); ?>
                    <div class="form-error"><?php echo $validator->error('cgmail'); ?></div>
                </div>

                <!-- Age -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Age</label>
                    <?php echo $form->inputBox('cage', array("class" => "form-input-modern", "placeholder" => ""), "number"); ?>
                    <div class="form-error"><?php echo $validator->error('cage'); ?></div>
                </div>

                <!-- Gender -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Gender</label>
                    <div class="gender-box-custom">
                        <?php
                        echo $form->radioGroup(
                            'cgender',
                            array(),
                            array(
                                "Male" => "m",
                                "Female" => "f"
                            )
                        );
                        ?>
                    </div>
                    <div class="form-error"><?php echo $validator->error('cgender'); ?></div>
                </div>

                <!-- Phone -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Phone Number</label>
                    <?php echo $form->textBox('cphone', array("class" => "form-input-modern", "placeholder" => "10-digit number")); ?>
                    <div class="form-error"><?php echo $validator->error('cphone'); ?></div>
                </div>

                <!-- Address -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Home Address</label>
                    <?php echo $form->textBox('caddress', array("class" => "form-input-modern", "placeholder" => "City, State, Country")); ?>
                    <div class="form-error"><?php echo $validator->error('caddress'); ?></div>
                </div>

                <!-- Password -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Password</label>
                    <?php echo $form->passwordBox('cpassword', array("class" => "form-input-modern", "placeholder" => "At least 6 characters")); ?>
                    <div class="form-error"><?php echo $validator->error('cpassword'); ?></div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Confirm Password</label>
                    <?php echo $form->passwordBox('copassword', array("class" => "form-input-modern", "placeholder" => "Re-type password")); ?>
                    <div class="form-error"><?php echo $validator->error('copassword'); ?></div>
                </div>

            </div>

            <div class="action-buttons">
                <a href="login.php" class="btn-modern btn-secondary-modern">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
                <button type="submit" name="register" class="btn-modern btn-primary-modern">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </div>

        </form>

    </div>

</div>

</body>
</html>