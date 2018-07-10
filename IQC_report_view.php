<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */     session_start ();     /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
//   now check the user is OK to view this page  //
/* /////// require ('page_access.php');  /*/////*/
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

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:
if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: IQC_reports.php?msg=NG&action=view&error=no_id");
	exit();
}

// OK! This is an IQC report page... let's get the info!
$get_IQC_report_SQL = "SELECT * FROM `IQC_report` WHERE `ID` = '" . $record_id . "' AND `record_status` = '2'";
$result_get_IQC_report = mysqli_query($con,$get_IQC_report_SQL);
// while loop
while($row_get_IQC_report = mysqli_fetch_array($result_get_IQC_report)) {

	// now print each record:
	$IQC_report_id 					= $row_get_IQC_report['ID'];
	$IQC_report_report_num 			=  $row_get_IQC_report['IQC_report_num'];
	$IQC_report_batch_ID 			=  $row_get_IQC_report['batch_ID'];
	$IQC_report_remarks 			=  $row_get_IQC_report['remarks'];
	$IQC_report_test_result 		=  $row_get_IQC_report['test_result'];
	$IQC_report_NCR_num 			=  $row_get_IQC_report['NCR_num'];
	$IQC_report_reviewer_ID 		=  $row_get_IQC_report['reviewer_ID'];
	$IQC_report_review_date 		=  $row_get_IQC_report['review_date'];
	$IQC_report_inspector_ID 		=  $row_get_IQC_report['inspector_ID'];
	$IQC_report_inspection_date 	=  $row_get_IQC_report['inspection_date'];
	$IQC_report_record_status 		=  $row_get_IQC_report['record_status'];
}


$get_batch_SQL = "SELECT * FROM  `part_batch` WHERE `ID` = " . $IQC_report_batch_ID;
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

if ($batch_id == '') { // we found an ID but it returned no results from the database?!
	// no batch found - check for a PO to redirect them to!
	if (isset($_REQUEST['PO_ID'])) {
		header("Location: purchase_order_view.php?id=" . $_REQUEST['PO_ID'] . "&msg=NG&action=view&error=no_id");
		exit();
	}
	else {
		// otherwise just redirect to the batch log list
		header("Location: batch_log.php?msg=NG&action=view&error=no_id");
		exit();
	}
}



// pull the header and template stuff:
pagehead();
?>
    <!-- start: page -->
    
    	<div class="row">
    	    <div class="col-md-4">

						<?php
						// now run the admin bar function:
						admin_bar('IQC_report');
						?>
			</div>
			<div class="col-md-8">
				<!-- Report JUMPER -->
				<select onchange="document.location = this.value" data-plugin-selecttwo class="form-control populate">
					<option value="#" selected="selected">JUMP TO ANOTHER REPORT / 看别的:</option>
					<option value="IQC_reports.php">View All / 看全部</option>
					<?php

					$get_j_IQC_report_SQL = "SELECT * FROM `IQC_report` WHERE `record_status` = '2'";
					// echo $get_j_IQC_report_SQL;

					$result_get_j_IQC_report = mysqli_query($con,$get_j_IQC_report_SQL);
								// while loop
					while($row_get_j_IQC_report = mysqli_fetch_array($result_get_j_IQC_report)) {

						$j_IQC_report_ID = $row_get_j_IQC_report['ID'];
						$j_IQC_report_report_num = $row_get_j_IQC_report['IQC_report_num'];
						$j_IQC_report_batch_ID = $row_get_j_IQC_report['batch_ID'];
						$j_IQC_report_remarks = $row_get_j_IQC_report['remarks'];
						$j_IQC_report_test_result = $row_get_j_IQC_report['test_result'];
						$j_IQC_report_NCR_num = $row_get_j_IQC_report['NCR_num'];
						$j_IQC_report_reviewer_ID = $row_get_j_IQC_report['reviewer_ID'];
						$j_IQC_report_review_date = $row_get_j_IQC_report['review_date'];
						$j_IQC_report_inspector_ID = $row_get_j_IQC_report['inspector_ID'];
						$j_IQC_report_inspection_date = $row_get_j_IQC_report['inspection_date'];
						$j_IQC_report_record_status = $row_get_j_IQC_report['record_status']; // should be 2!

								   ?>
					<option value="IQC_report_view.php?id=<?php echo $j_IQC_report_ID; ?>">
						<?php echo $j_IQC_report_report_num; ?>
					</option>
					<?php
								  } // end get IQC report list
								  ?>
					<option value="IQC_reports.php">View All / 看全部</option>
				</select>
				<!-- / report JUMPER -->
			</div>
		</div>
    
    <!-- START PANEL - SUPPLIER INFORMATION -->
		<section class="panel">
			<header class="panel-heading">
				<div class="panel-actions">
					<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
				</div>

				<h2 class="panel-title">
					<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
					<span class="va-middle">Basic Details</span>
				</h2>
			</header>
			<div class="panel-body">
				<div class="content">
	
	<div class="table-responsive">
	<table class="table table-bordered table-striped table-condensed mb-none">
	  <tr>
	  
		<th class="text-right">
			Report #:
		</th>
		<td class="text-center">
			<?php echo $IQC_report_report_num; ?>
		</td>
		<th class="text-right">
			Part Name:
		</th>
		<td class="text-center">
			<?php part_name_from_rev($part_rev); ?>
		</td>
		<th class="text-right">
			Part #:
		</th>
		<td class="text-center">
			<?php part_num_from_rev($part_rev); ?>
		</td>
		<th class="col-md-1 text-right">
			Part Rev:
		</th>
		<td class="text-center">
			<?php part_rev($part_rev); ?>
		</td>
	  </tr>
	  
	  
	  <tr>
	  	
		<th class="text-right">
			Supplier:
		</th>
		<td class="text-center" colspan="3">
			<?php get_supplier($PO_supplier_ID, 0); // show name and address in P.O. format, with link to vendor profile ?>
		</td>
		<th class="text-right">
			PO #:
		</th>
		<td class="text-center">
			<a href="purchase_order_view.php?id=<?php echo $PO_id; ?>" title="Click to view the PO record">
				<?php echo $PO_number; ?>
			</a>
		</td>
		<th class="text-right">
			QTY:
		</th>
		<td class="text-center">
			<?php
			// now use earliest record in the DB to find the QTY
				$get_first_batch_qty_SQL = "SELECT * FROM  `part_batch_movement` WHERE `part_batch_ID` = " . $batch_id . " AND `amount_in` > 0 ORDER BY `date` ASC LIMIT 0, 1";
				// echo '<h1>SQL is: ' . $get_first_batch_qty_SQL . '</h1>';
				$result_get_first_batch_qty = mysqli_query($con,$get_first_batch_qty_SQL);


				$movement_in = 0; // (RESET VARIABLE)

				// while loop
				while($row_get_first_batch_qty = mysqli_fetch_array($result_get_first_batch_qty)) {

					// now print each record to a variable:
					$movement_in = $row_get_first_batch_qty['amount_in'];
				}

				if ($movement_in == '') { $movement_in = 0; }
				
			?>

			<a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo number_format($movement_in); ?></a>
		</td>
		
	  </tr>
	  
	  
	  <tr>
	  	
		<th class="text-right">
			Delivery Note:
		</th>
		<td class="text-center" colspan="3">
			?
		</td>
		<th class="text-right">
			Delivery #:
		</th>
		<td class="text-center">
			?
		</td>
		<th class="text-right">
			Batch #:
		</th>
		<td class="text-center">
			<a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo $batch_number; ?></a>
		</td>
		
	  </tr>
	
	
	</table>
	</div>
	
	<!-- now close the panel body -->
    </div>
	</div>
</section>
	<!-- END PANEL #1 -->
    
    
    <!-- PANEL #2 -->
    
    
    <!-- START PANEL - SUPPLIER INFORMATION -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">QC inspection by documents / QC按文件检测</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
			
			<p class="lead">This section is coming soon...</p>
			
			<!-- now close the panel body -->
			</div>
		</div>
	</section>
    <!-- END PANEL #2 -->
    
    
    <!-- PANEL #3 -->
    
    
    <!-- START PANEL - SUPPLIER INFORMATION -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">QC inspection by EPG QC staff / 由EPG QC检测</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
			
			
			<!-- START TABLE BASED ON CRITICAL DIMENSIONS -->
			
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-hover table-condensed mb-none">
			  
				  <tr>
					<th class="text-center">Test item<br />检测项目</th>
					<th class="text-center">AQL</th>
					<th class="text-center" style="width: 5%">Sample QTY<br />取样数</th>
					<th class="text-center" style="width: 5%"><abbr title="Measurement">Q#</abbr><br />测量</th>
					<th class="text-center" style="width: 10%"><abbr title="Specification">Spec.</abbr><br />规格</th>
					<th class="text-center">Inspect Method<br />检测方法</th>
					<th class="text-center" style="width: 10%">Remark<br />备注</th>
					<th class="text-center" style="width: 40%">Inspection Data</th>
					<th class="text-center" style="width: 10%">Inspection Results</th>
				  </tr>
		  	  
					<?php
					
					$get_method_class_list_SQL = "SELECT * FROM `inspection_method_class` WHERE `record_status` = '2'";
					$result_get_method_class_list = mysqli_query($con,$get_method_class_list_SQL);
					// while loop
					while($row_get_method_class_list = mysqli_fetch_array($result_get_method_class_list)) {

						// now print each record:
						$method_class_list_ID 				= $row_get_method_class_list['ID'];
						$method_class_list_name_EN 			= $row_get_method_class_list['name_EN'];
						$method_class_list_name_CN 			= $row_get_method_class_list['name_CN'];
						$method_class_list_record_status 	= $row_get_method_class_list['record_status'];
						$method_class_list_AQL_level 		= $row_get_method_class_list['AQL_level'];
						$method_class_list_sample_level 	= $row_get_method_class_list['sample_level'];
						
						// now let's calculate the sample QTY based on the initial amount of parts rec.
						// $movement_in = first QTY recieved!
						// 2. Now let's establish the sample size RANGE, based on data in the DB and the dimension info:
													
						$get_AQL_letter_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` LIKE '" . $method_class_list_sample_level . "' AND `order_qty_max` > '" . $movement_in .  "' LIMIT 0,1";
						// echo '<h1>SQL: ' . $get_AQL_letter_SQL . '</h1>';
						$result_get_AQL_letter = mysqli_query($con,$get_AQL_letter_SQL);
						// while loop
						while($row_get_AQL_letter = mysqli_fetch_array($result_get_AQL_letter)) {

							// now print each record:
							$AQL_letter_ID = $row_get_AQL_letter['ID'];
							$AQL_letter_AQL_code = $row_get_AQL_letter['AQL_code'];
							$AQL_letter_order_qty_min = $row_get_AQL_letter['order_qty_min'];
							$AQL_letter_order_qty_max = $row_get_AQL_letter['order_qty_max'];
							$AQL_letter_result = $row_get_AQL_letter['AQL_letter_result'];
							
						}
						
						// 3. Now let's get the sample size!
						$get_sample_size_SQL = "SELECT * FROM `AQL_level` WHERE `AQL_level` = '" . $method_class_list_AQL_level . "' AND `letter_code` LIKE '" . $AQL_letter_result . "'";
						// echo '<h1>SAMPLE SIZE SQL: ' . $get_sample_size_SQL . '</h1>';
						$result_get_sample_size = mysqli_query($con,$get_sample_size_SQL);
						// while loop
						while($row_get_sample_size = mysqli_fetch_array($result_get_sample_size)) {

							// now print each record:
							$sample_size_ID 			= $row_get_sample_size['ID'];
							$sample_size_AQL_level 		= $row_get_sample_size['AQL_level'];		// SHOULD HAVE THIS
							$sample_size_fail_max_qty 	= $row_get_sample_size['fail_max_qty'];
							$sample_size_letter_code 	= $row_get_sample_size['letter_code'];		// SHOULD HAVE THIS
							$sample_size_sample_size 	= $row_get_sample_size['sample_size'];
							
						}
						
						$this_class_total_records = 0;
						
						// OK! Now, let's get the underlying inspection methods for each class:
						$get_this_class_method_list_SQL = "SELECT * FROM `inspection_method` WHERE `record_status` = '2' AND `method_class_ID` = '" . $method_class_list_ID . "'";
						$result_get_this_class_method_list = mysqli_query($con,$get_this_class_method_list_SQL);
						// while loop
						while($row_get_this_class_method_list = mysqli_fetch_array($result_get_this_class_method_list)) {

							// now print each record:
							$this_class_method_list_ID 				= $row_get_this_class_method_list['ID'];
							$this_class_method_list_name_EN 		= $row_get_this_class_method_list['name_EN'];
							$this_class_method_list_name_CN 		= $row_get_this_class_method_list['name_CN'];
							$this_class_method_list_description 	= $row_get_this_class_method_list['description'];
							$this_class_method_list_record_status 	= $row_get_this_class_method_list['record_status'];
							$this_class_method_list_method_class_ID = $row_get_this_class_method_list['method_class_ID'];
							
							// now count that parts for THIS PART REV and THIS Method, tallying up the total count for this method class
							// (long-winded!)
							
							// count variants for this purchase part
							$count_records_sql 		= "SELECT COUNT(ID) FROM `part_rev_critical_dimensions` WHERE `part_revision_ID` = '" . $part_rev . "' AND `inspection_method_ID` = '" . $this_class_method_list_ID . "' AND `record_status` = 2";
							// echo "<h1>SQL: " . $count_records_sql . "</h1>";
							$count_records_query 	= mysqli_query($con, $count_records_sql);
							$count_records_row 		= mysqli_fetch_row($count_records_query);
							$total_records 			= $count_records_row[0];
							// echo "<h1>COUNT: " . $total_records . "</h1>";
							
							// now append the total count for this class:
							$this_class_total_records = $this_class_total_records + $total_records;
							
							
						
						} // end get methods for this class list loop
						
						if ($this_class_total_records > 0) {
							// we found a record! Let's show the row!
							?>
							<tr>
								<td rowspan="<?php echo $this_class_total_records; ?>" class="text-center" name="Test Item">
								  <span title="Total Records Found: <?php echo $this_class_total_records; ?>">	
									<?php 
										echo $method_class_list_name_EN;
										if ( ( $method_class_list_name_CN != '' ) && ( $method_class_list_name_CN != '中文名' ) ) {
											echo ' / ' . $method_class_list_name_CN;
										}
									?>
								  </span>
								</td>
								<td rowspan="<?php echo $this_class_total_records; ?>" class="text-center" name="AQL">
								<span class="">
									<?php echo $method_class_list_AQL_level; ?>
							  	</span>
								&nbsp;
								<span class="">
									<?php echo $method_class_list_sample_level; ?>
							  	</span>
								</td>
								<td rowspan="<?php echo $this_class_total_records; ?>" class="text-center" name="Sample QTY">
								  <span title="ONLY <?php echo $sample_size_fail_max_qty; ?> CAN FAIL">
									<?php 
									  echo number_format($sample_size_sample_size,0);
									?>
								  </span>
								</td>
								<?php 
								
								// now let's loop through each inspection method:
								// OK! Now, let's get the underlying inspection methods for each class:
								$this_loop = 0;
								$add_SQL_method_list = "";
								$get_x_class_method_list_SQL = "SELECT * FROM `inspection_method` WHERE `record_status` = '2' AND `method_class_ID` = '" . $method_class_list_ID . "'";
								$result_get_x_class_method_list = mysqli_query($con,$get_x_class_method_list_SQL);
								// while loop
								while($row_get_x_class_method_list = mysqli_fetch_array($result_get_x_class_method_list)) {

									// now print each record:
									$x_class_method_list_ID 				= $row_get_x_class_method_list['ID'];
									$x_class_method_list_name_EN 		= $row_get_x_class_method_list['name_EN'];
									$x_class_method_list_name_CN 		= $row_get_x_class_method_list['name_CN'];
									$x_class_method_list_description 	= $row_get_x_class_method_list['description'];
									$x_class_method_list_record_status 	= $row_get_x_class_method_list['record_status'];
									$x_class_method_list_method_class_ID = $row_get_x_class_method_list['method_class_ID'];
							
									// let's build an add_SQL to get all crit_dims for this method class:
									if ($add_SQL_method_list == '') {
										$add_SQL_method_list .= " AND `inspection_method_ID` = '" . $x_class_method_list_ID . "'";
									}
									else {
										$add_SQL_method_list .= " OR `inspection_method_ID` = '" . $x_class_method_list_ID . "' AND `part_revision_ID` = '" . $part_rev . "'";
									}
							
									// now count that parts for THIS PART REV and THIS Method, tallying up the total count for this method class
									// (long-winded!)
							
									// count variants for this purchase part
									
									$count_x_records_sql 		= "SELECT COUNT(ID) FROM `part_rev_critical_dimensions` WHERE `part_revision_ID` = '" . $part_rev . "' AND `inspection_method_ID` = '" . $x_class_method_list_ID  . "' AND `record_status` = '2'";
									// echo "<h1>SQL: " . $count_x_records_sql . "</h1>";
									$count_x_records_query 	= mysqli_query($con, $count_x_records_sql);
									$count_x_records_row 		= mysqli_fetch_row($count_x_records_query);
									$total_x_records 			= $count_x_records_row[0];
									
								
								} // of RESULTS FOUND loop
									
									// as we're already looping here, I'm going to use the LIMIT option to get the right data... it's a little clunky, but it will work!
									// WITH THIS RESULT SET WE CAN ACTUALLY GET THE INFO!
									$y_loop = 0;
									while ($this_class_total_records > $y_loop) {
										$get_complete_crit_dim_list_SQL = "SELECT * FROM `part_rev_critical_dimensions` WHERE `part_revision_ID` = '" . $part_rev . "' AND `record_status` = '2' " . $add_SQL_method_list . " ORDER BY `part_rev_critical_dimensions`.`drawing_QC_ID` ASC, `part_rev_critical_dimensions`.`inspection_method_ID` ASC LIMIT ".$y_loop.",1";
										// echo '<h1>SQL: ' . $get_complete_crit_dim_list_SQL . '</h1>';
										$y_loop = $y_loop + 1;
									// echo "<h1>SQL IS: " . $get_complete_crit_dim_list_SQL . "</h1>";
									$result_get_complete_crit_dim_list = mysqli_query($con,$get_complete_crit_dim_list_SQL);
									// while loop
									while($row_get_complete_crit_dim_list = mysqli_fetch_array($result_get_complete_crit_dim_list)) {

										// now print each record:
										$complete_crit_dim_list_ID 						= $row_get_complete_crit_dim_list['ID'];
										$complete_crit_dim_list_part_revision_ID 		= $row_get_complete_crit_dim_list['part_revision_ID'];
										$complete_crit_dim_list_drawing_QC_ID 			= $row_get_complete_crit_dim_list['drawing_QC_ID'];
										$complete_crit_dim_list_dimension_type_ID 		= $row_get_complete_crit_dim_list['dimension_type_ID'];
										$complete_crit_dim_list_dimension_minimum		= $row_get_complete_crit_dim_list['dimension_minimum'];
										$complete_crit_dim_list_dimension_maximum 		= $row_get_complete_crit_dim_list['dimension_maximum'];
										$complete_crit_dim_list_specification_notes 	= $row_get_complete_crit_dim_list['specification_notes'];
										$complete_crit_dim_list_inspection_method_ID 	= $row_get_complete_crit_dim_list['inspection_method_ID'];
										$complete_crit_dim_list_inspection_level 		= $row_get_complete_crit_dim_list['inspection_level'];
										$complete_crit_dim_list_AQL_level 				= $row_get_complete_crit_dim_list['AQL_level'];
										$complete_crit_dim_list_record_status 			= $row_get_complete_crit_dim_list['record_status'];
										$complete_crit_dim_list_remarks 				= $row_get_complete_crit_dim_list['remarks'];
										
										// echo '<p>part rev is ' . $complete_crit_dim_list_part_revision_ID . '</p>';
										
										// echo 'GTR: ' . $this_class_total_records . '<br />';
										
										// now get the inspection method info:
										$get_z_class_method_list_SQL = "SELECT * FROM `inspection_method` WHERE `record_status` = '2' AND `ID` = '" . $complete_crit_dim_list_inspection_method_ID . "'";
										$result_get_z_class_method_list = mysqli_query($con,$get_z_class_method_list_SQL);
										// while loop
										while($row_get_z_class_method_list = mysqli_fetch_array($result_get_z_class_method_list)) {

											// now print each record:
											$z_class_method_list_ID 				= $row_get_z_class_method_list['ID'];
											$z_class_method_list_name_EN 			= $row_get_z_class_method_list['name_EN'];
											$z_class_method_list_name_CN 			= $row_get_z_class_method_list['name_CN'];
											$z_class_method_list_description 		= $row_get_z_class_method_list['description'];
											$z_class_method_list_record_status 		= $row_get_z_class_method_list['record_status'];
											$z_class_method_list_method_class_ID 	= $row_get_z_class_method_list['method_class_ID'];
											
										}
										
										// now get the dimension type data:
										$get_this_dimension_type_SQL = "SELECT * FROM `dimension_types` WHERE `ID` = '" . $complete_crit_dim_list_dimension_type_ID . "'";
										$result_get_this_dimension_type = mysqli_query($con,$get_this_dimension_type_SQL);
										// while loop
										while($row_get_this_dimension_type = mysqli_fetch_array($result_get_this_dimension_type)) {

											// now print each record:
											$this_dimension_type_ID 					= $row_get_this_dimension_type['ID'];
											$this_dimension_type_name_EN 				= $row_get_this_dimension_type['name_EN'];
											$this_dimension_type_name_CN 				= $row_get_this_dimension_type['name_CN'];
											$this_dimension_type_symbol 				= $row_get_this_dimension_type['symbol'];
											$this_dimension_type_unit_of_measurement 	= $row_get_this_dimension_type['unit_of_measurement'];
											$this_dimension_type_icon_code			 	= $row_get_this_dimension_type['icon_code'];
										} // end get dimension type loop
										
										
										
										
										// while ($this_class_total_records > $x_loop) {
									
														if ($this_loop != 0) { 
															// start a new row! 
															?>
																<tr>
															<?php 
														}
									
														// now let's get the critical dimension data:
															?>
															<td class="text-center"><span title="ID: <?php echo $complete_crit_dim_list_ID; ?> -- --  Ordered from 1 upwards through the inspection.">Q<?php echo $complete_crit_dim_list_drawing_QC_ID; ?></span></td>
															<td class="text-center"><?php 
															
															if ( ( $complete_crit_dim_list_dimension_minimum == '0.00' ) && ( $complete_crit_dim_list_dimension_maximum == '0.00' ) ) {
															// $this_dimension_type_ID == 4?????
																echo $complete_crit_dim_list_specification_notes;
															}
															else {
																echo $complete_crit_dim_list_dimension_minimum; 
																?> <?php
																echo $this_dimension_type_unit_of_measurement; 
																?><br /> <-> <br /><?php 
																echo $complete_crit_dim_list_dimension_maximum; 
																?> <?php
																echo $this_dimension_type_unit_of_measurement;
															}	
																
																?></td>
															<td class="text-center"><?php 
																echo $z_class_method_list_name_EN; 
																if ( ( $z_class_method_list_name_CN != '' ) && ( $z_class_method_list_name_CN != '中文名' ) ) {
																	echo ' / ' . $z_class_method_list_name_CN;
																}
															?></td>
															<td class="text-center"><?php echo $complete_crit_dim_list_remarks; ?></td>
															<td>
															
															<!-- START RESULTS TABLE: -->
															<div class="table-responsive">
																<table class="table table-bordered table-striped table-hover table-condensed mb-none">
															<?php 
															
															// RESULT LOGIC:
															
															/////////////////////////////////////////////////////////////////////
															// 1. Check if it's a dimension ($this_dimension_type_ID != 4)
															// echo '<h4>METHOD CLASS IS ' . $z_class_method_list_method_class_ID . '</h4>';
															if ($z_class_method_list_method_class_ID != 2) {
																// echo "This is a quantified measurement - go get " . $sample_size_sample_size . " rows of data!";
																
																// now count the number of records:
									
																$count_this_row_records_sql 		= "SELECT COUNT(ID) FROM `IQC_report_results` WHERE `IQC_report_ID` = '" . $record_id . "' AND `crit_dim_ID` = '" . $complete_crit_dim_list_ID . "' AND `record_status` = '2'";
																// echo "<h1>SQL: " . $count_x_records_sql . "</h1>";
																$count_this_row_records_query 		= mysqli_query($con, $count_this_row_records_sql);
																$count_this_row_records_row 		= mysqli_fetch_row($count_this_row_records_query);
																$total_this_row_records 			= $count_this_row_records_row[0];
																
																$this_row_count_score = ( ($total_this_row_records / $sample_size_sample_size ) * 100 );
																
																
																if ($total_this_row_records >= $sample_size_sample_size) {
																	$style_result = "success";
																	$result_msg = 'OK';
																}
																else if ( $total_this_row_records > 0 ) {
																	$style_result = "warning";
																	$result_msg = 'NG';
																}
																else {
																	$style_result = "danger";
																	$result_msg = 'NG';
																}
																// we will move the results to UNDERNEATH the individual dimension results:
																
																if ($total_this_row_records > 0) {
																	// now let's go and get whatever results are available and compare them with the standard:
																	
																	// REMINDER:
																	/*
																	echo $complete_crit_dim_list_dimension_minimum; 
																	?> <?php
																	echo $this_dimension_type_unit_of_measurement; 
																	?> - <?php 
																	echo $complete_crit_dim_list_dimension_maximum; 
																	?> <?php
																	echo $this_dimension_type_unit_of_measurement;
																	*/
																	
																	$this_row_OK_count = 0;
																	$this_row_NG_count = 0;
																	$this_result_number = 0;
																	$total_result_list_count = 1;
																	
																	$get_this_report_this_crit_dim_results_SQL = "SELECT * FROM `IQC_report_results` WHERE `IQC_report_ID` = '" . $record_id . "' AND `crit_dim_ID` = '" . $complete_crit_dim_list_ID . "' AND `record_status` = '2'";
																	$result_get_this_report_this_crit_dim_results = mysqli_query($con,$get_this_report_this_crit_dim_results_SQL);
																	// while loop
																	while($row_get_this_report_this_crit_dim_results = mysqli_fetch_array($result_get_this_report_this_crit_dim_results)) {
																	
																		if ($this_result_number == 0) {
																			?>
																			<!-- <div class="dimension_result_row" style="display: block;"> -->
																			<tr>
																			<?php
																		}

																		// now print each record:
																		$this_report_this_crit_dim_results_ID				= $row_get_this_report_this_crit_dim_results['ID'];
																		// $this_report_this_crit_dim_results_IQC_report_ID = $row_get_this_report_this_crit_dim_results['IQC_report_ID'];		== $record_id
																		// $this_report_this_crit_dim_results_crit_dim_ID 	= $row_get_this_report_this_crit_dim_results['crit_dim_ID'];		== $complete_crit_dim_list_ID
																		$this_report_this_crit_dim_results_test_result 		= $row_get_this_report_this_crit_dim_results['test_result'];
																		$this_report_this_crit_dim_results_date_entered 	= $row_get_this_report_this_crit_dim_results['date_entered'];
																		$this_report_this_crit_dim_results_created_by 		= $row_get_this_report_this_crit_dim_results['created_by'];
																		// $this_report_this_crit_dim_results_record_status 	= $row_get_this_report_this_crit_dim_results['record_status'];  	== 2
																		
																		// *********************************************************************
																		// *********************************************************************
																		// *********************************************************************
																		// *********************************************************************
																		// now let's make the analysis:
																		if ( ( ($this_report_this_crit_dim_results_test_result >= $complete_crit_dim_list_dimension_minimum ) && ( $this_report_this_crit_dim_results_test_result <= $complete_crit_dim_list_dimension_maximum ) ) || ( $this_report_this_crit_dim_results_test_result == 'OK' ) ) {
																			// OK!
																			$style_a_result = "success";
																			$result_a_msg = 'OK';
																			$this_row_OK_count = $this_row_OK_count + 1;
																		}
																		else {
																			// NG!
																			$style_a_result = "danger";
																			$result_a_msg = 'NG';
																			$this_row_NG_count = $this_row_NG_count + 1;
																		}
																		// now print the result:
																		?>
																		<td style="width:10%;" class="text-right">
																		<a class="modal-with-form text-<?php 
																			echo $style_a_result; 
																		?>" title="RESULT: <?php 
																			echo $result_a_msg; 
																		?> - <?php 
																			echo $total_result_list_count; 
																		?> of <?php 
																			echo $total_this_row_records; 
																		?> (DB info: RESULT ID # <?php 
																			echo $this_report_this_crit_dim_results_ID; 
																		?>)" href="#modalEditForm_<?php 
																			echo $this_report_this_crit_dim_results_ID; 
																		?>">
																			<strong>
																				<?php echo $this_report_this_crit_dim_results_test_result; ?>
																			</strong>
																		</a>
																		
																		<!-- STARTING EDIT FORM: -->
																		
																		<div id="modalEditForm_<?php echo $this_report_this_crit_dim_results_ID; ?>" class="modal-block modal-block-primary mfp-hide">
																		  <form class="form-horizontal form-bordered" action="IQC_results_edit_do.php" method="post">
																			<section class="panel">
																				<header class="panel-heading">
																					<h2 class="panel-title">EDIT IQC RESULT: 
																					<?php 
																					  if ( ( $this_report_this_crit_dim_results_test_result == 'OK' ) || ( $this_report_this_crit_dim_results_test_result == 'NG' ) ) {
																						?> OK / NG <?php
																					  }
																					  else {
																					  	echo $complete_crit_dim_list_dimension_minimum . ' ' . $this_dimension_type_unit_of_measurement; ?> - <?php echo $complete_crit_dim_list_dimension_maximum . ' '  .$this_dimension_type_unit_of_measurement; 
																					  }	
																					?>
																					</h2>
																				</header>
																				<div class="panel-body">
																				
																				  <div class="row">
																					<div class="col-sm-1">
																					&nbsp;
																					</div>
																					<div class="col-sm-10">
																					<?php if ( ( $this_report_this_crit_dim_results_test_result == 'OK' ) || ( $this_report_this_crit_dim_results_test_result == 'NG' ) ) {
																						?>
																							<div class="radio-custom radio-success">
																								<input type="radio" id="res_<?php echo $this_report_this_crit_dim_results_ID; ?>_OK" name="ICQ_result_new" value="OK"<?php if ($this_report_this_crit_dim_results_test_result == 'OK') { ?> checked="checked"<?php } ?>>
																								<label for="res_<?php echo $this_report_this_crit_dim_results_ID; ?>_OK">OK</label>
																							</div>																								
																							<div class="radio-custom radio-danger">
																								<input type="radio" id="res_<?php echo $this_report_this_crit_dim_results_ID; ?>_NG" name="ICQ_result_new" value="NG"<?php if ($this_report_this_crit_dim_results_test_result == 'NG') { ?> checked="checked"<?php } ?>>
																								<label for="res_<?php echo $this_report_this_crit_dim_results_ID; ?>_NG">NG</label>
																							</div>
																						<?php
																					}
																					else {
																						?>
																						
																							<input type="text" name="ICQ_result_new" class="form-control" value="<?php echo $this_report_this_crit_dim_results_test_result; ?>" />
																						
																						<?php 
																					}
																					?>
																					</div>
																					<div class="col-sm-1">
																					&nbsp;
																					</div>
																				  </div>
																						
																				
																				 <input type="hidden" name="IQC_report_result_ID" value="<?php echo $this_report_this_crit_dim_results_ID; ?>" />
																				 <input type="hidden" name="IQC_report_result_old" value="<?php echo $this_report_this_crit_dim_results_test_result; ?>" />
																				 <input type="hidden" name="IQC_report_result_old_OK_NG" value="<?php echo $result_a_msg; ?>" />
																				 <input type="hidden" name="IQC_report_ID" value="<?php echo $record_id; ?>" />
																				  
																				</div>
																				<footer class="panel-footer">
																					<div class="row">
																						<div class="col-md-12 text-right">
																							<a href="record_delete_do.php?table_name=IQC_report_results&src_page=IQC_report_view.php&id=<?php echo $record_id; ?>" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
																							<button class="btn btn-warning modal-dismiss"><i class="fa fa-times"></i> Cancel</button>
																							<button class="btn btn-success" action="submit"><i class="fa fa-send"></i> Submit</button>
																						</div>
																					</div>
																				</footer>
																			</section>
																		  </form>
																		</div>
																		<!-- END MODAL FORM -->
																		
																		
																		</td>
																		
																		<!-- END EDIT FORM -->
																		<?php
																		// *********************************************************************
																		// *********************************************************************
																		// *********************************************************************
																		// *********************************************************************
																		
																		// we use this to make sure we have 10 records on each line, nice and neat :)
																		$this_result_number = $this_result_number + 1;
																		if ($this_result_number == 10) {
																			?>
																			<!-- close the row div 
																			</div>
																			<br />-->
																			</tr>
																			<?php
																			$this_result_number = 0;
																		}
																		
																		// now append the total count of all results for this row:
																		$total_result_list_count = $total_result_list_count + 1;
																		
																	} // end of get this crit dim result list for this report
																	
																	
																	if ($this_row_NG_count >= $sample_size_fail_max_qty) {
																		// NG!
																		$style_count_result = "danger";
																		$result_count_msg = 'NG';
																	}
																	else {
																		// OK!
																		$style_count_result = "success";
																		$result_count_msg = 'OK';
																	}
																	
																}
																	// now print the result:
																	?>
																	<!-- <br /> -->
																	<tr>
																	<td colspan="10">
																	<?php 
																	
																	if ($total_this_row_records < $sample_size_sample_size) {
																	
																	$add_button_style = 'success';
																	
																	$still_to_go = ( $sample_size_sample_size - $total_this_row_records);
																	
																	
																	/*	DEBUG:
																	// This was removed and incorporated in to the ADD button
																		?>
																		Test <?php echo $still_to_go; ?> more sample<?php if ($still_to_go > 1) { ?>s<?php } ?>. 
																		<?php 
																	*/
																	}
																	else {
																	
																	$add_button_style = 'default';
																	
																	?>
																	
																	<!-- PRINTING SAMPLE QTY CHECK RESULTS: -->
																	 <!-- 
																	<span class="btn btn-xs btn-<?php echo $style_result; ?>" title="RESULT FOR CRIT. DIM. <?php echo $complete_crit_dim_list_ID; ?>, REPORT ID <?php echo $record_id; ?>">
																		<strong>QTY <?php echo $result_msg; ?>: </strong>
																		<?php echo $total_this_row_records . '/' . $sample_size_sample_size . ' (' . $this_row_count_score . '%)'; ?> 
																	</span>
																-->
																	
																	
																	
																	<!-- PRINTING FAIL RATE RESULTS: -->
																	<!-- 
																	<span class="btn btn-xs btn-<?php echo $style_count_result; ?>" title="<?php echo $this_row_NG_count; ?> NG, <?php echo $sample_size_fail_max_qty; ?> ALLOWED">
																		<strong>FAIL RATE <?php echo $result_count_msg; ?>: </strong>
																		<?php echo $this_row_NG_count . '/' . $sample_size_fail_max_qty; ?> 
																	</span>
																	-->
																	
																	
																	
																	<?php
																	
																	}
																	
																	// now provide an add button to let them add more results!
																	
																	if ($still_to_go < 10) {
																		$show_num_results_boxes = 10;
																	}
																	else {
																		$show_num_results_boxes = $still_to_go;
																	}
																	
																	?>
																	
																	<a class="modal-with-form btn btn-<?php echo $add_button_style; ?> btn-xs" href="#modalForm_<?php echo $complete_crit_dim_list_ID; ?>">
																		<i class="fa fa-plus"></i>
																		<?php if ($still_to_go > 1) { 
																			echo ' ' . $still_to_go;
																		}
																		?>
																	</a>

																	<!-- Modal Form -->
																	<div id="modalForm_<?php echo $complete_crit_dim_list_ID; ?>" class="modal-block modal-block-primary mfp-hide">
																	  <form class="form-horizontal form-bordered" action="IQC_results_add_do.php?IQC_report_ID=<?php echo $record_id; ?>&crit_dim_ID=<?php echo $complete_crit_dim_list_ID; ?>" method="post">
																	  	<section class="panel">
																			<header class="panel-heading">
																				<h2 class="panel-title"><?php echo 'Q' . $complete_crit_dim_list_drawing_QC_ID; 
																				
																				if ($z_class_method_list_method_class_ID == 1) {
																					?> (<?php 
																						echo $this_dimension_type_name_EN; 
																						if ( ( $this_dimension_type_name_CN != '' ) && ( $this_dimension_type_name_CN != '中文名' ) ) { echo ' / ' . $this_dimension_type_name_CN; }
																					?>) - <?php echo $complete_crit_dim_list_dimension_minimum . ' ' . $this_dimension_type_unit_of_measurement; ?> - <?php echo $complete_crit_dim_list_dimension_maximum . ' '  .$this_dimension_type_unit_of_measurement; 
																			  	}
																			  	else { // show pass / fail option instead of critical dimensions:
																					?> - PASS / FAIL<?php
																			  	}
																				?></h2>
																			</header>
																			<div class="panel-body">
																				<?php
																				
																				// We will put 5 boxes on each line:
																				$results_per_row = 5;
																				$rows_needed = ceil(( $show_num_results_boxes / $results_per_row ));
																				$this_row = 1;
																				$this_result = 1;
																				
																				// DEBUG:
																				?>
																				<!-- 
																				ROWS NEEDED: <?php echo $rows_needed; ?>
																				CALC: <?php echo $show_num_results_boxes; ?> / <?php echo $results_per_row; ?> ROUNDED UP
																				-->
																				<?php
																				
																				while ($this_row <= $rows_needed) {
																				
																					?>
																					<!-- START ROW # <?php echo $this_row; ?> -->
																					<div class="row">
																						<div class="col-sm-1">
																						&nbsp;
																						</div>
																						<?php 
																						// now run through the results:
																						$this_row_result = 1;
																						while ($this_row_result <= $results_per_row){
																						
																						
																						if ($this_result <= $show_num_results_boxes) {	
																						
																							?>
																							
																						<div class="col-sm-2">
																							<?php 
																							if ($z_class_method_list_method_class_ID == 1) {
																								?>
																								<input type="text" name="IQC_result_<?php echo $this_result; ?>" class="form-control" placeholder="<?php echo $this_result; ?>" />
																								<?php 
																							}
																							else {
																								// let's show the PASS / FAIL check boxes:
																								?>
																								<?php echo $this_result; ?>
																								<!--
																								<script>
																								$('input[type="checkbox"]').on('change', function() {
																									$('input[name="' + this.name + '"]').not(this).prop('checked', false);
																								});
																								</script>
																								
																								<div class="checkbox-custom checkbox-default hidden" style="display: hidden;">
																									<input type="checkbox" name="group<?php echo $this_result; ?>[]" checked="checked" id="check_group_<?php echo $this_result; ?>_id_0" />
																									<label for="check_group_<?php echo $this_result; ?>_id_0">-</label>
																								</div>
																								
																								
																								<div class="row">
																								
																								<div class="checkbox-custom checkbox-success">
																									<input type="checkbox" name="group<?php echo $this_result; ?>[]" id="check_group_<?php echo $this_result; ?>_id_PASS" />
																									<label for="check_group_<?php echo $this_result; ?>_id_1" style="display: hidden;">OK</label>
																								</div>
																								
																								<div class="checkbox-custom checkbox-danger">
																									<input type="checkbox" name="group<?php echo $this_result; ?>[]" id="check_group_<?php echo $this_result; ?>_id_FAIL" />
																									<label for="check_group_<?php echo $this_result; ?>_id_2" style="display: hidden;">NG</label>
																								</div>
																								
																								</div>
																								
																								-->
																								
																								<div class="radio-custom radio-success">
																									<input type="radio" id="res_<?php echo $this_result; ?>_OK" name="IQC_result_<?php echo $this_result; ?>" value="OK">
																									<label for="res_<?php echo $this_result; ?>_OK">OK</label>
																								</div>																								
																								<div class="radio-custom radio-danger">
																									<input type="radio" id="res_<?php echo $this_result; ?>_NG" name="IQC_result_<?php echo $this_result; ?>" value="NG">
																									<label for="res_<?php echo $this_result; ?>_NG">NG</label>
																								</div>
																								
																								
																								
																								<?php
																							}
																							?>
																						</div>
																						
																							<?php
																							
																							}
																							else {
																								?>
																							
																						<div class="col-sm-2">
																							&nbsp;
																						</div>
																						
																								<?php
																							}
																							$this_result = $this_result + 1;
																							$this_row_result = $this_row_result + 1;
																						}
																						?>
																						<div class="col-sm-1">
																							&nbsp;
																						</div>
																					</div>
																					<br /><br />
																					
																					<!-- END ROW # <?php echo $this_row; ?> -->
																					<?php
																				
																					$this_row = $this_row + 1;
																					// DEBUG:
																					?>
																					<!-- 
																					THIS ROW AFTER APPEND: <?php echo $this_row; ?>
																					 -->
																					<?php
																				}
																				?>	
																					
																				  <input type="hidden" name="num_results_needed" value="<?php echo $show_num_results_boxes; ?>" />
																				  <input type="hidden" name="IQC_report_ID" value="<?php echo $record_id; ?>" />
																				  <input type="hidden" name="crit_dim_ID" value="<?php echo $complete_crit_dim_list_ID; ?>" />
																				  <input type="hidden" name="method_class_ID" value="<?php echo $z_class_method_list_method_class_ID; ?>" />
																				  
																			</div>
																			<footer class="panel-footer">
																				<div class="row">
																					<div class="col-md-12 text-right">
																						<button class="btn btn-success" action="submit">Submit</button>
																						<button class="btn btn-default modal-dismiss">Cancel</button>
																					</div>
																				</div>
																			</footer>
																		</section>
																	  </form>
																	</div>
																	<!-- END MODAL FORM -->
																	
																	</td>
																	</tr>
																	
																	<?
																	
																	// *********************************************************************
																	// *********************************************************************
																	// *********************************************************************
																	// *********************************************************************
																	// RESET VARIABLES FOR GOOD MEASURE...
																	
																	/*
																	$this_row_OK_count = 0;
																	$this_row_NG_count = 0;
																	$this_result_number = 0;
																	$total_result_list_count = 1;
																	$style_count_result = "success";
																	$result_count_msg = 'OK';
																	$style_a_result = "success";
																	$result_a_msg = 'OK';
																	$style_result = "success";
																	$result_msg = 'OK';
																	*/
																	
																	
															}
															else {
															
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																	
																	// now count the number of records:
									
																	$count_this_row_records_sql 		= "SELECT COUNT(ID) FROM `IQC_report_results` WHERE `IQC_report_ID` = '" . $record_id . "' AND `crit_dim_ID` = '" . $complete_crit_dim_list_ID . "' AND `record_status` = '2'";
																	// echo "<h1>SQL: " . $count_x_records_sql . "</h1>";
																	$count_this_row_records_query 		= mysqli_query($con, $count_this_row_records_sql);
																	$count_this_row_records_row 		= mysqli_fetch_row($count_this_row_records_query);
																	$total_this_row_records 			= $count_this_row_records_row[0];
																	
																	if ($total_this_row_records == 0) {
																	
																		// ******************************************************************
																		// ******************************************************************
																		// ******************************************************************
																		// ******************************************************************
																		// ******************************************************************
																		// ******************************************************************
																		
																		// NOW GIVE AN ADD FORM FOR THEM TO ADD A RESULT:
																		
																		?><abbr title="Quantity">QTY</abbr>&nbsp;<abbr title="No Good / FAIL">NG</abbr>: 
																		<a class="modal-with-form btn btn-<?php echo $add_button_style; ?> btn-xs" href="#modalForm_<?php echo $complete_crit_dim_list_ID; ?>">
																			<i class="fa fa-plus"></i>
																		</a>

																		<!-- Modal Form -->
																		<div id="modalForm_<?php echo $complete_crit_dim_list_ID; ?>" class="modal-block modal-block-primary mfp-hide">
																		  <form class="form-horizontal form-bordered" action="IQC_results_add_do.php?IQC_report_ID=<?php echo $record_id; ?>&crit_dim_ID=<?php echo $complete_crit_dim_list_ID; ?>" method="post">
																			<section class="panel">
																				<header class="panel-heading">
																					<h2 class="panel-title"><?php echo 'Q' . $complete_crit_dim_list_drawing_QC_ID; ?> - HOW MANY NG?</h2>
																				</header>
																				<div class="panel-body">
																					
																					
																					<!-- START FORM ROW -->
																								<select class="form-control populate" name="ICQ_result_new" id="ICQ_result_new">
																									<?php 
																									// set up a loop for number select:
																									$start_NG_range = 0;
																									$end_RG_range = 50;
																									$this_NG_range = 0;
																						
																									while ($this_NG_range <= $end_RG_range) {
																										?>
																										<option value="<?php echo $this_NG_range; ?>"<?php if ($this_NG_range == 0) { ?> selected="selected"<?php } ?>>
																											<?php echo $this_NG_range; ?>
																										</option>
																										<?php 
																										// append count:
																										$this_NG_range = $this_NG_range + 1;
																									}
																									?>
																								</select>
																					  <!-- END FORM ROW -->
																					
																					
																					  <input type="hidden" name="num_results_needed" value="<?php echo $show_num_results_boxes; ?>" />
																					  <input type="hidden" name="IQC_report_ID" value="<?php echo $record_id; ?>" />
																					  <input type="hidden" name="crit_dim_ID" value="<?php echo $complete_crit_dim_list_ID; ?>" />
																					  <input type="hidden" name="method_class_ID" value="<?php echo $z_class_method_list_method_class_ID; ?>" />
																					  <!-- 
																					  <p class="muted">DEBUG:</p>
																					  <ol>
																					    <li>num_results_needed: <?php echo $show_num_results_boxes; ?></li>
																					    <li>IQC report ID: <?php echo $record_id; ?></li>
																					    <li>crit dim ID: <?php echo $complete_crit_dim_list_ID; ?></li>
																					    <li>method class ID: <?php echo $z_class_method_list_method_class_ID; ?></li>
																					  </ol>
																					  -->
																				  
																				</div>
																				<footer class="panel-footer">
																					<div class="row">
																						<div class="col-md-12 text-right">
																							<button class="btn btn-success" action="submit">Submit</button>
																							<button class="btn btn-default modal-dismiss">Cancel</button>
																						</div>
																					</div>
																				</footer>
																			</section>
																		  </form>
																		</div>
																		<!-- END MODAL FORM -->
																		<?php
																		
																	}
																	else {
																	
																		// debug:
																		// echo "<h3>" . $total_this_row_records . " RESULT(S) FOUND</h3>"; // SHOULD ONLY EVER BE ONE - CREATE OR EDIT!
																		
																		// now get the result!
																		$get_appearance_result_SQL = "SELECT * FROM `IQC_report_results` WHERE `IQC_report_ID` = '" . $record_id . "' AND `crit_dim_ID` = '" . $complete_crit_dim_list_ID . "' AND `record_status` = '2'";
																		$result_get_appearance_result = mysqli_query($con,$get_appearance_result_SQL);
																		// while loop
																		while($row_get_appearance_result = mysqli_fetch_array($result_get_appearance_result)) {

																			// now print each record:
																			$appearance_result_ID					= $row_get_appearance_result['ID'];
																			// $appearance_result_IQC_report_ID 	= $row_get_appearance_result['IQC_report_ID'];		== $record_id
																			// $appearance_result_crit_dim_ID 		= $row_get_appearance_result['crit_dim_ID'];		== $complete_crit_dim_list_ID
																			$appearance_result_test_result 			= $row_get_appearance_result['test_result'];
																			$appearance_result_date_entered 		= $row_get_appearance_result['date_entered'];
																			$appearance_result_created_by 			= $row_get_appearance_result['created_by'];
																			// $appearance_result_record_status 	= $row_get_appearance_result['record_status'];  	== 2
																		
																			
																			// update the NG count for this row:
																			$this_row_NG_count = $appearance_result_test_result;
																		
																			// THE ACTUAL RESULT IS HERE WITH AN EDIT FORM:
																			
																			if ($appearance_result_test_result == 0) {
																				$result_style = 'success';
																			}
																			else {
																				$result_style = 'danger';
																			}
																			
																			?>
																					<a name="result_<?php echo $complete_crit_dim_list_ID; ?>" id="result_<?php echo $complete_crit_dim_list_ID; ?>"></a>
																					<h4 title="ID: <?php echo $appearance_result_ID; ?>" name="result_<?php echo $complete_crit_dim_list_ID; ?>" id="result_<?php echo $complete_crit_dim_list_ID; ?>">RESULT: 
																						<a href="#changeNG_<?php echo $complete_crit_dim_list_ID; ?>" class="modal-with-form text-<?php echo $result_style; ?>">
																							<?php echo $appearance_result_test_result; ?> NG
																						</a>
																					</h4>

																				<!-- Modal Form -->
																				<div id="changeNG_<?php echo $complete_crit_dim_list_ID; ?>" class="modal-block modal-block-primary mfp-hide">
																				  <form class="form-horizontal form-bordered" action="IQC_results_edit_do.php?IQC_report_ID=<?php echo $record_id; ?>&crit_dim_ID=<?php echo $complete_crit_dim_list_ID; ?>" method="post">
																					<section class="panel">
																						<header class="panel-heading">
																							<h2 class="panel-title"><?php echo 'Q' . $complete_crit_dim_list_drawing_QC_ID; ?> - HOW MANY NG?</h2>
																						</header>
																						<div class="panel-body">
																					
																					<p class="text-warning">You are updating an existing record.</p>
																					
																							  <!-- START FORM ROW -->
																										<select class="form-control populate" name="ICQ_result_new" id="ICQ_result_new">
																											<?php 
																											// set up a loop for number select:
																											$start_NG_range = 0;
																											$end_RG_range = 50;
																											$this_NG_range = 0;
																											
																											while ($this_NG_range <= $end_RG_range) {
																												?>
																												<option value="<?php echo $this_NG_range; ?>"<?php if ($this_NG_range == $appearance_result_test_result) { ?> selected="selected"<?php } ?>>
																													<?php echo $this_NG_range; ?>
																												</option>
																												<?php 
																												// append count:
																												$this_NG_range = $this_NG_range + 1;
																											}
																											?>
																										</select>
																							  <!-- END FORM ROW -->
																					
																							  <input type="hidden" name="num_results_needed" value="<?php echo $show_num_results_boxes; ?>" />
																							  <input type="hidden" name="IQC_report_ID" value="<?php echo $record_id; ?>" />
																							  <input type="hidden" name="crit_dim_ID" value="<?php echo $complete_crit_dim_list_ID; ?>" />
																							  <input type="hidden" name="method_class_ID" value="<?php echo $z_class_method_list_method_class_ID; ?>" />
																							 <input type="hidden" name="IQC_report_result_ID" value="<?php echo $appearance_result_ID; ?>" />
																							 <input type="hidden" name="IQC_report_result_old" value="<?php echo $appearance_result_test_result; ?>" />
																					  
																					  		<!-- 
																							  <p class="muted">DEBUG:</p>
																							  <ol>
																								<li>num_results_needed: <?php echo $show_num_results_boxes; ?></li>
																								<li>IQC report ID: <?php echo $record_id; ?></li>
																								<li>crit dim ID: <?php echo $complete_crit_dim_list_ID; ?></li>
																								<li>method class ID: <?php echo $z_class_method_list_method_class_ID; ?></li>
																							  </ol>
																							  -->
																				  
																						</div>
																						<footer class="panel-footer">
																							<div class="row">
																								<div class="col-md-12 text-right">
																									<button class="btn btn-success" action="submit">Submit</button>
																									<button class="btn btn-default modal-dismiss">Cancel</button>
																								</div>
																							</div>
																						</footer>
																					</section>
																				  </form>
																				</div>
																				<!-- END MODAL FORM -->
																				
																				<?php
																			
																			
																			
																			
																			
																			// NOW GET ANY RELATED PHOTOS:
																			$get_result_photos_SQL = "SELECT * FROM `documents` WHERE `lookup_table` LIKE 'IQC_report_results' AND `lookup_ID` = '" . $appearance_result_ID . "' AND `document_category` = '8' AND `record_status` = '2'";
																			$result_get_result_photos = mysqli_query($con,$get_result_photos_SQL);
																			// while loop
																			while($row_get_result_photos = mysqli_fetch_array($result_get_result_photos)) {

																				// now print each record:
																				$result_photos_ID 					= $row_get_result_photos['ID'];
																				$result_photos_name_EN 				= $row_get_result_photos['name_EN'];
																				$result_photos_name_CN 				= $row_get_result_photos['name_CN'];
																				$result_photos_filename 			= $row_get_result_photos['filename'];
																				$result_photos_filetype_ID 			= $row_get_result_photos['filetype_ID'];
																				$result_photos_file_location 		= $row_get_result_photos['file_location'];
																				$result_photos_lookup_table 		= $row_get_result_photos['lookup_table'];
																				$result_photos_lookup_ID 			= $row_get_result_photos['lookup_ID'];
																				$result_photos_document_category 	= $row_get_result_photos['document_category'];
																				$result_photos_record_status 		= $row_get_result_photos['record_status'];
																				$result_photos_created_by 			= $row_get_result_photos['created_by'];
																				$result_photos_date_created 		= $row_get_result_photos['date_created'];
																				$result_photos_filesize_bytes 		= $row_get_result_photos['filesize_bytes'];
																				$result_photos_document_icon 		= $row_get_result_photos['document_icon'];
																				$result_photos_document_remarks 	= $row_get_result_photos['document_remarks'];
																				$result_photos_doc_revision 		= $row_get_result_photos['doc_revision'];
																				
																				?>
																				  <a href="document_view.php?id=<?php echo $result_photos_ID; ?>" title="View this document record" target="_blank">
																					<i class="fa fa-<?php echo $result_photos_document_icon; ?> text-danger"></i>
																				  </a>
																				  &nbsp;
																				<?php
																				
																			} // end get photos for this result...
																			
																			?>
																			<a class="btn btn-success btn-xs" href="upload_file.php?lookup_ID=<?php echo $appearance_result_ID; ?>&table=IQC_report_results"
																			title="Click here to upload a new photo to this result set.">
																				<i class="fa fa-cloud-upload"></i>
																				<i class="fa fa-file-image-o"></i>
																				
																			</a>
																			<?php
																		}
																	
																	} // end results found for 'appearance'
																	
														
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
																// *********************************************************************
															}
															
															?>
															
															</table>
															
															</td>
															<td>
																<span class="" title="<?php echo $this_row_NG_count; ?> NG, <?php echo $sample_size_fail_max_qty; ?> ALLOWED">
																	<strong>NG COUNT: </strong>
																	<?php echo $this_row_NG_count; ?> 
																</span>
															</td>
														  </tr>
														<?php 
														
														// reset var:
														$this_row_NG_count = 0;
														
														
														if ($this_loop == $this_class_total_records) { 
															$this_loop = 0; 
														}
														else {
															$this_loop = $this_loop + 1;
														}
									
										// } // end of x_inspection_methods
										
									  } // END OF Y_LOOP
									}
									
						
						
						} // end of $this_class_total_records > 0
						else {
							// echo 'this_class_total_records = 0';
						}
						
						
					} // end of get method classes
					
						?>
				 </table>
			</div>
			
			<!-- END TABLE BASED ON CRITICAL DIMENSIONS  -->
			
			
			<!-- now close the panel body -->
			</div>
		</div>
	</section>
    <!-- END PANEL #3 -->
    
    
    <!-- PANEL #4 -->
    
    
    <!-- START PANEL - FOOTER -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Remarks &amp; Result</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
			
			<div class="table-responsive">
			<table class="table table-bordered table-striped table-condensed mb-none">
			  
			  <tr>
			  	<th class="text-right">Remarks:</th>
			  	<td colspan="3" class="text-left"><?php echo $IQC_report_remarks; ?></td>
			  </tr>
			  
			  <tr>
			  	<th class="text-right">Result:</th>
			  	<td class="text-center"><?php 
			  	
			  	/*
			  	Reject				1
				Sorting				2
				Special Accepted	3
				Accepted			4
			  	*/
			  	
			  	
			  	if ( $IQC_report_test_result == '4' ) {
			  		// ACCEPTED!
			  		?>
			  		<span class="btn btn-xs btn-success">
			  			<i class="fa fa-check-square"></i>
			  			ACCEPTED
			  			<i class="fa fa-check-square"></i>
			  		</span>
			  		<?php
			  	}
			  	else if ( $IQC_report_test_result == '3' ) {
			  		// ACCEPTED UNDER SPECIAL CIRCUMSTANCES
			  		?>
			  		<span class="btn btn-xs btn-primary">
			  			<i class="fa fa-check-square-o"></i>
			  			ACCEPTED UNDER SPECIAL CASE
			  			<i class="fa fa-check-square-o"></i>
			  		</span>
			  		<?php
			  	}
			  	else if ( $IQC_report_test_result == '2' ) {
			  		// SORTING
			  		?>
			  		<span class="btn btn-xs btn-warning">
			  			<i class="fa fa-recycle"></i>
			  			SORTING
			  			<i class="fa fa-recycle"></i>
			  		</span>
			  		<?php
			  	}
			  	else {
			  		// REJECTED!
			  		?>
			  		<span class="btn btn-xs btn-danger">
			  			<i class="fa fa-times"></i>
			  			REJECTED
			  			<i class="fa fa-times"></i>
			  		</span>
			  		<?php
			  	}
			  	
			  	
			  	
			  	
			  	
			  	?></td>
			  	<th class="text-right">NCR #:</th>
			  	<td class="text-center"><?php echo $IQC_report_NCR_num; ?></td>
			  </tr>
			  
			  <tr>
			  	<th class="text-right">Reviewer:</th>
			  	<td class="text-center"><?php get_creator($IQC_report_reviewer_ID); ?></td>
			  	<th class="text-right">Inspector:</th>
			  	<td class="text-center"><?php get_creator($IQC_report_inspector_ID); ?></td>
			  </tr>
			  
			  <tr>
			  	<th class="text-right">Rev. Date:</th>
			  	<td class="text-center"><?php echo date("Y-m-d", strtotime($IQC_report_review_date)); ?></td>
			  	<th class="text-right">Insp. Date:</th>
			  	<td class="text-center"><?php echo date("Y-m-d", strtotime($IQC_report_inspection_date)); ?></td>
			  </tr>
			  
			  <tr>
			  	<th class="text-right">Signed:</th>
			  	<td class="text-center">&nbsp;</td>
			  	<th class="text-right">Signed:</th>
			  	<td class="text-center">&nbsp;</td>
			  </tr>
			  
			</table>
			
			<!-- now close the panel body -->
			</div>
		</div>
	</section>
    <!-- END PANEL #4 -->
    
    <!-- end: page -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
