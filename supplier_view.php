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

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

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

// now get the supplier info:
$get_sups_SQL = "SELECT * FROM `suppliers` WHERE `ID` = " . $record_id;
                                                                     // echo $get_sups_SQL;

$result_get_sups = mysqli_query($con,$get_sups_SQL);

// while loop
while($row_get_sup = mysqli_fetch_array($result_get_sups)) {
	$sup_ID 					= $row_get_sup['ID'];
	$sup_en 					= $row_get_sup['name_EN'];
	$sup_cn 					= $row_get_sup['name_CN'];
	$sup_web 					= $row_get_sup['website'];
	$sup_internal_ID 			= $row_get_sup['epg_supplier_ID'];
	$sup_status 				= $row_get_sup['supplier_status'];
	$sup_part_classification 	= $row_get_sup['part_classification']; // look up
	$sup_item_supplied 			= $row_get_sup['items_supplied'];
	$sup_part_type_ID 			= $row_get_sup['part_type_ID']; // look up
	$sup_certs 					= $row_get_sup['certifications'];
	$sup_cert_exp_date 			= $row_get_sup['certification_expiry_date'];
	$sup_evaluation_date 		= $row_get_sup['evaluation_date'];
	$sup_address_EN 			= $row_get_sup['address_EN'];
	$sup_address_CN 			= $row_get_sup['address_CN'];
	$sup_country_ID 			= $row_get_sup['country_ID']; // look up
	$sup_contact_person 		= $row_get_sup['contact_person'];
	$sup_mobile_phone 			= $row_get_sup['mobile_phone'];
	$sup_telephone 				= $row_get_sup['telephone'];
	$sup_fax 					= $row_get_sup['fax'];
	$sup_email_1 				= $row_get_sup['email_1'];
	$sup_email_2 				= $row_get_sup['email_2'];
	$sup_record_status			= $row_get_sup['record_status'];

			// VENDOR CLASSIFICATION BY STATUS:

			$get_sup_status_SQL = "SELECT * FROM `supplier_status` WHERE `status_level` ='" . $sup_status . "'";
			// echo $get_vendor_status_SQL;

			$result_get_sup_status = mysqli_query($con,$get_sup_status_SQL);
			// while loop
			while($row_get_sup_status = mysqli_fetch_array($result_get_sup_status)) {
				$sup_status_ID 				= $row_get_sup_status['ID'];
				$sup_status_name_EN 		= $row_get_sup_status['name_EN'];
				$sup_status_name_CN 		= $row_get_sup_status['name_CN'];
				$sup_status_level 			= $row_get_sup_status['status_level'];
				$sup_status_description 	= $row_get_sup_status['status_description'];
				$sup_status_color_code 		= $row_get_sup_status['color_code'];
				$sup_status_icon 			= $row_get_sup_status['icon'];
			}



			// GET PART CLASSIFICATION:
			$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $sup_part_classification . "'";
			// echo $get_part_class_SQL;

			$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
			// while loop
			while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
				$part_class_EN 				= $row_get_part_class['name_EN'];
				$part_class_CN 				= $row_get_part_class['name_CN'];
				$part_class_description 	= $row_get_part_class['description'];
				$part_class_color 			= $row_get_part_class['color'];
			}

} // end get user info WHILE loop

?>

<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Supplier Profile - <?php echo $sup_en;
        if (($sup_cn!='')&&($sup_cn!='中文名')){
         	?> / <?php echo $sup_cn;
        }
        ?></h2>

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
                <option value="#" selected="selected">JUMP TO ANOTHER SUPPLIER / 看别的供应商:</option>
                <option value="suppliers.php">View All / 看全部</option>
                <?php

                $get_j_sups_SQL = "SELECT * FROM `suppliers`";
                // echo $get_j_sups_SQL;

                $result_get_j_sups = mysqli_query($con,$get_j_sups_SQL);
					  		// while loop
                while($row_get_j_sup = mysqli_fetch_array($result_get_j_sups)) {

                    $j_sup_ID = $row_get_j_sup['ID'];
                    $j_sup_en = $row_get_j_sup['name_EN'];
                    $j_sup_cn = $row_get_j_sup['name_CN'];

							   ?>
                <option value="supplier_view.php?id=<?php echo $j_sup_ID; ?>"><?php echo $j_sup_en; if (($j_sup_cn != '')&&($j_sup_cn != '中文名')) { ?> / <?php echo $j_sup_cn; } ?></option>
                <?php
							  } // end get supplier list
							  ?>
                <option value="suppliers.php">View All / 看全部</option>
            </select>
            <!-- / Supplier JUMPER -->
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <!-- START MAIN BODY COLUMN: -->
    <div class="col-md-12">



        <div class="row">

				<div class="col-md-4 col-lg-3">


							<?php
							// now run the admin bar function:
							admin_bar('supplier');
							?>

				<section class="panel">
						<header class="panel-heading">
							<div class="panel-actions">
								<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
								<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
							</div>

							<h2 class="panel-title">
								<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
								<span class="va-middle">General Details</span>
							</h2>
						</header>
						<div class="panel-body">
							<div class="content">


							<ul class="simple-card-list mb-xlg">
								<li class="<?php echo $sup_status_color_code; ?>">
									<h3><?php echo $sup_status_name_EN; if (($sup_status_name_CN!='')&&($sup_status_name_CN!='中文名')) { ?> / <?php echo $sup_status_name_CN; }?></h3>
									<p>Supplier Status</p>
								</li>
								<li class="<?php echo $part_class_color; ?>">
									<h3><?php echo $part_class_EN; ?> / <?php echo $part_class_CN; ?></h3>
									<p>Vendor Classification</p>
								</li>
								<li class="warning">
									<h3><?php echo $sup_evaluation_date; ?></h3>
									<p>Next Evaluation</p>
								</li>
							</ul>
						  </div>
						</div>
						  <div class="panel-footer">
							<div class="text-right">
								<a class="text-uppercase text-muted" href="#">(Edit)</a>
							</div>
						  </div>
					</section>



					<section class="panel">
						<header class="panel-heading">
							<div class="panel-actions">
								<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
								<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
							</div>

							<h2 class="panel-title">
								<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-envelope"></i></span>
								<span class="va-middle">Contact Details</span>
							</h2>
						</header>
						<div class="panel-body">
							<div class="content">
								<ul>
								  <li>
									<strong>Address:</strong>
									<?php echo $sup_address_EN; ?>
								  </li>
								  <li>
									<strong>地址:</strong>
									<?php echo $sup_address_CN; ?>
								  </li>
								  <li>
									<strong>Country / 国家:</strong>
									<?php echo $sup_address_CN; ?>
								  </li>
								  <li>
									<strong>Phone / 电话:</strong>
									<?php echo $sup_telephone; ?>
								  </li>
								  <li>
									<strong>Fax:</strong>
									<?php echo $sup_fax; ?>
								  </li>
								  <li>
									<strong>Website:</strong>
									<a href="<?php echo $sup_web; ?>" target="_blank" title="Launch in a new window"><?php echo $sup_web; ?></a>
								  </li>
								</ul>
						  </div>
						</div>
						  <div class="panel-footer">
							<div class="text-right">
								<a class="text-uppercase text-muted" href="#">(Edit)</a>
							</div>
						  </div>
					</section>

					<section class="panel">
						<header class="panel-heading">
							<div class="panel-actions">
								<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
								<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
							</div>

							<h2 class="panel-title">
								<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-certificate"></i></span>
								<span class="va-middle">Certificates</span>
							</h2>
						</header>

						<div class="panel-body">
							<div class="content">
								<ul class="simple-user-list">
									<li>
										<figure class="image rounded">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe Junior" class="img-circle" />
										</figure>
										<span class="title"><?php echo $sup_certs; ?></span>
										<span class="message truncate"><?php echo $sup_cert_exp_date; ?></span>
									</li>
								</ul>
						  </div>
						</div>
						  <div class="panel-footer">
							<div class="text-right">
								<a class="text-uppercase text-muted" href="#">(Edit)</a>
							</div>
						  </div>
					</section>

			</div>



        <div class="col-md-8 col-lg-9">

        <div class="row">

            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                </div>

                <h2 class="panel-title"><?php echo $sup_en; if (($sup_cn!='') && ($sup_cn!='中文名')) { ?> / <?php echo $sup_cn; } ?></h2>
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
                            <td><?php  if (($sup_cn!='') && ($sup_cn!='中文名')) { ?> / <?php echo $sup_cn; } else { echo '<span class="text-danger">没有中文公司名字</span>'; } ?></td>
                        </tr>
                        <tr>
                            <th>Supplier #:</th>
                            <td><?php echo $sup_internal_ID; ?></td>
                        </tr>
                        <tr>
                            <th>Items Supplied:</th>
                            <td><?php echo '<em>coming soon</em>'; ?></td>
                        </tr>
                        <tr>
                            <th>Part Type:</th>
                            <td><?php echo '<em>coming soon</em>'; ?></td>
                        </tr>
                        <tr>
                            <th>Record Status:</th>
                            <td><?php record_status($sup_record_status); ?></td>
                        </tr>
                    </table>
                </div>

            </div>

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
