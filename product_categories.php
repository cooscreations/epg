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

$page_id = 60; // in theory we shouldn't need to hard code this anymore...

// pull the header and template stuff:
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Product Categories</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><span>Product Categories</span></li>
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
				<th class="text-left" colspan="3">
					<a href="product_category_add.php" class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a>
				</th>
			</tr>
			
			<tr>
				<th>Name / 名字</th>
				<th>Category Code</th>
				<th class="text-center">Actions</th>
			</tr>
						  
						  <?php
								$get_product_categories_SQL = "SELECT * FROM  `product_categories` where `record_status` = 2 ORDER BY  `product_categories`.`name_EN` ASC";
								
								$product_categories_count = 0;
								
								$result_get_product_categories = mysqli_query ( $con, $get_product_categories_SQL );
								/* Part Classification Details */
								while ( $row_get_product_categories = mysqli_fetch_array ( $result_get_product_categories ) ) {
								
									?>
						  
			<tr>
				<td>
				  <a href="product_category_view.php?id=<?php echo $row_get_product_categories['ID']; ?>">
					<?php echo $row_get_product_categories['name_EN']; if (($row_get_product_categories['name_CN']!='')&&($row_get_product_categories['name_CN']!='中文名')) { echo " / " . $row_get_product_categories['name_CN']; } ?>
				  </a>
				</td>
				<td>
				  <a href="product_category_view.php?id=<?php echo $row_get_product_categories['ID']; ?>">
				  	<?php echo $row_get_product_categories['cat_code']; ?>
				  </a>
				</td>
				<td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="product_category_view.php?id=<?php echo $row_get_product_categories['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-info"><i class="fa fa-eye"></i></a>
                    <a href="product_category_edit.php?id=<?php echo $row_get_product_categories['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="record_delete_do.php?table_name=product_categories&src_page=product_categories.php&id=<?php echo $row_get_product_categories['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
			</tr>
						  
						  <?php
									
									$product_categories_count = $product_categories_count + 1;
								} // end while loop
								?>
						  
			 <tr>
				<th class="text-left" colspan="2">
					<a href="product_category_add.php" class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a>
				</th>
				<th>
					TOTAL: <?php echo $product_categories_count; ?>
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