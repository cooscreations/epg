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
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 99;

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else {	
	header("Location: part_types.php?msg=NG&action=view&error=no_id");
	exit();		
}

// all OK - continue...
$get_part_type_SQL = "SELECT * FROM `part_type` WHERE `ID` =".$record_id;

$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
		
	$part_type_ID = $row_get_part_type['ID'];
	$part_type_name_EN = $row_get_part_type['name_EN'];
	$part_type_name_CN = $row_get_part_type['name_CN'];
	$part_type_description = $row_get_part_type['description'];
	$part_type_code = $row_get_part_type['code'];
}

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Part Type <?php if ($record_id != 0) { ?> <? echo $part_type_name_EN . " / " . $part_type_name_CN; } ?></h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="part_type.php">All Part Type</a>
									</li>
								<li><span>Edit Part Type Record</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					<div class="row">
						<div class="col-md-12">
						
						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="part_type_edit_do.php" method="post">
						
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Edit Part Type Record Details:</h2>
								</header>
								<div class="panel-body">
								
									<div class="form-group">
										<label class="col-md-3 control-label">Name:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="name_EN"  value="<?php echo $part_type_name_EN; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									<div class="form-group">
										<label class="col-md-3 control-label">名字:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="name_CN" value="中文名"  value="<?php echo $part_type_name_CN; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									
									<div class="form-group">
										<label class="col-md-3 control-label">Description:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault"  name="description" value="<?php echo $part_type_description; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-md-3 control-label">Color:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="code" value="<?php echo $part_type_code; ?>"/>
										</div>
										
										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
								</div>
								
								
								<footer class="panel-footer">
										<input type="hidden" value="<?php echo $part_type_ID; ?>" name="id" />
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