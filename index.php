<?php
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////
/*//////*/      session_start();        /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//  now check the user is OK to view this page  //
/*//////*/ // require ('page_access.php'); /*//////*/
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

$page_id = 1;

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Welcome</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<!--
								<li><span>Layouts</span></li>
								<li><span>Menu Collapsed</span></li>
								-->
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					<h1>Welcome</h1>
					<div class="alert alert-success">
						<strong>UPDATE:</strong> This is now being dynamically generated in order to enable page authentication / permission access.
					</div>




							<div class="col-sm-9">

							<!-- START MAIN COLUMN -->

					<section class="panel">
							<header class="panel-heading">
								<div class="panel-actions">
									<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
									<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
								</div>

								<h2 class="panel-title">Page List (WIP)</h2>
							</header>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-bordered table-striped table-condensed mb-none">
										<thead>
											<tr>
												<th class="text-center"><span class="btn btn-default"><i class="fa fa-cog"></i></span></th>
												<th class="text-center"><span class="btn btn-info"><i class="fa fa-info"></i></span></th>
												<th class="text-center">Icon</th>
												<th class="text-center"><a href="index.php?sort=name_EN">Name / 中文名</a></th>
												<th class="text-center">Menu?</th>
												<th class="text-center">Privacy</th>
												<th class="text-center"><a href="index.php?sort=og_type">Type</a></th>
												<th class="text-center">Min. <br />User Level</th>
												<th class="text-center">Lookup Table</th>
												<th class="text-center">Sub <br />Pages</th>
											</tr>
										</thead>
										<tbody>
										<?php

										if (isset($_REQUEST['sort'])) {
											$sort_SQL = $_REQUEST['sort'];
										}
										else { // set default sort variable
											$sort_SQL = "order";
										}

					$get_pages_SQL = "SELECT * FROM `pages` ORDER BY `" . $sort_SQL . "` ASC";
					// echo $get_pages_SQL;

					  $page_count = 0;

					  $result_get_pages = mysqli_query($con,$get_pages_SQL);
					  // while loop


					  while($row_get_pages = mysqli_fetch_array($result_get_pages)) {

							$page_ID = $row_get_pages['ID'];
							$page_name_EN = $row_get_pages['name_EN'];
							$page_name_CN = $row_get_pages['name_CN'];
							$page_parent_ID = $row_get_pages['parent_ID'];
							$page_dept_ID = $row_get_pages['dept_ID'];
							$page_main_menu = $row_get_pages['main_menu'];
							$page_footer_menu = $row_get_pages['footer_menu'];
							$page_filename = $row_get_pages['filename'];
							$page_created_by = $row_get_pages['created_by'];
							$page_date_created = $row_get_pages['date_created'];
							$page_status = $row_get_pages['status'];
							$page_privacy = $row_get_pages['privacy'];
							$page_min_user_level = $row_get_pages['min_user_level'];
							$page_order = $row_get_pages['order'];
							$page_icon = $row_get_pages['icon'];
							$page_og_locale = $row_get_pages['og_locale'];
							$page_og_type = $row_get_pages['og_type'];
							$page_og_desc = $row_get_pages['og_desc'];
							$page_og_section = $row_get_pages['og_section'];
							$page_side_bar_config = $row_get_pages['side_bar_config'];
							$page_lookup_table = $row_get_pages['lookup_table'];


							if (($page_parent_ID != '')&&($page_parent_ID != 0)) {

								// get the parent page info:

								$get_parent_page_SQL = "SELECT * FROM `pages` WHERE `id` = '" . $page_parent_ID . "'";
								// echo $get_parent_page_SQL;

								$result_get_parent_page = mysqli_query($con,$get_parent_page_SQL);
								// while loop


								while($row_get_parent_page = mysqli_fetch_array($result_get_parent_page)) {

									$parent_page_ID = $row_get_page['ID'];
									$parent_page_name_EN = $row_get_page['name_EN'];
									$parent_page_name_CN = $row_get_page['name_CN'];
									$parent_page_parent_ID = $row_get_page['parent_ID'];
									$parent_page_dept_ID = $row_get_page['dept_ID'];
									$parent_page_main_menu = $row_get_page['main_menu'];
									$parent_page_footer_menu = $row_get_page['footer_menu'];
									$parent_page_filename = $row_get_page['filename'];
									$parent_page_created_by = $row_get_page['created_by'];
									$parent_page_date_created = $row_get_page['date_created'];
									$parent_page_status = $row_get_page['status'];
									$parent_page_privacy = $row_get_page['privacy'];
									$parent_page_min_user_level = $row_get_page['min_user_level'];
									$parent_page_order = $row_get_page['order'];
									$parent_page_icon = $row_get_page['icon'];
									$parent_page_og_locale = $row_get_page['og_locale'];
									$parent_page_og_type = $row_get_page['og_type'];
									$parent_page_og_desc = $row_get_page['og_desc'];
									$parent_page_og_section = $row_get_page['og_section'];
									$parent_page_side_bar_config = $row_get_page['side_bar_config'];
									$parent_page_lookup_table = $row_get_page['lookup_table'];

								} // END WHILE LOOP

							} // END page_parent_ID = 0 / NULL


							// Now we will count the number of sub-categories:
							$count_sub_pages_sql = "SELECT COUNT(ID) FROM `pages` WHERE `parent_ID` = '" . $page_ID . "'";
							$count_sub_pages_query = mysqli_query($con, $count_sub_pages_sql);
							$count_sub_pages_row = mysqli_fetch_row($count_sub_pages_query);
							// Here we have the total row count
							$total_sub_pages = $count_sub_pages_row[0];


					  ?>
											<tr>
											
												<td>

						<!-- ********************************************************* -->
						<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
						<!-- ********************************************************* -->
						 
						    <a class="modal-with-form btn btn-default" href="#modalForm_<?php echo $page_ID; ?>"><i class="fa fa-gear"></i></a>

							<!-- Modal Form -->
							<div id="modalForm_<?php echo $page_ID; ?>" class="modal-block modal-block-primary mfp-hide">
								<section class="panel">
									<header class="panel-heading">
										<h2 class="panel-title">Admin Options</h2>
									</header>
									<div class="panel-body">
									
										<div class="table-responsive">
										 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
										 <thead>
											<tr>
												<th class="text-left" colspan="2">Action</th>
												<th>Decsription</th>
											</tr>
										  </thead>
										  <tbody>
											<tr>
											  <td>VIEW</td>
											  <td>
											  <a href="<?php echo $page_filename; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-success" target="_blank"><i class="fa fa-arrow-right"></i></a></td>
											  <td>View this page (opens in a new window)</td>
											</tr>
											<tr>
											  <td>EDIT</td>
											  <td>
											  <a href="page_edit.php?id=<?php echo $page_ID; ?>" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a></td>
											  <td>Edit this page (page variables in the database)</td>
											</tr>
											<tr>
											  <td>DELETE</td>
											  <td><a href="record_delete_do.php?table_name=pages&src_page=index.php&id=<?php echo $page_ID; ?>" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a></td>
											  <td>Delete this record</td>
											</tr>
											<tr>
											  <td>ADD PAGE</td>
											  <td><a href="page_add.php" class="mb-xs mt-xs mr-xs btn btn-success"><i class="fa fa-plus"></i></a></td>
											  <td>Add a new page to the system</td>
											</tr>
										  </tbody>
										  <tfoot>
										  	<tr>
										  	  <td>&nbsp;</td>
										  	  <td>&nbsp;</td>
										  	  <td>&nbsp;</td>
										  	</tr>
										  </tfoot>
										  </table>
										</div><!-- end of responsive table -->	
									
									</div><!-- end panel body -->
									<footer class="panel-footer">
										<div class="row">
											<div class="col-md-12 text-right">
												<button class="btn btn-danger modal-dismiss"><i class="fa fa-times"></i> Cancel</button>
											</div>
										</div>
									</footer>
								</section>
							</div>
							
						<!-- ********************************************************* -->
						<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
						<!-- ********************************************************* -->
												
												
												</td>
											<td class="text-center">
													<a class="mb-xs mt-xs mr-xs btn btn-info" data-toggle="modal" data-target="#menu_item_<?php echo $page_ID; ?>"><i class="fa fa-info"></i></a>
<!-- START MODAL POP-UP CONTAINING PAGE INFO -->

									<div class="modal fade" id="menu_item_<?php echo $page_ID; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content large">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
													<h4 class="modal-title" id="myModalLabel"><?php echo $page_name_EN; if (($page_name_CN!='')&&($page_name_CN!='中文名')) { echo ' / ' . $page_name_CN; } ?> (<?php echo $page_filename; ?>)</h4>
												</div>
												<div class="modal-body">

								<div class="table-responsive">
									<table class="table table-bordered table-striped table-condensed mb-none">
  <tr>
    <th>FIELD</th>
    <th>STATUS</th>
  </tr>
  <tr>
    <td>ID</td>
    <td><a href="<?php echo $page_filename; ?>" title="Click to launch this page"><?php echo $page_ID; ?></a></td>
  </tr>
  <tr>
    <td>Name</td>
    <td>
    	<a href="<?php echo $page_filename; ?>" title="Click to launch this page"><?php echo $page_name_EN; ?></a>
    </td>
  </tr>
  <tr>
    <td>中文名</td>
    <td>
    	<a href="<?php echo $page_filename; ?>" title="Click to launch this page"><?php echo $page_name_CN; ?></a>
    </td>
  </tr>
  <tr>
    <td>Parent Page</td>
    <td>
    <?php

    // have parent, let's link to it here:
    if ($page_parent_ID != '0') {
    	?><a href="<?php echo $parent_page_filename; ?>" title="FILE: <?php echo $parent_page_filename; ?>"><?php echo $parent_page_filename; ?></a><?php
    }
    else {
    	?><span class="text-warning">Root / No Parent Set</span><?php
    }

    ?>
    </td>
  </tr>
  <tr>
    <td>Main Menu</td>
    <td>
    	<?php if ($page_main_menu == 1) {
    	 	?><span class="text-success">VISIBLE</span><?php
    	 }
    	 else {
    	 	?><span class="text-danger">NOT VISIBLE</span><?php
    	 }?>
    </td>
  </tr>
  <tr>
    <td>Filename</td>
    <td><a href="<?php echo $page_filename; ?>" title="Click to launch this page"><?php echo $page_filename; ?></a></td>
  </tr>
  <tr>
    <td>Created By</td>
    <td><?php

    // GET THE PAGE CREATOR USER INFO:

	  $get_user_SQL = "SELECT * FROM `users` WHERE `id` = '" . $page_created_by . "'";
	  // echo $get_user_SQL;

	  $result_get_user = mysqli_query($con,$get_user_SQL);
	  // while loop

	  while($row_get_user = mysqli_fetch_array($result_get_user)) {

	  	$creator_info = '<a href="user_view.php?id=' . $page_created_by . '" title="Click here to view user profile.">';

		$creator_info .= $row_get_user['first_name'] . ' ' . $row_get_user['last_name'];
		// echo 'OK';
		if (($row_get_user['name_CN'] != '')&&($row_get_user['name_CN'] != '中文名')) {
			$creator_info .= ' / ' . $row_get_user['name_CN'];
		}

		$creator_info .= '</a>';


	  } // end while loop

    echo $creator_info;






    ?></td>
  </tr>
  <tr>
    <td>Date Created</td>
    <td><?php echo $page_date_created; ?></td>
  </tr>
  <tr>
    <td>Status</td>
    <td><?php
    	if ($page_status == 2) { echo '<span class="text-success">✔ APPROVED ✔</span>'; }
    	else if ($page_status == 1) { echo '<span class="text-primary">? PENDING ?</span>'; }
		else if ($page_status == 0) { echo '<span class="text-danger">✘ DELETED ✘</span>'; }
		else { echo '<span class="text-warning">! UNKNOWN !</span>'; }
		?>
    </td>
  </tr>
  <tr>
    <td>Privacy</td>
    <td><?php
    	if ($page_privacy == 'PUBLIC') { echo '<span class="text-success"><i class="fa fa-eye"></i> PUBLIC</span>'; }
    	else { echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> PRIVATE</span>'; }
    ?>
	</td>
  </tr>
  <tr>
    <td>Minimum User Level</td>
    <td><?php echo $page_min_user_level; ?></td>
  </tr>
  <tr>
    <td>Order</td>
    <td><?php echo $page_order; ?></td>
  </tr>
  <tr>
    <td>Icon</td>
    <td><i class="fa <?php echo $page_icon; ?> fa-2x"></i></td>
  </tr>
  <tr>
    <td>OG Local</td>
    <td><?php echo $page_og_locale; ?></td>
  </tr>
  <tr>
    <td>OG Type</td>
    <td><?php echo $page_og_type; ?></td>
  </tr>
  <tr>
    <td>OG Description</td>
    <td><?php echo $page_og_desc; ?></td>
  </tr>
  <tr>
    <td>OG Section</td>
    <td><?php echo $page_og_section; ?></td>
  </tr>
  <tr>
    <td>Sidebar Config</td>
    <td><?php echo $page_side_bar_config; ?></td>
  </tr>
  <tr>
    <td>Lookup Table</td>
    <td><?php
    if ($page_lookup_table != '') {
    	echo $page_lookup_table;
    }
    else {
    	echo 'none';
	}
    ?></td>
  </tr>
  <tr>
    <td class="text-muted">Dept. ID</td>
    <td class="text-muted"><span title="These features are not currently in use">N/A</span></td>
  </tr>
  <tr>
    <td class="text-muted">Footer Menu</td>
    <td class="text-muted"><span title="These features are not currently in use">N/A</span></td>
  </tr>
</table>
</div>

<h3>Child (Sub) Pages</h3>

<div class="table-responsive">
									<table class="table table-bordered table-striped table-condensed mb-none">
  <tr>
    <th>ICON</th>
    <th>NAME</th>
    <th>ACTION</th>
  </tr>

  <?php

					$get_C_pages_SQL = "SELECT * FROM `pages` WHERE `parent_ID` = '". $page_ID ."'";
					// echo $get_C_pagesSQL;

					  $C_page_count = 0;

					  $result_get_C_pages = mysqli_query($con,$get_C_pages_SQL);
					  // while loop


					  while($row_get_C_pages = mysqli_fetch_array($result_get_C_pages)) {

							$C_page_ID = $row_get_C_pages['ID'];
							$C_page_name_EN = $row_get_C_pages['name_EN'];
							$C_page_name_CN = $row_get_C_pages['name_CN'];
							$C_page_parent_ID = $row_get_C_pages['parent_ID'];
							$C_page_dept_ID = $row_get_C_pages['dept_ID'];
							$C_page_main_menu = $row_get_C_pages['main_menu'];
							$C_page_footer_menu = $row_get_C_pages['footer_menu'];
							$C_page_filename = $row_get_C_pages['filename'];
							$C_page_created_by = $row_get_C_pages['created_by'];
							$C_page_date_created = $row_get_C_pages['date_created'];
							$C_page_status = $row_get_C_pages['status'];
							$C_page_privacy = $row_get_C_pages['privacy'];
							$C_page_min_user_level = $row_get_C_pages['min_user_level'];
							$C_page_order = $row_get_C_pages['order'];
							$C_page_icon = $row_get_C_pages['icon'];
							$C_page_og_locale = $row_get_C_pages['og_locale'];
							$C_page_og_type = $row_get_C_pages['og_type'];
							$C_page_og_desc = $row_get_C_pages['og_desc'];
							$C_page_og_section = $row_get_C_pages['og_section'];
							$C_page_side_bar_config = $row_get_C_pages['side_bar_config'];
							$C_page_lookup_table = $row_get_C_pages['lookup_table'];

					  ?>

  <tr>
    <td align="center"><i class="fa <?php echo $C_page_icon; ?> fa-2x" title="PAGE ID: <?php echo $C_page_ID; ?>"></i></td>
												<td>
													<a href="<?php echo $C_page_filename; ?>">
														<?php echo $C_page_name_EN; if (($C_page_name_CN!='')&&($C_page_name_CN!='中文名')) { echo ' / ' . $C_page_name_CN; } ?>
													</a>
												</td>
    <td><a href="<?php echo $C_page_filename; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-primary"><i class="fa fa-external-link"></i></a></td>
  </tr>

  <?php
  $C_page_count = $C_page_count +1;
  } // end WHILE FOUND CHILD PAGES LOOP
  ?>
  <?php  if ($C_page_count == 0) { ?>
  <tr>
    <td colspan="3"><span class="text-danger">There are 0 sub-pages / child pages listed for this page at present.</span></td>
  </tr>
  <?php } ?>
  </table>
  </div>


												</div>
												<div class="modal-footer">
													<a href="page_edit.php?id=<?php echo $page_ID; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i> Edit Page</a>
													<a href="<?php echo $page_filename; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-success"><i class="fa fa-arrow-right"></i> Launch Page</a>
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												</div>
											</div>
										</div>
									</div>

<!-- END OF MODEL POP-UP CONTAINING PAGE INFO -->
												</td>
												<td align="center"><i class="fa <?php echo $page_icon; ?> fa-2x" title="PAGE ID: <?php echo $page_ID; ?>"></i></td>
												<td>
													<a href="<?php echo $page_filename; ?>">
														<?php echo $page_name_EN; if (($page_name_CN!='')&&($page_name_CN!='中文名')) { echo ' / ' . $page_name_CN; } ?>
													</a>
												</td>
												<td class="text-center"><?php
												if ($page_main_menu == 1) {
													?>
													<i class="fa fa-check text-success fa-2x" title="THIS PAGE APPEARS IN THE MAIN MENU"></i>
													<?php
												}
												else {
													?>
													<i class="fa fa-times text-danger fa-2x" title="THIS PAGE DOES NOT APPEAR IN THE MAIN MENU"></i>
													<?php
												}
												?></td>
												<td class="text-center">
												<?php
												if ($page_privacy == 'PUBLIC') {
													?>
													<i class="fa fa-eye text-success fa-2x" title="THIS PAGE IS PUBLIC"></i>
													<?php
												}
												else {
													?>
													<i class="fa fa-eye-slash text-warning fa-2x" title="THIS PAGE IS VIEWABLE BY LOGGED IN USERS ONLY"></i>
													<?php
												}
												?>
												</td>
												<td>
												<!-- PAGE TYPE INFO: -->
												<?php
												if ($page_og_type == 'list') {
													?>
													<i class="fa fa-list text-default fa-2x" title="<?php echo $page_og_type; ?>: This page is a list of data, similar to a spreadsheet"></i>
													<?php
												}
												else if ($page_og_type == 'add') {
													?>
													<i class="fa fa-plus-square text-success fa-2x" title="<?php echo $page_og_type; ?>: Using this page, you can add a new entry to the system"></i>
													<?php
												}
												else if ($page_og_type == 'system') {
													?>
													<i class="fa fa-cogs text-warning fa-2x" title="<?php echo $page_og_type; ?>: This type of page is required to run / operate the information system"></i>
													<?php
												}
												else if ($page_og_type == 'report') {
													?>
													<i class="fa fa-bar-chart text-default fa-2x" title="<?php echo $page_og_type; ?>: Extrapolate data into a helpful report"></i>
													<?php
												}
												else if ($page_og_type == 'profile') {
													?>
													<i class="fa fa-file-o text-info fa-2x" title="<?php echo $page_og_type; ?>: View a single record in more detail"></i>
													<?php
												}
												else if ($page_og_type == 'edit') {
													?>
													<i class="fa fa-pencil text-warning fa-2x" title="<?php echo $page_og_type; ?>: Using this page, you can add a new entry to the system"></i>
													<?php
												}
												else {
													?>
													<i class="fa fa-exclamation-triangle text-danger fa-2x" title="<?php echo $page_og_type; ?>: TYPE NOT SET!"></i>
													<?php
												}
												?>
												<!-- END OF PAGE TYPE INFO -->
												</td>
												<td class="text-center"><?php echo $page_min_user_level; ?></td>
												<td class="text-center">
												<?php
												if ($page_lookup_table != '') {
													echo $page_lookup_table;
												}
												else {
													echo '<acronym title="Not Applicable">N/A</acronym>';
												}
												?>
												</td>
												<td class="text-center">
												<?php echo $total_sub_pages; ?>
												</td>
											</tr>


						<?php
						
						$page_count = $page_count + 1;

						} // end of get pages...

						?>
										</tbody>
										<tfoot>
											<tr>
											  <th colspan="10">TOTAL PAGES: <?php echo $page_count; ?></th>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</section>
					<!-- END OF MAIN COLUMN -->
					</div>


					<!-- ------------------------------------- -->
							<div class="col-sm-3">
							<!-- RIGHT COL -->
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">ADMIN ACTIONS</h2>
								</header>
								<div class="panel-body">

								<ul>
								  <li><a href="#" title="COMING SOON">View a report</a></li>
								  <li><a href="#" title="COMING SOON">View a list</a></li>
								  <li><a href="#" title="COMING SOON">View a profile</a></li>
								  <li><a href="#" title="COMING SOON">Edit / Delete something</a></li>
								  <li><a href="#" title="COMING SOON">Add something</a></li>
								  <li><a href="#" title="COMING SOON">System / Admin Links</a></li>
								</ul>


								</div>
							</section>
							<!-- END OF RIGHT COL -->
							</div>


					<!-- ------------------------------------- -->

					<!-- end: page -->






				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
