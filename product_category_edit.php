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

$page_id = 58;

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else {	
	header("Location: product_categories.php?msg=NG&action=view&error=no_id");
	exit();		
}

// all OK - continue...
$get_product_categories_SQL = "SELECT * FROM `product_categories` WHERE `ID` =".$record_id;

$result_get_product_categories = mysqli_query($con,$get_product_categories_SQL);
while($row_get_product_categories = mysqli_fetch_array($result_get_product_categories)) {
		
	$product_category_ID = $row_get_product_categories['ID'];
	$product_category_name_EN = $row_get_product_categories['name_EN'];
	$product_category_name_CN = $row_get_product_categories['name_CN'];
	$product_category_code = $row_get_product_categories['cat_code'];
}

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Part Type <?php if ($record_id != 0) { ?> <? echo $product_category_name_EN . " / " . $product_category_name_CN; } ?></h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="product_categories.php">All Part Type</a>
									</li>
								<li><span>Edit Product Category Record</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					<div class="row">
						<div class="col-md-12">
						
						<!-- START THE FORM! -->
						<form id="form" class="form-horizontal form-bordered" action="product_category_edit_do.php" method="post">
						
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Edit Product Category Record Details:</h2>
								</header>
								<div class="panel-body">
								
									<div class="form-group">
										<label class="col-md-3 control-label">Name:<span class="required">*</span></label>
										<div class="col-md-5">
											<input type="text" class="form-control" name="name_EN"  value="<?php echo $product_category_name_EN; ?>" required/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									<div class="form-group">
										<label class="col-md-3 control-label">名字:<span class="required">*</span></label>
										<div class="col-md-5">
											<input type="text" class="form-control" name="name_CN" value="中文名"  value="<?php echo $product_category_name_CN; ?>" required/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									<div class="form-group">
										<label class="col-md-3 control-label">Category Code:<span class="required">*</span></label>
										<div class="col-md-5">
											<input type="text" class="form-control" name="cat_code" value="<?php echo $product_category_code; ?>" required/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
								</div>
								
								<footer class="panel-footer">
										<input type="hidden" value="<?php echo $product_category_ID; ?>" name="id" />
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