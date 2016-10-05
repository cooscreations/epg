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

$page_id = 9;

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

				<section role="main" class="content-body content-footer-body">
					<header class="page-header">
						<h2>Purchase Orders (<?php echo $display_year; ?>)</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Purchase Orders</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">
							<!-- YEAR JUMPER -->

							<select onchange="document.location = this.value" data-plugin-selecttwo class="form-control populate">
								<option value="#" selected="selected">SELECT A YEAR / 选一年:</option>
								<option value="purchase_orders.php?year=all<?php echo $add_URL_vars_sort . $add_URL_vars_dir; ?>">View All / 看全部</option>

								<?php
									$start_year = 2010;
									$loop_year = $start_year;

									while ($loop_year <= date("Y")) {
										?>
								<option value="purchase_orders.php?year=<?php echo $loop_year; ?><?php echo $add_URL_vars_sort . $add_URL_vars_dir; ?>"<?php if ($loop_year == $_REQUEST['year']) { ?> selected="selected"<?php } ?>>SHOW POs FOR <?php echo $loop_year; ?> 的订单</option>
										<?
										$loop_year = $loop_year + 1;
									}
								?>
								<option value="purchase_orders.php?year=all<?php echo $add_URL_vars_sort . $add_URL_vars_dir; ?>">View All / 看全部</option>
							</select>
							<!-- / YEAR JUMPER -->
						</div>
					</div>

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


					 <?php
					 add_button('0', 'purchase_order_add');
					 ?>

				<div class="col-md-12">

    			  <div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
					 <thead>
						 <tr>
							<th class="text-center"><a href="purchase_orders.php?sort=PO_number&dir=ASC<?php echo $add_URL_vars_year; ?>">P.O. number</a></th>
							<th class="text-center"><a href="purchase_orders.php?sort=supplier_ID&dir=ASC<?php echo $add_URL_vars_year; ?>">Supplier</a></th>
							<th class="text-center"><a href="purchase_orders.php?sort=created_date&dir=DESC<?php echo $add_URL_vars_year; ?>">Created Date</a></th>
							<th class="text-center"># Items</th>
							<th class="text-center">Total QTY</th>
							<th class="text-center"># Batches</th>
							<th class="text-center">Actions</th>
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

					  $get_POs_SQL = "SELECT * FROM  `purchase_orders` WHERE `record_status` ='2'" . $add_SQL . $order_by;
					  // echo $get_mats_SQL;

					  $PO_count = 0;

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

							/* ***************  GET SUPPLIER INFO ************************** */

							// now get the record info:
							$get_sups_SQL = "SELECT * FROM `suppliers` WHERE `ID` = " . $PO_supplier_ID;
							// echo $get_sups_SQL;

							$result_get_sups = mysqli_query($con,$get_sups_SQL);

							// while loop
							while($row_get_sup = mysqli_fetch_array($result_get_sups)) {
								$sup_ID = $row_get_sup['ID'];
								$sup_en = $row_get_sup['name_EN'];
								$sup_cn = $row_get_sup['name_CN'];
								$sup_web = $row_get_sup['website'];
								$sup_internal_ID = $row_get_sup['epg_supplier_ID'];
								$sup_status = $row_get_sup['record_status'];
								$sup_part_classification = $row_get_sup['part_classification']; // look up
								$sup_item_supplied = $row_get_sup['items_supplied'];
								$sup_part_type_ID = $row_get_sup['part_type_ID']; // look up
								$sup_certs = $row_get_sup['certifications'];
								$sup_cert_exp_date = $row_get_sup['certification_expiry_date'];
								$sup_evaluation_date = $row_get_sup['evaluation_date'];
								$sup_address_EN = $row_get_sup['address_EN'];
								$sup_address_CN = $row_get_sup['address_CN'];
								$sup_country_ID = $row_get_sup['country_ID']; // look up
								$sup_contact_person = $row_get_sup['contact_person'];
								$sup_mobile_phone = $row_get_sup['mobile_phone'];
								$sup_telephone = $row_get_sup['telephone'];
								$sup_fax = $row_get_sup['fax'];
								$sup_email_1 = $row_get_sup['email_1'];
								$sup_email_2 = $row_get_sup['email_2'];

										// VENDOR CLASSIFICATION BY STATUS:

										$get_sup_status_SQL = "SELECT * FROM `supplier_status` WHERE `status_level` ='" . $sup_status . "'";
										// echo $get_vendor_status_SQL;

										$result_get_sup_status = mysqli_query($con,$get_sup_status_SQL);
										// while loop
										while($row_get_sup_status = mysqli_fetch_array($result_get_sup_status)) {
											$sup_status_ID = $row_get_sup_status['ID'];
											$sup_status_name_EN = $row_get_sup_status['name_EN'];
											$sup_status_name_CN = $row_get_sup_status['name_CN'];
											$sup_status_level = $row_get_sup_status['status_level'];
											$sup_status_description = $row_get_sup_status['status_description'];
											$sup_status_color_code = $row_get_sup_status['color_code'];
											$sup_status_icon = $row_get_sup_status['icon'];
										}



										// GET PART CLASSIFICATION:
										$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $sup_part_classification . "'";
										// echo $get_part_class_SQL;

										$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
										// while loop
										while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
											$part_class_EN = $row_get_part_class['name_EN'];
											$part_class_CN = $row_get_part_class['name_CN'];
											$part_class_description = $row_get_part_class['description'];
											$part_class_color = $row_get_part_class['color'];
										}

							} // end get record WHILE loop

							/* *************** END GET SUPPLIER INFO *********************** */


					  ?>

					  <tr>
					    <td><a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>"><?php echo $PO_number; ?></a></td>
					    <td>
					    	<a href="supplier_view.php?id=<?php echo $sup_ID; ?>">
					    		<?php echo $sup_en; if (($sup_cn!='')&&($sup_cn!='中文名')) { ?> / <?php echo $sup_cn; } ?>
					    	</a>
					    </td>
					    <td class="text-center">
					    	<a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>">
					    		<?php echo date("Y-m-d", strtotime($PO_created_date)); ?>
					    	</a>
					    </td>
					    <td class="text-center"><?php 
					    
					    // 1. get list of P.O. items for this P.O. 
					    
					    $po_items_count 	= 0;
					    $total_qty 			= 0;
                        
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
						<td class="text-center">
						
						<!-- ********************************************************* -->
						<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
						<!-- ********************************************************* -->
						 
						    <a class="modal-with-form btn btn-default" href="#modalForm_<?php echo $row_get_POs['ID']; ?>"><i class="fa fa-gear"></i></a>

							<!-- Modal Form -->
							<div id="modalForm_<?php echo $row_get_POs['ID']; ?>" class="modal-block modal-block-primary mfp-hide">
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
											  <a href="purchase_order_edit.php?id=<?php echo $row_get_POs['ID']; ?>" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a></td>
											  <td>Edit this record</td>
											</tr>
											<tr>
											  <td>DELETE</td>
											  <td><a href="record_delete_do.php?table_name=purchase_orders&src_page=purchase_orders.php&id=<?php echo $row_get_POs['ID']; ?>" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a></td>
											  <td>Delete this record</td>
											</tr>
											<tr>
											  <td>ADD BATCH</td>
											  <td><a href="part_batch_add.php?PO_ID=<?php echo $row_get_POs['ID']; ?>" class="mb-xs mt-xs mr-xs btn btn-success"><i class="fa fa-plus"></i></a></td>
											  <td>Add a batch to P.O. # <?php echo $PO_number; ?></td>
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
					  </tr>

					  <?php

					  $PO_count = $PO_count + 1;

					  } // end while loop
					  ?>
					  </tbody>
					  <tfoot>
						<tr>
							<th class="text-left">TOTAL: <?php echo $PO_count; ?></th>
							<th colspan="6">&nbsp;</th>
						</tr>
					  </tfoot>

					 </table>

					 </div><!-- end of responsive table -->	
					 
					 </div> <!-- end of row -->	
					 
					 <?php
					 add_button('0', 'purchase_order_add');
					 ?>

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
