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

$po_line_item_PO_ID 				= checkaddslashes($_REQUEST['PO_ID']);
$po_line_item_part_revision_ID 		= checkaddslashes($_REQUEST['part_rev_ID']);
$po_line_item_part_qty 				= checkaddslashes($_REQUEST['amount']);
$po_line_item_record_status 		= checkaddslashes($_REQUEST['record_status']);
$po_line_item_item_notes 			= checkaddslashes(str_replace("<br />", "", nl2br($_REQUEST['remarks'])));
$po_line_item_unit_price_currency 	= checkaddslashes($_REQUEST['po_item_unit_price_currency']);
$po_line_item_original_currency 	= checkaddslashes($_REQUEST['currency_id']);
$po_line_item_original_rate 		= checkaddslashes($_REQUEST['po_item_currency_rate']);


// calcualte USD amount:
if ($po_line_item_original_currency == 1) {
	$po_line_item_unit_price_USD = $po_line_item_unit_price_currency;
}
else {
	$po_line_item_unit_price_USD = ($po_line_item_unit_price_currency / $po_line_item_original_rate);
}

$update_note = "Adding a new line item in to Purchase Order ID# " . $po_line_item_PO_ID . ".";

$add_SQL = "INSERT INTO `purchase_order_items`(`ID`, `purchase_order_ID`, `part_revision_ID`, `part_qty`, `record_status`, `item_notes`, `unit_price_USD`, `unit_price_currency`, `original_currency`, `original_rate`) VALUES (NULL,'".$po_line_item_PO_ID."','".$po_line_item_part_revision_ID."','".$po_line_item_part_qty."','".$po_line_item_record_status."','".$po_line_item_item_notes."','".$po_line_item_unit_price_USD."','".$po_line_item_unit_price_currency."','".$po_line_item_original_currency."','".$po_line_item_original_rate."')";


// echo $add_supplier_SQL;

if (mysqli_query($con, $add_SQL)) {

	$record_id = mysqli_insert_id($con);

	// echo "INSERT # " . $record_id . " OK";

		// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'purchase_order_items','" . $record_id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
            header("Location: purchase_order_view.php?id=".$po_line_item_PO_ID."&msg=OK&action=add_line_item");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to add new P.O. item with SQL: <br />" . $add_SQL . "</h4>";
}

?>
