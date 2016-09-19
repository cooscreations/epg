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
include ('qrcode-generator/index_2.php');

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 11;

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: batch_log.php?msg=NG&action=view&error=no_id");
	exit();
}

		$get_batch_SQL = "SELECT * FROM  `part_batch` WHERE `ID` = " . $record_id;
		$result_get_batch = mysqli_query($con,$get_batch_SQL);
		// while loop
		while($row_get_batch = mysqli_fetch_array($result_get_batch)) {

				// now print each record:
				$batch_id 				= $row_get_batch['ID'];
				$PO_ID 					= $row_get_batch['PO_ID'];
				$part_ID 				= $row_get_batch['part_ID'];
				$batch_number 			= $row_get_batch['batch_number'];
				$part_rev 				= $row_get_batch['part_rev'];
				$batch_supplier_ID 		= $row_get_batch['supplier_ID'];
				$batch_record_status 	= $row_get_batch['record_status'];

				// GET PART DETAILS:
				$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $part_ID;
				$result_get_part = mysqli_query($con,$get_part_SQL);
				// while loop
				while($row_get_part = mysqli_fetch_array($result_get_part)) {

					// now print each result to a variable:
					$part_id 		= $row_get_part['ID'];
					$part_code 		= $row_get_part['part_code'];
					$part_name_EN 	= $row_get_part['name_EN'];
					$part_name_CN 	= $row_get_part['name_CN'];

				}



								// get part revision info:
								$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $part_rev;
								$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);
								// while loop
								while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {

									// now print each record:
									$rev_id 		= $row_get_part_rev['ID'];
									$rev_part_id 	= $row_get_part_rev['part_ID'];
									$rev_number 	= $row_get_part_rev['revision_number'];
									$rev_remarks 	= $row_get_part_rev['remarks'];
									$rev_date 		= $row_get_part_rev['date_approved'];
									$rev_user 		= $row_get_part_rev['user_ID'];

								}


				// GET P.O. DETAILS:
				$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $PO_ID;
				$result_get_PO = mysqli_query($con,$get_PO_SQL);
				// while loop
				while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

					// now print each record:
					$PO_id 					= $row_get_PO['ID'];
					$PO_number 				= $row_get_PO['PO_number'];
					$PO_created_date 		= $row_get_PO['created_date'];
					$PO_description 		= $row_get_PO['description'];
					$PO_record_status 		= $row_get_PO['record_status'];
					$PO_supplier_ID 		= $row_get_PO['supplier_ID'];  // LOOK THIS UP!
					$PO_created_by 			= $row_get_PO['created_by']; // use get_creator($PO_created_by);
					$PO_date_needed 		= $row_get_PO['date_needed'];
					$PO_date_delivered 		= $row_get_PO['date_delivered'];
					$PO_approval_status 	= $row_get_PO['approval_status']; // look this up?
					$PO_payment_status 		= $row_get_PO['payment_status']; // look this up?
					$PO_completion_status 	= $row_get_PO['completion_status'];

				} // end while loop

		// count variants for this purchase order
        $count_batches_sql 		= "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $PO_id;
        $count_batches_query 	= mysqli_query($con, $count_batches_sql);
        $count_batches_row 		= mysqli_fetch_row($count_batches_query);
        $total_batches 			= $count_batches_row[0];


		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		// IF THE BATCH SUPPLIER IS NOT SET, BUT P.O. VENDOR IS SET, WE WILL NOW UPDATE THE BATCH RECORD AUTOMATICALLY:
		// THIS IS POTENTIALLY DOUBLING-UP ON DATA!?!?!?
		
		if ($PO_supplier_ID != $batch_supplier_ID) {
			$quick_update_batch_SQL = "UPDATE  `part_batch` SET  `supplier_ID` =  '" . $PO_supplier_ID . "' WHERE  `part_batch`.`ID` ='" . $batch_id . "';";
			if (mysqli_query($con, $quick_update_batch_SQL)) {
				$batch_supplier_ID = $PO_supplier_ID;
			}
			else {
				echo "<h4>Failed to update existing part_batch record with SQL: <br />" . $quick_update_batch_SQL . "</h4>";
			}
		}
		
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////

} // end while loop

// pull the header and template stuff:
pagehead($page_id);

?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Batch Record - <?php echo $batch_number; ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><a href="purchase_orders.php">All P.O.s</a></li>
								<li><a href="batch_log.php">Batch Log</a></li>
								<li><span>Batch Record</span></li>
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
						<div class="col-md-4">

						<?php
						// now run the admin bar function:
						// Fix for bug#39 and 37- main table os part_batch
						admin_bar('part_batch');
						?>
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Main Details:</h2>
								</header>
								<div class="panel-body">

									<div class="table-responsive">
					 					<table class="table table-bordered table-striped table-hover table-condensed mb-none">
					 					  <tr>
					 					    <th>Batch Number:</th>
					 					    <td><?php echo $batch_number; ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Purchase Order #:</th>
					 					    <td><a href="purchase_order_view.php?id=<?php echo $PO_id; ?>"><?php echo $PO_number; ?></a></td>
					 					  </tr>
					 					  <tr>
					 					    <th>P.O. Created Date:</th>
					 					    <td>
					 					     <?php echo date("Y-m-d", strtotime($PO_created_date)); ?>
					 					    </td>
					 					  </tr>
					 					  <tr>
					 					    <th>P.O. Remarks:</th>
					 					    <td><?php echo $PO_description; ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Total Batches in this P.O.:</th>
					 					    <td>
					 					    	<a href="purchase_order_view.php?id=<?php echo $PO_id; ?>" title="Click to view all batches associated to this purchase order">
					 					    		<?php echo $total_batches; ?>
					 					    	</a>
					 					    	&nbsp;
					 					    	<a href="purchase_order_view.php?id=<?php echo $PO_id; ?>" class="btn btn-default btn-xs" title="Click to view all batches associated to this purchase order">
					 					    		<i class="fa fa-search"></i>
					 					    	</a>
					 					    </td>
					 					  </tr>
					 					  <tr>
					 					    <th>Supplier:</th>
					 					    <td><?php get_supplier($batch_supplier_ID); ?></td>
					 					  </tr>
					 					</table>
					 				</div>


								</div>
							</section>
						</div>



						<div class="col-md-4">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Part Details:</h2>
								</header>
								<div class="panel-body">

									<div class="table-responsive">
					 					<table class="table table-bordered table-striped table-hover table-condensed mb-none">
					 					  <tr>
					 					    <th>Part Code:</th>
					 					    <td>
					 					      <a href="part_view.php?id=<?php echo $part_id; ?>" class="btn btn-info btn-xs" title="View <?php echo $part_name_EN; if (($part_name_CN!='中文名')&&($part_name_CN!='')) { echo ' / '. $part_name_CN; } ?> Part Profile">
					 					    	<?php echo $part_code; ?>
					 					      </a>
					 					   </td>
					 					  </tr>
					 					  <tr>
					 					    <th>Part Name:</th>
					 					    <td><a href="part_view.php?id=<?php echo $part_id; ?>"><?php echo $part_name_EN; if (($part_name_CN!='中文名')&&($part_name_CN!='')) { echo ' / '. $part_name_CN; } ?></a></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Part Revision:</th>
					 					    <td>
					 					      <span class="btn btn-xs btn-warning" title="Rev. #: <?php echo $rev_id; ?>">
					 					    	<?php echo $rev_number; ?>
					 					      </span>
					 					    </td>
					 					  </tr>
					 					  <tr>
					 					    <th>Total Batches for this part:</th>
					 					    <td><?php

												// count variants for this purchase part
												$count_j_batches_sql 	= "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_ID` = " . $part_id;
												$count_j_batches_query 	= mysqli_query($con, $count_j_batches_sql);
												$count_j_batches_row 	= mysqli_fetch_row($count_j_batches_query);
												$total_j_batches 		= $count_j_batches_row[0];

					 					     	?>
					 					    	<a href="batch_log.php?part_id=<?php echo $part_id; ?>" title="Click to view all batches associated to this part">
					 					    		<?php echo $total_j_batches; ?>
					 					    	</a>
					 					    	&nbsp;
					 					    	<a href="batch_log.php?part_id=<?php echo $part_id; ?>" class="btn btn-default btn-xs" title="Click to view all batches associated to this part">
					 					    		<i class="fa fa-search"></i>
					 					    	</a>
					 					     </td>
					 					  </tr>
					 					</table>
					 				</div>

								</div>
							</section>
						</div>

						<div class="col-md-4">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">QR Code to this page:</h2>
								</header>
								<div class="panel-body">


					 				<!-- ********************************************************* -->

					 				<div class="thumb-info mb-md contentToPrint" style="text-align:center;">
										<?php
											// now show their QR code!
											show_code('batch_QR', $_REQUEST['id']);
										?>
										<br />
										<a href="#" id="printOut">
										<span class="fa-stack fa-lg">
										  <i class="fa fa-square fa-stack-2x"></i>
										  <i class="fa fa-print fa-stack-1x fa-inverse"></i>
										</span>
										</a>
									</div>

									<div class="nameToPrint" style="display: none;">
										<?php echo $part_name_EN; if (($part_name_CN!='中文名')&&($part_name_CN!='')) { echo ' / ' . $part_name_CN; } ?>
									</div>

									<div class="IDnumToPrint" style="display: none;">
										<?php echo $part_code; ?>
									</div>

									<div class="batchToPrint" style="display: none;">
										<?php echo $batch_number; ?>
									</div>

									<div class="statusToPrint" style="display: none;">
										ACCEPTED / 接受
									</div>

									<script type="text/javascript">
										$(function(){
											$('#printOut').click(function(e){
												e.preventDefault();
												var w 			= window.open();
												var printOne 	= $('.contentToPrint').html();
												var printTwo 	= $('.nameToPrint').html();
												var printThree 	= $('.IDnumToPrint').html();
												var printFour 	= $('.batchToPrint').html();
												var printFive 	= $('.statusToPrint').html();
												w.document.write('<html><head><title>存卡 - Batch Code</title><style type="text/css"> html { font-family: sans-serif; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-size: 25px; } body { margin: 0; } table { border: none; } td { border: 1px solid black; padding: 5px; } </style></head><body><hr /><table><tr><th rowspan="4">' + printOne + '</th><th>NAME</th><th>' + printTwo + '</th></tr><tr><td>ID #</td><td>' + printThree + '</td></tr><tr><td>BATCH #</td><td>' + printFour + '</td></tr><tr><td>STATUS</td><td>' + printFive + '</td></tr></table><hr />' ) + '</body></html>';

												w.window.print();
												w.document.close();
												return false;
											});
										});
									</script>

					 				<!-- ********************************************************* -->

								</div>
							</section>
						</div>


					</div>



					<div class="row">

					<div class="col-md-12">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Batch History:</h2>
								</header>
								<div class="panel-body">


					<?php add_button($record_id, 'part_movement_add', 'batch_id'); ?>




					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					  <tr>
					    <th class="text-center"><span class="btn btn-default"><i class="fa fa-gear"></i></span></th>
					    <th class="text-center">Date</th>
					    <th class="text-center">QTY In</th>
					    <th class="text-center">QTY Out</th>
					    <th class="text-center">Batch Balance</th>
					    <th class="text-center">Status</th>
					    <th class="text-center">Staff</th>
					    <th class="text-center">Remarks</th>
					  </tr>

					  <!-- START DATASET -->
					  <?php

					  // get movements for this batch

					  $total_movements = 0; // default
					  $total_in = 0; // default
					  $total_out = 0; // default

					  	$get_batch_movement_SQL = "SELECT * FROM  `part_batch_movement` WHERE  `part_batch_ID` =" . $record_id . " AND `record_status` = '2' ORDER BY `date` ASC";

						$result_get_batch_movement = mysqli_query($con,$get_batch_movement_SQL);
						// while loop
						while($row_get_batch_movement = mysqli_fetch_array($result_get_batch_movement)) {

								// now print each record:
								$batch_movement_id 		= $row_get_batch_movement['ID'];
								$amount_in 				= $row_get_batch_movement['amount_in'];
								$amount_out 			= $row_get_batch_movement['amount_out'];
								$part_batch_status_ID 	= $row_get_batch_movement['part_batch_status_ID'];
								$movement_remarks 		= $row_get_batch_movement['remarks'];
								$movement_user_ID 		= $row_get_batch_movement['user_ID'];
								$movement_date 			= $row_get_batch_movement['date'];
								$movement_record_status = $row_get_batch_movement['record_status'];

								// now let's do the running total math:

								$total_in 	= $total_in + $amount_in;
								$total_out 	= $total_out + $amount_out;

								$total_now 	= $total_in - $total_out;



					  // get status

					  	$get_mvmnt_status_SQL = "SELECT * FROM  `part_batch_status` WHERE  `ID` =" . $part_batch_status_ID;

						$result_get_mvmnt_status = mysqli_query($con,$get_mvmnt_status_SQL);
						// while loop
						while($row_get_mvmnt_status = mysqli_fetch_array($result_get_mvmnt_status)) {

								// now print each record:
								$mvmnt_status_name_EN 	= $row_get_mvmnt_status['name_EN'];
								$mvmnt_status_name_CN 	= $row_get_mvmnt_status['name_CN'];
								$mvmnt_status_desc 		= $row_get_mvmnt_status['desc'];
								$mvmnt_status_icon 		= $row_get_mvmnt_status['icon'];
								$mvmnt_status_color 	= $row_get_mvmnt_status['color'];
						}

					// NOW LET'S DO THIS!

					  ?>
					  <tr<?php if ($batch_movement_id == $_REQUEST['new_record_id']) { ?> class="success"<?php } ?>>
					    <td class="text-center">
					    
					    
					    <!-- ********************************************************* -->
						<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
						<!-- ********************************************************* -->
						 
						    <a class="modal-with-form btn btn-default" href="#modalForm_<?php echo $batch_movement_id; ?>"><i class="fa fa-gear"></i></a>

							<!-- Modal Form -->
							<div id="modalForm_<?php echo $batch_movement_id; ?>" class="modal-block modal-block-primary mfp-hide">
								<section class="panel">
									<header class="panel-heading">
										<h2 class="panel-title">Admin Options</h2>
									</header>
									<div class="panel-body">
									
										<div class="table-responsive">
										 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
										 <thead>
											<tr>
												<th class="text-left" colspan="2">Action</th>
												<th>Decsription</th>
											</tr>
										  </thead>
										  <tbody>
											<tr>
											  <td>EDIT</td>
											  <td>
											  <a href="batch_movement_edit.php?id=<?php echo $batch_movement_id; ?>" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a></td>
											  <td>Edit this record</td>
											</tr>
											<tr>
											  <td>DELETE</td>
											  <td><a href="record_delete_do.php?table_name=part_batch_movement&src_page=batch_view.php&id=<?php echo $batch_movement_id; ?>&batch_id=<?php echo $record_id; ?>" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a></td>
											  <td>Delete this record</td>
											</tr>
											<tr>
											  <td>ADD BATCH</td>
											  <td><a href="part_movement_add.php?batch_id=<?php echo $record_id; ?>" class="mb-xs mt-xs mr-xs btn btn-success"><i class="fa fa-plus"></i></a></td>
											  <td>Add a batch movement to this batch</td>
											</tr>
										  </tbody>
										  <tfoot>
										  	<tr>
										  	  <td>&nbsp;</td>
										  	  <td>&nbsp;</td>
										  	  <td>&nbsp;</td>
										  	</tr>
										  </tfoot>
										  </table>
										</div><!-- end of responsive table -->	
									
									</div><!-- end panel body -->
									<footer class="panel-footer">
										<div class="row">
											<div class="col-md-12 text-right">
												<button class="btn btn-danger modal-dismiss"><i class="fa fa-times"></i> Cancel</button>
											</div>
										</div>
									</footer>
								</section>
							</div>
							
						<!-- ********************************************************* -->
						<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
						<!-- ********************************************************* -->
					    
					    
					    
					    </td>
					    <td class="text-center"><?php echo date("Y-m-d", strtotime($movement_date)); ?></td>
					    <td class="text-center"><?php if ($amount_in > 0) { echo number_format($amount_in); } ?></td>
					    <td class="text-center"><?php if ($amount_out > 0) { echo number_format($amount_out); } ?></td>
					    <td class="text-center"><?php echo number_format($total_now); ?></td>
					    <td class="text-center">
					    <?php if ($amount_in > 0) {
					    ?>
					    	<span class="button btn-xs btn-<?php echo $mvmnt_status_color; ?>"><i class="fa <?php echo $mvmnt_status_icon; ?>"></i> <?php echo $mvmnt_status_name_EN; ?> / <?php echo $mvmnt_status_name_CN; ?></span>
					    <?php }
					    else { ?>&nbsp;<?php }
					    ?>
					    </td>
					    <td class="text-center"><?php get_creator($movement_user_ID); ?></td>
					    <td class="text-center"><?php echo $movement_remarks; ?></td>
					  </tr>
					  <?php



					  $total_movements = $total_movements + 1;
					  } // END GET BATCH, STATUS AND USER INFO 'WHILE' LOOP

					  ?>
					  <!-- END DATASET -->

					  <tr>
					    <th>&nbsp;</th>
					    <th>TOTAL ENTRIES: <?php echo $total_movements ;?></th>
					    <th class="text-center"><?php echo number_format($total_in); ?></th>
					    <th class="text-center"><?php echo number_format($total_out); ?></th>
					    <th class="text-center"><?php echo number_format($total_now); ?></th>
					    <th class="text-center">&nbsp;</th>
					    <th class="text-center">&nbsp;</th>
					    <th class="text-center">&nbsp;</th>
					  </tr>


					 </table>
					</div>
					 
					 			 <?php add_button($record_id, 'part_movement_add', 'batch_id'); ?>

								<!-- now close the panel -->
								</div>
							</section>
						</div>
					</div> <!-- end row! -->

					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
