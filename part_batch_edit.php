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

// pull the header and template stuff:
pagehead();

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: batch_log.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	$get_part_batch_SQL = "SELECT *  FROM  `part_batch` WHERE `ID` = " . $record_id;
	// echo '<h1>SQL: ' . $get_part_batch_SQL . '</h1>';
	$result_get_part_batch = mysqli_query($con,$get_part_batch_SQL);
	// while loop
	while($row_get_part_batch = mysqli_fetch_array($result_get_part_batch)) {

			// now print each record:
			$part_batch_id 				= $row_get_part_batch['ID'];
			$part_batch_po_id 			= $row_get_part_batch['PO_ID'];
			$part_batch_part_id 		= $row_get_part_batch['part_ID'];
			$part_batch_batch_number 	= $row_get_part_batch['batch_number'];
			$part_batch_part_rev_id 	= $row_get_part_batch['part_rev'];
			$part_batch_supplier_id 	= $row_get_part_batch['supplier_ID'];
			$part_batch_record_status 	= $row_get_part_batch['record_status'];

	} // end while loop
}

?>
					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="part_batch_edit_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Edit Part Batch Details:</h2>
								</header>
								<div class="panel-body">
									<div class="form-group">
										<label class="col-md-3 control-label">PO #:<span class="required">*</label>
										<div class="col-md-5">
											<?php purchase_orders_drop_down($part_batch_po_id); ?>
										</div>
										<div class="col-md-1">
											<a href="purchase_order_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Part / Revision #:<span class="required">*</span></label>
										<div class="col-md-5">
											<?php part_rev_drop_down($part_batch_part_rev_id); ?>
										</div>
										<div class="col-md-1">
											<a href="part_revision_add.php<?php if ($record_id != 0) { ?>?PO_ID=<?php echo $record_id; } ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">Batch #:<span class="required">*</span></label>
										<div class="col-md-5">
											<input type="text" class="form-control" placeholder="" name="batch_number" value="<?php echo $part_batch_batch_number; ?>" required ></input>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Supplier:<span class="required">*</span></label>
										<div class="col-md-5">
											<?php supplier_drop_down($part_batch_supplier_id); ?>
										</div>

										<div class="col-md-1">
											<a href="supplier_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Record Status:</label>
										<div class="col-md-5">
											<?php record_status_drop_down($part_batch_record_status); ?>
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
