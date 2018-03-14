<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Persona{
	private $nro_inscripcion;
	private $apellido;
	private $nombres;
	private $nro_documento;
	private $tipo_documento;
	private $sexo;
	private $fecha_nacimiento;
	private $ci;



	/* Función factoría */
	static function get_instance(){
		return new Persona();
	}

	public function obtener_datos($nro_inscripcion){
		/*Buscamos una persona por el nro_inscripcion y
		  completamos los otros atributos con datos obtenidos de la BD */
		$this->ci =& get_instance();
		if($nro_inscripcion){
			$params = $this->ci->guarani_model->get_datos_personales_tmp(strtoupper($nro_inscripcion));	
		}else{
			$params = array();
		}
		
		foreach ($params as $atributo => $valor) {
			$nombre_funcion = "set_".$atributo;
			$this->$nombre_funcion($valor);
		}
	}
	/* ------------------Setters ------------------ */
	public function set_nro_inscripcion($agr){
		$this->nro_inscripcion = $agr;
	}

	public function set_apellido($apellido){
		$this->apellido = $apellido;
	}

	public function set_nombres($nombres){
		$this->nombres = $nombres;
	}

	public function set_nro_documento($nro_documento){
		$this->nro_documento = $nro_documento;
	}

	public function set_tipo_documento($tipo = 'DNI'){
		$this->tipo_documento = $tipo;
	}

	public function set_sexo($sexo){
		$this->sexo = $sexo;
	}

	public function set_fecha_nacimiento($fecha){
		$this->fecha_nacimiento = $fecha;
	}

	/* ------------------ Getters ------------------ */
	public function get_nro_inscripcion(){
		return $this->nro_inscripcion;
	}
	public function get_apellido(){
		return $this->apellido;
	}
	public function get_nombres(){
		return $this->nombres;
	}
	public function get_nro_documento(){
		return $this->nro_documento;
	}
	public function get_tipo_documento(){
		return $this->tipo_documento;
	}
	public function get_sexo(){
		return $this->sexo;
	}
	public function get_fecha_nacimiento(){
		if(strlen($this->fecha_nacimiento) == 10) {
			$partes = explode("/",$this->fecha_nacimiento);
			return array("dia"=>$partes[0],"mes"=>$partes[1],"anio"=>$partes[2]);
		}else{
			return array("dia"=>"1","mes"=>"1","anio"=>"0001");
		}
	}
	




}

?>