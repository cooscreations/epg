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

// $id 					= checkaddslashes($_REQUEST['id']); 					// NOT USED!
$user_fn 				= checkaddslashes($_REQUEST['fn_text']);
$user_mn 				= checkaddslashes($_REQUEST['mn_text']);
$user_ln 				= checkaddslashes($_REQUEST['ln_text']);
$user_cn 				= checkaddslashes($_REQUEST['cn_text']);
$user_email 			= checkaddslashes($_REQUEST['email_text']);
$user_pwd 				= md5($_REQUEST['pwd_text']);
$user_level 			= checkaddslashes($_REQUEST['level_text']);
$user_pos 				= checkaddslashes($_REQUEST['pos_text']);
$user_mobile_number 	= checkaddslashes($_REQUEST['mobile_number']);
$user_facebook 			= checkaddslashes($_REQUEST['facebook']);
$user_linkedin 			= checkaddslashes($_REQUEST['linkedin']);
$user_twitter 			= checkaddslashes($_REQUEST['twitter']);
$user_wechat 			= checkaddslashes($_REQUEST['wechat']);
$user_skype 			= checkaddslashes($_REQUEST['skype']);
if($_REQUEST['cn_text'] == ''){
	$user_cn 			= '中文名';
}
else{
	$user_cn 			= checkaddslashes($_REQUEST['cn_text']);
}



$update_note = "Adding a new user to the system.";

$add_user_SQL = "INSERT INTO `users`(`ID`, `first_name`, `middle_name`, `last_name`, `name_CN`, `email`, `password`, `user_level`, `position`, `last_login_date`, `facebook_profile`, `twitter_profile`, `linkedin_profile`, `skype_profile`, `wechat_profile`, `record_status`, `mobile_number`) VALUES (NULL,'".$user_fn."','".$user_mn."','".$user_ln."','".$user_cn."','".$user_email."','".$user_pwd."','".$user_level."','".$user_pos."','0000-00-00 00:00:00','" . $user_facebook . "','" . $user_twitter . "','" . $user_linkedin . "','" . $user_skype . "','" . $user_wechat . "','2','" . $user_mobile_number . "')";

// echo $add_movement_SQL;

if (mysqli_query($con, $add_user_SQL)) {

	$record_id = mysqli_insert_id($con);

	// echo "INSERT # " . $record_id . " OK";

		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'users','" . $record_id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
				header("Location: users.php?msg=OK&action=add&new_record_id=".$record_id."");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $add_user_SQL . "</h4>";
}



?>
