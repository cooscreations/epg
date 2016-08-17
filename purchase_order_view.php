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
		$PO_supplier_ID = $row_get_PO['supplier_ID'];  // LOOK THIS UP!
		$PO_created_by = $row_get_PO['created_by']; // use get_creator($PO_created_by);
		$PO_date_needed = $row_get_PO['date_needed'];
		$PO_date_delivered = $row_get_PO['date_delivered'];
		$PO_approval_status = $row_get_PO['approval_status']; // look this up?
		$PO_payment_status = $row_get_PO['payment_status']; // look this up?
		$PO_completion_status = $row_get_PO['completion_status'];
		
		
		// count variants for this purchase order
        $count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $record_id;
        $count_batches_query = mysqli_query($con, $count_batches_sql);
        $count_batches_row = mysqli_fetch_row($count_batches_query);
        $total_batches = $count_batches_row[0];
		
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
					 					    <th>Created By:</th>
					 					    <td><?php get_creator($PO_created_by); ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Completition Status:</th>
					 					    <td><?php 
					 					    
					 					    if ($PO_completion_status > 66) {
					 					    	$bar_color = "success";
					 					    }
					 					    else if ($PO_completion_status > 33) {
					 					    	$bar_color = "warning";
					 					    }
					 					    else {
					 					    	$bar_color = "danger";
					 					    }
					 					    
					 					    ?>
					 					    
					 					    
					 					    <div class="progress">
											  <div class="progress-bar progress-bar-striped active progress-bar-<?php echo $bar_color; ?>" 
											  	role="progressbar" 
											  	aria-valuenow="<?php echo $PO_completion_status; ?>" 
											  	aria-valuemin="0" 
											  	aria-valuemax="100" 
											  	style="width:<?php echo $PO_completion_status; ?>%">
												<?php echo $PO_completion_status; ?>%
											  </div>
											</div>
					 					    
					 					    
					 					    
					 					    </td>
					 					  </tr>
					 					  <tr>
					 					    <th>START:</th>
					 					    <td><?php echo substr($PO_created_date, 0, 10); ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>TARGET:</th>
					 					    <td><?php echo substr($PO_date_needed, 0, 10); ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>CLOSE:</th>
					 					    <td><?php echo substr($PO_date_delivered, 0, 10); ?> <span class="btn btn-xs btn-info" title="DEV. NOTE: We should add the difference in days between target date and actual date"><i class="fa fa-lightbulb-o"></i></span></td>
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
					 					    <th>P.O. Approval Status:</th>
					 					    <?php 
					 					    
					 					    if ($PO_approval_status == 0) {
					 					    	?>
					 					    	<td class="danger">
					 					    	  <i class="fa fa-times"></i> 
					 					    	  DELETED
					 					    	</td>
					 					    	<?php
					 					    }
					 					    else if ($PO_approval_status == 1) {
					 					    	?>
					 					    	<td class="warning">
					 					    	  <i class="fa fa-exclamation-triangle"></i> 
					 					    	  PENDING
					 					    	</td>
					 					    	<?php
					 					    }
					 					    else {
					 					    	?>
					 					    	<td class="success">
					 					    	  <i class="fa fa-check"></i>
					 					    	  OK
					 					    	</td>
					 					    	<?php
					 					    }
					 					    
					 					    
					 					    ?>
					 					  </tr>
					 					  <!-- **************************************** -->
					 					  <tr>
					 					    <th>P.O. Payment Status:</th>
					 					    <?php 
					 					    
					 					    if ($PO_payment_status == 0) {
					 					    	?>
					 					    	<td class="danger">
					 					    	  <i class="fa fa-times"></i> 
					 					    	  DELETED
					 					    	</td>
					 					    	<?php
					 					    }
					 					    else if ($PO_payment_status == 1) {
					 					    	?>
					 					    	<td class="warning">
					 					    	  <i class="fa fa-exclamation-triangle"></i> 
					 					    	  PENDING
					 					    	</td>
					 					    	<?php
					 					    }
					 					    else {
					 					    	?>
					 					    	<td class="success">
					 					    	  <i class="fa fa-check"></i>
					 					    	  OK
					 					    	</td>
					 					    	<?php
					 					    }
					 					    
					 					    
					 					    ?>
					 					  </tr>
					 					  <!-- **************************************** -->
					 					  <tr>
					 					    <th>DB Record Status:</th>
					 					    <?php 
					 					    
					 					    if ($PO_record_status == 0) {
					 					    	?>
					 					    	<td class="danger">
					 					    	  <i class="fa fa-times"></i> 
					 					    	  DELETED
					 					    	</td>
					 					    	<?php
					 					    }
					 					    else if ($PO_record_status == 1) {
					 					    	?>
					 					    	<td class="warning">
					 					    	  <i class="fa fa-exclamation-triangle"></i> 
					 					    	  PENDING
					 					    	</td>
					 					    	<?php
					 					    }
					 					    else {
					 					    	?>
					 					    	<td class="success">
					 					    	  <i class="fa fa-check"></i>
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
									  <li><?php get_supplier($PO_supplier_ID); ?></li>
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
						<div class="col-md-1">
							<a href="part_batch_add.php?PO_ID=<?php echo $_REQUEST['id']; ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-left"><i class="fa fa-plus-square"></i></a>
						</div>	
						<div id="feature_buttons_container_id" class="col-md-11">
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
					    <td>
					    	<a href="part_view.php?id=<?php echo $batch_part_ID; ?>" class="btn btn-info btn-xs" title="View Part Profile"><?php echo $part_code; ?></a>
					    </td>
					    <td>
					    	<span class="btn btn-warning btn-xs" title="Rev. ID#: <?php echo $rev_id; ?>">
					    		<?php echo $rev_number; ?>
					    	</span>
					    </td>
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
						<div class="col-md-1">
							<a href="part_batch_add.php?PO_ID=<?php echo $record_id; ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-left"><i class="fa fa-plus-square"></i></a>
						</div>	
						<div class="col-md-11"> </div>
					 </div>
					
								<!-- now close the panel -->
								</div>
							</section>
						</div>
					</div> <!-- end row! -->
				
				<br />
				<hr />
				<br />
				<h1>STARTING ORIGINAL P.O. FORM</h1>
				
				<!-- START P.O. CONTENT -->
<div class="row"><!-- P.O. ROW 1: -->


<div class="col-md-4">

	<!-- START PANEL - SUPPLIER INFORMATION -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Supplier Information</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				<strong>COMPANY NAME:</strong><br />
				Company Name will go here...
		  </div>
		</div>
	</section>
	<!-- END PANEL - SUPPLIER INFORMATION -->
	
</div>


<div class="col-md-8">

	<!-- START PANEL - PURCHASER INFORMATION -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Purchaser Information</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				
				<div class="row">
				
					<div class="col-md-6">
						European Pharma Group BV<br />
						Gebouw Euro Offices - 3<sup>rd</sup> floor<br />
						Beechavenue 127<br />
						1119 RB Schipol- Rijk<br />
						The Netherlands<br />
						T: +31 20 31 60 140<br />
						F: +31 20 31 60 141
					</div>
				
					<div class="col-md-6">
						EPG Shenzhen Representative Office<br />
						Room 509, Unit 2, Building 25, Keyuan West<br />
						Industrial Zone | No. 5 Kezi West Road, Nanshan<br />
						Shenzhen, P.R. China<br />
						Zip Code: 518054<br />
						T: +86 755 2167 3659<br />
						F: +86 755 8659 5232
					</div>
				
				</div>
				
				
		  </div>
		</div>
	</section>
	<!-- END PANEL - PURCHASER INFORMATION -->

</div>

</div><!-- END P.O. ROW 1 -->

<div class="row"><!-- P.O. ROW 2 -->

	<!-- START PANEL - ORDER INFORMATION -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Order</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				
				<div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                        <tr>
                            <th>Purchase Order No.</th>
                            <td>EPG2016xxxx</td>
                        </tr>
                        <tr>
                            <th>SHIP VIA</th>
                            <td>?</td>
                        </tr>
                        <tr>
                            <th>ORDERED BY:</th>
                            <td><a href="user_view.php?id=2">Nicky Canton</a></td>
                        </tr>
                        <tr>
                            <th>Date Ordered:</th>
                            <td>2016-08-17</td>
                        </tr>
                        <tr>
                            <th>Ship To:</th>
                            <td>abc abc</td>
                        </tr>
                        <tr>
                            <th>Date Needed:</th>
                            <td>2016-09-17</td>
                        </tr>
                        <tr>
                            <th>Order Type:</th>
                            <td><span class="btn btn-success"><i class="fa fa-tick"></i> New</span> | <span stlye="text-decoration: strikethrough">Change</span></td>
                        </tr>
                    </table>
                </div>
				
		  </div>
		</div>
	</section>
	<!-- END PANEL - ORDER INFORMATION -->

</div><!-- END P.O. ROW 2 -->

<div class="row"><!-- P.O. ROW 3 -->

	<!-- START PANEL - INSTRUCTIONS -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Instructions</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				<ol>
					<li>Please confirm the receipt of this order indicating the shipping date and address and quantity.</li>
					<li>All goods will be inspected and quantities verified by the receiving organization.</li>
					<li>Supplier agrees to notify European Pharma Group of any changes to the product or the process in order to give European Pharma Group the opportunity to determine whether the change may affect the Quality of the finished Medical Device.</li>
					<li>Fax or e-mail the confirmation to European Pharma Group.</li>
				</ol>
		  </div>
		</div>
	</section>
	<!-- END PANEL - INSTRUCTIONS -->

</div><!-- END P.O. ROW 3 -->

<div class="row"><!-- P.O. ROW 4 -->

	<!-- START PANEL - LINE ITEMS -->
	<section class="panel">
		<!-- NO HEADER FOR THIS PANEL -->
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				
				<div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                        <tr>
                            <th>ITEM NO.</th>
                            <th>DESCRIPTION</th>
                            <th>QTY</th>
                            <th>UNIT PRICE</th>
                            <th>TOTAL</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>
                            	01337 - Nozzle twist cap<br />
                            	according to 01337-A-2-2016xxxx<br />
                            	VAT included<br />
                            	Run mold at least 3H<br />
                            	Sample at least 300 shots<br />
                            	Including labour cost for cosmetic and dimension<br />
                            	Price included drilled hole in the side according spec.
                            </td>
                            <td>1</td>
                            <td>¥3,645.00</td>
                            <td>¥3,645.00</td>
                        </tr>
                    </table>
                </div>
				
		  </div>
		</div>
	</section>
	<!-- END PANEL - LINE ITEMS -->

</div><!-- END P.O. ROW 4 -->

<div class="row"><!-- P.O. ROW 5 -->

	<!-- START PANEL - OTHER INSTRUCTIONS -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Other Instructions</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				<ol>
					<li>
						<strong>
							Special requirements of the specifications, process requirements/protocols and requirements for approval of product or process:
						</strong>
						<br />
						Request COC or inspection report of supplier. Need to provide inspection report.
					</li>
					<li>
						<strong>
							Related standards:
						</strong>
						<br />
						N/A
					</li>
					<li>
						<strong>
							Special contracts, quality agreements/supply agreements:
						</strong>
						<br />
						Supplier Agreement (SunNgai-EPG2011) and Quality Agreement (SunNgai-EPG2012)
					</li>
					<li>
						<strong>
							Special requirements for qualification personnel:
						</strong>
						<br />
						N/A
					</li>
					<li>
						<strong>
							Special requirements for Quality Management System:
						</strong>
						<br />
						N/A
					</li>
					<li>
						<strong>
							Fax or e-mail the confirmation to European Pharma Group.
						</strong>	
					</li>
				</ol>
		  </div>
		</div>
	</section>
	<!-- END PANEL - OTHER INSTRUCTIONS -->

</div><!-- END P.O. ROW 5 -->

<div class="row"><!-- P.O. ROW 6 -->

	<div class="col-md-9">
	
		<!-- START CHECK AND SIGN -->
		
		<div class="row">
			<strong>Include Certificate of Compliance with Order</strong>
			<br />
			<span class="btn btn-success"><i class="fa fa-tick"></i> YES</span> | <span stlye="text-decoration: strikethrough">NO</span>
			<br />
			<strong>Approved By:</strong> <a href="user_view.php?id=2">Nicky Canton</a>
			<strong>Date:</strong> 2016-08-17
		</div>
		
		<div class="row">
			<strong>Confirmation Received</strong>
			<br />
			<span class="btn btn-success"><i class="fa fa-tick"></i> YES</span> | <span stlye="text-decoration: strikethrough">NO</span>
			<strong>Date:</strong> 2016-08-27
			<br />
			<strong>Comments:</strong> No comments at this time...
		</div>
		
		<!-- END CHECK AND SIGN -->
	
	</div>

	<div class="col-md-3">
	
		<!-- START P.O. SUMMARY TABLE -->
				
				<div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                        <tr>
                            <th>Subtotal</th>
                            <td>¥3,645.00</td>
                        </tr>
                        <tr>
                            <th>Shipping</th>
                            <td>¥0.00</td>
                        </tr>
                        <tr>
                            <th>Handling</th>
                            <td>¥0.00</td>
                        </tr>
                        <tr>
                            <th>TOTAL DUE</th>
                            <td>¥3,645.00</td>
                        </tr>
                    </table>
                </div>
		
		<!-- END P.O. SUMMARY TABLE -->
		
	</div>

</div><!-- END P.O. ROW 6 -->

<p>Page 1 of 1</p>
<!-- END OF TOTAL P.O. CONTENT -->

				
						
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>