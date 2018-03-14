<?php 
require_once 'Pdf_html.php';

class Pdf_cert_pendientes extends Pdf_html{
	private $pendientes;

	//constructor de clase
	function __construct(){
		
		//constructor de la clase padre
		parent::__construct();
		//agrego una hoja al documento
		$this->Addpage('P','Legal');

		

	}



	public function Header(){
		$this->SetTitle("Certificados Pendientes");
		
	}

	public function set_pendientes($pendientes){
		$this->pendientes = $pendientes;
		$this->cuerpo();

	}

	public function cuerpo(){
		$meses = array('01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre');
		$this->SetFont('Times','bu',12);
		$this->SetXY(0,20);
		$this->Cell(0,0,"COMPLETO - TURNO TARDE (".date("d")." de ".$meses[date("m")]." de ".date("Y").")", 0, 1, "C", false);
		
		if(count($this->pendientes) == 0){
			
			$this->Cell(0,0,"NO HAY CERTIFICADOS PENDIENTES DE IMPRESION", 0, 1, "C", false);
		}else{
			$this->SetFont('Times','B',11);
			$this->setY(30);
			$this->SetLeftMargin(25);
			$this->SetFillColor(100,100,100);
			$this->SetTextColor(230,230,230);
			
			$this->Cell(20,5,"LU",1,0,"C",true);
			$this->Cell(25,5,"AGR",1,0,"C",true);
			$this->Cell(60,5,"APELLIDO",1,0,"C",true);
			$this->Cell(60,5,"NOMBRES",1,1,"C",true);

			$this->SetFillColor(255,255,255);
			$this->SetTextColor(0,0,0);
			$this->SetFont('Times','',10);

			foreach ($this->pendientes as $key => $value) {
				$this->Cell(20,5,$value['legajo'],1,0,"C",false);
				$this->Cell(25,5,$value['agr'],1,0,"C",false);
				$this->Cell(60,5,$value['apellido'],1,0,"L",false);
				$this->Cell(60,5,$value['nombres'],1,1,"L",false);
								
			}
			
		}
		
	}

}

?>
