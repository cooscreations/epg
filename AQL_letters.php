<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */     session_start ();     /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
//   now check the user is OK to view this page  //
/* /////// require ('page_access.php');  /*/////*/
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////

header ( 'Content-Type: text/html; charset=utf-8' );
require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

// pull the header and template stuff:
pagehead ();
?>

    <!-- start: page -->
    
    
    <?php add_button(0, 'AQL_letter_add'); ?>
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed mb-none">
          <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">AQL Code</th>
                <th class="text-center">Min.</th>
                <th class="text-center">Max.</th>
                <th class="text-center">Result</th>
                <th class="text-center"><i class="fa fa-cogs"></i></th>
            </tr>
          </thead>
		  <tbody>
            <?php
                          $get_con_SQL = "SELECT * FROM  `AQL_letter` ORDER BY `ID` ASC";
                          // echo $get_con_SQL;

								$con_count = 0;

								$result_get_cons = mysqli_query ( $con, $get_con_SQL );
								// while loop
								while ( $row_get_cons = mysqli_fetch_array ( $result_get_cons ) ) {
										
									$AQL_letter_ID 					= $row_get_cons['ID'];
									$AQL_letter_AQL_code 			= $row_get_cons['AQL_code'];
									$AQL_letter_order_qty_min 		= $row_get_cons['order_qty_min'];
									$AQL_letter_order_qty_max 		= $row_get_cons['order_qty_max'];
									$AQL_letter_AQL_letter_result 	= $row_get_cons['AQL_letter_result'];
									$AQL_letter_status 				= $row_get_cons['status'];

									?>

            <tr>
                <td class="text-center"><?php echo $AQL_letter_ID;?></td>
                <td class="text-center"><?php echo $AQL_letter_AQL_code;?></td>
                <td class="text-center"><?php echo number_format($AQL_letter_order_qty_min, 0);?></td>
                <td class="text-center"><?php echo number_format($AQL_letter_order_qty_max, 0);?></td>
                <td class="text-center"><?php echo $AQL_letter_AQL_letter_result; ?></td>
                <td class="text-center">
                	<a class="btn btn-default" href="AQL_letter_edit.php?id=<?php echo $AQL_letter_ID;?>">&nbsp;<i class="fa fa-cog"></i>&nbsp;</a>
                </td>
            </tr>

            <?php

									$con_count = $con_count + 1;
								} // end while loop
								?>
			
          
          <tfoot>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">AQL Code</th>
                <th class="text-center">Min.</th>
                <th class="text-center">Max.</th>
                <th class="text-center">Result</th>
                <th class="text-center"><i class="fa fa-cogs"></i></th>
            </tr>
          
            <tr>
                <th colspan="6">TOTAL: <?php echo $con_count; ?></th>
            </tr>
          </tfoot>
            </tbody>
        </table>
    </div>
    
    <?php add_button(0, 'AQL_letter_add'); ?>
    
    <!-- end: page -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
