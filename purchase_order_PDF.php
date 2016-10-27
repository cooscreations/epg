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
include ('qrcode-generator/index_2.php');
include ('fpdf181/fpdf.php');

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 10;

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: purchase_orders.php?msg=NG&action=view&error=no_id");
	exit();
}

$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $record_id;
$result_get_PO = mysqli_query($con,$get_PO_SQL);
// while loop
while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

		// now print each record:
		$PO_id 						= $row_get_PO['ID'];
		$PO_number 					= $row_get_PO['PO_number'];
		$GLOBALS['PO_number'] 		= $PO_number;
		$PO_created_date 			= $row_get_PO['created_date'];
		$PO_description 			= $row_get_PO['description'];
		$PO_record_status 			= $row_get_PO['record_status'];
		$PO_supplier_ID 			= $row_get_PO['supplier_ID'];  				// LOOK THIS UP!
		$PO_created_by 				= $row_get_PO['created_by']; 				// use get_creator($PO_created_by);
		$PO_date_needed 			= $row_get_PO['date_needed'];
		$PO_date_delivered 			= $row_get_PO['date_delivered'];
		$PO_approval_status 		= $row_get_PO['approval_status']; 			// look this up?
		$PO_payment_status 			= $row_get_PO['payment_status']; 			// look this up?
		$PO_completion_status 		= $row_get_PO['completion_status'];
			
		// ADDING NEW VARIABLES AS WE EXPAND THIS PART OF THE SYSTEM:
		$PO_remark 					= $row_get_PO['remark'];
		$PO_approved_by 			= $row_get_PO['approved_by']; 				// use get_creator($PO_approved_by);
		$PO_approval_date 			= $row_get_PO['approval_date']; 
		$PO_include_CoC 			= $row_get_PO['include_CoC'];
		$PO_date_confirmed 			= $row_get_PO['date_confirmed'];
		$PO_ship_via 				= $row_get_PO['ship_via'];
		$PO_special_reqs 			= $row_get_PO['special_reqs'];
		$PO_related_standards 		= $row_get_PO['related_standards'];
		$PO_special_contracts 		= $row_get_PO['special_contracts'];
		$PO_qualification_personnel = $row_get_PO['qualification_personnel'];
		$PO_QMS_reqs 				= $row_get_PO['QMS_reqs'];
		$PO_local_location_ID 		= $row_get_PO['local_location_ID'];			// use function: get_location($PO_local_location_ID,1);
		$PO_HQ_location_ID 			= $row_get_PO['HQ_location_ID'];			// use function! get_location($PO_HQ_location_ID,1);
		$PO_ship_to_location_ID		= $row_get_PO['ship_to_location_ID'];		// use function! get_location($PO_ship_to_location_ID,0); (show title ONLY)

		// ADDING NEW VARIABLES - DEFAULT CURRENCY!
		
		$PO_default_currency		= $row_get_PO['default_currency']; // look this up!
		$PO_default_currency_rate	= $row_get_PO['default_currency_rate'];
		
				// now get the currency info
				$get_PO_default_currency_SQL = "SELECT * FROM `currencies` WHERE `ID` ='" . $PO_default_currency . "'";
				// debug:
				// echo '<h3>'.$get_PO_default_currency_SQL.'<h3>';
				$result_get_PO_default_currency = mysqli_query($con,$get_PO_default_currency_SQL);
				// while loop
				while($row_get_PO_default_currency = mysqli_fetch_array($result_get_PO_default_currency)) {

					// now print each result to a variable:
					$PO_default_currency_ID 			= $row_get_PO_default_currency['ID'];
					$PO_default_currency_name_EN		= $row_get_PO_default_currency['name_EN'];
					$PO_default_currency_name_CN		= $row_get_PO_default_currency['name_CN'];
					$PO_default_currency_one_USD_value	= $row_get_PO_default_currency['one_USD_value'];
					$PO_default_currency_symbol			= $row_get_PO_default_currency['symbol'];
					$PO_default_currency_abbreviation	= $row_get_PO_default_currency['abbreviation'];
					$PO_default_currency_record_status	= $row_get_PO_default_currency['record_status'];

				}

		// count variants for this purchase order
        $count_batches_sql 		= "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $record_id;
        $count_batches_query 	= mysqli_query($con, $count_batches_sql);
        $count_batches_row 		= mysqli_fetch_row($count_batches_query);
        $total_batches 			= $count_batches_row[0];

} // end while loop

/*
$pdf = new FPDF('P','mm','A4'); // in fact, these are the default parameters, but it's good to state them here anyway :)
$pdf->AddPage();
$pdf->SetFont('Helvetica','B',16);
$pdf->Cell(40,10,$PO_number);
$pdf->Output();

*/

// MAKING PAGES, PAGE NUMBERS AND THE LOGO!

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    $this->Image('assets/images/logo.png',10,6,30);
    // Arial bold 15
    $this->SetFont('Helvetica','B',15);
    // Move to the right
    $this->Cell(80);
    // Title
    $PDF_title= "Purchase Order " . $GLOBALS['PO_number'];
    $this->Cell(60,10,$PDF_title,1,0,'C');
    // Line break
    $this->Ln(20);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
for($i=1;$i<=40;$i++)
    $pdf->Cell(0,10,'Printing line number '.$i,0,1);
$pdf->Output();
?>


/*

?>
	