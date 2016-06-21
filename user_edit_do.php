<meta content="text/html; charset=utf-8" http-equiv="content-type" /><?php 
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
$id = $_REQUEST['user_id'];
$user_fn = $_REQUEST['fn_text'];
$user_mn = $_REQUEST['mn_text'];
$user_ln = $_REQUEST['ln_text'];
$user_cn = $_REQUEST['cn_text'];
$user_email = $_REQUEST['email_text'];
$user_pwd = _base64_encrypt($_REQUEST['pwd_text']);
$user_level = $_REQUEST['level_text'];
$user_pos = $_REQUEST['pos_text'];

$update_note = "Editing a user to the system.";

$edit_user_SQL = "UPDATE `users` SET `first_name` = '".$user_fn."',`middle_name` = '".$user_mn."',`last_name` = '".$user_ln."',`name_CN` = '".$user_cn."',`email` = '".$user_email."',`password` = '".$user_pwd."',`user_level` = '".$user_level."',`position` = '".$user_pos."' WHERE `ID` = '".$id."' ";
    
// echo $edit_user_SQL;

if (mysqli_query($con, $edit_user_SQL)) {
	
	$record_id = mysqli_insert_id($con);
	
	// echo "UPDATE # " . $record_id . " OK";
	
		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'users','" . $record_id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
				
				// regular add - send them to the revisions list for that part	
				header("Location: users.php?msg=UPDATEOK&action=edit&edit_record_id=".$record_id."");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $edit_user_SQL . "</h4>";
}

function _base64_encrypt($str,$passw=null){
    $r='';
    $md=$passw?substr(md5($passw),0,16):'';
    $str=base64_encode($md.$str);
    $abc='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $a=str_split('+/='.$abc);
    $b=strrev('-_='.$abc);
    if($passw){
        $b=_mixing_passw($b,$passw);
    }else{
        $r=rand(10,65);
        $b=mb_substr($b,$r).mb_substr($b,0,$r);
    }
    $s='';
    $b=str_split($b);
    $str=str_split($str);
    $lens=count($str);
    $lena=count($a);
    for($i=0;$i<$lens;$i++){
        for($j=0;$j<$lena;$j++){
            if($str[$i]==$a[$j]){
                $s.=$b[$j];
            }
        };
    };
    return $s.$r;
};

function _mixing_passw($b,$passw){
    $s='';
    $c=$b;
    $b=str_split($b);
    $passw=str_split(sha1($passw));
    $lenp=count($passw);
    $lenb=count($b);
    for($i=0;$i<$lenp;$i++){
        for($j=0;$j<$lenb;$j++){
            if($passw[$i]==$b[$j]){
                $c=str_replace($b[$j],'',$c);
                if(!preg_match('/'.$b[$j].'/',$s)){
                    $s.=$b[$j];
                }
            }
        };
    };
    return $c.''.$s;
};

?>