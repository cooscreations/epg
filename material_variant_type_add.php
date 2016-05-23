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

$page_id = 22;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else if (isset($_REQUEST['MATERIAL_ID'])) { 
	$record_id = $_REQUEST['MATERIAL_ID']; 
}

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Add a New Material Variant Type</h2>
					
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
								if ($record_id != 0) {
									?>
									<li>
										<a href="material_view.php?id=<?php echo $record_id; ?>">Material Record</a>
									</li>
									<li>
										<a href="material_variant_add.php?id=<?php echo $record_id; ?>">Add New Material Variant Record</a>
									</li>
									
									<?php
								} ?>
								<li><span>Add New Material Variant Type Record</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					<div class="row">
						<div class="col-md-12">
						
						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="material_variant_type_add_do.php" method="post">
						
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Add Material Variant Type Record Details:</h2>
								</header>
								<div class="panel-body">
										
									<div class="form-group">
										<label class="col-md-3 control-label">Name EN:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" placeholder="" name="name_EN" />
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									<div class="form-group">
										<label class="col-md-3 control-label">Name CN:</label>
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
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>