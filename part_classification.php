<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
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
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

// pull the header and template stuff:
pagehead();
?>
	<!-- start: page -->
	<div class="table-responsive">
		<table
			class="table table-bordered table-striped table-condensed mb-none">
			<tr>
				<th>Name</th>
				<th>名字</th>
				<th>Description</th>
				<th>Color</th>
				<th class="text-center">Actions</th>
			</tr>

						  <?php
								$get_part_classification_SQL = "SELECT * FROM  `part_classification` ORDER BY  `part_classification`.`name_EN` ASC";

								$part_classification_count = 0;

								$result_get_part_classification = mysqli_query ( $con, $get_part_classification_SQL );
								/* Part Classification Details */
								while ( $row_get_part_classification = mysqli_fetch_array ( $result_get_part_classification ) ) {

									?>

			<tr>
				<td><?php echo $row_get_part_classification['name_EN']; ?></td>
				<td><?php echo $row_get_part_classification['name_CN']; ?></td>
				<td><?php echo $row_get_part_classification['description']; ?></td>
				<td><?php echo $row_get_part_classification['color']; ?></td>
				<td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="part_classification_edit.php?id=<?php echo $row_get_part_classification['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="record_delete_do.php?table_name=part_classification&src_page=part_classification.php&id=<?php echo $row_get_part_classification['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
			</tr>

						  <?php

									$part_classification_count = $part_classification_count + 1;
								} // end while loop
								?>

			 <tr>
				<th colspan="4">TOTAL: <?php echo $part_classification_count; ?></th>
				<th class="text-center"><a href="part_classification_add.php"
					class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a></th>
			</tr>


		</table>
	</div>
	<!-- end: page -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
