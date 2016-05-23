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

require ('page_functions.php'); 
include 'db_conn.php';

/* -- NO USER SESSIONS YET...
if (isset($_SESSION['user_id'])) {
	header("Location: user_home.php"); // send them to the user home...
}
*/

$page_id = 4;

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else { // no id = nothing to see here!	
	header("Location: products.php?msg=NG&action=view&error=no_id");
	exit();		
}

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>InsuJet&trade; - Basic Pen</h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Products</span></li>
								<li><span>InsuJet&trade; - Basic Pen</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>
					
					<h1>DEV NOTE: This is a hard-coded page - it needs to be made dynamic! Sorry for the delay.</h1>

					<!-- start: page -->
					
					<h2>Product Break Down:</h2>
					<table class="table table-bordered table-striped mb-none">
					  <tr>
					    <th>Level</th>
					    <th>Type</th>
					    <th>Code</th>
					    <th>Name</th>
					    <th>名字</th>
					    <th>Ver.</th>
					    <th>Photo</th>
					  </tr>
					  
					  <tr>
					    <td><a href="part_view.php?id=1">1</a></td>
					    <td>Assembly</td>
					    <td><a href="part_view.php?id=1">01012</a></td>
					    <td><a href="part_view.php?id=1">InsuJet&trade; Basic Final Assembly (Lime)</a></td>
					    <td><a href="part_view.php?id=1">中文名</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=1"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>2</td>
					    <td>Assembly</td>
					    <td><a href="part_view.php?id=2">01010</a></td>
					    <td><a href="part_view.php?id=2">Front (Lime)</a></td>
					    <td><a href="part_view.php?id=2">前体组件</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=2"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>3</td>
					    <td>Assembly</td>
					    <td><a href="part_view.php?id=9">01007</a></td>
					    <td><a href="part_view.php?id=9">Central Assembly</a></td>
					    <td><a href="part_view.php?id=9">螺纹管组件</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=9"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>4</td>
					    <td>Assembly</td>
					    <td><a href="part_view.php?id=16">01003</a></td>
					    <td><a href="part_view.php?id=16">Mainbody Assembly</a></td>
					    <td><a href="part_view.php?id=16">主体组件</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=16"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>4</td>
					    <td>Assembly</td>
					    <td><a href="part_view.php?id=17">01005</a></td>
					    <td><a href="part_view.php?id=17">Cylinder Assembly</a></td>
					    <td><a href="part_view.php?id=17">螺纹管组件</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=17"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>4</td>
					    <td>Assembly</td>
					    <td><a href="part_view.php?id=18">01006</a></td>
					    <td><a href="part_view.php?id=18">Clicker House Assembly</a></td>
					    <td><a href="part_view.php?id=18">字体套组件</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=18"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>4</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=19">01401</a></td>
					    <td><a href="part_view.php?id=19">Main Power Spring</a></td>
					    <td><a href="part_view.php?id=19">主体弹簧</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=19"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>3</td>
					    <td>Assembly</td>
					    <td><a href="part_view.php?id=10">01009</a></td>
					    <td><a href="part_view.php?id=10">Nozzle Twist Cap Assembly</a></td>
					    <td><a href="part_view.php?id=10">喷嘴套组件</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=10"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>3</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=11">01104</a></td>
					    <td><a href="part_view.php?id=11">Front Body Housing Key</a></td>
					    <td><a href="part_view.php?id=11">前体定位销</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=11"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>3</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=12">01402</a></td>
					    <td><a href="part_view.php?id=12">Nozzle Lock Circlip</a></td>
					    <td><a href="part_view.php?id=12">卡环</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=12"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>3</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=13">01302</a></td>
					    <td><a href="part_view.php?id=13">Retaining Cap</a></td>
					    <td><a href="part_view.php?id=13">保持盖</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=13"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>3</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=14">01303</a></td>
					    <td><a href="part_view.php?id=14">Lock Yoke</a></td>
					    <td><a href="part_view.php?id=14">锁扣</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=14"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>3</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=15">01317</a></td>
					    <td><a href="part_view.php?id=15">Front Body</a></td>
					    <td><a href="part_view.php?id=15">前体</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=15"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>2</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=3">01312</a></td>
					    <td><a href="part_view.php?id=3">Release Paddle</a></td>
					    <td><a href="part_view.php?id=3">释放片</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=3"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>2</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=4">01316</a></td>
					    <td><a href="part_view.php?id=4">Plunger Mistral (Back Body)</a></td>
					    <td><a href="part_view.php?id=4">后体</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=4"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>2</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=5">01314</a></td>
					    <td><a href="part_view.php?id=5">Drive Plate</a></td>
					    <td><a href="part_view.php?id=5">驱动板</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=5"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>2</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=6">01315</a></td>
					    <td><a href="part_view.php?id=6">Latch Cover</a></td>
					    <td><a href="part_view.php?id=6">后(栓)盖</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=6"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>2</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=7">01320</a></td>
					    <td><a href="part_view.php?id=7">Plunger Assist Pin</a></td>
					    <td><a href="part_view.php?id=7">发射杆</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=7"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					  <tr>
					    <td>2</td>
					    <td>Part</td>
					    <td><a href="part_view.php?id=8">01405</a></td>
					    <td><a href="part_view.php?id=8">Plunger Spring</a></td>
					    <td><a href="part_view.php?id=8">活塞弹簧</a></td>
					    <td>1.0</td>
					    <td><a href="part_view.php?id=8"><img src="assets/images/parts/01312.png" title="Part Photo" style="width:100px;" /></a></td>
					  </tr>
					  
					</table>
					
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>