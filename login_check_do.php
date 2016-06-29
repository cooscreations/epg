
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



// username and password sent from form
$username = $_REQUEST['username'];
$pwd = $_REQUEST['pwd'];


// To protect MySQL injection (more detail about MySQL injection)
$username = stripslashes($username);
$pwd = stripslashes($pwd);



//encrypted password
$pwd = md5($pwd);

$sql = "SELECT * FROM `users` WHERE email='$username' and password='$pwd'";

$result = mysqli_query ( $con, $sql );

// while loop
while($row_get_user = mysqli_fetch_array($result)) {
	$user_ID = $row_get_user['ID'];
	$user_fn = $row_get_user['first_name'];
	$user_mn = $row_get_user['middle_name'];
	$user_ln = $row_get_user['last_name'];
	$user_name_cn = $row_get_user['name_CN'];
	$user_email = $row_get_user['email'];
	//$user_pwd = _base64_decrypt($row_get_user['password']); May not need this. Why woud we display the password plain text ?
	$user_level = $row_get_user['user_level'];
	$user_position = $row_get_user['position'];
	$user_last_login_date = $row_get_user['last_login_date'];
	$user_facebook = $row_get_user['facebook_profile'];	
	$user_linkedin = $row_get_user['linkedin_profile'];	
	$user_twitter = $row_get_user['twitter_profile'];	
	$user_wechat = $row_get_user['wechat_profile'];	
	$user_skype = $row_get_user['skype_profile'];	
	$user_mobile_number = $row_get_user['mobile_number'];
	
} // end get user info WHILE loop


$count = $result->num_rows;

if ($count == 1) {
	// update session.
	$_SESSION['username']= $username;
	$_SESSION['user_level']= $user_level;
	$_SESSION['user_ID']= $user_ID;
	
	//Update last login date in users table.
	$update_last_login_SQL = "UPDATE `users` SET `last_login_date` = SYSDATE() WHERE email='$username' ";
	
	if (mysqli_query($con, $update_last_login_SQL)) {
		header ( "Location: index.php" );
	}
	
} else {
	//Show error.
	header("Location: login.php?msg=NG&error=invalid_login");
}
?>