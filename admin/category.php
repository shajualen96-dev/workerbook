<?php
require('../config/autoload.php');

/* ADMIN LOGIN CHECK */
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

include("header.php");

$file = new FileUpload();
$dao  = new DataAccess();

/* ---------------------------
   DELETE
----------------------------*/
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $dao->delete("category", "cid=" . $id);

    echo "<script>window.location='category.php';</script>";
    exit();
}

/* ---------------------------
   EDIT FETCH
----------------------------*/
$editid = "";

$editdata = array(
    "cname" => "",
    "cdescription" => "",
    "cimage" => ""
);

if (isset($_GET['edit'])) {

    $editid = $_GET['edit'];

    $result = $dao->getData("*", "category", "cid=" . $editid);

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
    "cname" => $editdata['cname'],
    "cdescription" => $editdata['cdescription'],
    "cimage" => ""
);

$form = new FormAssist($elements, $_POST);

$labels = array(
    "cname" => "Category Name",
    "cdescription" => "Description",
    "cimage" => "Image"
);

$rules = array(
    "cname" => array("required" => true),
    "cdescription" => array("required" => true)
);

$validator = new FormValidator($rules, $labels);

/* ---------------------------
   INSERT
----------------------------*/
if (isset($_POST['btn_insert'])) {

    if ($validator->validate($_POST)) {

        if ($fileName = $file->doUploadRandom(
            $_FILES['cimage'],
            array('.jpg', '.png', '.jpeg'),
            1000000,
            1,
            '../uploads'
        )) {

            $data = array(
                "cname" => $_POST['cname'],
                "cdescription" => $_POST['cdescription'],
                "cimage" => $fileName
            );

            $dao->insert($data, "category");

            echo "<script>window.location='category.php';</script>";
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
            "cname" => $_POST['cname'],
            "cdescription" => $_POST['cdescription']
        );

        if ($_FILES['cimage']['name'] != "") {

            if ($fileName = $file->doUploadRandom(
                $_FILES['cimage'],
                array('.jpg', '.png', '.jpeg'),
                1000000,
                1,
                '../uploads'
            )) {

                $data['cimage'] = $fileName;
            }
        }

        $dao->update($data, "category", "cid=" . $_POST['editid']);

        echo "<script>window.location='category.php';</script>";
        exit();
    }
}

/* ---------------------------
   CATEGORY LIST
----------------------------*/
$rows = $dao->getData("*", "category");
?>

<div id="page-wrapper" class="animate-fade-in">

    <!-- CATEGORY FORM -->
    <div class="glass-card" style="margin-bottom: 30px;">

        <h2 style="margin-bottom: 25px;">
            <?php
            if($editid!=""){
                echo "<i class='fas fa-edit' style='color: var(--primary);'></i> Update Category";
            }
            else{
                echo "<i class='fas fa-plus-circle' style='color: var(--primary);'></i> Add Category";
            }
            ?>
        </h2>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden"
                   name="editid"
                   value="<?php echo $editid; ?>">

            <div class="form-group-modern">
                <label class="form-label-modern">Category Name</label>
                <?= $form->textBox(
                    'cname',
                    array('class'=>'form-input-modern', 'placeholder'=>'Enter category name')
                ); ?>
                <div class="form-error">
                    <?= $validator->error('cname'); ?>
                </div>
            </div>

            <div class="form-group-modern">
                <label class="form-label-modern">Description</label>
                <?= $form->textBox(
                    'cdescription',
                    array('class'=>'form-input-modern', 'placeholder'=>'Enter description')
                ); ?>
                <div class="form-error">
                    <?= $validator->error('cdescription'); ?>
                </div>
            </div>

            <div class="form-group-modern">
                <label class="form-label-modern">Category Image</label>
                <?= $form->fileField(
                    'cimage',
                    array('class'=>'form-input-modern')
                ); ?>
            </div>

            <?php if($editid!=""){ ?>
                <button type="submit"
                        name="btn_update"
                        class="btn-modern btn-primary-modern">
                    <i class="fas fa-save"></i> Update Category
                </button>
            <?php } else { ?>
                <button type="submit"
                        name="btn_insert"
                        class="btn-modern btn-success-modern">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            <?php } ?>

        </form>

    </div>

    <!-- CATEGORY TABLE -->
    <div class="glass-card">

        <h2 style="margin-bottom: 20px;"><i class="fas fa-list" style="color: var(--primary);"></i> Category List</h2>

        <div class="table-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>CID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($rows){
                        foreach($rows as $row){
                    ?>
                    <tr>
                        <td><?php echo $row['cid']; ?></td>
                        <td style="font-weight: 600; color: var(--secondary);"><?php echo htmlspecialchars($row['cname']); ?></td>
                        <td style="color: var(--text-muted);"><?php echo htmlspecialchars($row['cdescription']); ?></td>
                        <td>
                            <img src="../uploads/<?php echo $row['cimage']; ?>" style="width: 60px; height: 60px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--border-color);">
                        </td>
                        <td>
                            <a href="category.php?edit=<?php echo $row['cid']; ?>" class="btn-modern btn-edit" style="display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                        <td>
                            <a href="category.php?delete=<?php echo $row['cid']; ?>" class="btn-modern btn-delete" style="display: inline-flex; align-items: center; gap: 5px;" onclick="return confirm('Delete this category?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-folder-open fa-2x" style="margin-bottom: 10px; display: block;"></i>
                            No categories found
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>