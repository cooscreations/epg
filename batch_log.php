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

$page_id = 15;

$sort = '';
if (isset($_REQUEST['sort'])){ $sort = $_REQUEST['sort']; }

$sort_dir = 'ASC';
if (isset($_REQUEST['sort_dir'])){ $sort_dir = $_REQUEST['sort_dir']; }

// pull the header and template stuff:
pagehead($page_id);

if (isset($_REQUEST['part_id'])) {
	// we only want to show the batches for 1 part...
	$part_id = $_REQUEST['part_id'];

	// GET THIS PART DETAILS:
	$get_this_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $part_id;
	$result_get_this_part = mysqli_query($con,$get_this_part_SQL);
	// while loop
	while($row_get_this_part = mysqli_fetch_array($result_get_this_part)) {

		// now print each result to a variable:
		$this_part_id = $row_get_this_part['ID'];
		$this_part_code = $row_get_this_part['part_code'];
		$this_part_name_EN = $row_get_this_part['name_EN'];
		$this_part_name_CN = $row_get_this_part['name_CN'];

		$title_add = " for " . $this_part_code . " - " . $this_part_name_EN;
		if (($this_part_name_CN!='')&&($this_part_name_CN!='中文名')) { $title_add .= " / " . $this_part_name_CN; }

	}

}
else {
	$part_id = 0;
	$title_add = "";
}

?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Batch Log<?php echo $title_add; ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><a href="purchase_orders.php">All P.O.s</a></li>
								<li><span>Batch Log</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<?php

							// run notifications function:
							$msg = 0;
							if (isset ( $_REQUEST ['msg'] )) {
								$msg = $_REQUEST ['msg'];
							}
							$action = 0;
							if (isset ( $_REQUEST ['action'] )) {
								$action = $_REQUEST ['action'];
							}
							$change_record_id = 0;
							if (isset ( $_REQUEST ['new_record_id'] )) {
								$change_record_id = $_REQUEST ['new_record_id'];
							}
							$page_record_id = 0;
							if (isset ( $record_id )) {
								$page_record_id = $record_id;
							}

							// now run the function:
							notify_me ( $page_id, $msg, $action, $change_record_id, $page_record_id );
							?>

					<!-- start: page -->

					<div class="row">

					<div class="col-md-12">


					<div class="row">
						<div class="col-md-1">
					 	<a href="part_batch_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-left"><i class="fa fa-plus-square"></i></a>
						 </div>
						<div class="col-md-11">
						<!-- PART JUMPER -->
                            <select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
                              <option value="#" selected="selected">SHOW BATCHES FOR JUST ONE PART:</option>
                              <option value="batch_log.php">View All / 看全部</option>
                              <?php

							$get_j_parts_SQL = "SELECT * FROM `parts`";
					  		// echo $get_parts_SQL;

					  		$result_get_j_parts = mysqli_query($con,$get_j_parts_SQL);
					  		// while loop
					  		while($row_get_j_parts = mysqli_fetch_array($result_get_j_parts)) {

								$j_part_ID = $row_get_j_parts['ID'];
								$j_part_code = $row_get_j_parts['part_code'];
								$j_part_name_EN = $row_get_j_parts['name_EN'];
								$j_part_name_CN = $row_get_j_parts['name_CN'];
								$j_part_description = $row_get_j_parts['description'];
								$j_part_type_ID = $row_get_j_parts['type_ID'];
								$j_part_classification_ID = $row_get_j_parts['classification_ID'];



								// count variants for this purchase order
        						$count_j_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_ID` = " . $j_part_ID;
        						$count_j_batches_query = mysqli_query($con, $count_j_batches_sql);
        						$count_j_batches_row = mysqli_fetch_row($count_j_batches_query);
        						$total_j_batches = $count_j_batches_row[0];

							   ?>
                              <option value="batch_log.php?part_id=<?php echo $j_part_ID; ?>"><?php echo $j_part_code; ?> - <?php echo $j_part_name_EN; if (($j_part_name_CN != '')&&($j_part_name_CN != '中文名')) { ?> / <?php echo $j_part_name_CN; } ?> (<?php echo $total_j_batches; ?> batche<?php if ($total_j_batches != 1) { ?>s<?php } ?>)</option>
                              <?php
							  } // end get part list
							  ?>
                              <option value="batch_log.php">View All / 看全部</option>
                             </select>
                            <!-- / PART JUMPER -->
						</div>
					</div>





					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					  <tr>
					    <th class="text-center">
					    	Batch #
					    	<span class="col_sort pull-right"><?php
					    	// column sort - this should be written better! :-/
					    	if ($sort == 'batch_num') {
					    		if ($sort_dir == 'ASC') { ?><a href="?part_id=<?php echo $part_id; ?>&sort=<?php echo $sort; ?>&sort_dir=DESC"><i class="fa fa-sort-desc"></i></a><? }
					    		else { ?><a href="?part_id=<?php echo $part_id; ?>&sort=<?php echo $sort; ?>&sort_dir=ASC"><i class="fa fa-sort-asc"></i></a><? }
					    	}
					    	else { // show default icon ?><a href="?part_id=<?php echo $part_id; ?>&sort=batch_num&sort_dir=ASC"><i class="fa fa-sort"></i></a><?php } ?>
					    	</span>
					    </th>
					    <th class="text-center">P.O. #
					    	<span class="col_sort pull-right"><?php
					    	// column sort - this should be written better! :-/
					    	if ($sort == 'PO_ID') {
					    		if ($sort_dir == 'ASC') { ?><a href="?part_id=<?php echo $part_id; ?>&sort=<?php echo $sort; ?>&sort_dir=DESC"><i class="fa fa-sort-desc"></i></a><? }
					    		else { ?><a href="?part_id=<?php echo $part_id; ?>&sort=<?php echo $sort; ?>&sort_dir=ASC"><i class="fa fa-sort-asc"></i></a><? }
					    	}
					    	else { // show default icon ?><a href="?part_id=<?php echo $part_id; ?>&sort=PO_ID&sort_dir=ASC"><i class="fa fa-sort"></i></a><?php } ?>
					    	</span>
					    </th>
					    <th class="text-center">P.O. Date</th>
					    <th class="text-center">Part #
					    	<span class="col_sort pull-right"><?php
					    	// column sort - this should be written better! :-/
					    	if ($sort == 'part_ID') {
					    		if ($sort_dir == 'ASC') { ?><a href="?part_id=<?php echo $part_id; ?>&sort=<?php echo $sort; ?>&sort_dir=DESC"><i class="fa fa-sort-desc"></i></a><? }
					    		else { ?><a href="?part_id=<?php echo $part_id; ?>&sort=<?php echo $sort; ?>&sort_dir=ASC"><i class="fa fa-sort-asc"></i></a><? }
					    	}
					    	else { // show default icon ?><a href="?part_id=<?php echo $part_id; ?>&sort=part_ID&sort_dir=ASC"><i class="fa fa-sort"></i></a><?php } ?>
					    	</span>
					    </th>
					    <th class="text-center">Part Rev.</th>
					    <th class="text-center">Part Name / 名字</th>
						<th class="text-center">QTY In</th>
						<th class="text-center">QTY Out</th>
						<th class="text-center">Batch Balance</th>
					  </tr>

					  <!-- START DATASET -->
					  <?php

					  // get batch list
					  
					  $grand_total_in 			= 0; // default
					  $grand_total_out 			= 0; // default
					  $grand_total_remaining 	= 0; // default
					  $total_batches 			= 0; // default
					  $array_total_in_by_rev	= array[]; // BLANK EMPTY ARRAY

					  // SORT IT!

					  if ($sort == '') {
					  	$sort_SQL = " ORDER BY `PO_ID` ASC";
					  }
					  else if ($sort == 'batch_num') {
					  	$sort_SQL = " ORDER BY `batch_number` " . $sort_dir;
					  }
					  else if ($sort == 'PO_ID') {
					  	$sort_SQL = " ORDER BY `PO_ID` " . $sort_dir;
					  }
					  else if ($sort == 'part_ID') {
					  	$sort_SQL = " ORDER BY `part_ID` " . $sort_dir;
					  }

					  // END OF SORT

					  //SHOW 1 PART ONLY:

						$WHERE_SQL = " WHERE `record_status` = '2'";

					  if ($part_id >0) {
						$WHERE_SQL .= " AND `part_ID` = " . $part_id;
					  }

					  // END SHOW 1 PART ONLY

					  $get_batch_SQL = "SELECT * FROM `part_batch`" . $WHERE_SQL . $sort_SQL;
						$result_get_batch = mysqli_query($con,$get_batch_SQL);
						// while loop
						while($row_get_batch = mysqli_fetch_array($result_get_batch)) {

								// now print each record:
								$batch_id = $row_get_batch['ID'];
								$PO_ID = $row_get_batch['PO_ID'];
								$part_ID = $row_get_batch['part_ID'];
								$batch_number = $row_get_batch['batch_number'];
								$part_rev = $row_get_batch['part_rev'];

								// GET PART DETAILS:
								$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $part_ID;
								$result_get_part = mysqli_query($con,$get_part_SQL);
								// while loop
								while($row_get_part = mysqli_fetch_array($result_get_part)) {

									// now print each result to a variable:
									$part_id = $row_get_part['ID'];
									$part_code = $row_get_part['part_code'];
									$part_name_EN = $row_get_part['name_EN'];
									$part_name_CN = $row_get_part['name_CN'];

								}


								// GET P.O. DETAILS:
								$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $PO_ID;
								$result_get_PO = mysqli_query($con,$get_PO_SQL);
								// while loop
								while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

									// now print each record:
									$PO_id = $row_get_PO['ID'];
									$PO_number = $row_get_PO['PO_number'];
									$PO_created_date = $row_get_PO['created_date'];
									$PO_description = $row_get_PO['description'];

								} // end while loop

								// get part revision info:
								$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $part_rev;
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
								
								// UPDATE: Let's calculate total in-bound QTY over all time
								
								$get_in_and_out_totals_SQL = "SELECT sum(`amount_in`), sum(`amount_out`) FROM `part_batch_movement` WHERE `part_batch_ID` = '" . $batch_id . "' AND `record_status` = 2";
								$result_get_in_and_out_totals = mysqli_query($con,$get_in_and_out_totals_SQL);
								// while loop
								while($row_get_in_and_out_totals = mysqli_fetch_array($result_get_in_and_out_totals)) {

									// now print each result:
									$total_qty_in 	= $row_get_in_and_out_totals['sum(`amount_in`)'];
									$total_qty_out 	= $row_get_in_and_out_totals['sum(`amount_out`)'];

								}
								
								// THEN let's calculate how many are in stock at present
								$qty_remaining = $total_qty_in - $total_qty_out;
								
								// UPDATE 2016-09-16 - SHOW A RUNNING TOTAL FOR DIFFERENT REVISIONS
								
								$array_total_in_by_rev_value = $array_total_in_by_rev[$rev_id] + $total_qty_in;
								
								$array_total_in_by_rev = array($rev_id => $array_total_in_by_rev_value);
								
								$stack = array("orange", "banana");
								array_push($stack, "apple", "raspberry");
								print_r($stack);

					// NOW LET'S DO THIS!

					  ?>
					  <tr<?php if ($batch_id == $_REQUEST['new_record_id']) { ?> class="success"<?php } ?>>
					    <td class="text-center"><a href="batch_view.php?id=<?php echo $batch_id; ?>" title="Database Record ID = <?php echo $batch_id; ?>"><?php echo $batch_number; ?></a></td>
					    <td class="text-center"><a href="purchase_order_view.php?id=<?php echo $PO_id; ?>"><?php echo $PO_number; ?></a></td>
					    <td class="text-center"><?php echo substr($PO_created_date, 0, 10); ?></td>
					    <td class="text-center"><a href="part_view.php?id=<?php echo $part_ID; ?>" class="btn btn-info btn-xs" title="View <?php
					    	echo $part_name_EN;
					    	if (($part_name_CN!='')&&($part_name_CN!='中文名')) {
					    		echo " / " . $part_name_CN;
					    	}
					    ?> Part Profile"><?php
					    // now do a quick check to make sure that the batch number (first 5 chars) matches the part code:
					    if (substr($batch_number,0,5)!= $part_code) {
					    	echo '<span class="text-danger" title="Batch Number Does Not Match Part Code!">' . $part_code . '</span>';
					    }
					    else {
					    	echo $part_code;
					    }

					    ?></a></td>
					    <td class="text-center"><span class="btn btn-xs btn-warning" title="Rev. #: <?php echo $rev_id; ?>"><?php echo $rev_number; ?></span></td>
					    <td class="text-center"><a href="part_view.php?id=<?php echo $part_ID; ?>"><?php
					    	echo $part_name_EN;
					    	if (($part_name_CN!='')&&($part_name_CN!='中文名')) {
					    		echo " / " . $part_name_CN;
					    	}
					    ?></a></td>
					    <td class="text-center"><?php echo number_format($total_qty_in); ?></td>
					    <td class="text-center"><?php echo number_format($total_qty_out); ?></td>
					    <td class="text-center"><?php echo number_format($qty_remaining); ?></td>
					  </tr>
					  <?php
					  
					  $grand_total_in 			= $grand_total_in + $total_qty_in;
					  $grand_total_out 			= $grand_total_out + $total_qty_out;
					  $grand_total_remaining 	= $grand_total_remaining + $qty_remaining;
					  // $grand_total_remaining	= $grand_total_in - $grand_total_out; // should be the same as line above! :)
					  $total_batches 			= $total_batches + 1;
					  } // END GET BATCH 'WHILE' LOOP

					  ?>
					  <!-- END DATASET -->

					  <tr>
					    <th colspan="6">TOTAL ENTRIES: <?php echo $total_batches ;?></th>
					    <th class="text-center"><?php echo number_format($grand_total_in); ?></th>
						<th class="text-center"><?php echo number_format($grand_total_out); ?></th>
						<th class="text-center"><?php echo number_format($grand_total_remaining); ?></th>
					  </tr>


					 </table>
					 
					 <?php 
					 
						foreach($array_total_in_by_rev as $in => $in_value) {
							echo "Key=" . $in . ", Value=" . $in_value;
							echo "<br>";
						}
					 
					 
					 ?>
					 
					</div>

					<div class="row">
					  <div class="col-md-12">
					 	<a href="part_batch_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-left"><i class="fa fa-plus-square"></i></a>
					</div>
					 </div>

								<!-- now close the panel -->
								</div>
					</div> <!-- end row! -->

					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
