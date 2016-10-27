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


$record_id = 0;

if (isset($_REQUEST['table'])) {
	$doc_lookup_table = $_REQUEST['table'];
}
else {
	$doc_lookup_table = '';
}

if (isset($_REQUEST['id'])) {

	$record_id = $_REQUEST['id'];
	// WE ARE EDITING!
	$get_doc_SQL = "SELECT * FROM `documents` WHERE `ID` = " . $record_id;
	// echo '<h1>SQL: ' . $get_doc_SQL . '</h1>';
	$result_get_doc = mysqli_query($con,$get_doc_SQL);

    // while loop
    while($row_get_doc = mysqli_fetch_array($result_get_doc)) {
        
		$record_ID 			= $row_get_doc['ID'];
		$doc_name_EN 		= $row_get_doc['name_EN'];
		$doc_name_CN 		= $row_get_doc['name_CN'];
		$doc_filename 		= $row_get_doc['filename'];
		$doc_filetype_ID 	= $row_get_doc['filetype_ID'];
		$doc_file_location 	= $row_get_doc['file_location'];
		$doc_lookup_table 	= $row_get_doc['lookup_table'];
		$doc_lookup_ID 		= $row_get_doc['lookup_ID'];
		$doc_category 		= $row_get_doc['document_category'];
		$doc_record_status 	= $row_get_doc['record_status'];
		$doc_created_by 	= $row_get_doc['created_by'];
		$doc_date_created 	= $row_get_doc['date_created'];
		$doc_filesize_bytes = $row_get_doc['filesize_bytes'];
		$doc_icon 			= $row_get_doc['document_icon'];
		$doc_remarks 		= $row_get_doc['document_remarks'];
		$doc_revision 		= $row_get_doc['doc_revision'];
	}

	$page_title = 'Edit File';
	$form_action = 'EDIT';
}
else {

	$page_title = 'Add File';
	$form_action = 'ADD';
	// WE ARE ADDING!
	// specify default vars for a document recrod:
		$record_ID 				= '';									// AUTO-INCREMENT (NULL)
		$doc_name_EN 			= '';									// USER MUST INSERT
		$doc_name_CN 			= '中文名';								//
		$doc_filename 			= '';									// READ FROM FILE
		$doc_filetype_ID 		= 5; 									// PHOTO
		$doc_file_location 		= 'parts';								//	
		if ($doc_lookup_table != 'NONE') {
			$doc_lookup_table 	= '';									//
		}
		if (isset($_REQUEST['lookup_ID'])){
			$doc_lookup_ID 		= $_REQUEST['lookup_ID'];				//
		}
		else {
			$doc_lookup_ID 			= 0;								// UNLESS SPECIFIED IN REQUEST[]
		}
		if ($_REQUEST['table'] == 'users'){
			$doc_category		= 6;									// PROFILE PHOTO
		}
		else {
			$doc_category 		= 5;									// PART PHOTO
		}
		$doc_record_status 		= 2;									// PUBLISHED
		$doc_created_by 		= $_SESSION['user_ID'];					// 
		$doc_date_created 		= date("Y-m-d H:i:s");					// RIGHT NOW!
		$doc_filesize_bytes 	= 0;									// GET FROM FILE
		$doc_icon 				= 'file-image-o';						//
		$doc_remarks 			= 'Please help to update this record.';	//
		$doc_revision 			= 1;									//
		
}

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2><?php echo $page_title ?> in <?php echo $doc_lookup_table; ?> table</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="documents.php">All Files</a></li>
                <?php 
                if ($record_id != 0) {
                	?>
                <li><a href="document_view.php?id=<?php echo $record_id; ?>">View File</a></li>
                	<?php
                }
                ?>
                <li><span><?php echo $page_title; ?></span></li>
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

    <div class="row">
        <div class="col-md-12">
			
			

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="upload.php" method="post" enctype="multipart/form-data">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">File Details:</h2>
                    </header>
                    <div class="panel-body">
                    
                        
                    	<div class="col-md-4 col-lg-9">
                            <div class="panel-body">
        
        <?php 
        
        // UPDATE: MUST select a table first:

		if (!isset($_REQUEST['table'])) {
			// ASK FOR THE TABLE
			// echo '<h1>No Table</h1>'; 
			
			?>
			
			<!-- START FORM ROW -->
			<div class="form-group">
				<label class="col-md-3 control-label">Select Look-up Table:</label>
				<div class="col-md-5">
					
					<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate" name="lookup_table">
						<option value="#" selected="selected">Select Look-up Table:</option>
						<option value="upload_file.php?table=NONE">No Table / Ignore</option>
					<?php
					$get_tables_SQL = "SHOW TABLES IN  `cl11-epg`";

					$result_get_tables = mysqli_query($con,$get_tables_SQL);
					// while loop
					while($row_get_tables = mysqli_fetch_array($result_get_tables)) {

						// now print each common search result
						$table_name = $row_get_tables['Tables_in_cl11-epg'];
						
						  // ALSO ALLOWING A LIST OF TABLES TO SKIP:
						  if (

								($table_name != 'AQL_letter')
								&&
								($table_name != 'AQL_level')
								&&
								($table_name != 'global_config')
								&&
								($table_name != 'prt_batch_movement')
								&&
								($table_name != 'search_queries')
								&&
								($table_name != 'record_status_type')
								&&
								($table_name != 'update_log')
								&&
								($table_name != 'documents')
								&&
								($table_name != 'document_categories')
								&&
								($table_name != 'document_filetype')
								// can add more tables to ignore here...

							) {
							?>
							<option value="upload_file.php?table=<?php echo $table_name; ?>"<?php if ( ( $doc_lookup_table == $table_name )||( $this_table_name == $table_name ) ) { ?> selected="selected"<?php } ?>>
								<?php echo str_replace("_"," ",$table_name); ?>
							</option>
							<?php
						  }	
						}
					?>
					</select>
				</div>

				<div class="col-md-1">
					&nbsp;
				</div>
			</div>
			<!-- END FORM ROW -->
			
			<?php
			
		}
		else {
			// have table - show contents!
			$this_table_name = $_REQUEST['table'];
			$doc_lookup_table = $this_table_name;
			// echo '<h1>Table is ' . $_REQUEST['table'] . '</h1>';
			
			// OK TO CONTINUE......:
			
			if ($record_ID == '') { // NEW FILE (TAKE FILENAME FROM FILE ITSELF)
				?>
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
								<label class="col-md-3 control-label">Upload New File:</label>
								<div class="col-md-5">
									<input type="file" name="file" id="file" class="form-control">
								</div>
								
								<div class="col-md-4">
								<em>"gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xsl", "xslx"</em>
								</div>
                                
							</div>
                            
                            <!-- END FORM ROW -->
				<?php
			}
			else { // SHOW FILENAME
				?>
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">File Name:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="doc_filename" value="<?php echo $doc_filename; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
				<?php
			}
			
			
		?>
                            
                            
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label text-success">SELECTED TABLE IS:</label>
                                <div class="col-md-5">
                                
                                    <input type="hidden" name="doc_lookup_table" id="doc_lookup_table" value="<?php echo $this_table_name; ?>" />
                                    <?php echo $doc_lookup_table; ?> <a href="upload_file.php?id=<?php echo $record_id; ?>" class="muted"><em>(Change)</em></a>
                                    
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            
                            <?php /*
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Change Look-up Table:</label>
                                <div class="col-md-5">
                                	
                                    
                                	<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
										<option value="upload_file.php?table=NONE" selected="selected">Select Look-up Table / Ignore:</option>
                                    <?php
                                    $get_tables_SQL = "SHOW TABLES IN  `cl11-epg`";

									$result_get_tables = mysqli_query($con,$get_tables_SQL);
									// while loop
									while($row_get_tables = mysqli_fetch_array($result_get_tables)) {

										// now print each common search result
										$table_name = $row_get_tables['Tables_in_cl11-epg'];
										
										  // ALSO ALLOWING A LIST OF TABLES TO SKIP:
										  if (

												($table_name != 'AQL_letter')
												&&
												($table_name != 'AQL_level')
												&&
												($table_name != 'global_config')
												&&
												($table_name != 'prt_batch_movement')
												&&
												($table_name != 'search_queries')
												&&
												($table_name != 'record_status_type')
												&&
												($table_name != 'update_log')
												// can add more tables to ignore here...

											) {
											?>
											<option value="upload_file.php?table=<?php echo $table_name; ?>"<?php if ( ( $doc_lookup_table == $table_name )||( $this_table_name == $table_name ) ) { ?> selected="selected"<?php } ?>>
												<?php echo $table_name; ?>
											</option>
											<?php
										  }	
										}
									?>
									</select>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <?php 
                            */
                            
                            if ($this_table_name != 'NONE') {
                            
                            ?>
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Look-up ID:</label>
                                <div class="col-md-5">
                                    <select class="form-control populate" name="doc_lookup_ID" id="doc_lookup_ID" data-plugin-selectTwo>
										<option value="0" selected="selected">Select Table Record / Ignore:</option>
                                    <?php 
                                    
									$lookup_SQL = $lookup_SQL . "SELECT * FROM `" . $this_table_name . "`";
                                    // echo '<h1>SQL is ' . $lookup_SQL . '</h1>';
                                    $result_get_lookup = mysqli_query($con,$lookup_SQL);
									// while loop
									while($row_get_lookup = mysqli_fetch_array($result_get_lookup)) {

										// now print each common search result
										$lookup_ID 			= $row_get_lookup['ID'];
										$lookup_name_EN 	= $row_get_lookup['name_EN'];
										$lookup_name_CN 	= $row_get_lookup['name_CN'];
										
										?>
										<option value="<?php echo $lookup_ID; ?>"<?php if ($doc_lookup_ID == $lookup_ID) { ?> selected="selected"<?php } ?>><?php 
											echo '[ ID: ' . $lookup_ID . ' ] : ';
											
											if ($this_table_name == 'users') {
												echo $row_get_lookup['first_name'] . ' ' . $row_get_lookup['last_name'];
											}
											else if ($this_table_name == 'part_revisions') {
												part_num_from_rev($lookup_ID, 0);
												echo ' - ';
												part_name_from_rev($lookup_ID, 0);
												echo ' - ';
												part_rev($lookup_ID, 0);
											}
											else if ($this_table_name == 'parts') {
												part_num($lookup_ID, 0);
												echo ' - ';
												echo $lookup_name_EN;
											}
											else if ($lookup_name_EN=='') {
												echo 'no name?';
											}
											else {
												echo $lookup_name_EN;
											}
											
											
											
											
											
											if (($lookup_name_CN!='')&&($lookup_name_CN!='中文名')){
												echo ' / ' . $lookup_name_CN;
											}
										?></option>
										<?php
									}
                                    
                                    
                                    ?>
                                  </select>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <?php 
                            
                            } // end hide for TABLE NONE
                            else {
                            	?>
                                    <input type="hidden" name="doc_lookup_ID" id="doc_lookup_ID" value="0" />
                            	<?
                            }
                            
                            ?>
                            
                            
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Name EN:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="doc_name_EN" value="<?php echo $doc_name_EN; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Name CN:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="doc_name_CN" value="<?php echo $doc_name_CN; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">File Type:</label>
                                <div class="col-md-5">
                                	<select class="form-control populate" name="doc_filetype_ID" id="doc_filetype_ID" data-plugin-selectTwo>
										<option value="0" selected="selected">Select File Type:</option>
	
									<?php 
	
									$get_file_type_SQL = "SELECT * FROM `document_filetype` WHERE `record_status` = '2'";
									// echo '<h1>SQL: ' . $get_file_type_SQL . '</h1>';
	
									$result_get_file_type = mysqli_query($con,$get_file_type_SQL);

									// while loop
									while($row_get_file_type = mysqli_fetch_array($result_get_file_type)) {
										$file_type_ID 				= $row_get_file_type['ID'];
										$file_type_name_EN 			= $row_get_file_type['type_name_EN'];
										$file_type_name_CN 			= $row_get_file_type['type_name_CN'];
										$file_type_default_icon 	= $row_get_file_type['default_icon'];
										$file_type_record_status 	= $row_get_file_type['record_status']; // Should be 2
										$file_type_created_by 		= $row_get_file_type['created_by']; // don't need this!
										$file_type_created_date 	= $row_get_file_type['created_date']; // don't need this!
		
										?>
										<option value="<?php echo $file_type_ID; ?>"<?php if ($doc_filetype_ID == $file_type_ID) { ?> selected="selected"<?php } ?>><?php 
											echo $file_type_name_EN;
											if (($file_type_name_CN!='')&&($file_type_name_CN!='中文名')){
												echo ' / ' . $file_type_name_CN;
											}
										?></option>
										<?php
									}
									?>
									</select>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Document Category:</label>
                                <div class="col-md-5">
                                	<select class="form-control populate" name="doc_category" id="doc_category" data-plugin-selectTwo>
										<option value="0" selected="selected">Select Document Category:</option>
	
									<?php 
	
									$get_doc_cat_SQL = "SELECT * FROM `document_categories` WHERE `record_status` = '2'";
									// echo '<h1>SQL: ' . $get_doc_cat_SQL . '</h1>';
	
									$result_get_doc_cat = mysqli_query($con,$get_doc_cat_SQL);

									// while loop
									while($row_get_doc_cat = mysqli_fetch_array($result_get_doc_cat)) {
										$doc_cat_ID 				= $row_get_doc_cat['ID'];
										$doc_cat_name_EN 			= $row_get_doc_cat['name_EN'];
										$doc_cat_name_CN 			= $row_get_doc_cat['name_CN'];
										$doc_cat_record_status 		= $row_get_doc_cat['record_status']; // Should be 2
										$doc_cat_target_dir 		= $row_get_doc_cat['target_dir'];
		
										?>
										<option value="<?php echo $doc_cat_ID; ?>"<?php if ($doc_category == $doc_cat_ID) { ?> selected="selected"<?php } ?>><?php 
											echo $doc_cat_name_EN;
											if (($doc_cat_name_CN!='')&&($doc_cat_name_CN!='中文名')){
												echo ' / ' . $doc_cat_name_CN;
											}
										?></option>
										<?php
									}
									?>
									</select>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            <?php 
                            if ($record_ID != '') { // ALLOW EDIT, BUT NORMALLY THIS COMES FROM THE FILE!
								?>
								<!-- START FORM ROW -->
								<div class="form-group">
									<label class="col-md-3 control-label">Filesize (Bytes):</label>
									<div class="col-md-5">
										<input type="text" class="form-control" id="inputDefault" name="doc_filesize_bytes" value="<?php echo $doc_filesize_bytes; ?>" />
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								<!-- END FORM ROW -->
								<?php 
                            }
                            ?>
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Icon:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="doc_icon" value="<?php echo $doc_icon; ?>" />
                                </div>

                                <div class="col-md-1">
                                    <a href="http://www.fontawesome.io/icons/" target="_blank" class="btn btn-xs btn-info"><strong>?</strong></a>
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Remarks:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="doc_remarks" value="<?php echo $doc_remarks; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Revision #:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="doc_revision" value="<?php echo $doc_revision; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Date Created:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="doc_date_created" value="<?php echo $doc_date_created; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Created By:</label>
                                <div class="col-md-5">
                                    <?php echo creator_drop_down($doc_created_by); ?>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
								<label class="col-md-3 control-label">Record Status:</label>
								<div class="col-md-5">
									<?php echo record_status_drop_down($doc_record_status); ?>
								</div>
							
								<div class="col-md-1">
									&nbsp;
								</div>
							</div>
                            <!-- END FORM ROW -->
                            
                
                <?php
		} // END OF OK TO CONTINUE
        
        
        ?>
                            
                                </div>
                        </div>

                    </div>
                    
                    <footer class="panel-footer">
                    
                    <div class="row">
					 
					 <?php 
        
        // UPDATE: MUST select a table first:

		if (isset($_REQUEST['table'])) {
			// ASK FOR THE TABLE
			// echo '<h1>No Table</h1>'; 
			
			?>
                    
						<!-- ADD ANY OTHER HIDDEN VARS HERE -->
					  <div class="col-md-5 text-left">	
					  
						<input type="hidden" value="<?php echo $form_action; ?>" name="form_action" />
						
						<?php form_buttons('document_view', $record_id); ?>
					  </div>
					  
					  
					   <!-- NEXT STEP SELECTION -->
							
							<?php 
							if ($_REQUEST['next_step'] == 'view_list') {
								$next_step_selected = 'view_list';
							}
							else {
								$next_step_selected = 'view';
							}
							?>
							
							<label class="col-md-1 control-label text-right">...and then...</label>
							
							<div class="col-md-6 text-left">
								<div class="radio-custom radio-success">
									<input type="radio" id="next_step" name="next_step" value="view_record"<?php if ($next_step_selected == 'view') { ?> checked="checked"<?php } ?>>
									<label for="radioExample9">View Document</label>
								</div>

								<div class="radio-custom radio-warning">
									<input type="radio" id="next_step" name="next_step" value="view_list"<?php if ($next_step_selected == 'view_list') { ?> checked="checked"<?php } ?>>
									<label for="radioExample10">View ALL Documents</label>
								</div>
							</div>
							
							<!-- END OF NEXT STEP SELECTION -->
							
							<?php 
							
							} // end of hide buttons for table selection 
							
							?>
							
					    </div><!-- END ROW -->
					  
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
