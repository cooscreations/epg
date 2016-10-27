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

/*

THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

*/
$id 					= checkaddslashes($_REQUEST['id']);
$name_en 				= checkaddslashes($_REQUEST['name_EN']);
$name_cn 				= checkaddslashes($_REQUEST['name_CN']);
$part_code 				= checkaddslashes($_REQUEST['part_code']);
$record_status 			= checkaddslashes($_REQUEST['record_status']); 		// output in the previous page is via page_functions.php
$created_by 			= checkaddslashes($_REQUEST['created_by']); 			// output in the previous page is via page_functions.php
$part_description 		= checkaddslashes($_REQUEST['part_desc']);
$part_type_ID 			= checkaddslashes($_REQUEST['part_type_ID']);
$classificiation_ID 	= checkaddslashes($_REQUEST['classificiation_ID']);
$sup_ID					= checkaddslashes($_REQUEST['sup_ID']);
$product_type_ID		= checkaddslashes($_REQUEST['product_type_ID']);
$default_suppler_ID		= checkaddslashes($_REQUEST['sup_ID']);
$is_finished_product	= checkaddslashes($_REQUEST['is_finished_product']);

if ($is_finished_product == '') { $is_finished_product = 0; }

$update_note = "Editing a Part record in the system.";

$edit_SQL = "UPDATE `parts` SET `part_code`='" . $part_code . "',`name_EN` = '".$name_en."', `name_CN` = '".$name_cn."',`description`='".$part_description."',`type_ID`='".$part_type_ID."',`classification_ID`='".$classificiation_ID."',`default_suppler_ID`='".$default_suppler_ID."',`record_status`='".$record_status."',`product_type_ID`='".$product_type_ID."',`created_by`='".$created_by."', `is_finished_product`='".$is_finished_product."' WHERE ID = '" . $id . "'";

// echo $edit_SQL;

if (mysqli_query($con, $edit_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'parts','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
            header("Location: part_view.php?id=".$id."&msg=OK&action=edit");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing part with SQL: <br />" . $edit_SQL . "</h4>";
}

?>
