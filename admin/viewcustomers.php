<?php require('../config/autoload.php'); ?>

<?php
$dao=new DataAccess();
?>

<?php include('header.php'); ?>

<div id="page-wrapper" class="animate-fade-in">

    <div class="glass-card">

        <h2 style="margin-bottom: 25px;"><i class="fas fa-users" style="color: var(--primary);"></i> Customer Registration List</h2>

        <div class="table-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>CRID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Password</th>
                        <th>Phone</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $actions=array();
                    $config=array();
                    $join=array();
                    $fields=array(
                        'crid',
                        'cname',
                        'cgmail',
                        'cage',
                        'cgender',
                        'caddress',
                        'cpassword',
                        'cphone',
                        'cstatus'
                    );

                    $users=$dao->selectAsTable(
                        $fields,
                        'cregistration as s',
                        1,
                        $join,
                        $actions,
                        $config
                    );

                    // To make statuses look like modern badges, we use preg_replace targeting only the last column of each row
                    $users = preg_replace('/<td>1<\/td>(?=\s*<\/tr>)/', "<td><span class='badge-status badge-status-approved'><span class='status-dot status-dot-approved'></span>Active</span></td>", $users);
                    $users = preg_replace('/<td>0<\/td>(?=\s*<\/tr>)/', "<td><span class='badge-status badge-status-pending'><span class='status-dot status-dot-pending'></span>Pending</span></td>", $users);
                    $users = preg_replace('/<td>2<\/td>(?=\s*<\/tr>)/', "<td><span class='badge-status badge-status-cancelled'><span class='status-dot status-dot-cancelled'></span>Blocked</span></td>", $users);

                    echo $users;
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</div>