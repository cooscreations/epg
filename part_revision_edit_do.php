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
$part_ID 				= checkaddslashes($_REQUEST['part_ID']);
$revision_number 		= checkaddslashes($_REQUEST['revision_number']);
$part_rev_material_ID 	= checkaddslashes($_REQUEST['part_rev_material_ID']);
$price_USD 				= checkaddslashes($_REQUEST['price_USD']);
$weight_g 				= checkaddslashes($_REQUEST['weight_g']);
$part_rev_status_ID 	= checkaddslashes($_REQUEST['part_rev_status_ID']);
$remarks 				= checkaddslashes($_REQUEST['remarks']);
$date_added 			= check_date_time($_REQUEST['date_added']);
$created_by 			= checkaddslashes($_REQUEST['created_by']);
$record_status 			= checkaddslashes($_REQUEST['record_status']);


$update_note = "Editing a Part Revision in the system.";

$edit_SQL = "UPDATE `countries` SET `name_EN` = '".$name_en."', `name_CN` = '".$name_cn."', `code` = '".$code."' WHERE `ID` = '".$id."' ";

$edit_SQL = "UPDATE `part_revisions` SET 
`part_ID`='".$part_ID."',
`revision_number`='".$revision_number."',
`remarks`='".$remarks."',
`date_approved`='".$date_added."',
`user_ID`='".$created_by."',
`price_USD`='".$price_USD."',
`weight_g`='".$weight_g."',
`status_ID`='".$part_rev_status_ID."',
`material_ID`='".$part_rev_material_ID."',
`record_status`='".$record_status."' 
WHERE `ID` = '".$id."' ";

// echo $edit_SQL;

if (mysqli_query($con, $edit_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_revisions','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
            header("Location: part_revisions.php?msg=OK&action=edit");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing part revision with SQL: <br />" . $edit_SQL . "</h4>";
}

?>
