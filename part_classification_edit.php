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

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: part_classification.php?msg=NG&action=view&error=no_id");
	exit();
}

	$get_part_classification_SQL = "SELECT * FROM `part_classification` WHERE `ID` =".$record_id;

	$result_get_part_classification = mysqli_query($con,$get_part_classification_SQL);
	while($row_get_part_classification = mysqli_fetch_array($result_get_part_classification)) {

		$part_classification_ID = $row_get_part_classification['ID'];
		$part_classification_name_EN = $row_get_part_classification['name_EN'];
		$part_classification_name_CN = $row_get_part_classification['name_CN'];
		$part_classification_description = $row_get_part_classification['description'];
		$part_classification_color = $row_get_part_classification['color'];
	}

// pull the header and template stuff:
pagehead();

?>
					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="part_classification_edit_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Edit Part Classification Record Details:</h2>
								</header>
								<div class="panel-body">

									<div class="form-group">
										<label class="col-md-3 control-label">Name:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="name_EN"  value="<?php echo $part_classification_name_EN; ?>"/>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">名字:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="name_CN" value="中文名"  value="<?php echo $part_classification_name_CN; ?>"/>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">Description:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault"  name="description" value="<?php echo $part_classification_description; ?>"/>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Color:</label>
										<div class="col-md-5">
											<input type="text" class="form-control" id="inputDefault" name="color" value="<?php echo $part_classification_color; ?>"/>
										</div>

										<div class="col-md-1">
											&nbsp;
										</div>
									</div>
								</div>


								<footer class="panel-footer">
										<input type="hidden" value="<?php echo $part_classification_ID; ?>" name="id" />
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
