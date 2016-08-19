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
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$session_user_id = $_SESSION['user_ID'];
// quick hack for Mark to load data faster:
if ($session_user_id == 1) { $session_user_id = 6; } // set Mark to ROBIN as default for Mark whilst entering data...

$page_id = 12;

// pull the header and template stuff:
pagehead($page_id);

$record_id = $_REQUEST['id'];

if (isset($_REQUEST['batch_id'])) { $record_id = $_REQUEST['batch_id']; }

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

		$get_batch_SQL = "SELECT * FROM  `part_batch` WHERE `ID` = " . $record_id;
		$result_get_batch = mysqli_query($con,$get_batch_SQL);
		// while loop
		while($row_get_batch = mysqli_fetch_array($result_get_batch)) {

				// now print each record:
				$batch_id = $row_get_batch['ID'];
				$PO_ID = $row_get_batch['PO_ID'];
				$part_ID = $row_get_batch['part_ID'];
				$batch_number = $row_get_batch['batch_number'];
				$part_rev = $row_get_batch['part_rev'];

				// GET PART DETAILS:
				$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $part_ID;
				$result_get_part = mysqli_query($con,$get_part_SQL);
				// while loop
				while($row_get_part = mysqli_fetch_array($result_get_part)) {

					// now print each result to a variable:
					$part_id = $row_get_part['ID'];
					$part_code = $row_get_part['part_code'];
					$part_name_EN = $row_get_part['name_EN'];
					$part_name_CN = $row_get_part['name_CN'];

				}


				// GET P.O. DETAILS:
				$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $PO_ID;
				$result_get_PO = mysqli_query($con,$get_PO_SQL);
				// while loop
				while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

					// now print each record:
					$PO_id = $row_get_PO['ID'];
					$PO_number = $row_get_PO['PO_number'];
					$PO_created_date = $row_get_PO['created_date'];
					$PO_description = $row_get_PO['description'];

				} // end while loop

		// count variants for this purchase order
        $count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $PO_id;
        $count_batches_query = mysqli_query($con, $count_batches_sql);
        $count_batches_row = mysqli_fetch_row($count_batches_query);
        $total_batches = $count_batches_row[0];


		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////

} // end while loop

?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Add Part Movement Record - <?php echo $batch_number; ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Add Part Movement Record</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					
					<?php

					// run notifications function:
					$msg = 0;
					if (isset($_REQUEST['msg'])) { $msg = $_REQUEST['msg']; }
					$action = 0;
					if (isset($_REQUEST['action'])) { $action = $_REQUEST['action']; }
					$change_record_id = 0;
					if (isset($_REQUEST['new_record_id'])) { $change_record_id = $_REQUEST['new_record_id']; }
					$page_record_id = 0;
					if (isset($record_id)) { $page_record_id = $record_id; }

					// now run the function:
					notify_me($page_id, $msg, $action, $change_record_id, $page_record_id);
					?>
					
					

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form id="form" class="form-horizontal form-bordered" action="part_movement_add_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Add Batch Record Details:</h2>
								</header>
								<div class="panel-body">

											<div class="form-group">
												<label class="col-md-3 control-label">Batch #:<span class="required">*</span></label>
												<div class="col-md-5">
													<select data-plugin-selectTwo class="form-control populate" name="batch_ID" required>
                                                        <option value=""></option>
													<?php
													// get batch list
													$get_batch_list_SQL = "SELECT `part_batch`.`ID`,
													`part_batch`.`PO_ID`,
													`part_batch`.`part_ID`,
													`part_batch`.`batch_number`,
													`part_batch`.`part_rev`,
													`purchase_orders`.`PO_number`,
													`purchase_orders`.`created_date`,
													`purchase_orders`.`description`
													FROM  `part_batch` ,  `purchase_orders`
													WHERE  `part_batch`.`PO_ID` =  `purchase_orders`.`ID` ";
													$result_get_batch_list = mysqli_query($con,$get_batch_list_SQL);
													// while loop
													while($row_get_batch_list = mysqli_fetch_array($result_get_batch_list)) {

														// now print each record:
														$batch_id = $row_get_batch_list['ID'];
														$PO_id = $row_get_batch_list['PO_ID'];
														$part_id = $row_get_batch_list['part_ID'];
														$batch_number = $row_get_batch_list['batch_number'];
														$part_rev = $row_get_batch_list['part_rev'];
														$PO_number = $row_get_batch_list['PO_number'];
														$created_date = $row_get_batch_list['created_date'];

														// get part revision info:
														$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $part_rev;
														$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);
														// while loop
														while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {

															// now print each record:
															$rev_id = $row_get_part_rev['ID'];
															$rev_part_id = $row_get_part_rev['part_ID'];
															$rev_number = $row_get_part_rev['revision_number'];
															$rev_remarks = $row_get_part_rev['remarks'];
															$rev_date = $row_get_part_rev['date_approved'];
															$rev_user = $row_get_part_rev['user_ID'];

														}

													?>
														<option value="<?php echo $batch_id; ?>"<?php if ($batch_id == $record_id) { ?> selected=""<?php } ?>><?php echo $batch_number; ?> (<?php echo $rev_number; ?>) [PO: <?php echo $PO_number; ?>]</option>

														<?php
														}
														?>
													</select>
												</div>
												<div class="col-md-1">
													<a href="part_batch_add.php?PO_ID=<?php echo $PO_id; ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
												</div>
											</div>

											<!-- HIDE STATUS IF BEING CHECKED OUT! -->
											<script type="text/javascript">
$(document).ready(function(){


			// SET DEFAULT SHOW DIV STATE:
            $(".box").not(".in").hide();
            $(".in").show();

    $('input[type="radio"]').click(function(){
        if($(this).attr("value")=="in"){
            $(".box").not(".in").hide();
            $(".in").show();
        }
        if($(this).attr("value")=="out"){
            $(".box").not(".out").hide();
            $(".out").show();
        }
    });
});
</script>

											<div class="form-group">
												<label class="col-md-3 control-label">In or Out?:<span class="required">*</span></label>
												<div class="col-md-2">
													<div class="radio-custom radio-success">
														<input type="radio" id="value_direction" name="value_direction"  value="in" required>
														<label for="radioExample3">Parts In</label>
													</div>

													<div class="radio-custom radio-warning">
														<input type="radio" id="value_direction" name="value_direction" value="out">
														<label for="radioExample4">Parts Out</label>
													</div>
												</div>



												<label class="col-md-1 control-label">IQC Status:<span class="required">*</span></label>
												<div class="col-md-2">

												  <div class="in box">


													<select data-plugin-selectTwo class="form-control populate" name="status_ID" required>
													<option value=""></option>
													<?php
													// get batch list
													$get_status_list_SQL = "SELECT * FROM `part_batch_status`";
													$result_get_status_list = mysqli_query($con,$get_status_list_SQL);
													// while loop
													while($row_get_status_list = mysqli_fetch_array($result_get_status_list)) {

														// now print each record:
														$status_id = $row_get_status_list['ID'];
														$status_name_EN = $row_get_status_list['name_EN'];
														$status_name_CN = $row_get_status_list['name_CN'];
													?>
														<option value="<?php echo $status_id; ?>"><?php echo $status_name_EN . " / " . $status_name_CN; ?></option>

														<?php
														}
														?>
													</select>


												  </div>
												  <div class="out box text-warning">
													Not applicable for parts out
												  </div>

												</div>

												<div class="col-md-1">
													<a href="batch_status.php" class="mb-xs mt-xs mr-xs btn btn-info pull-right"><i class="fa fa-question"></i></a>
												</div>

												</div>



											<div class="form-group">
												<label class="col-md-3 control-label">Amount:<span class="required">*</span></label>
												<div class="col-md-5">
													<input type="text" class="form-control" id="inputDefault" placeholder="Enter whole numbers ONLY" name="amount" required>
												</div>


												<div class="col-md-1">
													&nbsp;
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">Remarks:<span class="required">*</span></label>
												<div class="col-md-5">
													<textarea class="form-control" rows="3" id="textareaDefault" name="remarks" required></textarea>
												</div>


												<div class="col-md-1">
													&nbsp;
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">User:<span class="required">*</span></label>
												<div class="col-md-5">
													<?php creator_drop_down($session_user_id); ?>
												</div>

												<div class="col-md-1">
													<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
												</div>

											</div>


											<div class="form-group">
												<label class="col-md-3 control-label">Date:<span class="required">*</span></label>
												<div class="col-md-5">
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</span>
														<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_added" required>
													</div>
												</div>



												<div class="col-md-1">
													&nbsp;
												</div>
											</div>

												</div>
											</div>

								<footer class="panel-footer">
								
									<div class="row">
									    <div class="col-md-5">
										  <input type="hidden" name="PO_ID" value="<?php echo $PO_id; ?>" />
										  <a class="btn btn-danger" href="batch_view.php?id=<?php echo $record_id; ?>"><i class="fa fa-arrow-left"></i> CANCEL / BACK</a>
										  <button type="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> RESET</button>
										  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SAVE CHANGES</button>
										  
										  
									    </div>
									    
									    
									    <!-- NEXT STEP SELECTION -->
									    
									    <?php 
									    if ($_REQUEST['next_step'] == 'add') {
									    	$next_step_selected = 'add';
									    }
									    else {
									    	$next_step_selected = 'view';
									    }
									    ?>
									    
										<label class="col-md-1 control-label text-right">...and then...</label>
										
										<div class="col-md-6 text-left">
											<div class="radio-custom radio-success">
												<input type="radio" id="next_step" name="next_step" value="view"<?php if ($next_step_selected == 'view') { ?> checked="checked"<?php } ?>>
												<label for="radioExample9">View Results</label>
											</div>

											<div class="radio-custom radio-warning">
												<input type="radio" id="next_step" name="next_step" value="add"<?php if ($next_step_selected == 'add') { ?> checked="checked"<?php } ?>>
												<label for="radioExample10">Add Another Entry</label>
											</div>
										</div>
										
										<!-- END OF NEXT STEP SELECTION -->
										
									  </div><!-- close row -->
										
									</footer>



								</div>
							</section>
										<!-- now close the form -->
										</form>
						</div>






								<!-- now close the panel --><!-- end row! -->

					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
