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
	$get_po_SQL = "SELECT `ID`,`PO_number`, date(`created_date`) as 'created_date', `description`  FROM  `purchase_orders` WHERE `ID` = " . $record_id;
	$result_get_po = mysqli_query($con,$get_po_SQL);
	// while loop
	while($row_get_po = mysqli_fetch_array($result_get_po)) {
		
			// now print each record:  
			$po_id = $row_get_po['ID'];
			$po_number = $row_get_po['PO_number'];
			$po_created_date = $row_get_po['created_date'];
			$po_description = $row_get_po['description'];
			
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
									<label class="col-md-3 control-label">P.O. Number:</label>
									<div class="col-md-5">
										<input type="text" class="form-control" id="inputDefault" placeholder="PO#" name="po_number" value="<?php echo $po_number; ?>"/>
										<input type="hidden" name="po_id" value="<?php echo $po_id; ?>"/>
									</div>
									
									<div class="col-md-1">
										&nbsp;
									</div>
								</div>	
											
											<div class="form-group">
												<label class="col-md-3 control-label">Description:</label>
												<div class="col-md-5">
													<textarea class="form-control" rows="3" id="textareaDefault" name="description"><?php echo $po_description; ?></textarea>
												</div>
												
									
												<div class="col-md-1">
													&nbsp;
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Created Date:</label>
												<div class="col-md-5">
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</span>
														<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_added" value="<?php echo $po_created_date; ?>">
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