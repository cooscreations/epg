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

		$update_table = 'pages';

		$update_note = "Editing a page record in the system.";

		$edit_SQL = "UPDATE `pages` SET `name_EN` ='" . $name_EN . "', `name_CN` ='" . $name_CN . "', `parent_ID` ='" . $parent_page_ID . "', `dept_ID` ='0', `main_menu` ='" . $page_main_menu . "', `footer_menu` ='0', `filename` ='" . $filename . "', `created_by` ='" . $created_by . "', `date_created` ='" . $date_created . "', `record_status` ='" . $status . "', `privacy` ='" . $privacy . "', `min_user_level` ='" . $min_user_level . "', `order` ='" . $page_order . "', `icon` ='" . $icon . "', `og_locale` ='" . $OG_locale . "', `og_type` ='" . $OG_type . "', `og_desc` ='" . $OG_desc . "', `og_section` ='" . $OG_section . "', `side_bar_config` ='" . $sidebar_config . "', `lookup_table` ='" . $lookup_table . "' WHERE `ID` ='" . $page_ID . "'";

		// echo $edit_SQL;

		if (mysqli_query($con, $edit_SQL)) {

			$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $update_table . "','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
				// echo $record_edit_SQL;

				if (mysqli_query($con, $record_edit_SQL)) {
					// AWESOME! We added the change record to the database

						// regular add
					header("Location: index.php?msg=OK&action=edit&id=" . $id . "");

					exit();

				}
				else {
					echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
				}

		}
		else {
			echo "<h4>Failed to update existing record with SQL: <br />" . $edit_SQL . "</h4>";
		}

}

?>
