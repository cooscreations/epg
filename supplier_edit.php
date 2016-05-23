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

/* -- NO USER SESSIONS YET...
if (isset($_SESSION['user_id'])) {
	header("Location: user_home.php"); // send them to the user home...
}
*/

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else {	
	header("Location: suppliers.php?msg=NG&action=view&error=no_id");
	exit();		
}

if ($record_id != 0) {
	// now get the part info:
    $get_sups_SQL = "SELECT * FROM `suppliers` WHERE `ID` = " . $record_id;
    // echo $get_sups_SQL;

    $result_get_sups = mysqli_query($con,$get_sups_SQL);

    // while loop
    while($row_get_sup = mysqli_fetch_array($result_get_sups)) {
        $sup_ID = $row_get_sup['ID'];
        $sup_en = $row_get_sup['name_EN'];
        $sup_cn = $row_get_sup['name_CN'];
        $sup_web = $row_get_sup['website'];
        
    } // end get supplier info WHILE loop
}

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Edit Supplier<?php if ($record_id != 0) { ?> : <? echo $sup_ID;
                                               } ?></h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="suppliers.php">All Suppliers</a></li>
                <li><span>Edit Supplier</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="supplier_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit Supplier Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Name:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="name_en" value="<?php echo $sup_en; ?>"/>
                                <input type="hidden" name="sup_id" value="<?php echo $sup_ID; ?>"/>
                            </div>
                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">??:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="name_cn" value="<?php echo $sup_cn; ?>"/>
                            </div>
                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Website:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="sup_website" value="<?php echo $sup_web; ?>"/>
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
                        <input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="sup_id" />
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