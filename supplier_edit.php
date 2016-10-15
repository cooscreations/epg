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

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: suppliers.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	// now get the part info:
    $get_sups_SQL = "SELECT * FROM `suppliers` WHERE `ID` = " . $record_id;
    // echo $get_sups_SQL;

    $result_get_sups = mysqli_query($con,$get_sups_SQL);

    // while loop
    while($row_get_sup = mysqli_fetch_array($result_get_sups)) {
        $sup_ID 						= $row_get_sup['ID'];
        $sup_en 						= $row_get_sup['name_EN'];
        $sup_cn 						= $row_get_sup['name_CN'];
        $sup_web 						= $row_get_sup['website'];
		$sup_epg_supplier_ID 			= $row_get_sup['epg_supplier_ID'];
        $sup_supplier_status 			= $row_get_sup['supplier_status'];
        $sup_part_classification 		= $row_get_sup['part_classification'];
        $sup_items_supplied 			= $row_get_sup['items_supplied'];
        $sup_part_type_ID 				= $row_get_sup['part_type_ID'];
        $sup_certifications 			= $row_get_sup['certifications'];
        $sup_certification_expiry_date 	= $row_get_sup['certification_expiry_date'];
        $sup_evaluation_date 			= $row_get_sup['evaluation_date'];
        $sup_address_EN 				= $row_get_sup['address_EN'];
        $sup_address_CN 				= $row_get_sup['address_CN'];
        $sup_country_ID 				= $row_get_sup['country_ID'];
        $sup_contact_person 			= $row_get_sup['contact_person'];
        $sup_mobile_phone 				= $row_get_sup['mobile_phone'];
        $sup_telephone 					= $row_get_sup['telephone'];
        $sup_fax 						= $row_get_sup['fax'];
        $sup_email_1 					= $row_get_sup['email_1'];
        $sup_email_2 					= $row_get_sup['email_2'];
        $sup_record_status 				= $row_get_sup['record_status'];

    } // end get supplier info WHILE loop
}

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Edit Supplier Record</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="suppliers.php">All Suppliers</a></li>
                <li><span>Edit Supplier</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

             <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="supplier_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit Supplier: <?php get_supplier($sup_ID, 0, 0) ?></h2>
                    </header>
                    <div class="panel-body">

						<div class="form-group">
								<label class="col-md-3 control-label">EPG Supplier ID:<span class="required">*</span></label>
								<div class="col-md-5">
										<input type="text" class="form-control" id="inputDefault" name="epg_supplier_ID" value="<?php echo $sup_epg_supplier_ID; ?>" required />
								</div>

								<div class="col-md-1">
										&nbsp;
								</div>
						</div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name:<span class="required">*</span></label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="name_en" required value="<?php echo $sup_en; ?>" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">名字:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="name_cn" value="<?php echo $sup_cn; ?>" placeholder="中文名" />
                            </div>


                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Website:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="sup_website" value="<?php echo $sup_web; ?>" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

						<div class="form-group">
							<label class="col-md-3 control-label">Supplier Status:<span class="required">*</span></label>
							<div class="col-md-5">
								<select data-plugin-selectTwo class="form-control populate" name="supplier_status" required>

										<?php
												$get_sup_status_SQL = "SELECT * FROM `supplier_status` ";
												// echo $get_vendor_status_SQL;

												$result_get_sup_status = mysqli_query($con,$get_sup_status_SQL);
												// while loop
												while($row_get_sup_status = mysqli_fetch_array($result_get_sup_status)) {
													$sup_status_ID = 			$row_get_sup_status['ID'];
													$sup_status_name_EN = 		$row_get_sup_status['name_EN'];
													$sup_status_name_CN = 		$row_get_sup_status['name_CN'];
													$sup_status_level = 		$row_get_sup_status['status_level'];
													$sup_status_description = 	$row_get_sup_status['status_description'];
													$sup_status_color_code = 	$row_get_sup_status['color_code'];
													$sup_status_icon = 			$row_get_sup_status['icon'];
												?>
											<option value="<?php echo $sup_status_ID; ?>" <?php if ($sup_status_ID == $sup_supplier_status) { ?> selected="selected"<?php } ?>>
												<?php echo $sup_status_name_EN; if (($sup_status_name_CN!='')&&($sup_status_name_CN!='中文名')) { echo " / " . $sup_status_name_CN; } ?>
											</option>
										<?php
									}
									?>
								</select>
							</div>

							<div class="col-md-1">
								&nbsp;
							</div>
						</div>

												<div class="form-group">
													<label class="col-md-3 control-label">Part Classification:<span class="required">*</span></label>
													<div class="col-md-5">
														<select data-plugin-selectTwo class="form-control populate" name="part_classification" required>
															<option value=""></option>
															<?php

																	// GET PART CLASSIFICATION:
																	$get_part_class_SQL = "SELECT * FROM  `part_classification` ";
																	// echo $get_part_class_SQL;

																	$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
																	// while loop
																	while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
																		$part_class_ID = $row_get_part_class['ID'];
																		$part_class_EN = $row_get_part_class['name_EN'];
																		$part_class_CN = $row_get_part_class['name_CN'];
																		$part_class_description = $row_get_part_class['description'];
																		$part_class_color = $row_get_part_class['color'];
																	?>
																	<option value="<?php echo $part_class_ID; ?>"<?php if ($part_class_ID == $sup_part_classification){ ?> selected="selected"<?php } ?>>
																		<?php echo $part_class_EN; if (($part_class_CN!='')&&($part_class_CN!='中文名')) { echo " / " . $part_class_CN; } ?>
																	</option>
																<?php
															}
															?>
														</select>
													</div>

													<div class="col-md-1">
														&nbsp;
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-3 control-label">Part Type:<span class="required">*</span></label>
													<div class="col-md-5">
														<select data-plugin-selectTwo class="form-control populate" name="part_type_ID" required>
															<option value=""></option>
															<?php

																	// GET PART TYPE:
																	$list_part_types_SQL = "SELECT * FROM  `part_type` WHERE `record_status` = 2";
																	// echo $get_part_type_SQL;

																	$result_list_part_types = mysqli_query($con,$list_part_types_SQL);
																	// while loop
																	while($row_list_part_types = mysqli_fetch_array($result_list_part_types)) {
																		$list_part_type_ID = $row_list_part_types['ID'];
																		$list_part_type_EN = $row_list_part_types['name_EN'];
																		$list_part_type_CN = $row_list_part_types['name_CN'];

																	?>
																	<option value="<?php echo $list_part_type_ID; ?>"<?php if ($list_part_type_ID == $sup_part_type_ID){ ?> selected="selected"<?php } ?>>
																		<?php echo $list_part_type_EN; if (($list_part_type_CN!='')&&($list_part_type_CN!='中文名')) { echo " / " . $list_part_type_CN; } ?>
																	</option>
																<?php
															}
															?>
														</select>
													</div>

													<div class="col-md-1">
														&nbsp;
													</div>
												</div>

												<div class="form-group">
                            <label class="col-md-3 control-label">Items Supplied:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="items_supplied" value="<?php echo $sup_items_supplied; ?>" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

												<div class="form-group">
                            <label class="col-md-3 control-label">Certifications:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="certifications" value="<?php echo $sup_certifications; ?>" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

												<div class="form-group">
													<label class="col-md-3 control-label">Certification Expiry date:</label>
													<div class="col-md-5">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</span>
															<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="certification_expiry_date" value="<?php echo $sup_certification_expiry_date; ?>"  />
														</div>
													</div>
													<div class="col-md-1">
														&nbsp;
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-3 control-label">Evaluation date:</label>
													<div class="col-md-5">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</span>
															<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="evaluation_date" value="<?php echo $sup_evaluation_date; ?>"  />
														</div>
													</div>
													<div class="col-md-1">
														&nbsp;
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-3 control-label">Address:<span class="required">*</span></label>
													<div class="col-md-5">
														<textarea class="form-control" rows="3" id="textareaDefault" name="address_EN" required><?php echo $sup_address_EN; ?></textarea>
													</div>
													<div class="col-md-1">&nbsp;</div>
												</div>

												<div class="form-group">
													<label class="col-md-3 control-label">Address CN:</label>
													<div class="col-md-5">
														<textarea class="form-control" rows="3" id="textareaDefault" name="address_CN" ><?php echo $sup_address_CN; ?></textarea>
													</div>
													<div class="col-md-1">&nbsp;</div>
												</div>

												<div class="form-group">
													<label class="col-md-3 control-label">Country:<span class="required">*</span></label>
													<div class="col-md-5">
														<select data-plugin-selectTwo class="form-control populate" name="country_ID" required>
															<option value=""></option>
															<?php

																	// GET Countries:
																	$get_con_SQL = "SELECT * FROM  `countries` ORDER BY  `countries`.`name_EN` ASC";
				                          // echo $get_con_SQL;
																	$con_count = 0;

																	$result_get_cons = mysqli_query ( $con, $get_con_SQL );
																	// while loop
																	while ( $row_get_cons = mysqli_fetch_array ( $result_get_cons ) ) {
																		$country_ID = $row_get_cons['ID'];
																		$country_name_EN = $row_get_cons['name_EN'];
																		$country_name_CN = $row_get_cons['name_CN'];
																		$country_code = $row_get_cons['code'];
																		$country_alpha_2 = $row_get_cons['alpha_2'];
																		$country_alpha_3 = $row_get_cons['alpha_3'];
																		$country_iso_code = $row_get_cons['ISO_code'];

																	?>
																	<option value="<?php echo $country_ID; ?>"<?php if ($country_ID == $sup_country_ID){ ?> selected="selected"<?php } ?>>
																		<?php echo $country_name_EN; if (($country_name_CN!='')&&($country_name_CN!='中文名')) { echo " / " . $country_name_CN; } ?>
																	</option>
																<?php
															}
															?>
														</select>
													</div>

													<div class="col-md-1">
														&nbsp;
													</div>
												</div>
												

												<div class="form-group">
                            <label class="col-md-3 control-label">Contact Person:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="contact_person" value="<?php echo $sup_contact_person; ?>" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

												<div class="form-group">
													<label class="col-md-3 control-label">Mobile Number:</label>
													<div class="col-md-5">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-phone"></i>
															</span>
															<input type="text" class="form-control" id="inputDefault" name="mobile_phone" placeholder="+86xxxxxxxxxxx" value="<?php echo $sup_mobile_phone; ?>" />
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-3 control-label">Telephone Number:</label>
													<div class="col-md-5">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-phone"></i>
															</span>
															<input type="text" class="form-control" id="inputDefault" name="telephone" placeholder="+86xxxxxxxxxxx" value="<?php echo $sup_telephone; ?>" />
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-3 control-label">Fax:</label>
													<div class="col-md-5">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-phone"></i>
															</span>
															<input type="text" class="form-control" id="inputDefault" name="fax" placeholder="+86xxxxxxxxxxx" value="<?php echo $sup_fax; ?>" />
														</div>
													</div>
												</div>


												<div class="form-group">
                            <label class="col-md-3 control-label">E-mail:</label>
                            <div class="col-md-5">
                                <input type="email" class="form-control" name="email_1" value="<?php echo $sup_email_1; ?>" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

						<div class="form-group">
								<label class="col-md-3 control-label">Alternate E-mail:</label>
								<div class="col-md-5">
										<input type="email" class="form-control" name="email_2" value="<?php echo $sup_email_2; ?>" />
								</div>

								<div class="col-md-1">
										&nbsp;
								</div>
						</div>

						<div class="form-group">
							<label class="col-md-3 control-label">Record Status:</label>
							<div class="col-md-5">
								<?php record_status_drop_down($sup_record_status); ?>
							</div>

							<div class="col-md-1">
								&nbsp;
							</div>
						</div>



                    </div>
                    
                    <footer class="panel-footer">
						<div class="row">
							<!-- ADD ANY OTHER HIDDEN VARS HERE -->
						  	<div class="col-md-5 text-left">	
								<?php form_buttons('supplier_view', $record_id); ?>
						  	</div>
						 </div><!-- end row div -->
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
