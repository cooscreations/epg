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

$test = 0;

if ($test == 1) { ?>

		<table>
		<?php 

			echo "<table>";
			echo "<tr><th>KEY</th><th>VALUE</th></tr>";
			foreach ($_POST as $key => $value) {
				
				echo "<tr>";
				echo "<td>";
				echo $key;
				echo "</td>";
				echo "<td>";
				echo $value;
				echo "</td>";
				echo "</tr>";
				
				// echo "<pre>$" . $key . " = checkaddslashes($_REQUEST['" . $key . "']);</pre><br />"
				
			}
			echo "</table>";

} 
else {

		/*

		THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

		*/
		
		$id 					= checkaddslashes($_REQUEST['id']);
		$name_EN 				= checkaddslashes($_REQUEST['name_EN']);
		$name_CN 				= checkaddslashes($_REQUEST['name_CN']);
		$parent_page_ID 		= checkaddslashes($_REQUEST['parent_page_ID']);
		$page_main_menu 		= checkaddslashes($_REQUEST['page_main_menu']);
		if ($page_main_menu == 'on') { $page_main_menu = 1; } else { $page_main_menu = 0; }
		$filename 				= checkaddslashes($_REQUEST['filename']);
		$created_by 			= checkaddslashes($_REQUEST['created_by']);
		$date_created 			= checkaddslashes($_REQUEST['date_created']);
		$status 				= checkaddslashes($_REQUEST['status']);
		$privacy 				= checkaddslashes($_REQUEST['privacy']);
		$min_user_level 		= checkaddslashes($_REQUEST['min_user_level']);
		$page_order 			= checkaddslashes($_REQUEST['page_order']);
		$icon 					= checkaddslashes($_REQUEST['icon']);
		$OG_locale 				= checkaddslashes($_REQUEST['OG_locale']);
		$OG_type 				= checkaddslashes($_REQUEST['OG_type']);
		$OG_desc 				= checkaddslashes($_REQUEST['OG_desc']);
		$OG_section 			= checkaddslashes($_REQUEST['OG_section']);
		$sidebar_config 		= checkaddslashes($_REQUEST['sidebar_config']);
		$lookup_table 			= checkaddslashes($_REQUEST['lookup_table']);
		$page_ID 				= checkaddslashes($_REQUEST['page_ID']);

			$update_note = "Adding a new page to the system.";

			// $add_SQL = "INSERT INTO `product_BOM`(`ID`, `part_rev_ID`, `date_entered`, `record_status`, `created_by`, `BOM_type`, `parent_BOM_ID`, `entry_order`) VALUES (NULL,'" . $part_rev_ID . "','" . date("Y-m-d H:i:s") . "','2','" . $_SESSION['user_ID'] . "','" . $BOM_type . "','" . $parent_BOM_ID . "','" . $entry_order . "')";

			$add_SQL = "INSERT INTO `pages`(`ID`, `name_EN`, `name_CN`, `parent_ID`, `dept_ID`, `main_menu`, `footer_menu`, `filename`, `created_by`, `date_created`, `record_status`, `privacy`, `min_user_level`, `order`, `icon`, `og_locale`, `og_type`, `og_desc`, `og_section`, `side_bar_config`, `lookup_table`) 
			VALUES (NULL,'" . $name_EN . "','" . $name_CN . "','" . $parent_page_ID . "','0','" . $page_main_menu . "','0','" . $filename . "','" . $_SESSION['user_ID'] . "','" . date("Y-m-d H:i:s") . "','2','" . $privacy . "','" . $min_user_level . "','" . $page_order . "','" . $icon . "','" . $OG_locale . "','" . $OG_type . "','" . $OG_desc . "','" . $OG_section . "','" . $sidebar_config . "', '".$lookup_table."')";
	
			// echo $add_SQL;

			if (mysqli_query($con, $add_SQL)) {

				$record_id = mysqli_insert_id($con);

				// echo "INSERT # " . $record_id . " OK";

				// AWESOME! We added the record
				$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'product_BOM','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
				// echo $record_edit_SQL;

				if (mysqli_query($con, $record_edit_SQL)) {
					// AWESOME! We added the change record to the database

					// regular add
					header("Location: index.php?msg=OK&action=add&id=".$record_id."");
					exit();

				}
				else {
					echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
				}

			}
			else {
				echo "<h4>Failed to add new record with SQL: <br />" . $add_SQL . "</h4>";
			}
}
?>
