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

$page_id = 59;

// pull the header and template stuff:
pagehead ( $page_id );

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Add A New Product Category</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><a href="part_type.php">All Product Categories</a></li>
				<li><span>Add New Product Category</span></li>
			</ol>

			<a class="sidebar-right-toggle" data-open="sidebar-right"><i
				class="fa fa-chevron-left"></i></a>
		</div>
	</header>

	<!-- start: page -->

	<div class="row">
		<div class="col-md-12">

			<!-- START THE FORM! -->
			<form id="form" class="form-horizontal form-bordered"
				action="product_category_add_do.php" method="post">

				<section class="panel">
					<header class="panel-heading">
						<div class="panel-actions">
							<a href="#" class="panel-action panel-action-toggle"
								data-panel-toggle></a> <a href="#"
								class="panel-action panel-action-dismiss" data-panel-dismiss></a>
						</div>

						<h2 class="panel-title">Add Product Category Details:</h2>
					</header>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-md-3 control-label">Name:<span class="required">*</span></label>
							<div class="col-md-5">
								<input type="text" class="form-control"
									name="name_en" required />
							</div>

							<div class="col-md-1">&nbsp;</div>
						</div>

						<div class="form-group">
							<label class="col-md-3 control-label">名字:<span class="required">*</span></label>
							<div class="col-md-5">
								<input type="text" class="form-control"
									name="name_cn" required />
							</div>
							<div class="col-md-1">&nbsp;</div>
						</div>

						<div class="form-group">
							<label class="col-md-3 control-label">Category Code:<span class="required">*</span></label>
							<div class="col-md-5">
								<input type="text" class="form-control"
									name="cat_code" required />
							</div>
							<div class="col-md-1">&nbsp;</div>
						</div>

					</div>
					<footer class="panel-footer">
						<button type="submit" class="btn btn-success">Submit</button>
						<button type="reset" class="btn btn-default">Reset</button>
					</footer>
				</section>
				<!-- now close the form -->
			</form>



		</div>

	</div>




	<!-- now close the panel -->
	<!-- end row! -->

	<!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
