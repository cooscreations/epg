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
		<table class="table table-bordered table-striped table-condensed mb-none">
		  <thead>
			<tr>
				<th width="30%">Name</th>
				<th width="20%">Description</th>
				<th width="10%">Variants</th>
				<th width="10%">Suppliers</th>
				<th width="15%">Photo</th>
				<th class="text-center" width="10%">Actions</th>
			</tr>
		  </thead>
		  <tbody>

						  <?php
								$get_mats_SQL = "SELECT * FROM `material` WHERE `record_status` = '2' ORDER BY `material`.`name_EN` ASC";
								// echo $get_mats_SQL;

								$mat_count = 0;

								$result_get_mats = mysqli_query ( $con, $get_mats_SQL );
								// while loop
								while ( $row_get_mats = mysqli_fetch_array ( $result_get_mats ) ) {

									?>

			<tr>
				<td><a
					href="material_view.php?id=<?php echo $row_get_mats['ID']; ?>"><?php

					echo $row_get_mats['name_EN'];
					if (($row_get_mats['name_CN']!='')&&($row_get_mats['name_CN']!='中文名')) {
						echo ' / ' . $row_get_mats['name_CN'];
					}

					?></a></td>
				<td><?php echo $row_get_mats['description']; ?></td>
				<td>
					<!-- START VARIANTS -->
							<?php
									// count variants for this material
									$count_var_sql = "SELECT COUNT( ID ) FROM  `material_variant` WHERE  `material_ID` = " . $row_get_mats ['ID'];
									$count_var_query = mysqli_query ( $con, $count_var_sql );
									$count_var_row = mysqli_fetch_row ( $count_var_query );
									$total_var = $count_var_row [0];
									?>
							<a href="material_view.php?id=<?php echo $row_get_mats['ID']; ?>"><span
						<?php if ($total_var == 0) { ?> style="color: red;" <?php } ?>><?php echo $total_var; ?></span></a>
					<!-- END VARIANTS -->
				</td>
				<td><a
					href="suppliers.php?material_id=<?php echo $row_get_mats['ID']; ?>">VIEW</a>
				</td>
				<td><a
					href="material_view.php?id=<?php echo $row_get_users['ID']; ?>"><img
						src="assets/images/users/mat_<?php echo $row_get_mats['ID']; ?>.png"
						title="<?php echo $row_get_users['name_EN']; ?> / <?php echo $row_get_users['name_CN']; ?>"
						style="width: 100px;" /></a>
				</td>
				<td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="material_edit.php?id=<?php echo $row_get_mats['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="record_delete_do.php?table_name=material&src_page=materials.php&id=<?php echo $row_get_mats['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
			</tr>

						  <?php

									$mat_count = $mat_count + 1;
								} // end while loop
								?>
		  </tbody>
		  <tfoot>
			 <tr>
				<th colspan="6">TOTAL: <?php echo $mat_count; ?></th>
				<th class="text-center"><a href="material_add.php"
					class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a></th>
			</tr>
		  </tfoot>
		</table>
	</div>
	<!-- end: page -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
