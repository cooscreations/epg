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
			$page_id = $row_get_page['ID'];
			$page_name_EN = $row_get_page['name_EN'];
			$page_name_CN = $row_get_page['name_CN'];
			$page_parent_ID = $row_get_page['parent_ID'];
			$page_dept_ID = $row_get_page['dept_ID'];
			$page_main_menu = $row_get_page['main_menu'];
			$page_footer_menu = $row_get_page['footer_menu'];
			$page_filename = $row_get_page['filename'];
			$page_icon = $row_get_page['icon'];
			$page_privacy = $row_get_page['privacy'];
			$page_min_user_level = $row_get_page['min_user_level'];
			$page_created_by = $row_get_page['created_by'];
			$page_date_created = $row_get_page['date_created'];
			$page_status = $row_get_page['status'];
			$page_order = $row_get_page['order'];
			$page_og_locale = $row_get_page['og_locale'];
			$page_og_type = $row_get_page['og_type'];
			$page_og_desc = $row_get_page['og_desc'];
			$page_og_section = $row_get_page['og_section'];
			$page_side_bar_config = $row_get_page['side_bar_config'];
			$page_lookup_table = $row_get_page['lookup_table'];
			
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
		} ?></title>
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
									<li class="nav-parent">
										<a>
											<i class="fa fa-eyedropper" aria-hidden="true"></i>
											<span>Products</span>
										</a>
										<ul class="nav nav-children">
											<li>
												<a href="products.php">
													 View All
												</a>
											</li>
											<li>
												<a href="devices.php">
													 Devices
												</a>
											</li>
											<li>
												<a href="consumables.php">
													 Consumables
												</a>
											</li>
										</ul>
									</li>
									<li>
										<a href="users.php">
											<i class="fa fa-users" aria-hidden="true"></i>
											<span>Users</span>
										</a>
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
								
								
								<strong>Well done!</strong> You successfully added a record to the database. You can see it below.
								
								<?php } ?>
								
								<!--  UPDATE -->
								<?php if ($_REQUEST['action'] == 'edit') { ?>
								
								<span class="fa-stack fa-3x">
  									<i class="fa fa-circle-o fa-stack-2x"></i>
  									<i class="fa fa-check fa-stack-1x"></i>
								</span>
								
								
								<strong>Well done!</strong> You successfully updated the record in the database. You can see it below.
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

?>