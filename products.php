<?php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */
session_start (); /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// now check the user is OK to view this page //
/* //////// require ('page_access.php'); / */
// ///*/
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////

header ( 'Content-Type: text/html; charset=utf-8' );
require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 3;

// pull the header and template stuff:
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Products</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><span>Products</span></li>
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
	<div class="table-responsive">
		<table
			class="table table-bordered table-striped table-condensed mb-none">
			<tr>
				<th>Name</th>
				<th>名字</th>
				<th>Description</th>
				<th>Product Type</th>
				<th class="text-center">Actions</th>
			</tr>
						  
						  <?php
								$get_products_SQL = "SELECT * FROM  `products` ORDER BY  `products`.`name_EN` ASC";
								
								$product_count = 0;
								
								$result_get_products = mysqli_query ( $con, $get_products_SQL );
								/* Product Details */
								while ( $row_get_products = mysqli_fetch_array ( $result_get_products ) ) {
								
									/* Product Type Details */
									$get_product_type_SQL = "SELECT * FROM  `product_type` WHERE  `product_type`.`ID` = " . $row_get_products['product_type_ID'];
									$result_get_product_type = mysqli_query ( $con, $get_product_type_SQL );
									$row_get_product_type = mysqli_fetch_array ( $result_get_product_type );
									$product_type_code = $row_get_product_type['product_type_code'];
									
									?>
						  
			<tr>
				<td><a
					href="product_view.php?id=<?php echo $row_get_products['ID']; ?>"><?php echo $row_get_products['name_EN']; ?></a>
				</td>
				<td><a
					href="product_view.php?id=<?php echo $row_get_products['ID']; ?>"><?php echo $row_get_products['name_CN']; ?></a>
				</td>
				<td><?php echo $row_get_products['description']; ?></td>
				<td><a
					href="product_type_view.php?id=<?php echo $row_get_products['product_type_ID']; ?>"><?php echo $product_type_code; ?></a>
				</td>
				<td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="product_edit.php?id=<?php echo $row_get_products['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="product_delete_do.php?id=<?php echo $row_get_products['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
			</tr>
						  
						  <?php
									
									$product_count = $product_count + 1;
								} // end while loop
								?>
						  
			 <tr>
				<th colspan="4">TOTAL: <?php echo $product_count; ?></th>
				<th class="text-center"><a href="product_add.php"
					class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a></th>
			</tr>


		</table>
	</div>
	<!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>