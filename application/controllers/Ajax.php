<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Guarani_model");
	}

	function get_config_guarani_rest()
	{
		echo json_encode(array(
			'url_guarani_rest' => $this->config->item('url_guarani_rest'),
			'user_guarani_rest' => $this->config->item('user_guarani_rest'),
			'pass_guarani_rest' => $this->config->item('pass_guarani_rest')
		));
	}


	public function condicion_tfg($legajo,$carrera){
		$nro_inscripcion = $this->Guarani_model->get_nro_inscripcion($legajo,$carrera);
		$requisitos_adeuda = $this->Guarani_model->requisitos_adeuda($nro_inscripcion,$carrera);		
		$datos_personales = $this->Guarani_model->get_datos_personales($legajo,$carrera);
		$obligatorias = $this->Guarani_model->cant_mat_obligatorias($legajo,$carrera);
		$optativas = $this->Guarani_model->cant_mat_optativas($legajo,$carrera);
		$tercer_anio = $this->Guarani_model->tercer_anio_completo($legajo,$carrera);
		$regular = $this->Guarani_model->es_regular($legajo,$carrera);
		$calidad = $this->Guarani_model->get_calidad($legajo, $carrera);
		$respuesta = array("nro_inscripcion"   => $nro_inscripcion,
						   "legajo"            => $legajo,
						   "datos_personales"  => $datos_personales,
						   "requisitos_adeuda" => $requisitos_adeuda ,
						   "optativas"         => $optativas,
						   "obligatorias"      => $obligatorias,
						   "tercer_anio"       => $tercer_anio,
						   "regular"           => $regular,
						   "calidad"           => $calidad);
		echo json_encode($respuesta);
	}

	public function ultimos_examenes($carrera, $legajo, $cantidad){
		
		$ultimos_examenes = $this->Guarani_model->ultimos_examenes($carrera,$legajo, $cantidad); //cambiar esta linea para traer mas examenes
		echo json_encode($ultimos_examenes);
	}



	public function encuestas_get_materias(){
		$id_alcance = $this->input->post("id_alcance");
		$materias = $this->Guarani_model->encuestas_get_materias($id_alcance);
		if(count($materias) > 0){
			while($materia = array_shift($materias) ){
					$json[] = array("materia"=>$materia['materia'],"nombre"=>utf8_encode($materia['nombre'])); 
			}
			echo json_encode($json);
		}else{
			echo json_encode(array("materias"=>"No se encontraron"));
		} 

	}

	public function get_materias_curs(){
		$ubicacion = "./assets/materias.json";
		$materias = file_get_contents($ubicacion);
		echo $materias;
	}
	public function get_materias_curs_array(){
		$ubicacion = "./assets/materias.json";
		$materias = file_get_contents($ubicacion);
		return $materias;
	}

	public function get_cert_pendientes(){
		echo json_encode($this->Guarani_model->cert_pendientes());
	}

	public function get_alcances()
	{
		$alcances = $this->Guarani_model->get_alcances();
		$a = array();
		foreach($alcances as $alcance){
			$a[] = array('alcance'=>$alcance['alcance'],'titulo'=>utf8_encode($alcance['titulo']));
		}
		echo json_encode($a);
	}

	public function encuestas_get_docentes(){
		$id_alcance = $this->input->post("id_alcance");
		$id_materia = $this->input->post("id_materia");

		$docentes = $this->Guarani_model->encuestas_get_docentes($id_alcance,$id_materia);
		if(count($docentes) > 0){
			while($docente = array_shift($docentes) ){
					$json[] = array("legajo"=>$docente['legajo'],"nombre"=>utf8_encode($docente['apellido'].", ".$docente['nombres']) ); 
			}
			echo json_encode($json);
		}else{
			echo json_encode(array("docentes"=>"No se encontraron"));
		} 

	}

	public function obtener_respuestas(){
		
	}

	public function obtener_observaciones(){
		$id_alcance = $this->input->post("id_alcance");
		$id_materia = $this->input->post("id_materia");
		$legajo_docente = $this->input->post("legajo_docente");

		$observaciones = $this->Guarani_model->obtener_observaciones($id_alcance,$id_materia,$legajo_docente);
		
		if(count($observaciones) > 0){
			while($observacion = array_shift($observaciones) ){
					$json[] = array("observacion"=>utf8_encode($observacion['observaciones']) ); 
			}
			echo json_encode($json);
		}else{
			echo json_encode(array("Error"=>json_last_error()));
		} 
	}

	public function get_historial_materia(){
		$materia = $this->input->post("materia");
		$anio = $this->input->post("anio");
		$curs_periodo = $this->input->post("curs_periodo");
		$resultados = $this->Guarani_model->get_historial_materia($materia,$anio,$curs_periodo);
		
		if(count($resultados) > 0){
			echo json_encode($resultados);
		}else{
			echo json_encode(array("Error"=>json_last_error()));
		} 

	}

	public function get_turnos_examen(){
		$anio = $this->input->post("anio");
		$resultados = $this->Guarani_model->get_turnos_examen($anio);
		
		if(count($resultados) > 0){
			echo json_encode($resultados);
		}else{
			echo json_encode(array("Error"=>json_last_error()));
		} 

	}

	public function get_llamados_examen(){
		$anio = $this->input->post("anio");
		$turno = $this->input->post("turno");
		$resultados = $this->Guarani_model->get_llamados_examen($anio, $turno);
		
		if(count($resultados) > 0){
			echo json_encode($resultados);
		}else{
			echo json_encode(array("Error"=>json_last_error()));
		} 
	}

	public function get_reporte_examenes(){
		$anio = $this->input->post("anio");
		$turno = $this->input->post("turno");
		$llamado = $this->input->post("llamado");
		$materias = $this->get_materias_curs_array();
		$resultados = $this->Guarani_model->get_reporte_examenes($anio, $turno, $llamado, $materias);
		
		if(count($resultados) > 0){
			echo json_encode($resultados);
		}else{
			
			echo json_encode(array("Error"=>json_last_error()));
		} 
	}

	public function horarios_llamado(){
		$anio = $this->input->post("anio");
		$turno = $this->input->post("turno");
		$llamado = $this->input->post("llamado");
		$resultados = $this->Guarani_model->horarios_llamado($anio, $turno, $llamado);
		
		if(count($resultados) > 0){
			echo json_encode($resultados);
		}else{
			echo json_encode(array("Error"=>json_last_error()));
		} 
	}

	public function get_egresados(){
		echo json_encode($this->Guarani_model->get_egresados());
	}

	public function get_fechas_regularizadas($legajo){
		echo json_encode($this->Guarani_model->get_fechas_regularizadas($legajo));
	}

	public function get_datos_coneau(){
		echo json_encode($this->Guarani_model->get_datos_coneau($this->input->post("codigos"),$this->input->post("promocionable"),$this->input->post("tipo_materia")));	
	}
	public function get_materias_plan($plan){
		echo json_encode($this->Guarani_model->get_materias_plan($plan));

	}

	public function desercion(){
		echo json_encode($this->Guarani_model->desercion($this->input->post('anios_anteriores')));
	}
	
} //