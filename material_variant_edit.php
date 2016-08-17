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

$page_id = 21;

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: material_variants.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {

	/* MAterial Variant details */
	$get_material_variant_SQL = "SELECT * FROM `material_variant` WHERE `ID` =".$record_id;

	$result_get_material_variant = mysqli_query($con,$get_material_variant_SQL);
	// while loop
	while($row_get_material_variant = mysqli_fetch_array($result_get_material_variant)) {

		$material_variant_ID = $row_get_material_variant['ID'];
		$material_variant_type_ID = $row_get_material_variant['variant_type'];
		$material_ID = $row_get_material_variant['material_ID'];
		$material_variant_name_EN = $row_get_material_variant['name_EN'];
		$material_variant_name_CN = $row_get_material_variant['name_CN'];
		$material_variant_description = $row_get_material_variant['description'];
		$material_variant_color_code = $row_get_material_variant['code'];
	echo $material_variant_ID;
	}

	/* Material Details */
		$get_materials_SQL = "SELECT * FROM `material` WHERE `ID` =".$material_ID;
  		// echo $get_parts_SQL;

  		$result_get_materials = mysqli_query($con,$get_materials_SQL);
  		// while loop
  		while($row_get_materials = mysqli_fetch_array($result_get_materials)) {

			$material_ID = $row_get_materials['ID'];
			$material_name_EN = $row_get_materials['name_EN'];
			$material_name_CN = $row_get_materials['name_CN'];
			$material_description = $row_get_materials['description'];

  		}
  	/* Material Variant Type Details */
  		$get_material_variant_type_SQL = "SELECT * FROM `material_variant_types` WHERE `ID` =".$material_variant_type_id;

  		$result_get_material_variant_type = mysqli_query($con,$get_material_variant_type_SQL);
  		// while loop
  		while($row_get_material_variant_type = mysqli_fetch_array($result_get_material_variant_type)) {

  			$material_variant_type_name_EN = $row_get_material_variant_type['name_EN'];
  			$material_variant_type_name_CN = $row_get_material_variant_type['name_CN'];
  			$material_variant_type_description = $row_get_material_variant_type['description'];

  		}
}

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Material Variant<?php if ($record_id != 0) { ?> <? echo $material_name_EN . " / " . $material_name_CN; } ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="materials.php">All Materials</a>
									</li>
								<?php
								if ($material_ID != 0) {
									?>
									<li>
										<a href="material_view.php?id=<?php echo $material_ID; ?>">Material Record</a>
									</li>
									<?php
								} ?>
								<li><span>Edit Material Variant Record</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="material_variant_edit_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Edit Material Variant Record Details:</h2>
								</header>
								<div class="panel-body">

									<div class="form-group">
										<label class="col-md-3 control-label">Material:</label>
										<div class="col-md-5">
											<select data-plugin-selectTwo class="form-control populate" name="material_ID">
											<?php
											// get materials list
											$get_materials_list_SQL = "SELECT * FROM `material` ORDER BY `name_EN` ASC";

											$result_get_materials_list = mysqli_query($con,$get_materials_list_SQL);
											// while loop
											while($row_get_materials_list = mysqli_fetch_array($result_get_materials_list)) {

												$list_material_id = $row_get_materials_list['ID'];


											?>

											<option value="<?php echo $list_material_id; ?>"<?php if ($material_ID == $list_material_id) { ?> selected=""<?php } ?>><?php echo $row_get_materials_list['name_EN']; ?> / <?php echo $row_get_materials_list['name_CN']; ?></option>

											<?php
											} // END WHILE LOOP

											?>
											</select>
										</div>
										<div class="col-md-1">
											<a href="material_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Variant Type:</label>
										<div class="col-md-5">
											<select data-plugin-selectTwo class="form-control populate" name="material_variant_type_ID">
											<?php
											// get material variant types list
											$get_material_variant_type_list_SQL = "SELECT * FROM `material_variant_types` ORDER BY `name_EN` ASC";

											$result_get_material_variant_type_list = mysqli_query($con,$get_material_variant_type_list_SQL);
											// while loop
											while($row_get_material_variant_type_list = mysqli_fetch_array($result_get_material_variant_type_list)) {

												$list_material_variant_type_id = $row_get_material_variant_type_list['ID'];


											?>

											<option value="<?php echo $list_material_variant_type_id; ?>"><?php echo $row_get_material_variant_type_list['name_EN']; ?> / <?php echo $row_get_material_variant_type_list['name_CN']; ?></option>

											<?php
											} // END WHILE LOOP

											?>
											</select>
										</div>
										<div class="col-md-1">
											<a href="material_variant_type_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Name:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="name_EN"  value="<?php echo $material_variant_name_EN; ?>"/>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">名字:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="name_CN" value="中文名"  value="<?php echo $material_variant_name_CN; ?>"/>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">Description:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault"  name="description" value="<?php echo $material_variant_description; ?>"/>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">Color:</label>
										<div class="col-md-5">
											<div class="input-group myColorPicker colorpicker-component colorpicker-element">
												<input type="text" class="form-control"  name="color_code" value="<?php echo $material_variant_color_code; ?>">
												<span class="input-group-addon" >
													<i style="background-color: rgb(54, 26, 46);" ></i>
												</span>
											</div>
										</div>
										<script>
											$(function(){
												$('.myColorPicker').colorpicker();
											});
										</script>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>



								</div>


								<footer class="panel-footer">
										<input type="hidden" value="<?php echo $material_variant_ID; ?>" name="id" />
										<input type="hidden" value="<?php echo $material_variant_type_ID; ?>" name="MATERIAL_VARIANT_TYPE_ID" />
										<input type="hidden" value="<?php echo $material_ID; ?>" name="MATERIAL_ID" />
										<button type="submit" class="btn btn-success">Submit </button>
										<button type="reset" class="btn btn-default">Reset</button>
									</footer>
							</section>
										<!-- now close the form -->
										</form>


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
