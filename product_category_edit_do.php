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
	header("Location: login.php"); // send them to the Login page.
}
$user_ID = $_SESSION['user_ID'];
/* 

THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

*/
$id = $_REQUEST['id'];
$name_en = $_REQUEST['name_EN'];
$name_cn = $_REQUEST['name_CN'];
$cat_code = $_REQUEST['cat_code'];

$update_note = "Editing a Product Category record in the system.";

$edit_product_category_SQL = "UPDATE `product_categories` SET `name_EN` = '".$name_en."', `name_CN` = '".$name_cn."', `cat_code` = '".$cat_code."' WHERE `ID` = '".$id."' ";


if (mysqli_query($con, $edit_product_category_SQL)) {
	
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'product_categories','" . $id . "','".$user_ID."','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			
				header("Location: product_categories.php?msg=OK&action=edit");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing part type with SQL: <br />" . $edit_product_category_SQL . "</h4>";
}

?>