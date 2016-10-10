<?php
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

$page_id = 22;

// pull the header and template stuff:
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Blank Page</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                <li><a href="#">Blank Page</a></li>
                <li><span>This Page</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i
                class="fa fa-chevron-left"></i></a>
        </div>
    </header>
    <?php

			// run notifications function:
			$msg = 0;
			if (isset ( $_REQUEST ['msg'] )) {
				$msg = $_REQUEST ['msg'];
			}
			$action = 0;
			if (isset ( $_REQUEST ['action'] )) {
				$action = $_REQUEST ['action'];
			}
			$change_record_id = 0;
			if (isset ( $_REQUEST ['new_record_id'] )) {
				$change_record_id = $_REQUEST ['new_record_id'];
			}
			$page_record_id = 0;
			if (isset ( $record_id )) {
				$page_record_id = $record_id;
			}

			// now run the function:
			notify_me ( $page_id, $msg, $action, $change_record_id, $page_record_id );
	?>

    <!-- start: page -->
    
    
    <?php add_button(0, 'country_add'); ?>
    
    <div class="table-responsive">
        <table
            class="table table-bordered table-striped table-condensed mb-none">
            <tr>
                <th class="text-center">Flag</th>
                <th class="text-center">Name</th>
                <th class="text-center">名字</th>
                <th class="text-center">Alpha 2</th>
                <th class="text-center">Alpha 3</th>
                <th class="text-center">ISO Code</th>
                <th class="text-center"># Suppliers</th>
                <th class="text-center">Actions</th>
            </tr>

            <?php
                          $get_con_SQL = "SELECT * FROM  `countries` WHERE `record_status` = '2' ORDER BY `countries`.`name_EN` ASC";
                          // echo $get_con_SQL;

								$con_count = 0;

								$result_get_cons = mysqli_query ( $con, $get_con_SQL );
								// while loop
								while ( $row_get_cons = mysqli_fetch_array ( $result_get_cons ) ) {
								
									$country_ID 			= $row_get_cons['ID'];
									$country_name_EN 		= $row_get_cons['name_EN'];
									$country_name_CN 		= $row_get_cons['name_CN'];
									$country_code 			= $row_get_cons['code'];
									$country_record_status 	= $row_get_cons['record_status'];
									$country_alpha_3 		= $row_get_cons['alpha_3'];
									$country_ISO_code		= $row_get_cons['ISO_code'];

									?>

            <tr>
            	<td class="text-center"><?php 
            	
            		$find_file = "assets/images/flags/" . strtolower($country_code) . ".png";
            	
            		if (file_exists($find_file)) {
    					?><img src="<?php echo $find_file; ?>" title="<?php 
            		echo $country_name_EN; 
            		if (($country_name_CN!='')&&($country_name_CN!='中文名')) { 
            			echo $country_name_CN . '的国旗'; 
            		} ?> Flag" /><?php
					} 
					else { 
						?><span class="btn btn-xs btn-warning" title="FAILED TO LOCATE THE FOLLOWING FILE: <?php echo $find_file; ?>"><i class="fa fa-info"></i></span><?
					} ?></td>
                <td><a
					href="country_view.php?id=<?php echo $country_ID; ?>"><?php echo $country_name_EN; ?></a></td>
                <td><a
					href="country_view.php?id=<?php echo $country_ID; ?>"><?php if (($country_name_CN!='')&&($country_name_CN!='中文名')) { echo $country_name_CN; } else { echo '<span class="text-danger">没有中文名</span>'; } ?></a></td>
                <td class="text-center"><?php echo $country_code; ?></td>
                <td class="text-center"><?php echo $country_alpha_3; ?></td>
                <td class="text-center"><?php echo $country_ISO_code; ?></td>
                <td class="text-center"><?php
                
                // count suppliers for this country:
				$count_sup_sql 		= "SELECT COUNT( ID ) FROM  `suppliers` WHERE  `country_ID` = " . $country_ID;
				$count_sup_query 	= mysqli_query($con, $count_sup_sql);
				$count_sup_row 		= mysqli_fetch_row($count_sup_query);
				$total_suppliers 			= $count_sup_row[0];
                
                // make a 0 red!
                $start_span = '<span class="text-success">';
                $end_span = '</span>';
                if ($total_suppliers == 0) {
                	$start_span = '<span class="text-danger">';
                	$end_span = '</span>';
                }
                
                echo $start_span;
                // now show the number!
                echo $total_suppliers;
                echo $end_span;
                
                // reset:
                $total_suppliers = 0;
                
                ?></td>
                <td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="country_edit.php?id=<?php echo $country_ID; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
                    <a href="record_delete_do.php?table_name=countries&src_page=countries.php&id=<?php echo $country_ID; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
            </tr>

            <?php

									$con_count = $con_count + 1;
								} // end while loop
								?>

            <tr>
                <th colspan="8">TOTAL: <?php echo $con_count; ?></th>
            </tr>
        </table>
    </div>
    
    <?php add_button(0, 'country_add'); ?>
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>