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

$update_note = "Deleting a product in the system.";

$delete_product_SQL = "DELETE FROM `products` WHERE `ID` = '".$id."' ";


if (mysqli_query($con, $delete_product_SQL)) {


    $record_delete_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'products','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'DELETE')";

		if (mysqli_query($con, $record_delete_SQL)) {

            header("Location: products.php?msg=OK&action=delete");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_delete_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to delete existing product with SQL: <br />" . $delete_product_SQL . "</h4>";
}

?>
