<?php 
require_once("Fpdf.php");
class Pdf extends Fpdf{
	private $materia;

	function Header(){
		// Encabezado de la página
		
		$this->SetFont('Arial','B',13);
		$this->SetFillColor(0, 103, 0);
		$this->SetTextColor(255,255,255);
		$this->Cell(0,12,'Alumnos inscritos al examen de '.utf8_decode($this->materia),1,1,'C',true);
		
		$this->Ln();
	}

	public function set_materia($materia){
		$this->materia = $materia;
	}

	function Footer(){
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}
}
 ?>