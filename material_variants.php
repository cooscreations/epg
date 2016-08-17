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

$page_id = 20;

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Material Variants</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span><a href="materials.php">Materials</a></span></li>
								<li><span>Material Variants</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-condensed mb-none">
					  <tr>
					    <th>Name</th>
					    <th>中文名</th>
					    <th>Description</th>
					    <th>Material</th>
					    <th>Variant Type</th>
					    <th>Code</th>
					  </tr>

					  <?php
					  $get_mat_vars_SQL = "SELECT * FROM  `material_variant`";
					  // echo $get_mat_vars_SQL;

					  $mat_vars_count = 0;

					  $result_get_mat_vars = mysqli_query($con,$get_mat_vars_SQL);
					  // while loop
					  while($row_get_mat_vars = mysqli_fetch_array($result_get_mat_vars)) {



							$mat_var_ID = $row_get_mat_vars['ID'];
							$mat_var_material_ID = $row_get_mat_vars['material_ID'];
							$mat_var_variant_type = $row_get_mat_vars['variant_type'];
							$mat_var_name_EN = $row_get_mat_vars['name_EN'];
							$mat_var_name_CN = $row_get_mat_vars['name_CN'];
							$mat_var_description = $row_get_mat_vars['description'];
							$mat_var_code = $row_get_mat_vars['code'];

					  ?>

					  <tr>
					  	<td><a href="material_variant_view.php?id=<?php echo $mat_var_ID; ?>"><?php echo $mat_var_name_EN; ?></a></td>
					  	<td><a href="material_variant_view.php?id=<?php echo $mat_var_ID; ?>"><?php echo $mat_var_name_CN; ?></a></td>
					  	<td><a href="material_variant_view.php?id=<?php echo $mat_var_ID; ?>"><?php echo $mat_var_description; ?></a></td>
					  	<td><a href="material_view.php?id=<?php echo $mat_var_material_ID; ?>"><?php echo $mat_var_material_ID; ?></a></td>
					  	<td><a href="material_variant_view.php?id=<?php echo $mat_var_ID; ?>"><?php echo $mat_var_variant_type; ?></a></td>
					  	<td><a href="material_variant_view.php?id=<?php echo $mat_var_ID; ?>"><?php echo $mat_var_code; ?></a></td>
					  </tr>

					  <?php

					  $mat_vars_count = $mat_vars_count + 1;

					  } // end while loop
					  ?>

					  <tr>
					    <th colspan="5">TOTAL: <?php echo $mat_vars_count; ?></th>
					    <th><a href="material_variant_add.php" class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a></th>
					  </tr>


					 </table>
					</div>
					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
