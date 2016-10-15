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
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 7;

// pull the header and template stuff:
pagehead($page_id); 

// FILTER INFO:

$add_SQL = " WHERE `record_status` = '2'";
$add_URL_vars_sup_status = '';
$add_URL_vars_sort = '';
$add_URL_vars_dir = '';
$add_URL_vars_dir_opp = '';
$sort_SQL = " ORDER BY `record_status` DESC , `part_classification` ASC, `epg_supplier_ID` ASC"; // default sort
$dir_SQL = '';

if (isset($_REQUEST['sort'])) {
	$sort_SQL = " ORDER BY `" . $_REQUEST['sort'] . "`";
	$add_URL_vars_sort = "&sort=". $_REQUEST['sort'];
	if (isset($_REQUEST['dir'])) {
		$sort_SQL .= " " . $_REQUEST['dir'];
		$add_URL_vars_dir = "&dir=" . $_REQUEST['dir'];
	}
	else {
		$sort_SQL .= " ASC";
		$add_URL_vars_dir = "&dir=ASC";
	}
	
	if ($_REQUEST['dir'] == 'ASC') {
		$add_URL_vars_dir_opp = 'DESC';
		$alfa_sort_icon = "fa fa-sort-alpha-desc";
	}
	else {
		$add_URL_vars_dir_opp = 'ASC';
		$alfa_sort_icon = "fa fa-sort-alpha-asc";
	}
	
}

if (isset($_REQUEST['sup_status_id'])) {
	$add_SQL .= " AND `supplier_status` = '" . $_REQUEST['sup_status_id'] . "'";
	$add_URL_vars_sup_status = "&sup_status_id=" . $_REQUEST['sup_status_id'];
}

// OUTPUT VAR COMBO:
// $add_URL_vars_sup_status . 

?>



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
    
    <?php
	add_button(0, 'supplier_add');
	?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-condensed mb-none">
          <thead>
            <tr>
            	<th class="text-center"><i class="fa fa-cog" title="Action"></i></th>
                <th><abbr title="(EPG Supplier ID, NOT Database Unique ID!)">ID</abbr></ht>
                <th class="text-center">
                  <a href="suppliers.php?sort=name_EN<?php echo $add_URL_vars_sup_status; ?>&dir=<?php echo $add_URL_vars_dir_opp; ?>">
                	Name / 名字 <i class="<?php echo $alfa_sort_icon; ?> pull-right"></i>
                  </a>
                </th>
                <th class="text-center">
                	Status<br />
                	<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
						<option value="#" selected="selected">Filter:</option>
							<option value="suppliers.php?1<?php echo $add_URL_vars_sort . $add_URL_vars_dir; ?>">Clear This Filter</option>
							<option value="suppliers.php">Clear All Filters</option>
							<?php
							$get_j_sup_status_SQL = "SELECT * FROM `supplier_status`";
							$result_j_get_sup_status = mysqli_query($con,$get_j_sup_status_SQL);
							// while loop
							while($row_j_get_sup_status = mysqli_fetch_array($result_j_get_sup_status)) {

									// now print each record:
									$j_sup_status_id 					= $row_j_get_sup_status['ID'];
									$j_sup_status_name_EN 				= $row_j_get_sup_status['name_EN'];
									$j_sup_status_name_CN 				= $row_j_get_sup_status['name_CN'];
									$j_sup_status_status_level 			= $row_j_get_sup_status['status_level'];
									$j_sup_status_status_description 	= $row_j_get_sup_status['status_description'];
									$j_sup_status_color_code 			= $row_j_get_sup_status['color_code'];
									$j_sup_status_icon 					= $row_j_get_sup_status['icon'];

						
									// count docs in this category:
									$count_j_sup_status_sql = "SELECT COUNT( ID ) FROM  `suppliers` WHERE `supplier_status` = '" . $j_sup_status_id . "'";
									$count_j_sup_status_query = mysqli_query($con, $count_j_sup_status_sql);
									$count_j_sup_status_row = mysqli_fetch_row($count_j_sup_status_query);
									$total_j_sup_status = $count_j_sup_status_row[0];

									?>
									<option value="suppliers.php?sup_status_id=<?php echo $j_sup_status_id . $add_URL_vars_sort . $add_URL_vars_dir; ?>"<?php if ($_REQUEST['sup_status_id'] == $j_sup_status_id) { ?> selected="selected"<?php } ?>><?php 
					
										echo $j_sup_status_name_EN; 
										if (($j_sup_status_name_CN!='')&&($j_sup_status_name_CN!='中文名')) { 
											echo ' / ' . $j_sup_status_name_CN;
										}	
									?> (<?php echo $total_j_sup_status; ?>)</option>
									<?php
							}
							?>
					</select>
				</th>
                <th class="text-center">Type</th>
                <th class="text-center">Product Type</th>
                <th class="text-center">More Info</th>
                <th class="text-center">Cert.</th>
                <th class="text-center">Expires</th>
                <th class="text-center"><i class="fa fa-globe"></i></th>
            </tr>
          </thead>
          <tbody>

            <?php

					  $get_sups_SQL = "SELECT * FROM `suppliers`" . $add_SQL . $sort_SQL;
					  // echo '<h1>' . $get_sups_SQL . '</h1>';

					  $sup_count = 0;

					  $result_get_sups = mysqli_query($con,$get_sups_SQL);
					  // while loop
					  while($row_get_sups = mysqli_fetch_array($result_get_sups)) {

						$sup_ID = $row_get_sups['ID'];
						$sup_en = $row_get_sups['name_EN'];
						$sup_cn = $row_get_sups['name_CN'];
						$sup_web = $row_get_sups['website'];
						$sup_internal_ID = $row_get_sups['epg_supplier_ID'];
						$sup_status = $row_get_sups['supplier_status'];
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
						$sup_record_status = $row_get_sups['record_status'];



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
            	<td class="text-center">
						
				<!-- ********************************************************* -->
				<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
				<!-- ********************************************************* -->

				<?php 

				// VARS YOU NEED TO WATCH / CHANGE:
				$add_to_form_name 	= 'sup_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
				$form_ID 			= $sup_ID;					// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
				$edit_URL 			= 'supplier_edit'; 	// REQUIRED - specify edit page URL
				$add_URL 			= 'supplier_add'; 	// REQURED - specify add page URL
				$table_name 		= 'suppliers';		// REQUIRED - which table are we updating?
				$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
				$add_VAR 			= ''; 	// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO

				?>

					<a class="modal-with-form btn btn-default" href="#modalForm_<?php 

						echo $add_to_form_name; 
						echo $form_ID; 

					?>"><i class="fa fa-gear"></i></a>

					<!-- Modal Form -->
					<div id="modalForm_<?php 

						echo $add_to_form_name; 
						echo $form_ID; 

					?>" class="modal-block modal-block-primary mfp-hide">
						<section class="panel">
							<header class="panel-heading">
								<h2 class="panel-title">Admin Options</h2>
							</header>
							<div class="panel-body">

								<div class="table-responsive">
								 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
								 <thead>
									<tr>
										<th class="text-left" colspan="2">Action</th>
										<th>Decsription</th>
									</tr>
								  </thead>
								  <tbody>
									<tr>
									  <td>EDIT</td>
									  <td>
										<a href="<?php 
											echo $edit_URL; 
										?>.php?id=<?php 
											echo $form_ID; 
										?>" class="mb-xs mt-xs mr-xs btn btn-warning">
											<i class="fa fa-pencil" stlye="color: #999"></i>
										</a>
									  </td>
									  <td>Edit this record</td>
									</tr>
									<tr>
									  <td>DELETE</td>
									  <td>
										<a href="record_delete_do.php?table_name=<?php 
											echo $table_name; 
										?>&src_page=<?php 
											echo $src_page; 
										?>&id=<?php 
											echo $form_ID;
											echo '&' . $add_VAR; // NOTE THE LEADING '&' <<<  
										?>" class="mb-xs mt-xs mr-xs btn btn-danger">
											<i class="fa fa-trash modal-icon" stlye="color: #999"></i>
										</a>
									  </td>
									  <td>Delete this record</td>
									</tr>
									<tr>
									  <td>ADD</td>
									  <td>
										<a href="<?php 
											echo $add_URL; 
											echo '.php?' . $add_VAR;  // NOTE THE LEADING '?' <<<
										?>" class="mb-xs mt-xs mr-xs btn btn-success">
											<i class="fa fa-plus" stlye="color: #999"></i>
										</a>
									  </td>
									  <td>Add a similar item to this table</td>
									</tr>
								  </tbody>
								  <tfoot>
									<tr>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									</tr>
								  </tfoot>
								  </table>
								</div><!-- end of responsive table -->	

							</div><!-- end panel body -->
							<footer class="panel-footer">
								<div class="row">
									<div class="col-md-12 text-left">
										<button class="btn btn-danger modal-dismiss"><i class="fa fa-times" stlye="color: #999"></i> Cancel</button>
									</div>
								</div>
							</footer>
						</section>
					</div>

				<!-- ********************************************************* -->
				<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
				<!-- ********************************************************* -->
				
				 </td>
            	<td class="text-right">
            	  <?php 
            	
            		echo '<span';
            		if ($sup_internal_ID == 0) { echo ' class="text-danger" title="Internal ID number not entered. Please update this record!"'; }
            		echo '>';
            		echo $sup_internal_ID; 
            		echo '</span>';
            		
            	  ?>
            	</td>
                <td>
                  <?php get_supplier($sup_ID); ?>
                </td>
                <!-- <td class="<?php echo $sup_status_color_code; ?>"> -->
                <td class="text-center">
                  <button type="button" class="btn btn-xs btn-<?php echo $sup_status_color_code; ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo $sup_status_name_EN; if (($sup_status_name_CN!='')&&($sup_status_name_CN!='中文名')) { echo ' / ' . $sup_status_name_CN; } ?>">
                  	<i class="fa <?php echo $sup_status_icon; ?>"></i>
                  </button>
                </td>
                <td class="text-center"><?php
                  if ($sup_part_classification == 1) {
                	?><span class="text-danger"><i class="fa fa-exclamation-triangle" title="CRITICAL"></i></span><?php
                  }
                  else {
                	?><span class="text-success"><i class="fa fa-check" title="NON-CRITICAL"></i></span><?php
                  }?></td>
                <td><?php 
                
                	// get the part type info:
                	
                	$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` ='" . $sup_part_type_ID . "'";
					// echo $get_part_type_SQL;

					$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
					// while loop
					while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
						$part_type_EN = $row_get_part_type['name_EN'];
						$part_type_CN = $row_get_part_type['name_CN'];
						
						?>
						<a href="parts.php?type_id=<?php echo $sup_part_type_ID; ?>" title="View all parts of this type">
						  <?php 
							echo $part_type_EN;
							if (($part_type_CN!='')&&($part_type_CN!='中文名')) {
								echo $part_type_CN;
							}
						  ?>
						</a>
						<?php
						
					}
                
                ?>
                </td>
                <td>
                	<?php 
                	if ($sup_item_supplied!='') {
                		echo $sup_item_supplied; 
                	}
                	else { echo '&nbsp;'; }
                	?>
                </td>
                <td>
                	<?php 
                	  if ($sup_certs!='') {
                		echo $sup_certs; 
                	  }
                	  else { echo '&nbsp;'; }
                	?>
                </td>
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
            </tr>

            <?php

					  $sup_count = $sup_count + 1;

					  } // end while loop
					  ?>

          </tbody>
          <tfoot>
            <tr>
                <th colspan="10" class="text-left">TOTAL: <?php echo $sup_count; ?></th>
            </tr>
          </tfoot>
        </table>
    </div>
    
    <?php
	add_button(0, 'supplier_add');
	?>
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
