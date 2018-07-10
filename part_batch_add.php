<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
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

// pull the header and template stuff:
pagehead();

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else if (isset($_REQUEST['PO_ID'])) {
	$record_id = $_REQUEST['PO_ID'];
}

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

if ($record_id != 0) {
	$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $record_id;
	$result_get_PO = mysqli_query($con,$get_PO_SQL);
	// while loop
	while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

			// now print each record:
			$PO_id = $row_get_PO['ID'];
			$PO_number = $row_get_PO['PO_number'];
			$PO_created_date = $row_get_PO['created_date'];
			$PO_description = $row_get_PO['description'];

			// count variants for this purchase order
	   	 	$count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $record_id . " AND `record_status` = 2";
	   	 	$count_batches_query = mysqli_query($con, $count_batches_sql);
	   	 	$count_batches_row = mysqli_fetch_row($count_batches_query);
	   	 	$total_batches = $count_batches_row[0];
	   	 	
	   	 	$form_cancel_URL = "purchase_order_view.php?id=" . $PO_id;

	} // end while loop
}
else {
	$form_cancel_URL = "batch_log.php";
}

?>
					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form id="form" class="form-horizontal form-bordered"  action="part_batch_add_do.php" method="post">

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
									<label class="col-md-3 control-label">PO #:<span class="required">*</label>
									<div class="col-md-5">
									    <?php purchase_orders_drop_down($record_id); ?>
									</div>
									<div class="col-md-1">
										<a href="purchase_order_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label">Part / Revision #:<span class="required">*</span></label>
									<div class="col-md-5">
										<?php part_rev_drop_down($part_rev_ID); ?>
									</div>
									<div class="col-md-1">
										<a href="part_revision_add.php<?php if ($record_id != 0) { ?>?PO_ID=<?php echo $record_id; } ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>


								<div class="form-group">
									<label class="col-md-3 control-label">Batch #:<span class="required">*</span></label>
									<div class="col-md-5">
										<input type="text" class="form-control" placeholder="" name="batch_number" required />
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>


								</div>


								<footer class="panel-footer">
								
								<div class="row">
								
									<!-- ADD ANY OTHER HIDDEN VARS HERE -->
								  <div class="col-md-5 text-left">	
									<?php form_buttons($form_cancel_URL, $record_id); ?>
								  </div>
								  
								  
								   <!-- NEXT STEP SELECTION -->
									    
									    <?php 
									    if ($_REQUEST['next_step'] == 'view_PO') {
									    	$next_step_selected = 'PO';
									    }
									    else if ($_REQUEST['next_step'] == 'view_batch') {
									    	$next_step_selected = 'batch';
									    }
									    else {
									    	$next_step_selected = 'part';
									    }
									    ?>
									    
										<label class="col-md-1 control-label text-right">...and then...</label>
										
										<div class="col-md-6 text-left">
											<div class="radio-custom radio-success">
												<input type="radio" id="next_step" name="next_step" value="view_part"<?php if ($next_step_selected == 'part') { ?> checked="checked"<?php } ?>>
												<label for="radioExample9">View Part</label>
											</div>

											<div class="radio-custom radio-warning">
												<input type="radio" id="next_step" name="next_step" value="view_batch"<?php if ($next_step_selected == 'batch') { ?> checked="checked"<?php } ?>>
												<label for="radioExample10">View Batch</label>
											</div>

											<div class="radio-custom radio-info">
												<input type="radio" id="next_step" name="next_step" value="view_PO"<?php if ($next_step_selected == 'PO') { ?> checked="checked"<?php } ?>>
												<label for="radioExample11">View P.O.</label>
											</div>
										</div>
										
										<!-- END OF NEXT STEP SELECTION -->
								  </div><!-- end row div -->
								  
								</footer>
							</section>
										<!-- now close the form -->
										</form>


						</div>

						</div>




								<!-- now close the panel --><!-- end row! -->

					<!-- end: page -->

<?php
// now close the page out:
pagefoot($page_id);

?>
