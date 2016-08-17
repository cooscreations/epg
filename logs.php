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

require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 2;

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Logs</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Logs</span></li>
								<!--
								<li><span>Menu Collapsed</span></li>
								-->
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					<section class="panel">
							<header class="panel-heading">
								<div class="panel-actions">
									<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
									<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
								</div>

								<h2 class="panel-title">All Logs</h2>
							</header>
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-6">
										<div class="mb-md">
											<button id="addToTable" class="btn btn-primary">Add <i class="fa fa-plus"></i></button>
										</div>
									</div>
								</div>
								<div class="table-responsive">
								  <table class="table table-bordered table-striped table-condensed mb-none">
									<thead>
										<tr>
											<th>Log Name</th>
											<th>Creator</th>
											<th>Last Edit</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<tr class="gradeX">
											<td>Master Document List</td>
											<td>Nicky Canton</td>
											<td>2016-02-02</td>
											<td class="actions">
												<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
												<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
												<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
												<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
												<a href="view_log.php?id=1" class="on-default view-row"><i class="fa fa-eye"></i></a>
											</td>
										</tr>
										<tr class="gradeX">
											<td>Product &amp; Part Number</td>
											<td>Nicky Canton</td>
											<td>2016-02-02</td>
											<td class="actions">
												<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
												<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
												<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
												<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
												<a href="view_log.php?id=2" class="on-default view-row"><i class="fa fa-eye"></i></a>
											</td>
										</tr>
										<tr class="gradeX">
											<td>Track &amp; Trace</td>
											<td>Nicky Canton</td>
											<td>2016-02-02</td>
											<td class="actions">
												<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
												<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
												<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
												<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
												<a href="view_log.php?id=3" class="on-default view-row"><i class="fa fa-eye"></i></a>
											</td>
										</tr>
										<tr class="gradeX">
											<td>Translations</td>
											<td>Nicky Canton</td>
											<td>2016-02-02</td>
											<td class="actions">
												<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
												<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
												<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
												<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
												<a href="view_log.php?id=4" class="on-default view-row"><i class="fa fa-eye"></i></a>
											</td>
										</tr>
										<tr class="gradeX">
											<td>Engineering Samples</td>
											<td>Nicky Canton</td>
											<td>2016-02-02</td>
											<td class="actions">
												<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
												<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
												<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
												<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
												<a href="view_log.php?id=5" class="on-default view-row"><i class="fa fa-eye"></i></a>
											</td>
										</tr>
										<tr class="gradeX">
											<td>Internal Purchase Orders</td>
											<td>Nicky Canton</td>
											<td>2016-02-02</td>
											<td class="actions">
												<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
												<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
												<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
												<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
												<a href="view_log.php?id=6" class="on-default view-row"><i class="fa fa-eye"></i></a>
											</td>
										</tr>
									</tbody>
								  </table>
								</div>

								<h2>Other Logs in Progress</h2>

								<ul>
						<li><a href="products.php">PRODUCTS</a></li>
						<li><a href="users.php">USERS</a></li>
						<li><a href="materials.php">MATERIALS</a></li>
						<li><a href="suppliers.php">SUPPLIERS</a></li>
						<li><a href="parts.php">PARTS</a></li>
						<li><a href="purchase_orders.php">PURCHASE ORDERS</a></li>
						<li><a href="batch_log.php">BATCH LOG (View all Batches)</a></li>
						<li><a href="warehouse_stock_log.php">WAREHOUSE STOCK LOG</a></li>
						<li><a href="part_revisions.php">PART REVISION LIST</a></li>
                        <li><a href="countries.php">COUNTRIES</a></li>
                        <li><a href="product_types.php">PRODUCT TYPE</a></li>
                        <li><a href="part_treatment.php">PART TREATMENT</a></li>
                        <li><a href="part_classification.php">PART CLASSIFICATIONS</a></li>
                        <li><a href="part_types.php">PART TYPE</a></li>
                        <li><a href="part_revisions.php">PART REVISIONS</a></li>
					</ul>

							</div>
						</section>
					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
