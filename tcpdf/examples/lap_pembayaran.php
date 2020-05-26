<?php

include "../../koneksi/config.php";
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Kamandanu');
$pdf->SetTitle('Laporan Pelanggan');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData( PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' ', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 13, PDF_MARGIN_RIGHT);
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
<h2>Laporan Data Pembayaran</h2>
OED;

$pdf->WriteHTMLCell(0,0,'','',$title,0,1,0,true,'C',true);


$table = '<table style="border:1px solid #000; padding:6px;">
		<tr style = "background-color:#ccc;">
			<th style="border:1px solid #000; width:50px;">No.</th>
			<th style="border:1px solid #000; width:250;">Nama</th>
			<th style="border:1px solid #000; width:100;">No. Inet</th>
			<th style="border:1px solid #000; width:150;">Paket</th>
			<th style="border:1px solid #000; width:250;">Add On</th>
			<th style="border:1px solid #000; width:150;">Total Bayar</th>
		</tr>';

			$sql = mysqli_query($con,"SELECT * FROM tbl_pelanggan 
			inner join tbl_paket on tbl_pelanggan.id_paket = tbl_paket.id_paket 
			where status = 'Aktif' or status = 'Menunggu Berhenti'");
			$no=1;
			$total_pembayaran = 0;
			while ($pelanggan = mysqli_fetch_array($sql)) {
				$q_paket = mysqli_query($con, "select * from tbl_pelanggan inner join tbl_paket on tbl_pelanggan.id_paket = tbl_paket.id_paket 
				where tbl_pelanggan.no_inet = '$pelanggan[no_inet]' and tbl_pelanggan.status = 'Aktif' 
				or tbl_pelanggan.no_inet = '$pelanggan[no_inet]' and tbl_pelanggan.status = 'Menunggu Berhenti'");
				$paket = mysqli_fetch_array($q_paket);
				$bayar_paket = $paket['harga'];

				$q_upgrade = mysqli_query($con, "SELECT sum(tbl_addon.harga) as total FROM tbl_upgrade 
				inner join tbl_addon on tbl_upgrade.id_addon = tbl_addon.id_addon
				where tbl_upgrade.no_inet = '$pelanggan[no_inet]' and tbl_upgrade.status = 'Aktif' 
				or tbl_upgrade.no_inet = '$pelanggan[no_inet]' and tbl_upgrade.status = 'Menunggu Berhenti'");
				$upgrade = mysqli_fetch_array($q_upgrade);
				$bayar_upgrade = $upgrade['total'];

				$t_bayar = $bayar_paket + $bayar_upgrade;

				$total_pembayaran = $total_pembayaran + $t_bayar;

				$total_bayar = number_format($t_bayar,2,',','.');
								$table .='
								<tr>
									<td style="border:1px solid #000;">'.$no++.'</td>
									<td style="border:1px solid #000;">'.$pelanggan['nama_pelanggan'].'</td>
									<td style="border:1px solid #000;">'.$pelanggan['no_inet'].'</td>
									<td style="border:1px solid #000;">'.$pelanggan['nama_paket'].'</td>
									<td style="border:1px solid #000;">';

									$q_upgrade = mysqli_query($con, "select * from tbl_upgrade 
									inner join tbl_addon on tbl_upgrade.id_addon = tbl_addon.id_addon 
									where tbl_upgrade.no_inet = '$pelanggan[no_inet]' and tbl_upgrade.status = 'Aktif' 
									or tbl_upgrade.no_inet = '$pelanggan[no_inet]' and tbl_upgrade.status = 'Menunggu Berhenti'");
									while($upgrade = mysqli_fetch_array($q_upgrade)){
										$table .= $upgrade[nama_layanan];
									}
								$table .='
									</td>			
									<td style="border:1px solid #000;">Rp. '.$total_bayar.'</td>
								</tr>';
								
								}
			
				
$table .= '		<tr>
<th colspan="5" style="border:1px solid #000;">Total</th>
<th style="border:1px solid #000;">Rp. '.number_format($total_pembayaran,2,',','.').'</th>
</tr></table>';

$pdf->WriteHTMLCell(0,0,'','',$table,0,1,0,true,'C',true);




// move pointer to last page
$pdf->lastPage();

// ---------------------------------------------------------
ob_clean();
//Close and output PDF document
//$judul	= '/Penawaran'.'_'.$idsuplier.'.pdf';
//$pdf->IncludeJS("print();");
//$pdf->Output(__DIR__.'/LaporanPengaduan.pdf', 'FD');
//$pdf->Output($judul, 'I');

$pdf->Output('Laporan Pengaduan.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

?>