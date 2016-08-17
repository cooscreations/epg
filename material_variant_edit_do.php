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
$id = $_REQUEST['id'];
$name_en = $_REQUEST['name_EN'];
$name_cn = $_REQUEST['name_CN'];
$description = $_REQUEST['description'];
$material_ID = $_REQUEST['MATERIAL_ID'];
$material_variant_type_ID = $_REQUEST['MATERIAL_VARIANT_TYPE_ID'];
$color_code = $_REQUEST['color_code'];

$update_note = "Editing a Material Variant in the system.";

$edit_material_variant_SQL = "UPDATE `material_variant` SET `name_EN` = '".$name_en."', `name_CN` = '".$name_cn."', `description` = '".$description."', `material_ID` = '".$material_ID."', `variant_type` = '".$material_variant_type_ID."', `code` = '".$color_code."' WHERE `ID` = '".$id."' ";
if (mysqli_query($con, $edit_material_variant_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'material_variant','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add - send them to the revisions list for that part
				header("Location: material_view.php?id=".$material_ID."&msg=OK&action=edit");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing material with SQL: <br />" . $edit_material_variant_SQL . "</h4>";
}

?>
