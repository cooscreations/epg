<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */     session_start ();     /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
//   now check the user is OK to view this page  //
/* //////// require ('page_access.php'); */// ///*/
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////

header ( 'Content-Type: text/html; charset=utf-8' );
require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: BOM.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	// now get the record info:
    $get_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `ID` = '" . $record_id . "'";
    // echo $get_BOM_SQL;

    $result_get_BOM = mysqli_query($con,$get_BOM_SQL);

    // while loop
    while($row_get_BOM = mysqli_fetch_array($result_get_BOM)) {
        $BOM_ID 			= $row_get_BOM['ID']; 				// same as record_id
		$BOM_part_rev_ID 	= $row_get_BOM['part_rev_ID'];		//
		$BOM_date_entered 	= $row_get_BOM['date_entered'];
		$BOM_record_status 	= $row_get_BOM['record_status'];
		$BOM_created_by 	= $row_get_BOM['created_by'];
		$BOM_type 			= $row_get_BOM['BOM_type'];			//
		$BOM_parent_BOM_ID 	= $row_get_BOM['parent_BOM_ID'];
		$BOM_entry_order 	= $row_get_BOM['entry_order'];

    } // end get info WHILE loop
}

// pull the header and template stuff:
pagehead ();

?>

	<!-- start: page -->
 
	<div class="row">
		<div class="col-md-12">

			<!-- START THE FORM! -->
			 <form class="form-horizontal form-bordered" action="BOM_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Part Revision Assembly:</label>
                            <div class="col-md-5">
                                <?php part_rev_drop_down($BOM_part_rev_ID,10); ?>
                            </div>
                            
							<div class="col-md-1">
								<a href="part_add.php?part_type_ID=10" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
							</div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">BOM Type:</label>
                            <div class="col-md-5">
                                <select data-plugin-selectTwo class="form-control populate" name="BOM_type">
									<option value="" selected="selected">Select Type:</option>
									<option value="sub"<?php if ($BOM_type == 'sub') { ?> selected="selected"<? } ?>>Sub-Assembly</option>
									<option value="final"<?php if ($BOM_type == 'final') { ?> selected="selected"<? } ?>>Final Assembly</option>
									</select>
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Parent BOM:</label>
                            <div class="col-md-5">
                                <select data-plugin-selectTwo class="form-control populate" name="parent_BOM_ID">
									<option value="0" selected="selected">Ignore / No Parent:</option>
									<?php 
									
									$get_existing_BOM_list_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = '2' AND `ID` != '" . $record_id . "' ORDER BY `entry_order` ASC";
									$result_get_existing_BOM_list = mysqli_query($con,$get_existing_BOM_list_SQL);
									// while loop
									while($row_get_existing_BOM_list = mysqli_fetch_array($result_get_existing_BOM_list)) {
										$P_BOM_ID 				= $row_get_existing_BOM_list['ID'];
										$P_BOM_part_rev_ID 		= $row_get_existing_BOM_list['part_rev_ID']; // look up
										$P_BOM_date_entered 	= $row_get_existing_BOM_list['date_entered'];
										$P_BOM_record_status 	= $row_get_existing_BOM_list['record_status'];
										$P_BOM_created_by 		= $row_get_existing_BOM_list['created_by'];
										$P_BOM_type 			= $row_get_existing_BOM_list['BOM_type'];
										$P_BOM_parent_BOM_ID 	= $row_get_existing_BOM_list['parent_BOM_ID'];
										$P_BOM_entry_order 		= $row_get_existing_BOM_list['entry_order'];
										
										// Get the Part ID:
										
										$get_this_part_ID_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $P_BOM_part_rev_ID . "'";
										$result_get_this_part_ID = mysqli_query($con,$get_this_part_ID_SQL);
										// while loop
										while($row_get_this_part_ID = mysqli_fetch_array($result_get_this_part_ID)) {
											$this_part_ID 			= $row_get_this_part_ID['part_ID'];	
										}
										
										?>
										<option value="<?php echo $P_BOM_ID; ?>"<?php if ($BOM_parent_BOM_ID == $P_BOM_ID) { ?> selected="selected"<?php } ?>>BOM #<?php echo $P_BOM_ID; ?> - <?php part_name($this_part_ID, 0); ?></option>
										<?php 
										
									} // end while loop
									?>
									</select>
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                        
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Entry Order:</label>
                            <div class="col-md-5">
                                <select data-plugin-selectTwo class="form-control populate" name="entry_order">
									<option value="0" selected="selected">Select List Position:</option>
									<?php 
									$list_start = 1;
									$list_finish = 101;
									while ($list_start < $list_finish) {
										?>
										<option value="<?php echo $list_start; ?>"<?php if ($BOM_entry_order == $list_start) { ?> selected="selected"<?php } ?>><?php echo $list_start; ?></option>
										<?php 
										$list_start = $list_start + 1;
									} 
									?>
								</select>
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                        
                        <div class="form-group">
							<label class="col-md-3 control-label">Created By:</label>
							<div class="col-md-5">
								<?php echo creator_drop_down($BOM_created_by); ?>
							</div>
							
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-md-3 control-label">Date Created:</label>
							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
									<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_created" value="<?php echo $BOM_date_entered; ?>">
								</div>
							</div>
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
			
                        <div class="form-group">
							<label class="col-md-3 control-label">Record Status:</label>
							<div class="col-md-5">
								<?php echo record_status_drop_down($BOM_record_status); ?>
							</div>
							
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>



                    </div>
                    <footer class="panel-footer">
                        <?php form_buttons('BOM.php', $record_id); ?>
                        
                    </footer>
                </section>
                <!-- now close the form -->
            </form>



		</div>

	</div>




	<!-- now close the panel -->
	<!-- end row! -->

	<!-- end: page -->

<?php

// now close the page out:
pagefoot ( $page_id );

?>