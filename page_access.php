<?php 
session_start();
include 'db_conn.php';

// check the page authentication settings and either grant or deny access

// check filename for DB lookup

// this can be replaced by $url as a function variable later:
$public_path = pathinfo($_SERVER['SCRIPT_NAME']);
$this_file = $public_path['basename'];

// echo "<h2>This file: ".$this_file."</h2>";

global $show_msg;
$show_msg = '';

global $page_access;
$page_access = 0;

// echo "<h3>Page Access ln 20: " . $page_access . "</h3>";

global $site_status;
$site_status = '0';

$get_site_stats_SQL = "SELECT * FROM  `global_config` WHERE `ID` = 1";
// echo $get_site_stats_SQL;
$result_get_stats = mysqli_query($con,$get_site_stats_SQL);
// while loop
while($row_get_stats = mysqli_fetch_array($result_get_stats)) {
	$site_ID = $row_get_stats['ID'];
	$site_status = $row_get_stats['site_status'];
	$site_cookie_lifetime = $row_get_stats['cookie_lifetime'];
	$site_site_name_EN = $row_get_stats['site_name_EN'];
	$site_site_name_CN = $row_get_stats['site_name_CN'];
	$site_GMT_diff = $row_get_stats['GMT_diff'];
	$site_root_url = $row_get_stats['root_url'];
	$site_default_og_image = $row_get_stats['default_og_image'];
}

// echo "<h3>Site Status: " . $site_status . "</h3>";

if ($site_status==2) {
	// site is OK and running!
	
	// GET PAGE ACCESS INFO FROM THE DB:
	
	// 1. count to make sure we have at least 1:
	$count_pages_sql = "SELECT COUNT(ID) FROM `pages` WHERE `filename` = '" . $this_file . "'";
	// debug:
	// echo "<h3>Count SQL: ".$count_pages_sql."</h3>";			
	$count_pages_query = mysqli_query($con, $count_pages_sql);
	$count_pages_row = mysqli_fetch_row($count_pages_query);
	$total_pages = $count_pages_row[0];
	// debug:
	// echo "<h3>Total pages: ".$total_pages."</h3>";
	
	if ($total_pages>0) {
		// we found pages - go get the info!
		$get_page_SQL = "SELECT * FROM `pages` WHERE `filename` = '" . $this_file . "'";
		$result_get_page = mysqli_query($con,$get_page_SQL);
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
				
				// now let's do some more auth checks:
				
				// 1. Minimum auth check:
				if (($_SESSION["user_level"] < $page_min_user_level)&&($page_min_user_level!=0)) {
					header("Location: user_message.php?msg=NG&error=level&page_id=" . $page_id . "&mul=" . $page_min_user_level . "&sul=" . $_SESSION["user_level"] . "");
					exit();	
				}
				
				// 2. Check to make sure the page is live
				if ($page_status<2) {
					if ($_SESSION["user_level"]>5) {
						
						if ($page_status==1) { $show_page_status = 'pending'; }
						else if ($page_status==0) { $show_page_status = 'deleted'; }
						
						// for execs or admins, show the page with an error
						$show_msg = '
		
<div id="error" style="text-align:center;"><i class="fa fa-eye-slash"></i> This page is currently ' . $show_page_status . ' <i class="fa fa-eye-slash"></i></div>';

						// show the page
						// debug:
						// echo "<h3>Access Granted at Ln 100</h3>";
						$page_access = 1;
					
				  	} // end if EXEC OR ABOVE
					
					
				} // END IF PAGE STATUS IS NOT 2
				else {
					// this user matches the required minimum level (otherwise they would have been redirected above)
					// the page is live (status 2)
					// SHOW THE PAGE!
					$page_access = 1;
					// debug:
					// echo "<h3>Access Granted at Ln 111</h3>";
				} // END PAGE STATUS == 2
				
				
		} // END WHILE PAGE FOUND
	} // END IF PAGES > 0
	else {
		// page info not found?! Send them to an error page:
		header("Location: user_message.php?msg=NG&error=page_not_in_DB&filename=".$this_file."");
	} // END ELSE PAGE NOT FOUND

	
} // END IF SITE STATUS == 2
else if ($site_status==1) {
	// site is in testing / dev mode
	if ($_SESSION["user_level"] > 4) {
		$show_msg = $show_msg . '
		
<div id="error" style="text-align:center;"><i class="fa fa-eye-slash"></i> The site is currently in testing / development mode <i class="fa fa-eye-slash"></i></div>';
	}
	else {
		// site is offline
		header("Location: site_down.php?msg=NG&error=testing");
		exit();
	}
} // END IF SITE STATUS == 1

else {
	// site is offline
	header("Location: site_down.php?msg=NG&error=offline");
	exit();
}

// now write the variables to the session data:

$_SESSION["site_status"] = $site_status;
$_SESSION["cookie_lifetime"] = $site_cookie_lifetime;

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($page_access == 1) {
	// now that it's OK to show them the page, let's use this info to populate the page_head function variables:
	
	global $og_locale;
	global $og_type;
	global $og_title;
	global $og_description;
	global $og_url;
	global $og_site_name;
	global $article_section;
	global $article_published_time;
	global $article_modified_time;
	global $article_updated_time;
	global $og_image;
	global $side_bar_config;
	global $page_id;
	
	$og_locale = $page_og_locale;
	$og_type = $page_og_type;
	$og_title = $page_name_EN;
	if (($page_name_CN!='')&&($page_name_CN!='中文名')) {
		$og_title = $og_title . " / " . $page_name_CN;
	}
	$og_description = $page_og_desc;
	$og_url = $site_root_url . $_SERVER['SCRIPT_NAME'];
	
	$og_site_name = $site_site_name_EN;
	if (($site_site_name_CN!='')&&($site_site_name_CN!='中文名')&&($site_site_name_CN!=$site_site_name_EN)) {
		$og_title = $og_title . " / " . $site_site_name_CN;
	}
	
	$article_section = $page_og_section;
	
	if (($page_date_created=='')||($page_date_created=='0000-00-00 00:00:00')) {
		
		// set defaults to 'SELECT'
		$created_date = date('Y-m-d\TH:i:s') . '+08:00';
		
	}
	else {
	
		// show year, month and date selectors:	
		
		$created_year = substr($page_date_created,0,4);
		$created_month = substr($page_date_created,5,2);
		$created_day = substr($page_date_created,8,2);
		
		$created_date ="".$created_year."-".$created_month."-".$created_day."T08:20+08:00";
	
	}
	
	$article_published_time = $created_date;
	
	// get the last update time:
	$get_update_log_SQL = "SELECT * FROM  `update_log` WHERE  `table_name` = 'pages' AND `update_ID` = " . $page_id . " ORDER BY `update_date` DESC LIMIT 1";
	$result_get_log = mysqli_query($con,$get_update_log_SQL);
		// while loop
		while($row_get_log = mysqli_fetch_array($result_get_log)) {
			
				// set vars:  
				$update_date = $row_get_log['update_date'];
		}
		if (($update_date=='')||($update_date=='0000-00-00 00:00:00')) {
		
			$modified_date = $created_date;
			
		}
		else {	
			
			$modified_year = substr($update_date,0,4);
			$modified_month = substr($update_date,5,2);
			$modified_day = substr($update_date,8,2);
			
			$modified_date ="".$modified_year."-".$modified_month."-".$modified_day."T08:20+08:00";
		
		}
	
	$article_modified_time = $modified_date;
	$article_updated_time = $modified_date;
	$og_image = $site_default_og_image;
	$side_bar_config = $page_side_bar_config;
	
	return $og_locale;
	return $og_type;
	return $og_title;
	return $og_description;
	return $og_url;
	return $og_site_name;
	return $article_section;
	return $article_published_time;
	return $article_modified_time;
	return $article_updated_time;
	return $og_image;
	return $side_bar_config;
	return $page_id;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

return $page_access;
return $show_msg;

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// echo "<h3>Page access within script: " . $page_access . "</h3>";

?>