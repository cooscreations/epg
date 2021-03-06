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
		<h2>Part Treatment</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><span>Part Treatment</span></li>
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
				<th class="text-center">Actions</th>
			</tr>
						  
						  <?php
								$get_part_treatment_SQL = "SELECT * FROM  `part_treatment` ORDER BY  `part_treatment`.`name_EN` ASC";
								
								$product_type_count = 0;
								
								$result_get_part_treatment = mysqli_query ( $con, $get_part_treatment_SQL );
								/* Product Type Details */
								while ( $row_get_part_treatment = mysqli_fetch_array ( $result_get_part_treatment ) ) {
								
									?>
						  
			<tr>
				<td><?php echo $row_get_part_treatment['name_EN']; ?></td>
				<td><?php echo $row_get_part_treatment['name_CN']; ?></td>
				<td><?php echo $row_get_part_treatment['description']; ?></td>
				<td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="part_treatment_edit.php?id=<?php echo $row_get_part_treatment['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="record_delete_do.php?table_name=part_treatment&src_page=part_treatment.php&id=<?php echo $row_get_part_treatment['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
			</tr>
						  
						  <?php
									
									$product_type_count = $product_type_count + 1;
								} // end while loop
								?>
						  
			 <tr>
				<th colspan="3">TOTAL: <?php echo $product_type_count; ?></th>
				<th class="text-center"><a href="part_treatment_add.php"
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