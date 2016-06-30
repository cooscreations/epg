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

/* 

THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

*/
$id = $_REQUEST['id'];
$name_en = $_REQUEST['name_EN'];
$name_cn = $_REQUEST['name_CN'];
$product_type_code = $_REQUEST['product_type_code'];
$product_cat_ID = $_REQUEST['product_cat_ID'];

$update_note = "Editing a Product Type in the system.";

$edit_product_type_SQL = "UPDATE `product_type` SET `name_EN` = '".$name_en."', `name_CN` = '".$name_cn."', `product_type_code` = '".$product_type_code."', `product_cat_ID` = '".$product_cat_ID."' WHERE `ID` = '".$id."' ";


// echo $edit_supplier_SQL;

if (mysqli_query($con, $edit_product_type_SQL)) {
	
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'product_type','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		
		if (mysqli_query($con, $record_edit_SQL)) {	
				
				header("Location: product_types.php?msg=OK&action=edit");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing product type with SQL: <br />" . $edit_product_type_SQL . "</h4>";
}

?>