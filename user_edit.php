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

header('Content-Type: text/html; charset=utf-8');
echo "sometthing1";
require ('page_functions.php');
echo "sometthing2";
include 'db_conn.php';
echo "sometthing3";

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}
echo $_REQUEST['id'];
$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: users.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	$get_user_SQL = "SELECT `ID`, `first_name`, `middle_name`, `last_name`, `name_CN`, `email`, `password`, `user_level`, `position`, `last_login_date` ,`mobile_number` FROM `users` WHERE `ID` = " . $record_id;
	echo $get_user_SQL;
	$result_get_user = mysqli_query($con,$get_user_SQL);

    // while loop
    while($row_get_user = mysqli_fetch_array($result_get_user)) {
        $user_ID = $row_get_user['ID'];
        $user_fn = $row_get_user['first_name'];
        $user_mn = $row_get_user['middle_name'];
        $user_ln = $row_get_user['last_name'];
        $user_cn = $row_get_user['name_CN'];
        $user_email = $row_get_user['email'];
        $user_pwd = _base64_decrypt($row_get_user['password']);
        $user_level = $row_get_user['user_level'];
        $user_pos = $row_get_user['position'];
        $user_last_login_date = $row_get_user['last_login_date'];
        $user_mobile_number = $row_get_user['mobile_number'];
        echo $user_mobile_number;
    } // end get part info WHILE loop
}

function _base64_decrypt($str,$passw=null){
    $abc='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $a=str_split('+/='.$abc);
    $b=strrev('-_='.$abc);
    if($passw){
        $b=_mixing_passw($b,$passw);
    }else{
        $r=mb_substr($str,-2);
        $str=mb_substr($str,0,-2);
        $b=mb_substr($b,$r).mb_substr($b,0,$r);
    }
    $s='';
    $b=str_split($b);
    $str=str_split($str);
    $lens=count($str);
    $lenb=count($b);
    for($i=0;$i<$lens;$i++){
        for($j=0;$j<$lenb;$j++){
            if($str[$i]==$b[$j]){
                $s.=$a[$j];
            }
        };
    };
    $s=base64_decode($s);
    if($passw&&substr($s,0,16)==substr(md5($passw),0,16)){
        return substr($s,16);
    }else{
        return $s;
    }
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
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Edit User - <?php echo $user_fn; ?> <?php echo $user_mn; ?> <?php echo $user_ln; ?> / <?php echo $user_name_cn; ?></h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="users.php">All Users</a></li>
                <li><span>Edit User</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="user_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit User Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="col-md-4 col-lg-3">
                            <section class="panel">
                                <div class="panel-body">
                                    <div class="thumb-info mb-md">
                                        <img src="assets/images/users/user_<?php echo $user_ID; ?>.png" title="<?php echo $user_fn; echo ' ' . $user_mn; echo ' ' . $user_ln; ?>" style="width:100px;" />
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-md-4 col-lg-9">
                            <div class="panel-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">First Name:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="fn_text" value="<?php echo $user_fn; ?>"/>
                                    <input type="hidden" name="user_id" value="<?php echo $user_ID; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Middle Name:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="mn_text" value="<?php echo $user_mn; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Last Name:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="ln_text" value="<?php echo $user_ln; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">名字:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="cn_text" value="<?php echo $user_cn; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">E-mail:</label>
                                <div class="col-md-5">
                                    <input type="email" class="form-control" id="inputDefault" name="email_text" value="<?php echo $user_email; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Password:</label>
                                <div class="col-md-5">
                                    <input type="password" class="form-control" id="inputDefault" name="pwd_text" value="<?php echo $user_pwd; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
								<label class="col-md-3 control-label">Mobile Number:<span class="required">*</span></label>
								<div class="col-md-5">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-phone"></i>
										</span>
										<input id="inputDefault" name="mobile_number" value="<?php echo $user_mobile_number; ?>" data-plugin-masked-input data-input-mask="(999) 999-9999" placeholder="(123) 123-1234" class="form-control" required />
									</div>
								</div>
							</div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Level:</label>
                                <div class="col-md-5">
                                    <input type="number" class="form-control" id="inputDefault" name="level_text" min="10" max="100" step="10" value="<?php echo $user_level; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Position:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="pos_text" value="<?php echo $user_pos; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                                </div>
                        </div>

                    </div>


                    <footer class="panel-footer">
                        <?php
										if (isset($_REQUEST['id'])) {
											?>
                        <input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="user_id" />
                        <?php
										}
										?>
                        <button type="submit" class="btn btn-success">Submit </button>
                        <button type="reset" class="btn btn-default">Reset</button>
                    </footer>
                </section>
                <!-- now close the form -->
            </form>
        </div>
    </div>
    <!-- now close the panel -->
    <!-- end row! -->

    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
