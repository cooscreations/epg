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

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: purchase_orders.php?msg=NG&action=view&error=no_id");
	exit();
}

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

if ($record_id != 0) {
	$get_PO_SQL = "SELECT *  FROM  `purchase_orders` WHERE `ID` = " . $record_id;
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


	} // end while loop
}

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Purchase Order<?php if ($record_id != 0) { ?> : <? echo $PO_number; } ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="purchase_orders.php">Purchase Order</a>
									</li>
								<li><span>Edit Purchase Order</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="purchase_order_edit_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Supplier / Purchaser Details</h2>
								</header>
								<div class="panel-body">

									<div class="form-group">
										<label class="col-md-3 control-label">Supplier:<span class="required">*</span></label>
										<div class="col-md-5">
											<!-- PARSING SUP ID: <?php echo $PO_supplier_ID; ?> -->
											<?php supplier_drop_down($PO_supplier_ID); ?>
										</div>
										
										<div class="col-md-1">
					 						<?php add_button(0, 'supplier_add'); ?>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Local Office:</label>
										<div class="col-md-5">
											<!-- PARSING SUP ID: <?php echo $PO_supplier_ID; ?> -->
											<?php location_drop_down($PO_local_location_ID, 'local_location_ID'); ?>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Head Office:</label>
										<div class="col-md-5">
											<!-- PARSING SUP ID: <?php echo $PO_supplier_ID; ?> -->
											<?php location_drop_down($PO_HQ_location_ID, 'HQ_location_ID'); ?>
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

									<h2 class="panel-title">Order Details</h2>
								</header>
								<div class="panel-body">
									
									<div class="form-group">
										<label class="col-md-3 control-label">P.O. Number:<span class="required">*</span></label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" placeholder="PO#" name="po_number" value="<?php echo $PO_number; ?>" required/>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Description <em>(internal use only)</em>:</label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="description"><?php echo $PO_description; ?></textarea>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Ship via:</label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="ship_via"><?php echo $PO_ship_via; ?></textarea>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Ordered by:<span class="required">*</span></label>
										<div class="col-md-5">
											<?php creator_drop_down($PO_created_by); ?>
										</div>

										<div class="col-md-1">
											<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Date Ordered:</label>
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</span>
												<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_added" value="<?php echo $PO_created_date; ?>">
											</div>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Date Needed:</label>
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</span>
												<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_needed" value="<?php echo $PO_date_needed; ?>">
											</div>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Date Delivered:</label>
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</span>
												<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_delivered" value="<?php echo $PO_date_delivered; ?>">
											</div>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Ship To:<span class="required">*</span></label>
										<div class="col-md-5">
											<!-- PARSING SUP ID: <?php echo $PO_supplier_ID; ?> -->
											<?php location_drop_down($PO_ship_to_location_ID, 'ship_to_location_ID'); ?>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Completion Status:</label>
										<div class="col-md-5">
											<div class="m-md slider-primary" data-plugin-slider data-plugin-options='{ "value": <?php echo $PO_completion_status; ?>, "range": "min", "max": 100 }' data-plugin-slider-output="#listenSlider">
												<input id="listenSlider" type="hidden" value="<?php echo $PO_completion_status; ?>" name="completion_status" />
											</div>
										</div>
										
										<div class="col-md-1">
											<p class="output"><b><?php echo $PO_completion_status; ?>%</b></p>
										</div>
									</div>
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Payment Status:</label>
										<div class="col-md-5">
											<select class="form-control populate" name="payment_status" id="payment_status">
											  <option value="0"<?php if ($PO_payment_status == 0) { ?> selected="selected"<?php } ?>>✘ NOT PAID ✘</option>
											  <option value="1"<?php if ($PO_payment_status == 1) { ?> selected="selected"<?php } ?>>? PENDING ?</option>
											  <option value="2"<?php if ($PO_payment_status == 2) { ?> selected="selected"<?php } ?>>✔ PAID ✔</option>
											</select>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Currency & Rate:</label>
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
													<option value="<?php echo $currency_ID; ?>"<?php if ($PO_default_currency == $currency_ID) { ?> selected="selected"<?php } ?>><?php 
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
										
										<div class="col-md-2">
											<input type="text" class="form-control" id="inputDefault" name="po_default_currency_rate" value="<?php echo $PO_default_currency_rate; ?>" />
										</div>
										
										

										<div class="col-md-1">
											<button type="button" class="mb-xs mt-xs mr-xs btn btn-info pull-right" data-toggle="popover" data-container="body" data-placement="top" title="Current Exchange Rates" data-content="For current exchange rates, please refer to the drop down list on the left." data-original-title="Currency Exchange Rates" aria-describedby="popover352503"><i class="fa fa-info"></i></button>
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

									<h2 class="panel-title">Order Instructions</h2>
								</header>
								<div class="panel-body">
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Special requirements of the specifications, process requirements/protocols and requirements for approval of product or process:</label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="special_reqs"><?php echo $PO_special_reqs; ?></textarea>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Related standards:</label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="related_standards"><?php echo $PO_related_standards; ?></textarea>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Special contracts, quality agreements/supply agreements:</label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="special_contracts"><?php echo $PO_special_contracts; ?></textarea>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Special requirements for qualification personnel:</label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="qualification_personnel"><?php echo $PO_qualification_personnel; ?></textarea>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Special requirements for Quality Management System:</label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="QMS_reqs"><?php echo $PO_QMS_reqs; ?></textarea>
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

									<h2 class="panel-title">Authorisation</h2>
								</header>
								<div class="panel-body">
									
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Include Certificate of Compliance with Order:</label>
										<div class="col-md-5">
											<div class="switch switch-lg switch-success">
												<input type="checkbox" name="include_CoC" id="include_CoC" data-plugin-ios-switch<?php echo $PO_include_CoC == '1' ? ' checked="checked"' : '' ?> value="1" />
											</div>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Approval Status:</label>
										<div class="col-md-5">
											<select class="form-control populate" name="approval_status" id="approval_status">
											  <option value="0"<?php if ($PO_approval_status == 0) { ?> selected="selected"<?php } ?>>✘ NOT APPROVED ✘</option>
											  <option value="1"<?php if ($PO_approval_status == 1) { ?> selected="selected"<?php } ?>>? PENDING ?</option>
											  <option value="2"<?php if ($PO_approval_status == 2) { ?> selected="selected"<?php } ?>>✔ APPROVED ✔</option>
											</select>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Authorised by:</label>
										<div class="col-md-5">
											<?php creator_drop_down($PO_approved_by,'approved_by'); ?>
										</div>

										<div class="col-md-1">
											<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Date Authorised:</label>
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</span>
												<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_approved" value="<?php echo $PO_approval_date; ?>">
											</div>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									<!-- ************************************************************** -->

									<div class="form-group">
										<label class="col-md-3 control-label">Date Confirmed:</label>
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</span>
												<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_confirmed" value="<?php echo $PO_date_confirmed; ?>">
											</div>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									

									<div class="form-group">
										<label class="col-md-3 control-label">Comments:</label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="remarks"><?php echo $PO_remark; ?></textarea>
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
												<label for="radioExample10">View ALL P.O.s</label>
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
