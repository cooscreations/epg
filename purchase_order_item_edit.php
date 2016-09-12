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
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: purchase_order_items.php?msg=NG&action=view&error=no_id");
	exit();
}


if ($record_id != 0) {

	$get_purchase_order_items = "SELECT * FROM `purchase_order_items` WHERE `ID` = '" . $record_id . "'";
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
		
			// curencies don't match - let's fix it!
			// CONVERT ALL TO DOLLARS (PO AND PO LINE ITEM)
		
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

	}
}

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Purchase Order Item</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="purchase_orders.php">All P.O.s</a>
									</li>
									<li>
										<a href="purchase_order_view.php?id=<?php echo $po_item_purchase_order_ID; ?>" title="Visit P.O. number <?php echo $PO_number; ?>">Original Purchase Order</a>
									</li>
								<li><span>Edit Purchase Order Item</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="purchase_order_item_edit_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">P.O. Item Details</h2>
								</header>
								<div class="panel-body">

									<div class="form-group">
										<label class="col-md-3 control-label">P.O. ID:</label>
										<div class="col-md-5">
											<?php purchase_orders_drop_down($po_item_purchase_order_ID); ?>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									

									<div class="form-group">
										<label class="col-md-3 control-label">Part Revision #:</label>
										<div class="col-md-5">
											<?php part_rev_drop_down($po_item_part_revision_ID); ?>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									

									<div class="form-group">
										<label class="col-md-3 control-label">Quantity:<span class="required">*</span></label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" placeholder="Enter whole numbers ONLY" name="amount" required value="<?php echo $po_item_part_qty; ?>" />
										</div>


										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Remarks:<span class="required">*</span></label>
										<div class="col-md-5">
											
											<textarea class="form-control" rows="3" id="textareaDefault" name="remarks" required><?php 
											
											// echo nl2br(htmlentities($po_item_item_notes, ENT_QUOTES, 'UTF-8'));
											
												$po_item_item_notes = str_replace("<br />", "\n", $po_item_item_notes);
												echo $po_item_item_notes;
											
												// echo nl2br($po_item_item_notes);  // THIS LINE SHOWS HTML eg. <br />
											
											?></textarea>
											
											 <!--
											<div name="remarks" id="remarks" class="summernote" data-plugin-summernote data-plugin-options='{ "height": 180, "codemirror": { "theme": "ambiance" } }'><?php 
											
												echo nl2br($po_item_item_notes);  // THIS LINE SHOWS HTML eg. <br />
											
											?></div>
											-->
											
										</div>


										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
								  </div>	
								</section>
								
								<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Financial Details</h2>
								</header>
								<div class="panel-body">
									
									<div class="form-group">
										<label class="col-md-1 control-label">Price:</label>
										
										
										
										<div class="col-md-1">
											<input type="text" class="form-control" id="inputDefault" name="po_item_unit_price_currency" value="<?php echo $po_item_unit_price_currency; ?>" />
										</div>
										
										
										<label class="col-md-1 control-label">Currency:</label>
										
										<div class="col-md-3">
											<select class="form-control populate" name="currency_id" id="currency_id">
												<?php 
												// now get the currency info
												$get_currency_SQL = "SELECT * FROM `currencies` WHERE `record_status` ='2'";
												// debug:
												// echo '<h3>'.$get_PO_default_currency_SQL.'<h3>';
												$result_get_currency = mysqli_query($con,$get_currency_SQL);
												// while loop
												while($row_get_currency = mysqli_fetch_array($result_get_currency)) {

													// now print each result to a variable:
													$currency_ID 			= $row_get_currency['ID'];
													$currency_name_EN		= $row_get_currency['name_EN'];
													$currency_name_CN		= $row_get_currency['name_CN'];
													$currency_one_USD_value	= $row_get_currency['one_USD_value'];
													$currency_symbol		= $row_get_currency['symbol'];
													$currency_abbreviation	= $row_get_currency['abbreviation'];
													$currency_record_status	= $row_get_currency['record_status'];

													

													// now output the results:
													?>
													<option value="<?php echo $currency_ID; ?>"<?php if ($po_item_original_currency == $currency_ID) { ?> selected="selected"<?php } ?>><?php 
														echo $currency_symbol;
														echo $currency_abbreviation;
													?> (<?php 
														echo $currency_name_EN; 
														if (($currency_name_CN!='')&&($currency_name_CN!='中文名')) {
															echo $currency_name_CN;
														}
													?>) @ <?php
														echo $currency_one_USD_value . ' / $USD';
													?></option>
													<?php

												}
												?>
											</select>
										</div>
										
										
										<label class="col-md-1 control-label"><abbr title="What is the exchange rate for USD?">$</abbr> Rate:</label>
										
										<div class="col-md-1">
											<input type="text" class="form-control" id="inputDefault" name="po_item_currency_rate" value="<?php echo $po_item_original_rate; ?>" />
										</div>
									
									
									
								  </div>	
								</section>
								
								<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Database Record Data</h2>
								</header>
								<div class="panel-body">

									<div class="form-group">
										<label class="col-md-3 control-label">Record Status:</label>
										<div class="col-md-5">
											<?php echo record_status_drop_down($PO_record_status); ?>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
								</div>


								<footer class="panel-footer">
									<!-- ADD ANY OTHER HIDDEN VARS HERE -->
								  <div class="col-md-5 text-left">	
									<?php form_buttons('purchase_order_view', $record_id); ?>
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
												<input type="radio" id="next_step" name="next_step" value="view_record"<?php if ($next_step_selected == 'view') { ?> checked="checked"<?php } ?>>
												<label for="radioExample9">View P.O.</label>
											</div>

											<div class="radio-custom radio-warning">
												<input type="radio" id="next_step" name="next_step" value="view_list"<?php if ($next_step_selected == 'add') { ?> checked="checked"<?php } ?>>
												<label for="radioExample10">View ALL P.O. line items</label>
											</div>
										</div>
										
										<!-- END OF NEXT STEP SELECTION -->
								  
								  
								</footer>
							</section>
										<!-- now close the form -->
										</form>


						</div>

						</div>




								<!-- now close the panel --><!-- end row! -->

					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
