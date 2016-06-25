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
require ('page_functions.php'); 
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 7;

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Suppliers</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><span>Suppliers</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <?php 
    
    // run notifications function:
    $msg = 0;
    if (isset($_REQUEST['msg'])) { $msg = $_REQUEST['msg']; }
    $action = 0;
    if (isset($_REQUEST['action'])) { $action = $_REQUEST['action']; }
    $change_record_id = 0;
    if (isset($_REQUEST['new_record_id'])) { $change_record_id = $_REQUEST['new_record_id']; }
    $page_record_id = 0;
    if (isset($record_id)) { $page_record_id = $record_id; }
    
    // now run the function:
    notify_me($page_id, $msg, $action, $change_record_id, $page_record_id);
    ?>

    <!-- start: page -->

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed mb-none">
            <tr>
                <th><abbr title="(EPG Supplier ID, NOT Database Unique ID!)">ID</abbr></ht>
                <th>Name</th>
                <th>Status</th>
                <th>Type</th>
                <th>Product Type <br />(Notes)</th>
                <th>Cert.</th>
                <th>Expires</th>
                <th><i class="fa fa-globe"></i></th>
                <th># <abbr title="Purchase Orders / 订单">PO</abbr>s</th>
                <th class="text-center">Actions</th>
            </tr>

            <?php 
            		  $order_by = " ORDER BY  `record_status` DESC ,  `part_classification` ASC, `epg_supplier_ID` ASC";
            
					  $get_sups_SQL = "SELECT * FROM  `suppliers`" . $order_by;
					  // echo $get_sups_SQL;
					  
					  $sup_count = 0;
	
					  $result_get_sups = mysqli_query($con,$get_sups_SQL);
					  // while loop
					  while($row_get_sups = mysqli_fetch_array($result_get_sups)) {
					  
						$sup_ID = $row_get_sups['ID'];
						$sup_en = $row_get_sups['name_EN'];
						$sup_cn = $row_get_sups['name_CN'];
						$sup_web = $row_get_sups['website'];
						$sup_internal_ID = $row_get_sups['epg_supplier_ID'];
						$sup_status = $row_get_sups['record_status'];
						$sup_part_classification = $row_get_sups['part_classification']; // look up
						$sup_item_supplied = $row_get_sups['items_supplied'];
						$sup_part_type_ID = $row_get_sups['part_type_ID']; // look up
						$sup_certs = $row_get_sups['certifications'];
						$sup_cert_exp_date = $row_get_sups['certification_expiry_date'];
						$sup_evaluation_date = $row_get_sups['evaluation_date'];
						$sup_address_EN = $row_get_sups['address_EN'];
						$sup_address_CN = $row_get_sups['address_CN'];
						$sup_country_ID = $row_get_sups['country_ID']; // look up
						$sup_contact_person = $row_get_sups['contact_person'];
						$sup_mobile_phone = $row_get_sups['mobile_phone'];
						$sup_telephone = $row_get_sups['telephone'];
						$sup_fax = $row_get_sups['fax'];
						$sup_email_1 = $row_get_sups['email_1'];
						$sup_email_2 = $row_get_sups['email_2'];
						
						
								// look up the part type info:
								$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $sup_part_type_ID;
								// echo $get_part_type_SQL;
								$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
								// while loop
								while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
									$part_type_EN = $row_get_part_type['name_EN'];
									$part_type_CN = $row_get_part_type['name_CN'];
								}
						
		
								// count # purchase orders for this vendor
								$count_POs_sql = "SELECT COUNT( ID ) FROM  `purchase_orders` WHERE  `supplier_ID` = " . $sup_ID .' AND `record_status` = 2';
								$count_POs_query = mysqli_query($con, $count_POs_sql);
								$count_POs_row = mysqli_fetch_row($count_POs_query);
								$total_POs = $count_POs_row[0];
						
						
	
								// VENDOR CLASSIFICATION BY STATUS:
						
								$get_sup_status_SQL = "SELECT * FROM `supplier_status` WHERE `status_level` ='" . $sup_status . "'";
								// echo $get_vendor_status_SQL;
	
								$result_get_sup_status = mysqli_query($con,$get_sup_status_SQL);
								// while loop
								while($row_get_sup_status = mysqli_fetch_array($result_get_sup_status)) {
									$sup_status_ID = $row_get_sup_status['ID'];
									$sup_status_name_EN = $row_get_sup_status['name_EN'];
									$sup_status_name_CN = $row_get_sup_status['name_CN'];
									$sup_status_level = $row_get_sup_status['status_level'];
									$sup_status_description = $row_get_sup_status['status_description'];
									$sup_status_color_code = $row_get_sup_status['color_code'];
									$sup_status_icon = $row_get_sup_status['icon'];
								}
	
	
	
								// GET PART CLASSIFICATION:
								$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $sup_part_classification . "'";
								// echo $get_part_class_SQL;
	
								$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
								// while loop
								while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
									$part_class_EN = $row_get_part_class['name_EN'];
									$part_class_CN = $row_get_part_class['name_CN'];
									$part_class_description = $row_get_part_class['description'];
									$part_class_color = $row_get_part_class['color'];
								}
					  
					  ?>

            <tr>
            	<td><?php echo $sup_internal_ID; ?></td>
                <td><a href="supplier_view.php?id=<?php echo $sup_ID; ?>"><?php echo $sup_en; if (($sup_cn!='')&&($sup_cn!='中文名')) { ?> / <?php echo $sup_cn; } ?></a></td>
                <td class="<?php echo $sup_status_color_code; ?>">
                	<i class="fa <?php echo $sup_status_icon; ?>"></i> <?php echo $sup_status_name_EN; if (($sup_status_name_EN!='')&&($sup_status_name_EN!='中文名')) { ?> <br /><?php echo $sup_status_name_CN; }?>
                </td>
                <td><?php 
                  if ($sup_part_classification == 1) { 
                	?><span class="text-danger">CRITICAL</span><?php  
                  } 
                  else { 
                	?><span class="text-success">NON-CRITICAL</span><?php 
                  }?></td>
                <td>
                	<a href="part_type.php?id=<?php echo $sup_part_type_ID; ?>">
                	<?php echo $part_type_EN; 
                	if (($part_type_CN!='')&&($part_type_CN!='中文名')) {
                		echo " / " . $part_type_CN;
                	}
                	?>
                	
                	</a>
                	<br />
                	(<?php echo $sup_item_supplied; ?>)
                </td>
                <td><?php echo $sup_certs; ?></td>
                <td><?php 
                  if ($sup_cert_exp_date != '0000-00-00') {
                		
                	?><span class="text-<?php
                	// now check to see if it's expired!
					if ($sup_cert_exp_date < date("Y-m-d")) {  echo 'danger'; }
					else { echo 'success'; }
					
					?>"><?php

                	echo $sup_cert_exp_date; 
                	
                	?></span><?php
                	
                  
                  } ?></td>
                <td>
                <?php 
                if ($sup_web != '') { 
                	?>
                	<a href="<?php echo $sup_web; ?>" target="_blank" title="Launch in a new window"><i class="fa fa-external-link"></i></a>
                	<?php 
                } 
                ?></td>
                <td><?php echo $total_POs ?></td>
                <td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="supplier_view.php?id=<?php echo $sup_ID; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-primary"><i class="fa fa-eye"></i></a>
                    <a href="supplier_edit.php?id=<?php echo $sup_ID; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="record_delete_do.php?table_name=suppliers&src_page=suppliers.php&id=<?php echo $sup_ID; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
            </tr>

            <?php 
					  
					  $sup_count = $sup_count + 1;
					  
					  } // end while loop
					  ?>

            <tr>
                <th colspan="9">TOTAL: <?php echo $sup_count; ?></th>
                <th class="text-center"><a href="supplier_add.php" class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a></th>
            </tr>


        </table>
    </div>
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>