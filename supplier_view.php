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

header('Content-Type: text/html; charset=utf-8');
require ('page_functions.php'); 
include 'db_conn.php';

/* -- NO USER SESSIONS YET...
if (isset($_SESSION['user_id'])) {
	header("Location: user_home.php"); // send them to the user home...
}
*/

$page_id = 99;

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else { // no id = nothing to see here!	
	header("Location: suppliers.php?msg=NG&action=view&error=no_id");
	exit();		
}

// pull the header and template stuff:
pagehead($page_id); 

// now get the part info:
$get_sups_SQL = "SELECT * FROM `suppliers` WHERE `ID` = " . $record_id;
                                                                     // echo $get_sups_SQL;

$result_get_sups = mysqli_query($con,$get_sups_SQL);

// while loop
while($row_get_sup = mysqli_fetch_array($result_get_sups)) {
	$sup_ID = $row_get_sup['ID'];
	$sup_en = $row_get_sup['name_EN'];
	$sup_cn = $row_get_sup['name_CN'];
	$sup_web = $row_get_sup['website'];
	
} // end get user info WHILE loop

?>

<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Supplier Profile - <?php echo $sup_en; ?> / <?php echo $sup_cn; ?></h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="suppliers.php">All Suppliers</a></li>
                <li><span>Supplier Profile</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">
            <!-- Supplier JUMPER -->
            <select onchange="document.location = this.value" data-plugin-selecttwo class="form-control populate">
                <option value="#" selected="selected">JUMP TO ANOTHER SUPPLIER / ???:</option>
                <option value="suppliers.php">View All / ???</option>
                <?php 	
										
                $get_j_sups_SQL = "SELECT * FROM `suppliers`";
                // echo $get_j_sups_SQL;
	
                $result_get_j_sups = mysqli_query($con,$get_j_sups_SQL);
					  		// while loop
                while($row_get_j_sup = mysqli_fetch_array($result_get_j_sups)) {
					  		
                    $j_sup_ID = $row_get_j_sup['ID'];
                    $j_sup_en = $row_get_j_sup['name_EN'];
                    $j_sup_cn = $row_get_j_sup['name_CN'];
                    $j_sup_web = $row_get_j_sup['website'];
										
							   ?>
                <option value="supplier_view.php?id=<?php echo $j_sup_ID; ?>"><?php echo $j_sup_en; if (($j_sup_cn != '')&&($j_sup_cn != '???')) { ?> / <?php echo $j_sup_cn; } ?></option>
                <?php 
							  } // end get supplier list 
							  ?>
                <option value="suppliers.php">View All / ???</option>
            </select>
            <!-- / Supplier JUMPER -->
        </div>
    </div>
    
    <div class="clearfix">&nbsp;</div>

    <!-- START MAIN BODY COLUMN: -->
    <div class="col-md-12">
        <div class="row">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                </div>

                <h2 class="panel-title">Supplier Details:</h2>
            </header>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                        <tr>
                            <th>Name:</th>
                            <td><?php echo $sup_en; ?></td>
                        </tr>
                        <tr>
                            <th>名字:</th>
                            <td><?php echo $sup_cn; ?></td>
                        </tr>
                        <tr>
                            <th>Website:</th>
                            <td><a href="<?php echo $sup_web; ?>" target="_blank" title="Launch in a new window"><?php echo $sup_web; ?></a></td>
                        </tr>
                    </table>
                </div>

            </div>

        </div>

        <div class="clearfix">&nbsp;</div>
        <!-- END OF SUPPLIER PROFILE (numbered tab) -->
    </div>
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>