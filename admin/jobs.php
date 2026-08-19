<?php
require('../config/autoload.php');
include("header.php");

$file = new FileUpload();
$dao  = new DataAccess();

/* ---------------------------
   DELETE
----------------------------*/
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $dao->delete("job", "jid=" . $id);

    echo "<script>window.location='jobs.php';</script>";
    exit();
}

/* ---------------------------
   EDIT FETCH
----------------------------*/
$editid = "";

$editdata = array(
    "jname" => "",
    "jdescription" => "",
    "cid" => "",
    "jimage" => ""
);

if (isset($_GET['edit'])) {

    $editid = $_GET['edit'];

    $result = $dao->getData("*", "job", "jid=" . $editid);

    if ($result) {
        foreach ($result as $row) {
            $editdata = $row;
        }
    }
}

/* ---------------------------
   FORM
----------------------------*/
$elements = array(
    "jname" => $editdata['jname'],
    "jdescription" => $editdata['jdescription'],
    "cid" => $editdata['cid'],
    "jimage" => ""
);

$form = new FormAssist($elements, $_POST);

$labels = array(
    "jname" => "Job Name",
    "jdescription" => "Description",
    "cid" => "Category Name",
    "jimage" => "Image"
);

$rules = array(
    "jname" => array("required" => true),
    "jdescription" => array("required" => true),
    "cid" => array("required" => true)
);

$validator = new FormValidator($rules, $labels);

/* ---------------------------
   INSERT
----------------------------*/
if (isset($_POST['btn_insert'])) {

    if ($validator->validate($_POST)) {

        if ($fileName = $file->doUploadRandom(
            $_FILES['jimage'],
            array('.jpg', '.png', '.jpeg'),
            1000000,
            1,
            '../uploads'
        )) {

            $data = array(
                "jname" => $_POST['jname'],
                "jdescription" => $_POST['jdescription'],
                "cid" => $_POST['cid'],
                "jimage" => $fileName
            );

            $dao->insert($data, "job");

            echo "<script>window.location='jobs.php';</script>";
            exit();
        }
    }
}

/* ---------------------------
   UPDATE
----------------------------*/
if (isset($_POST['btn_update'])) {

    if ($validator->validate($_POST)) {

        $data = array(
            "jname" => $_POST['jname'],
            "jdescription" => $_POST['jdescription'],
            "cid" => $_POST['cid']
        );

        if ($_FILES['jimage']['name'] != "") {

            if ($fileName = $file->doUploadRandom(
                $_FILES['jimage'],
                array('.jpg', '.png', '.jpeg'),
                1000000,
                1,
                '../uploads'
            )) {

                $data['jimage'] = $fileName;
            }
        }

        $dao->update($data, "job", "jid=" . $_POST['editid']);

        echo "<script>window.location='jobs.php';</script>";
        exit();
    }
}

/* ---------------------------
   JOB LIST
----------------------------*/
$rows = $dao->getData("*", "job");
?>

<div id="page-wrapper" class="animate-fade-in">

    <!-- JOB FORM -->
    <div class="glass-card" style="margin-bottom: 30px;">

        <h2 style="margin-bottom: 25px;">
            <?php
            if($editid!=""){
                echo "<i class='fas fa-edit' style='color: var(--primary);'></i> Update Job";
            } else {
                echo "<i class='fas fa-plus-circle' style='color: var(--primary);'></i> Add Job";
            }
            ?>
        </h2>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="editid" value="<?php echo $editid; ?>">

            <div class="form-group-modern">
                <label class="form-label-modern">Job Name</label>
                <?= $form->textBox('jname', array('class'=>'form-input-modern', 'placeholder'=>'Enter job name')); ?>
                <div class="form-error">
                    <?= $validator->error('jname'); ?>
                </div>
            </div>

            <div class="form-group-modern">
                <label class="form-label-modern">Description</label>
                <?= $form->textBox('jdescription', array('class'=>'form-input-modern', 'placeholder'=>'Enter job description')); ?>
                <div class="form-error">
                    <?= $validator->error('jdescription'); ?>
                </div>
            </div>

            <div class="form-group-modern">
                <label class="form-label-modern">Category Name</label>
                <?php
                $options = $dao->createOptions('cname','cid','category');
                echo $form->dropDownList(
                    'cid',
                    array('class'=>'form-input-modern'),
                    $options
                );
                ?>
                <div class="form-error">
                    <?= $validator->error('cid'); ?>
                </div>
            </div>

            <div class="form-group-modern">
                <label class="form-label-modern">Job Image</label>
                <?= $form->fileField('jimage', array('class'=>'form-input-modern')); ?>
            </div>

            <?php if($editid!=""){ ?>
                <button type="submit"
                        name="btn_update"
                        class="btn-modern btn-primary-modern">
                    <i class="fas fa-save"></i> Update Job
                </button>
            <?php } else { ?>
                <button type="submit"
                        name="btn_insert"
                        class="btn-modern btn-success-modern">
                    <i class="fas fa-plus"></i> Add Job
                </button>
            <?php } ?>

        </form>

    </div>

    <!-- JOB TABLE -->
    <div class="glass-card">

        <h2 style="margin-bottom: 20px;"><i class="fas fa-list" style="color: var(--primary);"></i> Job List</h2>

        <div class="table-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>JID</th>
                        <th>Job Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($rows){
                        foreach($rows as $row){
                            $cat = $dao->getData(
                                "cname",
                                "category",
                                "cid=".$row['cid']
                            );
                            $cname = "";
                            if($cat){
                                foreach($cat as $c){
                                    $cname = $c['cname'];
                                }
                            }
                    ?>
                    <tr>
                        <td><?php echo $row['jid']; ?></td>
                        <td style="font-weight: 600; color: var(--secondary);"><?php echo htmlspecialchars($row['jname']); ?></td>
                        <td style="color: var(--text-muted);"><?php echo htmlspecialchars($row['jdescription']); ?></td>
                        <td>
                            <span class="badge-status badge-status-approved">
                                <span class="status-dot status-dot-approved"></span>
                                <?php echo htmlspecialchars($cname); ?>
                            </span>
                        </td>
                        <td>
                            <img src="../uploads/<?php echo $row['jimage']; ?>" style="width: 60px; height: 60px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--border-color);">
                        </td>
                        <td>
                            <a href="jobs.php?edit=<?php echo $row['jid']; ?>" class="btn-modern btn-edit" style="display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                        <td>
                            <a href="jobs.php?delete=<?php echo $row['jid']; ?>" class="btn-modern btn-delete" style="display: inline-flex; align-items: center; gap: 5px;" onclick="return confirm('Delete this job?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-folder-open fa-2x" style="margin-bottom: 10px; display: block;"></i>
                            No jobs found
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>