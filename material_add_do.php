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

$name_en = $_REQUEST['name_en'];
$name_cn = $_REQUEST['name_cn'];
$description = $_REQUEST['description'];

$update_note = "Adding a new material to the system.";

$add_material_SQL = "INSERT INTO `material`(`ID`, `name_EN`, `name_CN`, `description`) VALUES (NULL,'".$name_en."','".$name_cn."','".$description."')";


if (mysqli_query($con, $add_material_SQL)) {

	$record_id = mysqli_insert_id($con);


    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'material','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";

		if (mysqli_query($con, $record_edit_SQL)) {

				header("Location: materials.php?msg=OK&action=add&new_record_id=".$record_id."");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to add new material with SQL: <br />" . $add_material_SQL . "</h4>";
}

?>
