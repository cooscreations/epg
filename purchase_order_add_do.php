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

$po_id 							= 'NULL';														//
$po_sup_ID 						= checkaddslashes($_REQUEST['sup_ID']);							// 
$po_local_location_ID 			= checkaddslashes($_REQUEST['local_location_ID']);				//
$po_HQ_location_ID 				= checkaddslashes($_REQUEST['HQ_location_ID']);					//
$po_number 						= checkaddslashes($_REQUEST['po_number']);						//
$po_description 				= checkaddslashes($_REQUEST['description']);					//
$po_ship_via 					= checkaddslashes($_REQUEST['ship_via']);						//
$po_created_by 					= checkaddslashes($_REQUEST['created_by']);						//
$po_date_added 					= check_date_time($_REQUEST['date_added']);						//
$po_date_needed					= check_date_time($_REQUEST['date_needed']);					//
$po_date_delivered 				= check_date_time($_REQUEST['date_delivered']);					//
$po_ship_to_location_ID 		= checkaddslashes($_REQUEST['ship_to_location_ID']);			//
$po_payment_status 				= checkaddslashes($_REQUEST['payment_status']);					//
$po_special_reqs 				= checkaddslashes($_REQUEST['special_reqs']);					//
$po_related_standards 			= checkaddslashes($_REQUEST['related_standards']);				//
$po_special_contracts 			= checkaddslashes($_REQUEST['special_contracts']);				//
$po_qualification_personnel 	= checkaddslashes($_REQUEST['qualification_personnel']);		//
$po_QMS_reqs 					= checkaddslashes($_REQUEST['QMS_reqs']);						//
$po_include_CoC 				= checkaddslashes($_REQUEST['include_CoC']);					//
$po_approval_status 			= checkaddslashes($_REQUEST['approval_status']);				//
$po_approved_by 				= checkaddslashes($_REQUEST['approved_by']);					//
$po_date_approved 				= check_date_time($_REQUEST['date_approved']);					//
$po_date_confirmed 				= check_date_time($_REQUEST['date_confirmed']);					//
$po_remarks 					= checkaddslashes($_REQUEST['remarks']);						//
$po_record_status 				= checkaddslashes($_REQUEST['record_status']);					//
$po_completion_status			= checkaddslashes($_REQUEST['completion_status']);				//
$po_default_currency_rate 		= checkaddslashes($_REQUEST['po_default_currency_rate']);		//				
$po_currency_id					= checkaddslashes($_REQUEST['currency_id']);					//

if ($po_include_CoC == '') { $po_include_CoC = 0; }

/*
 *  Check if PO# already exists in the database.
 */

$select_by_po = "select `po_number` from `purchase_orders` where `po_number` = '" . $po_number . "'";
$result_select_by_po = mysqli_query($con,$select_by_po);
while($row_select_by_po = mysqli_fetch_array($result_select_by_po)) {
	header("Location: purchase_order_add.php?msg=NG&error=duplicate&field=PO_number&po_ID=".$po_number);
	exit();
}


/* Add PO Begin*/
$update_note = "Adding a new purchase order to the system.";

// ADDING RECORD:
$add_record_SQL = "INSERT INTO `purchase_orders`(
	`ID`, 
	`PO_number`, 
	`created_date`, 
	`description`, 
	`record_status`, 
	`supplier_ID`, 
	`created_by`, 
	`date_needed`, 
	`date_delivered`, 
	`approval_status`, 
	`payment_status`, 
	`completion_status`, 
	`remark`, 
	`approved_by`, 
	`approval_date`, 
	`include_CoC`, 
	`date_confirmed`, 
	`ship_via`, 
	`special_reqs`, 
	`related_standards`, 
	`special_contracts`, 
	`qualification_personnel`, 
	`QMS_reqs`, 
	`local_location_ID`, 
	`HQ_location_ID`, 
	`ship_to_location_ID`, 
	`default_currency`, 
	`default_currency_rate`) VALUES (
	NULL,
	'" . $po_number . "',
	'" . $po_date_added . "',
	'" . $po_description . "',
	'" . $po_record_status . "',
	'" . $po_sup_ID . "',
	'" . $po_created_by . "',
	'" . $po_date_needed . "',
	'" . $po_date_delivered . "',
	'" . $po_approval_status . "',
	'" . $po_payment_status . "',
	'" . $po_completion_status . "',
	'" . $po_remarks . "',
	'" . $po_approved_by . "',
	'" . $po_date_approved . "',
	'" . $po_include_CoC . "',
	'" . $po_date_confirmed . "',
	'" . $po_ship_via . "',
	'" . $po_special_reqs . "',
	'" . $po_related_standards . "',
	'" . $po_special_contracts . "',
	'" . $po_qualification_personnel . "',
	'" . $po_QMS_reqs . "',
	'" . $po_local_location_ID . "',
	'" . $po_HQ_location_ID . "',
	'" . $po_ship_to_location_ID . "',
	'" . $po_currency_id . "',
	'" . $po_default_currency_rate . "')";

// echo $add_record_SQL;

if (mysqli_query($con, $add_record_SQL)) {

	$record_id = mysqli_insert_id($con);

		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'purchase_orders','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database
				
				if ($_REQUEST['next_step'] == 'add_record') {
					// regular add - send them to the revisions list for that part
					header("Location: purchase_order_add.php?msg=OK&action=add&id=".$record_id."&sup_ID=" . $po_sup_ID . "&next_step=add");
					exit();
				}
				else {
					// regular add - send them to the revisions list for that part
					header("Location: purchase_order_view.php?msg=OK&action=add&id=".$record_id."");
					exit();
				}

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $add_record_SQL . "</h4>";
}

?>
