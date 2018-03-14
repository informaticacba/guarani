<?php 
	require_once('fpdf.php');
 	require_once('fpdi.php');
	// initiate FPDI
	$pdf = new FPDI();
	// add a page
	$pdf->AddPage();
	// set the sourcefile
	$pdf->setSourceFile('modelo.pdf');
	// import page 1
	$tplIdx = $pdf->importPage(1);
	// use the imported page and place it at point 10,10 with a width of 100 mm
	$pdf->useTemplate($tplIdx,null,null,null,null,true);
	
	$texto_cabecera = "por haber cumplido la asistencia al evento sarasa...";
	$apellido = "GONZALEZ";
	$nombres ="MARIANO GERMAN";
	
	$nombres= strtoupper($nombres);
	$apellido = strtoupper($apellido);
	$nom_count = strlen($apellido.$nombres);
	
	//formula para cuadrar el nombre en el certificado dependiendo su longitud
	if($nom_count>50)
		$x = 55;
	else{
		$nombre_espacio = 60;
	if($nom_count<23)
		$nombre_espacio = 72;
		$x= $nombre_espacio + (22-$nom_count);
	}

	/* Texto de Cabecera */
	$pdf->SetFont('Arial','i', 14);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetXY(65, 47);
	$pdf->Write(0, $texto_cabecera);

	/* NOMBRE DE LA PERSONA */
	$pdf->SetFont('Arial','B', 30);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetXY($x, 90);
	$pdf->Write(0, $apellido.", ".$nombres);
	
	$pdf->Output('diplomas/'.$apellido.", ".$nombres." (".time().').pdf','F');
?>