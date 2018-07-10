<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */
session_start (); /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// now check the user is OK to view this page //
/* //////// require ('page_access.php'); / */
// ///*/
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

// pull the header and template stuff:
pagehead();

$add_SQL = '';

if (isset($_REQUEST['batch_ID'])) {
	$add_SQL .= " AND `batch_ID` = '".$_REQUEST['batch_ID']."'";
}
?>
    <!-- start: page -->
    
    
    <?php add_button(0, 'IQC_report_add'); ?>
    
    <div class="table-responsive">
        <table
            class="table table-bordered table-striped table-condensed mb-none">
            <tr>
            	<th class="text-center"><i class="fa fa-gear btn btn-default"></i></th>
                <th class="text-center">Report #</th>
                <th class="text-center">Batch #</th>
                <th class="text-center">P.O. #</th>
                <th class="text-center">Part #</th>
                <th class="text-center">Part Name</th>
                <th class="text-center">Part Rev.</th>
                <th class="text-center">Vendor</th>
                <th class="text-center">Result</th>
                <th class="text-center">Review Date</th>
            </tr>

            <?php
                          	$get_IQC_reports_SQL = "SELECT * FROM  `IQC_report` WHERE `record_status` = '2'" . $add_SQL . " ORDER BY `IQC_report`.`IQC_report_num` ASC";
                          	// echo $get_con_SQL;

							$IQC_reports_count = 0;

							$result_get_IQC_reports = mysqli_query ( $con, $get_IQC_reports_SQL );
							// while loop
							while ( $row_get_IQC_reports = mysqli_fetch_array ( $result_get_IQC_reports ) ) {
						
								$IQC_report_ID 				= $row_get_IQC_reports['ID'];
								$IQC_report_report_num 		= $row_get_IQC_reports['IQC_report_num'];
								$IQC_report_batch_ID		= $row_get_IQC_reports['batch_ID'];
								$IQC_report_remarks 		= $row_get_IQC_reports['remarks'];
								$IQC_report_test_result 	= $row_get_IQC_reports['test_result'];
								$IQC_report_NCR_num 		= $row_get_IQC_reports['NCR_num'];
								$IQC_report_reviewer_ID 	= $row_get_IQC_reports['reviewer_ID'];
								$IQC_report_review_date 	= $row_get_IQC_reports['review_date'];
								$IQC_report_inspector_ID 	= $row_get_IQC_reports['inspector_ID'];
								$IQC_report_inspection_date = $row_get_IQC_reports['inspection_date'];
								$IQC_report_record_status 	= $row_get_IQC_reports['record_status'];
							
								// GET BATCH AND PO INFO
							
								$get_batch_SQL = "SELECT * FROM  `part_batch` WHERE `ID` = " . $IQC_report_batch_ID;
								// echo "<h2>SQL is: " . $get_batch_SQL . "</h2>";
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

									// establish the size of the first in-coming batch:
									$get_first_batch_movement_SQL = "SELECT * FROM  `part_batch_movement` WHERE  `part_batch_ID` =" . $record_id . " AND `amount_in` > 0 AND `record_status` = '2' ORDER BY `date` ASC LIMIT 0,1";

									$result_get_first_batch_movement = mysqli_query($con,$get_first_batch_movement_SQL);
									// while loop
									while($row_get_first_batch_movement = mysqli_fetch_array($result_get_first_batch_movement)) {

											// now print each record:
											$first_batch_movement_id 		= $row_get_first_batch_movement['ID'];
											$first_amount_in 				= $row_get_first_batch_movement['amount_in'];
											$first_amount_out 				= $row_get_first_batch_movement['amount_out'];
											$first_part_batch_status_ID 	= $row_get_first_batch_movement['part_batch_status_ID'];
											$first_movement_remarks 		= $row_get_first_batch_movement['remarks'];
											$first_movement_user_ID 		= $row_get_first_batch_movement['user_ID'];
											$first_movement_date 			= $row_get_first_batch_movement['date'];
											$first_movement_record_status 	= $row_get_first_batch_movement['record_status'];
	
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
							
								// END GET BATCH AND PO INFO
								}

								?>

            <tr>
            
            <td class="text-center">
                    
					<!-- ********************************************************* -->
					<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
					<!-- ********************************************************* -->
			
					<?php 
			
					// VARS YOU NEED TO WATCH / CHANGE:
					$add_to_form_name 	= '';						// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
					$form_ID 			= $IQC_report_ID;				// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
					$edit_URL 			= 'IQC_report_edit'; 			// REQUIRED - specify edit page URL
					$add_URL 			= 'IQC_report_add'; 			// REQURED - specify add page URL
					$table_name 		= 'IQC_report';				// REQUIRED - which table are we updating?
					$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
					$add_VAR 			= ''; 						// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
			
					?>
	 
						<a class="modal-with-form btn btn-default" href="#modalForm_<?php 
				
							echo $add_to_form_name; 
							echo $form_ID; 
				
						?>"><i class="fa fa-gear"></i></a>

						<!-- Modal Form -->
						<div id="modalForm_<?php 
				
							echo $add_to_form_name; 
							echo $form_ID; 
					
						?>" class="modal-block modal-block-primary mfp-hide">
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
											<a href="<?php 
												echo $edit_URL; 
											?>.php?id=<?php 
												echo $form_ID; 
											?>" class="mb-xs mt-xs mr-xs btn btn-warning">
												<i class="fa fa-pencil" stlye="color: #999"></i>
											</a>
										  </td>
										  <td>Edit this record</td>
										</tr>
										<tr>
										  <td>DELETE</td>
										  <td>
											<a href="record_delete_do.php?table_name=<?php 
												echo $table_name; 
											?>&src_page=<?php 
												echo $src_page; 
											?>&id=<?php 
												echo $form_ID;
												echo '&' . $add_VAR; // NOTE THE LEADING '&' <<<  
											?>" class="mb-xs mt-xs mr-xs btn btn-danger">
												<i class="fa fa-trash modal-icon" stlye="color: #999"></i>
											</a>
										  </td>
										  <td>Delete this record</td>
										</tr>
										<tr>
										  <td>ADD</td>
										  <td>
											<a href="<?php 
												echo $add_URL; 
												echo '.php?' . $add_VAR;  // NOTE THE LEADING '?' <<<
											?>" class="mb-xs mt-xs mr-xs btn btn-success">
												<i class="fa fa-plus" stlye="color: #999"></i>
											</a>
										  </td>
										  <td>Add a similar item to this table</td>
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
										<div class="col-md-12 text-left">
											<button class="btn btn-danger modal-dismiss"><i class="fa fa-times" stlye="color: #999"></i> Cancel</button>
										</div>
									</div>
								</footer>
							</section>
						</div>
		
					<!-- ********************************************************* -->
					<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
					<!-- ********************************************************* -->
                </td>
            
            	<td class="text-center">
            		<a href="IQC_report_view.php?id=<?php echo $IQC_report_ID; ?>">
            			<?php echo $IQC_report_report_num; ?>
            		</a>
            	</td>
            	<td class="text-center">
            		<a href="batch_view.php?id=<?php echo $batch_id; ?>">
            			<?php echo $batch_number; ?>
            		</a>
            	</td>
            	<td class="text-center">
            		<a href="purchase_order_view.php?id=<?php echo $PO_id; ?>">
            			<?php echo $PO_number; ?>
            		</a>
            	</td>
            	<td class="text-center">
            		<a href="part_view.php?rev_id=<?php echo $part_rev; ?>">
            			<?php part_num_from_rev($part_rev); ?>
            		</a>
            	</td>
            	<td class="text-center">
            		<a href="part_view.php?rev_id=<?php echo $part_rev; ?>">
            			<?php part_name_from_rev($part_rev); ?>
            		</a>
            	</td>
            	<td class="text-center">
            		<a href="part_view.php?rev_id=<?php echo $part_rev; ?>">
            			<?php part_rev($part_rev); ?>
            		</a>
            	</td>
            	<td class="text-center">
            		<a href="supplier_view.php?id=<?php echo $PO_supplier_ID; ?>">
            			<?php get_supplier($PO_supplier_ID); ?>
            		</a>
            	</td>
            	<td class="text-center">
            		<span class="text-success">PASS?</span>
            	</td>
            	<td class="text-center">
            		<?php echo $IQC_report_review_date; ?>
            	</td>
            </tr>

            <?php

									$IQC_reports_count = $IQC_reports_count + 1;
								} // end while loop
								
								
								
			if ($IQC_reports_count == 0) { ?>
					<tr>
						<th colspan="10"><span class="text-danger">NO ROWS FOUND.</span> <a href="IQC_reports.php">RESET</a></th>
					</tr>
            <?php
			}					
								
								
			?>

            <tr>
                <th colspan="10">TOTAL: <?php echo $IQC_reports_count; ?></th>
            </tr>
        </table>
    </div>
    
    <?php add_button(0, 'IQC_report_add'); ?>
    
    <!-- end: page -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
