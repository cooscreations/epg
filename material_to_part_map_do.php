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

NOTE: This is for the multi-dimensional mapping of multiple materials to multiple part revisions!!!

*/


if (!isset($_REQUEST['id'])){
	header("Location: parts.php?msg=NG&action=view&error=no_id");
	exit();
}
					
$all_good 	= 1; // default overall state... (?)

$part_ID 	= checkaddslashes($_REQUEST['part_id']);
$rev_ID 	= checkaddslashes($_REQUEST['id']);

// echo '<h2>ACTION REQUIRED (not displaying ignored fields):</h2>';
// echo '<ol>';
		
		
	// IN ORDER TO CHECK FOR ALL FORM ENTRIES, WE WILL RUN SIMILAR CODE TO THE PART_TO_MATERIAL_MAP PAGE
	// IT'S NOT ENOUGH JUST TO CAPTURE THE 'ON' CHECKBOXES, BECAUSE WE WOULD WANT TO TURN OFF ANY UNCHECKED BOXES THAT EXISTED BEFORE!

	// 1. get all revisions for a given part:
	// 2. loop through each material (2.1) and see if there is a map already present (2.2)
	// 3. now check for form data (will only show ON, otherwise it's OFF)
		// 4. if no present mapping and form data is ON, INSERT into DB
		// 5. if map is present and published but form data is not, the user turned this OFF - UPDATE
		// 6. if map is present but UNPUBLISHED and form data is present, user has now turned this ON - UPDATE
		// 7. if no present mapping and no form data, NOTHING TO DO
		// 8. if map present and published and form data is ON, NOTHING TO DO


	// 1. get all revisions for a given part:
	$get_all_revs_SQL = "SELECT * FROM `part_revisions` WHERE `part_ID` = '" . $part_ID . "' AND `record_status` = '2' ORDER BY `revision_number` DESC";
	$result_get_all_revs = mysqli_query($con,$get_all_revs_SQL);
	$rev_count = 0;
	$push_HTML = array(); 		// BLANK EMPTY ARRAY
	$rev_list_array = array(); 	// BLANK EMPTY ARRAY
	// while loop
	while($row_get_all_revs = mysqli_fetch_array($result_get_all_revs)) {

		// now print each result to a variable:
		$this_rev_ID 					= $row_get_all_revs['ID'];
		$this_part_ID 					= $row_get_all_revs['part_ID'];
		$this_revision_number 			= $row_get_all_revs['revision_number'];
		$this_rev_remarks 				= $row_get_all_revs['remarks'];
		$this_rev_date_approved 		= $row_get_all_revs['date_approved'];
		$this_rev_user_ID 				= $row_get_all_revs['user_ID'];
		$this_rev_price_USD 			= $row_get_all_revs['price_USD'];
		$this_rev_weight_g 				= $row_get_all_revs['weight_g'];
		$this_rev_status_ID 			= $row_get_all_revs['status_ID'];
		$this_rev_material_ID 			= $row_get_all_revs['material_ID'];
		$this_rev_treatment_ID 			= $row_get_all_revs['treatment_ID'];
		$this_rev_treatment_notes		= $row_get_all_revs['treatment_notes'];
		$this_rev_record_status 		= $row_get_all_revs['record_status'];
	
		$rev_count = $rev_count + 1;
	
		// $rev_list_array[$this_rev_ID];
		// $push_HTML[$this_rev_ID] = $this_rev_ID;
	
		// 2.1 loop through each material (2.1) and see if there is a map already present (2.2)
		$get_mats_SQL = "SELECT * FROM `material` WHERE `record_status` = '2' ORDER BY `name_EN` ASC";
		// echo $get_mats_SQL;

		$mat_count = 0;

		$result_get_mats = mysqli_query ( $con, $get_mats_SQL );
		// while loop
		while ( $row_get_mats = mysqli_fetch_array ( $result_get_mats ) ) {

			$this_mat_id 			= $row_get_mats['ID'];
			$this_mat_name_EN 		= $row_get_mats['name_EN'];
			$this_mat_name_CN 		= $row_get_mats['name_CN'];
			$this_mat_description 	= $row_get_mats['description'];
			$this_mat_record_status	= $row_get_mats['record_status'];
			$this_mat_wiki_URL 		= $row_get_mats['wiki_URL'];
		
			// 2.2 loop through each material (2.1) and see if there is a map already present (2.2)
			$get_existing_map_SQL = "SELECT * FROM `part_to_material_map` WHERE `part_rev_ID` = '" . $this_rev_ID . "' AND `material_ID` = '" . $this_mat_id . "'";

			$existing_map_count = 0;

			$result_get_existing_map = mysqli_query ( $con, $get_existing_map_SQL );
			// while loop
			while ( $row_get_existing_map = mysqli_fetch_array ( $result_get_existing_map ) ) {

				$this_existing_map_ID 				= $row_get_existing_map['ID'];
				$this_existing_map_part_rev_ID 		= $row_get_existing_map['part_rev_ID'];
				$this_existing_map_material_ID 		= $row_get_existing_map['material_ID'];
				$this_existing_map_variant_ID 		= $row_get_existing_map['variant_ID'];
				$this_existing_map_record_status 	= $row_get_existing_map['record_status']; // SHOULD BE 2

				$existing_map_count = $existing_map_count + 1;

			} // end get existing
		
			// 3. now check for form data (will only show ON, otherwise it's OFF)
			if (isset($_REQUEST['mat_' . $this_mat_id . '_rev_' . $this_rev_ID . ''])){
				$this_map_value = 1; // USER WANTS IT ON, REGARDLESS OF WHAT THE DB SAYS!
				// echo ' FORM VAR FOUND: (mat_' . $this_mat_id . '_rev_' . $this_rev_ID . ') Value = "' . $_REQUEST['mat_' . $this_mat_id . '_rev_' . $this_rev_ID . ''] . '"<br />';
				// echo 'EXISTING MAPPING = ' . $existing_map_count . '.<br /><br />';
			}
			else {
				$this_map_value = 0; // USER WANTS IT OFF, REGARDLESS OF WHAT THE DB SAYS!
			}
		
		
			// ****************************************************************** //
			// ****************************************************************** //
			// ************* NOW MAKE THE CHECKS AND UPDATES! ******************* //
			// ****************************************************************** //
			// ****************************************************************** //
			// ****************************************************************** //
			if ( ( $existing_map_count == 0 ) && ( $this_map_value == 1 ) ) {
				// 4. if no present mapping and form data is ON, INSERT into DB
				$action = 'INSERT';
				$new_record_status = 2;
			}
		
			else if ( ( $existing_map_count == 1 ) && ( $this_existing_map_record_status == 2 ) && ( $this_map_value == 0 ) ) {
				// 5. if map is present and published but form data is not, the user turned this OFF - UPDATE
				$action = 'UPDATE';
				$new_record_status = 0;
			}
		
			else if ( ( $existing_map_count == 1 ) && ( $this_existing_map_record_status != 2 ) && ( $this_map_value == 1 ) ) {
				// 6. if map is present but UNPUBLISHED and form data is present, user has now turned this ON - UPDATE
				$action = 'UPDATE';
				$new_record_status = 2;
			}
		
			else if ( ( $existing_map_count == 0 ) && ( $this_map_value == 0 ) ) {
				// 7. if no present mapping and no form data, NOTHING TO DO
				$action = '';
				$new_record_status = 0;
			}
		
			else if ( ( $this_map_value == 1 ) && ( $this_existing_map_record_status == 2 ) && ( $existing_map_count == 1 ) ) {
				// 8. if map present and form data is ON, NOTHING TO DO
				$action = '';
				$new_record_status = 2;
			}
		
			// ****************************************************************** //
			// ****************************************************************** //
			// *********************    NOW TAKE ACTION!    ********************* //
			// ****************************************************************** //
			// ****************************************************************** //
			// ****************************************************************** //
		
		
			if ($action != '') {
				
					// we have something to do!
				
					if ($action == 'INSERT') {
						// add a new record to the DB
						$update_note = "Adding a part-to-material map table record.";
						$action_SQL= "INSERT INTO `part_to_material_map`(`ID`, `part_rev_ID`, `material_ID`, `variant_ID`, `record_status`) VALUES (NULL,'" . $this_rev_ID . "','" . $this_mat_id . "','0','2')";
						$fail_note = '<h4>Failed to create a new part-to-material map with SQL: <br />' . $action_SQL . '</h4>';
					}
				
					else if ($action == 'UPDATE') {
						// update an existing record!
						$update_note = "Editing an existing part-to-material map table record.";
						$action_SQL = "UPDATE `part_to_material_map` SET `record_status`='" . $new_record_status . "' WHERE `ID`='" . $this_existing_map_ID . "'";
						$fail_note = '<h4>Failed to update existing part-to-material map (# ' . $this_existing_map_ID . ') with SQL: <br />' . $action_SQL . '</h4>';
					}

					// echo "<li>SQL: <strong>" . $action_SQL . "</strong></li>";
				
				
				
					//////////////////////////////////////////////////////////
					//////              NOW RUN THE SQL!                 /////
					//////////////////////////////////////////////////////////
					
					if (mysqli_query($con, $action_SQL)) {
						if ($action == 'INSERT') {
							$record_id = mysqli_insert_id($con);
						}
						else {
							$record_id = $this_existing_map_ID;
						}
					
						// update was successful!
						$all_good = 1;
						
						// echo '<h4>Success with SQL: ' . $action_SQL . '</h4>';
						
						// now record the change:
						$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_to_material_map','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', '" . $action . "')";
						// echo $record_edit_SQL;
						if (mysqli_query($con, $record_edit_SQL)) {
							$all_good = 1; // still good to continue
							// echo '<h4>Success with SQL: ' . $record_edit_SQL . '</h4>'
						}
						else {
							$all_good = 0; // error!
							echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
						}
					}
					else {
						$all_good = 0;
						echo $fail_note;
					}
					
				
			} // end of 'ACTION FOUND'
	
	
		} // end get materials
	
	} // end get revisions loop


// echo '</ol>';

// FINALLY, LET'S CHECK WHERE TO SEND PEOPLE NEXT:
if ($_REQUEST['next_step'] == 'view_record') {
	// regular add - send them to the updated record
	header("Location: part_view.php?msg=OK&action=part_mat_map&id=".$part_ID."&rev_id=".$rev_ID."");
	exit();
}
else {
	// send them to the list of users
	header("Location: parts.php?msg=OK&action=part_mat_map&id=".$part_ID."&rev_id=".$rev_ID."");
	exit();
}
// echo 'EOF';
?>
