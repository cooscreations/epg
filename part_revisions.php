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
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 99;

// pull the header and template stuff:
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Part Revisions</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><span>Part Revisions</span></li>
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
		<?php
		add_button(0, 'part_revision_add');
		?>
		
	<div class="table-responsive">
		<table
			class="table table-bordered table-striped table-condensed mb-none">
			<tr>
				<th class="text-center">ID</th>
				<th class="text-center">Code</th>
				<th>Part Name</th>
				<th class="text-center">Rev. #</th>
				<th>Remarks</th>
				<th class="text-center">Date Approved</th>
				<th class="text-center">User</th>
				<th class="text-center">Price</th>
				<th class="text-center">Weight</th>
				<th class="text-center">Status</th>

				<th class="text-center"><span class="btn btn-xs btn-default" title="Action"><i class="fa fa-gear"></i></span></th>
			</tr>
						  <?php
								$get_part_revisions_SQL = "SELECT `part_revisions`.`ID`, `parts`.`name_EN` as `part_name_EN` " .
															" , `parts`.`name_CN` as `part_name_CN` " .
															" , `parts`.`ID` as `part_ID` " .
															" , `parts`.`part_code` " .
															" , `part_revisions`.`revision_number` " .
															" , `part_revisions`.`remarks` ".
															" , `part_revisions`.`date_approved` ".
															" , `users`.`ID` as `user_ID` ".
															" , concat(`users`.`first_name`,' ',`users`.`last_name`) as `user_name` ".
															" , `part_revisions`.`price_USD` ".
															" , `part_revisions`.`weight_g` ".
															" , `part_status`.`name_EN` as `status_name_EN` ".
															" FROM `part_revisions`, `parts`, `part_status`, `users`  WHERE ".
															" `part_revisions`.`part_ID` = `parts`.`ID` " .
															" AND `part_revisions`.`status_ID` = `part_status`.`ID` ".
															" AND `part_revisions`.`user_ID` = `users`.`ID` ".
															" AND `part_revisions`.`record_status` = 2 ".
															" ORDER BY `part_revisions`.`ID` ASC";

								$part_revision_count = 0;

								$result_get_part_revision = mysqli_query ( $con, $get_part_revisions_SQL );
								while ( $row_get_part_revision = mysqli_fetch_array ( $result_get_part_revision ) ) {

									?>

			<tr>
				<td class="text-center"><?php echo $row_get_part_revision['ID']; ?></td>
				<td class="text-center"><a href="part_view.php?id=<?php echo $row_get_part_revision['part_ID']; ?>" class="btn btn-xs btn-info"><?php echo $row_get_part_revision['part_code']; ?></a></td>
				<td><a href="part_view.php?id=<?php echo $row_get_part_revision['part_ID']; ?>"><?php echo $row_get_part_revision['part_name_EN']; if (($row_get_part_revision['part_name_CN']!='')&&($row_get_part_revision['part_name_CN']!='中文名')) { echo " / " . $row_get_part_revision['part_name_CN']; }?></a></td>
				<td class="text-center"><span class="btn btn-xs btn-warning" title="Rev #: <?php echo $row_get_part_revision['ID']; ?>"><?php echo $row_get_part_revision['revision_number']; ?></span></td>
				<td><?php echo $row_get_part_revision['remarks']; ?></td>
				<td class="text-center"><?php echo date("Y-m-d", strtotime($row_get_part_revision['date_approved'])); ?></td>
				<td class="text-center"><a href="user_view.php?id=<?php echo $row_get_part_revision['user_ID']; ?>"><?php echo $row_get_part_revision['user_name']; ?></a></td>
				<td class="text-center"><?php echo $row_get_part_revision['price_USD']; ?></td>
				<td class="text-center"><?php echo $row_get_part_revision['weight_g']; ?></td>
				<td class="text-center"><?php echo $row_get_part_revision['status_name_EN']; ?></td>
				<td class="text-center">
                    <a href="part_revision_edit.php?id=<?php echo $row_get_part_revision['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="record_delete_do.php?table_name=part_revisions&src_page=part_revisions.php&id=<?php echo $row_get_part_revision['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
			</tr>

						  <?php

									$part_revision_count = $part_revision_count + 1;
								} // end while loop
								?>

			 <tr>
				<th colspan="11" class="text-left">TOTAL: <?php echo $part_revision_count; ?></th>
			</tr>


		</table>
	</div>
	
	<?php
		add_button(0, 'part_revision_add');
		?>
	
	<!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
