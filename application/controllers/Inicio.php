<?php
//use Spipu\Html2Pdf\Html2Pdf;
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Guarani_model");
		$this->load->library("fpdf/Pdf");
		$this->load->library("fpdf/Pdf_const_ingresante");
		$this->load->library("fpdf/Pdf_cert_pendientes");
		$this->load->library("fpdf/Pdf_tiempos_materias");
		$this->load->library('generador_plan/Generador_plan');
		$this->load->library("Persona");
		//$this->Guarani_model->cant_mat_obligatorias('9969'); die;
		//$datos = $this->Guarani_model->tiempos_materias(2011,array('50','51.'));
		//var_dump($datos); die;
		//$reporte = new PDF_tiempos_materia();

		//$this->Guarani_model->desercion(); die;
		
		

	}
	//funcion auxiliar que no se utiliza
	public function curs(){
		$ubicacion = "./assets/materias.json";
		$materias = file_get_contents($ubicacion);
		return $materias;
	}


	public function get_datos_personales($agr){
		
		//echo json_encode(array("nombre"=>"Federico"));
		echo json_encode($this->Guarani_model->get_datos($agr));
	}

	public function index(){
		//$this->Guarani_model->pruebas(); die;
		$this->load->view('inicio_view');

	}

	public function informe_encuesta(){
		$id_alcance = $this->input->post("alcance");
		$id_materia = $this->input->post("materia");
		$legajo_docente = $this->input->post("docente");
		
		$materia = $this->Guarani_model->obtener_materia($id_materia);
		$docente = $this->Guarani_model->obtener_docente($legajo_docente);
		$respuestas = $this->Guarani_model->obtener_respuestas($id_alcance,$id_materia,$legajo_docente);
		
		$observaciones = $this->Guarani_model->obtener_observaciones($id_alcance, $id_materia, $legajo_docente);
		$registros = array();
		if(count($respuestas) > 0){
			while($respuesta = array_shift($respuestas) ){
					$apellido = (array_key_exists('apellido',$respuesta))?$respuesta['apellido']:NULL;
					$nombres = (array_key_exists('nombres',$respuesta))?$respuesta['nombres']:NULL;
					$registros[] = array("pregunta"=>utf8_encode($respuesta['pregunta']),"opcion"=>utf8_encode($respuesta['opcion']),"apellido"=>utf8_encode($apellido),"nombres"=>utf8_encode($nombres),"cantidad"=>utf8_encode($respuesta['cantidad'])  ); 
			}
			$this->load->view("reportes/informe_encuesta",array("datos"=>$registros,"datos_docente"=>$docente,"materia"=>$materia,"observaciones"=>$observaciones));
		}else{
			$this->load->view("reportes/informe_encuesta",array());
		} 
	}

	public function ver_reporte($reporte, $apellido, $nombres, $dni, $modalidad, $sexo, $obligatorias = NULL, $optativas = NULL,$nota = NULL, $autor = NULL, $materia = NULL, $fecha_examen = NULL, $legajo = NULL){
		switch ($reporte) {
			case 'realizar_pasantia':
				$this->load->view('reportes/cert_realizar_tfg',array("apellido"=>$apellido,"nombres"=>$nombres,"dni"=>$dni,"modalidad"=>"pasantia","sexo"=>$sexo,"obligatorias"=>$obligatorias,"optativas"=>$optativas,"autor"=>$autor));
				break;
			case 'realizar_tesina':
				$this->load->view('reportes/cert_realizar_tfg',array("apellido"=>$apellido,"nombres"=>$nombres,"dni"=>$dni,"modalidad"=>"tesina","sexo"=>$sexo,"obligatorias"=>$obligatorias,"optativas"=>$optativas,"autor"=>$autor));
				break;
			case 'rendir_pasantia':
				$this->load->view('reportes/cert_rendir_tfg',array("apellido"=>$apellido,"nombres"=>$nombres,"dni"=>$dni,"modalidad"=>"pasantia","sexo"=>$sexo,"autor"=>$autor));
				break;
			case 'rendir_tesina':
				$this->load->view('reportes/cert_rendir_tfg',array("apellido"=>$apellido,"nombres"=>$nombres,"dni"=>$dni,"modalidad"=>"tesina","sexo"=>$sexo,"autor"=>$autor));
				break;
			case 'examen':
				
				$this->load->view('reportes/cert_examen',array("apellido"=>$apellido,"nombres"=>$nombres,"dni"=>$dni,"nota"=>$nota,"autor"=>$autor,"materia"=>$materia,"fecha"=>$fecha_examen,"legajo"=>$legajo));
				break;
		}
		
	}

	public function pdf_inscritos_examen(){
		//obtengo una lista de los nombres que debe tener cada archivo PDF
		$nombres_archivo = $this->Guarani_model->get_nombres_archivos();

		//obtengo los codigos de la materia, el turno y llamado seleccionados por el usuario
		$codigos_materia = $this->input->post("insc_materia");
		$llamado = $this->input->post("insc_llamado");
		$turno = $this->input->post("insc_turno");
		//echo $codigos_materia; die;
		//Obtengo el nombre de la materia en funcion a los codigos seleccionados
		$materia = $this->Guarani_model->get_nombre_materia($codigos_materia);

		//obtengo los inscritos correspondientes
		$inscritos = $this->Guarani_model->get_inscritos_examen($codigos_materia,$turno,$llamado);
		//var_dump($nombres_archivo); die;
		//var_dump($materia); die;
		$nombre_archivo = $nombres_archivo[$materia];
		//echo $materia; die;

		/*$inscritos = array(
							array("legajo"=>"10237","alumno"=>"Alemany, Marcelo Federico", "condicion"=>"Regular"),
							array("legajo"=>"10237","alumno"=>"Alemany, Marcelo Federico", "condicion"=>"Regular"),
							array("legajo"=>"10237","alumno"=>"Alemany, Marcelo Federico", "condicion"=>"Regular"),
							array("legajo"=>"10237","alumno"=>"Alemany, Marcelo Federico", "condicion"=>"Regular")
							);*/


		//ancho de las columnas
		$ancho_orden = 15;
		$ancho_legajo = 15;
		$ancho_alumno = 130;
		$ancho_condicion = 30;
		$alto_fila = 8;

		//creo el pdf
		$pdf = new $this->pdf("Portrait","mm","A4");
		$pdf->SetTitle($materia, TRUE);
		$pdf->set_materia($materia);

		//encabezado y pie de páginas
		$pdf->Header();
		$pdf->AliasNbPages();
		$pdf->AddPage();

		//Encabezado de la tabla de inscritos
		$pdf->SetFont('Arial','B',10);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell($ancho_orden,$alto_fila,'#',1,0,'C');
		$pdf->Cell($ancho_legajo,$alto_fila,'L.U.',1,0,'C');
		$pdf->Cell($ancho_alumno,$alto_fila,'Apellido y Nombres',1,0,'C');
		$pdf->Cell($ancho_condicion,$alto_fila,'Condicion',1,1,'C');

		//Cuerpo de la tabla
		$pdf->SetFont('Arial','',8);
		$par = TRUE;
		$orden = 1;
		foreach ($inscritos as $value) {
			//controla el color de fondo de la fila para lograr tabla cebra
			if($par){
				$pdf->SetFillColor(230, 230, 230);
			}else{
				$pdf->SetFillColor(250, 250, 250);
			}
			$par = !$par;
			/* ------------------------------------------------------------ */
			$pdf->Cell($ancho_orden,$alto_fila,$orden,1,0,'C',TRUE);
			$pdf->Cell($ancho_legajo,$alto_fila,$value['legajo'],1,0,'C',TRUE);
			$pdf->Cell($ancho_alumno,$alto_fila,"     ".$value['alumno'],1,0,'L',TRUE);
			$pdf->Cell($ancho_condicion,$alto_fila,$value['condicion'],1,1,'C',TRUE);
			$orden++;
		}
		
		//visualizar en el navegador o publicarlo en el sitio web de la facultad
		if($this->input->post("publicar") == 1){
			$pdf->Output("./assets/pdfs/examenes/".$nombre_archivo.".pdf","F");
			$this->subir_archivo($nombre_archivo);
		}else{
			$pdf->Output($nombre_archivo.".pdf","I");
		}
		

	}

	function generar_plan_estudios($legajo,$carrera)
	{
		
		$d = $this->Guarani_model->get_datos_personales($legajo,$carrera);
		if( ! count($d)){
			die("No se encontró el legajo ingresado en la carrera seleccionada. Prestá mas atención mi gente!");
		}
		$ingreso = explode('-',$d['fecha_ingreso']);

		$reporte = new Generador_plan();
		$reporte->set_datos($d['apellido'],$d['nombres'],$d['nro_documento'],$d['sexo'],$carrera,$ingreso[0]);
		$reporte->generar_reporte();
		
		
		
	}

	private function subir_archivo($nombre_archivo){
		
		
	}

	public function pdf_const_ingresante($legajo, $nro_inscripcion, $tipodoc, $nro_documento, $apellido, $nombres, $sexo, $tipo_constancia){
		//creo el pdf
		$p = new PDF_const_ingresante(array('legajo'      	  => $legajo, 
											'nro_inscripcion' => $nro_inscripcion,
											'nro_documento'   => $nro_documento, 
											'tipo_documento'  => $tipodoc,
											'apellido'        => $apellido, 
											'nombres'         => $nombres, 
											'sexo'            => $sexo,
											'tipo_constancia' => $tipo_constancia
											)
									);
	
		$p->Output($apellido.", ".$nombres." (".$legajo.")",'I');
	}


	public function cert_pendientes(){
		$pendientes = $this->Guarani_model->cert_pendientes();
		//var_dump($pendientes); die;
		$reporte = new PDF_cert_pendientes();
		$reporte->set_pendientes($pendientes);
		$reporte->Output("cert_pendientes.pdf","I");
	}

	

	

	


}


