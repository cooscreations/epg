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
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 99;

// pull the header and template stuff:
pagehead ( $page_id );

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Add A New Product Type</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><a href="products.php">All Products</a></li>
				<li><span>Add New Product Type</span></li>
			</ol>

			<a class="sidebar-right-toggle" data-open="sidebar-right"><i
				class="fa fa-chevron-left"></i></a>
		</div>
	</header>

	<!-- start: page -->

	<div class="row">
		<div class="col-md-12">

			<!-- START THE FORM! -->
			<form class="form-horizontal form-bordered"
				action="product_type_add_do.php" method="post">

				<section class="panel">
					<header class="panel-heading">
						<div class="panel-actions">
							<a href="#" class="panel-action panel-action-toggle"
								data-panel-toggle></a> <a href="#"
								class="panel-action panel-action-dismiss" data-panel-dismiss></a>
						</div>

						<h2 class="panel-title">Add Product Type Details:</h2>
					</header>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-md-3 control-label">Type Code:</label>
							<div class="col-md-5">
								<input type="text" class="form-control" id="inputDefault"
									name="product_type_code" />
							</div>

							<div class="col-md-1">&nbsp;</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Name:</label>
							<div class="col-md-5">
								<input type="text" class="form-control" id="inputDefault"
									name="name_en" />
							</div>

							<div class="col-md-1">&nbsp;</div>
						</div>

						<div class="form-group">
							<label class="col-md-3 control-label">名字:</label>
							<div class="col-md-5">
								<input type="text" class="form-control" id="inputDefault"
									name="name_cn" />
							</div>


							<div class="col-md-1">&nbsp;</div>
						</div>

						<div class="form-group">
							<label class="col-md-3 control-label">Status:</label>
							<div class="col-md-5">
								<input type="text" class="form-control" id="inputDefault"
									name="status" />
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