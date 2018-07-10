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

?>
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

<?php
// now close the page out:
pagefoot($page_id);

?>
