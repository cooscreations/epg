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

// USED FOR YEAR JUMPER AND FILTERING:
$start_year = 2010;

// pull the header and template stuff:
pagehead($page_id); ?>

<?php

$add_SQL = '';
$add_URL_vars_sort = '';
$add_URL_vars_dir = '';
$add_URL_sup_ID = '';
$add_URL_vars_year = "&year=" . date("Y");
$add_URL_vars_month = '';
$add_URL_vars_order_status = '';
$add_URL_vars_rec = '';
$add_URL_vars_payment_status = '';
$sup_add_SQL = '';
$end_date_filter_year = (date("Y") + 1);
$display_year = date("Y");
$time_search_SQL = '';

if (isset($_REQUEST['sort'])) {
	$add_URL_vars_sort = '&sort=' . $_REQUEST['sort'];
	$order_by = " ORDER BY `" . $_REQUEST['sort'] . "` " . $_REQUEST['dir'] . "";
}
else {
	$order_by = " ORDER BY  `purchase_orders`.`PO_number` DESC";
}

if (isset($_REQUEST['dir'])) {
	$add_URL_vars_dir = '&dir=' . $_REQUEST['dir'];
}

if (isset($_REQUEST['sup_id'])) {
	$add_URL_sup_ID = '&sup_id=' . $_REQUEST['sup_id'];
	$add_SQL .= " AND `supplier_ID` = '" . $_REQUEST['sup_id'] . "'";
}

if (isset($_REQUEST['year'])) {
	if ($_REQUEST['year']!='all') {
		$add_URL_vars_year = '&year=' . $_REQUEST['year'];
		$display_year = $_REQUEST['year'];
		$sup_add_SQL = " AND `created_date` >  '" . $_REQUEST['year'] . "-01-01 00:00:00' AND `created_date` <  '" . ($_REQUEST['year'] + 1) . "-01-01 00:00:00'";
		$add_SQL .= $sup_add_SQL;
		$end_date_filter_year = ($_REQUEST['year'] + 1);
	}
	else {
		$add_URL_vars_year = '&year=all';
		$display_year = 'all';
		$sup_add_SQL = '';
		$add_SQL .= ''; // SHOW ALL YEARS aka DO NOT ADD TIME DATA HERE! (may be specified above in other condition, so use the '.')
	}
}
else {
	if (!isset($_REQUEST['month'])){
		// by default, we will just show this year!
		$sup_add_SQL = " AND `created_date` >  '" . date("Y") . "-01-01 00:00:00' AND `created_date` <  '" . (date("Y") + 1) . "-01-01 00:00:00'";
		$add_SQL .= $sup_add_SQL;
	}
	else { /* MONTH var will handle this */ }
}

if (isset($_REQUEST['order_status'])) {
	$add_SQL .= " AND `order_status` = '" . $_REQUEST['order_status'] . "'";
	$add_URL_vars_order_status = '&order_status=' . $_REQUEST['order_status'];
}

if (isset($_REQUEST['month'])) {
	// first let's set up the full date info:
	
	// EXAMPLE: 2016-01
	
	$year_to_filter = substr($_REQUEST['month'],0,4);
	// echo '<h3>Year To Filter: ' . $year_to_filter . '</h3>';
	
	$month_to_filter = substr($_REQUEST['month'],5,2);
	// echo '<h3>Month To Filter: ' . $month_to_filter . '</h3>';
	
	// now establish the start and end dates:
	
	$start_year = $year_to_filter;
	$start_month = $month_to_filter;
	
	$end_year = $year_to_filter;
	$end_month = $month_to_filter + 1;
	
	if ($end_month == 13) { 
		$end_month = '01';
		$end_year = $year_to_filter + 1;
	}
	
	if ($end_month <= 9) {
		$end_month = '0' . $end_month; // add leading zero
	}
	$sup_add_SQL = " AND `created_date` >  '" . $start_year . "-" . $start_month . "-01 00:00:00' AND `created_date` <  '" . $end_year . "-" . $end_month . "-01 00:00:00'";
	$add_SQL .= $sup_add_SQL;
	$order_by = ' ORDER BY `created_date` ASC';
	$add_URL_vars_month = '&month=' . $_REQUEST['month'] . '';
	$add_URL_vars_year = ''; // THIS CAN BE REMOVED AS THE ONE ABOVE NEGATES IT
}

if (isset($_REQUEST['rec'])) {
	$add_URL_vars_rec = '&rec=' . $_REQUEST['rec'];
	
	if ($_REQUEST['rec'] == 0) {
		// not received:
		$add_SQL .= " AND `date_delivered` = '0000-00-00 00:00:00'";
		$sup_add_SQL .= " AND `date_delivered` = '0000-00-00 00:00:00'";
	}
	else {
		// received:
		$add_SQL .= " AND `date_delivered` != '0000-00-00 00:00:00'";
		$sup_add_SQL .= " AND `date_delivered` != '0000-00-00 00:00:00'";
	}
	
}

if (isset($_REQUEST['payment_status'])) {
	$add_SQL .= " AND `payment_status` = '" . $_REQUEST['payment_status'] . "'";
	$add_URL_vars_payment_status = "&payment_status=" . $_REQUEST['payment_status'];
}

// now run it like this:
// echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID;

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
						<div class="col-md-1">
							<a class="btn btn-primary" href="purchase_orders.php"><i class="fa fa-refresh" title="RESET PAGE"></i></a>
						</div>
						<div class="col-md-11">
							<!-- YEAR JUMPER -->

							<select onchange="document.location = this.value" data-plugin-selecttwo class="form-control populate">
								<option value="#" selected="selected">SELECT A YEAR / 选一年:</option>
								<option value="purchase_orders.php?year=all<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>">View All / 看全部</option>

								<?php
									// $start_year = 2010; THIS IS NOW SPECIFIED ABOVE AS IT'S ALSO USED FOR FILTERING
									$loop_year = $start_year;

									while ($loop_year <= date("Y")) {
										?>
								<option value="purchase_orders.php?year=<?php echo $loop_year; ?><?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>"<?php if ($loop_year == $_REQUEST['year']) { ?> selected="selected"<?php } ?>>SHOW POs FOR <?php echo $loop_year; ?> 的订单</option>
										<?
										$loop_year = $loop_year + 1;
									}
								?>
								<option value="purchase_orders.php?year=all<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>">View All / 看全部</option>
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
							<th class="text-center"><i class="fa fa-cog" title="Actions"></i></th>
							<th class="text-center">
								<a href="purchase_orders.php?sort=PO_number&dir=ASC<?php echo $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>">
								  P.O. number
								</a>
								<br />
								<!-- PURCHASE ORDER JUMPER -->
								<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
								  <option value="#" selected="selected">Filter:</option>
								  <option value="purchase_orders.php">View All / 看全部</option>
								  <?php

								$get_j_POs_SQL = "SELECT `ID`, `PO_number` FROM `purchase_orders` WHERE `record_status` = '2'";
								// echo $get_j_POs_SQL;

								$result_get_j_POs = mysqli_query($con,$get_j_POs_SQL);
								// while loop
								while($row_get_j_POs = mysqli_fetch_array($result_get_j_POs)) {
									$j_PO_ID = $row_get_j_POs['ID'];
									$j_PO_number = $row_get_j_POs['PO_number'];

								   ?>
								  <option value="purchase_order_view.php?id=<?php echo $j_PO_ID; ?>"><?php echo $j_PO_number; ?></option>
								  <?php
								  } // end get part list
								  ?>
								  <option value="purchase_orders.php">View All / 看全部</option>
								 </select>
								<!-- / PURCHASE ORDER JUMPER -->	
							</th>
							<th class="text-center">
								<a href="purchase_orders.php?sort=created_date&dir=DESC<?php echo $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>">Created Date</a>
								<br />
								<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
									<option value="#" selected="selected">Filter:</option>
										<option value="purchase_orders.php?1<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_sup_ID . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>">Clear This Filter</option>
										<option value="purchase_orders.php?1<?php echo $add_URL_vars_year; ?>">Clear All Filters</option>
										<?php 
										
										if ( ( $display_year!='' ) && ( $display_year!='all' ) ) {
											$filter_year = $display_year;
										}
										else {
											$filter_year = $start_year; // this is specified above for the entire document
										}
										
										while ( $filter_year <= $end_date_filter_year ) {
											// now set the month:
											$filter_month = 1;
										
											while ($filter_month <= 12) {
										
												if ($filter_month <= 9) {
													$display_filter_month = '0' . $filter_month;
												}
												else {
													$display_filter_month = $filter_month;
												}
										
												?>
												<option value="purchase_orders.php?month=<?php echo $filter_year; ?>-<?php echo $display_filter_month; ?><?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_sup_ID . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>"<?php if ( $_REQUEST['month'] == $filter_year . '-' .$display_filter_month ) { ?> selected="selected"<?php } ?>><?php echo $filter_year; ?>-<?php echo $display_filter_month ; ?></option>
												<?php
												// loop through the months
												$filter_month = $filter_month + 1;
											} 
											// loop through the years
											$filter_year = $filter_year + 1;
										}
									
										/*
									
									
										// REVERSE THE DISPLAY ORDER, just for fun:
									
										$filter_year = date("Y"); // this is specified above for the entire document
										while ($filter_year >= $start_year) {
											// now set the month:
											$filter_month = 12;
										
											while ($filter_month >= 1) {
										
												if ($filter_month <= 9) {
													$display_filter_month = '0' . $filter_month;
												}
												else {
													$display_filter_month = $filter_month;
												}
										
												?>
												<option value="purchase_orders.php?month=<?php echo $filter_year; ?>-<?php echo $display_filter_month; ?><?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_sup_ID . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>"<?php if ( $_REQUEST['month'] == $filter_year . '-' .$display_filter_month ) { ?> selected="selected"<?php } ?>><?php echo $filter_year; ?>-<?php echo $display_filter_month ; ?></option>
												<?php
												// loop through the months
												$filter_month = $filter_month - 1;
											} 
											// loop through the years
											$filter_year = $filter_year - 1;
										}
										*/
										?>
									</select>
							</th>
							<th class="text-center">
								Description
							</th>
							<th class="text-center">
								<a href="purchase_orders.php?sort=supplier_ID&dir=ASC<?php echo $add_URL_vars_year . $add_URL_vars_month . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>">Supplier</a><br />
								<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
									<option value="#" selected="selected">Filter:</option>
									<option value="purchase_orders.php?1<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_vars_order_status . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>">Clear This Filter</option>
									<option value="purchase_orders.php?1<?php echo $add_URL_vars_year; ?>">Clear All Filters</option>
									<?php 
									$get_sup_list_SQL = "SELECT * FROM `suppliers` WHERE `record_status` = '2'";
									$result_sup_list = mysqli_query($con,$get_sup_list_SQL);
									// while loop
									while($row_sup_list = mysqli_fetch_array($result_sup_list)) {

											// now print each record:
											$sup_list_id 				= $row_sup_list['ID'];
											$sup_list_epg_supplier_ID 	= $row_sup_list['epg_supplier_ID'];
											$sup_list_name_EN 			= $row_sup_list['name_EN'];
											$sup_list_name_CN 			= $row_sup_list['name_CN'];
											$sup_list_supplier_status 	= $row_sup_list['supplier_status'];

							
											// now count POs for this vendor:
											
											$total_j_sup_POs = 0;
							
											// count POs for this vendor:
											$count_j_sup_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `supplier_ID` = '" . $sup_list_id . "'" . $sup_add_SQL;
											echo "<h1>SQL here: " . $count_j_sup_POs_sql . "</h1>";
											$count_j_sup_POs_query = mysqli_query($con, $count_j_sup_POs_sql);
											$count_j_sup_POs_row = mysqli_fetch_row($count_j_sup_POs_query);
											$total_j_sup_POs = $count_j_sup_POs_row[0];
											
											$total_j_sup_GT_POs = 0;
											
											// if there is a filter on this count, let's also grab the grand totals:
											if ($add_SQL!='') {
												// count POs GRAND TOTAL (GT) for this vendor:
												$count_j_sup_GT_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `supplier_ID` = '" . $sup_list_id . "'";
												$count_j_sup_GT_POs_query = mysqli_query($con, $count_j_sup_GT_POs_sql);
												$count_j_sup_GT_POs_row = mysqli_fetch_row($count_j_sup_GT_POs_query);
												$total_j_sup_GT_POs = $count_j_sup_GT_POs_row[0];
											}
											
											// UPDATE - I'm not even going to bother showing records with 0 value:
											
											if ( ( $total_j_sup_GT_POs != 0 ) && ( $add_SQL!='' ) ) {
												?>
												<option value="purchase_orders.php?sup_id=<?php echo $sup_list_id; echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_vars_order_status . $add_URL_vars_rec; ?>"<?php if ($_REQUEST['sup_id'] == $sup_list_id) { ?> selected="selected"<?php } ?>><?php 
					
													echo $sup_list_name_EN; 
													
													if (($sup_list_name_CN!='')&&($sup_list_name_CN!='中文名')) { 
														echo ' / ' . $sup_list_name_CN;
													}
													
													echo '&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;&nbsp;';
													
													if ( ( $total_j_sup_GT_POs != 0 ) && ( $total_j_sup_GT_POs!= $total_j_sup_POs ) ) { 
														echo $display_year . ': ' . $total_j_sup_POs . ']&nbsp;&nbsp;&nbsp;[Total: ' . $total_j_sup_GT_POs . ' order';
														if ($total_j_sup_GT_POs!=1) { echo 's'; }
													} 
													else {
														echo $total_j_sup_POs . ' order';
														if ($total_j_sup_POs != 1) { echo 's'; }
													}
													
													echo '&nbsp;&nbsp;]';
													
													?>
												</option>
												<?php
											} // end of show > 0 results only
									} // end of while loop
									?>
								</select>
								</th>
								<th class="text-center"># Items</th>
								<th class="text-center">Total QTY</th>
								<th class="text-center"># Batches</th>
								<th class="text-center">
									Order Status
									<br />
									<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
										<option value="#" selected="selected">Filter:</option>
										<option value="purchase_orders.php?1<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>">Clear This Filter</option>
										<option value="purchase_orders.php?1<?php echo $add_URL_vars_year; ?>">Clear All Filters</option>
										<?php 
										
										
										// now count POs by status:
										
										$total_status_0_POs = 0; // OBSOLETE
										$total_status_1_POs = 0; // OPEN
										$total_status_2_POs = 0; // COMPLETE
										
										$count_status_0_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `order_status` = '0'" . $sup_add_SQL;
										// echo "<h1>SQL here: " . $count_status_0_POs_sql . "</h1>";
										$count_status_0_POs_query = mysqli_query($con, $count_status_0_POs_sql);
										$count_status_0_POs_row = mysqli_fetch_row($count_status_0_POs_query);
										$total_status_0_POs = $count_status_0_POs_row[0];
										
										$count_status_1_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `order_status` = '1'" . $sup_add_SQL;
										// echo "<h1>SQL here: " . $count_status_1_POs_sql . "</h1>";
										$count_status_1_POs_query = mysqli_query($con, $count_status_1_POs_sql);
										$count_status_1_POs_row = mysqli_fetch_row($count_status_1_POs_query);
										$total_status_1_POs = $count_status_1_POs_row[0];
										
										$count_status_2_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `order_status` = '2'" . $sup_add_SQL;
										// echo "<h1>SQL here: " . $count_status_2_POs_sql . "</h1>";
										$count_status_2_POs_query = mysqli_query($con, $count_status_2_POs_sql);
										$count_status_2_POs_row = mysqli_fetch_row($count_status_2_POs_query);
										$total_status_2_POs = $count_status_2_POs_row[0];
										
										?>
										<option value="purchase_orders.php?order_status=0<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>"<?php if ( $_REQUEST['order_status'] == 0 ) { ?> selected="selected"<?php } ?>>OBSOLETE (<?php echo $total_status_0_POs; ?>)</option>
										<option value="purchase_orders.php?order_status=1<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>"<?php if ( $_REQUEST['order_status'] == 1 ) { ?> selected="selected"<?php } ?>>OPEN (<?php echo $total_status_1_POs; ?>)</option>
										<option value="purchase_orders.php?order_status=2<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_rec . $add_URL_vars_payment_status; ?>"<?php if ( $_REQUEST['order_status'] == 2 ) { ?> selected="selected"<?php } ?>>COMPLETE (<?php echo $total_status_2_POs; ?>)</option>
									</select>
								</th>
								<th class="text-center">
									Goods Received?
									<br />
									<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
										<option value="#" selected="selected">Filter:</option>
										<option value="purchase_orders.php?1<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_payment_status; ?>">Clear This Filter</option>
										<option value="purchase_orders.php?1<?php echo $add_URL_vars_year; ?>">Clear All Filters</option>
										<?php 
										
										
										// now count POs by status:
										
										$total_rec_0_POs = 0; // NOT RECEIVED
										$total_rec_1_POs = 0; // RECEIVED
										
										$count_rec_0_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `date_delivered` = '0000-00-00 00:00:00'" . $add_SQL;
										// echo "<h1>SQL here: " . $count_rec_0_POs_sql . "</h1>";
										$count_rec_0_POs_query = mysqli_query($con, $count_rec_0_POs_sql);
										$count_rec_0_POs_row = mysqli_fetch_row($count_rec_0_POs_query);
										$total_rec_0_POs = $count_rec_0_POs_row[0];
										
										$count_rec_1_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `date_delivered` != '0000-00-00 00:00:00'" . $add_SQL;
										// echo "<h1>SQL here: " . $count_rec_1_POs_sql . "</h1>";
										$count_rec_1_POs_query = mysqli_query($con, $count_rec_1_POs_sql);
										$count_rec_1_POs_row = mysqli_fetch_row($count_rec_1_POs_query);
										$total_rec_1_POs = $count_rec_1_POs_row[0];
										
										?>
										<option value="purchase_orders.php?rec=0<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_payment_status; ?>"<?php if ( $_REQUEST['rec'] == 0 ) { ?> selected="selected"<?php } ?>>Not Received (<?php echo $total_rec_0_POs; ?>)</option>
										<option value="purchase_orders.php?rec=1<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_payment_status; ?>"<?php if ( $_REQUEST['rec'] == 1 ) { ?> selected="selected"<?php } ?>>Received (<?php echo $total_rec_1_POs; ?>)</option>
									</select>
								</th>
								<th class="text-center">
									Payment Status
									<br />
									<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
										<option value="#" selected="selected">Filter:</option>
										<option value="purchase_orders.php?1<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_rec; ?>">Clear This Filter</option>
										<option value="purchase_orders.php?1<?php echo $add_URL_vars_year; ?>">Clear All Filters</option>
										<?php 
										
										
										// now count POs by status:
										
										$total_payment_status_0_POs = 0; // NOT PAID
										$total_payment_status_1_POs = 0; // PENDING
										$total_payment_status_2_POs = 0; // PAID
										
										$count_payment_status_0_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `payment_status` = '0'" . $add_SQL;
										// echo "<h1>SQL here: " . $count_payment_status_0_POs_sql . "</h1>";
										$count_payment_status_0_POs_query = mysqli_query($con, $count_payment_status_0_POs_sql);
										$count_payment_status_0_POs_row = mysqli_fetch_row($count_payment_status_0_POs_query);
										$total_payment_status_0_POs = $count_payment_status_0_POs_row[0];
										
										$count_payment_status_1_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `payment_status` = '1'" . $add_SQL;
										// echo "<h1>SQL here: " . $count_payment_status_1_POs_sql . "</h1>";
										$count_payment_status_1_POs_query = mysqli_query($con, $count_payment_status_1_POs_sql);
										$count_payment_status_1_POs_row = mysqli_fetch_row($count_payment_status_1_POs_query);
										$total_payment_status_1_POs = $count_payment_status_1_POs_row[0];
										
										$count_payment_status_2_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE `record_status` = '2' AND `payment_status` = '2'" . $add_SQL;
										// echo "<h1>SQL here: " . $count_payment_status_2_POs_sql . "</h1>";
										$count_payment_status_2_POs_query = mysqli_query($con, $count_payment_status_2_POs_sql);
										$count_payment_status_2_POs_row = mysqli_fetch_row($count_payment_status_2_POs_query);
										$total_payment_status_2_POs = $count_payment_status_2_POs_row[0];
										
										?>
										<option value="purchase_orders.php?payment_status=0<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_rec; ?>"<?php if ( $_REQUEST['payment_status'] == 0 ) { ?> selected="selected"<?php } ?>>NOT PAID (<?php echo $total_payment_status_0_POs; ?>)</option>
										<option value="purchase_orders.php?payment_status=1<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_rec; ?>"<?php if ( $_REQUEST['payment_status'] == 1 ) { ?> selected="selected"<?php } ?>>PENDING (<?php echo $total_payment_status_1_POs; ?>)</option>
										<option value="purchase_orders.php?payment_status=2<?php echo $add_URL_vars_sort . $add_URL_vars_dir . $add_URL_vars_year . $add_URL_vars_month . $add_URL_sup_ID . $add_URL_vars_rec; ?>"<?php if ( $_REQUEST['payment_status'] == 2 ) { ?> selected="selected"<?php } ?>>PAID (<?php echo $total_payment_status_2_POs; ?>)</option>
									</select></th>
								<th class="text-center">Currency</th>
						</tr>
					  </thead>
					  <tbody>

					  <?php
					  
					  // VARIABLES ARE NOW SPECIFIED RIGHT AT THE TOP OF THIS PAGE! ^_^

					  $get_POs_SQL = "SELECT * FROM `purchase_orders` WHERE `record_status` ='2'" . $add_SQL . $order_by;
					  echo '<!-- MASTER LIST SQL IS ' . $get_POs_SQL . '-->';

					  $PO_count = 0;

					  $result_get_POs = mysqli_query($con,$get_POs_SQL);
					  // while loop
					  while($row_get_POs = mysqli_fetch_array($result_get_POs)) {

					  		// now assign the results to the vars
							$PO_ID 							= $row_get_POs['ID'];
							$PO_number 						= $row_get_POs['PO_number'];
							$PO_created_date 				= $row_get_POs['created_date'];
							$PO_description 				= $row_get_POs['description'];
							$PO_record_status 				= $row_get_POs['record_status'];
							$PO_supplier_ID 				= $row_get_POs['supplier_ID'];
							$PO_created_by 					= $row_get_POs['created_by'];
							$PO_date_needed 				= $row_get_POs['date_needed'];
							$PO_date_delivered 				= $row_get_POs['date_delivered'];
							$PO_approval_status 			= $row_get_POs['approval_status'];
							$PO_payment_status 				= $row_get_POs['payment_status'];
							$PO_completion_status 			= $row_get_POs['completion_status'];
			
							// ADDING NEW VARIABLES AS WE EXPAND THIS PART OF THE SYSTEM:
							$PO_remark 						= $row_get_POs['remark'];
							$PO_approved_by 				= $row_get_POs['approved_by'];
							$PO_approval_date 				= $row_get_POs['approval_date'];
							$PO_include_CoC 				= $row_get_POs['include_CoC'];
							$PO_date_confirmed 				= $row_get_POs['date_confirmed'];
							$PO_ship_via 					= $row_get_POs['ship_via'];
							$PO_special_reqs 				= $row_get_POs['special_reqs'];
							$PO_related_standards 			= $row_get_POs['related_standards'];
							$PO_special_contracts 			= $row_get_POs['special_contracts'];
							$PO_qualification_personnel 	= $row_get_POs['qualification_personnel'];
							$PO_QMS_reqs 					= $row_get_POs['QMS_reqs'];
							$PO_local_location_ID 			= $row_get_POs['local_location_ID'];
							$PO_HQ_location_ID 				= $row_get_POs['HQ_location_ID'];
							$PO_ship_to_location_ID 		= $row_get_POs['ship_to_location_ID'];
							$PO_default_currency 			= $row_get_POs['default_currency'];
							$PO_default_currency_rate 		= $row_get_POs['default_currency_rate'];
							$PO_order_status 				= $row_get_POs['order_status'];
							
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
					    <td><a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>"><?php echo $PO_number; ?></a></td>
					    <td class="text-center">
					    	<a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>">
					    		<?php echo date("Y-m-d", strtotime($PO_created_date)); ?>
					    	</a>
					    </td>
					    <td>
					    	<?php echo $PO_description; ?>
					    </td>
					    <td>
					    	<a href="supplier_view.php?id=<?php echo $sup_ID; ?>">
					    		<?php echo $sup_en; if (($sup_cn!='')&&($sup_cn!='中文名')) { echo '<br />' . $sup_cn; } ?>
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
					    <td class="text-center"><?php 
					    
					    	if ($PO_order_status == 0) {
					    		// OBSOLETE
					    		?>
					    		<span class="btn btn-warning btn-xs"><i class="fa fa-times" title="OBSOLETE"></i></span>
					    		<?php
					    	}
					    	else if ($PO_order_status == 1) {
					    		// OPEN
					    		?>
					    		<span class="btn btn-warning btn-xs"><i class="fa fa-question" title="OPEN"></i></span>
					    		<?php
					    	}
					    	else if ($PO_order_status == 2) {
					    		// COMPLETE
					    		?>
					    		<span class="btn btn-success btn-xs"><i class="fa fa-check" title="COMPLETE"></i></span>
					    		<?php
					    	}
					    
					    
					    ?></td>
					    <td class="text-center"><?php 
					    if (($PO_date_delivered !='')&&($PO_date_delivered!="0000-00-00 00:00:00")) { 
					    	?>
					    	<span class="btn btn-success btn-xs"><i class="fa fa-check" title="YES"></i></span>
					    	<?php
					    } 
					    else {
					    	?>
					    	<span class="btn btn-danger btn-xs"><i class="fa fa-times" title="NO"></i></span>
					    	<?php
					    }
					    ?></td>
					    <td class="text-center"><?php payment_status($PO_payment_status); ?></td>
					    <td class="text-center"><?php 
					    
					    	echo $PO_default_currency_symbol;
					    	echo $PO_default_currency_abbreviation; 
					    	
					    ?></td>
					  </tr>

					  <?php

					  $PO_count = $PO_count + 1;

					  } // end while loop
					  ?>
					  </tbody>
					  <tfoot>
						<tr>
							<th colspan="12" class="text-left">TOTAL: <?php echo $PO_count; ?></th>
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
