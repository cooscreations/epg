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

$page_id = 61;

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
						<h2>Product Category - <?php echo $product_category_name_EN;
						if (($product_category_name_CN!='')&&($product_category_name_CN!='中文名')) {
							echo " / " . $product_category_name_CN;
						} ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Product Category</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						  <!-- BASIC CATEGORY DETAILS -->
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Product Category Details:</h2>
								</header>
								<div class="panel-body">


								<div class="table-responsive">
									<table class="table table-bordered table-striped table-hover table-condensed mb-none">
									  <tr>
										<th>Name</th>
										<td><?php echo $product_category_name_EN;
											if (($product_category_name_CN!='')&&($product_category_name_CN!='中文名')) {
												echo " / " . $product_category_name_CN;
											} ?></td>
									  </tr>
									  <tr>
										<th>Category Code</th>
										<td><?php echo $product_category_code; ?></td>
									  </tr>
									</table>
								</div>

								</div>

								<footer class="panel-footer">
									&nbsp;
								</footer>

							</section>
						  <!-- END BASIC PRODUCT CATEGORY DETAILS -->


						  <!-- BASIC CATEGORY TYPES DETAILS -->
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Product Types in this Category</h2>
								</header>
								<div class="panel-body">


								<div class="table-responsive">
									<table class="table table-bordered table-striped table-hover table-condensed mb-none">
									  <tr>
										<th>Code</th>
										<th>Name - 中文名</th>
										<th># Products</th>
									  </tr>
									  <?php

									// get product types for this product category!
									$get_product_types_SQL = "SELECT * FROM `product_type` WHERE `product_cat_ID` = " . $record_id . " AND `record_status` = 2";
									$result_get_product_types = mysqli_query($con,$get_product_types_SQL);
									while($row_get_product_types = mysqli_fetch_array($result_get_product_types)) {

										$product_types_ID = $row_get_product_types['ID'];
										$product_type_code = $row_get_product_types['product_type_code'];
										$name_EN = $row_get_product_types['name_EN'];
										$name_CN = $row_get_product_types['name_CN'];
										$record_status = $row_get_product_types['record_status']; // should be 2 (active / published)
										$product_cat_ID = $row_get_product_types['product_cat_ID']; // should match record_id


										// count products for this category:
										$count_cat_products_sql = "SELECT COUNT( ID ) FROM  `products` WHERE `product_type_ID` = " . $product_types_ID;
										$count_cat_products_query = mysqli_query($con, $count_cat_products_sql);
										$count_cat_products_row = mysqli_fetch_row($count_cat_products_query);
										$total_cat_products = $count_cat_products_row[0];


									  ?>
										  <tr>
											<td><a href="product_type_view.php?id=<?php echo $product_types_ID; ?>"><?php echo $product_type_code; ?></a></td>
											<td><a href="product_type_view.php?id=<?php echo $product_types_ID; ?>"><?php echo $name_EN; if (($name_CN!='')&&($name_CN!='中文名')) { echo " / " . $name_CN; } ?></a></td>
											<td><a href="products.php?type_ID=<?php echo $product_types_ID; ?>"><?php echo $total_cat_products; ?></a></td>
										  </tr>
									  <?php
									  // end get product types for this category
									  }
									  ?>
									</table>
								</div>

								</div>

								<footer class="panel-footer">
									&nbsp;
								</footer>

							</section>
						  <!-- END BASIC PRODUCT CATEGORY TYPES DETAILS -->


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
