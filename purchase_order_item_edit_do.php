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

$update_note = "Editing a purchase order item in the system.";

// DEVELOPMENT AREA:

// DEBUG

// foreach ($_REQUEST as $key => $value)
//  echo "$po_".htmlspecialchars($key)." = ".htmlspecialchars($value)."<br>";
 
 
// echo "$po_".htmlspecialchars($key)." = $_REQUEST['".htmlspecialchars($key)."'];";
 
// $po_remarks = nl2br(htmlentities($_REQUEST['remarks'], ENT_QUOTES, 'UTF-8'));
 
// $record_id 					= checkaddslashes($_REQUEST['id']);									// WRONG! THIS IS SOMEHOW THE PO NUMBER!!!
$record_id						= checkaddslashes($_REQUEST['PO_line_item_ID']);
$po_id 							= checkaddslashes($_REQUEST['PO_ID']);								//
$part_rev_ID 					= checkaddslashes($_REQUEST['part_rev_ID']);						//
$item_qty 						= checkaddslashes($_REQUEST['amount']);								//
// $po_remarks 					= checkaddslashes(nl2br(htmlentities($_REQUEST['remarks'], ENT_QUOTES, 'UTF-8')));	//
$po_remarks 					= checkaddslashes(str_replace("<br />", "", nl2br($_REQUEST['remarks'])));		//
// $po_remarks 					= str_replace("\n", "<br />", $_REQUEST['remarks']);				//
$po_record_status 				= checkaddslashes($_REQUEST['record_status']);						//
$po_default_currency_rate 		= checkaddslashes($_REQUEST['po_item_currency_rate']);				//				
$po_currency_id					= checkaddslashes($_REQUEST['currency_id']);						//
$po_item_unit_price_currency 	= checkaddslashes($_REQUEST['po_item_unit_price_currency']);		//				

// now calculate the USD amount:
if ($po_currency_id == 1) { // USD SELECTED
	$unit_price_USD = $po_item_unit_price_currency;
}
else {
	$unit_price_USD = ($po_item_unit_price_currency / $po_default_currency_rate);
}

$edit_purchase_order_item_SQL = "UPDATE `purchase_order_items` SET
`purchase_order_ID`='" . $po_id . "',
`part_revision_ID`='" . $part_rev_ID . "',
`part_qty`='" . $item_qty . "',
`record_status`='" . $po_record_status . "',
`item_notes`='" . $po_remarks . "',
`unit_price_USD`='" . $unit_price_USD . "',
`unit_price_currency`='" . $po_item_unit_price_currency . "',
`original_currency`='" . $po_currency_id . "',
`original_rate`='" . $po_default_currency_rate . "' 
WHERE `ID` = '" . $record_id . "'";

// echo '<h1>SQL QUERY: ' . $edit_purchase_order_item_SQL . '</h1>';


if (mysqli_query($con, $edit_purchase_order_item_SQL)) {

	// echo "UPDATE # " . $record_id . " OK";

		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'purchase_order_items','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database
				
				// now work out where to send them:
				
				if ($_REQUEST['next_step'] == 'view_record') {
				
					// regular add - send them to the revisions list for that part
					header("Location: purchase_order_view.php?line_item_id=".$record_id."&msg=OK&action=edit&id=".$po_id);
					exit();
				}
				else {
					// regular add - send them to the revisions list for that part
					header("Location: purchase_order_items.php?po_id=".$po_id."&msg=OK&action=edit&id=".$record_id);
					exit();
				}

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing purchase order with SQL: <br />" . $edit_purchase_order_item_SQL . "</h4>";
}

?>
