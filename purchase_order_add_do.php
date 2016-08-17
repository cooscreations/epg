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

$po_number = $_REQUEST['po_number'];
$user_ID = $_REQUEST['user_ID'];
$date_added = $_REQUEST['date_added'] . " 00:00:00";
$description = $_REQUEST['description'];
$record_status = 2;
$supplier_ID = $_REQUEST['sup_ID'];
$supplier_name_en = $_REQUEST['supplier_name_en'];
$supplier_status = 3;
/*
 *  Check if PO# already exists in the database.
 */

$select_by_po = "select `po_number` from `purchase_orders` where `po_number` = '" . $po_number . "'";
$result_select_by_po = mysqli_query($con,$select_by_po);
while($row_select_by_po = mysqli_fetch_array($result_select_by_po)) {
	header("Location: purchase_order_add.php?msg=NG&error=duplicate&field=".$po_number);
	exit();
}

/* Add supplier Begin */
if ($supplier_name_en != ""){
	$update_note = "Adding a new supplier to the system.";
	$add_supplier_SQL = "INSERT INTO `suppliers`(`ID`, `name_EN`,`supplier_status`) VALUES (NULL,'".$supplier_name_en."','".$supplier_status."')";
	if (mysqli_query($con, $add_supplier_SQL)) {
		$supplier_ID = mysqli_insert_id($con);
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'suppliers','" . $supplier_ID . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		if (mysqli_query($con, $record_edit_SQL)) {
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
	}
	else {
		echo "<h4>Failed to add new supplier with SQL: <br />" . $add_supplier_SQL . "</h4>";
	}
}


/* Add PO Begin*/
$update_note = "Adding a new purchase order to the system.";

// ADDING SUPPLIER ID, USER ID AND RECORD STATUS (defaulted to '2')
$add_purchaseorder_SQL = "INSERT INTO `purchase_orders`(`ID`, `PO_number`, `created_date`, `description`, `record_status`, `supplier_ID`, `created_by`) VALUES (NULL,'".$po_number."','".$date_added."','".$description."', '".$record_status."', '".$supplier_ID."', '". $user_ID ."')";

// echo $add_movement_SQL;

if (mysqli_query($con, $add_purchaseorder_SQL)) {

	$record_id = mysqli_insert_id($con);

		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'purchase_orders','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add - send them to the revisions list for that part
				header("Location: purchase_orders.php?po_number=".$po_number."&msg=OK&action=add&new_record_id=".$record_id."");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $add_purchaseorder_SQL . "</h4>";
}

?>
