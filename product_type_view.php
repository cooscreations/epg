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

$page_id = 62;

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
		$product_type_record_status = $row_get_product_type['record_status'];

	}

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Product Type - <?php echo $product_type_name_EN; if (($product_type_name_CN!='')&&($product_type_name_CN!='中文名')) { echo " / " . $product_type_name_CN; } ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="products.php">All Products</a>
									</li>
									<li>
										<a href="product_types.php">All Product Type</a>
									</li>
								<li><span>Product Type Profile</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- BASIC TYPE DETAILS -->
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Product Type Details:</h2>
								</header>
								<div class="panel-body">

								<div class="table-responsive">
									<table class="table table-bordered table-striped table-hover table-condensed mb-none">
									  <tr>
										<th>Name / 名字</th>
										<td>
											<?php
												echo $product_type_name_EN;
												if (($product_type_name_CN!='')&&($product_type_name_CN!='中文名')) {
													echo " / " . $product_type_name_CN;
												}
											?>
										</td>
									  </tr>
									  <tr>
										<th>Code</th>
										<td>
											<?php echo $product_type_type_code; ?>
										</td>
									  </tr>
									</table>
								</div>

								<footer class="panel-footer">
									&nbsp;
								</footer>

							</section>
						  <!-- END BASIC PRODUCT TYPE DETAILS -->

						<!-- ****************************************************************** -->



						<!-- PRODUCTS OF THIS PRODUCT TYPE -->
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Products Of This Product Type:</h2>
								</header>
								<div class="panel-body">

								<div class="table-responsive">
									<table class="table table-bordered table-striped table-hover table-condensed mb-none">

									  <tr>
										<th>Code</th>
										<th>Name</th>
										<th>中文名</th>
										<th>Description</th>
										<th>Variant</th>
										<th>ACTIONS</th>
									  </tr>


									  <?php

									  		$total_product_count = 0;

									  		$get_products_SQL = "SELECT * FROM `products` WHERE  `product_type_ID` = " . $record_id . " AND `record_status` =2";

											$result_get_products = mysqli_query($con,$get_products_SQL);
											// while loop
											while($row_get_products = mysqli_fetch_array($result_get_products)) {

												$list_product_id = $row_get_products['ID'];


												$list_product_id = $row_get_products['ID'];
												$list_product_name_EN = $row_get_products['name_EN'];
												$list_product_name_CN = $row_get_products['name_CN'];
												$list_product_description = $row_get_products['description'];
												$list_product_type_id = $row_get_products['product_type_ID']; // should match the record_id
												$list_product_record_status = $row_get_products['record_status']; // should be 2
												$list_product_code = $row_get_products['product_code'];
												$list_product_material_variant_ID = $row_get_products['material_variant_ID']; // look this up!

												$total_product_count = $total_product_count+1;
											?>

									  <tr>
										<td><?php echo $list_product_code; ?></td>
										<td><?php echo $list_product_name_EN; ?></td>
										<td><?php echo $list_product_name_CN; ?></td>
										<td><?php echo $list_product_description; ?></td>
										<td><?php echo $list_product_material_variant_ID;?> (lookup)</td>
										<td>
											<a href="product_view.php?id=<?php echo $list_product_id; ?>" class="btn btn-info">
												<i class="fa fa-eye"></i>
											</a>
											E
											D</td>
									  </tr>

									  <?php
											} // END WHILE LOOP

											?>


									  <tr>
										<td colspan="5">ADD</td>
										<th>Total Rows: <?php echo $total_product_count; ?></th>
									  </tr>




									</table>
								</div>

								<footer class="panel-footer">
									&nbsp;
								</footer>

							</section>
						  <!-- END PRODUCTS OF THIS PRODUCT TYPE -->

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
