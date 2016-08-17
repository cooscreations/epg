<meta content="text/html; charset=utf-8" http-equiv="content-type" /><?php
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
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}

if ($record_id != 0) {
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
        <h2>Add A New Supplier<?php if ($record_id != 0) { ?> Supplier ID: <? echo $sup_ID;
                                    } ?></h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="suppliers.php">All Suppliers</a></li>
                <li><span>Add New Supplier</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="supplier_add_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Add Supplier Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Name:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="name_en" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">名字:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="name_cn" />
                            </div>


                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Website:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="sup_website" />
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
                        <input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="sup_ID" />
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
