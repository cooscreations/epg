<?php

/*


THIS IS AN AWESOME ADMIN FORM TO CONVERT ALL BATCH MOVEMENT (FIRST INCOMING) RECORDS INTO PURCHASE ORDER ITEMS FOR LEGACY PURPOSES.

*/


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

/*

header('Content-Type: text/html; charset=utf-8');
require ('page_functions.php');
include 'db_conn.php';

// echo "<h1>HERE WE GO:</h1>";
$get_existing_batch_info_SQL = "SELECT `part_batch`.`ID` AS `part_batch_ID`, `part_batch`.`PO_ID`, `part_batch`.`part_ID`, `part_batch`.`batch_number`, `part_batch`.`part_rev`, `part_batch`.`supplier_ID`, `part_batch`.`record_status` AS `part_batch_record_status`, `part_batch_movement`.`ID` AS `movement_ID`, `part_batch_movement`.`part_batch_ID`, `part_batch_movement`.`amount_in`, `part_batch_movement`.`amount_out`, `part_batch_movement`.`part_batch_status_ID`, `part_batch_movement`.`remarks`, `part_batch_movement`.`user_ID`, `part_batch_movement`.`date`, `part_batch_movement`.`record_status` FROM `part_batch` JOIN `part_batch_movement` ON `part_batch_movement`.`part_batch_ID`= `part_batch`.`ID` WHERE `part_batch_movement`.`amount_in` > 0 AND `part_batch_movement`.`record_status` = 2 AND `part_batch`.`record_status` = 2 GROUP BY `part_batch_movement`.`part_batch_ID` ORDER BY `part_batch_movement`.`date` ASC";
// echo $get_existing_batch_info_SQL; 

$result_get_existing_batch_info = mysqli_query($con,$get_existing_batch_info_SQL);
	// while loop
	while($row_get_existing_batch_info = mysqli_fetch_array($result_get_existing_batch_info)) {

		// now print each result to a variable:	
		$PO_ID 			= $row_get_existing_batch_info['PO_ID'];
		$part_rev 		= $row_get_existing_batch_info['part_rev'];
		$amount_in 		= $row_get_existing_batch_info['amount_in'];
		$remarks 		= $row_get_existing_batch_info['remarks'];


		$insert_PO_item_SQL = "INSERT INTO `purchase_order_items`(`ID`, `purchase_order_ID`, `part_revision_ID`, `part_qty`, `record_status`, `item_notes`, `unit_price_USD`, `unit_price_currency`, `original_currency`, `original_rate`) VALUES (NULL,'" . $PO_ID . "','" . $part_rev . "','" . $amount_in . "','2','" . $remarks . "','0.1500','1.0000','2','6.6700');";

	echo $insert_PO_item_SQL;
	echo "<br />";

}

// echo '<h1>BOOYAHHHHHH</h1>';

*/

echo "I'm not sure how you found this page, but you'll need to edit it directly to understand what it does.";

?>
