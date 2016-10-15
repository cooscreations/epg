<?php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */
session_start (); /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// now check the user is OK to view this page //
/* //////// require ('page_access.php'); / */
// ///*/
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

$add_cat_VARS = '';
$add_type_VARS = '';
$add_table_VARS = '';

if (isset($_REQUEST['cat_id'])){
	$add_cat_VARS .= '&cat_id=' . $_REQUEST['cat_id'];
}
if (isset($_REQUEST['filetype_id'])){
	$add_type_VARS .= '&filetype_id=' . $_REQUEST['filetype_id'];
}
if (isset($_REQUEST['table_name'])){
	$add_table_VARS .= '&table_name=' . $_REQUEST['table_name'];
}


?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Documents</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                <li><span>Documents</span></li>
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
    
    
    <?php add_button(0, 'document_add'); ?>
   
    <div class="table-responsive">
        <table
            class="table table-bordered table-striped table-condensed mb-none">
            <tr>
            	<th class="text-center"><i class="fa fa-gear btn btn-default"></i></th>
                <th class="text-center">ID</th>
                <th class="text-center">Name / 名字</th>
                <th class="text-center">Category<br />
                	<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
						<option value="#" selected="selected">Filter:</option>
							<option value="documents.php?1<?php echo $add_table_VARS; echo $add_type_VARS; ?>">Clear This Filter</option>
							<option value="documents.php">Clear All Filters</option>
						<?php
						$get_j_this_doc_cat_SQL = "SELECT * FROM `document_categories` WHERE `record_status` = '2'";
						$result_j_get_this_doc_cat = mysqli_query($con,$get_j_this_doc_cat_SQL);
						// while loop
						while($row_j_get_this_doc_cat = mysqli_fetch_array($result_j_get_this_doc_cat)) {

								// now print each record:
								$j_doc_cat_id 				= $row_j_get_this_doc_cat['ID'];
								$j_doc_cat_name_EN 			= $row_j_get_this_doc_cat['name_EN'];
								$j_doc_cat_name_CN 			= $row_j_get_this_doc_cat['name_CN'];
								$j_doc_cat_record_status 	= $row_j_get_this_doc_cat['record_status'];

						
								// count docs in this category:
								$count_j_cats_sql = "SELECT COUNT( ID ) FROM  `documents` WHERE `document_category` = '" . $j_doc_cat_id . "'";
								$count_j_cats_query = mysqli_query($con, $count_j_cats_sql);
								$count_j_cats_row = mysqli_fetch_row($count_j_cats_query);
								$total_j_cats = $count_j_cats_row[0];

								?>
								<option value="documents.php?cat_id=<?php echo $j_doc_cat_id; echo $add_table_VARS; echo $add_type_VARS; ?>"<?php if ($_REQUEST['cat_id'] == $j_doc_cat_id) { ?> selected="selected"<?php } ?>><?php 
					
									echo $j_doc_cat_name_EN; 
									if (($j_doc_cat_name_CN!='')&&($j_doc_cat_name_CN!='中文名')) { 
										echo ' / ' . $j_doc_cat_name_CN;
									}	
								?> (<?php echo $total_j_cats; ?>)</option>
								<?php
						}
						?>
					</select>
                </th>
                <th class="text-center">Type<br />
					<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
						<option value="#" selected="selected">Filter:</option>
							<option value="documents.php?1<?php echo $add_table_VARS; echo $add_cat_VARS; ?>">Clear This Filter</option>
							<option value="documents.php">Clear All Filters</option>
						<?php 
						$get_filetype_list_SQL = "SELECT * FROM `document_filetype` WHERE `record_status` = '2'";
						$result_filetype_list = mysqli_query($con,$get_filetype_list_SQL);
						// while loop
						while($row_filetype_list = mysqli_fetch_array($result_filetype_list)) {

								// now print each record:
								$filetype_list_id 				= $row_filetype_list['ID'];
								$filetype_list_type_name_EN 	= $row_filetype_list['type_name_EN'];
								$filetype_list_type_name_CN 	= $row_filetype_list['type_name_CN'];
								$filetype_list_default_icon 	= $row_filetype_list['default_icon'];
								$filetype_list_record_status 	= $row_filetype_list['record_status'];
								$filetype_list_created_by 		= $row_filetype_list['created_by'];
								$filetype_list_created_date 	= $row_filetype_list['created_date'];
							
								// now count docs for this filetype:
							
							
								// count docs of this filetype:
								$count_j_docs_sql = "SELECT COUNT( ID ) FROM  `documents` WHERE `filetype_ID` = '" . $filetype_list_id . "'";
								$count_j_docs_query = mysqli_query($con, $count_j_docs_sql);
								$count_j_docs_row = mysqli_fetch_row($count_j_docs_query);
								$total_j_docs = $count_j_docs_row[0];

								?>
								<option value="documents.php?filetype_id=<?php echo $filetype_list_id; echo $add_table_VARS; echo $add_cat_VARS; ?>"<?php if ($_REQUEST['filetype_id'] == $filetype_list_id) { ?> selected="selected"<?php } ?>><?php 
					
									echo $filetype_list_type_name_EN; 
									if (($filetype_list_type_name_CN!='')&&($filetype_list_type_name_CN!='中文名')) { 
										echo ' / ' . $filetype_list_type_name_CN;
									}	
								?> (<?php echo $total_j_docs; ?>)</option>
								<?php
						}
						?>
					</select>
                </th>
                <th class="text-center">Ref. Table / ID<br />
					<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
							<option value="#" selected="selected">Filter:</option>
							<option value="documents.php?1<?php echo $add_cat_VARS; echo $add_type_VARS; ?>">Clear This Filter</option>
							<option value="documents.php">Clear All Filters</option>
							<?php
						
							$get_tables_SQL = "SHOW TABLES IN  `cl11-epg`";

							$result_get_tables = mysqli_query($con,$get_tables_SQL);
								// while loop
								while($row_get_tables = mysqli_fetch_array($result_get_tables)) {

									// now print each common search result
									$table_name = $row_get_tables['Tables_in_cl11-epg'];

						
									// count docs in this table:
									$count_j_table_sql = "SELECT COUNT( ID ) FROM  `documents` WHERE `lookup_table` = '" . $table_name . "'";
									$count_j_table_query = mysqli_query($con, $count_j_table_sql);
									$count_j_table_row = mysqli_fetch_row($count_j_table_query);
									$total_j_table = $count_j_table_row[0];

									?>
									<option value="documents.php?table_name=<?php echo $table_name; echo $add_cat_VARS; echo $add_type_VARS; ?>"<?php if ($_REQUEST['table_name'] == $table_name) { ?> selected="selected"<?php } ?>>
									  <?php echo $table_name; ?> (<?php echo $total_j_table; ?>)
									</option>
									<?php
							}
							?>
						</select>
					</th>
                <th class="text-center">FILE</th>
            </tr>

            <?php
            
            $add_SQL = '';
            
			if (isset($_REQUEST['filetype_id'])) {
				$add_SQL .= " AND `filetype_ID` = '" . $_REQUEST['filetype_id'] . "'";
			}
			if (isset($_REQUEST['cat_id'])) {
				$add_SQL .= " AND `document_category` = '" . $_REQUEST['cat_id'] . "'";
			}
			if (isset($_REQUEST['table_name'])) {
				$add_SQL .= " AND `lookup_table` = '" . $_REQUEST['table_name'] . "'";
			}
            
                          	$get_doc_SQL = "SELECT * FROM  `documents` WHERE `record_status` = '2'" . $add_SQL . "";
							$result_get_doc = mysqli_query($con,$get_doc_SQL);
							// while loop
							while($row_get_doc = mysqli_fetch_array($result_get_doc)) {

									// now print each record:
									$doc_id 				= $row_get_doc['ID']; // same as $record_id
									$doc_name_EN 			= $row_get_doc['name_EN'];
									$doc_name_CN 			= $row_get_doc['name_CN'];
									$doc_filename 			= $row_get_doc['filename'];
									$doc_filetype_ID 		= $row_get_doc['filetype_ID'];
									$doc_file_location 		= $row_get_doc['file_location'];
									$doc_lookup_table 		= $row_get_doc['lookup_table'];
									$doc_lookup_ID 			= $row_get_doc['lookup_ID'];
									$doc_document_category 	= $row_get_doc['document_category'];
									$doc_record_status 		= $row_get_doc['record_status'];
									$doc_created_by 		= $row_get_doc['created_by'];
									$doc_date_created 		= $row_get_doc['date_created'];
									$doc_filesize_bytes 	= $row_get_doc['filesize_bytes'];
									$doc_document_icon 		= $row_get_doc['document_icon'];
									$doc_document_remarks 	= $row_get_doc['document_remarks'];
									$doc_doc_revision 		= $row_get_doc['doc_revision'];
		
									// SPECIFY FULL FILE LOCATION:
		
									if ($doc_document_category == 5) {
										// this is a part photo -  let's link to it
										$file_path = 'assets/images';
									}
									else {
										// DEFAULT?
										$file_path = 'assets/files';
									}
									// now build the link:
									$full_file_path = 'http://120.24.71.207/' . $file_path . '/' .  $doc_file_location . '/' . $doc_filename;
									// echo '<h4>Path: ' . $full_file_path . '</h4>';
		
									// GET DOC CATEGORY
		
									$get_this_doc_cat_SQL = "SELECT * FROM `document_categories` WHERE `ID` = '" . $doc_document_category . "'";
									$result_get_this_doc_cat = mysqli_query($con,$get_this_doc_cat_SQL);
									// while loop
									while($row_get_this_doc_cat = mysqli_fetch_array($result_get_this_doc_cat)) {

											// now print each record:
											$doc_cat_id 			= $row_get_this_doc_cat['ID'];
											$doc_cat_name_EN 		= $row_get_this_doc_cat['name_EN'];
											$doc_cat_name_CN 		= $row_get_this_doc_cat['name_CN'];
											$doc_cat_record_status 	= $row_get_this_doc_cat['record_status'];
				
									}
		
									// GET FILETYPE
		
									$get_this_filetype_SQL = "SELECT * FROM `document_filetype` WHERE `ID` = '" . $doc_filetype_ID . "'";
									$result_get_this_filetype = mysqli_query($con,$get_this_filetype_SQL);
									// while loop
									while($row_get_this_filetype = mysqli_fetch_array($result_get_this_filetype)) {

											// now print each record:
											$filetype_id 			= $row_get_this_filetype['ID'];
											$filetype_type_name_EN 	= $row_get_this_filetype['type_name_EN'];
											$filetype_type_name_CN 	= $row_get_this_filetype['type_name_CN'];
											$filetype_default_icon 	= $row_get_this_filetype['default_icon'];
											$filetype_record_status = $row_get_this_filetype['record_status'];
											$filetype_created_by 	= $row_get_this_filetype['created_by'];
											$filetype_created_date 	= $row_get_this_filetype['created_date'];
				
									}
		
									// COMPARE TRUE FILESIZE WITH DB INFO - just to be sure...
									/*
									$php_calculate_filesize = filesize($full_file_path);
									if ($php_calculate_filesize != $doc_filesize_bytes) {
										// let's update the database with the true size of the file!
										$update_file_size_SQL = "";
									}
		
									function human_filesize($bytes, $decimals = 2) {
									  $sz = 'BKMGTP';
									  $factor = floor((strlen($bytes) - 1) / 3);
									  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
									}
									*/
			?>

            <tr>
            
            <td class="text-center">
                    
					<!-- ********************************************************* -->
					<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
					<!-- ********************************************************* -->
			
					<?php 
			
					// VARS YOU NEED TO WATCH / CHANGE:
					$add_to_form_name 	= '';						// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
					$form_ID 			= $doc_id;				// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
					$edit_URL 			= 'document_edit'; 			// REQUIRED - specify edit page URL
					$add_URL 			= 'document_add'; 			// REQURED - specify add page URL
					$table_name 		= 'documents';				// REQUIRED - which table are we updating?
					$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
					$add_VAR 			= ''; 						// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
			
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
           
            	<td class="text-center"><?php echo $doc_id; ?></td>
            	<td class="text-center">
            	  <a href="document_view.php?id=<?php echo $doc_id; ?>" title="Click to view full document details">	
            		<?php 
						echo $doc_name_EN;
						if (($doc_name_CN!='')&&($doc_name_CN!='中文名')) {
							echo ' / ' . $doc_name_CN;
						}            		
            		?>
            	  </a>
            	</td>
            	<td class="text-center"><?php 
            		echo $doc_cat_name_EN;
            		if (($doc_cat_name_CN!='')&&($doc_cat_name_CN!='中文名')) {
            			echo ' / ' . $doc_cat_name_CN;
            		}
            	?></td>
            	<td class="text-center">
            		<i class="fa fa-<?php echo $filetype_default_icon; ?>"></i> 
					<?php 
						echo $filetype_type_name_EN;
						if (($filetype_type_name_CN!='')&&($filetype_type_name_CN!='中文名')) {
							echo ' / ' . $filetype_type_name_CN;
						}
					?>
				</td> 
            	<td class="text-center"><?php echo $doc_lookup_table; ?> (ID. #: <?php echo $doc_lookup_ID; ?>)
            	<?php 
            	
            	if ($doc_lookup_table == 'part_revisions') {
            	
            		// get the part number!
            		$this_rev_part_num_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $doc_lookup_ID . "'";
            		$result_this_rev_part_num = mysqli_query($con,$this_rev_part_num_SQL);
					// while loop
					while($row_this_rev_part_num = mysqli_fetch_array($result_this_rev_part_num)) {

							// now print each record:
							$part_id 			= $row_this_rev_part_num['part_ID'];
							echo '<br />';
							part_num($part_id);
							echo ' ';
							part_rev($doc_lookup_ID);
							
					}
            	}
            	
            	?></td>
            	<td class="text-center">
            		<a href="<?php echo $full_file_path; ?>" target="_blank" class="text-muted"><i class="fa fa-cloud-download"></i></a>
            	</td>
            </tr>

            <?php
            
				$doc_count = $doc_count + 1;
			} // end while loop
			?>

            <tr>
                <th colspan="8">TOTAL: <?php echo $doc_count; ?></th>
            </tr>
        </table>
    </div>
    
    <?php add_button(0, 'document_add'); ?>
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
