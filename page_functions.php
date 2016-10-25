<?php

/* 

REFERENCE LIST OF FUNCTIONS:

ID	AUTHOR		NAME / VARS																PURPOSE											NOTES

1	  MC		checkaddslashes($str)													Remove ' on form input					
2	  MC		realStrip($input)														Remove ' on form input					
3	  MC		check_date_time($date_to_check)											Add trailing HH:MM:SS to date					Surely there's a beter way to do this within PHP?
4 	  MC		year_jumper($jump_to_URL, $show_year = '0', $start_year = 2010)			Drop down with YEAR select				
5	  MC		pagehead($page_id, $record_id=NULL)										BUILD THE MAIN PAGE!!							Very important
6	  MC		pagefoot($page_id, $record_id=NULL)										BUILD THE REST OF THE PAGE!!					Very important
7	  MC		notify_me($page_id, $msg, $action, $change_record_id, $page_record_id)	Notification panel (top center)					Communicate something special to the user - this would be better as a true notification pop-up rather than a space of text...
8	  VK		_base64_encrypt($str,$passw=null)										?												
9	  VK		_base64_decrypt($str,$passw=null)										?												
10	  VK		_mixing_passw($b,$passw)												?												
11	  MC		get_creator($user_id, $display_weblink = 1)								Display user name and link (optional)			This could change to a nice Faebook-style pop-up with more user info
12	  MC		get_supplier($sup_id, $display_type = 0, $profile_link = 1)				Display supplier name and link					
13	  MC		creator_drop_down($this_user_ID, $form_element_name = 'created_by')		Show list of users with preselected (op.)		
14	  MC		supplier_drop_down($this_sup_ID, $form_element_name = 'sup_ID')			Show list of suppliers with preselect (op.)		
15	  MC		record_status_drop_down($current_status)								Show record status drop-down
16	  MC		admin_bar($add_edit_file_name_append)									Admin controls to edit a record					This is usually displayed on a record VIEW page and could be updated to be just a cog with pop-up
17	  MC		get_part_name($part_id, $profile_link)
18	  MC		get_location($loc_id, $display_type = 0, $profile_link = 1)
19	  MC		location_drop_down($this_loc_ID, $form_element_name = 'loc_ID')
20	  MC		form_buttons($cancel_url, $record_id)
21	  MC		add_button($record_id, $add_page_url, $record_var = 'id', $add_title='Click here to add a new record to this table', $add_url = '')
22	  MC		part_num_button($part_id)
23	  MC		part_drop_down($current_ID=0)
24	  MC		part_rev_drop_down($current_ID=0)
25	  MC		purchase_orders_drop_down($part_batch_po_id=0)
26	  MC		part_num($part_id, $show_button = 1)
27	  MC		part_rev($part_rev_id, $show_button = 1)
28	  MC		part_name($part_id, $show_button = 1)
29 	  MC		part_img($part_rev_id, $profile_link = 1, $img_width_px = 100)
30 	  MC		batch_num_dropdown($record_id = 0)
31	  MC		record_status($record_status_ID, $show_button = 1)

*/





	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// header('Content-Type: text/html; charset=utf-8');
	include 'form_data_helper.php';
	
	
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
	
	function checkaddslashes($str){        
		if(strpos(str_replace("\'",""," $str"),"'")!=false)
			return addslashes($str);
		else
			return $str;
	}
	
	// WHAT IS THIS?:
	function realStrip($input)
	{
		global $con;
		return mysqli_real_escape_string($con, stripslashes(trim($input)));
	}
	
	
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
	
	// CHECK DATES!!!!
	function check_date_time($date_to_check) {
	
		if ($date_to_check == '') {
			$date_to_check = '0000-00-00 00:00:00';
		}

		if (substr($date_to_check, -3, 1) == ':') { 
			$date_to_return = $date_to_check; // THIS HAS MINUTES AND HOURS ALREADY!
		}
		else { 
			$date_to_return = $date_to_check . ' 00:00:00'; // NO TIME ALREADY, LET'S ADD IT NOW!
		}
		return $date_to_return;

	}
	
	
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
	
	// CODE BLOCK TO ESTABLISH SORT VARS FROM URL?
	$add_URL_vars_sort = '';
	$add_URL_vars_dir = '';
	$add_URL_vars_year = "&year=" . date("Y");
	$display_year = date("Y");

	if (isset($_REQUEST['sort'])) {

		$add_URL_vars_sort = '&sort=' . $_REQUEST['sort'];
	}

	if (isset($_REQUEST['dir'])) {

		$add_URL_vars_dir = '&dir=' . $_REQUEST['dir'];
	}

	if (isset($_REQUEST['year'])) {
		if ($_REQUEST['year']!='all') {
			$add_URL_vars_year = '&year=' . $_REQUEST['year'];
			$display_year = 'all';
		}
		else {
			$add_URL_vars_year = '';
			$display_year = $_REQUEST['year'];
		}
	}
	
	$GLOBALS['display_year'] 		= $display_year;
	$GLOBALS['add_URL_vars_year'] 	= $add_URL_vars_year;
	$GLOBALS['add_URL_vars_dir'] 	= $add_URL_vars_dir;
	$GLOBALS['add_URL_vars_sort'] 	= $add_URL_vars_sort;
	
	// END OF SORTING VARS CODE BLOCK 
	
	
	// START FUNCTION TO SHOW 'JUMP TO ANOTHER YEAR' info

	
	function year_jumper($jump_to_URL, $show_year = '0', $start_year = 2010) {
			
		if ($show_year == 0) {
			// $default show year:
			$show_year = date("Y");
		}
		
		// start the session:
		session_start();
		// enable the DB connection:
		include 'db_conn.php';
	
		$loop_year = $start_year;
		
		
		// $GLOBALS['display_year'];
		// $GLOBALS['add_URL_vars_year'];
		// $GLOBALS['add_URL_vars_dir'];
		// $GLOBALS['add_URL_vars_sort'];
		
	 
		?>
	
		<div class="row">
			<div class="col-md-12">
				<!-- YEAR JUMPER -->

				<select onchange="document.location = this.value" data-plugin-selecttwo class="form-control populate">
					<option value="#" selected="selected">SELECT A YEAR / 选一年:</option>
					<option value="<?php 
						echo $jump_to_URL; 
					?>.php?year=all<?php 
						echo $GLOBALS['add_URL_vars_sort'] . $GLOBALS['add_URL_vars_dir'];; 
					?>">
						View All / 看全部
					</option>

					<?php

						while ($loop_year <= date("Y")) {
							?>
					<option value="<?php 
						echo $jump_to_URL; 
					?>.php?year=<?php 
						echo $loop_year; 
					?><?php 
						echo $GLOBALS['add_URL_vars_sort'] . $GLOBALS['add_URL_vars_dir'];; 
					?>"<?php 
					if ($loop_year == $show_year) { ?> selected="selected"<?php } ?>>SHOW POs FOR <?php 
						echo $loop_year; 
					?> 的订单</option>
							<?
							$loop_year = $loop_year + 1;
						}
					?>
					<option value="<?php 
						echo $jump_to_URL; 
					?>.php?year=all<?php 
						echo $GLOBALS['add_URL_vars_sort'] . $GLOBALS['add_URL_vars_dir'];; 
					?>">
						View All / 看全部
					</option>
				</select>
				<!-- / YEAR JUMPER -->
			</div>
		</div>
		<br />

		<?php
		// RESET THE LEAP YEAR VAR in case we need it later
		$loop_year = $start_year;
	
	}
	// END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	

	function pagehead($page_id, $record_id=NULL) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	$page_load_time_now = microtime();
	$page_load_time_now = explode(' ', $page_load_time_now);
	$page_load_time_now = $page_load_time_now[1] + $page_load_time_now[0];
	$page_load_start_time = $page_load_time_now;
	$GLOBALS['page_load_start_time'] = $page_load_start_time;

	// use this to get the current (active) page info:

	header('Content-Type: text/html; charset=utf-8');

	$public_path 			= pathinfo($_SERVER['SCRIPT_NAME']);
	$this_file 				= $public_path['basename'];
	$GLOBALS['this_file'] 	= $this_file; 


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
							$add_title_info = " PO # " . $row_get_title_extra['PO_number']; // this is NOT appended - it overwrites!
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
							$add_title_info = " BATCH # " . $row_get_title_extra['batch_number']; // this is NOT appended - it overwrites!
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
		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css" type="text/css" media="print" ><!-- printer-friendly? -->

		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css" />
		<link rel="stylesheet" href="assets/vendor/intl-tel-input/css/intlTelInput.css" />

		<?php
		if ($page_id == 2) {
			?>
		<!-- Specific Page Vendor CSS -->
			<link rel="stylesheet" href="assets/vendor/select2/select2.css" />
			<link rel="stylesheet" href="assets/vendor/jquery-datatables-bs3/assets/css/datatables.css" />
			<?php
		}
		?>
		
		<!-- Specific Page Vendor CSS - BASIC FORMS -->		
		<link rel="stylesheet" href="assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css" />
		
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
	<!--  NICE LOADING FEATURE BUT IT HANGS WHEN THE PAGE IS TOO LARGE?!  
	<body class="loading-overlay-showing" data-loading-overlay>
    
    <div class="loading-overlay dark">
        <div class="loader white"></div>
        <p class="text-center">LOADING, PLEASE WAIT...</p>
    </div>
    -->
    
    
    
		<section class="body">

			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="/" class="logo">
						<img src="assets/images/logo.png" height="35" alt="European Pharma Group" title="European Pharma Group" />
					</a>
					<div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
					</div>
				</div>

				<!-- start: search & user box -->
				<div class="header-right">

					<form action="search.php" class="search nav-form">
						<div class="input-group input-search">
							<input type="text" class="form-control" name="query" id="query" placeholder="Search / 搜索...">
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
								<?php get_img('users', $result_row['ID'], 0, 'header'); ?>
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
									<a role="menuitem" tabindex="-1" href="user_view.php?id=<?php echo $result_row['ID']; ?>"><i class="fa fa-user"></i> My Profile / 我的简历</a>
								</li>
								<li>
									<a role="menuitem" tabindex="-1" href="#" data-lock-screen="true"><i class="fa fa-lock"></i> Lock Screen / 锁屏</a>
								</li>
								<li>
									<a role="menuitem" tabindex="-1" href="logout.php"><i class="fa fa-power-off"></i> Logout / 登出</a>
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
							Navigation / 导航
						</div>
						<div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
							<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
						</div>
					</div>

					<div class="nano">
						<div class="nano-content">
							<nav id="menu" class="nav-main" role="navigation">
								<?php main_menu(1); ?>
								<!-- 
								<ul class="nav nav-main">
									<li<?php if ($page_id == 1) { ?> nav-active<?php } ?>>
										<a href="index.php">
											<i class="fa fa-home" aria-hidden="true"></i>
											<span>Dashboard / 主页</span>
										</a>
									</li>
									<li class="nav-parent<?php if ($page_id == 2) { ?> nav-active<?php } ?>">
										<a>
											<i class="fa fa-table" aria-hidden="true"></i>
											<span>Logs / 日志</span>
										</a>
										<ul class="nav nav-children">
											<li>
												<a href="logs.php">
													 View All / 查看全部
												</a>
											</li>
											<li>
												<a href="master_document_log.php">
													 Master Document Log / 主控文档记录
												</a>
											</li>
											<li>
												<a href="parts.php">
													 Part Number / 零件编号
												</a>
											</li>
											<li>
												<a href="part_revisions.php">
													 Part Revisions / 部分修订
												</a>
											</li>
											<li>
												<a href="BOM.php">
													 Bill Of Material (BOM) / 物料清单
												</a>
											</li>
											<li>
												<a href="purchase_orders.php">
													 Purchase Orders / 订单
												</a>
											</li>
											<li>
												<a href="batch_log.php">
													 Batch Log / 批处理日志
												</a>
											</li>
											<li>
												<a href="warehouse_stock_log.php">
													 Warehouse Stock Log / 仓库库存日志
												</a>
											</li>
										</ul>
									</li>
									<li>
										<a href="parts.php?show=products">
											<i class="fa fa-eyedropper" aria-hidden="true"></i>
											<span>Products / 产品</span>
										</a>
									</li>
									<li class="nav-parent">
										<a>
											<i class="fa fa-table" aria-hidden="true"></i>
											<span>Documents / 文件</span>
										</a>
										<ul class="nav nav-children">
											<li>
												<a href="documents.php">
													 View All / 查看全部
												</a>
											</li>
											<li>
												<a href="upload_file.php">
													 Upload New Doc.
												</a>
											</li>
										</ul>
									</li>
									<li class="nav-parent<?php if ($page_id == 2) { ?> nav-active<?php } ?>">
										<a>
											<i class="fa fa-users" aria-hidden="true"></i>
											<span>Users / 用户</span>
										</a>
										<ul class="nav nav-children">
											<li>
												<a href="users.php">
													 View All / 查看全部
												</a>
											</li>
											<li>
												<a href="update_log.php">
													 Update Log / 更新日志
												</a>
											</li>
											<li>
												<a href="feedback.php">
													 Feedback / 反馈
												</a>
											</li>
										</ul>
									</li>
									<li>
										<a href="materials.php">
											<i class="fa fa-flask" aria-hidden="true"></i>
											<span>Materials / 材料</span>
										</a>
									</li>
									<li>
										<a href="suppliers.php">
											<i class="fa fa-building" aria-hidden="true"></i>
											<span>Suppliers / 供应商</span>
										</a>
									</li>
									<li>
										<a href="logout.php">
											<i class="fa fa-sign-out" aria-hidden="true"></i>
											<span>Log Out / 登出</span>
										</a>
									</li>
								</ul>
								-->
							</nav>

							<hr class="separator" />
<!-- 
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
-->
						</div>

					</div>

				</aside>
				<!-- end: sidebar -->
	<?

		if ($pages_found == 0) {
			echo '
			<section role="main" class="content-body">
			  <div class="row">
				<span class="btn btn-danger">Page not found in the database. Please contact the system administrator. 网页在数据库中找不到。请与系统管理员联系。</span>
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

		<section role="main" class="content-body content-footer-body">

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

			<?php

			if ($page_id == 2) {
				?>
			<!-- Specific Page Vendor -->
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
		
		
		
		<!-- Specific Page Vendor JS - BASIC FORMS-->
		<script src="assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>

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

		<?php 
		
		$page_load_end_time = $page_load_time_now;
		
		$page_load_end_time = microtime();
		$page_load_end_time = explode(' ', $page_load_end_time);
		$page_load_end_time = $page_load_end_time[1] + $page_load_end_time[0];
		$finish_load_time = $page_load_end_time;
		$total_page_load_time = round(($page_load_end_time - $GLOBALS['page_load_start_time']), 4);
		$GLOBALS['page_load_end_time'] = $page_load_end_time;
		?>
		
		<div class="row text-center">
			<small class="text-center">Page generated in <?php echo $total_page_load_time; ?> seconds.</small>
		</div>
		
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
								<?php if ($_REQUEST['action'] == 'add') { ?>
									<!--  ADD -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-check fa-stack-1x"></i>
									</span>
									<strong>Well done!</strong> You successfully added a record to the database.
								<?php }
								
								if ($_REQUEST['action'] == 'add_line_item') { ?>
									<!-- ADD NEW LINE ITEM -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-check fa-stack-1x"></i>
									</span>
									<strong>Well done!</strong> You successfully added a new line item to this Purchase Order.
								<?php 
								}
								
								if ($_REQUEST['action'] == 'add_BOM_item') { ?>
									<!-- ADD NEW LINE ITEM -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-check fa-stack-1x"></i>
									</span>
									<strong>Well done!</strong> You successfully added a new line item to this Bill of Materials.
								<?php 
								}
								
								if ($_REQUEST['action'] == 'add_batch') { ?>
									<!-- ADD NEW LINE ITEM -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-check fa-stack-1x"></i>
									</span>
									<strong>Well done!</strong> You successfully added a new batch to this product record.
								<?php 
								}
								
								
								if ($_REQUEST['action'] == 'edit') { ?>
									<!--  UPDATE -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-check fa-stack-1x"></i>
									</span>
									<strong>Well done!</strong> You successfully updated the record in the database.
								<?php }
								
								if ($_REQUEST['action'] == 'delete') { ?>
									<!--  DELETE -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-check fa-stack-1x"></i>
									</span>
									<strong>Well done!</strong> You successfully deleted the record in the database.
								<?php 
								}
								
								if ($_REQUEST['action'] == 'feedback_sent') { ?>
									<!--  Feedback Sent -->
									<span class="fa-stack fa-3x">
											<i class="fa fa-circle-o fa-stack-2x"></i>
											<i class="fa fa-check fa-stack-1x"></i>
									</span>
									<strong>Thank you!</strong> Your feedback is sent.
								<?php 
								} 
								
								if ($_REQUEST['action'] == 'part_mat_map') { ?>
									<!--  Material to Part Map Change Request Complete! -->
									<span class="fa-stack fa-3x">
											<i class="fa fa-circle-o fa-stack-2x"></i>
											<i class="fa fa-check fa-stack-1x"></i>
									</span>
									<strong>Material Record Updated!</strong> The part record has now been updated. Thank you.
								<?php 
								}
								?>
							</div>
						<?php
						} // END OF SUCCESS MESSAGES
						else if ($_REQUEST['msg'] == 'NG') {
						
						?>
							<div class="alert alert-warning">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<?php
								
								if ($_REQUEST['error'] == 'level') { ?>
									<!-- USER LEVEL ISN'T HIGH ENOUGH -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-exclamation fa-stack-1x"></i>
									</span>
									<strong>Oh No!</strong> Page # <?php echo $_REQUEST['page_id']; ?> has a user access level of <?php echo $_REQUEST['mul']; ?> and your user level is only <?php echo $_REQUEST['sul']; ?>. Please contact the administrator to change access settings.
								<?php 
								}
								
								if ($_REQUEST['error'] == 'no_id') { ?>
									<!-- NO RECORD ID -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-exclamation fa-stack-1x"></i>
									</span>
									<strong>Oh No!</strong> You must select a record from the list below in order to view a single record page.
								<?php 
								} 
								
								if ($_REQUEST['error'] == 'no_po_id') { ?>
									<!-- NO PURCHASE ORDER ID -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-exclamation fa-stack-1x"></i>
									</span>
									<strong>Oh No!</strong> You must select an existing Purchase Order to do that.
								<?php 
								}
								
								if ($_REQUEST['error'] == 'exists') { ?>
									<!-- NO PURCHASE ORDER ID -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-exclamation fa-stack-1x"></i>
									</span>
									<strong>FILE FOUND!</strong> That file is already in the system. Please rename the new file or edit the existing one.
								<?php 
								}
								
								if ($_REQUEST['error'] == 'invalid_login') { ?>
									<!--  Login error -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-exclamation fa-stack-1x"></i>
									</span>
									<strong>Invalid username or password.</strong>
								<?php 
								}
								
								if ($_REQUEST['error'] == 'duplicate') { ?>
									<!--  Duplicates -->
									<span class="fa-stack fa-3x">
										<i class="fa fa-circle-o fa-stack-2x"></i>
										<i class="fa fa-exclamation fa-stack-1x"></i>
									</span>
									<?php if (isset($_REQUEST['field'])) { ?>
										<strong>DUPLICATE FOUND:</strong> <?php echo $_REQUEST['field']?> already exists in the system.
									<?php 
											if ($_REQUEST['existing_part_ID']) { ?>
												 <a href="part_view.php?id=<?php echo $_REQUEST['existing_part_ID']; ?>">VIEW PART PROFILE</a>
											<?php }
									} 
									else {
									?>
										<strong>DUPLICATE FOUND:</strong> A duplicate record was found. Please try again.
									<?php
									}
									?>
								<?php 
								} 
								
								?>
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

function get_creator($user_id, $display_weblink = 1) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	if ($user_id == 0) {
	  if ($display_weblink == 1) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO USER FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
		}
		else {
			?>
			*** NO USER FOUND! ***
			<?php
		}
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
		
		if ($display_weblink == 1) { 
			$open_link = '<a href="user_view.php?id=' . $user_id . '" title="Click here to view this user profile">';
			$close_link = '</a>';
		}
		else {
			$open_link = '';
			$close_link = '';
		}
			echo $open_link;
			echo $user_first_name . " " . $user_last_name;
			if (($user_name_CN!='')&&($user_name_CN!='中文名')) {
			  echo " / " . $user_name_CN;
			}
			echo $close_link;
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

function get_supplier($sup_id, $display_type = 0, $profile_link = 1) {

	// debug:
	// echo "display type = " . $display_type;

	/* 
	
	DISPLAY TYPES:
	
	0 / null / default	=	display the company name and a link to the profile page
	1 					=	show the name and address for the purchase order
	2					= 	show minial output for purchase order PRINT view
	*/

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	// echo 'get_supplier function called OK';

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
			$sup_ID 					= $row_get_sup['ID'];
			$sup_en 					= $row_get_sup['name_EN'];
			$sup_cn 					= $row_get_sup['name_CN'];
			$sup_internal_ID 			= $row_get_sup['epg_supplier_ID'];
			$sup_status 				= $row_get_sup['record_status'];
			$sup_part_classification 	= $row_get_sup['part_classification']; // look up
			$sup_item_supplied 			= $row_get_sup['items_supplied'];
			$sup_part_type_ID 			= $row_get_sup['part_type_ID']; // look up
			$sup_certs 					= $row_get_sup['certifications'];
			$sup_cert_exp_date 			= $row_get_sup['certification_expiry_date'];
			$sup_evaluation_date 		= $row_get_sup['evaluation_date'];
			$sup_address_EN 			= $row_get_sup['address_EN'];
			$sup_address_CN 			= $row_get_sup['address_CN'];
			$sup_country_ID 			= $row_get_sup['country_ID']; // look up below
			$sup_contact_person 		= $row_get_sup['contact_person'];
			$sup_mobile_phone 			= $row_get_sup['mobile_phone'];
			$sup_telephone 				= $row_get_sup['telephone'];
			$sup_fax 					= $row_get_sup['fax'];
			$sup_email_1 				= $row_get_sup['email_1'];
			$sup_email_2 				= $row_get_sup['email_2'];
			$sup_web 					= $row_get_sup['website'];
			
					// get country
					$get_sup_country_SQL = "SELECT * FROM `countries` WHERE `ID` ='" . $sup_country_ID . "'";
					// echo $get_sup_country_SQL;

					$result_get_sup_country = mysqli_query($con,$get_sup_country_SQL);
					// while loop
					while($row_get_sup_country = mysqli_fetch_array($result_get_sup_country)) {
						$sup_country_ID 			= $row_get_sup_country['ID'];
						$sup_country_name_EN 		= $row_get_sup_country['name_EN'];
						$sup_country_name_CN 		= $row_get_sup_country['name_CN'];
						$sup_country_code 			= $row_get_sup_country['code'];
						$sup_country_record_status 	= $row_get_sup_country['record_status'];
						$sup_country_alpha_2 		= $row_get_sup_country['alpha_2'];
						$sup_country_alpha_3 		= $row_get_sup_country['alpha_3'];
						$sup_country_ISO_code 		= $row_get_sup_country['ISO_code'];
					}
					

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

					if ($profile_link == 1) { 
						$start_url = '<a href="supplier_view.php?id=' . $sup_ID . '" title="Click to view this supplier profile">';
						$end_url = '</a>';
					}
					else {
						$start_url = '';
						$end_url = '';
					}
					
						if ($display_type == 1) {
							// let's make a list!
							
							$start_icon_list = '<ul class="fa-ul">';
							$start_icon_name_item = '<li><i class="fa-li fa fa-info-circle"></i>';
							$end_icon_name_item = '</li>';
							$end_icon_list = '</ul>';
						}
						else {
							$start_icon_list = '';
							$start_icon_name_item = '<strong>';
							$end_icon_name_item = '</strong><br />';
							$end_icon_list = '';
						}
					
						echo $start_icon_list;
							
							echo $start_icon_name_item;
	
								// may or may not be a link to the profile!
								echo $start_url;
	
									// now write the vendor name:
						
									echo $sup_en; 
						
									if (($sup_cn!='')&&($sup_cn!='中文名')){ 
										echo " / " . $sup_cn; 
									}
						
								// may or may not be empty! :)
								echo $end_url;
						
							echo $end_icon_name_item;
						
							if ($display_type == 1) {
								// now show the address!
							
								if ($sup_address_EN			!= '') { 	echo '<li title="Address / 地址"><i class="fa-li fa fa-map-marker"></i>' . $sup_address_EN . '</li>'; }
								if (($sup_address_CN != '')&&($sup_address_CN != '没有中文地址')) { 	echo '<br />' . $sup_address_CN . '</li>'; }
								if ($sup_country_name_EN 	!= '') {	echo '<li title="Country / 国家"><i class="fa-li fa fa-globe"></i>' . $sup_country_name_EN; if (($sup_country_name_CN != '')&&($sup_country_name_CN != '中文名')) { echo ' / ' . $sup_country_name_CN; } echo '</li>'; }
								if ($sup_contact_person 	!= '') { 	echo '<li title="Contact Person / 负责人"><i class="fa-li fa fa-user"></i>' . $sup_contact_person . '</li>'; }
								if ($sup_mobile_phone 		!= '') { 	echo '<li title="Cellphone / 手机"><i class="fa-li fa fa-mobile"></i>' . $sup_mobile_phone . '</li>'; }
								if ($sup_telephone 			!= '') {	echo '<li title="Telephone / 电话"><i class="fa-li fa fa-phone"></i>' . $sup_telephone . '</li>'; }
								if ($sup_fax 				!= '') { 	echo '<li title="Fax"><i class="fa-li fa fa-fax"></i>' . $sup_fax . '</li>'; }
								if ($sup_email_1 			!= '') { 	echo '<li title="E-mail"><i class="fa-li fa fa-envelope"></i><a href="mailto:' . $sup_email_1 . '" title="Click here to send an email">' . $sup_email_1 . '</a></li>'; }
								if ($sup_email_2 			!= '') { 	echo '<li title="E-mail"><i class="fa-li fa fa-envelope"></i><a href="mailto:' . $sup_email_2 . '" title="Click here to send an email">' . $sup_email_2 . '</a></li>'; }
								if ($sup_web 				!= '') { 	echo '<li title="Website / 网站"><i class="fa-li fa fa-external-link"></i><a href="' . $sup_web . '" target="_blank" title="Launch website in a new window">' . $sup_web . '</a></li>'; }
						
							
							} // end of Purchase Order display full address / contact detils
						
							else if ($display_type == 2) {
								// now show the address!
							
								if ($sup_address_EN			!= '') { 	echo $sup_address_EN . '<br />'; }
								if (($sup_address_CN != '')&&($sup_address_CN != '没有中文地址')) { 	echo $sup_address_CN . '<br />'; }
								if ($sup_country_name_EN 	!= '') {	echo $sup_country_name_EN; if (($sup_country_name_CN != '')&&($sup_country_name_CN != '中文名')) { echo ' / ' . $sup_country_name_CN; } echo '<br />'; }
								if ($sup_telephone 			!= '') {	echo 'T: ' . $sup_telephone; }
								if (($sup_telephone != '')&&($sup_fax != '')) { echo ' | '; }
								if ($sup_fax 				!= '') { 	echo 'F:' . $sup_fax; }
								if (($sup_telephone != '')||($sup_fax != '')) { echo '<br />'; }
						
							
							} // end of Purchase Order display full address / contact detils
						
						
						echo $end_icon_list; // now close the UL is it exists...

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


function creator_drop_down($this_user_ID, $form_element_name = 'created_by') {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// now output the result:

	?>
	<!-- originally parsed USER ID = <?php echo $this_user_ID; ?> -->
	<select class="form-control populate" name="<?php echo $form_element_name; ?>" id="<?php echo $form_element_name; ?>" data-plugin-selectTwo>
		<option value="0">Select User</option>
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


function supplier_drop_down($this_sup_ID, $form_element_name = 'sup_ID') {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// now output the result:

	?>
	<!-- originally parsed SUP ID = <?php echo $this_sup_ID; ?> -->
	<select data-plugin-selectTwo class="form-control populate" name="<?php echo $form_element_name; ?>" id="<?php echo $form_element_name; ?>" required data-plugin-selectTwo>
	  <?php if ($this_sup_ID == 0) { ?><option value="0" selected="selected" style="display:none">Select vendor:</option><?php } ?>
		<?php
		// get batch list
		$order_by = " ORDER BY `record_status` DESC";
		$get_sup_list_SQL = "SELECT * FROM `suppliers` WHERE `record_status` = '2'" . $order_by; // SHOWING APPROVED VENDORS ONLY!
		echo "<!-- DEBUG: " . $get_sup_list_SQL . " -->";
		$result_get_sup_list = mysqli_query($con,$get_sup_list_SQL);
		// while loop
		while($row_get_sup_list = mysqli_fetch_array($result_get_sup_list)) {

				// now print each record:
				$sup_id = $row_get_sup_list['ID'];
				$sup_epg_supplier_ID = $row_get_sup_list['epg_supplier_ID'];
				$sup_name_EN = $row_get_sup_list['name_EN'];
				$sup_name_CN = $row_get_sup_list['name_CN'];
				$sup_website = $row_get_sup_list['website'];
				$sup_record_status = $row_get_sup_list['record_status'];
				$sup_part_classification = $row_get_sup_list['part_classification'];
				$sup_items_supplied = $row_get_sup_list['items_supplied'];
				$sup_part_type_ID = $row_get_sup_list['part_type_ID'];
				$sup_certifications = $row_get_sup_list['certifications'];
				$sup_certification_expiry_date = $row_get_sup_list['certification_expiry_date'];
				$sup_evaluation_date = $row_get_sup_list['evaluation_date'];
				$sup_address_EN = $row_get_sup_list['address_EN'];
				$sup_address_CN = $row_get_sup_list['address_CN'];
				$sup_country_ID = $row_get_sup_list['country_ID'];
				$sup_contact_person = $row_get_sup_list['contact_person'];
				$sup_mobile_phone = $row_get_sup_list['mobile_phone'];
				$sup_telephone = $row_get_sup_list['telephone'];
				$sup_fax = $row_get_sup_list['fax'];
				$sup_email_1 = $row_get_sup_list['email_1'];
				$sup_email_2 = $row_get_sup_list['email_2'];
				
				// VENDOR CLASSIFICATION BY STATUS:

				$get_sup_status_SQL = "SELECT * FROM `supplier_status` WHERE `status_level` ='" . $sup_record_status . "'";
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

				?>
				<option value="<?php echo $sup_id; ?>" <?php if ($sup_id == $this_sup_ID) { ?> selected="selected"<?php } ?>>
					<?php echo $sup_name_EN; if (($sup_name_CN!='')&&($sup_name_CN!='中文名')) { echo " / " . $sup_name_CN; } ?> (Status: <?php echo $sup_status_name_EN; if (($sup_status_name_CN!='')&&($sup_status_name_CN!='中文名')) { echo ' / ' . $sup_status_name_CN; } ?>)
				</option>
				<?php
			}
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
<select class="form-control populate" name="record_status" id="record_status" data-plugin-selectTwo>
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

function get_part_name($part_id, $profile_link) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	if ($part_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO PART FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {

		// now get the part info:
		$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $part_id;
		// echo $get_parts_SQL;

		$result_get_part = mysqli_query($con,$get_part_SQL);

		// while loop
		while($row_get_part = mysqli_fetch_array($result_get_part)) {
			$part_ID 					= $row_get_part['ID'];
			$part_code 					= $row_get_part['part_code'];
			$name_EN 					= $row_get_part['name_EN'];
			$name_CN 					= $row_get_part['name_CN'];
			$description 				= $row_get_part['description'];
			$type_ID 					= $row_get_part['type_ID'];
			$classification_ID 			= $row_get_part['classification_ID'];
			$part_default_suppler_ID 	= $row_get_part['default_suppler_ID'];
			$part_record_status 		= $row_get_part['record_status'];
			$part_product_type_ID 		= $row_get_part['product_type_ID'];
			$part_created_by 			= $row_get_part['created_by'];
			$part_is_finished_product 	= $row_get_part['is_finished_product'];

			// now print the result (no link here)

			$close_link = '';

			if ($profile_link == 1) {
				?>
				<a href="part_view.php?id=<?php echo $part_id; ?>" title="Click here to view this part profile">
				<?php
				$close_link = "</a>";
			}

			echo $part_code . ' - ';
			echo $name_EN;
			if (($name_CN!='')&&($name_CN!='中文名')) { echo ' / ' . $name_CN; }
			echo $close_link; // maybe blank - see above


		} // end get part info WHILE loop
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

function get_location($loc_id, $display_type = 0, $profile_link = 1) { // THIS IS VERY SIMILAR TO GET SUPPLIER FUNCTION...

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	// echo 'get_location function called OK';

	if ($loc_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO LOCATION FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {

		// now get the location info:
		$get_loc_SQL = "SELECT * FROM `locations` WHERE `ID` = " . $loc_id;
		// echo $get_loc_SQL;

		$result_get_loc = mysqli_query($con,$get_loc_SQL);

		// while loop
		while($row_get_loc = mysqli_fetch_array($result_get_loc)) {
			$loc_ID 				= $row_get_loc['ID'];
			$loc_name_EN 			= $row_get_loc['name_EN'];
			$loc_name_CN 			= $row_get_loc['name_CN'];
			$loc_company_name_EN 	= $row_get_loc['company_name_EN'];
			$loc_company_name_CN 	= $row_get_loc['company_name_CN'];
			$loc_address_CN 		= $row_get_loc['address_CN'];
			$loc_address_line_1 	= $row_get_loc['address_line_1'];
			$loc_address_line_2 	= $row_get_loc['address_line_2'];
			$loc_address_line_3 	= $row_get_loc['address_line_3'];
			$loc_city 				= $row_get_loc['city'];
			$loc_state 				= $row_get_loc['state'];
			$loc_zipcode 			= $row_get_loc['zipcode'];
			$loc_country_ID 		= $row_get_loc['country_ID']; // look this up!
			$loc_telephone 			= $row_get_loc['telephone'];
			$loc_fax				= $row_get_loc['fax'];
			$loc_email 				= $row_get_loc['email'];
			$loc_web 				= $row_get_loc['web'];
			$loc_contact_user_ID 	= $row_get_loc['contact_user_ID']; // run function...
			$loc_record_status 		= $row_get_loc['record_status'];
			
					// get country
					$get_loc_country_SQL = "SELECT * FROM `countries` WHERE `ID` ='" . $loc_country_ID . "'";
					// echo $get_loc_country_SQL;

					$result_get_loc_country = mysqli_query($con,$get_loc_country_SQL);
					// while loop
					while($row_get_loc_country = mysqli_fetch_array($result_get_loc_country)) {
						$loc_country_ID 			= $row_get_loc_country['ID'];
						$loc_country_name_EN 		= $row_get_loc_country['name_EN'];
						$loc_country_name_CN 		= $row_get_loc_country['name_CN'];
						$loc_country_code 			= $row_get_loc_country['code'];
						$loc_country_record_status 	= $row_get_loc_country['record_status'];
						$loc_country_alpha_2 		= $row_get_loc_country['alpha_2'];
						$loc_country_alpha_3 		= $row_get_loc_country['alpha_3'];
						$loc_country_ISO_code 		= $row_get_loc_country['ISO_code'];
					}
			
			// now print the result (no link here)
			
			
			// NOTE: default display_type (0) is a list, using FONTAWESOME icons to decorate it.
			
			if ($profile_link == 1) { // By default, we will link the name to the profile page
				$start_url = '<a href="location_view.php?id=' . $loc_id . '" title="Click here to view this location profile">';
				$end_url = "</a>";
			}
			else {
				$start_url = '';
				$end_url = '';
			}
			
						if ($display_type == 1) {
							// let's make a list!
							
							$start_icon_list = '<ul class="fa-ul">';
							$start_icon_name_item = '<li><i class="fa-li fa fa-info-circle"></i>';
							$end_icon_name_item = '</li>';
							$end_icon_list = '</ul>';
						}
						else {
							$start_icon_list = '';
							$start_icon_name_item = '<strong>';
							$end_icon_name_item = '</strong><br />';
							$end_icon_list = '';
						}
					
						echo $start_icon_list;
							
							echo $start_icon_name_item;
	
								// may or may not be a link to the profile!
								echo $start_url;
	
									// now write the vendor name:
						
									echo $loc_company_name_EN;
									if (($loc_company_name_CN!='')&&($loc_company_name_CN!='中文名')) { echo ' / ' . $loc_company_name_CN; }
						
								// may or may not be empty! :)
								echo $end_url;
						
							echo $end_icon_name_item;
							
							
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
						
							if ($display_type == 1) {
								// now show the address!
								
								echo '<li title="Address"><i class="fa-li fa fa-map-marker"></i>';
								
								if ($loc_address_line_1!='') {
									// for now, we will only specify that if the ADDRESS LINE 1 is blank, it will return an error...
									echo $loc_address_line_1;
									
									if ($loc_address_line_2 != '') {
										echo ', ' . $loc_address_line_2;
									}
									
									if ($loc_address_line_3 != '') {
										echo ', ' . $loc_address_line_3;
									}
									
									if ($loc_city != '') {
										echo '<br />' . $loc_city;
									}
									
									if ($loc_state != '') {
										echo ', ' . $loc_state;
									}
									
									if ($loc_zipcode != '') {
										echo ', ' . $loc_zipcode;
									}
									
								}
								else { 
									?>
									<span class="btn btn-danger">
										<i class="fa fa-exclamation-triangle"></i>
										NO ADDRESS PROVIDED!
										<i class="fa fa-exclamation-triangle"></i>
									</span>
									<?php
								}
								
								echo '</li>';
								
								
								if (($loc_address_CN != '')&&($loc_address_CN != '没有中文地址')) { 	echo '<li title="地址"><i class="fa-li fa fa-map-marker"></i>' . $loc_address_CN . '</li>'; }
								if ($loc_country_name_EN 	!= '') {	echo '<li title="Country / 国家"><i class="fa-li fa fa-globe"></i>' . $loc_country_name_EN; if (($loc_country_name_CN != '')&&($loc_country_name_CN != '中文名')) { echo ' / ' . $loc_country_name_CN; } echo '</li>'; }
								if ($loc_contact_user_ID 	!= '') { 	echo '<li title="Contact Person / 负责人"><i class="fa-li fa fa-user"></i>'; get_creator($loc_contact_user_ID); echo '</li>'; }
								if ($loc_telephone 			!= '') {	echo '<li title="Telephone / 电话"><i class="fa-li fa fa-phone"></i>' . $loc_telephone . '</li>'; }
								if ($loc_fax 				!= '') { 	echo '<li title="Fax"><i class="fa-li fa fa-fax"></i>' . $loc_fax . '</li>'; }
								if ($loc_email	 			!= '') { 	echo '<li title="E-mail"><i class="fa-li fa fa-envelope"></i><a href="mailto:' . $loc_email . '" title="Click here to send an email">' . $loc_email . '</a></li>'; }
								if ($loc_web 				!= '') { 	echo '<li title="Website / 网站"><i class="fa-li fa fa-external-link"></i><a href="' . $loc_web . '" target="_blank" title="Launch website in a new window">' . $loc_web . '</a></li>'; }
						
							
							} // end of Purchase Order display full address / contact detils for an EPG location
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							else if ($display_type == 2) {
								// now show the address!
								
								if ($loc_address_line_1!='') {
									// for now, we will only specify that if the ADDRESS LINE 1 is blank, it will return an error...
									echo $loc_address_line_1;
									
									if ($loc_address_line_2 != '') {
										echo ', ' . $loc_address_line_2;
									}
									
									if ($loc_address_line_3 != '') {
										echo ', ' . $loc_address_line_3;
									}
									
									if ($loc_city != '') {
										echo '<br />' . $loc_city;
									}
									
									if ($loc_state != '') {
										echo ', ' . $loc_state;
									}
									
									if ($loc_zipcode != '') {
										echo ', ' . $loc_zipcode;
									}
									
								}
								else { 
									?>
									<strong>NO ADDRESS PROVIDED!</strong>
									<?php
								}
								
								
								if (($loc_address_CN != '')&&($loc_address_CN != '没有中文地址')) { 	echo $loc_address_CN . '<br />'; }
								if ($loc_country_name_EN 	!= '') {	echo $loc_country_name_EN; if (($loc_country_name_CN != '')&&($loc_country_name_CN != '中文名')) { echo ' / ' . $loc_country_name_CN; } echo '<br />'; }
								if ($loc_telephone 			!= '') {	echo 'T: ' . $loc_telephone; }
								if (($loc_telephone != '')&&($loc_fax != '')) { echo ' | '; }
								if ($loc_fax 				!= '') { 	echo 'F:' . $loc_fax; }
								if (($loc_telephone != '')||($loc_fax != '')) { echo '<br />'; }
						
							
							} // end of Purchase Order display full address / contact detils for an EPG location
							
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
							/////////////////////////////////////////////////
						
						
						echo $end_icon_list; // now close the UL if it exists...
						
						
						
						
						
			
			
			

		} // end get location info WHILE loop
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


function location_drop_down($this_loc_ID, $form_element_name = 'loc_ID') {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	// now output the result:

	?>
	<!-- originally parsed SUP ID = <?php echo $this_sup_ID; ?> -->
	<select data-plugin-selectTwo class="form-control populate" name="<?php echo $form_element_name; ?>" id="<?php echo $form_element_name; ?>">
		<?php
		// now get the location info:
		$get_loc_SQL = "SELECT * FROM `locations` WHERE `record_status` = 2";
		// echo $get_loc_SQL;

		$result_get_loc = mysqli_query($con,$get_loc_SQL);

		// while loop
		while($row_get_loc = mysqli_fetch_array($result_get_loc)) {
			$loc_ID 				= $row_get_loc['ID'];
			$loc_name_EN 			= $row_get_loc['name_EN'];
			$loc_name_CN 			= $row_get_loc['name_CN'];
			$loc_company_name_EN 	= $row_get_loc['company_name_EN'];
			$loc_company_name_CN 	= $row_get_loc['company_name_CN'];
			$loc_address_CN 		= $row_get_loc['address_CN'];
			$loc_address_line_1 	= $row_get_loc['address_line_1'];
			$loc_address_line_2 	= $row_get_loc['address_line_2'];
			$loc_address_line_3 	= $row_get_loc['address_line_3'];
			$loc_city 				= $row_get_loc['city'];
			$loc_state 				= $row_get_loc['state'];
			$loc_zipcode 			= $row_get_loc['zipcode'];
			$loc_country_ID 		= $row_get_loc['country_ID']; // look this up!
			$loc_telephone 			= $row_get_loc['telephone'];
			$loc_fax				= $row_get_loc['fax'];
			$loc_email 				= $row_get_loc['email'];
			$loc_web 				= $row_get_loc['web'];
			$loc_contact_user_ID 	= $row_get_loc['contact_user_ID']; // run function...
			$loc_record_status 		= $row_get_loc['record_status'];
			
					// get country
					$get_loc_country_SQL = "SELECT * FROM `countries` WHERE `ID` ='" . $loc_country_ID . "'";
					// echo $get_loc_country_SQL;

					$result_get_loc_country = mysqli_query($con,$get_loc_country_SQL);
					// while loop
					while($row_get_loc_country = mysqli_fetch_array($result_get_loc_country)) {
						$loc_country_ID 			= $row_get_loc_country['ID'];
						$loc_country_name_EN 		= $row_get_loc_country['name_EN'];
						$loc_country_name_CN 		= $row_get_loc_country['name_CN'];
						$loc_country_code 			= $row_get_loc_country['code'];
						$loc_country_record_status 	= $row_get_loc_country['record_status'];
						$loc_country_alpha_2 		= $row_get_loc_country['alpha_2'];
						$loc_country_alpha_3 		= $row_get_loc_country['alpha_3'];
						$loc_country_ISO_code 		= $row_get_loc_country['ISO_code'];
					}
			
			// now print the result

				?>
				<option value="<?php echo $loc_ID; ?>" <?php if ($loc_ID == $this_loc_ID) { ?> selected="selected"<?php } ?>>
					<?php echo $loc_name_EN; if (($loc_name_CN!='')&&($loc_name_CN!='中文名')) { echo " / " . $loc_name_CN; } ?>
					(<?php echo $loc_country_name_EN; if (($loc_country_name_CN!='')&&($loc_country_name_CN!='中文名')) { echo ' / ' . $loc_country_name_CN; } ?>)
				</option>
				<?php
			}
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

function form_buttons($cancel_url, $record_id, $add_VARS = '') { 
	if (($record_id!=0)&&($record_id!='')) { ?>
		<input type="hidden" value="<?php echo $record_id; ?>" name="id" />
		<a class="btn btn-danger" href="<?php echo $cancel_url; ?>.php?id=<?php echo $record_id;?>&<?php echo $add_VARS; ?>"><i class="fa fa-arrow-left"></i> CANCEL / BACK</a>
	<?php }
	else {
	?>
		<a class="btn btn-danger" href="<?php echo $cancel_url; ?>.php?<?php echo $add_VARS; ?>"><i class="fa fa-arrow-left"></i> CANCEL / BACK</a>
	<?php
	}?>
	<button type="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> RESET</button>
	<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SAVE CHANGES</button>
<?php }
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
function add_button($record_id, $add_page_url, $record_var = 'id', $add_title='Click here to add a new record to this table', $add_url = '') {
?>

<div class="row"><!-- start add button row (function) -->
	<!-- start add button div -->
	<div class="col-md-1">
		<a href="<?php 
		
			echo $add_page_url; 
			
		?>.php?<?php 
		
			echo $record_var; 
			
		?>=<?php 
		
			echo $record_id; 
			echo $add_url; 
			
		?>" class="mb-xs mt-xs mr-xs btn btn-success pull-left" title="<?php 
		
			echo $add_title; 
			
		?>">
		  <i class="fa fa-plus-square"></i>
		</a>
	</div>
	<!-- end add button div -->
	<!-- empty container for remaining space -->
	<div id="feature_buttons_container_id" class="col-md-11">
	</div>
	<!-- end empty div -->
	
 </div><!-- end add button row (function) -->
 
 <?php 
 } // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function part_num_button($part_id) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	if ($part_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO PART FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {

		// now get the part info:
		$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $part_id;
		// echo $get_parts_SQL;

		$result_get_part = mysqli_query($con,$get_part_SQL);

		// while loop
		while($row_get_part = mysqli_fetch_array($result_get_part)) {
			$part_ID 					= $row_get_part['ID'];
			$part_code 					= $row_get_part['part_code'];
			$part_name_EN 				= $row_get_part['name_EN'];
			$part_name_CN 				= $row_get_part['name_CN'];
			$description 				= $row_get_part['description'];
			$type_ID 					= $row_get_part['type_ID'];
			$classification_ID 			= $row_get_part['classification_ID'];
			$part_default_suppler_ID 	= $row_get_part['default_suppler_ID'];
			$part_record_status 		= $row_get_part['record_status'];
			$part_product_type_ID 		= $row_get_part['product_type_ID'];
			$part_created_by 			= $row_get_part['created_by'];
			$part_is_finished_product 	= $row_get_part['is_finished_product'];

			// now print the result (no link here)
			
			?>
			<a href="part_view.php?id=<?php echo $part_ID; ?>" class="btn btn-info btn-xs" title="View <?php
					    	echo $part_name_EN;
					    	if (($part_name_CN!='')&&($part_name_CN!='中文名')) {
					    		echo " / " . $part_name_CN;
					    	}
					    ?> Part Profile"><?php
					    // now do a quick check to make sure that the batch number (first 5 chars) matches the part code:
					    if (substr($batch_number,0,5)!= $part_code) {
					    	echo '<span class="text-danger" title="Batch Number Does Not Match Part Code!">' . $part_code . '</span>';
					    }
					    else {
					    	echo $part_code;
					    }

					    ?></a>
				<?php


		} // end get part info WHILE loop
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
function part_drop_down($current_ID=0) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
?>
<!-- starting part_drop_down function: -->
<select data-plugin-selectTwo class="form-control populate" name="part_ID" required>
	<option value="">Select:</option>
	<?php
	// get parts list
	$get_parts_SQL = "SELECT * FROM `parts` WHERE `record_status` = '2' ORDER BY `part_code` ASC";
	// echo $get_parts_SQL;

	$part_count = 0;

	$result_get_parts = mysqli_query($con,$get_parts_SQL);
	// while loop
	while($row_get_parts = mysqli_fetch_array($result_get_parts)) {
	
		$part_ID 					= $row_get_parts['ID'];
		$part_code 					= $row_get_parts['part_code'];
		$part_name_EN 				= $row_get_parts['name_EN'];
		$part_name_CN 				= $row_get_parts['name_CN'];
		$part_description 			= $row_get_parts['description'];
		$part_type_ID 				= $row_get_parts['type_ID'];
		$part_classification_ID 	= $row_get_parts['classification_ID'];
		$part_default_suppler_ID 	= $row_get_parts['default_suppler_ID'];
		$part_record_status 		= $row_get_parts['record_status'];
		$part_product_type_ID 		= $row_get_parts['product_type_ID'];
		$part_created_by 			= $row_get_parts['created_by'];
		$part_is_finished_product 	= $row_get_parts['is_finished_product'];

		// GET PART TYPE:

		$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` ='" . $part_type_ID . "'";
		// echo $get_part_type_SQL;

		$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
		// while loop
		while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
			$part_type_EN = $row_get_part_type['name_EN'];
			$part_type_CN = $row_get_part_type['name_CN'];
		}

		// GET PART CLASSIFICATION:

		$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $part_classification_ID . "'";
		// echo $get_part_class_SQL;

		$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
		// while loop
		while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
			$part_class_EN = $row_get_part_class['name_EN'];
			$part_class_CN = $row_get_part_class['name_CN'];
		}
		?>
		
		<option value="<?php echo $part_ID; ?>" <?php if ($part_ID == $current_ID) { ?> selected="selected"<?php } ?>>
			<?php echo $part_code; ?> - <?php echo $part_name_EN; 
			
			if (($part_name_CN!='')&&($part_name_CN!='中文名')){
				echo ' / ' . $part_name_CN;
			}
			
			?>
		</option>
		
	<?php
	} // END WHILE LOOP

	?>
</select>
	
<!-- end part_drop_down function: -->
<?php
} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
function part_rev_drop_down($current_ID=0, $part_type_ID=0, $option_name = 'part_rev_ID') {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	if ($part_type_ID != 0){
		$add_part_type_SQL = " AND `type_ID` = '" . $part_type_ID . "'";
	}
	else {
		$add_part_type_SQL = '';
	}
	
	if (($option_name == 'parent_ID')||($option_name == 'part_rev_ID_reference')) {
		$required = '';
	}
	else {
		$required="required";
	}
	
?>
<!-- starting part_rev_drop_down function: -->
<select data-plugin-selectTwo class="form-control populate" name="<?php echo $option_name; ?>" <?php echo $required; ?>>
	<option value=""></option>
	<?php
	
	// get parts list
	$get_parts_SQL = "SELECT * FROM `parts` WHERE `record_status` = '2'" . $add_part_type_SQL . " ORDER BY `part_code` ASC";
	// echo $get_parts_SQL;

	$part_count = 0;

	$result_get_parts = mysqli_query($con,$get_parts_SQL);
	// while loop
	while($row_get_parts = mysqli_fetch_array($result_get_parts)) {

		// GET PART TYPE:

		$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` ='" . $row_get_parts['type_ID'] . "'";
		// echo $get_part_type_SQL;

		$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
		// while loop
		while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
			$part_type_EN = $row_get_part_type['name_EN'];
			$part_type_CN = $row_get_part_type['name_CN'];
		}

		// GET PART CLASSIFICATION:

		$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $row_get_parts['classification_ID'] . "'";
		// echo $get_part_class_SQL;

		$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
		// while loop
		while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
			$part_class_EN = $row_get_part_class['name_EN'];
			$part_class_CN = $row_get_part_class['name_CN'];
		}
		?>

		<optgroup label="<?php echo $row_get_parts['part_code']; ?> - <?php 
			echo $row_get_parts['name_EN'];
			if (($row_get_parts['name_CN']!='')&&($row_get_parts['name_CN']!='中文名')) {
		 		echo  " / " . $row_get_parts['name_CN']; 
		 	} 
		 	?>">

		<?php

		// now list the revisions for this part:

		$get_part_rev_SQL = "SELECT * FROM `part_revisions` WHERE `part_ID` =" . $row_get_parts['ID'] . " AND `record_status`='2' ORDER BY `revision_number` DESC";
		$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);

		// while loop
		while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {

			// now print each record:
			$rev_id = $row_get_part_rev['ID'];
			$rev_part_id = $row_get_part_rev['part_ID'];
			$rev_number = $row_get_part_rev['revision_number'];
			$rev_remarks = $row_get_part_rev['remarks'];
			$rev_date = $row_get_part_rev['date_approved'];
			$rev_user = $row_get_part_rev['user_ID'];

			?>
				<option value="<?php echo $rev_id; ?>" <?php if ($rev_id == $current_ID) { ?> selected="selected"<?php } ?>><?php echo $row_get_parts['part_code']; ?> - <?php echo $rev_number; ?></option>
			<?php

		} // end revision look-up loop
		?>

		</optgroup>

	<?php
	} // END WHILE LOOP

	?>
</select>
	
<!-- end part_rev_drop_down function: -->
<?php
} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function purchase_orders_drop_down($part_batch_po_id=0) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
?>

<select data-plugin-selectTwo class="form-control populate" name="PO_ID" required>
			<?php
			$get_PO_SQL = "SELECT * FROM `purchase_orders` WHERE `record_status` = 2 ORDER BY `PO_number` ASC ";
			$result_get_PO = mysqli_query($con,$get_PO_SQL);
			// while loop
			while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

					// now print each record:
					$PO_id = $row_get_PO['ID'];
					$PO_number = $row_get_PO['PO_number'];
					$PO_created_date = $row_get_PO['created_date'];
					$PO_description = $row_get_PO['description'];

					// count batches for this purchase order
					$count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE `PO_ID` = " . $PO_id . " AND `record_status` = 2";
					$count_batches_query = mysqli_query($con, $count_batches_sql);
					$count_batches_row = mysqli_fetch_row($count_batches_query);
					$total_batches = $count_batches_row[0];

					// count line items for this purchase order
					$count_po_line_items_sql = "SELECT COUNT( ID ) FROM  `purchase_order_items` WHERE `purchase_order_ID` = " . $PO_id . " AND `record_status` = 2";
					$count_po_line_items_query = mysqli_query($con, $count_po_line_items_sql);
					$count_po_line_items_row = mysqli_fetch_row($count_po_line_items_query);
					$total_po_line_items = $count_po_line_items_row[0];

			?>
	<option value="<?php echo $PO_id; ?>"<?php if ($PO_id == $part_batch_po_id) { ?> selected="selected"<?php } ?>><?php echo $PO_number; 
	if ($total_batches!=0) { // batches found!
	
		if ($total_batches>1) { $add_plural = 'es'; }
		else { $add_plural = ''; }
		
		?>  - [<?php echo $total_batches; ?> Batch<?php echo $add_plural; ?>]<?php 
		
	}
	if ($total_po_line_items!=0) {
	
		if ($total_po_line_items>1) { $add_plural = 's'; }
		else { $add_plural = ''; }
		
		?>  - [<?php echo $total_po_line_items; ?> P.O. Item<?php echo $add_plural; ?>]<?php 
		
	} 
	?></option>

			<?php
			} // END WHILE LOOP
			?>
</select>
<?php 

} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
function part_num($part_id, $show_button = 1){ // default is to show a light blue button. Print view requires '0' to remove button styling

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	if ($part_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO PART FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {
	
		// now get the part info
		$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = '" . $part_id . "'";
		// debug:
		// echo '<h3>' . $get_part_SQL . '</h3>'; 
		$result_get_part = mysqli_query($con,$get_part_SQL);
		// while loop
		while($row_get_part = mysqli_fetch_array($result_get_part)) {

			// now print each result to a variable:
			$part_id 		= $row_get_part['ID'];
			$part_code 		= $row_get_part['part_code'];
			$part_name_EN 	= $row_get_part['name_EN'];
			$part_name_CN 	= $row_get_part['name_CN'];

		}
	
			$close_link = '';
	
			if ($show_button == 1) { 
	
				$close_link = '</a>';
	
				?><a href="part_view.php?id=<?php echo $part_id; ?>" class="btn btn-info btn-xs" title="View <?php echo $part_name_EN; 
				if (($part_name_CN!='')&&($part_name_CN!='中文名')) { echo ' / ' . $part_name_CN; } ?> Part Profile"><?php 
			} 
	
			echo $part_code; 
			echo $close_link;
	}

} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function part_rev($part_rev_id, $show_button = 1) {

	// this function returns the part revision orange button with the part_rev_ID as a mouse-over title to help with development

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	if ($part_rev_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO REVISION FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {
			$get_part_rev_SQL = "SELECT * FROM `part_revisions` WHERE `ID` = '" . $part_rev_id . "'";
			// debug:
			// echo $get_part_rev_SQL;
			$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);

				// while loop
				while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {
		
					$part_rev_ID 				= $row_get_part_rev['ID'];
					$part_rev_part_ID 			= $row_get_part_rev['part_ID'];
					$part_rev_revision_number 	= $row_get_part_rev['revision_number'];
					$part_rev_remarks 			= $row_get_part_rev['remarks'];
					$part_rev_date_approved 	= $row_get_part_rev['date_approved'];
					$part_rev_user_ID 			= $row_get_part_rev['user_ID'];
					$part_rev_price_USD 		= $row_get_part_rev['price_USD'];
					$part_rev_weight_g 			= $row_get_part_rev['weight_g'];
					$part_rev_status_ID 		= $row_get_part_rev['status_ID'];
					$part_rev_material_ID 		= $row_get_part_rev['material_ID'];
					$part_rev_treatment_ID 		= $row_get_part_rev['treatment_ID'];
					$part_rev_treatment_notes 	= $row_get_part_rev['treatment_notes'];
					$part_rev_record_status 	= $row_get_part_rev['record_status'];
			
						// now print the results:
			
						if ($part_rev_record_status != 2) {
							// this revision is either pending review or turned off!
							$button_style = 'danger'; // highlight red!
						}
						else {
							$button_style = 'warning'; // default orange
						}
			
			
			
			
	
				if ($show_button == 1) { 
	
					$close_link = '</a>';
	
					?><a href="part_view.php?id=<?php echo $part_rev_part_ID; ?>&rev_id=<?php echo $part_rev_ID; ?>" class="btn btn-xs btn-<?php echo $button_style; ?>" title="Rev. #: <?php echo $part_rev_ID; ?> (Part #: <?php echo $part_rev_part_ID; ?>)"><?php 
				} 
				else { 
					?>Rev. <?php
					$close_link = '';
				}
	
				echo $part_rev_revision_number; 
				echo $close_link;

				} // end of while results loop
				
		} // end else 0 results flag

 } // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function part_name($part_id, $show_button = 1){ // default is to show a default button. Print view requires '0' to remove button styling and link

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	if ($part_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO PART FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {
	
		// now get the part info
		$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = '" . $part_id . "'";
		// debug:
		// echo '<h3>' . $get_part_SQL . '</h3>'; 
		$result_get_part = mysqli_query($con,$get_part_SQL);
		// while loop
		while($row_get_part = mysqli_fetch_array($result_get_part)) {

			// now print each result to a variable:
			$part_id 		= $row_get_part['ID'];
			$part_code 		= $row_get_part['part_code'];
			$part_name_EN 	= $row_get_part['name_EN'];
			$part_name_CN 	= $row_get_part['name_CN'];

		}
	
			$close_link = '';
	
			if ($show_button == 1) { 
	
				$close_link = '</a>';
	
				?><a href="part_view.php?id=<?php echo $part_id; ?>" class="btn btn-default btn-xs" title="View <?php echo $part_name_EN; 
				if (($part_name_CN!='')&&($part_name_CN!='中文名')) { echo ' / ' . $part_name_CN; } ?> Part Profile"><?php 
			} 
	
			echo $part_name_EN; 
			if (($part_name_CN!='')&&($part_name_CN!='中文名')) { echo ' / ' . $part_name_CN; } 
			echo $close_link;
	}

} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function part_name_from_rev($part_rev_id, $show_button = 1) {

	// this function returns the part revision orange button with the part_rev_ID as a mouse-over title to help with development

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	if ($part_rev_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO REVISION FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {
			$get_part_rev_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $part_rev_id . "'";
			// debug:
			// echo $get_part_rev_SQL;
			$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);

				// while loop
				while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {
					$part_rev_part_ID 			= $row_get_part_rev['part_ID'];
					// now call another function?
					part_name($part_rev_part_ID, $show_button);
				} // end of while results loop
				
		} // end else 0 results flag

 } // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function part_num_from_rev($part_rev_id, $show_button = 1) {

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';

	if ($part_rev_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO REVISION FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {
			$get_part_rev_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $part_rev_id . "'";
			// debug:
			// echo $get_part_rev_SQL;
			$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);

				// while loop
				while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {
					$part_rev_part_ID 			= $row_get_part_rev['part_ID'];
					// now call another function:
					part_num($part_rev_part_ID, $show_button);
				} // end of while results loop
				
		} // end else 0 results flag

 } // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function part_img($part_rev_id, $profile_link = 1, $img_width_px = 100){ // default is to show a default button. Print view requires '0' to remove button styling and link

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	if ($part_rev_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO PART FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {
	
	
		// now get the part revision photo!
		$num_component_photos_found = 0;
		$component_photo_location = "assets/images/no_image_found.jpg";

		$get_part_component_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $part_rev_id . " AND `filetype_ID` = '5'";
		// echo "<h1>".$get_part_component_photo_SQL."</h1>";
		$result_get_part_component_photo = mysqli_query($con,$get_part_component_photo_SQL);
		// while loop
		while($row_get_part_component_photo = mysqli_fetch_array($result_get_part_component_photo)) {

			$num_component_photos_found = $num_component_photos_found + 1;

			// now print each record:
			$component_photo_id 				= $row_get_part_component_photo['ID'];
			$component_photo_name_EN 			= $row_get_part_component_photo['name_EN'];
			$component_photo_name_CN 			= $row_get_part_component_photo['name_CN'];
			$component_photo_filename 			= $row_get_part_component_photo['filename'];
			$component_photo_filetype_ID 		= $row_get_part_component_photo['filetype_ID'];
			$component_photo_location 			= $row_get_part_component_photo['file_location'];
			$component_photo_lookup_table 		= $row_get_part_component_photo['lookup_table'];
			$component_photo_lookup_id 			= $row_get_part_component_photo['lookup_ID'];
			$component_photo_document_category 	= $row_get_part_component_photo['document_category'];
			$component_photo_record_status 		= $row_get_part_component_photo['record_status'];
			$component_photo_created_by 		= $row_get_part_component_photo['created_by'];
			$component_photo_date_created 		= $row_get_part_component_photo['date_created'];
			$component_photo_filesize_bytes 	= $row_get_part_component_photo['filesize_bytes'];
			$component_photo_document_icon 		= $row_get_part_component_photo['document_icon'];
			$component_photo_document_remarks 	= $row_get_part_component_photo['document_remarks'];
			$component_photo_doc_revision 		= $row_get_part_component_photo['doc_revision'];

			if ($component_photo_filename!='') {
				// now apply filename
				$component_photo_location = "" . $component_photo_location . "/" . $component_photo_filename;
			}
			else {
				$component_photo_location = "assets/images/no_image_found.jpg";
			}

		} // end get part rev photo
	
		$get_part_rev_SQL = "SELECT * FROM `part_revisions` WHERE `ID` = '" . $part_rev_id . "'";
		// debug:
		// echo $get_part_rev_SQL;
		$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);

			// while loop
			while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {
	
				$part_rev_ID 				= $row_get_part_rev['ID'];
				$part_rev_part_ID 			= $row_get_part_rev['part_ID'];
				$part_rev_revision_number 	= $row_get_part_rev['revision_number'];
				$part_rev_remarks 			= $row_get_part_rev['remarks'];
				$part_rev_date_approved 	= $row_get_part_rev['date_approved'];
				$part_rev_user_ID 			= $row_get_part_rev['user_ID'];
				$part_rev_price_USD 		= $row_get_part_rev['price_USD'];
				$part_rev_weight_g 			= $row_get_part_rev['weight_g'];
				$part_rev_status_ID 		= $row_get_part_rev['status_ID'];
				$part_rev_material_ID 		= $row_get_part_rev['material_ID'];
				$part_rev_treatment_ID 		= $row_get_part_rev['treatment_ID'];
				$part_rev_treatment_notes 	= $row_get_part_rev['treatment_notes'];
				$part_rev_record_status 	= $row_get_part_rev['record_status'];
		}
	
		// now get the part info
		$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = '" . $part_rev_part_ID . "'";
		// debug:
		// echo '<h3>' . $get_part_SQL . '</h3>'; 
		$result_get_part = mysqli_query($con,$get_part_SQL);
		// while loop
		while($row_get_part = mysqli_fetch_array($result_get_part)) {

			// now print each result to a variable:
			$part_id 		= $row_get_part['ID'];
			$part_code 		= $row_get_part['part_code'];
			$part_name_EN 	= $row_get_part['name_EN'];
			$part_name_CN 	= $row_get_part['name_CN'];

		}
	
			$close_link = '';
	
			if ($profile_link == 1) { 
	
				$close_link = '</a>';
				
				if ($photo_location == "assets/images/no_image_found.jpg"){
					?><a href="upload_file.php?table=part_revisions&lookup_ID=<?php echo $part_rev_id; ?>" title="Upload a new image"><?php 
				}
				else {
					?><a href="part_view.php?id=<?php 
						echo $part_id; 
					?>" title="View <?php 
						echo $part_name_EN; 
						if (($part_name_CN!='')&&($part_name_CN!='中文名')) { 
							echo ' / ' . $part_name_CN; 
						} 
					?> Part Profile"><?php
				}
				 
			} 
			
			?>
				<img src="<?php 
				  echo $component_photo_location; 
				?>" class="rounded img-responsive" alt="<?php 
				  echo $part_code; 
				?> - <?php 
				  echo $part_name_EN; 
				  if (($part_name_CN!='')&&($part_name_CN!='中文名')) { 
				    echo ' / ' . $part_name_CN; 
				  } 
				?>" style="width:<?php 
				  echo $img_width_px; 
				?>px; border:0;" />
			<?php
			
			echo $close_link;
			
	}

} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function batch_num_dropdown($record_id = 0) {
	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
?>
	<select data-plugin-selectTwo class="form-control populate" name="batch_ID" required>
	<option value=""></option>
	<?php
	// get batch list
	$get_batch_list_SQL = "SELECT `part_batch`.`ID`,
	`part_batch`.`PO_ID`,
	`part_batch`.`part_ID`,
	`part_batch`.`batch_number`,
	`part_batch`.`part_rev`,
	`purchase_orders`.`PO_number`,
	`purchase_orders`.`created_date`,
	`purchase_orders`.`description`
	FROM  `part_batch` ,  `purchase_orders`
	WHERE  `part_batch`.`PO_ID` =  `purchase_orders`.`ID` ";
	$result_get_batch_list = mysqli_query($con,$get_batch_list_SQL);
	// while loop
	while($row_get_batch_list = mysqli_fetch_array($result_get_batch_list)) {

		// now print each record:
		$batch_id = $row_get_batch_list['ID'];
		$PO_id = $row_get_batch_list['PO_ID'];
		$part_id = $row_get_batch_list['part_ID'];
		$batch_number = $row_get_batch_list['batch_number'];
		$part_rev = $row_get_batch_list['part_rev'];
		$PO_number = $row_get_batch_list['PO_number'];
		$created_date = $row_get_batch_list['created_date'];

		// get part revision info:
		$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $part_rev;
		$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);
		// while loop
		while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {

			// now print each record:
			$rev_id = $row_get_part_rev['ID'];
			$rev_part_id = $row_get_part_rev['part_ID'];
			$rev_number = $row_get_part_rev['revision_number'];
			$rev_remarks = $row_get_part_rev['remarks'];
			$rev_date = $row_get_part_rev['date_approved'];
			$rev_user = $row_get_part_rev['user_ID'];

		}

	?>
		<option value="<?php echo $batch_id; ?>"<?php if ($batch_id == $record_id) { ?> selected=""<?php } ?>><?php echo $batch_number; ?> (<?php echo $rev_number; ?>) [PO: <?php echo $PO_number; ?>]</option>

		<?php
		}
		?>
	</select>
<?php
} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function record_status($record_status_ID, $show_button = 1) {
				
	if ($record_status_ID == 2) {
		// OK
		$status_color 		= 'success';
		$status_label 		= 'OK';
		$status_icon 		= 'check-circle';
		$status_message 	= 'This record is active and published';
	}
	else if ($record_status_ID == 1) {
		// PENDING
		$status_color 		= 'warning';
		$status_label 		= 'PENDING';
		$status_icon 		= 'exclaimation-circle';
		$status_message		= 'This record is pending review';
	}
	else {
		// DELETED!
		$status_color 		= 'danger';
		$status_label 		= 'UNPUBLISHED';
		$status_icon 		= 'times-circle';
		$status_message 	= 'This record is deleted / unpublished';
	}

	// now chek if we need a button and icon
	if ($show_button == 1) {
		$open_button 	= '<span class="btn btn-' . $status_color . ' btn-xs" title="' . $status_message . '">';
		$icon_code 		= '<i class="fa fa-' . $status_icon . '"></i> ';
		$close_button 	= '</span>';
	}
	else {
		$open_button = '';
		$icon_code = '';
		$close_button = '';
	}
	
	// now build the output:
	echo $open_button;
	echo $icon_code;
	// echo $status_label;
	echo $close_button;

} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */

function get_page_url(){

	// Find out the URL of a PHP file

	global $url;

	$url = 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'];

	if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != ''){
		$url.= $_SERVER['REQUEST_URI'];
	}
	else{
		$url.= $_SERVER['PATH_INFO'];
	}

	return $url;
}// END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
		  
function pagination($URL, $last, $sort, $current_page, $filter='filter=0') { ?>
		<p><strong>Select page:</strong> <select onChange="document.location = this.value"><?php 
	$this_page = 0;
	
	while ($this_page < $last) {
		
		$show_id = $this_page + 1;
		
		?>
        
		<option value="<?php echo $URL; ?>.php?<?php if ($sort!='') { ?>sort=<?php echo $sort; ?>&<?php } ?>page=<?php echo $show_id; ?>&<?php echo $filter; ?>"<?php if ($current_page == $show_id) { ?> selected="selected"<?php } ?>><?php echo $show_id; ?></option><?php 
		
		// now add a count to $this_page:
		$this_page = $this_page + 1;
	
	} // end of pagination menu 
	
	
	?> 
    <option value="<?php echo $URL; ?>.php?<?php if ($sort!='') { ?>sort=<?php echo $sort; ?>&<?php } ?>page=all&<?php echo $filter; ?>"<?php if (($current_page == 'all')||($current_page == '')) { ?> selected="selected"<?php } ?>>ALL</option>
    </select></p>
    <?php 
		
} // end of pagination function
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
function material_drop_down($material_ID = 0, $required_field = 0) {
	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	?>
	<select data-plugin-selectTwo class="form-control populate" name="material_ID"<?php if ($required_field == 1) { ?> required<?php } ?>>
		<?php
	
		$get_mats_SQL = "SELECT * FROM  `material` WHERE `record_status` = '2' ORDER BY `name_EN` ASC";
		// echo $get_mats_SQL;

		$result_get_mats = mysqli_query ( $con, $get_mats_SQL );
		// while loop
		while ( $row_get_mats = mysqli_fetch_array ( $result_get_mats ) ) {	
			$mat_ID = $row_get_mats['ID'];
			$mat_name_EN = $row_get_mats['name_EN'];
			$mat_name_CN = $row_get_mats['name_CN'];
			$mat_description = $row_get_mats['description'];
			$mat_record_status = $row_get_mats['record_status'];
			$mat_wiki_URL = $row_get_mats['wiki_URL'];
		
			// now show the result!
			?>
			<option value="<?php echo $mat_ID; ?>"<?php if ($mat_ID == $material_ID) { ?> selected="selected"<?php } ?>><?php echo $mat_name_EN; 
			
			if (($mat_name_CN != '')&&($mat_name_CN != '中文名')){
				echo " / " . $mat_name_CN;
			}
			
			?></option>
			<?php
		}
		?>
	</select>
	<?php
} // end of material drop-down function
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////

								
function payment_status($current_status, $print_view=0) {
	if ($current_status == 0) {
		?>
			<span class="btn btn-danger btn-xs">
				<i class="fa fa-times" title="NOT PAID"></i>
			</span>
		<?php
	}
	else if ($current_status == 1) {
		?>
			<span class="btn btn-warning btn-xs">
				<i class="fa fa-exclamation-triangle" title="PENDING"></i>
			</span>
		<?php
	}
	else {
		?>
			<span class="btn btn-success btn-xs">
				<i class="fa fa-check" title="PAID"></i>
			</span>
		<?php
	}
} // end of payment_status function
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
function get_img($lookup_table, $record_id, $doc_link = 0, $img_width_px = 100){ 

	// start the session:
	session_start();
	// enable the DB connection:
	include 'db_conn.php';
	
	if ($record_id == 0) {
		?>
		<span class="btn btn-danger">
			<i class="fa fa-exclamation-triangle"></i>
			NO RECORD FOUND!
			<i class="fa fa-exclamation-triangle"></i>
		</span>
		<?php
	}
	else {
	
	
		// now get the photo!
		$num_photos_found = 0;
		$photo_location = "assets/images/no_image_found.jpg";

		$get_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  '" . $lookup_table . "' AND  `lookup_ID` =" . $record_id . " AND `filetype_ID` = '5' AND `record_status` = '2'"; // FILETYPE 5 = PHOTO!
		// echo "<h1>" . $get_photo_SQL . "</h1>";
		$result_get_photo = mysqli_query($con,$get_photo_SQL);
		// while loop
		while($row_get_photo = mysqli_fetch_array($result_get_photo)) {

			$num_photos_found = $num_photos_found + 1;

			// now print each record:
			$photo_id 					= $row_get_photo['ID'];
			$photo_name_EN 				= $row_get_photo['name_EN'];
			$photo_name_CN 				= $row_get_photo['name_CN'];
			$photo_filename 			= $row_get_photo['filename'];
			$photo_filetype_ID 			= $row_get_photo['filetype_ID'];
			$photo_location 			= $row_get_photo['file_location'];
			$photo_lookup_table 		= $row_get_photo['lookup_table'];
			$photo_lookup_id 			= $row_get_photo['lookup_ID'];
			$photo_document_category 	= $row_get_photo['document_category'];
			$photo_record_status 		= $row_get_photo['record_status'];
			$photo_created_by 			= $row_get_photo['created_by'];
			$photo_date_created 		= $row_get_photo['date_created'];
			$photo_filesize_bytes 		= $row_get_photo['filesize_bytes'];
			$photo_document_icon 		= $row_get_photo['document_icon'];
			$photo_document_remarks 	= $row_get_photo['document_remarks'];
			$photo_doc_revision 		= $row_get_photo['doc_revision'];

			if ($photo_filename!='') {
				// now apply filename
				$photo_location = $photo_location . "/" . $photo_filename;
			}
			else {
				$photo_location = "assets/images/no_image_found.jpg";
			}

		} // end get photo
	
		
	
			$close_link = '';
	
			if ($doc_link == 1) { 
	
				$close_link = '</a>';
				if ($photo_location == "assets/images/no_image_found.jpg"){
					?><a href="upload_file.php?table=<?php echo $lookup_table; ?>&lookup_ID=<?php echo $record_id; ?>" title="Upload a new document"><?php 
				}
				else {
					?><a href="document_view.php?id=<?php echo $photo_id; ?>" title="View Document Details"><?php 
				}
			} 
			
			if ($img_width_px == 'header') {
			 	$img_class="img-circle";
			}
			else {
				$img_class="rounded img-responsive";
			}
			
			?>
				<img src="<?php 
					echo $photo_location; 
				?>" class="<?php 
					echo $img_class; 
				?>" alt="Photo" style="width:<?php 
					echo $img_width_px; 
				?>px; border:0;" />
			<?php
			
			echo $close_link;
			
	}

} // END OF FUNCTION
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
/* ****************************************************************** */
function main_menu($menu_id) {
	session_start();
	include 'db_conn.php';
	
	// use this to get the current (active) page info:
	
	$public_path = pathinfo($_SERVER['SCRIPT_NAME']);
	$this_file = $public_path['basename'];
	
	// DEFAULT
	$add_SQL = '';
	if (isset($_SESSION["user_level"])) {
		// there is a live session - show them things like the log out button, instead of the log in button etc.
		
		// ONLY SHOW MENU ITEMS THEIR USER LEVEL ALLOWS
		$add_SQL = " AND `min_user_level` <= " . $_SESSION["user_level"] . "";
	}
	else {
		// not logged in - show public pages ONLY
		$add_SQL = " AND `privacy` = 'PUBLIC'";
	}
	
	if ($menu_id == 1) {
		$count_pages_1_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = 0" . $add_SQL . "";
		$get_menu_1_SQL = "SELECT * FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = 0" . $add_SQL . " ORDER BY `order` ASC";
	}
	else if ($menu_id == 2) {
		$count_pages_1_sql = "SELECT COUNT(ID) FROM `pages` WHERE `footer_menu` = 1 AND `record_status` = 2 AND `parent_ID` = 0" . $add_SQL . "";
		$get_menu_1_SQL = "SELECT * FROM `pages` WHERE `footer_menu` = 1 AND `record_status` = 2 AND `parent_ID` = 0" . $add_SQL . " ORDER BY `order` ASC";
	}
	else {
		echo "<h4>Something strange happened to level 1...?!</h4>";
	}
	
	// echo "<h3>COUNT: ".$count_pages_1_sql."</h3>";
	// echo "<h3>SQL: ".$get_menu_1_SQL."</h3>";
	
	// first, let's check to make sure we have menu items:			
	$count_pages_1_query = mysqli_query($con, $count_pages_1_sql);
	
	$count_pages_1_row = mysqli_fetch_row($count_pages_1_query);
	// Here we have the total row count
	$total_pages_1 = $count_pages_1_row[0];
	
	if ($total_pages_1 > 0) {
	
	?>
    <!-- START MENU ID <?php echo $menu_id; ?> -->
    <ul class="nav nav-main"><?	
	
	$result_get_menu_1 = mysqli_query($con,$get_menu_1_SQL);
	// while loop
	while($row_get_menu_1 = mysqli_fetch_array($result_get_menu_1)) {
		
			// set vars:  
			$page_1_id = $row_get_menu_1['ID'];
			$page_1_name_EN = $row_get_menu_1['name_EN'];
			$page_1_name_CN = $row_get_menu_1['name_CN'];
			$page_1_parent_ID = $row_get_menu_1['parent_ID'];
			$page_1_dept_ID = $row_get_menu_1['dept_ID'];
			$page_1_filename = $row_get_menu_1['filename'];
			$page_1_icon = $row_get_menu_1['icon'];
			$page_1_privacy = $row_get_menu_1['privacy'];
			$page_1_og_type = $row_get_menu_1['og_type'];
			
			// count sub-items:	
			if ($menu_id == 1) {
				$count_sub_pages_1_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ". $page_1_id ."" . $add_SQL . "";
			}
			else if ($menu_id == 2) {
				$count_sub_pages_1_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 2 AND `record_status` = 2 AND `parent_ID` = ". $page_1_id ."" . $add_SQL . "";
			}		
			$count_sub_pages_1_query = mysqli_query($con, $count_sub_pages_1_sql);
			$count_sub_pages_1_row = mysqli_fetch_row($count_sub_pages_1_query);
			// Here we have the total row count
			$total_sub_pages_1 = $count_sub_pages_1_row[0];
	?>
    
    <li class="<?php if ($total_sub_pages_1 > 0) { ?>nav-parent<?php } if ($this_file == $page_1_filename) { ?> nav-active<?php } ?>"><a href="<?php echo $page_1_filename; ?>" title="<?php echo $page_1_name_EN; if (($page_1_name_CN!='')&&($page_1_name_CN!='中文名')) { ?> / <?php echo $page_1_name_CN; } ?>">
    	<?php if ($page_1_og_type == 'inbox') { 
						
		// get TOTAL mail count
		$count_total_mail_SQL = "SELECT COUNT( ID ) FROM  `message_log` WHERE `type` =  'cosmosysmail' AND `to_ID` = '" . $_SESSION['user_id'] . "' AND  `status` =  '2' OR `type` = 'cosmosysmail' AND `to_ID` = '" . $_SESSION['user_id'] . "' AND  `status` =  '3'";
		
		$count_total_mail_query = mysqli_query($con, $count_total_mail_SQL);
		$count_total_mail_row = mysqli_fetch_row($count_total_mail_query);
		// Here we have the total row count
		$count_total_mail = $count_total_mail_row[0];
		
		?>
		<span class="pull-right label label-primary"><?php echo $count_total_mail; ?></span>
        <?php } ?>
        <i class="fa <?php echo $page_1_icon; ?>" aria-hidden="true"></i> 
		<span><?php echo $page_1_name_EN; ?></span></a><?php
    ////////////////////////////////////////////////////
	//           START LEVEL 2 CODE: 
    ////////////////////////////////////////////////////
	if ($menu_id == 1) {
		$count_pages_2_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ".$page_1_id."" . $add_SQL . " ORDER BY `order` ASC";
		$get_menu_2_SQL = "SELECT * FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ".$page_1_id."" . $add_SQL . " ORDER BY `order` ASC";
	}
	else if ($menu_id == 2) {
		$count_pages_2_sql = "SELECT COUNT(ID) FROM `pages` WHERE `footer_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ".$page_1_id."" . $add_SQL . " ORDER BY `order` ASC";
		$get_menu_2_SQL = "SELECT * FROM `pages` WHERE `footer_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ".$page_1_id."" . $add_SQL . " ORDER BY `order` ASC";
	}
	else {
		echo "<h4>Something strange happened to level 2...?!</h4>";
	}
	
	// echo "<h3>".$count_pages_2_sql."</h3>";
	// echo "<h3>".$get_menu_2_SQL."</h3>";
	
	// first, let's check to make sure we have menu items:
	$count_pages_2_query = mysqli_query($con, $count_pages_2_sql);
	
	$count_pages_2_row = mysqli_fetch_row($count_pages_2_query);
	// Here we have the total row count
	$total_pages_2 = $count_pages_2_row[0];
	
	if ($total_pages_2 > 0) {
	
	?>
    
    <!-- START LEVEL 2 UL -->
    <ul class="nav nav-children"><?	
	
	$result_get_menu_2 = mysqli_query($con,$get_menu_2_SQL);
	// while loop
	while($row_get_menu_2 = mysqli_fetch_array($result_get_menu_2)) {
		
			// set vars:  
			$page_2_id = $row_get_menu_2['ID'];
			$page_2_name_EN = $row_get_menu_2['name_EN'];
			$page_2_name_CN = $row_get_menu_2['name_CN'];
			$page_2_parent_ID = $row_get_menu_2['parent_ID'];
			$page_2_dept_ID = $row_get_menu_2['dept_ID'];
			$page_2_filename = $row_get_menu_2['filename'];
			$page_2_icon = $row_get_menu_2['icon'];
			$page_2_privacy = $row_get_menu_2['privacy'];
			$page_2_og_type = $row_get_menu_2['og_type'];
			
			// count sub-items:	
			if ($menu_id == 1) {
				$count_sub_pages_2_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ". $page_2_id ."" . $add_SQL . "";
			}
			else if ($menu_id == 2) {
				$count_sub_pages_2_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 2 AND `record_status` = 2 AND `parent_ID` = ". $page_2_id ."" . $add_SQL . "";
			}		
			$count_sub_pages_2_query = mysqli_query($con, $count_sub_pages_2_sql);
			$count_sub_pages_2_row = mysqli_fetch_row($count_sub_pages_2_query);
			// Here we have the total row count
			$total_sub_pages_2 = $count_sub_pages_2_row[0];
	
	?>
    
    <li class="<?php if ($total_sub_pages_2 > 0) { ?>nav-parent<?php } if ($this_file == $page_2_filename) { ?> nav-active<?php } ?>"><a href="<?php echo $page_2_filename; ?>" title="<?php echo $page_2_name_EN; if (($page_2_name_CN!='')&&($page_2_name_CN!='中文名')) { ?> / <?php echo $page_2_name_CN; } ?>">
	<i class="fa <?php echo $page_2_icon; ?>" aria-hidden="true"></i> 
	<span><?php echo $page_2_name_EN; if (($page_2_name_CN!='')&&($page_2_name_CN!='中文名')) { ?> / <?php echo $page_2_name_CN; } ?></span></a><?php 
if ($menu_id == 1) {
		$count_pages_3_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ".$page_2_id."" . $add_SQL . " ORDER BY `order` ASC";
		$get_menu_3_SQL = "SELECT * FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ".$page_2_id."" . $add_SQL . " ORDER BY `order` ASC";
	}
	else if ($menu_id == 2) {
		$count_pages_3_sql = "SELECT COUNT(ID) FROM `pages` WHERE `footer_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ".$page_2_id."" . $add_SQL . " ORDER BY `order` ASC";
		$get_menu_3_SQL = "SELECT * FROM `pages` WHERE `footer_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ".$page_2_id."" . $add_SQL . " ORDER BY `order` ASC";
	}
	else {
		echo "<h4>Something strange happened to level 3...?!</h4>";
	}
	
	// echo "<h3>".$count_pages_3_sql."</h3>";
	// echo "<h3>".$get_menu_3_SQL."</h3>";
	
	// first, let's check to make sure we have menu items:
	
				
	$count_pages_3_query = mysqli_query($con, $count_pages_3_sql);
	
	$count_pages_3_row = mysqli_fetch_row($count_pages_3_query);
	// Here we have the total row count
	$total_pages_3 = $count_pages_3_row[0];
	
	if ($total_pages_3 > 0) {
	
	?>
    
    <!-- START LEVEL 3 UL -->
    <ul class="nav nav-children"><?	
	
	$result_get_menu_3 = mysqli_query($con,$get_menu_3_SQL);
	// while loop
	while($row_get_menu_3 = mysqli_fetch_array($result_get_menu_3)) {
		
			// set vars:  
			$page_3_id = $row_get_menu_3['ID'];
			$page_3_name_EN = $row_get_menu_3['name_EN'];
			$page_3_name_CN = $row_get_menu_3['name_CN'];
			$page_3_parent_ID = $row_get_menu_3['parent_ID'];
			$page_3_dept_ID = $row_get_menu_3['dept_ID'];
			$page_3_filename = $row_get_menu_3['filename'];
			$page_3_icon = $row_get_menu_3['icon'];
			$page_3_privacy = $row_get_menu_3['privacy'];
			$page_3_og_type = $row_get_menu_3['og_type'];
			
			// count sub-items:	
			if ($menu_id == 1) {
				$count_sub_pages_3_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 1 AND `record_status` = 2 AND `parent_ID` = ". $page_3_id ."" . $add_SQL . "";
			}
			else if ($menu_id == 2) {
				$count_sub_pages_3_sql = "SELECT COUNT(ID) FROM `pages` WHERE `main_menu` = 2 AND `record_status` = 2 AND `parent_ID` = ". $page_3_id ."" . $add_SQL . "";
			}		
			$count_sub_pages_3_query = mysqli_query($con, $count_sub_pages_3_sql);
			$count_sub_pages_3_row = mysqli_fetch_row($count_sub_pages_3_query);
			// Here we have the total row count
			$total_sub_pages_3 = $count_sub_pages_3_row[0];
	?>
    
    <li class="<?php if ($total_sub_pages_3 > 0) { ?>nav-parent<?php }if ($this_file == $page_3_filename) { ?>nav-active<?php } ?>"><a href="<?php echo $page_3_filename; ?>" title="<?php echo $page_3_name_EN; if (($page_3_name_CN!='')&&($page_3_name_CN!='中文名')) { ?> / <?php echo $page_3_name_CN; } ?>">
	<i class="fa <?php echo $page_3_icon; ?>" aria-hidden="true"></i> 
     <?php echo $page_3_name_EN; ?></a>
    <!-- FINISH LEVEL 3 LIST ITEM -->
    </li>
    <?php 
	// END WHILE MENU_3
	}
	?>
    
  <!-- close the level 3 list -->
  </ul>
  <?php } // end if ($total_pages_3 > 0) ?>

    <!-- FINISH LEVEL 2 LIST ITEM -->
    </li>
    <?php } // END WHILE MENU_3  ?>
    
  <!-- close the level 2 list -->
  </ul>
  <?php 
	} // end if ($total_pages_2 > 0)
    ////////////////////////////////////////////////////
	//                END LEVEL 2 CODE: 
    //////////////////////////////////////////////////// ?>
    
    <!-- END MENU LIST ITEM 1 -->
    </li>
    <?php 
	// END WHILE MENU_1
	}
	
	// NOW SHOW LOG OUT!
	if (!isset($_SESSION["user_level"])) { ?>
    	<li class="<?php if ($this_file == 'login.php') { ?>nav-active<?php } ?>">
        	<a href="login.php" title="Login">
				<i class="fa fa-power-off" aria-hidden="true" style="color:#0C0;"></i> 
     			<span>Login / 注册</span>
            </a>
        </li>
	<?php } ?>
        
  <!-- close the level 1 list -->
  </ul>
  <?php 
	} // end if ($total_pages_1 > 0)
  ?>
  
    <!-- FINISH MENU ID <?php echo $menu_id; ?> --> <?php 
} // END OF MAIN MENU FUNCTION


///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
?>
