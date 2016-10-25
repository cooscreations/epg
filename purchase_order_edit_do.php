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

$update_note = "Editing a purchase order in the system.";

// old SQL query: 
// $edit_purchaseorder_SQL = "UPDATE `purchase_orders` SET `PO_number` = '".$po_number."', `created_date` = '".$date_added."', `description` = '".$description."', `supplier_ID` = '".$supplier_ID."', `created_by` = '".$user_ID."' WHERE `ID` = '".$po_id."' ";



// DEVELOPMENT AREA:

// DEBUG

// foreach ($_REQUEST as $key => $value)
//  echo "$po_".htmlspecialchars($key)." = ".htmlspecialchars($value)."<br>";
 
 
 // echo "$po_".htmlspecialchars($key)." = $_REQUEST['".htmlspecialchars($key)."'];";


$po_id 							= checkaddslashes($_REQUEST['id']);								//
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
$po_tax_rate					= checkaddslashes($_REQUEST['tax_rate']);						//

if ($po_include_CoC == '') { $po_include_CoC = 0; }


$edit_purchaseorder_SQL = "UPDATE `purchase_orders` SET 
`PO_number`					='" . $po_number . "',
`created_date`				='" . $po_date_added . "',
`description`				='" . $po_description . "',
`record_status`				='" . $po_record_status . "',
`supplier_ID`				='" . $po_sup_ID . "',
`created_by`				='" . $po_created_by . "',
`date_needed`				='" . $po_date_needed . "',
`date_delivered`			='" . $po_date_delivered . "',
`approval_status`			='" . $po_approval_status . "',
`payment_status`			='" . $po_payment_status . "',
`completion_status`			='" . $po_completion_status . "',
`remark`					='" . $po_remarks . "',
`approved_by`				='" . $po_approved_by . "',
`approval_date`				='" . $po_date_approved . "',
`include_CoC`				='" . $po_include_CoC . "',
`date_confirmed`			='" . $po_date_confirmed . "',
`ship_via`					='" . $po_ship_via . "',
`special_reqs`				='" . $po_special_reqs . "',
`related_standards`			='" . $po_related_standards . "',
`special_contracts`			='" . $po_special_contracts . "',
`qualification_personnel`	='" . $po_qualification_personnel . "',
`QMS_reqs`					='" . $po_QMS_reqs . "',
`local_location_ID`			='" . $po_local_location_ID . "',
`HQ_location_ID`			='" . $po_HQ_location_ID . "',
`ship_to_location_ID`		='" . $po_ship_to_location_ID . "',
`default_currency`			='" . $po_currency_id . "',
`default_currency_rate`		='" . $po_default_currency_rate . "',
`tax_rate`					='" . $po_tax_rate . "'
WHERE `ID` 					= '". $po_id . "' ";


// echo '<h1>SQL QUERY: ' . $edit_purchaseorder_SQL . '</h1>';


if (mysqli_query($con, $edit_purchaseorder_SQL)) {

	$record_id = $po_id; // FOR AN EDIT PAGE, WE NEED THE EXISTING ID NUMBER

	// echo "UPDATE # " . $record_id . " OK";

		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'purchase_orders','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database
				
				// now work out where to send them:
				
				if ($_REQUEST['next_step'] == 'view_record') {
				
					// regular add - send them to the revisions list for that part
					header("Location: purchase_order_view.php?po_number=".$po_number."&msg=OK&action=edit&id=".$record_id);
					exit();
				}
				else {
					// regular add - send them to the revisions list for that part
					header("Location: purchase_orders.php?po_number=".$po_number."&msg=OK&action=edit&id=".$record_id);
					exit();
				}

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing purchase order with SQL: <br />" . $edit_purchaseorder_SQL . "</h4>";
}


?>
