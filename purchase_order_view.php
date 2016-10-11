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

// THIS PAGE HAS A PRINTABLE VERSION...
$print_view = 0; // DO NOT SHOW PRINT VIEW BY DEFAULT
$print_function_var = 1; // SHOW LINKS AND NON-PRINT STUFF BY DEFAULT (this is a little clunky)
if ($_REQUEST['print_view'] == 1) {
	$print_view = 1;
	$print_function_var = 0;
}

header('Content-Type: text/html; charset=utf-8');
require ('page_functions.php');
include 'db_conn.php';
include ('qrcode-generator/index_2.php');

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
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
		$PO_order_status 			= $row_get_PO['order_status'];

		// ADDING NEW VARIABLES - DEFAULT CURRENCY!
		
		$PO_default_currency		= $row_get_PO['default_currency']; // look this up!
		$PO_default_currency_rate	= $row_get_PO['default_currency_rate'];
		
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

		// count variants for this purchase order
        $count_batches_sql 		= "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $record_id;
        $count_batches_query 	= mysqli_query($con, $count_batches_sql);
        $count_batches_row 		= mysqli_fetch_row($count_batches_query);
        $total_batches 			= $count_batches_row[0];

} // end while loop


if ($print_view == 0) { // regular page
	// pull the header and template stuff:
	pagehead($page_id);
	// show regular button / weblinks
	$display_button = 1;
}
else {
	// show plain text
	$display_button = 0;
	// show printable header here:
	?>
	<!doctype html>

	<!--

	<html class="fixed js flexbox flexboxlegacy no-touch csstransforms csstransforms3d no-overflowscrolling no-mobile-device custom-scroll sidebar-left-collapsed">

	-->

	<html class="fixed sidebar-left-collapsed">

	<head>


<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>

		<!-- Basic -->
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>EPG Purchase Order <?php echo $PO_number; ?></title>
		<meta name="keywords" content="EPG Connect, <?php echo $PO_number; ?>" />
		<meta name="description" content="PO # <?php echo $PO_number; ?> (printable version)">
		<meta name="author" content="MarkClulow.com">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="bootstrap.min.css" type="text/css" media="print" ><!-- printer-friendly? -->

		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css" />
		<link rel="stylesheet" href="assets/vendor/intl-tel-input/css/intlTelInput.css" />

		<!-- Specific Page Vendor CSS - ADVANCED FORMS-->
		<link rel="stylesheet" href="assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css" />
		<link rel="stylesheet" href="assets/vendor/select2/select2.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css" />
		<link rel="stylesheet" href="assets/vendor/dropzone/css/basic.css" />
		<link rel="stylesheet" href="assets/vendor/dropzone/css/dropzone.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-markdown/css/bootstrap-markdown.min.css" />
		<link rel="stylesheet" href="assets/vendor/summernote/summernote.css" />
		<link rel="stylesheet" href="assets/vendor/summernote/summernote-bs3.css" />
		<link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css" />
		<link rel="stylesheet" href="assets/vendor/codemirror/theme/monokai.css" />


		<!-- Specific Page Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/pnotify/pnotify.custom.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="assets/stylesheets/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme-custom.css">

		<!-- Head Libs -->
		<script src="assets/vendor/modernizr/modernizr.js"></script>
		
		<style>
		  body, * {
		  	/* font-size: small; */
		  }
		</style>

	</head>
	<body>
		<section class="body">
				<section role="main" class="content-body">
				<div class="row"><!-- ROW 0 -->
		
		<!-- 
		  <div class="row text-center">
			<img src="assets/images/logo.png" height="30" alt="EPG Connect" class="text-center" />
		  </div>
		  <div class="row text-center">
			<h3 class="text-center">PURCHASE ORDER</h3>
		  </div>
		-->
		<div class="table-responsive">  
		  <table class="table table-condensed mb-none">
		  <tbody>
		    <tr>
		      <td style="vertical-align: middle; text-align: center; width: 25%;"><img src="assets/images/logo.png" height="30" alt="EPG Connect" class="text-center" /></td>
		      <td style="vertical-align: middle; text-align: center;"><h3 class="text-center">PURCHASE ORDER # <?php echo $PO_number; ?></h3></td>
		    </tr>
		  </tbody>
		</table>
	  </div>
	  
	  </div><!-- END ROW 0 -->
		  
	<?php
}

?>



<!-- START MAIN PAGE BODY : -->

				<?php 
				if ($print_view == 0) { // ONLY SHOW THIS ON PRINT VERSION!
				?>
				
				
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
					
				<?php
				}
				else {
					// show printable header here:

				}
				?>

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
				
				<!-- START P.O. CONTENT -->
<div class="row"><!-- P.O. ROW 1: -->



	<?php
	// now run the admin bar function:
	// Fix for bug#39 and 37 - main table os part_batch
	if ($print_view == 0) { // HIDE ADMIN BAR ON PRINT VIEW
	?>

		<div class="col-md-4">
			<?php
				admin_bar('purchase_order');
			?>
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
						<?php get_supplier($PO_supplier_ID, 1); // show name and address in P.O. format, with link to vendor profile ?>
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
				
							<!-- GET THIS FROM THE `locations` TABLE! -->
				
							<div class="col-md-6">
							  <?php get_location($PO_local_location_ID,1); ?>
							</div>
				
							<div class="col-md-6">
							  <?php get_location($PO_HQ_location_ID,1); ?>
							</div>
				
						</div>
				
				
				  </div>
				</div>
			</section>
			<!-- END PANEL - PURCHASER INFORMATION -->

		</div>
<?php 
	} // END OF HIDE FOR PRINT
	else {
		// UGLY PRINT VIEW! 
		?>
		<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover table-condensed mb-none">
		  <thead>
		  	<tr>
		      <th class="text-center">SUPPLIER INFORMATION:</th>
		      <th class="text-center" colspan="2">PURCHASED BY:</th>
		  	</tr>
		  </thead>
		  <tbody>
		    <tr>
		      <td valign="top" width="33%"><?php get_supplier($PO_supplier_ID,2,0); ?></td>
		      <td valign="top" width="33%"><?php get_location($PO_local_location_ID,2,0); ?></td>
		      <td valign="top" width="33%"><?php get_location($PO_HQ_location_ID,2,0); ?></td>
		    </tr>
		  </tbody>
		</table>
		</div>
		<?php
	} // END PRINT VIEW TABLE
	?>

</div><!-- END P.O. ROW 1 -->

<div class="row"><!-- P.O. ROW 2 -->

<?php 
if ($print_view == 0) { // ONLY SHOW THIS ON PRINT VERSION!
?>

  <div class="col-md-8">

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
<?php 
} // END OF HIDE FOR PRINT
?>				
				<div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					  <tbody>
                        <?php 
						if ($print_view == 0) { // ONLY SHOW THIS ON PRINT VERSION!
							?>
							<tr>
								<th style="text-align: right">Purchase Order No.</th>
								<td><?php echo $PO_number; ?></td>
							</tr>
							<tr>
								<th style="text-align: right">Short Description</th>
								<td><?php echo $PO_description; ?></td>
							</tr>
							<?php 
                        } 
                        
                        
                        if ($PO_ship_via != 'N/A') { 
							?>
							<tr>
								<th style="text-align: right">Ship Via.</th>
								<td><?php echo $PO_ship_via; ?></td>
							</tr>
							<?php 
                        }
                        ?>
                        <tr>
                            <th style="text-align: right">Ordered By</th>
                            <td><?php get_creator($PO_created_by, $display_button); ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right">Date Ordered</th>
                            <td><?php echo substr($PO_created_date, 0, 10); ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right">Date Needed</th>
                            <td><?php echo substr($PO_date_needed, 0, 10); ?></td>
                        </tr>
                        
                        <?php 
						if ($print_view == 0) { // ONLY SHOW THIS ON PRINT VERSION!
							?>
							<tr>
								<th style="text-align: right">Date Delivered</th>
								<td><?php echo substr($PO_date_delivered, 0, 10); ?> <span class="btn btn-xs btn-info" title="DEV. NOTE: We should add the difference in days between target date and actual date"><i class="fa fa-lightbulb-o"></i></span></td>
							</tr>
							<tr>
							  <th style="text-align: right">Total Batches in System:</th>
							  <td><?php echo $total_batches; ?> (see below)</td>
							</tr>
							<?php 
					    } 
					    ?>
                        <tr>
                            <th style="text-align: right">Ship To</th>
                            <td><?php get_location($PO_ship_to_location_ID,0,$print_function_var); ?></td>
                        </tr>
                        <?php 
						if ($print_view == 0) { // ONLY SHOW THIS ON PRINT VERSION!
							?>
							<tr>
								<th style="text-align: right">Completition Status:</th>
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
						  
							  <!-- **************************************** -->
							  <tr>
								<th style="text-align: right">P.O. Payment Status:</th>
								<td>
									<?php payment_status($PO_payment_status); ?>
								</td>
							  </tr>
							  <!-- **************************************** -->
						  
							  <tr>
								<th style="text-align: right">P.O. Currency:</th>
								<td>
								  <span class="btn btn-default" title="<?php 
									echo $PO_default_currency_name_EN;
									if (($PO_default_currency_name_CN!='')&&($PO_default_currency_name_CN!='中文名')) {
										echo ' / ' . $PO_default_currency_name_CN;
									}
								
									if ($PO_default_currency_symbol != 1) { // SHOW USD CONVERSION RATE!
										echo ' @ ' . $PO_default_currency_symbol . $PO_default_currency_rate . ' / $1 USD';
									}
								  ?>">
									<?php
								
									echo $PO_default_currency_symbol;
									echo $PO_default_currency_abbreviation; 
								
									?>
								  </span>
								</td>
							  </tr>
						  
							  <?php 
						  } // END OF SCREEN ONLY DATA (Don't print)
						  ?>
						</tbody>
						  <!-- **************************************** -->
                    </table>
                </div>
<?php 
if ($print_view == 0) { // ONLY SHOW THIS ON PRINT VERSION!
?>				
		  </div>
		</div>
	</section>
	<!-- END PANEL - ORDER INFORMATION -->
<?php } // end of hide for print ?>	
  </div>
<?php 
if ($print_view == 0) { 
// DO NOT SHOW THIS SECTION ON THE PRINT VERSION!
?>  
			  <!-- **************************************** -->
			  <!-- **************************************** -->
			  <!-- **************************************** -->
			  <!--      START AUTO QR CODE PANEL HERE       -->
			  <!-- **************************************** -->
			  <!-- **************************************** -->
			  <!-- **************************************** -->
  
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
								show_code('PO_QR', $record_id);
							?>
				
							<!-- LINK TO PDF VERSION -->
							<div class="row">
							  <div class="text-center">
								<a class="btn btn-danger" href="purchase_order_view.php?id=<?php echo $record_id; ?>&print_view=1" target="_blank" title="View printable version">
								  <i class="fa fa-file-pdf-o"></i>
								  VIEW PRINT VERSION
								</a>
							  </div>
							</div>
						</div>

						<!-- ********************************************************* -->

					</div>
				</section>
			</div>
  
			  <!-- **************************************** -->
			  <!-- **************************************** -->
			  <!-- **************************************** -->
			  <!--        END AUTO QR CODE PANEL HERE       -->
			  <!-- **************************************** -->
			  <!-- **************************************** -->
			  <!-- **************************************** -->
<?php 
} // END OF HIDE SECTION FOR PRINT VIEW
?>
</div><!-- END P.O. ROW 2 -->

<div class="row"><!-- P.O. ROW 3 -->
<?php 
if ($print_view == 0) { // ONLY SHOW THIS ON PRINT VERSION!
?>
	<!-- START PANEL - INSTRUCTIONS -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Special Instructions</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
<?php 
} // end of print only view
else {
	?>
	<div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                    	<?php 
						if ($print_view == 1) { // ONLY SHOW THIS ON PRINT VERSION!
						?>
                      <thead>
						<tr>
						  <th class="text-center">SPECIAL INSTRUCTIONS</th>
						</tr>
					  <thead>
					  <tbody>
					    <tr>
					  	  <td>
						<?php } ?>
	<?php
} // end of PRINT ONLY view
?>
				<!-- PANEL CONTENT HERE -->
				<ol>
					<li><strong>Please confirm the receipt of this order indicating the shipping date and address and quantity.</strong></li>
					<li><strong>All goods will be inspected and quantities verified by the receiving organization.</strong></li>
					<li><strong>Supplier agrees to notify European Pharma Group of any changes to the product or the process in order to give European Pharma Group the opportunity to determine whether the change may affect the Quality of the finished Medical Device.</strong></li>
					<li><strong>Fax or e-mail the confirmation to European Pharma Group.</strong></li>
				</ol>
<?php 
if ($print_view == 0) { // ONLY SHOW THIS ON PRINT VERSION!
?>
		  </div>
		</div>
	</section>
	<!-- END PANEL - INSTRUCTIONS -->
<?php 
} // end of print only view
else {
	?>
	        </td>
	      </tr>
	    </tbody>
	  </table>
	</div>
<?php 
} // end of print only view
?>
</div><!-- END P.O. ROW 3 -->

<div class="row"><!-- P.O. ROW 4 -->
<?php 
if ($print_view == 0) { // ONLY SHOW THIS ON SCREEN VERSION!
?>
	<!-- START PANEL - LINE ITEMS -->
	<section class="panel">
		<!-- NO HEADER FOR THIS PANEL -->
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				<?php add_button($record_id, 'purchase_order_item_add', 'PO_ID', 'Click here to add another item to this Purchase Order'); ?>
<?php 
} // end of SCREEN ONLY view 
?>				
				<div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                        <tr>
                            <?php if ($print_view == 0) { ?><th class="text-center"><i class="fa fa-cog"></i></th><?php } ?>
                            <th class="text-center">ITEM NO.</th>
                            <th class="text-center">DESCRIPTION</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">UNIT PRICE</th>
                            <th class="text-center">TOTAL</th>
                        </tr>
                        <?php 
                        
                        $po_line_number = 1;
                        $line_total = 0;	
						$subtotal = 0;
						$total_qty = 0;
                        
                        // GET PURCHASE ORDER ITEMS FROM THE DATABASE:
                        
                        $po_items_count = 0;
                        
                        $get_purchase_order_items = "SELECT * FROM `purchase_order_items` WHERE `purchase_order_ID` ='" . $record_id . "' AND `record_status` = '2'";
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
								
							if ($PO_default_currency_ID != $po_item_original_currency) {
								// CURRENCY NEEDS ADJUSTING!
								echo '<h1>WARNING - CURRENCY (ID# ' . $po_item_original_currency . ') DOES NOT MATCH DEFAULT PO CURRENCY (ID# ' . $PO_default_currency_ID . ')!</h1>';
								
								/* 
								
								curencies don't match - let's fix it!
								CONVERT ALL TO DOLLARS (PO AND PO LINE ITEM)
								
								*/
								
								// now get the currency info
								$get_po_item_currency_SQL = "SELECT * FROM `currencies` WHERE `ID` ='" . $po_item_original_currency . "'";
								// debug:
								// echo '<h3>'.$get_po_item_currency_SQL.'<h3>';
								$result_get_po_item_currency = mysqli_query($con,$get_po_item_currency_SQL);
									// while loop
									while($row_get_po_item_currency = mysqli_fetch_array($result_get_po_item_currency)) {

										// now print each result to a variable:
										$po_item_currency_ID 			= $row_get_po_item_currency['ID'];
										$po_item_currency_name_EN		= $row_get_po_item_currency['name_EN'];
										$po_item_currency_name_CN		= $row_get_po_item_currency['name_CN'];
										$po_item_currency_one_USD_value	= $row_get_po_item_currency['one_USD_value'];
										$po_item_currency_symbol		= $row_get_po_item_currency['symbol'];
										$po_item_currency_abbreviation	= $row_get_po_item_currency['abbreviation'];
										$po_item_currency_record_status	= $row_get_po_item_currency['record_status'];
										
										// OK, now convert to dollars
										$po_item_currency_USD_value = ($po_item_unit_price_currency / $po_item_currency_one_USD_value);
										
										// NOW CONVERT IT BACK TO PO DEFAULT RATE
										$po_item_unit_price_currency = ($po_item_unit_price_currency * $PO_default_currency_one_USD_value);
										
										// IDEAL WORLD - now update the database - update line item to match default PO currency
										// // //
									}
								
							}
							
								// NOW DO THE TOTALS CALCULATIONS
								$line_total = ($po_item_unit_price_currency * $po_item_part_qty);	
								$subtotal = $subtotal + $line_total;
							
								// get part revision info:
								$get_po_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` ='" . $po_item_part_revision_ID . "'";
								// debug:
								// echo '<h2>'.$get_po_part_rev_SQL . '</h2>'; 
								$result_get_po_part_rev = mysqli_query($con,$get_po_part_rev_SQL);
								// while loop
								while($row_get_po_part_rev = mysqli_fetch_array($result_get_po_part_rev)) {

									// now print each record:
									$po_rev_id 			= $row_get_po_part_rev['ID'];
									$po_rev_part_id 	= $row_get_po_part_rev['part_ID'];
									$po_rev_number 		= $row_get_po_part_rev['revision_number'];
									$po_rev_remarks 	= $row_get_po_part_rev['remarks'];
									$po_rev_date 		= $row_get_po_part_rev['date_approved'];
									$po_rev_user 		= $row_get_po_part_rev['user_ID'];

								}
								
								// now get the part info
								$get_po_part_SQL = "SELECT * FROM `parts` WHERE `ID` = '" . $po_rev_part_id . "'";
								// debug:
								// echo '<h3>' . $get_po_part_SQL . '</h3>'; 
								$result_get_po_part = mysqli_query($con,$get_po_part_SQL);
								// while loop
								while($row_get_po_part = mysqli_fetch_array($result_get_po_part)) {

									// now print each result to a variable:
									$po_part_id 		= $row_get_po_part['ID'];
									$po_part_code 		= $row_get_po_part['part_code'];
									$po_part_name_EN 	= $row_get_po_part['name_EN'];
									$po_part_name_CN 	= $row_get_po_part['name_CN'];

								}
                        ?>
					  
					  
                        <tr>
                        <?php if ($print_view == 0) { ?>
									<td class="text-center"> 
										<!-- ********************************************************* -->
										<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
										<!-- ********************************************************* -->
								
										<?php 
								
										// VARS YOU NEED TO WATCH / CHANGE:
										$add_to_form_name 	= 'line_item_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
										$form_ID 			= $po_item_ID;					// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
										$edit_URL 			= 'purchase_order_item_edit'; 	// REQUIRED - specify edit page URL
										$add_URL 			= 'purchase_order_item_add'; 	// REQURED - specify add page URL
										$table_name 		= 'purchase_order_items';		// REQUIRED - which table are we updating?
										$src_page 			= $this_file;					// REQUIRED - this SHOULD be coming from page_functions.php
										$add_VAR 			= 'PO_ID='.$record_id.''; 		// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
								
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
																	echo '?' . $add_VAR;  // NOTE THE LEADING '?' <<<
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
                        	<?php } // END OF HIDE ADMIN ACTIONS BUTTON FOR PRINT VIEW! ?>
                            <td class="text-center">
                              <strong>
                            	<?php echo $po_line_number; ?>
                              </strong>	
                            </td>
                            <td>
                            	<strong>
                            		<?php part_num($po_part_id, $display_button); ?> 
                            		
                            		- 
                            	
                            	<?php part_name($po_part_id, $print_function_var); ?>
                            	
                            		-
								
								<?php part_rev($po_rev_id, $display_button); ?>
                            	
								<br />
                            	<?php echo nl2br($po_item_item_notes); ?>
                              </strong>
                            </td>
                            <td class="text-right">
                            	<strong>
                            		<?php echo number_format($po_item_part_qty); ?>
                            	</strong>
                            </td>
                            <td class="text-right">
                              <strong>
                        		<?php 
                            	echo $PO_default_currency_symbol;	// NOTE: We are using the default PO currency symbol
                            	echo number_format($po_item_unit_price_currency, 2); 
                            	?>
                              </strong>	
                            </td>
                            <td class="text-right">
                              <strong>
                            	<?php
                            	echo $PO_default_currency_symbol;
                            	
                            	// LINE TOTALS!
                            	echo number_format($line_total, 2); 
                            	?>
                              </strong>
                            </td>
                        </tr>
                        <?php 
                        
                        	$po_line_number = $po_line_number + 1;
                        
                        // END GET PURCHASE ORDER LINE ITEMS FROM DB (while loop)
                        }
                        ?>
                    </table>
                </div>
                
                <?php 
				
	if ($print_view == 0) { // UPDATE: Hide this for print view!
				
		add_button($record_id, 'purchase_order_item_add', 'PO_ID', 'Click here to add another item to this Purchase Order'); 
		
		?>
				
			  </div>
			</div>
		</section>
		<!-- END PANEL - LINE ITEMS -->
		<?php 
	} // END OF SCREEN ONLY DATA 
	?>

</div><!-- END P.O. ROW 4 -->

<div class="row"><!-- P.O. ROW 5 -->
<?php 		
	if ($print_view == 0) { // UPDATE: Hide this for print view!
?>
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
<?php 
} // END SCREEN-ONLY DATA
else {
	?>
	<div class="table-responsive">
	 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
	 <thead>
		<tr>
			<th class="text-center">OTHER INSTRUCTIONS</th>
		</tr>
	  </thead>
	  <tbody>
		<tr>
		  <td>
	<?php
}
?>
				<ol>
					<li>
						<strong>
							Special requirements of the specifications, process requirements/protocols and requirements for approval of product or process:
						</strong> 
						<?php echo $PO_special_reqs; ?>
					</li>
					<li>
						<strong>
							Related standards:
						</strong> 
						<?php echo $PO_related_standards; ?>
					</li>
					<li>
						<strong>
							Special contracts, quality agreements/supply agreements:
						</strong> 
						<?php echo $PO_special_contracts; ?>
					</li>
					<li>
						<strong>
							Special requirements for qualification personnel:
						</strong> 
						<?php echo $PO_qualification_personnel; ?>
					</li>
					<li>
						<strong>
							Special requirements for Quality Management System:
						</strong> 
						<?php echo $PO_QMS_reqs; ?>
					</li>
				</ol>
<?php 		
	if ($print_view == 0) { // UPDATE: Hide this for print view!
?>
		  </div>
		</div>
	</section>
	<!-- END PANEL - OTHER INSTRUCTIONS -->
<?php 
} // END SCREEN ONLY DATA
else {
	?>
		</td>
	  </tr>
	</tbody>
  </table>
  </div>
	<?php
} // end PRINT ONLY view
?>

</div><!-- END P.O. ROW 5 -->

<div class="row"><!-- P.O. ROW 6 -->
<?php 		
	if ($print_view == 0) { // UPDATE: Hide this for print view!
?>		
		<!-- START PANEL - CHECK AND SIGN -->
	<section class="panel col-md-9">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Authorisation</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
<?php 
} // END SCREEN-ONLY DATA
else {
	?>
	<div class="table-responsive">
	 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
	  <tbody>
		<tr>
		  <td style="width:75%">
	<?php
}
?>
				<!-- PANEL CONTENT HERE -->  
				
					<strong>Include Certificate of Compliance with Order</strong>
					<br />
						<?php
						
						if ($print_view == 1) {
								if ($PO_include_CoC == 1) { // YES!
									?>
									YES <big>☑</big> NO <big>☐</big>
									<?php
								}
								else { // NO!
									?>
									YES <big>☐</big> NO <big>☑</big>
									<?php
								}
						}
						else {
								if ($PO_include_CoC == 1) { // YES! 
								?>
									<span class="btn btn-success">
										<i class="fa fa-tick"></i> YES
									</span> 
									| 
									<span class="btn btn-default">
										<s>NO</s>
									</span>
								<?php }
								else { // NO!
								?>
									<span class="btn btn-default">
										<s>YES</s>
									</span> 
									| 
									<span class="btn btn-danger">
										<i class="fa fa-times"></i> NO
									</span>
								<?php 
								} 
						}
						
						?>
					<br />
					<br />
					<?php 
						if ($print_view == 1) {
						
									echo '<strong>Approved By:</strong>'; // SPACE TO SIGN WITH ADOBE!
						
									/*
									if ($PO_approved_by == 0) {
										echo '<strong>Approved By:</strong> _______________________________________________'; // SPACE TO SIGN!
									}
									else {
										?>
							<strong>Approved By:</strong> <?php get_creator($PO_approved_by, $display_button); ?>
										<?php
									}
									*/
						
									// now let's but a big of space in here... 
									echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						
						}
						else {
							?>
							<strong>Approved By:</strong> <?php get_creator($PO_approved_by, $display_button); ?>
							<?php
						}
					?>
					 <?php 
					
					if ($print_view == 1) {
						/* -- DELETING THIS BECAUSE ADOBE SIGN WILL HANDLE IT
						if ($PO_approval_date == '0000-00-00 00:00:00') {
							echo '<strong>Date:</strong> 2 0 __ __ - __ __ - __ __ <em>(YYYY-MM-DD)</em>';
						}
						else {
							echo '<strong>Date:</strong> ' . substr($PO_approval_date, 0, 10);
						}
						*/
					}
					else {
						echo '<strong>Date:</strong> ' . substr($PO_approval_date, 0, 10); 
					} ?>
				
				<br />
				<br />
				
					<strong>Confirmation Received</strong>
					<br />
						<?php
						
						if ($print_view == 1) {
								if ($PO_date_confirmed != '0000-00-00 00:00:00') { // YES!
									?>
									YES <big>☑</big> NO <big>☐</big>
									<?php
								}
								else {
									?>
									YES <big>☐</big> NO <big>☐</big>
									<?php
								}
								// now let's but a big of space in here... 
								echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						else {
								if ($PO_date_confirmed != '0000-00-00 00:00:00') { // YES! 
								?>
									<span class="btn btn-success">
										<i class="fa fa-tick"></i> YES
									</span> 
									| 
									<span class="btn btn-default">
										<s>NO</s>
									</span>
								<?php }
								else {
								?>
									<span class="btn btn-default">
										<s>YES</s>
									</span> 
									| 
									<span class="btn btn-danger">
										<i class="fa fa-times"></i> NO
									</span>
								<?php 
								} 
						}
						
						?>
					<strong>Date:</strong> <?php 
					
					if ($print_view == 1) {
						if ($PO_date_confirmed == '0000-00-00 00:00:00') {
							echo '2 0 __ __ - __ __ - __ __ <em>(YYYY-MM-DD)</em>';
						}
						else {
							echo substr($PO_date_confirmed, 0, 10);
						}
					}
					else {
						echo substr($PO_date_confirmed, 0, 10); 
					} ?>
					<br />
					<br />
					<strong>Comments:</strong> <?php 
					if ($print_view == 1) {
						if ($PO_remark !='Please help to update this record.') {
							echo $PO_remark; 
						}
						else {
							// do nothing... leave a space for notes on the print version!
						}
						
					}
					else {
						echo $PO_remark; 
					}
					
					?>
					
				<!-- END OF PANEL CONTENT -->
		
<?php 		
	if ($print_view == 0) { // UPDATE: Hide this for print view!
?>
		  </div>
		</div>
	</section>
	<!-- END PANEL - OTHER INSTRUCTIONS -->
<?php 
} // END SCREEN ONLY DATA
else {
	?>
		</td>
		<!-- THE TOTALS GO ON THE SAME TABLE ROW, so don't close it until after! -->
	<?php
} // end PRINT ONLY view
?>
		
		<!-- END CHECK AND SIGN -->
	
	<!-- START PANEL - P.O. SUMMARY / ORDER TOTALS $$$ -->
<?php 		
	if ($print_view == 0) { // UPDATE: Hide this for print view!
?>	
	<section class="panel col-md-3">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Order Summary</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
<?php 
} // END SCREEN-ONLY DATA
else { ?><td><?php }
	?>
				<!-- PANEL CONTENT HERE -->
	
	
		<!-- START P.O. SUMMARY TABLE -->
		
		<?php 
		
		$handling_total = '0';
		$shipping_total = '0';
		
		// $subtotal calculated above
		$grand_total = ($handling_total + $shipping_total + $subtotal);
		
		?>
				
				<div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                        <tr>
                            <th>Total Qty</th>
                            <td class="text-right"><?php echo number_format($total_qty); ?> pcs</td>
                        </tr>
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-right">
                              <?php 
                                echo $PO_default_currency_symbol;
                              	echo number_format($subtotal, 2);
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Shipping</th>
                            <td class="text-right">
                              <?php 
                                echo $PO_default_currency_symbol;
                                echo number_format($handling_total, 2); 
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Handling</th>
                            <td class="text-right">
                              <?php 
                                echo $PO_default_currency_symbol; 
                                echo number_format($shipping_total, 2);
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <th>TOTAL DUE</th>
                            <td class="text-right">
                              <?php 
                            	echo $PO_default_currency_symbol; 
                            	echo number_format($grand_total, 2);; 
                              ?>
                            </td>
                        </tr>
                    </table>
                </div>
                
		<!-- END P.O. SUMMARY TABLE  -->
                
<?php 		
	if ($print_view == 0) { // UPDATE: Hide this for print view!
?>
		  </div>
		</div>
	</section>
	<!-- END PANEL - OTHER INSTRUCTIONS -->
<?php 
} // END SCREEN ONLY DATA
else {
	?>
		</td>
	</tr>
	</tbody>
	</table>
	<?php
} // end PRINT ONLY view
?>
	<!-- END P.O. SUMMARY / ORDER TOTALS $$$  -->
	

</div><!-- END P.O. ROW 6 -->

<!-- END OF TOTAL P.O. CONTENT -->


<br />
<?php 
if ($print_view == 0) { 
// DO NOT SHOW THIS SECTION ON THE PRINT VERSION!
?>
<hr />
<br />



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
					 
					<?php add_button($record_id, 'part_batch_add', 'PO_ID', 'Click here to add a new batch to this purchase order'); ?>

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
					    <td><a href="part_view.php?id=<?php echo $batch_part_ID; ?>"><?php 
					    if (($part_name_CN!='')&&($part_name_CN!='中文名')) {
					    	echo $part_name_CN; 
					    }
					    
					    ?></a></td>
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
							<th colspan="5">TOTAL: <?php echo $batch_count; ?></th>
							<th><?php echo number_format($movement_in_total); ?></th>
						  </tr>
					  </tfoot>

					 </table>
					 </div>


					 <?php add_button($record_id, 'part_batch_add', 'PO_ID', 'Click here to add a new batch to this purchase order'); ?>

								<!-- now close the panel -->
								</div>
							</section>
						</div>
					</div> <!-- end row! -->

<?php 
} // end of HIDE SECTION FOR PRINT VIEW

?>



					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php

if ($print_view == 0) {
	// now close the page out:
	pagefoot($page_id);
}
else {
	// show printable header here:
	?>
	</section>
	
	<!-- Vendor -->
			<script src="assets/vendor/jquery/jquery.js"></script>
			<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
			<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
			<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
			<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
			<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
			<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

			<!-- Specific Page Vendor (LIGHTBOX) -->
			<script src="assets/vendor/pnotify/pnotify.custom.js"></script>
			<!-- Examples (LIGHTBOX) -->
			<script src="assets/javascripts/ui-elements/examples.lightbox.js"></script>

			<!-- Specific Page Vendor - PROFILE -->
			<script src="assets/vendor/jquery-autosize/jquery.autosize.js"></script>

			<!-- Specific Page Vendor -->
		<script src="assets/vendor/pnotify/pnotify.custom.js"></script>

<!-- Examples -->
		<script src="assets/javascripts/ui-elements/examples.modals.js"></script>

		<!-- Specific Page Vendor - ADVANCED FORMS -->
		<script src="assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
		<script src="assets/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js"></script>
		<script src="assets/vendor/select2/select2.js"></script>
		<script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>
		<script src="assets/vendor/jquery-maskedinput/jquery.maskedinput.js"></script>
		<script src="assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
		<script src="assets/vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
		<script src="assets/vendor/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
		<script src="assets/vendor/fuelux/js/spinner.js"></script>
		<script src="assets/vendor/dropzone/dropzone.js"></script>
		<script src="assets/vendor/bootstrap-markdown/js/markdown.js"></script>
		<script src="assets/vendor/bootstrap-markdown/js/to-markdown.js"></script>
		<script src="assets/vendor/bootstrap-markdown/js/bootstrap-markdown.js"></script>
		<script src="assets/vendor/codemirror/lib/codemirror.js"></script>
		<script src="assets/vendor/codemirror/addon/selection/active-line.js"></script>
		<script src="assets/vendor/codemirror/addon/edit/matchbrackets.js"></script>
		<script src="assets/vendor/codemirror/mode/javascript/javascript.js"></script>
		<script src="assets/vendor/codemirror/mode/xml/xml.js"></script>
		<script src="assets/vendor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
		<script src="assets/vendor/codemirror/mode/css/css.js"></script>
		<script src="assets/vendor/summernote/summernote.js"></script>
		<script src="assets/vendor/bootstrap-maxlength/bootstrap-maxlength.js"></script>
		<script src="assets/vendor/ios7-switch/ios7-switch.js"></script>
		<script src="assets/vendor/bootstrap-confirmation/bootstrap-confirmation.js"></script>
		<script src="assets/vendor/intl-tel-input/js/intlTelInput.min.js"></script>

			<!-- Theme Base, Components and Settings -->
			<script src="assets/javascripts/theme.js"></script>

			<!-- Theme Custom -->
			<script src="assets/javascripts/theme.custom.js"></script>

			<!-- Theme Initialization Files -->
			<script src="assets/javascripts/theme.init.js"></script>

			<!--  Validations -->
			<script src="assets/vendor/jquery-validation/jquery.validate.js"></script>
			<script src="assets/javascripts/forms/examples.validation.js"></script>

		</section>
	</body>
</html>
		
	<?php
}

?>
	