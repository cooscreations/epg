<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
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

// pull the header and template stuff:
pagehead();

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else if (isset($_REQUEST['MATERIAL_ID'])) {
	$record_id = $_REQUEST['MATERIAL_ID'];
}

if ($record_id != 0) {
							$get_materials_SQL = "SELECT * FROM `material` WHERE `ID` =".$record_id;
					  		// echo $get_parts_SQL;

					  		$result_get_materials = mysqli_query($con,$get_materials_SQL);
					  		// while loop
					  		while($row_get_materials = mysqli_fetch_array($result_get_materials)) {

								$material_ID = $row_get_materials['ID'];
								$material_name_EN = $row_get_materials['name_EN'];
								$material_name_CN = $row_get_materials['name_CN'];
								$material_description = $row_get_materials['description'];



					  		}
}

?>
					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="material_variant_add_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Add Material Variant Record Details:</h2>
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

											<option value="<?php echo $list_material_id; ?>"<?php if ($record_id == $list_material_id) { ?> selected=""<?php } ?>><?php echo $row_get_materials_list['name_EN']; ?> / <?php echo $row_get_materials_list['name_CN']; ?></option>

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
											<input type="text" class="form-control" id="inputDefault" placeholder="" name="name_EN" />
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">名字:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" placeholder="" name="name_CN" value="中文名" />
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">Description:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" placeholder="" name="description" />
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">Color:</label>
										<div class="col-md-5">
											<div class="input-group myColorPicker colorpicker-component colorpicker-element">
												<input type="text" class="form-control" placeholder="#00AABB" name="color_code">
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
										<?php
										if (isset($_REQUEST['MATERIAL_ID'])) {
											?>
											<input type="hidden" value="<?php echo $_REQUEST['MATERIAL_ID']; ?>" name="MATERIAL_ID" />
											<?php
										}
										?>
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

<?php
// now close the page out:
pagefoot($page_id);

?>
