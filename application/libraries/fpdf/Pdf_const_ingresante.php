<?php 
require_once 'Pdf_html.php';

class Pdf_const_ingresante extends Pdf_html{
	private $tratamiento;
	private $imagen_header;
	private $legajo;
	private $apellido;
	private $nombres;
	private $nro_inscripcion;
	private $nro_documento;
	private $tipo_documento;
	private $sexo;
	private $tipo_constancia;

	//constructor de clase
	function __construct($parametros = array()){
		//constructor de la clase padre
		parent::__construct();

		//asignacion de los datos recibidos como parametros del constructor
		if(array_key_exists('legajo', $parametros)){ $this->set_legajo($parametros['legajo']); }
		if(array_key_exists('apellido', $parametros)){ $this->set_apellido( $parametros['apellido']); }
		if(array_key_exists('nombres', $parametros)){ $this->set_nombres( ucwords(strtolower($parametros['nombres'])) ); }
		if(array_key_exists('nro_inscripcion', $parametros)){ $this->set_nro_inscripcion(strtoupper($parametros['nro_inscripcion']) ); }
		if(array_key_exists('nro_documento', $parametros)){ $this->set_nro_documento($parametros['nro_documento']); }
		if(array_key_exists('tipo_documento', $parametros)){ $this->set_tipo_documento(strtoupper($parametros['tipo_documento'])); }
		if(array_key_exists('sexo', $parametros)){ $this->set_sexo($parametros['sexo']); }
		if(array_key_exists('tipo_constancia', $parametros)){ $this->set_tipo_constancia($parametros['tipo_constancia']); }
		if($this->get_sexo() == 1){
			$this->set_tratamiento("el Sr.");
		}else{
			$this->set_tratamiento("la Srta.");
		}
		//se asigna una imagen por defecto
		$this->set_imagen_header("./assets/img/perm/membrete.png");
		//echo $this->get_apellido(); 
		//agrego una hoja al documento
		$this->Addpage('P','Legal');
	}



	public function Header(){
		$this->SetTitle("Constancia Regularidad");
		$this->Image($this->get_imagen_header(),27,10,160,0);
		$this->cuerpo();
	}

	public function cuerpo(){

		$meses = array('01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre');
		$this->SetFont('Times','bu',15);
		$this->setXY(0,50);
		
		if( $this->get_legajo() <> 'null' ){
			//var_dump($this->get_legajo() ); die;
			if($this->get_tipo_constancia() == 'yovoy'){
				$this->Cell(0, 10, utf8_decode("CONSTANCIA DE ALUMNO REGULAR"), 0, 1, 'C', FALSE);
			}else{
				$this->Cell(0, 10, utf8_decode("CONSTANCIA DE ALUMNO INGRESANTE"), 0, 1, 'C', FALSE);
			}
			$this->SetLeftMargin(18);
			$this->SetRightMargin(18);
			$this->SetFont('Times','',12);
			$this->setY(65);
			$texto = "<p align='justify'>               <i>La que subscribe, Directora Gestión Estudios de la Facultad de Ciencias Agrarias, </i><u><b>HACE CONSTAR QUE</u>: ".$this->get_tratamiento()." ".str_replace(array("%20","%C3%91"),array(" ","Ñ"),$this->get_apellido()).", ".ucwords(str_replace(array("%20","%c3%91"),array(" ","ñ"),$this->get_nombres()))." (".$this->get_tipo_documento()." Nº ".$this->get_nro_documento()."),</b><i> legajo Nº".$this->get_legajo()." es </i>";
			
			if($this->get_sexo() == 1){
				$texto .= "ALUMNO";
			}else{
				$texto .= "ALUMNA";
			}

			
			if($this->get_tipo_constancia() == 'yovoy'){
				/* ======================== YO VOY ============================================= */
				$texto .= " REGULAR <i>de la carrera Ingeniería Agronómica <b>(en los términos de la Ordenanza Municipal Nº 5877/13 H.C.D.)</b>.</p><br><br><p align='justify'>               Se extiende el presente certificado a solicitud ";
				if($this->get_sexo() == 1){
					$texto .= "del interesado";
				}else{
					$texto .= "de la interesada";
				}
				
				$texto .= " y a solo efecto de ser presentado ante las autoridades de las Empresas de Transporte que lo requieran para el trámite del boleto estudiantil.</p><br><br><p align='justify'>               A los ".date("d")." días del mes de ".$meses[date("m")]." de ".date("Y").".</i></p>";
			}else{
				/* ======================== INGRESANTE ============================================= */
				$texto .= " INGRESANTE <i>de la carrera Ingeniería Agronómica.</p><br><br><p align='justify'>               A pedido de parte interesada y a los efectos de ser presentado ante QUIEN CORRESPONDA, se extiende la presente que sella y firma en la Ciudad de Corrientes a los ".date("d")." días del mes de ".$meses[date("m")]." de ".date("Y").".</i></p>";
			}
		}else{
			$this->Cell(0, 10, utf8_decode("EL ALUMNO NO TIENE LEGAJO ASIGNADO EN LA CARRERA"), 0, 1, 'C', FALSE);
			$texto = '';

			
			
		}
		$this->WriteHTML(utf8_decode($texto));

		//Linea para la firma
		$this->line(130, 150,180,150);
	}

	public function Footer(){
		//$this->Cell(0, 200, "A", 0, 1, 'C', FALSE);
	}

	private function set_tratamiento($tratamiento){
		$this->tratamiento = $tratamiento;
	}
	private function set_imagen_header($url_imagen){
		$this->imagen_header = $url_imagen;
	}
	private function set_legajo($legajo = NULL){
		$this->legajo = $legajo; 
	}
	private function set_apellido($apellido = '<<Sin apellido asignado>>'){
		$this->apellido = $apellido;
	}
	private function set_nombres($nombre = '<<Sin nombre asignado>>'){
		$this->nombres = $nombre;
	}
	private function set_nro_inscripcion($nro_inscripcion = '<<Sin Nro. de Inscripción asignado>>'){
		$this->nro_inscripcion = $nro_inscripcion;
	}
	private function set_nro_documento($nro_documento = '<<Sin Nro. de Documento asignado>>'){
		$this->nro_documento = $nro_documento;
	}
	private function set_tipo_documento($tipo_documento = '<<Sin Tipo de Documento asignado>>'){
		$this->tipo_documento = $tipo_documento;
	}
	private function set_sexo($sexo = '<<Sin sexo asignado>>'){
		$this->sexo = $sexo;
	}
	private function set_tipo_constancia($tipo_constancia = 'ingresante'){
		$this->tipo_constancia = $tipo_constancia;
	}


	private function get_tratamiento(){
		return $this->tratamiento;
	}
	private function get_legajo(){
		return $this->legajo;
	}
	private function get_apellido(){
		return strtoupper(str_replace("_"," ",$this->apellido));
	}
	private function get_nombres(){
		return ucwords(str_replace("_"," ",$this->nombres));
	}
	private function get_tipo_documento(){
		switch (strtolower($this->tipo_documento)) {
			case 'dni':
				return "D.N.I.";
				break;
			case 'ci':
				return "C.I.";
				break;
			case 'le':
				return "L.E.";
				break;
			case 'lc':
				return "L.C";
				break;
			case 'pas':
				return "PAS.";
				break;
			case 'dnt':
				return "D.N.T.";
				break;
			default:
				return $this->tipo_documento;
				break;
		}
		if( strtolower($this->tipo_documento) == 'dni' ){
			return "D.N.I.";
		}else{
			return $this->tipo_documento;
		}
	}
	private function get_nro_documento(){
		if($this->get_tipo_documento() == 'D.N.I.'){
			return substr($this->nro_documento, 0, 2).".".substr($this->nro_documento, 2, 3).".".substr($this->nro_documento, 5, 3);
		}else{
			return $this->nro_documento;	
		}
		
	}
	private function get_nro_inscripcion(){
		return $this->nro_inscripcion;
	}
	private function get_sexo(){
		return $this->sexo;
	}
	private function get_tipo_constancia(){
		return $this->tipo_constancia;
	}
	private function get_imagen_header(){
		return $this->imagen_header;	
	}
}

?>