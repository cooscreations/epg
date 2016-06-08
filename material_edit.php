<?php 
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////
/*//////*/      session_start();        /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//  now check the user is OK to view this page  //
/*//////// require ('page_access.php'); /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////

header('Content-Type: text/html; charset=utf-8');
require ('page_functions.php'); 
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 99;

$record_id = 0;

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else {	
	header("Location: materials.php?msg=NG&action=view&error=no_id");
	exit();		
}

if ($record_id != 0) {
	// now get the material info:
    $get_material_SQL = "SELECT * FROM `material` WHERE `ID` = " . $record_id;
    // echo $get_material_SQL;

    $result_get_material = mysqli_query($con,$get_material_SQL);

    // while loop
    while($row_get_material = mysqli_fetch_array($result_get_material)) {
        $id = $row_get_material['ID'];
        $name_EN = $row_get_material['name_EN'];
        $name_CN = $row_get_material['name_CN'];
        $description = $row_get_material['description'];
        
    } // end get supplier info WHILE loop
}

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Edit Material<?php if ($record_id != 0) { ?> : <? echo $id;} ?>
        </h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="materials.php">All Materials</a></li>
                <li><span>Edit Material</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="material_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit Material Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Name:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="name_en" value="<?php echo $name_EN; ?>"/>
                                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                            </div>
                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">名字:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="name_cn" value="<?php echo $name_CN; ?>"/>
                            </div>
                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Description:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="description" value="<?php echo $description; ?>"/>
                            </div>
                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                    </div>

                    <footer class="panel-footer">
                        <?php 
										if (isset($_REQUEST['id'])) {
											?>
                        <input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="id" />
                        <?php
										}
										?>
                        <button type="submit" class="btn btn-success">Submit </button>
                        <button type="reset" class="btn btn-default">Reset</button>
                    </footer>
                </section>
                <!-- now close the form -->
            </form>
        </div>

    </div>
    <!-- now close the panel -->
    <!-- end row! -->

    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>