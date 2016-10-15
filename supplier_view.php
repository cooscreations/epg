<meta content="text/html; charset=utf-8" http-equiv="content-type" /><?php
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

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else { // no id = nothing to see here!
	header("Location: suppliers.php?msg=NG&action=view&error=no_id");
	exit();
}

// pull the header and template stuff:
pagehead($page_id);

// now get the supplier info:
$get_sups_SQL = "SELECT * FROM `suppliers` WHERE `ID` = " . $record_id;
                                                                     // echo $get_sups_SQL;

$result_get_sups = mysqli_query($con,$get_sups_SQL);

// while loop
while($row_get_sup = mysqli_fetch_array($result_get_sups)) {
	$sup_ID 					= $row_get_sup['ID'];
	$sup_en 					= $row_get_sup['name_EN'];
	$sup_cn 					= $row_get_sup['name_CN'];
	$sup_web 					= $row_get_sup['website'];
	$sup_internal_ID 			= $row_get_sup['epg_supplier_ID'];
	$sup_status 				= $row_get_sup['supplier_status'];
	$sup_part_classification 	= $row_get_sup['part_classification']; // look up
	$sup_item_supplied 			= $row_get_sup['items_supplied'];
	$sup_part_type_ID 			= $row_get_sup['part_type_ID']; // look up
	$sup_certs 					= $row_get_sup['certifications'];
	$sup_cert_exp_date 			= $row_get_sup['certification_expiry_date'];
	$sup_evaluation_date 		= $row_get_sup['evaluation_date'];
	$sup_address_EN 			= $row_get_sup['address_EN'];
	$sup_address_CN 			= $row_get_sup['address_CN'];
	$sup_country_ID 			= $row_get_sup['country_ID']; // look up
	$sup_contact_person 		= $row_get_sup['contact_person'];
	$sup_mobile_phone 			= $row_get_sup['mobile_phone'];
	$sup_telephone 				= $row_get_sup['telephone'];
	$sup_fax 					= $row_get_sup['fax'];
	$sup_email_1 				= $row_get_sup['email_1'];
	$sup_email_2 				= $row_get_sup['email_2'];
	$sup_record_status			= $row_get_sup['record_status'];
	$sup_controlled				= $row_get_sup['controlled'];

			// VENDOR CLASSIFICATION BY STATUS:

			$get_sup_status_SQL = "SELECT * FROM `supplier_status` WHERE `status_level` ='" . $sup_status . "'";
			// echo $get_vendor_status_SQL;

			$result_get_sup_status = mysqli_query($con,$get_sup_status_SQL);
			// while loop
			while($row_get_sup_status = mysqli_fetch_array($result_get_sup_status)) {
				$sup_status_ID 				= $row_get_sup_status['ID'];
				$sup_status_name_EN 		= $row_get_sup_status['name_EN'];
				$sup_status_name_CN 		= $row_get_sup_status['name_CN'];
				$sup_status_level 			= $row_get_sup_status['status_level'];
				$sup_status_description 	= $row_get_sup_status['status_description'];
				$sup_status_color_code 		= $row_get_sup_status['color_code'];
				$sup_status_icon 			= $row_get_sup_status['icon'];
			}



			// GET PART CLASSIFICATION:
			$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $sup_part_classification . "'";
			// echo $get_part_class_SQL;

			$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
			// while loop
			while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
				$part_class_EN 				= $row_get_part_class['name_EN'];
				$part_class_CN 				= $row_get_part_class['name_CN'];
				$part_class_description 	= $row_get_part_class['description'];
				$part_class_color 			= $row_get_part_class['color'];
			}

} // end get user info WHILE loop

?>

<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Supplier Profile - <?php echo $sup_en;
        if (($sup_cn!='')&&($sup_cn!='中文名')){
         	?> / <?php echo $sup_cn;
        }
        ?></h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="suppliers.php">All Suppliers</a></li>
                <li><span>Supplier Profile</span></li>
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
            <!-- Supplier JUMPER -->
            <select onchange="document.location = this.value" data-plugin-selecttwo class="form-control populate">
                <option value="#" selected="selected">JUMP TO ANOTHER SUPPLIER / 看别的供应商:</option>
                <option value="suppliers.php">View All / 看全部</option>
                <?php

                $get_j_sups_SQL = "SELECT * FROM `suppliers`";
                // echo $get_j_sups_SQL;

                $result_get_j_sups = mysqli_query($con,$get_j_sups_SQL);
					  		// while loop
                while($row_get_j_sup = mysqli_fetch_array($result_get_j_sups)) {

                    $j_sup_ID 		= $row_get_j_sup['ID'];
                    $j_sup_en 		= $row_get_j_sup['name_EN'];
                    $j_sup_cn 		= $row_get_j_sup['name_CN'];
                    $j_sup_EPG_ID 	= $row_get_j_sup['epg_supplier_ID'];

							   ?>
                <option value="supplier_view.php?id=<?php echo $j_sup_ID; ?>"><?php echo $j_sup_EPG_ID . " - " . $j_sup_en; if (($j_sup_cn != '')&&($j_sup_cn != '中文名')) { ?> / <?php echo $j_sup_cn; } ?></option>
                <?php
							  } // end get supplier list
							  ?>
                <option value="suppliers.php">View All / 看全部</option>
            </select>
            <!-- / Supplier JUMPER -->
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <!-- START MAIN BODY COLUMN: -->
    <div class="col-md-12">



        <div class="row">

				<div class="col-md-4 col-lg-3">


							<?php
							// now run the admin bar function:
							admin_bar('supplier');
							?>

				<section class="panel">
						<header class="panel-heading">
							<div class="panel-actions">
								<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
								<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
							</div>

							<h2 class="panel-title">
								<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
								<span class="va-middle">General Details</span>
							</h2>
						</header>
						<div class="panel-body">
							<div class="content">


							<ul class="simple-card-list mb-xlg">
								<li class="<?php echo $sup_status_color_code; ?>">
									<h3><?php echo $sup_status_name_EN; if (($sup_status_name_CN!='')&&($sup_status_name_CN!='中文名')) { ?> / <?php echo $sup_status_name_CN; }?></h3>
									<p>Supplier Status</p>
								</li>
								<li class="<?php echo $part_class_color; ?>">
									<h3><?php echo $part_class_EN; ?> / <?php echo $part_class_CN; ?></h3>
									<p>Vendor Classification</p>
								</li>
								<?php 
								if ($sup_controlled == 1) {
									?>
									<li class="danger">
										<h3>Controlled</h3>
										<p>Highest Requirements</p>
									</li>
								<?php
								}
								else {
									?>
									<li class="success">
										<h3>Not Controlled</h3>
										<p>Lower Requirements</p>
									</li>
								<?php
								}
								?>
								<li class="warning">
									<h3><?php echo $sup_evaluation_date; ?></h3>
									<p>Next Evaluation</p>
								</li>
							</ul>
						  </div>
						</div>
						  <div class="panel-footer">
							<div class="text-right">
								<a class="text-uppercase text-muted" href="#">(Edit)</a>
							</div>
						  </div>
					</section>



					<section class="panel">
						<header class="panel-heading">
							<div class="panel-actions">
								<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
								<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
							</div>

							<h2 class="panel-title">
								<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-envelope"></i></span>
								<span class="va-middle">Contact Details</span>
							</h2>
						</header>
						<div class="panel-body">
							<div class="content">
								<ul>
								  <li>
									<strong>Address:</strong>
									<?php echo $sup_address_EN; ?>
								  </li>
								  <li>
									<strong>地址:</strong>
									<?php echo $sup_address_CN; ?>
								  </li>
								  <li>
									<strong>Country / 国家:</strong>
									<?php echo $sup_address_CN; ?>
								  </li>
								  <li>
									<strong>Phone / 电话:</strong>
									<?php echo $sup_telephone; ?>
								  </li>
								  <li>
									<strong>Fax:</strong>
									<?php echo $sup_fax; ?>
								  </li>
								  <li>
									<strong>Website:</strong>
									<a href="<?php echo $sup_web; ?>" target="_blank" title="Launch in a new window"><?php echo $sup_web; ?></a>
								  </li>
								</ul>
						  </div>
						</div>
						  <div class="panel-footer">
							<div class="text-right">
								<a class="text-uppercase text-muted" href="#">(Edit)</a>
							</div>
						  </div>
					</section>

					<section class="panel">
						<header class="panel-heading">
							<div class="panel-actions">
								<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
								<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
							</div>

							<h2 class="panel-title">
								<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-certificate"></i></span>
								<span class="va-middle">Certificates</span>
							</h2>
						</header>

						<div class="panel-body">
							<div class="content">
								<ul class="simple-user-list">
									<li>
										<figure class="image rounded">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe Junior" class="img-circle" />
										</figure>
										<span class="title"><?php echo $sup_certs; ?></span>
										<span class="message truncate"><?php echo $sup_cert_exp_date; ?></span>
									</li>
								</ul>
						  </div>
						</div>
						  <div class="panel-footer">
							<div class="text-right">
								<a class="text-uppercase text-muted" href="#">(Edit)</a>
							</div>
						  </div>
					</section>

			</div>



        <div class="col-md-8 col-lg-9">

        <div class="row">
          <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                </div>

                <h2 class="panel-title"><?php echo $sup_en; if (($sup_cn!='') && ($sup_cn!='中文名')) { ?> / <?php echo $sup_cn; } ?></h2>
            </header>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                        <tr>
                            <th>Name:</th>
                            <td><?php echo $sup_en; ?></td>
                        </tr>
                        <tr>
                            <th>名字:</th>
                            <td><?php  if (($sup_cn!='') && ($sup_cn!='中文名')) { echo $sup_cn; } else { echo '<span class="text-danger">没有中文公司名字</span>'; } ?></td>
                        </tr>
                        <tr>
                            <th>Supplier #:</th>
                            <td><?php echo $sup_internal_ID; ?></td>
                        </tr>
                        <tr>
                            <th>Items Supplied:</th>
                            <td><?php echo '<em>coming soon</em>'; ?></td>
                        </tr>
                        <tr>
                            <th>Part Type:</th>
                            <td><?php echo '<em>coming soon</em>'; ?></td>
                        </tr>
                        <tr>
                            <th>Record Status:</th>
                            <td><?php record_status($sup_record_status); ?></td>
                        </tr>
                    </table>
                </div><!-- resonsive table div -->

            </div><!-- panel body -->
            
            
            			  <div class="panel-footer">
							<div class="text-right">
								:)
							</div>
						  </div>
            
            
		  </section>
        </div><!-- row -->

        <div class="clearfix">&nbsp;</div>

		<!-- 
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		              START SUPPLIER P.O. LIST SECTION 
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		-->
		
		<div class="row">
		  <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                </div>

                <h2 class="panel-title">Purchase Orders From <?php echo $sup_en; if (($sup_cn!='') && ($sup_cn!='中文名')) { ?> / <?php echo $sup_cn; } ?></h2>
            </header>
            
            
						<div class="panel-body">
		
 					<?php
					 add_button($record_id, 'purchase_order_add', 'sup_ID', 'Click here to add a new purchase order to this vendor record');
					 ?>

				<div class="col-md-12">

    <div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
					 <thead>
						 <tr>
							<th class="text-center"><i class="fa fa-cog" title="Actions"></i></th>
							<th class="text-center"><a href="supplier_view.php?id=<?php echo $record_id; ?>&sort=PO_number&dir=ASC<?php echo $add_URL_vars_year; ?>">P.O. number</a></th>
							<th class="text-center"><a href="supplier_view.php?id=<?php echo $record_id; ?>&sort=created_date&dir=DESC<?php echo $add_URL_vars_year; ?>">Created Date</a></th>
							<th class="text-center"># Items</th>
							<th class="text-center">Total QTY</th>
							<th class="text-center"># Batches</th>
						</tr>
					  </thead>
					  <tbody>

					  <?php
					  if (isset($_REQUEST['sort'])) {
					  	$order_by = " ORDER BY `" . $_REQUEST['sort'] . "` " . $_REQUEST['dir'] . "";
					  }
					  else {
					  	$order_by = " ORDER BY  `purchase_orders`.`PO_number` DESC";
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

					  $get_POs_SQL = "SELECT * FROM  `purchase_orders` WHERE `supplier_ID` = '" . $record_id . "' AND `record_status` =2" . $add_SQL . $order_by;
					  // echo $get_mats_SQL;

					  $PO_count 		= 0;
					  $total_batches 	= 0;
					    
					  $po_items_count 	= 0;
					  $total_qty 		= 0;
					  
					  
						$grand_total_qty		= 0;
						$grand_total_items 		= 0;
						$grand_total_batches	= 0;

					  $result_get_POs = mysqli_query($con,$get_POs_SQL);
					  // while loop
					  while($row_get_POs = mysqli_fetch_array($result_get_POs)) {

					  		// now assign the results to the vars
							$PO_ID = $row_get_POs['ID'];
							$PO_number = $row_get_POs['PO_number'];
							$PO_created_date = $row_get_POs['created_date'];
							$PO_description = $row_get_POs['description'];
							$PO_record_status = $row_get_POs['record_status'];
							$PO_supplier_ID = $row_get_POs['supplier_ID'];
							$PO_created_by = $row_get_POs['created_by'];

							// NO NEED TO GET SUPPLIER INFO AS THIS IS THE SUPPLIER PAGE!

							  ?>

							  <tr>
								<td class="text-center">
						
								<!-- ********************************************************* -->
								<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
								<!-- ********************************************************* -->
		
								<?php 
		
								// VARS YOU NEED TO WATCH / CHANGE:
								$add_to_form_name 	= 'PO_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
								$form_ID 			= $PO_ID;					// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
								$edit_URL 			= 'purchase_order_edit'; 	// REQUIRED - specify edit page URL
								$add_URL 			= 'purchase_order_add'; 	// REQURED - specify add page URL
								$table_name 		= 'purchase_orders';		// REQUIRED - which table are we updating?
								$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
								$add_VAR 			= 'sup_ID=' . $record_id; 	// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
		
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
									<a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>"><?php echo $PO_number; ?></a>
								</td>
								<td class="text-center">
									<a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>">
										<?php echo date("Y-m-d", strtotime($PO_created_date)); ?>
									</a>
								</td>
								<td class="text-center"><?php 
						
								// 1. get list of P.O. items for this P.O. 
						
								$po_items_count 		= 0;
								$total_qty 				= 0;
						
								$get_purchase_order_items = "SELECT * FROM `purchase_order_items` WHERE `purchase_order_ID` ='" . $PO_ID . "' AND `record_status` = '2'";
								// echo '<h1>' . $get_purchase_order_items . '</h1>'; 
								$result_get_po_items = mysqli_query($con,$get_purchase_order_items);
								// while loop
								while($row_get_po_items = mysqli_fetch_array($result_get_po_items)) {
									$po_item_ID						= $row_get_po_items['ID'];
									$po_item_purchase_order_ID		= $row_get_po_items['purchase_order_ID'];		// should = RECORD_ID for this PO
									$po_item_part_revision_ID		= $row_get_po_items['part_revision_ID'];		// look this up!
									$po_item_part_qty				= $row_get_po_items['part_qty'];
									$po_item_record_status			= $row_get_po_items['record_status']; 			// should be 2
									$po_item_item_notes				= $row_get_po_items['item_notes'];
									$po_item_unit_price_USD			= $row_get_po_items['unit_price_USD'];
									$po_item_unit_price_currency	= $row_get_po_items['unit_price_currency']; 	
									$po_item_original_currency		= $row_get_po_items['original_currency'];		// look this up!
									$po_item_original_rate			= $row_get_po_items['original_rate'];
							
									$total_qty = $total_qty + $po_item_part_qty;
						
								// 2. for each list item found, append the total QTY count
						
									$po_items_count = $po_items_count + 1;
						
								}
						
								if ($po_items_count == 0) {
									echo '<a href="purchase_order_view.php?id=' . $PO_ID . '" class="text-danger">';
									echo number_format($po_items_count); // THIS IS THE TOTAL NUMBER OF LINES IN THE P.O.
									echo '</a>';
								}
								else {
									echo '<a href="purchase_order_view.php?id=' . $PO_ID . '">';
									echo number_format($po_items_count); // THIS IS THE TOTAL NUMBER OF LINES IN THE P.O.
									echo '</a>';
								}
						
						
								?></td>
								<td class="text-center"><?php 
						
								if ($total_qty == 0) {
									echo '<a href="purchase_order_view.php?id=' . $PO_ID . '" class="text-danger">';
									echo number_format($total_qty); // THIS IS THE TOTAL NUMBER OF LINES IN THE P.O.
									echo '</a>';
								}
								else {
									echo '<a href="purchase_order_view.php?id=' . $PO_ID . '">';
									echo number_format($total_qty); // THIS IS THE TOTAL NUMBER OF LINES IN THE P.O.
									echo '</a>';
								}
						
						
								?></td>
								<td class="text-center">
								<!-- COUNT BATCHES -->
								<?php
									// count batches for this PO
									$count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $PO_ID;
									$count_batches_query = mysqli_query($con, $count_batches_sql);
									$count_batches_row = mysqli_fetch_row($count_batches_query);
									$total_batches = $count_batches_row[0];



								if ($total_batches == 0) {
									echo '<a href="purchase_order_view.php?id=' . $PO_ID . '" class="text-danger">';
									echo number_format($total_batches);
									echo '</a>';
								}
								else {
									echo '<a href="purchase_order_view.php?id=' . $PO_ID . '">';
									echo number_format($total_batches);
									echo '</a>';
								}

								?>
								<!-- END COUNT BATCHES -->
								</td>
							  </tr>

							  <?php

								$grand_total_qty		= $grand_total_qty + $total_qty;
								$grand_total_items 		= $grand_total_items + $po_items_count;
								$grand_total_batches	= $grand_total_batches + $total_batches;
							  	$PO_count 				= $PO_count + 1;

					  } // end while loop
					  ?>
					  </tbody>
					  <tfoot>
						<tr>
							<th class="text-center" colspan="3">TOTAL ROWS: <?php echo $PO_count; ?></th>
							<th class="text-center"><?php echo number_format($grand_total_items); ?></th>
							<th class="text-center"><?php echo number_format($grand_total_qty); ?></th>
							<th class="text-center"><?php echo number_format($grand_total_batches); ?></th>
						</tr>
					  </tfoot>

					 </table>
					 </div>
					 </div>
					 <?php
					 add_button($record_id, 'purchase_order_add', 'sup_ID', 'Click here to add a new purchase order to this vendor record');
					 ?>
					 

            </div><!-- panel body -->
            
            			  <div class="panel-footer">
							<div class="text-left">
								<a class="btn btn-default btn-xs" href="purchase_orders.php">View All P.O.s</a>
							</div>
						  </div>
            
            
		  </section>
        </div><!-- row -->

        <div class="clearfix">&nbsp;</div>
		<!-- 
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		              END SUPPLIER P.O. LIST SECTION 
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		-->

		<!-- 
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		              START SUPPLIER BATCH LIST SECTION 
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		-->

		<div class="row">
		  <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                </div>

                <h2 class="panel-title">Batches From <?php echo $sup_en; if (($sup_cn!='') && ($sup_cn!='中文名')) { ?> / <?php echo $sup_cn; } ?></h2>
            </header>
            
            
						<div class="panel-body">
			 
			<?php add_button($record_id, 'part_batch_add', 'supplier_ID', 'Click here to add a new batch to this vendor record'); ?>

			<div class="table-responsive">
			 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
			 <thead>
				  <tr>
				  	<th class="text-center"><i class="fa fa-cog" title="Action"></i></th>
					<th class="text-center">Batch Number</th>
					<th class="text-center">Part Code</th>
					<th class="text-center">Rev.</th>
					<th class="text-center">Name / 名字</th>
					<th class="text-center">QTY Rec.</th>
				  </tr>
			  </thead>
			  <tbody>
				<?php

			  $batch_count = 0;
			  $movement_in_total = 0;

			  // GET BATCHES:
				$get_batches_SQL = "SELECT * FROM `part_batch` WHERE `supplier_ID` =" . $record_id . "";
				// debug
				// echo $get_batches_SQL; 
				$result_get_batches = mysqli_query($con,$get_batches_SQL);
				// while loop
				while($row_get_batches = mysqli_fetch_array($result_get_batches)) {

					// now print each record to a variable:
					$batch_id = $row_get_batches['ID'];
					$batch_part_ID = $row_get_batches['part_ID'];
					$batch_number = $row_get_batches['batch_number'];
					$batch_part_rev = $row_get_batches['part_rev'];


					// get part revision info:
					$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $batch_part_rev;
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

					// now get the part info
					$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $batch_part_ID;
					$result_get_part = mysqli_query($con,$get_part_SQL);
					// while loop
					while($row_get_part = mysqli_fetch_array($result_get_part)) {

						// now print each result to a variable:
						$part_id = $row_get_part['ID'];
						$part_code = $row_get_part['part_code'];
						$part_name_EN = $row_get_part['name_EN'];
						$part_name_CN = $row_get_part['name_CN'];

					}

			  ?>

			  <tr<?php if ($batch_id == $change_record_id) { ?> class="success"<?php } ?>>
			  	<td class="text-center">
						
					<!-- ********************************************************* -->
					<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
					<!-- ********************************************************* -->

					<?php 

					// VARS YOU NEED TO WATCH / CHANGE:
					$add_to_form_name 	= 'batch_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
					$form_ID 			= $batch_id;					// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
					$edit_URL 			= 'part_batch_edit'; 	// REQUIRED - specify edit page URL
					$add_URL 			= 'part_batch_add'; 	// REQURED - specify add page URL
					$table_name 		= 'part_batch';		// REQUIRED - which table are we updating?
					$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
					$add_VAR 			= 'sup_ID=' . $record_id; 	// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO

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
					<a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo $batch_number; ?></a>
				</td>
				<td class="text-center">
					<?php part_num_from_rev($rev_id); ?>
				</td>
				<td class="text-center">
					<?php part_rev($rev_id); ?>
				</td>
				<td>
					<?php part_name_from_rev($rev_id); ?>
				</td>
				<td class="text-right">
				<!-- get first batch count: -->
				<?php
				// now use earliest record in the DB to find the QTY
					$get_first_batch_qty_SQL = "SELECT * FROM  `part_batch_movement` WHERE `part_batch_ID` = " . $batch_id . " AND `amount_in` > 0 ORDER BY `date` ASC LIMIT 0, 1";
					$result_get_first_batch_qty = mysqli_query($con,$get_first_batch_qty_SQL);


					$movement_in = 0; // (RESET VARIABLE)

					// while loop
					while($row_get_first_batch_qty = mysqli_fetch_array($result_get_first_batch_qty)) {

						// now print each record to a variable:
						$movement_in = $row_get_first_batch_qty['amount_in'];
					}

					if ($movement_in == '') { $movement_in = 0; }

					// now append the total part count
					$movement_in_total = $movement_in_total + $movement_in;
				?>

				<a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo number_format($movement_in); ?></a>

				<!-- end first batch count -->
				</td>
			  </tr>

			  <?php

			  $batch_count = $batch_count + 1;

			  }

			  ?>
			  </tbody>

			  <tfoot>
				  <tr>
					<th colspan="5">TOTAL ROWS: <?php echo $batch_count; ?></th>
					<th class="text-right">
						<?php echo number_format($movement_in_total); ?>
					</th>
				  </tr>
			  </tfoot>

			 </table>
			 </div>
			
			 <?php add_button($record_id, 'part_batch_add', 'supplier_ID', 'Click here to add a new batch to this vendor record'); ?>

						<!-- now close the panel -->
						</div>
						
						
						<div class="panel-footer">
							
							<div class="text-left">
								<a class="btn btn-default btn-xs" href="batch_log.php">View All Batches</a>
							</div>
						  </div>
						
					</section>
				</div>
			</div> <!-- end row! -->



		<!-- 
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		              END SUPPLIER BATCH LIST SECTION 
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////
		-->


        </div><!-- end of sizing div? md-8 / lg-9 -->


		</div>

        <div class="clearfix">&nbsp;</div>
        <!-- END OF SUPPLIER PROFILE (numbered tab) -->
    </div>
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
