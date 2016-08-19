<?php
	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// header('Content-Type: text/html; charset=utf-8');

	function pagehead($page_id, $record_id=NULL) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// use this to get the current (active) page info:

	header('Content-Type: text/html; charset=utf-8');

	$public_path = pathinfo($_SERVER['SCRIPT_NAME']);
	$this_file = $public_path['basename'];


	$get_page_SQL = "SELECT * FROM `pages` WHERE `filename` = '" . $this_file . "'";
	// echo '<h1>' . $get_page_SQL . '</h1>';

	$result_get_page = mysqli_query($con,$get_page_SQL);
	$pages_found = 0;
	// while loop
	while($row_get_page = mysqli_fetch_array($result_get_page)) {

			// set vars:
			$page_id 				= $row_get_page['ID'];
			$page_name_EN 			= $row_get_page['name_EN'];
			$page_name_CN 			= $row_get_page['name_CN'];
			$page_parent_ID 		= $row_get_page['parent_ID'];
			$page_dept_ID 			= $row_get_page['dept_ID'];
			$page_main_menu 		= $row_get_page['main_menu'];
			$page_footer_menu 		= $row_get_page['footer_menu'];
			$page_filename 			= $row_get_page['filename'];
			$page_icon 				= $row_get_page['icon'];
			$page_privacy 			= $row_get_page['privacy'];
			$page_min_user_level 	= $row_get_page['min_user_level'];
			$page_created_by 		= $row_get_page['created_by'];
			$page_date_created 		= $row_get_page['date_created'];
			$page_status 			= $row_get_page['status'];
			$page_order 			= $row_get_page['order'];
			$page_og_locale 		= $row_get_page['og_locale'];
			$page_og_type 			= $row_get_page['og_type'];
			$page_og_desc 			= $row_get_page['og_desc'];
			$page_og_section 		= $row_get_page['og_section'];
			$page_side_bar_config 	= $row_get_page['side_bar_config'];
			$page_lookup_table 		= $row_get_page['lookup_table']; // look up info for TITLE tag!

			$GLOBALS['page_id'] 			= $page_id; 					// hoping to use this data in the footer also
			$GLOBALS['page_lookup_table'] 	= $page_lookup_table; 			// hoping to use this data in the footer also
			$GLOBALS['page_og_type'] 		= $page_og_type;				// hoping to use this data in the footer also

			$add_title_info = ''; // DEFAULT IS NIL
			if ($page_lookup_table!='') {

				// ID number SHOULD be $_REQUEST['id'];

				$record_to_find = $_REQUEST['id'];
				if ($record_to_find == '') {
					$record_to_find 		= $_REQUEST['ID']; // sloppy!
					$GLOBALS['record_id'] 	= $record_to_find; // hoping to use this data in the footer also
				}

				$get_title_extra_SQL = "SELECT * FROM `" . $page_lookup_table . "` WHERE `ID` = '" . $record_to_find . "'";
				$result_get_title_extra = mysqli_query($con,$get_title_extra_SQL);
				// while loop
				while($row_get_title_extra = mysqli_fetch_array($result_get_title_extra)) {

						// set vars:


						// DEFAULT:
						$add_title_info = " - " . $row_get_title_extra['name_EN'];
						if (($row_get_title_extra['name_CN']!='')&&($row_get_title_extra['name_CN']!='中文名')){
							$add_title_info .= " / " . $row_get_title_extra['name_CN'];
						}

						// we can get more info for the TITLE tag!
						if ($page_lookup_table == 'users') {
							$add_title_info = " - " . $row_get_title_extra['first_name'] . " " . $row_get_title_extra['last_name'];
						if (($row_get_title_extra['name_CN']!='')&&($row_get_title_extra['name_CN']!='中文名')){
							$add_title_info .= " / " . $row_get_title_extra['name_CN'];
						}

						}
						else if ($page_lookup_table == 'suppliers') {
							$add_title_info .= " (" . $row_get_title_extra['epg_supplier_ID'] . ")"; // appended
						}
						else if ($page_lookup_table == 'bug_report') {
							$add_title_info .= ' ("' . $row_get_title_extra['title'] . '")'; // appended
						}
						else if ($page_lookup_table == 'purchase_orders') {
							$add_title_info = "PO # " . $row_get_title_extra['PO_number']; // this is NOT appended - it overwrites!
						}
						else if ($page_lookup_table == 'product_type') {
							$add_title_info .= " (Code: " . $row_get_title_extra['product_type_code'] . ")"; // appended
						}
						else if ($page_lookup_table == 'product_categories') {
							$add_title_info .= " (Code: " . $row_get_title_extra['cat_code'] . ")"; // appended
						}
						else if ($page_lookup_table == 'parts') {
							$add_title_info .= " (Code: " . $row_get_title_extra['part_code'] . ")"; // appended
						}
						else if ($page_lookup_table == 'part_batch') {
							$add_title_info = "BATCH # " . $row_get_title_extra['batch_number']; // this is NOT appended - it overwrites!
						}
						/* THE FOLLOWING ARE TAKEN CARE OF BY THE DEFAULT AT THE START / TOP ^^^
						else if ($page_lookup_table == 'pages') {
							name_EN
							name_CN
						}
						else if ($page_lookup_table == 'material') {
							name_EN
							name_CN
						}
						else if ($page_lookup_table == 'countries') {
							name_EN
							name_CN
						}
						else if ($page_lookup_table == 'part_type') {
							name_EN
							name_CN
						}
						else if ($page_lookup_table == 'part_treatment') {
							name_EN
							name_CN
						}
						*/
						else if ($page_lookup_table == 'product_BOM') {

							$combine_part_and_rev_SQL = "SELECT `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $row_get_title_extra['part_rev_ID'] . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2";

							$result_get_rev_part_join = mysqli_query($con,$combine_part_and_rev_SQL);
							// while loop
							while($row_get_rev_part_join = mysqli_fetch_array($result_get_rev_part_join)) {

								// NOW WRITE THE DATA:
								$add_title_info = " - " . $row_get_rev_part_join['name_EN'];
								if (($row_get_rev_part_join['name_CN']!='')&&($row_get_rev_part_join['name_CN']!='中文名')){
									$add_title_info .= " / " . $row_get_rev_part_join['name_CN'];
								}
								$add_title_info .= " (Code: " . $row_get_rev_part_join['part_code'] . ", Rev. " . $row_get_rev_part_join['revision_number'] . ")";

							} // end get BOM part / part rev data
						}
						else if ($page_lookup_table == 'products') {
							// NOT USED, as this was moved to the parts.php?show=products page :)
						}
						else {
							// what else is there?
						}
				} // END LOOK UP EXTRA INFO!
			}

			$pages_found = $pages_found + 1;
	}


	?>
	<!doctype html>

	<!--

	<html class="fixed js flexbox flexboxlegacy no-touch csstransforms csstransforms3d no-overflowscrolling no-mobile-device custom-scroll sidebar-left-collapsed">

	-->

	<html class="fixed sidebar-left-collapsed">

	<head>


<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>

		<!-- Basic -->
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>EPG Connect - <?php
		// get the page name from our result set now ^_^
		echo $page_name_EN;

		if (($page_name_CN!='')&&($page_name_CN!='中文名')) {
			echo " / " . $page_name_CN;
		}

		echo $add_title_info;

		?></title>
		<meta name="keywords" content="EPG Connect, <?php
		// get the page name from our result set now ^_^
		echo $page_name_EN;

		if (($page_name_CN!='')&&($page_name_CN!='中文名')) {
			echo ", " . $page_name_CN;
		} ?>" />
		<meta name="description" content="A system to connect EPG data, people and systems. <?php if ($page_og_desc!='') { ?>On this page: <?php echo $page_og_desc; } ?>">
		<meta name="author" content="MarkClulow.com">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />

		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css" />
		<link rel="stylesheet" href="assets/vendor/intl-tel-input/css/intlTelInput.css" />

		<!-- Specific Page Vendor CSS -->
		<?php
		if ($page_id == 2) {
			?>
			<link rel="stylesheet" href="assets/vendor/select2/select2.css" />
			<link rel="stylesheet" href="assets/vendor/jquery-datatables-bs3/assets/css/datatables.css" />
			<?php
		}
		?>

		<!-- Specific Page Vendor CSS - ADVANCED FORMS-->
		<link rel="stylesheet" href="assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css" />
		<link rel="stylesheet" href="assets/vendor/select2/select2.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css" />
		<link rel="stylesheet" href="assets/vendor/dropzone/css/basic.css" />
		<link rel="stylesheet" href="assets/vendor/dropzone/css/dropzone.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-markdown/css/bootstrap-markdown.min.css" />
		<link rel="stylesheet" href="assets/vendor/summernote/summernote.css" />
		<link rel="stylesheet" href="assets/vendor/summernote/summernote-bs3.css" />
		<link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css" />
		<link rel="stylesheet" href="assets/vendor/codemirror/theme/monokai.css" />


		<!-- Specific Page Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/pnotify/pnotify.custom.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="assets/stylesheets/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme-custom.css">

		<!-- Head Libs -->
		<script src="assets/vendor/modernizr/modernizr.js"></script>

	</head>
	<body>
		<section class="body">

			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="/" class="logo">
						<img src="assets/images/logo.png" height="35" alt="EPG Connect" />
					</a>
					<div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
					</div>
				</div>

				<!-- start: search & user box -->
				<div class="header-right">

					<form action="search.php" class="search nav-form">
						<div class="input-group input-search">
							<input type="text" class="form-control" name="query" id="query" placeholder="Search...">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
							</span>
						</div>
					</form>

					<span class="separator"></span>

				<!--
					<ul class="notifications">
						<li>
							<a href="#" class="dropdown-toggle notification-icon" data-toggle="dropdown">
								<i class="fa fa-tasks"></i>
								<span class="badge">3</span>
							</a>

							<div class="dropdown-menu notification-menu large">
								<div class="notification-title">
									<span class="pull-right label label-default">3</span>
									Tasks
								</div>

								<div class="content">
									<ul>
										<li>
											<p class="clearfix mb-xs">
												<span class="message pull-left">Generating Sales Report</span>
												<span class="message pull-right text-dark">60%</span>
											</p>
											<div class="progress progress-xs light">
												<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
											</div>
										</li>

										<li>
											<p class="clearfix mb-xs">
												<span class="message pull-left">Importing Contacts</span>
												<span class="message pull-right text-dark">98%</span>
											</p>
											<div class="progress progress-xs light">
												<div class="progress-bar" role="progressbar" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100" style="width: 98%;"></div>
											</div>
										</li>

										<li>
											<p class="clearfix mb-xs">
												<span class="message pull-left">Uploading something big</span>
												<span class="message pull-right text-dark">33%</span>
											</p>
											<div class="progress progress-xs light mb-xs">
												<div class="progress-bar" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%;"></div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</li>
						<li>
							<a href="#" class="dropdown-toggle notification-icon" data-toggle="dropdown">
								<i class="fa fa-envelope"></i>
								<span class="badge">4</span>
							</a>

							<div class="dropdown-menu notification-menu">
								<div class="notification-title">
									<span class="pull-right label label-default">230</span>
									Messages
								</div>

								<div class="content">
									<ul>
										<li>
											<a href="#" class="clearfix">
												<figure class="image">
													<img src="assets/images/!sample-user.jpg" alt="Joseph Doe Junior" class="img-circle" />
												</figure>
												<span class="title">Joseph Doe</span>
												<span class="message">Lorem ipsum dolor sit.</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<figure class="image">
													<img src="assets/images/!sample-user.jpg" alt="Joseph Junior" class="img-circle" />
												</figure>
												<span class="title">Joseph Junior</span>
												<span class="message truncate">Truncated message. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet lacinia orci. Proin vestibulum eget risus non luctus. Nunc cursus lacinia lacinia. Nulla molestie malesuada est ac tincidunt. Quisque eget convallis diam, nec venenatis risus. Vestibulum blandit faucibus est et malesuada. Sed interdum cursus dui nec venenatis. Pellentesque non nisi lobortis, rutrum eros ut, convallis nisi. Sed tellus turpis, dignissim sit amet tristique quis, pretium id est. Sed aliquam diam diam, sit amet faucibus tellus ultricies eu. Aliquam lacinia nibh a metus bibendum, eu commodo eros commodo. Sed commodo molestie elit, a molestie lacus porttitor id. Donec facilisis varius sapien, ac fringilla velit porttitor et. Nam tincidunt gravida dui, sed pharetra odio pharetra nec. Duis consectetur venenatis pharetra. Vestibulum egestas nisi quis elementum elementum.</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<figure class="image">
													<img src="assets/images/!sample-user.jpg" alt="Joe Junior" class="img-circle" />
												</figure>
												<span class="title">Joe Junior</span>
												<span class="message">Lorem ipsum dolor sit.</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<figure class="image">
													<img src="assets/images/!sample-user.jpg" alt="Joseph Junior" class="img-circle" />
												</figure>
												<span class="title">Joseph Junior</span>
												<span class="message">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet lacinia orci. Proin vestibulum eget risus non luctus. Nunc cursus lacinia lacinia. Nulla molestie malesuada est ac tincidunt. Quisque eget convallis diam.</span>
											</a>
										</li>
									</ul>

									<hr />

									<div class="text-right">
										<a href="#" class="view-more">View All</a>
									</div>
								</div>
							</div>
						</li>
						<li>
							<a href="#" class="dropdown-toggle notification-icon" data-toggle="dropdown">
								<i class="fa fa-bell"></i>
								<span class="badge">3</span>
							</a>

							<div class="dropdown-menu notification-menu">
								<div class="notification-title">
									<span class="pull-right label label-default">3</span>
									Alerts
								</div>

								<div class="content">
									<ul>
										<li>
											<a href="#" class="clearfix">
												<div class="image">
													<i class="fa fa-thumbs-down bg-danger"></i>
												</div>
												<span class="title">Server is Down!</span>
												<span class="message">Just now</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<div class="image">
													<i class="fa fa-lock bg-warning"></i>
												</div>
												<span class="title">User Locked</span>
												<span class="message">15 minutes ago</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<div class="image">
													<i class="fa fa-signal bg-success"></i>
												</div>
												<span class="title">Connection Restored</span>
												<span class="message">10/10/2014</span>
											</a>
										</li>
									</ul>

									<hr />

									<div class="text-right">
										<a href="#" class="view-more">View All</a>
									</div>
								</div>
							</div>
						</li>
					</ul>

					<span class="separator"></span>

					-->

					<div id="userbox" class="userbox">
					<?php
						//Granb user name from the session.
						$username = $_SESSION['username'];
						$get_logged_in_user_details_SQL = "SELECT * FROM  `users` where email='$username' ";
						$result = mysqli_query ( $con, $get_logged_in_user_details_SQL );
						$count = $result->num_rows;
						while($result_row = mysqli_fetch_array($result)) {

					?>
						<a href="#" data-toggle="dropdown">
							<figure class="profile-picture">
								<img src="assets/images/users/user_<?php echo $result_row['ID']; ?>.png" alt="<?php echo $result_row['first_name']; echo ' ' . $result_row['middle_name']; echo ' ' . $result_row['last_name']; ?>" class="img-circle" data-lock-picture="assets/images/!logged-user.jpg" />
							</figure>
							<div class="profile-info" data-lock-name="<?php echo $result_row['first_name']; echo ' ' . $result_row['last_name']; ?>" data-lock-email="<?php echo $result_row['email']; ?>">
								<span class="name"><?php echo $result_row['first_name']; echo ' ' . $result_row['middle_name']; echo ' ' . $result_row['last_name']; ?></span>
								<span class="role"><?php echo $result_row['position']; ?></span>
							</div>

							<i class="fa custom-caret"></i>
						</a>

						<div class="dropdown-menu">
							<ul class="list-unstyled">
								<li class="divider"></li>
								<li>
									<a role="menuitem" tabindex="-1" href="user_view.php?id=<?php echo $result_row['ID']; ?>"><i class="fa fa-user"></i> My Profile</a>
								</li>
								<li>
									<a role="menuitem" tabindex="-1" href="#" data-lock-screen="true"><i class="fa fa-lock"></i> Lock Screen</a>
								</li>
								<li>
									<a role="menuitem" tabindex="-1" href="logout.php"><i class="fa fa-power-off"></i> Logout</a>
								</li>
							</ul>
						</div>
					<?php

					  } // end of the IF.
					?>
					</div>
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

			<div class="inner-wrapper">
				<!-- start: sidebar -->
				<aside id="sidebar-left" class="sidebar-left">

					<div class="sidebar-header">
						<div class="sidebar-title">
							Navigation
						</div>
						<div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
							<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
						</div>
					</div>

					<div class="nano">
						<div class="nano-content">
							<nav id="menu" class="nav-main" role="navigation">
								<ul class="nav nav-main">
									<li<?php if ($page_id == 1) { ?> nav-active<?php } ?>>
										<a href="index.php">
											<i class="fa fa-home" aria-hidden="true"></i>
											<span>Dashboard</span>
										</a>
									</li>
									<li class="nav-parent<?php if ($page_id == 2) { ?> nav-active<?php } ?>">
										<a>
											<i class="fa fa-table" aria-hidden="true"></i>
											<span>Logs</span>
										</a>
										<ul class="nav nav-children">
											<li>
												<a href="logs.php">
													 View All
												</a>
											</li>
											<li>
												<a href="master_document_log.php">
													 Master Document Log
												</a>
											</li>
											<li>
												<a href="parts.php">
													 Part Number
												</a>
											</li>
											<li>
												<a href="part_revisions.php">
													 Part Revisions
												</a>
											</li>
											<li>
												<a href="BOM.php">
													 Bill Of Material (BOM)
												</a>
											</li>
											<li>
												<a href="purchase_orders.php">
													 Purchase Orders
												</a>
											</li>
											<li>
												<a href="batch_log.php">
													 Batch Log
												</a>
											</li>
											<li>
												<a href="warehouse_stock_log.php">
													 Warehouse Stock Log
												</a>
											</li>
										</ul>
									</li>
									<li>
										<a href="parts.php?show=products">
											<i class="fa fa-eyedropper" aria-hidden="true"></i>
											<span>Products</span>
										</a>
									</li>
									<li>
										<a href="users.php">
											<i class="fa fa-users" aria-hidden="true"></i>
											<span>Users</span>
										</a>
									</li>
									<li class="nav-parent<?php if ($page_id == 2) { ?> nav-active<?php } ?>">
										<a>
											<i class="fa fa-table" aria-hidden="true"></i>
											<span>Users</span>
										</a>
										<ul class="nav nav-children">
											<li>
												<a href="users.php">
													 View All
												</a>
											</li>
											<li>
												<a href="update_log.php">
													 Update Log
												</a>
											</li>
											<li>
												<a href="feedback.php">
													 Feedback
												</a>
											</li>
										</ul>
									</li>
									<li>
										<a href="materials.php">
											<i class="fa fa-flask" aria-hidden="true"></i>
											<span>Materials</span>
										</a>
									</li>
									<li>
										<a href="suppliers.php">
											<i class="fa fa-building" aria-hidden="true"></i>
											<span>Suppliers</span>
										</a>
									</li>
									<li>
										<a href="logout.php">
											<i class="fa fa-sign-out" aria-hidden="true"></i>
											<span>Log Out</span>
										</a>
									</li>
								</ul>
							</nav>

							<hr class="separator" />

							<div class="sidebar-widget widget-tasks">
								<div class="widget-header">
									<h6>Projects</h6>
									<div class="widget-toggle">+</div>
								</div>
								<div class="widget-content">
									<ul class="list-unstyled m-none">
										<li><a href="#">Porto HTML5 Template</a></li>
										<li><a href="#">Tucson Template</a></li>
										<li><a href="#">Porto Admin</a></li>
									</ul>
								</div>
							</div>

							<hr class="separator" />

							<div class="sidebar-widget widget-stats">
								<div class="widget-header">
									<h6>Company Stats</h6>
									<div class="widget-toggle">+</div>
								</div>
								<div class="widget-content">
									<ul>
										<li>
											<span class="stats-title">Stat 1</span>
											<span class="stats-complete">85%</span>
											<div class="progress">
												<div class="progress-bar progress-bar-primary progress-without-number" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 85%;">
													<span class="sr-only">85% Complete</span>
												</div>
											</div>
										</li>
										<li>
											<span class="stats-title">Stat 2</span>
											<span class="stats-complete">70%</span>
											<div class="progress">
												<div class="progress-bar progress-bar-primary progress-without-number" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: 70%;">
													<span class="sr-only">70% Complete</span>
												</div>
											</div>
										</li>
										<li>
											<span class="stats-title">Stat 3</span>
											<span class="stats-complete">2%</span>
											<div class="progress">
												<div class="progress-bar progress-bar-primary progress-without-number" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100" style="width: 2%;">
													<span class="sr-only">2% Complete</span>
												</div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>

					</div>

				</aside>
				<!-- end: sidebar -->
	<?

		if ($pages_found == 0) {
			echo '
			<section role="main" class="content-body">
			  <div class="row">
				<span class="btn btn-danger">Page not found in the database. Please contact the system administrator.</span>
			  </div>
			</section>';
		}


		/* ***************************************************************************** */
		/* ******************************* FEEDBACK FORM ******************************* */
		/* ***************************************************************************** */

		?>
		<div class="rotate bootstro" style="position: fixed; right: -30px; top: 50%; z-index: 9999; margin-top: -100px;"
                                    data-bootstro-title="feedback!"
                                    data-bootstro-content='You can click this on any page to send us feedback about your experience.<br /><br />We will also log the page you are on'
                                    data-bootstro-placement='left'
                                    data-bootstro-width='400px'
                                     data-bootstro-step='7'
                                     data-boostro-html='true'><a class="btn btn-warning simple-ajax-modal" href="info_pop.php?id=feedback&referrer=<?php echo $_SERVER['HTTP_REFERER']; ?>&ref_page=<?php echo $page_filename; ?>&page_id=<?php echo $page_id; ?>">Feedback / 反馈</a></div>
	<?php


		/* ***************************************************************************** */
		/* ***************************** END FEEDBACK FORM ***************************** */
		/* ***************************************************************************** */

	}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

	function pagefoot($page_id, $record_id=NULL) {


	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// let's see if we can see these:
	// DEBUG
	/*
	echo "<h1>GLOBAL VARS:</h1>";
	echo 'page id:' 		. $GLOBALS['page_id'] 				. '<br />'; 	// hoping to use this data in the footer also
	echo 'lookup table: ' 	. $GLOBALS['page_lookup_table'] 	. '<br />'; 	// hoping to use this data in the footer also
	echo 'page OG type:' 	. $GLOBALS['page_og_type'] 			. '<br />'; 	// hoping to use this data in the footer also
	echo '<h4>END PRINT VARS</h4>';
	echo '<hr />';
	*/


	if ( ($GLOBALS['page_og_type'] == 'list') || ($GLOBALS['page_og_type'] == 'profile') ) {
		// SHOW THE REVISION HISTORY FOR THIS PAGE / RECORD!

		$total_updates = 0;

		if ($GLOBALS['page_og_type'] == 'list') {

			$add_URL_vars = '';
			$update_header = '10 Most Recent Updates';

			// SHOW THE 10 LATEST UPDATES
			$get_updates_SQL = "SELECT * FROM `update_log` WHERE `table_name` = '" . $GLOBALS['page_lookup_table'] . "' ORDER BY `update_date` DESC LIMIT 0 , 10";

		}
		else if ($GLOBALS['page_og_type'] == 'profile') {

			$add_URL_vars = "&id=".$GLOBALS['record_id']."";
			$update_header = 'Update History';

			// SHOW THE 30 LATEST UPDATES
			$get_updates_SQL = "SELECT * FROM `update_log` WHERE `table_name` = '" . $GLOBALS['page_lookup_table'] . "' AND `update_ID` = '" . $GLOBALS['record_id'] . "' ORDER BY `update_date` DESC LIMIT 0 , 30";

		}

		// start the table:

		?>

		<section role="main" class="content-body">

		<div>
		<div class="col-md-12">

		<section class="panel panel-collapsed">
			<header class="panel-heading">
				<div class="panel-actions">
					<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
				</div>

				<h2 class="panel-title">
					<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
					<span class="va-middle"><?php echo $update_header; ?></span>
				</h2>
			</header>
			<div class="panel-body">
				<div class="content">


		<!-- START THE UPDATES TABLE: -->
		<table class="table table-striped table-no-more table-bordered  mb-none">
			<thead>
				<tr class="dark">
					<th style="width: 10%"><span class="text-normal text-sm">Type</span></th>
					<th style="width: 10%"><span class="text-normal text-sm">Action</span></th>
					<th style="width: 15%"><span class="text-normal text-sm">Date</span></th>
					<th><span class="text-normal text-sm">Message</span></th>
				</tr>
			</thead>
			<tbody class="log-viewer">

		<?php
			// DEBUG
			// echo $get_updates_SQL;

			// NOW RUN THE SQL!
			$result_get_updates = mysqli_query($con,$get_updates_SQL);
				// while loop
				while($row_get_updates = mysqli_fetch_array($result_get_updates)) {

					// NOW WRITE THE DATA:
					$update_ID 			= $row_get_updates['ID'];
					$table_name 		= $row_get_updates['table_name'];
					$update_ID 			= $row_get_updates['update_ID'];
					$update_user_ID 	= $row_get_updates['user_ID'];
					$update_notes 		= $row_get_updates['notes'];
					$update_date 		= $row_get_updates['update_date'];
					$update_type 		= $row_get_updates['update_type'];
					$update_action 		= $row_get_updates['update_action'];

					// now display the updates!

					?>
					<tr>
					  <td><?php echo $update_type; ?></td>
					  <td><?php

					  if ($update_action == 'UPDATE') {
					  	$update_icon 		= 'pencil';
					  	$update_color_code 	= 'warning';
					  }
					  else if ($update_action == 'DELETE') {
					  	$update_icon 		= 'times';
					  	$update_color_code 	= 'danger';
					  }
					  else if ($update_action == 'INSERT') {
					  	$update_icon 		= 'plus-square';
					  	$update_color_code 	= 'success';
					  }
					  else { /* NOT FOUND? */
					  	$update_icon 		= 'question-circle';
					  	$update_color_code 	= 'primary';
					  }

					  ?>
					    <a type="button" class="mb-xs mt-xs mr-xs btn btn-xs btn-<?php echo $update_color_code; ?>">
							<i class="fa fa-<?php echo $update_icon; ?>"></i>
							<?php echo $update_action; ?>
					    </a>
					  </td>
					  <td><?php echo $update_date; ?></td>
					  <td data-title="Message" class="pt-md pb-md">
						<?php
						// show the creator name and link:
						get_creator($update_user_ID);

						if ($update_action == 'UPDATE') {
							?> updated table <?php
						}
						else if ($update_action == 'INSERT') {
							?> added a record to table <?php
						}
						else if ($update_action == 'DELETE') {
							?> deleted a record from table <?php
						}
						?>'<?php echo $table_name; ?>', record #<?php echo $update_ID; ?>.
						<br />
						<strong>NOTE:</strong> <em>"<?php echo $update_notes; ?>"</em>
					  </td>
					</tr>
					<?php
						$total_updates = $total_updates + 1;

				} // END RESULTS LOOP


		if ($total_updates == 0) {
			?>
			<tr>
			  <td colspan="4">
			  	<strong>
			  		<span class="text-danger">
			  			0 UPDATES FOUND FOR THIS RECORD
			  		</span>
			  	</strong>
			  </td>
			</tr>
			<?php
		}

		// now close the table

		?>

		</tbody>
		<tfoot>
			<tr>
			  <td colspan="4"><strong>TOTAL UPDATES: <?php echo $total_updates; ?></strong></td>
			</tr>
		</tfoot>
		</table>

		<?php if ($GLOBALS['page_lookup_table'] == 'parts') { ?>
			<p class="text-warning"><strong>PLEASE NOTE: </strong>Showing updates for the 'PART' record only - for 'REVISION' updates, please click the 'VIEW REVISION UPDATES' button in the individual revision tabs above.</p>
		<?php } ?>

		</div>
		</div>

		<div class="panel-footer">
			<div class="text-left">

			<?php  if ($total_updates != 0) { ?>

				  <a type="button" class="mb-xs mt-xs mr-xs btn btn-xs btn-primary" href="update_log.php?table=<?php
					echo $GLOBALS['page_lookup_table'];
					echo $add_URL_vars;
				?>">
					<i class="fa fa-list"></i>
					View All Updates
				</a>

			<?php }
			else {
			?>
				<a type="button" class="mb-xs mt-xs mr-xs btn btn-xs btn-primary" href="update_log.php">
					<i class="fa fa-list"></i>
					View All Updates
				</a>
			<?php
			}
			?>

			</div>
		</div>

		</section><!-- END THE PANEL -->

		</div>
		</div><!-- END ROW -->
		</section>

	<?php
	} // END OF LIST AND PROFILE PAGE UPDATE TABLES IN THE BOTTOM OF THE PAGE
	?>

			</div>

			<aside id="sidebar-right" class="sidebar-right">
				<div class="nano">
					<div class="nano-content">
						<a href="#" class="mobile-close visible-xs">
							Collapse <i class="fa fa-chevron-right"></i>
						</a>

						<div class="sidebar-right-wrapper">

							<div class="sidebar-widget widget-calendar">
								<h6>Upcoming Tasks</h6>
								<div data-plugin-datepicker data-plugin-skin="dark" ></div>

								<ul>
									<li>
										<time datetime="2014-04-19T00:00+00:00">04/19/2014</time>
										<span>Company Meeting</span>
									</li>
								</ul>
							</div>

							<div class="sidebar-widget widget-friends">
								<h6>Friends</h6>
								<ul>
									<li class="status-online">
										<figure class="profile-picture">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe" class="img-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-online">
										<figure class="profile-picture">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe" class="img-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-offline">
										<figure class="profile-picture">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe" class="img-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-offline">
										<figure class="profile-picture">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe" class="img-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
								</ul>
							</div>

						</div>
					</div>
				</div>
			</aside>

			<?php

			if ($page_id == 2) {

			// run modal delete window for editable tables
			?>
		<div id="dialog" class="modal-block mfp-hide">
			<section class="panel">
				<header class="panel-heading">
					<h2 class="panel-title">Are you sure?</h2>
				</header>
				<div class="panel-body">
					<div class="modal-wrapper">
						<div class="modal-text">
							<p>Are you sure that you want to delete this row?</p>
						</div>
					</div>
				</div>
				<footer class="panel-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button id="dialogConfirm" class="btn btn-primary">Confirm</button>
							<button id="dialogCancel" class="btn btn-default">Cancel</button>
						</div>
					</div>
				</footer>
			</section>
		</div>
			<?

			}

			?>

			<!-- Vendor -->
			<script src="assets/vendor/jquery/jquery.js"></script>
			<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
			<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
			<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
			<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
			<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
			<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

			<!-- Specific Page Vendor (LIGHTBOX) -->
			<script src="assets/vendor/pnotify/pnotify.custom.js"></script>
			<!-- Examples (LIGHTBOX) -->
			<script src="assets/javascripts/ui-elements/examples.lightbox.js"></script>

			<!-- Specific Page Vendor - PROFILE -->
			<script src="assets/vendor/jquery-autosize/jquery.autosize.js"></script>

			<!-- Specific Page Vendor -->
			<?php

			if ($page_id == 2) {
				?>
				<script src="assets/vendor/select2/select2.js"></script>
				<script src="assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
				<script src="assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>

				<!-- Examples -->
				<script src="assets/javascripts/tables/examples.datatables.editable.js"></script>

				<?
			}

			?>

			<!-- Specific Page Vendor -->
		<script src="assets/vendor/pnotify/pnotify.custom.js"></script>

<!-- Examples -->
		<script src="assets/javascripts/ui-elements/examples.modals.js"></script>

		<!-- Specific Page Vendor - ADVANCED FORMS -->
		<script src="assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
		<script src="assets/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js"></script>
		<script src="assets/vendor/select2/select2.js"></script>
		<script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>
		<script src="assets/vendor/jquery-maskedinput/jquery.maskedinput.js"></script>
		<script src="assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
		<script src="assets/vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
		<script src="assets/vendor/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
		<script src="assets/vendor/fuelux/js/spinner.js"></script>
		<script src="assets/vendor/dropzone/dropzone.js"></script>
		<script src="assets/vendor/bootstrap-markdown/js/markdown.js"></script>
		<script src="assets/vendor/bootstrap-markdown/js/to-markdown.js"></script>
		<script src="assets/vendor/bootstrap-markdown/js/bootstrap-markdown.js"></script>
		<script src="assets/vendor/codemirror/lib/codemirror.js"></script>
		<script src="assets/vendor/codemirror/addon/selection/active-line.js"></script>
		<script src="assets/vendor/codemirror/addon/edit/matchbrackets.js"></script>
		<script src="assets/vendor/codemirror/mode/javascript/javascript.js"></script>
		<script src="assets/vendor/codemirror/mode/xml/xml.js"></script>
		<script src="assets/vendor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
		<script src="assets/vendor/codemirror/mode/css/css.js"></script>
		<script src="assets/vendor/summernote/summernote.js"></script>
		<script src="assets/vendor/bootstrap-maxlength/bootstrap-maxlength.js"></script>
		<script src="assets/vendor/ios7-switch/ios7-switch.js"></script>
		<script src="assets/vendor/bootstrap-confirmation/bootstrap-confirmation.js"></script>
		<script src="assets/vendor/intl-tel-input/js/intlTelInput.min.js"></script>

			<!-- Theme Base, Components and Settings -->
			<script src="assets/javascripts/theme.js"></script>

			<!-- Theme Custom -->
			<script src="assets/javascripts/theme.custom.js"></script>

			<!-- Theme Initialization Files -->
			<script src="assets/javascripts/theme.init.js"></script>

			<!--  Validations -->
			<script src="assets/vendor/jquery-validation/jquery.validate.js"></script>
			<script src="assets/javascripts/forms/examples.validation.js"></script>

		</section>
	</body>
</html>


	<?


	}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

function notify_me($page_id, $msg, $action, $change_record_id, $page_record_id){
					////////////////////////////////////////////////////////////
					// MESSAGE NOTIFICATIONS
					////////////////////////////////////////////////////////////
					if (isset($_REQUEST['msg'])) { ?>
					<div class="row">
						<div class="col-md-12">
						<?php
						if ($_REQUEST['msg'] == 'OK') {
						?>
							<div class="alert alert-success">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<!--  ADD -->
								<?php if ($_REQUEST['action'] == 'add') { ?>

								<span class="fa-stack fa-3x">
  									<i class="fa fa-circle-o fa-stack-2x"></i>
  									<i class="fa fa-check fa-stack-1x"></i>
								</span>


								<strong>Well done!</strong> You successfully added a record to the database.

								<?php } ?>

								<!--  UPDATE -->
								<?php if ($_REQUEST['action'] == 'edit') { ?>

								<span class="fa-stack fa-3x">
  									<i class="fa fa-circle-o fa-stack-2x"></i>
  									<i class="fa fa-check fa-stack-1x"></i>
								</span>


								<strong>Well done!</strong> You successfully updated the record in the database.
								<?php } ?>

								<!--  DELETE -->
								<?php if ($_REQUEST['action'] == 'delete') { ?>

								<span class="fa-stack fa-3x">
  									<i class="fa fa-circle-o fa-stack-2x"></i>
  									<i class="fa fa-check fa-stack-1x"></i>
								</span>


								<strong>Well done!</strong> You successfully deleted the record in the database.
								<?php } ?>

							</div>
						<?php
						} // END OF SUCCESS MESSAGES
						else if ($_REQUEST['msg'] == 'NG') {
						?>
							<div class="alert alert-warning">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<?php if ($_REQUEST['error'] == 'no_id') { ?>

								<span class="fa-stack fa-3x">
  									<i class="fa fa-circle-o fa-stack-2x"></i>
  									<i class="fa fa-exclamation fa-stack-1x"></i>
								</span>

								<strong>Oh No!</strong> You must select a record from the list below in order to view a single record page.


								<?php } ?>
								<!--  Login error -->
								<?php if ($_REQUEST['error'] == 'invalid_login') { ?>

								<span class="fa-stack fa-3x">
  									<i class="fa fa-circle-o fa-stack-2x"></i>
  									<i class="fa fa-exclamation fa-stack-1x"></i>
								</span>

								<h4>Invalid username or password.</h4>


								<?php } ?>
									<!--  Duplicates -->
								<?php if ($_REQUEST['error'] == 'duplicate') { ?>

								<span class="fa-stack fa-3x">
  									<i class="fa fa-circle-o fa-stack-2x"></i>
  									<i class="fa fa-exclamation fa-stack-1x"></i>
								</span>

								<h4><?php echo $_REQUEST['field']?> already exists in the system.</h4>


								<?php } ?>
							</div>
						<?php
						}
						?>
						</div>
					</div>
					<?php
					}

					////////////////////////////////////////////////////////////
					// END MESSAGE NOTIFICATIONS
					////////////////////////////////////////////////////////////
				} // end function

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

				function _base64_encrypt($str,$passw=null){
					$r='';
					$md=$passw?substr(md5($passw),0,16):'';
					$str=base64_encode($md.$str);
					$abc='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
					$a=str_split('+/='.$abc);
					$b=strrev('-_='.$abc);
					if($passw){
						$b=_mixing_passw($b,$passw);
					}else{
						$r=rand(10,65);
						$b=mb_substr($b,$r).mb_substr($b,0,$r);
					}
					$s='';
					$b=str_split($b);
					$str=str_split($str);
					$lens=count($str);
					$lena=count($a);
					for($i=0;$i<$lens;$i++){
						for($j=0;$j<$lena;$j++){
							if($str[$i]==$a[$j]){
								$s.=$b[$j];
							}
						};
					};
					return $s.$r;
				};

				function _base64_decrypt($str,$passw=null){
					$abc='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
					$a=str_split('+/='.$abc);
					$b=strrev('-_='.$abc);
					if($passw){
						$b=_mixing_passw($b,$passw);
					}else{
						$r=mb_substr($str,-2);
						$str=mb_substr($str,0,-2);
						$b=mb_substr($b,$r).mb_substr($b,0,$r);
					}
					$s='';
					$b=str_split($b);
					$str=str_split($str);
					$lens=count($str);
					$lenb=count($b);
					for($i=0;$i<$lens;$i++){
						for($j=0;$j<$lenb;$j++){
							if($str[$i]==$b[$j]){
								$s.=$a[$j];
							}
						};
					};
					$s=base64_decode($s);
					if($passw&&substr($s,0,16)==substr(md5($passw),0,16)){
						return substr($s,16);
					}else{
						return $s;
					}
				};

				function _mixing_passw($b,$passw){
					$s='';
					$c=$b;
					$b=str_split($b);
					$passw=str_split(sha1($passw));
					$lenp=count($passw);
					$lenb=count($b);
					for($i=0;$i<$lenp;$i++){
						for($j=0;$j<$lenb;$j++){
							if($passw[$i]==$b[$j]){
								$c=str_replace($b[$j],'',$c);
								if(!preg_match('/'.$b[$j].'/',$s)){
									$s.=$b[$j];
								}
							}
						};
					};
					return $c.''.$s;
				};
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function get_creator($user_id) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	if ($user_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO USER FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {

		// get user
		$get_user_SQL = "SELECT * FROM  `users` WHERE  `ID` =" . $user_id;
		$result_get_user = mysqli_query($con,$get_user_SQL);
		// DEBUG:
		// echo "<h4>SQL: " . $get_user_SQL . "</h4>";

		// while loop
		while($row_get_user = mysqli_fetch_array($result_get_user)) {
				// now print each record:
				$user_first_name = $row_get_user['first_name'];
				$user_last_name = $row_get_user['last_name'];
				$user_name_CN = $row_get_user['name_CN'];
		}
		?>
		<a href="user_view.php?id=<?php echo $user_id; ?>" title="Click here to view this user profile">
		  <?php
			echo $user_first_name . " " . $user_last_name;
			if (($user_name_CN!='')&&($user_name_CN!='中文名')) {
			  echo " / " . $user_name_CN;
			}
		  ?>
		</a>
		<?php
	}
}
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function get_supplier($sup_id) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	if ($sup_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO SUPPLIER FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {

		/* ***************  GET SUPPLIER INFO ************************** */

		// now get the record info:
		$get_sups_SQL = "SELECT * FROM `suppliers` WHERE `ID` = " . $sup_id;
		// echo $get_sups_SQL;

		$result_get_sups = mysqli_query($con,$get_sups_SQL);

		// while loop
		while($row_get_sup = mysqli_fetch_array($result_get_sups)) {
			$sup_ID = $row_get_sup['ID'];
			$sup_en = $row_get_sup['name_EN'];
			$sup_cn = $row_get_sup['name_CN'];
			$sup_web = $row_get_sup['website'];
			$sup_internal_ID = $row_get_sup['epg_supplier_ID'];
			$sup_status = $row_get_sup['record_status'];
			$sup_part_classification = $row_get_sup['part_classification']; // look up
			$sup_item_supplied = $row_get_sup['items_supplied'];
			$sup_part_type_ID = $row_get_sup['part_type_ID']; // look up
			$sup_certs = $row_get_sup['certifications'];
			$sup_cert_exp_date = $row_get_sup['certification_expiry_date'];
			$sup_evaluation_date = $row_get_sup['evaluation_date'];
			$sup_address_EN = $row_get_sup['address_EN'];
			$sup_address_CN = $row_get_sup['address_CN'];
			$sup_country_ID = $row_get_sup['country_ID']; // look up
			$sup_contact_person = $row_get_sup['contact_person'];
			$sup_mobile_phone = $row_get_sup['mobile_phone'];
			$sup_telephone = $row_get_sup['telephone'];
			$sup_fax = $row_get_sup['fax'];
			$sup_email_1 = $row_get_sup['email_1'];
			$sup_email_2 = $row_get_sup['email_2'];

					// VENDOR CLASSIFICATION BY STATUS:

					$get_sup_status_SQL = "SELECT * FROM `supplier_status` WHERE `status_level` ='" . $sup_status . "'";
					// echo $get_vendor_status_SQL;

					$result_get_sup_status = mysqli_query($con,$get_sup_status_SQL);
					// while loop
					while($row_get_sup_status = mysqli_fetch_array($result_get_sup_status)) {
						$sup_status_ID = $row_get_sup_status['ID'];
						$sup_status_name_EN = $row_get_sup_status['name_EN'];
						$sup_status_name_CN = $row_get_sup_status['name_CN'];
						$sup_status_level = $row_get_sup_status['status_level'];
						$sup_status_description = $row_get_sup_status['status_description'];
						$sup_status_color_code = $row_get_sup_status['color_code'];
						$sup_status_icon = $row_get_sup_status['icon'];
					}



					// GET PART CLASSIFICATION:
					$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $sup_part_classification . "'";
					// echo $get_part_class_SQL;

					$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
					// while loop
					while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
						$part_class_EN = $row_get_part_class['name_EN'];
						$part_class_CN = $row_get_part_class['name_CN'];
						$part_class_description = $row_get_part_class['description'];
						$part_class_color = $row_get_part_class['color'];
					}

					// NOW DISPLAY THE VENDOR DETAILS!

					?>
						  <a href="supplier_view.php?id=<?php echo $sup_ID; ?>" title="Click to view this supplier profile">
							<?php echo $sup_en; if (($sup_cn!='')&&($sup_cn!='中文名')){ echo " / " . $sup_cn; } ?>
						  </a>
					<?php

		} // end get record WHILE loop

		/* *************** END GET SUPPLIER INFO *********************** */

	} // END IF ELSE
} // CLOSE FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */


function creator_drop_down($this_user_ID) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// now output the result:

	?>
	<select class="form-control populate" name="created_by" id="created_by">
		<?php
		// GET PART TYPE:
		$get_user_list_SQL = "SELECT * FROM  `users` WHERE `record_status` = 2";
		// echo $get_part_type_SQL;
		$result_get_user_list = mysqli_query($con,$get_user_list_SQL);
		// while loop
		while($row_get_user = mysqli_fetch_array($result_get_user_list)) {

			$user_ID = $row_get_user['ID'];
			$user_fn = $row_get_user['first_name'];
			$user_mn = $row_get_user['middle_name'];
			$user_ln = $row_get_user['last_name'];
			$user_name_cn = $row_get_user['name_CN'];
			$user_email = $row_get_user['email'];
			$user_level = $row_get_user['user_level'];
			$user_position = $row_get_user['position'];
			$user_last_login_date = $row_get_user['last_login_date'];
			$user_facebook = $row_get_user['facebook_profile'];
			$user_linkedin = $row_get_user['linkedin_profile'];
			$user_twitter = $row_get_user['twitter_profile'];
			$user_wechat = $row_get_user['wechat_profile'];
			$user_skype = $row_get_user['skype_profile'];
			$user_record_status = 	$row_get_user['record_status']; // should be 2

		?>
			<option value="<?php echo $user_ID; ?>"<?php if ($user_ID == $this_user_ID) { ?> selected="selected"<?php } ?>><?php echo $user_fn; ?> <?php echo $user_ln; if (($user_name_cn!='')&&($user_name_cn!='中文名')) { echo " / " . $user_name_cn; } ?></option>
		<?php
		} // end get part type loop
		?>
	</select>
	<?php


} // CLOSE FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
function record_status_drop_down($current_status) {
// now output the result:
?>
<select class="form-control populate" name="record_status" id="record_status">
  <option value="0"<?php if ($current_status == 0) { ?> selected="selected"<?php } ?>>✘ DELETED ✘</option>
  <option value="1"<?php if ($current_status == 1) { ?> selected="selected"<?php } ?>>? PENDING ?</option>
  <option value="2"<?php if ($current_status == 2) { ?> selected="selected"<?php } ?>>✔ PUBLISHED ✔</option>
</select>
<?php
} // CLOSE FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
function record_status($current_status) {
	// we will make this a small button and look awesome:

	if ($current_status == 0) {
		// DELETED / UNPUBLISHED
		?>
		<a type="button" class="mb-xs mt-xs mr-xs btn btn-xs btn-danger">
			<i class="fa fa-times"></i>
			UNPUBLISHED
			<i class="fa fa-times"></i>
		</a>
		<?php
	}
	else if ($current_status == 1) {
		// PENDING
		?>
		<a type="button" class="mb-xs mt-xs mr-xs btn btn-xs btn-warning">
			<i class="fa fa-question-circle"></i>
			PENDING
			<i class="fa fa-question-circle"></i>
		</a>
		<?php
	}
	else if ($current_status == 2) {
		// OK
		?>
		<a type="button" class="mb-xs mt-xs mr-xs btn btn-xs btn-success">
			<i class="fa fa-check"></i>
			PUBLISHED
			<i class="fa fa-check"></i>
		</a>
		<?php
	}
	else {
	// ?
	}

} // CLOSE FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */


function admin_bar($add_edit_file_name_append) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// establish some variables:
	$table_name 	= $GLOBALS['page_lookup_table'];
	$record_id 		= $GLOBALS['record_id'];
	$public_path 	= pathinfo($_SERVER['SCRIPT_NAME']); // don't need this, except for the next line:
	$this_file 		= $public_path['basename'];

	?>
	<!-- ADMIN BAR -->
	<div class="btn-group btn-group-justified">
		<a class="btn btn-danger" title="DELETE THIS RECORD" href="record_delete_do.php?table_name=<?php echo $table_name; ?>&src_page=<?php echo $this_file; ?>&id=<?php echo $record_id; ?>"><i class="fa fa-trash"></i></a>
		<a class="btn btn-warning" title="EDIT THIS RECORD" href="<?php echo $add_edit_file_name_append; ?>_edit.php?id=<?php echo $record_id; ?>"><i class="fa fa-pencil"></i></a>
		<a class="btn btn-success" title="ADD A NEW RECORD" href="<?php echo $add_edit_file_name_append; ?>_add.php"><i class="fa fa-plus"></i></a>
		<a class="btn btn-info" title="UPDATE LOG" href="update_log.php?table_name=<?php echo $table_name; ?>&src_page=<?php echo $this_file; ?>&id=<?php echo $record_id; ?>"><i class="fa fa-question-circle"></i></a>
	</div>
	<!-- END ADMIN BAR -->
<?php
} // CLOSE FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
?>
