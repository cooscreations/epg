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

/* -- NO USER SESSIONS YET...
if (isset($_SESSION['user_id'])) {
	header("Location: user_home.php"); // send them to the user home...
}
*/

$page_id = 20;

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else {	
	header("Location: materials.php?msg=NG&action=view&error=no_id");
	exit();		
}

$get_material_by_id_SQL = "SELECT * FROM  `material` WHERE `ID` = " . $record_id;
$result_get_material = mysqli_query($con,$get_material_by_id_SQL);
// while loop
while($row_get_material = mysqli_fetch_array($result_get_material)) {
	
		// now print each record:  
		$Material_id = $row_get_material['ID'];
		$Material_name_en= $row_get_material['name_EN'];
		$Material_name_cn = $row_get_material['name_CN'];
		$Material_description = $row_get_material['description'];
		
		// count material variants for this material.
        $count_variants_sql = "SELECT COUNT( ID ) FROM  `material_variant` WHERE  `MATERIAL_ID` = " . $record_id;
        $count_variants_query = mysqli_query($con, $count_variants_sql);
        $count_variants_row = mysqli_fetch_row($count_variants_query);
        $total_variants = $count_variants_row[0];
		
} // end while loop

// pull the header and template stuff:
pagehead($page_id); 

?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Material Record</h2>
					
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
								<li><span>Material Record</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					<?php 
					
					// run notifications function:
					$msg = 0;
					if (isset($_REQUEST['msg'])) { $msg = $_REQUEST['msg']; }
					$action = 0;
					if (isset($_REQUEST['action'])) { $action = $_REQUEST['action']; }
					$change_record_id = 0;
					if (isset($_REQUEST['new_record_id'])) { $change_record_id = $_REQUEST['new_record_id']; }
					$page_record_id = 0;
					if (isset($record_id)) { $page_record_id = $record_id; }
					
					// now run the function:
					notify_me($page_id, $msg, $action, $change_record_id, $page_record_id);
					?>
					
					<div class="row">
						<div class="col-md-6">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Main Details:</h2>
								</header>
								<div class="panel-body">
									
									
									<div class="table-responsive">
					 					<table class="table table-bordered table-striped table-hover table-condensed mb-none">
					 					  <tr>
					 					    <th>Name:</th>
					 					    <td><?php echo $Material_name_en; ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>名字:</th>
					 					    <td><?php echo $Material_name_cn; ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Description:</th>
					 					    <td><?php echo $Material_description; ?></td>
					 					  </tr>
					 					  <tr>
					 					    <th>Total Variants:</th>
					 					    <td><?php echo $total_variants; ?> (see below)</td>
					 					  </tr>
					 					</table>
					 				</div>
									
								</div>
							</section>
						</div>
						
						
						
						<div class="col-md-6">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Supplier Details:</h2>
								</header>
								<div class="panel-body">
									
									<ul>
									  <li>Coming soon...</li>
									</ul>
									
								</div>
							</section>
						</div>
						
						
					</div>
					
					
					<div class="row">
					
					<div class="col-md-12">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Variants of this Material:</h2>
								</header>
								<div class="panel-body">
								
					 <div class="row">
						<div id="feature_buttons_container_id" class="col-md-11">
						</div>
						<div class="col-md-1">
							<a href="material_variant_add.php?MATERIAL_ID=<?php echo $_REQUEST['id']; ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
						</div>	
					 </div>
					 
					
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
					 <thead>
						  <tr>
							<th>Variant Type</th>
							<th>Name</th>
							<th>名字</th>
							<th>Description</th>
							<th>Code</th>
							<th class="text-center">Actions</th>
						  </tr>
					  </thead>
					  <tbody>
						<?php 
					  
					  $count = 0;
					
					  
					  // GET BATCHES: 
						$get_variants_SQL = "SELECT * FROM `material_variant` WHERE `MATERIAL_ID` = " . $_REQUEST['id'];
						$result_get_variants_SQL = mysqli_query($con,$get_variants_SQL);
						// while loop
						while($row_get_variants = mysqli_fetch_array($result_get_variants_SQL)) {
			
							// now print each record to a variable:  
							$material_variant_id = $row_get_variants['ID'];
							$material_variant_type_id = $row_get_variants['variant_type'];
							$material_variant_name_en = $row_get_variants['name_EN'];
							$material_variant_name_cn = $row_get_variants['name_CN'];
							$material_variant_description = $row_get_variants['description'];
							$material_variant_color_code = $row_get_variants['code'];
														
							// get variant type record:
							$get_variant_type_SQL = "SELECT * FROM  `material_variant_types` WHERE  `ID` =" . $material_variant_type_id;
							$result_get_variant_type = mysqli_query($con,$get_variant_type_SQL);
							// while loop
							while($row_get_variant_type = mysqli_fetch_array($result_get_variant_type)) {
	
								// now print each record:  
								$material_variant_type_id = $row_get_variant_type['ID'];
								$material_variant_type_name_en = $row_get_variant_type['name_EN'];
								$material_variant_type_name_cn = $row_get_variant_type['name_CN'];
								$material_variant_type_description = $row_get_variant_type['description'];
								
															
							}
						
					  
					  ?>
					  
					  <tr<?php if ($material_variant_id == $change_record_id) { ?> class="success"<?php } ?>>
					    <td><a href="material_variant_type_view.php?id=<?php echo $material_variant_type_id; ?>"><?php echo $material_variant_type_name_en; ?></a></td>
					    <td><a href="material_variant_view.php?id=<?php echo $material_variant_id; ?>"><?php echo $material_variant_name_en; ?></a></td>
						<td><a href="material_variant_view.php?id=<?php echo $material_variant_id; ?>"><?php echo $material_variant_name_cn; ?></a></td>
						<td><?php echo $material_variant_description; ?></td>
						<td><?php echo $material_variant_color_code; ?></td>	
						<td class="text-center">
                   			<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
		                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
		                    <a href="material_variant_edit.php?id=<?php echo $material_variant_id; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
							<a href="material_variant_delete_do.php?id=<?php echo $material_variant_id; ?>&material_ID=<?php echo $Material_id; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
               			 </td>				    
					  </tr>
					  
					  <?php 
					  
					  $count = $count + 1;
					  
					  } 
					  
					  ?>
					  </tbody>  
					  
					  <tfoot>
						  <tr>
							<th colspan="6">TOTAL: <?php echo $count; ?></th>
						  </tr>
					  </tfoot>
					  
					 </table>
					
					
					 <div class="row">
						<div class="col-md-11"> </div>
						<div class="col-md-1">
							<a href="material_variant_add.php?MATERIAL_ID=<?php echo $record_id; ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
						</div>	
					 </div>
					
								<!-- now close the panel -->
								</div>
							</section>
						</div>
					</div> <!-- end row! -->
						
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>