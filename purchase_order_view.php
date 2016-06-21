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
include ('qrcode-generator/index_2.php');

/* session check */
if (!isset($_SESSION['username'])) {
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 10;

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else {	
	header("Location: purchase_orders.php?msg=NG&action=view&error=no_id");
	exit();		
}

$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $record_id;
$result_get_PO = mysqli_query($con,$get_PO_SQL);
// while loop
while($row_get_PO = mysqli_fetch_array($result_get_PO)) {
	
		// now print each record:  
		$PO_id = $row_get_PO['ID'];
		$PO_number = $row_get_PO['PO_number'];
		$PO_created_date = $row_get_PO['created_date'];
		$PO_description = $row_get_PO['description'];
		$PO_record_status = $row_get_PO['record_status'];
		$PO_record_supplier_ID = $row_get_PO['supplier_ID'];
		$PO_record_created_by = $row_get_PO['created_by'];
		
		
		// count variants for this purchase order
        $count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $record_id;
        $count_batches_query = mysqli_query($con, $count_batches_sql);
        $count_batches_row = mysqli_fetch_row($count_batches_query);
        $total_batches = $count_batches_row[0];
		
		
		/* ***************  GET SUPPLIER INFO ************************** */
		
		// now get the record info:
		$get_sups_SQL = "SELECT * FROM `suppliers` WHERE `ID` = " . $PO_record_supplier_ID;
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
		
		
		
		
} // end while loop

// pull the header and template stuff:
pagehead($page_id); 

?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Purchase Order Record - <?php echo $PO_number; ?></h2>
					
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
								<li><span>Purchase Order Record</span></li>
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
						<div class="col-md-4">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Main Details:</h2>
								</header>
								<div class="panel-body">
									
									
									<div class="table-responsive">
					 					<table class="table table-bordered table-striped table-hover table-condensed mb-none">
					 					  <tr>
					 					    <th>Purchase Order #:</th>
					 					    <td><?php echo $PO_number; ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Created Date:</th>
					 					    <td><?php echo $PO_created_date; ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Remarks:</th>
					 					    <td><?php echo $PO_description; ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Total Batches:</th>
					 					    <td><?php echo $total_batches; ?> (see below)</td>
					 					  </tr>
					 					  <tr>
					 					    <th>DB Record Status:</th>
					 					    <?php 
					 					    
					 					    if ($PO_record_status == 0) {
					 					    	?>
					 					    	<td class="danger">
					 					    	DELETED
					 					    	</td>
					 					    	<?php
					 					    }
					 					    else if ($PO_record_status == 1) {
					 					    	?>
					 					    	<td class="warning">
					 					    	PENDING
					 					    	</td>
					 					    	<?php
					 					    }
					 					    else {
					 					    	?>
					 					    	<td class="success">
					 					    	OK
					 					    	</td>
					 					    	<?php
					 					    }
					 					    
					 					    
					 					    ?>
					 					  </tr>
					 					  
					 					</table>
					 				</div>
									
								</div>
							</section>
						</div>
						
						
						
						<div class="col-md-4">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Vendor Details:</h2>
								</header>
								<div class="panel-body">
									
									<ul>
									 <?php if ($sup_ID == 0) { 
									 	// no vendor found - flag it!
										 ?><li class="text-danger">NO VENDOR SET</li><?php 
									 }
									 else { 
									 	// we found a vendor, let's show the info!
									 	?>
									  	<li>
									  	  <a href="supplier_view.php?id=<?php echo $sup_ID; ?>">
									  		<?php echo $sup_en; if (($sup_cn!='')&&($sup_cn!='中文名')) { ?> / <?php echo $sup_cn; } ?>
									  	  </a>
									  	</li>
									  <?php 
									  } 
									  ?>
									</ul>
									
								</div>
							</section>
						</div>
						
						
						
						<div class="col-md-4">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">QR Code to this page:</h2>
								</header>
								<div class="panel-body">
					 				
					 				
					 				<!-- ********************************************************* -->
					 				
					 				<div class="thumb-info mb-md" style="text-align:center;">
										<?php 
											// now show their QR code!
											show_code('PO_QR', $_REQUEST['id']); 
										?>
									</div>
									
					 				<!-- ********************************************************* -->
								
								</div>
							</section>
						</div>
						
						
					</div>
					
					
					<div class="row">
					
					<div class="col-md-12">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Batches In This Purchase Order:</h2>
								</header>
								<div class="panel-body">
								
					 <div class="row">
						<div id="feature_buttons_container_id" class="col-md-11">
						</div>
						<div class="col-md-1">
							<a href="part_batch_add.php?PO_ID=<?php echo $_REQUEST['id']; ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
						</div>	
					 </div>
					 
					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
					 <thead>
						  <tr>
							<th>Batch Number</th>
							<th>Part Code</th>
							<th>Rev.</th>
							<th>Name</th>
							<th>名字</th>
							<th>QTY Rec.</th>
						  </tr>
					  </thead>
					  <tbody>
						<?php 
					  
					  $batch_count = 0;
					  $movement_in_total = 0;
					  
					  // GET BATCHES: 
						$get_batches_SQL = "SELECT * FROM `part_batch` WHERE `PO_ID` = " . $_REQUEST['id'];
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
					    <td><a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo $batch_number; ?></a></td>
					    <td><a href="part_view.php?id=<?php echo $batch_part_ID; ?>"><?php echo $part_code; ?></a></td>
					    <td><?php echo $rev_number; ?></td>
					    <td><a href="part_view.php?id=<?php echo $batch_part_ID; ?>"><?php echo $part_name_EN; ?></a></td>
					    <td><a href="part_view.php?id=<?php echo $batch_part_ID; ?>"><?php echo $part_name_CN; ?></a></td>
					    <td>
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
								
					    <a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo $movement_in; ?></a>
					    		
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
							<th colspan="5">TOTAL: <?php echo $batch_count; ?></th>
							<th><?php echo $movement_in_total; ?></th>
						  </tr>
					  </tfoot>
					  
					 </table>
					 </div>
					
					
					 <div class="row">
						<div class="col-md-11"> </div>
						<div class="col-md-1">
							<a href="part_batch_add.php?PO_ID=<?php echo $record_id; ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
						</div>	
					 </div>
					
								<!-- now close the panel -->
								</div>
							</section>
						</div>
					</div> <!-- end row! -->
						
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>