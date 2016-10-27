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

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id); ?>

<?php

$add_URL_vars_sort = '';
$add_URL_vars_dir = '';
$add_URL_vars_year = "&year=" . date("Y");
$display_year = date("Y");

if (isset($_REQUEST['sort'])) {

	$add_URL_vars_sort = '&sort=' . $_REQUEST['sort'];
}

if (isset($_REQUEST['dir'])) {

	$add_URL_vars_dir = '&dir=' . $_REQUEST['dir'];
}

if (isset($_REQUEST['year'])) {
	if ($_REQUEST['year']!='all') {
		$add_URL_vars_year = '&year=' . $_REQUEST['year'];
		$display_year = 'all';
	}
	else {
		$add_URL_vars_year = '';
		$display_year = $_REQUEST['year'];
	}
}

// now run it like this:
// echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year;
?>

<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Purchase Order Items (<?php echo $display_year; ?>)</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Purchase Order Items</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					<?php 
					
					// call the year jumper function:
					year_jumper('purchase_order_items', $display_year);
					
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


				<div class="col-md-12">

    <!-- START PANEL - LINE ITEMS -->
	<section class="panel">
		<!-- NO HEADER FOR THIS PANEL -->
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				
				<?php add_button($record_id, 'purchase_order_item_add', '', 'Click here to add an item to a Purchase Order'); ?>
				
				<div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                      <thead>
                        <tr>
                            <th><i class="fa fa-cog"></i></th>
                            <th>DATE</th>
                            <th><abbr title="Purchase Order">P.O.</abbr> NO.</th>
                            <th>DESCRIPTION</th>
                            <th>QTY</th>
                            <th>UNIT PRICE</th>
                            <th>TOTAL</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        
                        $po_line_number = 1;
                        $line_total = 0;	
						$subtotal = 0;
						$total_qty = 0;
                        
                        
					  if (isset($_REQUEST['sort'])) {
					  	$order_by = " ORDER BY `" . $_REQUEST['sort'] . "` " . $_REQUEST['dir'] . "";
					  }
					  else {
					  	$order_by = " ORDER BY  `purchase_order_items`.`purchase_order_ID` DESC";
					  }

					  $add_SQL = "";

					  if (isset($_REQUEST['year'])) {

					  	if ($_REQUEST['year'] == 'all') {
					  		$add_SQL = ""; // SHOW ALL YEARS!
					  	}
					  	else {
					  		$add_SQL = " AND `created_date` >  '" . $_REQUEST['year'] . "-01-01 00:00:00' AND `created_date` <  '" . ($_REQUEST['year'] + 1) . "-01-01 00:00:00'";
					  	}
					  }
					  else {
					  		// by default, we will just show this year!
					  		$add_SQL = " AND `created_date` >  '" . date("Y") . "-01-01 00:00:00' AND `created_date` <  '" . (date("Y") + 1) . "-01-01 00:00:00'";
					  }
                        
                        
                        
                        
                        
                        
                        
                        $get_purchase_order_items = "SELECT * FROM `purchase_order_items` WHERE `record_status` = '2'" . $add_SQL . $order_by;
                        // echo '<h1>' . $get_purchase_order_items . '</h1>'; 
                        $result_get_po_items = mysqli_query($con,$get_purchase_order_items);
                        // while loop
						while($row_get_po_items = mysqli_fetch_array($result_get_po_items)) {
							$po_item_ID						= $row_get_po_items['ID'];
							$po_item_purchase_order_ID		= $row_get_po_items['purchase_order_ID'];		// look this up!
							$po_item_part_revision_ID		= $row_get_po_items['part_revision_ID'];		// look this up!
							$po_item_part_qty				= $row_get_po_items['part_qty'];
							$po_item_record_status			= $row_get_po_items['record_status']; 			// should be 2
							$po_item_item_notes				= $row_get_po_items['item_notes'];
							$po_item_unit_price_USD			= $row_get_po_items['unit_price_USD'];
							$po_item_unit_price_currency	= $row_get_po_items['unit_price_currency']; 	
							$po_item_original_currency		= $row_get_po_items['original_currency'];		// look this up!
							$po_item_original_rate			= $row_get_po_items['original_rate'];
							
							$total_qty = $total_qty + $po_item_part_qty;
							
							
							// GET THE ASSOCIATED PO:
							
							$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $po_item_purchase_order_ID;
							$result_get_PO = mysqli_query($con,$get_PO_SQL);
							// while loop
							while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

									// now print each record:
									$PO_id 						= $row_get_PO['ID'];
									$PO_number 					= $row_get_PO['PO_number'];
									$PO_created_date 			= $row_get_PO['created_date'];
									$PO_description 			= $row_get_PO['description'];
									$PO_record_status 			= $row_get_PO['record_status'];
									$PO_supplier_ID 			= $row_get_PO['supplier_ID'];  				// LOOK THIS UP!
									$PO_created_by 				= $row_get_PO['created_by']; 				// use get_creator($PO_created_by);
									$PO_date_needed 			= $row_get_PO['date_needed'];
									$PO_date_delivered 			= $row_get_PO['date_delivered'];
									$PO_approval_status 		= $row_get_PO['approval_status']; 			// look this up?
									$PO_payment_status 			= $row_get_PO['payment_status']; 			// look this up?
									$PO_completion_status 		= $row_get_PO['completion_status'];
			
									// ADDING NEW VARIABLES AS WE EXPAND THIS PART OF THE SYSTEM:
									$PO_remark 					= $row_get_PO['remark'];
									$PO_approved_by 			= $row_get_PO['approved_by']; 				// use get_creator($PO_approved_by);
									$PO_approval_date 			= $row_get_PO['approval_date']; 
									$PO_include_CoC 			= $row_get_PO['include_CoC'];
									$PO_date_confirmed 			= $row_get_PO['date_confirmed'];
									$PO_ship_via 				= $row_get_PO['ship_via'];
									$PO_special_reqs 			= $row_get_PO['special_reqs'];
									$PO_related_standards 		= $row_get_PO['related_standards'];
									$PO_special_contracts 		= $row_get_PO['special_contracts'];
									$PO_qualification_personnel = $row_get_PO['qualification_personnel'];
									$PO_QMS_reqs 				= $row_get_PO['QMS_reqs'];
									$PO_local_location_ID 		= $row_get_PO['local_location_ID'];			// use function: get_location($PO_local_location_ID,1);
									$PO_HQ_location_ID 			= $row_get_PO['HQ_location_ID'];			// use function! get_location($PO_HQ_location_ID,1);
									$PO_ship_to_location_ID		= $row_get_PO['ship_to_location_ID'];		// use function! get_location($PO_ship_to_location_ID,0); (show title ONLY)

									// ADDING NEW VARIABLES - DEFAULT CURRENCY!
		
									$PO_default_currency		= $row_get_PO['default_currency']; // look this up!
									$PO_default_currency_rate	= $row_get_PO['default_currency_rate'];
		
											// now get the currency info
											$get_PO_default_currency_SQL = "SELECT * FROM `currencies` WHERE `ID` ='" . $PO_default_currency . "'";
											// debug:
											// echo '<h3>'.$get_PO_default_currency_SQL.'<h3>';
											$result_get_PO_default_currency = mysqli_query($con,$get_PO_default_currency_SQL);
											// while loop
											while($row_get_PO_default_currency = mysqli_fetch_array($result_get_PO_default_currency)) {

												// now print each result to a variable:
												$PO_default_currency_ID 			= $row_get_PO_default_currency['ID'];
												$PO_default_currency_name_EN		= $row_get_PO_default_currency['name_EN'];
												$PO_default_currency_name_CN		= $row_get_PO_default_currency['name_CN'];
												$PO_default_currency_one_USD_value	= $row_get_PO_default_currency['one_USD_value'];
												$PO_default_currency_symbol			= $row_get_PO_default_currency['symbol'];
												$PO_default_currency_abbreviation	= $row_get_PO_default_currency['abbreviation'];
												$PO_default_currency_record_status	= $row_get_PO_default_currency['record_status'];

											}

									// count variants for this purchase order
									$count_batches_sql 		= "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $record_id;
									$count_batches_query 	= mysqli_query($con, $count_batches_sql);
									$count_batches_row 		= mysqli_fetch_row($count_batches_query);
									$total_batches 			= $count_batches_row[0];

							} // end while loop
							
							
								
							if ($PO_default_currency_ID != $po_item_original_currency) {
								// CURRENCY NEEDS ADJUSTING!
								echo '<h1>WARNING - CURRENCY (ID# ' . $po_item_original_currency . ') DOES NOT MATCH DEFAULT PO CURRENCY (ID# ' . $PO_default_currency_ID . ')!</h1>';
								
								/* 
								
								curencies don't match - let's fix it!
								CONVERT ALL TO DOLLARS (PO AND PO LINE ITEM)
								
								*/
								
								// now get the currency info
								$get_po_item_currency_SQL = "SELECT * FROM `currencies` WHERE `ID` ='" . $po_item_original_currency . "'";
								// debug:
								// echo '<h3>'.$get_po_item_currency_SQL.'<h3>';
								$result_get_po_item_currency = mysqli_query($con,$get_po_item_currency_SQL);
									// while loop
									while($row_get_po_item_currency = mysqli_fetch_array($result_get_po_item_currency)) {

										// now print each result to a variable:
										$po_item_currency_ID 			= $row_get_po_item_currency['ID'];
										$po_item_currency_name_EN		= $row_get_po_item_currency['name_EN'];
										$po_item_currency_name_CN		= $row_get_po_item_currency['name_CN'];
										$po_item_currency_one_USD_value	= $row_get_po_item_currency['one_USD_value'];
										$po_item_currency_symbol		= $row_get_po_item_currency['symbol'];
										$po_item_currency_abbreviation	= $row_get_po_item_currency['abbreviation'];
										$po_item_currency_record_status	= $row_get_po_item_currency['record_status'];
										
										// OK, now convert to dollars
										// $po_item_currency_USD_value = ($po_item_unit_price_currency / $po_item_currency_one_USD_value);
										
										// NOW CONVERT IT BACK TO PO DEFAULT RATE
										// $po_item_unit_price_currency = ($po_item_unit_price_currency * $PO_default_currency_one_USD_value);
										
										// IDEAL WORLD - now update the database - update line item to match default PO currency
										// // //
									}
								
								}
							
								// NOW DO THE TOTALS CALCULATIONS
								$line_total = ($po_item_unit_price_currency * $po_item_part_qty);	
								$subtotal = $subtotal + $line_total;
							
								// get part revision info:
								$get_po_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` ='" . $po_item_part_revision_ID . "'";
								// debug:
								// echo '<h2>'.$get_po_part_rev_SQL . '</h2>'; 
								$result_get_po_part_rev = mysqli_query($con,$get_po_part_rev_SQL);
								// while loop
								while($row_get_po_part_rev = mysqli_fetch_array($result_get_po_part_rev)) {

									// now print each record:
									$po_rev_id 			= $row_get_po_part_rev['ID'];
									$po_rev_part_id 	= $row_get_po_part_rev['part_ID'];
									$po_rev_number 		= $row_get_po_part_rev['revision_number'];
									$po_rev_remarks 	= $row_get_po_part_rev['remarks'];
									$po_rev_date 		= $row_get_po_part_rev['date_approved'];
									$po_rev_user 		= $row_get_po_part_rev['user_ID'];

								}
								
								// now get the part info
								$get_po_part_SQL = "SELECT * FROM `parts` WHERE `ID` = '" . $po_rev_part_id . "'";
								// debug:
								// echo '<h3>' . $get_po_part_SQL . '</h3>'; 
								$result_get_po_part = mysqli_query($con,$get_po_part_SQL);
								// while loop
								while($row_get_po_part = mysqli_fetch_array($result_get_po_part)) {

									// now print each result to a variable:
									$po_part_id 		= $row_get_po_part['ID'];
									$po_part_code 		= $row_get_po_part['part_code'];
									$po_part_name_EN 	= $row_get_po_part['name_EN'];
									$po_part_name_CN 	= $row_get_po_part['name_CN'];

								}
                        ?>
					  
					  
                        <tr>
                        	<td> 
								<!-- ********************************************************* -->
								<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
								<!-- ********************************************************* -->
								
								<?php 
								
								// VARS YOU NEED TO WATCH / CHANGE:
								$add_to_form_name 	= 'line_item_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
								$form_ID 			= $po_item_ID;					// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
								$edit_URL 			= 'purchase_order_item_edit'; 	// REQUIRED - specify edit page URL
								$add_URL 			= 'purchase_order_item_add'; 	// REQURED - specify add page URL
								$table_name 		= 'purchase_order_items';		// REQUIRED - which table are we updating?
								$src_page 			= $this_file;					// REQUIRED - this SHOULD be coming from page_functions.php
								$add_VAR 			= 'PO_ID='.$record_id.''; 		// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
								
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
													  		echo '?' . $add_VAR;  // NOTE THE LEADING '?' <<<
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
                            <td><?php echo date("Y-m-d", strtotime($PO_created_date)); ?></td>
                            <td><a href="purchase_order_view.php?id=<?php echo $PO_id; ?>"><?php echo $PO_number; ?></a></td>
                            <td>
                            	<a href="part_view.php?id=<?php echo $po_part_id; ?>" class="btn btn-info btn-xs" title="View Part Profile">
                            		<?php echo 
                            			$po_part_code; 
                            		?></a>
                            		
                            		 - 
                            		 
                            	<a href="part_view.php?id=<?php echo $po_part_id; ?>" class="btn btn-default btn-xs" title="View Part Profile">
                            		 <?php 
                            			echo $po_part_name_EN; 
                            			if (($po_part_name_CN != '')&&($po_part_name_CN != '中文名')) { 
                            				echo ' / ' . $po_part_name_CN; 
                            			} 
                            		?>
                            	</a>
                            	
                            	<span class="btn btn-xs btn-warning" title="Rev. ID#: <?php echo $po_rev_id; ?>">
									<?php echo $po_rev_number; ?>
								</span>
                            	
                            	
								<br />
                            	<?php echo nl2br($po_item_item_notes); ?>
                            </td>
                            <td><?php echo number_format($po_item_part_qty); ?></td>
                            <td><?php 
                            	echo $PO_default_currency_symbol;	// NOTE: We are using the default PO currency symbol
                            	echo number_format($po_item_unit_price_currency, 2); ?></td>
                            <td><?php
                            	echo $PO_default_currency_symbol;
                            	
                            	// LINE TOTALS!
                            	echo number_format($line_total);
                            		
                            ?></td>
                        </tr>
                        <?php 
                        // END GET PURCHASE ORDER LINE ITEMS FROM DB (while loop)
                        }
                        if ($po_item_ID	== '') {
                        ?>
                        	<tr>
                        		<td colspan="7" class="text-danger">NO ITEMS FOUND</td>
                        	</tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        	<tr>
                        		<td colspan="7">&nbsp;</td>
                        	</tr>
                        </tfoot>
                    </table>
                </div>
                
                <?php add_button($record_id, 'purchase_order_item_add', 'PO_ID', 'Click here to add another item to this Purchase Order'); ?>
				
		  </div>
		</div>
	</section>
	<!-- END PANEL - LINE ITEMS -->


					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->
<div id="dialog" class="modal-block mfp-hide">
			<section class="panel">
				<header class="panel-heading">
					<h2 class="panel-title">Are you sure?</h2>
				</header>
				<div class="panel-body">
					<div class="modal-wrapper">
						<div class="modal-text">
							<p>Are you sure that you want to delete this row?</p>
						</div>
					</div>
				</div>
				<footer class="panel-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button id="dialogConfirm" class="btn btn-primary">Confirm</button>
							<button id="dialogCancel" class="btn btn-default">Cancel</button>
						</div>
					</div>
				</footer>
			</section>
		</div>
<?php
// now close the page out:
pagefoot($page_id);

?>
