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
	header("Location: purchase_orders.php?msg=NG&action=view&error=no_id");
	exit();		
}

if ($record_id != 0) {
	$get_po_SQL = "SELECT `ID`,`PO_number`, date(`created_date`) as 'created_date', `description`,`created_by`, `supplier_ID`  FROM  `purchase_orders` WHERE `ID` = " . $record_id;
	$result_get_po = mysqli_query($con,$get_po_SQL);
	// while loop
	while($row_get_po = mysqli_fetch_array($result_get_po)) {
		
			// now print each record:  
			$po_id = $row_get_po['ID'];
			$po_number = $row_get_po['PO_number'];
			$po_created_date = $row_get_po['created_date'];
			$po_description = $row_get_po['description'];
			$po_created_by_user = $row_get_po['created_by'];
			$po_supplier_id = $row_get_po['supplier_ID'];
			
	} // end while loop
}

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Purchase Order<?php if ($record_id != 0) { ?> : <? echo $po_number; } ?></h2>
					
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

									<h2 class="panel-title">Edit Purchase Order Details:</h2>
								</header>
								<div class="panel-body">
									<div class="form-group">
										<label class="col-md-3 control-label">P.O. Number:<span class="required">*</span></label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" placeholder="PO#" name="po_number" value="<?php echo $po_number; ?>" required/>
											<input type="hidden" name="po_id" value="<?php echo $po_id; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>	
									
									<div class="form-group">
										<label class="col-md-3 control-label">Supplier:<span class="required">*</span></label>
										<div class="col-md-5">
											<select data-plugin-selectTwo class="form-control populate" name="sup_ID" required>
											<?php 
											// get batch list
											$order_by = " ORDER BY `record_status` DESC";
											$get_sup_list_SQL = "SELECT * FROM `suppliers` WHERE `record_status` = 2 and `record_status` >= 4" . $order_by; // SHOWING APPROVED VENDORS ONLY!
											echo "<!-- DEBUG: " . $get_sup_list_SQL . " -->";
											$result_get_sup_list = mysqli_query($con,$get_sup_list_SQL);
											// while loop
											while($row_get_sup_list = mysqli_fetch_array($result_get_sup_list)) {

													// now print each record:  
													$sup_id = $row_get_sup_list['ID']; 
													$sup_epg_supplier_ID = $row_get_sup_list['epg_supplier_ID'];
													$sup_name_EN = $row_get_sup_list['name_EN'];
													$sup_name_CN = $row_get_sup_list['name_CN'];
													$sup_website = $row_get_sup_list['website'];
													$sup_record_status = $row_get_sup_list['record_status'];
													$sup_part_classification = $row_get_sup_list['part_classification'];
													$sup_items_supplied = $row_get_sup_list['items_supplied'];
													$sup_part_type_ID = $row_get_sup_list['part_type_ID'];
													$sup_certifications = $row_get_sup_list['certifications'];
													$sup_certification_expiry_date = $row_get_sup_list['certification_expiry_date'];
													$sup_evaluation_date = $row_get_sup_list['evaluation_date'];
													$sup_address_EN = $row_get_sup_list['address_EN'];
													$sup_address_CN = $row_get_sup_list['address_CN'];
													$sup_country_ID = $row_get_sup_list['country_ID'];
													$sup_contact_person = $row_get_sup_list['contact_person'];
													$sup_mobile_phone = $row_get_sup_list['mobile_phone'];
													$sup_telephone = $row_get_sup_list['telephone'];
													$sup_fax = $row_get_sup_list['fax'];
													$sup_email_1 = $row_get_sup_list['email_1'];
													$sup_email_2 = $row_get_sup_list['email_2'];
												
													?>
													<option value="<?php echo $sup_id; ?>" <?php if ($sup_id == $po_supplier_id) { ?> selected="selected"<?php } ?>>
														<?php echo $sup_name_EN; if (($sup_name_CN!='')&&($sup_name_CN!='中文名')) { echo " / " . $sup_name_CN; } ?>
													</option>
													<?php
												}
												?>
											</select>
										</div>
										
										
										
										
							
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
												
									<div class="form-group">
										<label class="col-md-3 control-label">Description:<span class="required">*</span></label>
										<div class="col-md-5">
											<textarea class="form-control" rows="3" id="textareaDefault" name="description" required><?php echo $po_description; ?></textarea>
										</div>
										
							
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group">
												<label class="col-md-3 control-label">User:<span class="required">*</span></label>
												<div class="col-md-5">
													<select data-plugin-selectTwo class="form-control populate" name="user_ID" required>
													<?php 
													// get batch list
													$get_user_list_SQL = "SELECT * FROM `users` WHERE `record_status` = 2";
													$result_get_user_list = mysqli_query($con,$get_user_list_SQL);
													// while loop
													while($row_get_user_list = mysqli_fetch_array($result_get_user_list)) {
	
														// now print each record:  
														$user_id = $row_get_user_list['ID']; 
														$user_first_name = $row_get_user_list['first_name'];
														$user_last_name = $row_get_user_list['last_name'];
														$user_name_CN = $row_get_user_list['name_CN'];
														$user_email = $row_get_user_list['email'];
													?>
														<option value="<?php echo $user_id; ?>"<?php if ($user_id == $po_created_by_user) { ?> selected="selected"<?php } ?>><?php echo $user_first_name . " " . $user_last_name; if (($user_name_CN != '') && ($user_name_CN != '中文名')) { echo  ' / ' . $user_name_CN; }?></option>
														
														<?php 
														}
														?>
													</select>
												</div>
												
												<div class="col-md-1">
													<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
												</div>
												
									</div>
									
									<div class="form-group">
										<label class="col-md-3 control-label">Created Date:<span class="required">*</span></label>
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</span>
												<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_added" value="<?php echo $po_created_date; ?>" required>
											</div>
										</div>
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
								</div>
								
								
								<footer class="panel-footer">
										<?php 
										if (isset($_REQUEST['po_number'])) {
											?>
											<input type="hidden" value="<?php echo $_REQUEST['po_number']; ?>" name="po_number" />
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
						
						
					
					
								<!-- now close the panel --><!-- end row! -->
					 
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>