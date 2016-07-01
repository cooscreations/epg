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

$page_id = 99;

// pull the header and template stuff:
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Product Type</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><span>Product Type</span></li>
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
		<table class="table table-bordered table-striped table-condensed mb-none">
		  
			 <tr>
				<th class="text-left" colspan="5">
					<a href="product_type_add.php" class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a>
				</th>
			</tr>
			
			<tr>
				<th>Code</th>
				<th>Name / 名字</th>
				<th>Product Category Code</th>
				<th>Product Category / 产品分类</th>
				<th class="text-center">Actions</th>
			</tr>
						  
						  <?php
								$get_product_types_SQL = "SELECT * FROM  `product_type` where `record_status` = 2 ORDER BY  `product_type`.`product_type_code` ASC";
								
								$product_type_count = 0;
								
								$result_get_product_types = mysqli_query ( $con, $get_product_types_SQL );
								/* Product Type Details */
								while ( $row_get_product_types = mysqli_fetch_array ( $result_get_product_types ) ) {
								
									/* Product Category  */
									$get_product_category_SQL = "SELECT * FROM  `product_categories` WHERE  `product_categories`.`ID` = " . $row_get_product_types['product_cat_ID'];
									$result_get_product_category = mysqli_query ( $con, $get_product_category_SQL );
									while ( $row_get_product_category = mysqli_fetch_array ( $result_get_product_category ) ) {	
										$product_category_ID = $row_get_product_category['ID'];
										$product_category_name_EN = $row_get_product_category['name_EN'];
										$product_category_name_CN = $row_get_product_category['name_CN'];
										$product_category_code = $row_get_product_category['cat_code'];
										$product_category_record_status = $row_get_product_category['record_status'];
									} // end while loop
									
								?>
						  
			<tr>
				<td>
				  <a href="product_type_view.php?id=<?php echo $row_get_product_types['ID']; ?>">
					<?php echo $row_get_product_types['product_type_code']; ?>
				  </a>
				</td>
				<td>
				  <a href="product_type_view.php?id=<?php echo $row_get_product_types['ID']; ?>">
					<?php echo $row_get_product_types['name_EN']; if (($row_get_product_types['name_CN']!='')&&($row_get_product_types['name_CN']!='中文名')) { echo " / " . $row_get_product_types['name_CN']; } ?>
				  </a>
				</td>
				<td>
				  <a href="product_category_view.php?id=<?php echo $product_category_ID; ?>">
					<?php echo $product_category_code; ?>
				  </a>	
				</td>
				<td>
				  <a href="product_category_view.php?id=<?php echo $product_category_ID; ?>">
					<?php echo $product_category_name_EN; if (($product_category_name_CN!='')&&($product_category_name_CN!='中文名')) { echo " / " . $product_category_name_CN }?>
				  </a>	
				</td>
				<td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="product_type_view.php?id=<?php echo $row_get_product_types['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-info"><i class="fa fa-eye"></i></a>
                    <a href="product_type_edit.php?id=<?php echo $row_get_product_types['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="record_delete_do.php?table_name=product_type&src_page=product_types.php&id=<?php echo $row_get_product_types['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
			</tr>
						  
						  <?php
									
									$product_type_count = $product_type_count + 1;
								} // end while loop
								?>
						  
			 <tr>
				<th class="text-left" colspan="4">
					<a href="product_type_add.php" class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a>
				</th>
				<th class="text-right">
					TOTAL: <?php echo $product_type_count; ?>
				</th>
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