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
$id 					= $_REQUEST['id'];
$name_en 				= $_REQUEST['name_EN'];
$name_cn 				= $_REQUEST['name_CN'];
$part_code 				= $_REQUEST['part_code'];
$record_status 			= $_REQUEST['record_status']; // output in the previous page is via page_functions.php
$created_by 			= $_REQUEST['created_by']; // output in the previous page is via page_functions.php
$part_description 		= $_REQUEST['part_desc'];
$part_type_ID 			= $_REQUEST['part_type_ID'];
$classificiation_ID 	= $_REQUEST['classificiation_ID'];
$sup_ID					= $_REQUEST['sup_ID'];
$product_type_ID		= $_REQUEST['product_type_ID'];

$update_note = "Editing a Part record in the system.";

$edit_SQL = "UPDATE `parts` SET `part_code`='" . $part_code . "',`name_EN` = '".$name_en."', `name_CN` = '".$name_cn."',`description`='".$part_description."',`type_ID`='".$part_type_ID."',`classification_ID`='".$classificiation_ID."',`default_suppler_ID`='".$sup_ID."',`record_status`='".$record_status."',`product_type_ID`='".$product_type_ID."',`created_by`='".$created_by."' WHERE ID = '" . $id . "'";


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