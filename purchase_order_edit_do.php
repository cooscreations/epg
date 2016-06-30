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
$po_id = $_REQUEST['po_id'];
$po_number = $_REQUEST['po_number'];
$user_ID = $_REQUEST['user_ID'];
$date_added = $_REQUEST['date_added'] . " 00:00:00";
$description = $_REQUEST['description'];
$supplier_ID = $_REQUEST['sup_ID'];

$update_note = "Editing a purchase order to the system.";

$edit_purchaseorder_SQL = "UPDATE `purchase_orders` SET `PO_number` = '".$po_number."', `created_date` = '".$date_added."', `description` = '".$description."', `supplier_ID` = '".$supplier_ID."', `created_by` = '".$user_ID."' WHERE `ID` = '".$po_id."' ";


// echo $edit_purchaseorder_SQL;

if (mysqli_query($con, $edit_purchaseorder_SQL)) {
	
	$record_id = mysqli_insert_id($con);
	
	// echo "UPDATE # " . $record_id . " OK";
	
		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'purchase_orders','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
				
				// regular add - send them to the revisions list for that part	
				header("Location: purchase_orders.php?po_number=".$po_number."&msg=UPDATEOK&action=edit&edit_record_id=".$record_id."");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing purchase order with SQL: <br />" . $edit_purchaseorder_SQL . "</h4>";
}

?>