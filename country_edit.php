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

$record_id = 0;

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else {	
	header("Location: countries.php?msg=NG&action=view&error=no_id");
	exit();		
}

if ($record_id != 0) {
	// now get the country info:
    $get_country_SQL = "SELECT * FROM `countries` WHERE `ID` = " . $record_id;
    // echo $get_country_SQL;

    $result_get_con = mysqli_query($con,$get_country_SQL);

    // while loop
    while($row_get_con = mysqli_fetch_array($result_get_con)) {
        $id = $row_get_con['ID'];
        $name_EN = $row_get_con['name_EN'];
        $name_CN = $row_get_con['name_CN'];
        $code = $row_get_con['code'];
        
    } // end get info WHILE loop
}

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Edit Country<?php if ($record_id != 0) { ?> : <? echo $id;
                              } ?>
        </h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="materials.php">All Countries</a></li>
                <li><span>Edit Country</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="country_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit country Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Name:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="inputDefault" name="name_en" value="<?php echo $name_EN; ?>"/>
                                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">名字:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="inputDefault" name="name_cn" value="<?php echo $name_CN; ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Code:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="code" value="<?php echo $code; ?>"/>
                            </div>
							<div class="col-md-1">
								<a href="https://www.iso.org/obp/ui/#search" target="_blank" class="mb-xs mt-xs mr-xs btn btn-info pull-right"><i class="fa fa-question-circle"></i></a>
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