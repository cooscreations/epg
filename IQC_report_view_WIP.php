<?php
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

$page_id = 99;

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
	$IQC_report_reiewer_ID 			=  $row_get_IQC_report['reiewer_ID'];
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
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Incoming Quality Control Inspection Report / 进料质量控制检测报告</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                <li><a href="IQC_reports.php">All Reports / 所有报告</a></li>
                <li><span>ICQ Report / IQC报告</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i
                class="fa fa-chevron-left"></i></a>
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
					<th>ITEM</th>
					<th class="text-center">Amount</th>
				  </tr>
				  <tr>
					<th>Initial Incoming QTY:</th>
					<td class="text-right">
					<?php 
					$first_amount_in = $movement_in;
					echo number_format($first_amount_in,0);
					?>
					</td>
				  </tr><?php
						// 1. Firstly, let's cycle through the existing critical dimensions for this part revision:
						$total_crit_dims = 0;
						$this_crit_dim_type_ID = 0;
						$new_dim_type = 1;
						// $first_amount_in is specified above
						
						$get_this_part_rev_crit_dim_types_SQL = "SELECT `dimension_type_ID` FROM `part_rev_critical_dimensions` WHERE `part_revision_ID` = '" . $part_rev . "' AND `record_status` = '2' GROUP BY `part_rev_critical_dimensions`.`dimension_type_ID` ORDER BY `part_rev_critical_dimensions`.`dimension_type_ID` ASC";
						$result_get_this_part_rev_crit_dim_type = mysqli_query($con,$get_this_part_rev_crit_dim_type_SQL);
						// while loop
						while($row_get_this_part_rev_crit_dim_type = mysqli_fetch_array($result_get_this_part_rev_crit_dim_type)) {

							// now print each record:
							$crit_dim_type_ID = $row_get_this_part_rev_crit_dim_type['dimension_type_ID'];
							
							// now get the dymension type data:
							$get_this_dimension_type_SQL = "SELECT * FROM `dimension_types` WHERE `ID` = '" . $crit_dim_type_ID . "'";
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
							
						} // end get types...
						
						
						
						
						
						
							// now count how many results there are:
							
							$type_colspan = 1;
							
							if ($this_crit_dim_type_ID != $crit_dim_type_ID) {
								$this_crit_dim_type_ID = $crit_dim_type_ID;
								$new_dim_type = 1;
								
								// start a new table row:
								?>
								</tr>
								<tr>
									<td colspan="<?php echo $type_colspan; ?>">
									  <span class="btn btn-xs btn-success" title="<?php 
									  	echo $this_dimension_type_name_EN; 
									  	if ( ( $this_dimension_type_name_CN != '' ) && ( $this_dimension_type_name_CN != '中文名' ) ) {
									  		echo ' / ' . $this_dimension_type_name_CN;
									  	}
									  ?>">
										<i class="fa fa-<?php echo $this_dimension_type_icon_code; ?>"></i>
										<?php
										echo $this_dimension_type_name_EN; 
									  	if ( ( $this_dimension_type_name_CN != '' ) && ( $this_dimension_type_name_CN != '中文名' ) ) {
									  		echo ' / ' . $this_dimension_type_name_CN;
									  	}
									  	?>
									  </span>
								<?php
								
							}
							else {
								// the type matches the type found, no need to make a new table rown just yet...
								// just run the rest of the rows?
								$new_dim_type = 0;
							}
							
						}
					
					
						// now get the crit_dim info
					
						$get_this_part_rev_crit_dims_SQL = "SELECT * FROM `part_rev_critical_dimensions` WHERE `part_revision_ID` = '" . $part_rev . "' AND `record_status` = '2' ORDER BY `part_rev_critical_dimensions`.`dimension_type_ID` ASC";
						$result_get_this_part_rev_crit_dims = mysqli_query($con,$get_this_part_rev_crit_dims_SQL);
						// while loop
						while($row_get_this_part_rev_crit_dims = mysqli_fetch_array($result_get_this_part_rev_crit_dims)) {

							// now print each record:
							$crit_dims_ID 						= $row_get_this_part_rev_crit_dims['ID'];
							$crit_dims_part_revision_ID 		= $row_get_this_part_rev_crit_dims['part_revision_ID'];
							$crit_dims_drawing_QC_ID 			= $row_get_this_part_rev_crit_dims['drawing_QC_ID'];
							$crit_dims_dimension_type_ID 		= $row_get_this_part_rev_crit_dims['dimension_type_ID'];
							$crit_dims_dimension_minimum 		= $row_get_this_part_rev_crit_dims['dimension_minimum'];
							$crit_dims_dimension_maximum 		= $row_get_this_part_rev_crit_dims['dimension_maximum'];
							$crit_dims_specification_notes 		= $row_get_this_part_rev_crit_dims['specification_notes'];
							$crit_dims_inspection_method_ID 	= $row_get_this_part_rev_crit_dims['inspection_method_ID'];
							$crit_dims_inspection_level 		= $row_get_this_part_rev_crit_dims['inspection_level'];
							$crit_dims_AQL_level 				= $row_get_this_part_rev_crit_dims['AQL_level'];
							$crit_dims_record_status 			= $row_get_this_part_rev_crit_dims['record_status'];
						
							$total_crit_dims = $total_crit_dims + 1;
						
							// 2. Now let's establish the sample size RANGE, based on data in the DB and the dimension info:
						
							$get_AQL_letter_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` LIKE '" . $crit_dims_inspection_level . "' AND `order_qty_max` > '" . $first_amount_in .  "' LIMIT 0,1";
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
							$get_sample_size_SQL = "SELECT * FROM `AQL_level` WHERE `AQL_level` = '" . $crit_dims_AQL_level . "' AND `letter_code` LIKE '" . $AQL_letter_result . "'";
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
						
							// 4. now get the method data:
							$get_this_method_SQL = "SELECT `inspection_method`.`ID` AS `method_ID`, `inspection_method`.`name_EN` AS `method_name_EN`, `inspection_method`.`name_CN` AS `method_name_CN`, `inspection_method`.`description`, `inspection_method_class`.`ID` AS `method_class_ID`, `inspection_method_class`.`name_EN` AS `class_name_EN`, `inspection_method_class`.`name_CN` AS `class_name_CN`, `AQL_level`, `sample_level` 
							FROM `inspection_method` 
							JOIN `inspection_method_class` 
							ON `inspection_method`.`method_class_ID` = `inspection_method_class`.`ID`
							WHERE `inspection_method`.`ID` = '" . $crit_dims_inspection_method_ID . "' 
							AND `inspection_method`.`record_status` = '2'
							AND `inspection_method_class`.`record_status` = '2'";
							$result_get_this_method = mysqli_query($con,$get_this_method_SQL);
							// while loop
							while($row_get_this_method = mysqli_fetch_array($result_get_this_method)) {

								// now print each record:
								$this_method_ID 				= $row_get_this_method['method_ID'];
								$this_method_method_name_EN 	= $row_get_this_method['method_name_EN'];
								$this_method_method_name_CN 	= $row_get_this_method['method_name_CN'];
								$this_method_description 		= $row_get_this_method['description'];
								$this_method_method_class_ID 	= $row_get_this_method['method_class_ID'];
								$this_method_class_name_EN 		= $row_get_this_method['class_name_EN'];
								$this_method_class_name_CN 		= $row_get_this_method['class_name_CN'];
								$this_method_AQL_level 			= $row_get_this_method['AQL_level'];
								$this_method_sample_level 		= $row_get_this_method['sample_level'];
							} // end get method and class loop
						
							
					
						// now output the results!
					
						?>
					  <tr>
						<th><?php echo 'Q' . $crit_dims_drawing_QC_ID; ?>: Sample Size:
						  <br />
							  <span class="btn btn-xs btn-success">
								<i class="fa fa-<?php echo $this_dimension_type_icon_code; ?>"></i>
							  </span>
							  &nbsp;
							  <span class="btn btn-xs btn-primary">
								<?php echo $AQL_letter_AQL_code; ?>
							  </span>
							  &nbsp;
							  <span class="btn btn-xs btn-warning">
								<?php echo $crit_dims_AQL_level; ?>
							  </span>
						</th>
						<td class="text-right">
						<?php
					
						if ($total_crit_dims == 0) {
							?><a href="part_rev_critical_dimensions_add.php?part_rev_ID=<?php echo $part_rev; ?>" class="text-danger">0 Ciritcal Dimensions Found - click to add one</a><?php
						}
						else {
					
							if ($sample_size_sample_size < $first_amount_in) {
								echo number_format($sample_size_sample_size,0);
							}
							else {
								echo number_format($first_amount_in,0);
							} 
						
						} 
					
						// now close the row:
					
						?>
						</td>
				  </tr>
						<?php
					
						} // end results loop for crit dims
						?>
				 </table>
			</div>
			
			<table class="table table-bordered table-striped table-condensed mb-none">
		  	  <tr>
			    <th class="text-center">Test item / 检测项目</th>
			    <th class="text-center">AQL</th>
			    <th class="text-center">Sample QTY / 取样数</th>
			    <th class="text-center">Measurement / 测量</th>
			    <th class="text-center">Specification / 规格</th>
			    <th class="text-center">Inspect Method / 检测方法</th>
			    <th class="text-center">Remark / 备注</th>
			    <th class="text-center">1</th>
			    <th class="text-center">2</th>
			    <th class="text-center">...</th>
		  	  </tr>
		  	  
		  	  <?php 
		  	  
		  	  $row_span = 5;
		  	  
		  	  ?>
		  	  <tr>
		  	  	<td rowspan="<?php echo $row_span; ?>" class="text-center">Dimension / 尺寸</td>
		  	  	<td rowspan="<?php echo $row_span; ?>" class="text-center">0.65 S3</td>
		  	  	<td rowspan="<?php echo $row_span; ?>" class="text-center">125</td>
		  	  	<td class="text-center">QC1</td>
		  	  	<td class="text-center">4.00 +0.05/-0</td>
		  	  	<td class="text-center">Caliper YATO YT219</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  </tr>
		  	  
		  	  
		  	  <tr>
		  	  	<td class="text-center">QC2</td>
		  	  	<td class="text-center">20 +0.1/-0</td>
		  	  	<td class="text-center">Caliper YATO YT219</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  </tr>
		  	  
		  	  
		  	  <tr>
		  	  	<td class="text-center">QC3</td>
		  	  	<td class="text-center">20 +0.1/-0</td>
		  	  	<td class="text-center">Caliper YATO YT219</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  </tr>
		  	  
		  	  
		  	  <tr>
		  	  	<td class="text-center">QC4</td>
		  	  	<td class="text-center">20 +0.1/-0</td>
		  	  	<td class="text-center">Caliper YATO YT219</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  </tr>
		  	  
		  	  
		  	  <tr>
		  	  	<td class="text-center">QC4</td>
		  	  	<td class="text-center">20 +0.1/-0</td>
		  	  	<td class="text-center">Caliper YATO YT219</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  	<td class="text-center">&nbsp;</td>
		  	  </tr>
		  	  
		    </table>
			
			
			<!-- END TABLE BASED ON CRITICAL DIMENSIONS  -->
			
			
			<?php
			// MORE WIP: THIS CODE WAS DELETED AS THINGS DEVELOP...
			?>
			
			<!-- ************************************************* -->
			<!-- ************************************************* -->
			<!-- ************************************************* -->
			<!-- ************************************************* -->
			<!-- ************************************************* -->
			<!-- NOW CREATE THE SAMPLE RECORD BOXES IN ROWS OF 10! -->
			<table class="table table-bordered table-striped table-hover table-condensed mb-none">
			<?php 
			$results_per_row = 10;
			$num_rows = round(( $sample_size_sample_size / $results_per_row ), 0, PHP_ROUND_HALF_UP);
			$this_row = 0;
			while ( $this_row < $num_rows ) {
			
				if ($this_row == 0) {
					$show_this_row = '';
				}
				else {
					$show_this_row = $this_row;
				}
				$multiplier = ( $show_this_row * $results_per_row );
			
				
			
				// NOW LOOP THE BOXES * $results_per_row:
				$this_cell = 1;
				$new_row_loop = 0;
				
				while ($sample_size_sample_size >= ( $multiplier + $this_cell )) {
				
					if ($new_row_loop == 0) {
						// open the tabel row:
						?>
						<tr>
						<?php
					}
					
					// open the table cell:
					?>
					<td class="text-center">
					<?php
					echo $show_this_row;
					if ($this_cell == 10) {
						echo ($show_this_row + 1) . "0";
					}
					else {
						echo $this_cell;
					}
					// now close the table cell:
					?>
					</td>
					<?php
					// now append the cell count:
					$this_cell = $this_cell + 1;
					$new_row_loop = $new_row_loop + 1;
					
					if ($new_row_loop == $results_per_row) {
						// now close the table row
						?>
						</tr>
						<?php
					}
					
				}
				
				?>
				
				<!-- 
			  <tr>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 1 )) {
					echo $show_this_row; ?>1<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 2 )) {
					echo $show_this_row; ?>2<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 3 )) {
					echo $show_this_row; ?>3<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 4 )) {
					echo $show_this_row; ?>4<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 5 )) {
					echo $show_this_row; ?>5<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 6 )) {
					echo $show_this_row; ?>6<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 7 )) {
					echo $show_this_row; ?>7<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 8 )) {
					echo $show_this_row; ?>8<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 9 )) {
					echo $show_this_row; ?>9<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
				<td class="text-center">
				<?php 
				
				  if ($sample_size_sample_size >= ( $multiplier + 10 )) {
					echo ($show_this_row + 1); ?>0<?php
				  }	
				  else {
					// show nothing as we have already met the minimum sample amount!
					?>/<?
				  }
				  
				?>
				</td>
			  </tr>
			  <tr>
				<td class="text-center text-success">4.01</td>
				<td class="text-center text-success">4.01</td>
				<td class="text-center text-success">4.01</td>
				<td class="text-center text-danger">4.12</td>
				<td class="text-center text-success">4.01</td>
				<td class="text-center text-success">4.01</td>
				<td class="text-center text-success"><i class="fa fa-check text-success"></i></td>
				<td class="text-center text-success"><i class="fa fa-times text-danger"></i></td>
				<td class="text-center text-success">4.01</td>
				<td class="text-center text-success">4.01</td>
			  </tr>-->
				<?php 
				
				$this_row = $this_row + 1;
				
			} // end of num_rows
			?>
			</table>
			
			<ul>
			  <li><span class="btn btn-xs btn-success"><strong>Pass:</strong> 12/20</span></li>
			  <li class="text-danger"><strong>Fail:</strong> 3/0</li>
			  <li class="text-warning"><strong>Untested:</strong> 5/0</li>
			  <li class="text-danger"><strong>Result:</strong> FAIL</li>
			</ul>
			
			<!-- END OF SAMPLE RECORD BOXES IN ROWS OF 10! -->
			<!-- ************************************************* -->
			<!-- ************************************************* -->
			<!-- ************************************************* -->
			<!-- ************************************************* -->
			<!-- ************************************************* -->
			
			
			
			<!-- now close the panel body -->
			</div>
		</div>
	</section>
    <!-- END PANEL #3 -->
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
