<?php
require('../config/autoload.php');

$dao = new DataAccess();

$elements = array(
    "jid" => "",
    "wname" => "",
    "wgmail" => "",
    "wage" => "",
    "wgender" => "",
    "wdescription" => "",
    "wphone" => "",
    "wpassword" => "",
    "cpassword" => ""
);

$form = new FormAssist($elements, $_POST);

$labels = array(
    "jid" => "Job",
    "wname" => "Worker Name",
    "wgmail" => "Email",
    "wage" => "Age",
    "wgender" => "Gender",
    "wdescription" => "Description",
    "wphone" => "Phone",
    "wpassword" => "Password",
    "cpassword" => "Confirm Password"
);

$rules = array(

    "jid" => array("required" => true),

    "wname" => array(
        "required" => true,
        "minlength" => 3,
        "maxlength" => 30,
        "alphaspaceonly" => true
    ),

    "wgmail" => array(
        "required" => true,
        "email" => true,
        "unique" => array(
            "field" => "wgmail",
            "table" => "wregistration"
        )
    ),

    "wage" => array(
        "required" => true
    ),

    "wgender" => array(
        "required" => true,
        "exist" => array("m", "f")
    ),

    "wphone" => array(
        "required" => true,
        "integeronly" => true,
        "minlength" => 10,
        "maxlength" => 10
    ),

    "wpassword" => array(
        "required" => true,
        "minlength" => 6
    ),

    "cpassword" => array(
        "required" => true,
        "compare" => array(
            "comparewith" => "wpassword",
            "operator" => "="
        )
    )
);

$validator = new FormValidator($rules, $labels);

$msg = "";

if(isset($_POST['register']))
{
    if($validator->validate($_POST))
    {
        $password = $_POST['wpassword'];

        $data = array(
            "jid" => $_POST['jid'],
            "wname" => $_POST['wname'],
            "wgmail" => $_POST['wgmail'],
            "wage" => $_POST['wage'],
            "wgender" => $_POST['wgender'],
            "wdescription" => $_POST['wdescription'],
            "wpass" => $password,
            "wphone" => $_POST['wphone'],
            "wstatus" => 1
        );

        if($dao->insert($data,"wregistration"))
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

<title>Worker Registration</title>

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
            background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
            bottom: -20%;
            right: -10%;
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
            accent-color: var(--accent);
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
        <h2>Worker Registration</h2>
        <p>Register a professional account and start receiving customer requests</p>
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
                    <?php echo $form->textBox('wname', array("class" => "form-input-modern", "placeholder" => "John Doe")); ?>
                    <div class="form-error"><?php echo $validator->error('wname'); ?></div>
                </div>

                <!-- Email -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Email Address</label>
                    <?php echo $form->textBox('wgmail', array("class" => "form-input-modern", "placeholder" => "worker@example.com")); ?>
                    <div class="form-error"><?php echo $validator->error('wgmail'); ?></div>
                </div>

                <!-- Age -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Age</label>
                    <?php echo $form->inputBox('wage', array("class" => "form-input-modern", "placeholder" => "25"), "number"); ?>
                    <div class="form-error"><?php echo $validator->error('wage'); ?></div>
                </div>

                <!-- Gender -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Gender</label>
                    <div class="gender-box-custom">
                        <?php
                        echo $form->radioGroup(
                            'wgender',
                            array(),
                            array(
                                "Male" => "m",
                                "Female" => "f"
                            )
                        );
                        ?>
                    </div>
                    <div class="form-error"><?php echo $validator->error('wgender'); ?></div>
                </div>

                <!-- Job category drop down -->
                <div class="form-group-modern full-width">
                    <label class="form-label-modern">Select Job Speciality</label>
                    <?php
                    $options = $dao->createOptions('jname','jid','job');
                    $options = array("" => "-- Select Job --") + $options;
                    
                    echo $form->dropDownList(
                        'jid',
                        array("class" => "form-input-modern"),
                        $options
                    );
                    ?>
                    <div class="form-error"><?php echo $validator->error('jid'); ?></div>
                </div>

                <!-- Phone -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Phone Number</label>
                    <?php echo $form->textBox('wphone', array("class" => "form-input-modern", "placeholder" => "10-digit number")); ?>
                    <div class="form-error"><?php echo $validator->error('wphone'); ?></div>
                </div>

                <!-- Description -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Work Description</label>
                    <?php echo $form->textBox('wdescription', array("class" => "form-input-modern", "placeholder" => "Brief bio / experience...")); ?>
                </div>

                <!-- Password -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Password</label>
                    <?php echo $form->passwordBox('wpassword', array("class" => "form-input-modern", "placeholder" => "At least 6 characters")); ?>
                    <div class="form-error"><?php echo $validator->error('wpassword'); ?></div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group-modern">
                    <label class="form-label-modern">Confirm Password</label>
                    <?php echo $form->passwordBox('cpassword', array("class" => "form-input-modern", "placeholder" => "Re-type password")); ?>
                    <div class="form-error"><?php echo $validator->error('cpassword'); ?></div>
                </div>

            </div>

            <div class="action-buttons">
                <a href="login.php" class="btn-modern btn-secondary-modern">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
                <button type="submit" name="register" class="btn-modern btn-primary-modern" style="background: linear-gradient(135deg, var(--accent) 0%, #0891b2 100%); box-shadow: 0 4px 14px rgba(6, 182, 212, 0.3);">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </div>

        </form>

    </div>

</div>

</body>
</html>