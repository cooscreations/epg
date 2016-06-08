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

$page_id = 45;

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else {	
	header("Location: product_types.php?msg=NG&action=view&error=no_id");
	exit();		
}

	
	/* Product Type details by ID */
	$get_product_type_SQL = "SELECT * FROM `product_type` WHERE `ID` =".$record_id;

	$result_get_product_type = mysqli_query($con,$get_product_type_SQL);

	while($row_get_product_type = mysqli_fetch_array($result_get_product_type)) {
			
		$product_type_ID = $row_get_product_type['ID'];
		$product_type_type_code = $row_get_product_type['product_type_code'];
		$product_type_name_EN = $row_get_product_type['name_EN'];
		$product_type_name_CN = $row_get_product_type['name_CN'];
		$product_type_description = $row_get_product_type['description'];
		$product_type_status = $row_get_product_type['status'];
	}
	
// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Product Type <?php if ($record_id != 0) { ?> <? echo $product_type_name_EN . " / " . $product_type_name_CN; } ?></h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="product_types.php">All Product Type</a>
									</li>
								<li><span>Edit Product Type Record</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					<div class="row">
						<div class="col-md-12">
						
						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="product_type_edit_do.php" method="post">
						
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Edit Product Type Record Details:</h2>
								</header>
								<div class="panel-body">
								
									<div class="form-group">
										<label class="col-md-3 control-label">Code:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="product_type_code"  value="<?php echo $product_type_type_code; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-md-3 control-label">Name:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="name_EN"  value="<?php echo $product_type_name_EN; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									<div class="form-group">
										<label class="col-md-3 control-label">名字:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="name_CN" value="中文名"  value="<?php echo $product_type_name_CN; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-md-3 control-label">Name:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="product_type_status"  value="<?php echo $product_type_status; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
					 
								</div>
								
								
								<footer class="panel-footer">
										<input type="hidden" value="<?php echo $product_type_ID; ?>" name="id" />
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