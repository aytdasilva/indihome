<?php

include "../../koneksi/config.php";

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Kamandanu');
$pdf->SetTitle('Laporan Rekap Pelanggan');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' ', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', '', 9);

// add a page
$pdf->AddPage('L');

// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);

// set color for background
$pdf->SetFillColor(255, 255, 127);

// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

$title = <<<OED
<h2>Laporan Rekap Pelanggan</h2>
OED;


$pdf->WriteHTMLCell(0,0,'','',$title,0,1,0,true,'C',true);


$table = '<table style="border:1px solid #000; padding:6px;">
		<tr style = "background-color:#ccc;">
			<th style="border:1px solid #000; width:50px;" rowspan="2">No.</th>
			<th style="border:1px solid #000; width:300px;" rowspan="2">Wilayah</th>
			<th style="border:1px solid #000; width:600px;" colspan="4">Status Pelanggan</th>
		</tr>
		<tr style = "background-color:#ccc;">
			<th style="border:1px solid #000; width:150px;">Total</th>
			<th style="border:1px solid #000; width:150px;">Pending</th>
			<th style="border:1px solid #000; width:150px;">Aktif</th>
			<th style="border:1px solid #000; width:150px;">Berhenti</th>
		</tr>';

$sql_kab_kota = mysqli_query($con,"SELECT * FROM tbl_kab_kota where id_provinsi = '63' ORDER BY id_kab_kota ASC");
$no = 1;                   
while($rs_kab_kota = mysqli_fetch_array($sql_kab_kota)){
	$q_total = mysqli_query($con, "SELECT count(*) as j_total FROM tbl_pelanggan 
	WHERE tbl_pelanggan.id_kab_kota = '$rs_kab_kota[id_kab_kota]'");
	$total = mysqli_fetch_array($q_total);
	if($total['j_total'] == 0){
		$j_total = '-';
	}else{
		$j_total = $total['j_total'];
	}

	$q_pending = mysqli_query($con, "SELECT count(*) as j_pending FROM tbl_pelanggan 
	WHERE tbl_pelanggan.id_kab_kota = '$rs_kab_kota[id_kab_kota]' and tbl_pelanggan.status = 'Belum Aktif'");
	$pending = mysqli_fetch_array($q_pending);
	if($pending['j_pending'] == 0){
		$j_pending = '-';
	}else{
		$j_pending = $pending['j_pending'];
	}

	$q_aktif = mysqli_query($con, "SELECT count(*) as j_aktif FROM tbl_pelanggan 
	WHERE tbl_pelanggan.id_kab_kota = '$rs_kab_kota[id_kab_kota]' and tbl_pelanggan.status = 'Aktif'");
	$aktif = mysqli_fetch_array($q_aktif);
	if($aktif['j_aktif'] == 0){
		$j_aktif = '-';
	}else{
		$j_aktif = $aktif['j_aktif'];
	}

	$q_berhenti = mysqli_query($con, "SELECT count(*) as j_berhenti FROM tbl_pelanggan 
	WHERE tbl_pelanggan.id_kab_kota = '$rs_kab_kota[id_kab_kota]' and tbl_pelanggan.status = 'Berhenti'");
	$berhenti = mysqli_fetch_array($q_berhenti);
	if($berhenti['j_berhenti'] == 0){
		$j_berhenti = '-';
	}else{
		$j_berhenti = $berhenti['j_berhenti'];
	}


	$table .= '<tr>
			<td style="border:1px solid #000;">'.$no++.'</td>
			<td style="border:1px solid #000;">'.$rs_kab_kota['nama_kab_kota'].'</td>
			<td style="border:1px solid #000;">'.$j_total.'</td>
			<td style="border:1px solid #000;">'.$j_pending.'</td>
			<td style="border:1px solid #000;">'.$j_aktif.'</td>
			<td style="border:1px solid #000;">'.$j_berhentil.'</td>
		</tr>';
}
			
				
$table .= '</table>';

$pdf->WriteHTMLCell(0,0,'','',$table,0,1,0,true,'C',true);

$today = date("d-m-Y");
$ttd = '<table>';
$ttd .= '
		<tr style="padding:30px;">
			<th>Mengetahui</th>
			<th></th>
			<th>Banjarmasin '.$today.'</th>
		</tr>
		<tr>
			<th>Manager</th>
			<th></th>
			<th>Kepala Bagian</th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		<tr style="padding:30px;">
			<th style="padding:30px;"></th>
			<th></th>
			<th></th>
		</tr>
		<tr style="padding:2px;">
			<th><u>Reza Abdillah</u></th>
			<th></th>
			<th><u>Riyan Maulana</u></th>
		</tr>
		<tr style="padding:1px;">
			<th>NIP. 2088913340012</th>
			<th></th>
			<th>NIP. 2088913340033</th>
		</tr>';

$ttd .= '</table>';
$pdf->WriteHTMLCell(0,0,'','',$ttd,0,1,0,true,'C',true);



// move pointer to last page
$pdf->lastPage();

// ---------------------------------------------------------
ob_clean();
//Close and output PDF document
//$judul	= '/Penawaran'.'_'.$idsuplier.'.pdf';
//$pdf->IncludeJS("print();");
//$pdf->Output(__DIR__.'/LaporanPengaduan.pdf', 'FD');
//$pdf->Output($judul, 'I');

$pdf->Output('Laporan Gangguan.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

?>