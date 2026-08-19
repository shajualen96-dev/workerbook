<?php
require('../config/autoload.php');
include("customerheader.php");

$dao = new DataAccess();

/* ---------------------------
   FETCH CATEGORY
----------------------------*/
$rows = $dao->getData("*","category");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Worker Categories</title>

<style>
        .page-header {
            margin-bottom: 40px;
            text-align: center;
            animation: fadeInUp 0.5s ease-out;
        }

        .page-header h1 {
            font-size: 38px;
            color: var(--secondary);
            font-weight: 800;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 16px;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
    </style>
</head>

<body>

<div class="main-container">

    <div style="margin-bottom: 20px;">
        <button type="button" onclick="if(document.referrer && document.referrer.indexOf(window.location.host) !== -1) { history.back(); } else { window.location.href='home.php'; }" class="btn-back-global">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="page-header">
        <h1>Worker Categories</h1>
        <p>Select a category to view available specialized jobs</p>
    </div>

    <?php
    if($rows)
    {
    ?>

    <div class="grid-modern animate-fade-up">

        <?php
        foreach($rows as $row)
        {
        ?>

        <div class="card-modern">
            
            <div class="card-image-wrapper">
                <img src="../uploads/<?php echo $row['cimage']; ?>" class="card-image" alt="<?php echo htmlspecialchars($row['cname']); ?>">
            </div>

            <div class="card-body-modern">

                <h3 class="card-title-modern">
                    <?php echo htmlspecialchars($row['cname']); ?>
                </h3>

                <p class="card-description-modern">
                    <?php echo htmlspecialchars($row['cdescription']); ?>
                </p>

                <a href="jobs.php?cid=<?php echo $row['cid']; ?>" class="btn-modern btn-primary-modern w-100" style="margin-top: auto;">
                    <i class="fas fa-search-plus"></i> View Jobs
                </a>

            </div>

        </div>

        <?php
        }
        ?>

    </div>

    <?php
    }
    else
    {
    ?>

    <div class="glass-card text-center" style="margin-top: 50px; padding: 50px;">
        <i class="fas fa-folder-open fa-3x" style="color: var(--text-muted); margin-bottom: 15px;"></i>
        <h3 style="color: var(--text-muted);">No Categories Available</h3>
        <p style="color: var(--text-muted); margin-top: 5px;">Check back later for updated worker roles.</p>
    </div>

    <?php
    }
    ?>

</div>

</body>
</html>