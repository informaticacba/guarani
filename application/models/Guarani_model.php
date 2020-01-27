<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Guarani_model extends CI_Model {
	
	public function get_datos($agr){
		$resultados = array();
		$consulta = "EXECUTE FUNCTION dba.spagr_get_datos_personales('".$agr."')";
		/*$consulta = "select per.nro_inscripcion,
					        per.apellido, 
							per.nombres,
							per.nro_documento,
							tipo.desc_abreviada as tipo_documento,
							case per.sexo when 1 then 'M' when 2 then 'F' end as sexo,
							per.fecha_nacimiento
					from sga_personas as per
					inner join mdp_tipo_documento as tipo on tipo.tipo_documento = per.tipo_documento
					where nro_inscripcion = '".$agr."'";*/
		$resultado = $this->db->query($consulta);

		if($resultado->num_rows() > 0){
			foreach ($resultado as $key => $value){
				//
			}	
			
		}else{
			return array();
		}
	}

	public function get_datos_personales($legajo,$carrera = '01'){
		//-------------------------------------- DATOS PERSONALES ----------------------------------------- 
		$consulta = "select first 1 per.apellido, per.nombres, per.nro_documento, per.sexo, alu.plan, alu.fecha_ingreso
		from sga_personas as per left join sga_alumnos as alu on alu.nro_inscripcion = per.nro_inscripcion
		where legajo = '".$legajo."' and alu.carrera = '".$carrera."'";
		
		
		$resultado=$this->db->query($consulta);

		$arr = $resultado->result_array();
		if(count($arr)){
			$datos = $arr[0];
		}else{
			return array();
		}

		$datos['apellido'] =strtoupper(str_replace("Ñ","-n-", utf8_encode($datos['apellido']))); 
		$datos['nombres'] = ucwords(strtolower(str_replace("Ñ","-n-",utf8_encode($datos['nombres'])))); 
			

if(strlen($datos['nombres']) > 0){
			return $datos;
		}else{
			return array();
		}
	}
	

	public function cant_mat_optativas($legajo,$carrera='01'){
		//$plan = $this->get_plan_alumno($legajo);
		$consulta= "select count(*)
					from vw_hist_academica as vw
					where vw.materia in (
					    select materia_optativa 
					    from sga_mat_genericas 
					    where materia_generica in ('87','88.','IOCUA','IOQUI') and materia_optativa <> 'IDIOM'
					)
					and vw.legajo = '$legajo'
					and vw.carrera = '$carrera'
					and vw.resultado = 'A'";
		//echo $consulta; die;
		$resultado=$this->db->query($consulta);
		$arr = $resultado->result_array();
		$cant = array_shift($arr);
		$optativas['produccion_vegetal'] = array_shift($cant);

		$consulta = str_replace("'87','88.'","'88','89.'",$consulta); //Produccion Animal
		$resultado=$this->db->query($consulta);
		$arr = $resultado->result_array();
		$cant = array_shift($arr);
		$optativas['produccion_animal'] = array_shift($cant);

		$consulta = str_replace("'88','89.'","'89','90.'",$consulta); //Otras Areas
		$resultado=$this->db->query($consulta);
		$arr = $resultado->result_array();
		$cant = array_shift($arr);
		$optativas['otras_areas'] = array_shift($cant);

		return $optativas;

		
	}

	public function cant_mat_obligatorias($legajo,$carrera='01'){
		$plan = $this->get_plan_alumno($legajo);
		//-------------------------------------- OBLIGATORIAS ----------------------------------------- 
		$consulta= "select count(*) 
					from vw_hist_academica 
					where legajo = '$legajo'
					and materia not in (
						select materia_optativa 
						from sga_mat_genericas where materia_generica in ('87','88','89','88.','89.','90.','IOCUA','IOQUI')
					)
					and resultado = 'A'
					and carrera = '".$carrera."'";
		
		$resultado=$this->db->query($consulta);
		$arr = $resultado->result_array();
		$cant = array_shift($arr);
		return array_shift($cant);
	}


	public function get_turnos_examen($anio){
		$consulta = "select turno_examen, nombre from sga_turnos_examen 
					where anio_academico = $anio 
					and lower(nombre) not like '%aduac%'
					and lower(nombre) not like '%ctuali%'";
		$resultado=$this->db->query($consulta);
		$resultado = $resultado->result_array();
		return $resultado;
		
	}

	public function get_llamados_examen($anio, $turno){
		$consulta = "select llamado from sga_llamados where anio_academico = $anio and turno_examen = '$turno'";
		$resultado=$this->db->query($consulta);
		$resultado = $resultado->result_array();
		return $resultado;
		
	}

	//LISTO PARA USAR ----------------------------------------------------------------------------
	public function get_reporte_examenes($anio, $turno, $llamado,$materias_obligatorias){
		//va a contener todos los resultados de examenes y los nombres de las materias
		$datos = array();

		//var_dump(json_decode($materias_obligatorias)); die;
		$materias = json_decode($materias_obligatorias);
		

		foreach($materias as $indice => $materia){
			
			if( ! $materia){
				continue;
			}

			//no me interesa si no tiene final
			if ( ! $materia->tiene_final){
				continue;
			}

			$mat = "";
			for($i=0; $i < count($materia->codigos); $i++ ){
				$mat .= "'".$materia->codigos[$i]."'";
				if($i != (count($materia->codigos) - 1) ){
					$mat .= ",";
				}
			}
			
			
			$consulta = "select count(*) as cantidad, det.resultado
						from sga_detalle_acta as det
						left join sga_actas_examen as act on act.acta = det.acta
						where 1=1
						--and det.carrera = '01' 
						and act.materia in ($mat)
						--and det.plan in ('1963','1985','2002','2006','2013')
						and det.rectificado = 'N'
						and act.anio_academico = '$anio'
						and act.turno_examen = '$turno'
						and act.llamado = $llamado
						and act.estado = 'C'
						group by det.resultado";

			//obtengo la materia
			
			$consulta_materia = "select first 1 nombre from sga_materias where materia in (".$mat.") order by materia DESC";
			$resultado=$this->db->query($consulta_materia);
			$materia = $resultado->result_array(); 
			$materia =  array_shift($materia)['nombre'];

			//obtengo los resultados de examenes
			$resultado=$this->db->query($consulta);
		
			if( !$resultado->result_array()){
				continue;
			}
			$arr = $resultado->result_array();
			
			$resultados = array("materia"=>$materia);
			
			for($k=0; $k<count($arr); $k++){
				if($arr[$k]['resultado']	){
					switch ($arr[$k]['resultado']) {
						case 'U':
							$resultados["ausentes"] = $arr[$k]['cantidad'];
							break;
						case 'A':
							$resultados["aprobados"] = $arr[$k]['cantidad'];
							break;
						case 'R':
							$resultados["reprobados"] = $arr[$k]['cantidad'];
							break;
						default:
							//
							break;
					}
				}
			}
			$datos[] = $resultados;
		}
		return $datos;
	}

	public function horarios_llamado($anio, $turno, $llamado){
		//asigno horario 00:00:00
		$consulta = "update sga_prestamos set hora_inicio = '00:00:00' where hora_inicio is null";
		$this->db->query($consulta);
		
		$consulta = "select mat.nombre, pres.fecha, pres.hora_inicio		
					from sga_llamados_mesa as llamado
					left join sga_prestamos as pres on pres.prestamo = llamado.prestamo
					left join sga_materias as mat on mat.materia = llamado.materia
					where anio_academico = $anio 
					and turno_examen = '$turno'
					and llamado = $llamado
					and habilitado = 'S'
					and pres.hora_inicio is not null
					group by 1,2,3
					order by 2,1";

			$resultado=$this->db->query($consulta);
			return $resultado->result_array(); 
	}

	public function tercer_anio_completo($legajo,$carrera='01'){
		
		//-------------------------------------- TERCER AÑO COMPLETO?? ----------------------------------------- 
		$consulta = "select count(*) from vw_hist_academica where legajo = '$legajo'	and resultado = 'A' and ";
		if($carrera == '01'){
			$consulta .= "materia in ('20','63','64.','17','64','65.','16','65','66.','22','66','67.','11','67','68.','68','69.','14','69','70.','26','70','71.','72','73.','71','72.') ";
		}
		if($carrera == '08'){
			$consulta .= "materia in ('I20','I21','I22','I23','I24','I25','I26','I27','I28','I29') ";
		}
		
		$resultado=$this->db->query($consulta);
		$completo = $resultado->result_array(); 
		$completo = array_shift($completo);
		if(array_shift($completo) >= 10){
			return TRUE;
		}else{
			return FALSE;
		}
		
	}

	public function es_regular($legajo,$carrera='01'){
		$consulta= "select first 1 regular from sga_alumnos where legajo = '$legajo' and carrera = '$carrera'";
		// obtenermos los resultados
		$resultado=$this->db->query($consulta);

		$regular = $resultado->result_array(); 
		if(array_shift($regular)['regular']){
			return TRUE;
		}else{
			return FALSE;
		}
		
	}

	public function get_calidad($legajo, $carrera='01'){
		$consulta= "select first 1 calidad from sga_alumnos where legajo = '$legajo' and carrera = '$carrera'";
		// obtenermos los resultados
		$resultado=$this->db->query($consulta);
		$calidad = $resultado->result_array(); 
		return array_shift($calidad)['calidad'];
			
		
	}

	public function get_plan_alumno($legajo){
		$consulta= "select first 1 plan from sga_alumnos where legajo = $legajo";
		// obtenermos los resultados
		$resultado=$this->db->query($consulta);
		$plan = $resultado->result_array(); 
		return array_shift($plan)['plan'];
		
	}

	public function ultimos_examenes($carrera, $legajo, $cant){
		$consulta = "select first $cant distinct per.apellido, 
												per.nombres, 
												per.nro_documento, 
												detacta.fecha_de_examen, 
												mat.nombre as materia, 
												detacta.resultado, 
												detacta.nota  
					from sga_detalle_acta as detacta
					left join sga_actas_examen as acta on acta.acta = detacta.acta
					left join sga_materias as mat on mat.materia = acta.materia
					left join sga_alumnos as alu on alu.legajo = detacta.legajo
					left join sga_personas as per on per.nro_inscripcion = alu.nro_inscripcion
					where detacta.legajo = '$legajo'
					and detacta.rectificado = 'N'
					and detacta.estado = 'A'
					and detacta.carrera = '$carrera'
					and detacta.resultado is not null 
					order by detacta.fecha_de_examen desc";
		$resultado = $this->db->query($consulta);
		$ultimos_examenes = $resultado->result_array();

		//este bucle es para codificar a utf8 los datos, para que no de error despues el json_encode
		for($i=0; $i<count($ultimos_examenes); $i++){
			// el remplazo de la Ñ se hace para que no vaya en la url (genera errores)
			$ultimos_examenes[$i] = array("apellido" =>  str_replace( array("Ñ","ñ"),"-n-", utf8_encode( $ultimos_examenes[$i]['apellido']) ), 
											"nombres" => str_replace( array("Ñ","ñ"),"-n-", utf8_encode( $ultimos_examenes[$i]['nombres']) ), 
											"materia" => str_replace( array("Ñ","ñ"),"-n-", utf8_encode( $ultimos_examenes[$i]['materia']) ), 
											"fecha_de_examen" => $ultimos_examenes[$i]['fecha_de_examen'],
											"nro_documento" => $ultimos_examenes[$i]['nro_documento'],
											"resultado" => $ultimos_examenes[$i]['resultado'],
											"nota" => $ultimos_examenes[$i]['nota'],
											);
		}
		return $ultimos_examenes;
	}

	public function get_alcances()
	{
		$sql = "select alcance, titulo from gde_encues_alcance";
		$resultado = $this->db->query($sql);
		return $resultado->result_array();
	}

	//obtiene las materias de una determinada encuesta
	public function encuestas_get_materias($id_alcance){
		$consulta = "select distinct mat.materia, mat.nombre
					from gde_encues_rpta as rpta
					left join sga_comisiones as com on com.comision = rpta.comision
					left join sga_materias as mat on mat.materia = com.materia
					where alcance = ".$id_alcance." order by mat.nombre ";
		$resultado = $this->db->query($consulta);
		$materias_comisiones = $resultado->result_array();
		return $materias_comisiones;

	}

	public function get_materias_curs(){
		$materias = file_get_contents("./assets/materias.json");
		return $materias; 
	}

	public function get_nombres_archivos(){
		$archivo = file_get_contents("./assets/nombres_archivo.json");

		$arr = json_decode($archivo);
		foreach ($arr as $key => $value) {
			$nombres[$value->materia] = $value->nombre_archivo;
		} 
		return $nombres;
	}

	public function get_materia($codigos_materia){
		$codigos = str_replace(array('(',')','"','\''), '', $codigos_materia);
		$codigos = explode(',',$codigos);

		$archivo = file_get_contents("./assets/nombres_archivo.json");
		$arr = json_decode($archivo);
		foreach ($arr as $key => $value) {
			if($value->codigos == $codigos){
				return $value;
			}
		}
	}

	//obtiene los docentes de una determinada materia
	public function encuestas_get_docentes($id_alcance, $id_materia){
		$consulta = "select distinct doc.legajo, per.apellido, per.nombres
					from gde_encues_rpta as rpta
					left join sga_comisiones as com on com.comision = rpta.comision
					left join sga_docentes as doc on doc.legajo = rpta.legajo
					left join sga_personas as per on per.nro_inscripcion = doc.nro_inscripcion
					where alcance = ".$id_alcance."
					and com.materia = '".$id_materia."'";
		$resultado = $this->db->query($consulta);
		$materias_comisiones = $resultado->result_array();
		return $materias_comisiones;

	}

	public function obtener_docente($legajo){
		$consulta = "select first 1 per.nro_inscripcion, per.apellido, per.nombres, per.nro_documento, per.sexo  
					from sga_docentes as doc 
					left join sga_personas as per on per.nro_inscripcion = doc.nro_inscripcion
					where doc.legajo = $legajo";
		$resultado = $this->db->query($consulta);
		
		$datos_docente = $resultado->result_array();
		return array_shift($datos_docente);
	}

	public function obtener_materia($materia){
		$consulta = "select first 1 nombre from sga_materias where materia = '$materia'";
		$resultado = $this->db->query($consulta);
		$datos_materia = $resultado->result_array();
		
		$nombre = array_shift($datos_materia);
		return $nombre['nombre'];

	}

	public function obtener_respuestas($id_alcance, $id_materia, $legajo_docente){
		$consulta = "select preg.texto as pregunta, pre_opc.descripcion as opcion, per.apellido, per.nombres, count(*) as cantidad
					from gde_encues_rpta as rpta
					left join gde_encuestas as enc on enc.encuesta = rpta.encuesta
					left join gde_preguntas as preg on rpta.pregunta = preg.pregunta
					left join gde_preg_opcion as pre_opc on pre_opc.opcion = rpta.opcion and pre_opc.pregunta = preg.pregunta and preg.pregunta = rpta.pregunta
					left join sga_comisiones as com on com.comision = rpta.comision
					left join sga_docentes as doc on doc.legajo = rpta.legajo
					left join sga_personas as per on per.nro_inscripcion = doc.nro_inscripcion
					where com.comision in (
						select comision from sga_comisiones where materia = '$id_materia' and anio_academico = com.anio_academico
					)
					and rpta.alcance = '$id_alcance'";
					if($legajo_docente <> 0){
						$consulta.= " and doc.legajo = '$legajo_docente'";
					}
					$consulta .= "group by per.apellido, per.nombres, preg.texto, pre_opc.descripcion
					order by preg.texto, pre_opc.descripcion, per.apellido, per.nombres";
					
					
			//echo $consulta; die;
			$resultado = $this->db->query($consulta);
			$respuestas = $resultado->result_array();
			return $respuestas;
		
	}

	public function obtener_observaciones($id_alcance, $id_materia, $legajo_docente){
		$consulta = "select distinct rpta.valor as observaciones
					from gde_encues_rpta as rpta
					left join gde_preguntas as preg on rpta.pregunta = preg.pregunta
					left join gde_encuestas as enc on enc.encuesta = rpta.encuesta
					left join sga_comisiones as com on com.comision = rpta.comision
					left join gde_preg_opcion as pre_opc on pre_opc.opcion = rpta.opcion and pre_opc.pregunta = rpta.pregunta
					left join sga_docentes as doc on doc.legajo = rpta.legajo
					left join sga_personas as per on per.nro_inscripcion = doc.nro_inscripcion
					where com.comision in (
											select comision from sga_comisiones where materia = '$id_materia' and anio_academico = com.anio_academico
											)
					and rpta.alcance = '$id_alcance'";
					if($legajo_docente <> 0){
						$consulta = $consulta." and doc.legajo = '$legajo_docente'"; 	
					} 
					$consulta = $consulta."	and rpta.valor is not null and LENGTH(rpta.valor) > 20";
					//echo $consulta; die;

		$resultado = $this->db->query($consulta);
		$observaciones = $resultado->result_array();
		return $observaciones;
	}

	public function get_historial_materia($materia, $anio, $curs_periodo){
		$nuevos_regularizaron = 'select count(*)
								from sga_cursadas as cur
								where cur.materia in '.$materia.'
								and cur.origen = \'C\'
								and cur.resultado in (\'A\')
								and year(cur.fecha_regularidad) = '.$anio.'
								and legajo not in (
									select legajo 
									from sga_cursadas 
									where year(fecha_regularidad) < '.$anio.'
									and materia in '.$materia.'
								)
								and cur.comision in (
									select comision 
									from sga_comisiones 
									where periodo_lectivo = \''.utf8_decode($curs_periodo).'\' 
									and anio_academico = '.$anio.' 
									and materia in '.$materia.' 
									and nombre not like \'%O VAN%\'
								)';
		
		
		$resultado = $this->db->query($nuevos_regularizaron);

		$nuevos_regularizaron = $resultado->result_array()[0];
		$nuevos_regularizaron = array_shift($nuevos_regularizaron);


		$nuevos_libres = 'select count(*)
							from sga_cursadas as cur
							where cur.materia in '.$materia.'
							and cur.origen in (\'C\',\'P\')
							and cur.resultado in (\'U\',\'L\')
							and year(cur.fecha_regularidad) = '.$anio.'
							and legajo not in (
								select legajo 
								from sga_cursadas 
								where year(fecha_regularidad) < '.$anio.'
								and materia in '.$materia.'
							)
							and cur.comision in (
								select comision 
								from sga_comisiones 
								where periodo_lectivo = \''.utf8_decode($curs_periodo).'\' 
								and anio_academico = '.$anio.' 
								and materia in '.$materia.' 
								and nombre not like \'%O VAN%\'
							)';
		//echo $nuevos_libres;
		$resultado = $this->db->query($nuevos_libres);
		$nuevos_libres = $resultado->result_array()[0];
		$nuevos_libres = array_shift($nuevos_libres);
		
				

		$nuevos_promocionaron = 'select count(*)
							from sga_cursadas as cur
							where cur.materia in '.$materia.'
							and cur.origen in (\'P\')
							and cur.resultado in (\'P\')
							and year(cur.fecha_regularidad) = '.$anio.'
							and legajo not in (
								select legajo 
								from sga_cursadas 
								where year(fecha_regularidad) < '.$anio.'
								and materia in '.$materia.'
							)
							and cur.comision in (
								select comision 
								from sga_comisiones 
								where periodo_lectivo = \''.utf8_decode($curs_periodo).'\' 
								and anio_academico = '.$anio.' 
								and materia in '.$materia.' 
								and nombre not like \'%O VAN%\'
							)';
		//echo nl2br($nuevos_promocionaron); die;
		$resultado = $this->db->query($nuevos_promocionaron);
		$nuevos_promocionaron = $resultado->result_array()[0];
		$nuevos_promocionaron = array_shift($nuevos_promocionaron);



		$recursantes_regularizaron = 'select count(*)
								from sga_cursadas as cur
								where cur.materia in '.$materia.'
								and cur.origen = \'C\'
								and cur.resultado in (\'A\')
								and year(cur.fecha_regularidad) = '.$anio.'
								and legajo in (
									select legajo 
									from sga_cursadas 
									where year(fecha_regularidad) < '.$anio.'
									and materia in '.$materia.'
								)
								and cur.comision in (
									select comision 
									from sga_comisiones 
									where periodo_lectivo = \''.utf8_decode($curs_periodo).'\' 
									and anio_academico = '.$anio.' 
									and materia in '.$materia.' 
									and nombre not like \'%O VAN%\'
								)';
		//echo $recursantes_regularizaron; die;
		
		$resultado = $this->db->query($recursantes_regularizaron);
		$recursantes_regularizaron = $resultado->result_array()[0];
		$recursantes_regularizaron = array_shift($recursantes_regularizaron);



		$recursantes_libres = 'select count(*)
							from sga_cursadas as cur
							where cur.materia in '.$materia.'
							and cur.origen in (\'C\',\'P\')
							and cur.resultado in (\'U\',\'L\')
							and year(cur.fecha_regularidad) = '.$anio.'
							and legajo in (
								select legajo 
								from sga_cursadas 
								where year(fecha_regularidad) < '.$anio.'
								and materia in '.$materia.'
							)
							and cur.comision in (
								select comision 
								from sga_comisiones 
								where periodo_lectivo = \''.utf8_decode($curs_periodo).'\' 
								and anio_academico = '.$anio.' 
								and materia in '.$materia.' 
								and nombre not like \'%O VAN%\'
							)';

		$resultado = $this->db->query($recursantes_libres);
		$recursantes_libres = $resultado->result_array()[0];
		$recursantes_libres = array_shift($recursantes_libres);



		$recursantes_promocionaron = 'select count(*)
							from sga_cursadas as cur
							where cur.materia in '.$materia.'
							and cur.origen in (\'P\')
							and cur.resultado in (\'P\')
							and year(cur.fecha_regularidad) = '.$anio.'
							and legajo in (
								select legajo 
								from sga_cursadas 
								where year(fecha_regularidad) < '.$anio.'
								and materia in '.$materia.'
							)
							and cur.comision in (
								select comision 
								from sga_comisiones 
								where periodo_lectivo = \''.utf8_decode($curs_periodo).'\' 
								and anio_academico = '.$anio.' 
								and materia in '.$materia.' 
								and nombre not like \'%O VAN%\'
							)';
		$resultado = $this->db->query($recursantes_promocionaron);
		$recursantes_promocionaron = $resultado->result_array()[0];
		$recursantes_promocionaron = array_shift($recursantes_promocionaron);

		$finales_ausentes = 'select count(*) from sga_detalle_acta where acta in (
								select acta from sga_actas_examen where materia in '.$materia.' and anio_academico = '.$anio.'
							) and resultado = \'U\'';
		//echo $recursantes_promocionaron; die;
		$resultado = $this->db->query($finales_ausentes);
		$finales_ausentes = $resultado->result_array()[0];
		$finales_ausentes = array_shift($finales_ausentes);



		$finales_aprobados = 'select count(*) from sga_detalle_acta where acta in (
								select acta from sga_actas_examen where materia in '.$materia.' and anio_academico = '.$anio.'
							) and resultado = \'A\'';
		$resultado = $this->db->query($finales_aprobados);
		$finales_aprobados = $resultado->result_array()[0];
		$finales_aprobados = array_shift($finales_aprobados);


		$finales_reprobados = 'select count(*) from sga_detalle_acta where acta in (
								select acta from sga_actas_examen where materia in '.$materia.' and anio_academico = '.$anio.'
							) and resultado = \'R\'';
		$resultado = $this->db->query($finales_reprobados);
		$finales_reprobados = $resultado->result_array()[0];
		$finales_reprobados = array_shift($finales_reprobados);

		$resultado = array(
				"nuevos_regularizaron"=>$nuevos_regularizaron,
				"nuevos_promocionaron"=>$nuevos_promocionaron,
				"nuevos_libres"=>$nuevos_libres,
				"recursantes_promocionaron"=>$recursantes_promocionaron,
				"recursantes_libres"=>$recursantes_libres,
				"recursantes_regularizaron"=>$recursantes_regularizaron,
				"finales_reprobados"=>$finales_reprobados,
				"finales_aprobados"=>$finales_aprobados,
				"finales_ausentes"=>$finales_ausentes
			);
		
		return $resultado;

		
	}

	public function get_egresados(){
		$egresados = array();
		$suma = 0;
		for($i = (date("Y")-15); $i < (date("Y")-3); $i++){ //controla la cohorte
		$cant = 0;
			for($j = (date("Y")-10); $j < (date("Y")+1); $j++){ //controla el año de egreso
				$consulta = "select count(distinct det.legajo) as cantidad
							from sga_detalle_acta as det
							left join sga_alumnos as al on al.legajo = det.legajo
							left join sga_personas as pe on pe.nro_inscripcion = al.nro_inscripcion
							where det.acta in(
								select acta from sga_actas_examen where materia in ('35','90','TFTES','TFPAS','TFGRA','TFG-P','TFG-T','GRATF','100CC')
							)  and year( det.fecha_de_examen) = $j
							and det.legajo in (
								select legajo from sga_alumnos where fecha_ingreso between mdy(11,30,".intval($i-1).") and mdy(11,30,$i)
							)"; //echo $consulta; die;

				//echo $consulta."<br>";
				$resultado = $this->db->query($consulta); 
				
				$resultado = $resultado->result_array();
				$resultado = intval($resultado[0]['cantidad']);
				
				
				if($resultado > 0){
					$cantidad = 0;
					for($p = 1; $p <= $resultado; $p++){
						$suma = $suma + (($j - $i) + 1) - 5;
						$cant++;
					}
					
				}
				$egresados[$i][$j] =$resultado;

			}
		}
		return $egresados;
	}

	public function get_nombre_materia($codigos){
		$consulta = "select first 1 nombre from sga_materias where materia in $codigos order by materia desc";
		//echo $consulta; die;
		$resultado = $this->db->query($consulta);
		//echo $resultado->result_array()['0']['nombre']; die;
		return $resultado->result_array()['0']['nombre'];
	}

	public function get_inscritos_examen($codigos_materia,$turno,$llamado){
		$consulta = "select distinct insc.legajo, 
					per.apellido||', '||per.nombres||' '|| case estado when 'P' then '(Pendiente)' else '' end  as alumno,
					case insc.tipo_inscripcion when 'L' then 'Libre' when 'R' then 'Regular' end as condicion
					from sga_insc_examen as insc
					left join sga_alumnos as alu on alu.legajo = insc.legajo
					left join sga_personas as per on per.nro_inscripcion = alu.nro_inscripcion
					where materia in $codigos_materia
					and turno_examen = '$turno'
					and anio_academico = ".date("Y")."
					and llamado = $llamado
					order by 2";
		//echo $consulta; die;
		$resultado = $this->db->query($consulta);
		return $resultado->result_array();
	}

	public function get_fechas_regularizadas($legajo){
		$consulta = "select * from (
				    select max(det.fecha_regularidad) as fecha,  mat.nombre, '<---- Regularizo' as forma_aprov 
				    from sga_det_acta_curs as det
				    left join sga_actas_cursado as act on act.acta = det.acta
				    left join sga_comisiones as com on com.comision = act.comision
				    left join sga_materias as mat on mat.materia = com.materia
				    where det.legajo = '$legajo'
				    and com.materia in (
				                select distinct materia
				                from vw_hist_academica as hist
				                where hist.legajo = det.legajo
				                and hist.resultado = 'A'
				                and forma_aprobacion = 'Examen'
				    ) group by mat.nombre
				    union
				    select fecha, mat.nombre, '<---- Promociono' as forma_aprov
				    from vw_hist_academica as vw
				    left join sga_materias as mat on mat.materia = vw.materia
				    where legajo = '".$legajo."'
				    and forma_aprobacion like '%romoci%' 
				) order by fecha";
		$resultado = $this->db->query($consulta);
		return $resultado->result_array();
	}

	public function cert_pendientes(){
		$pendientes = array();
		$consulta = "select count(*), 
			        alu.legajo, 
					per.nro_inscripcion, 
					per.apellido, 
					per.nombres, 
					case 
                        when month(alu.fecha_ingreso) > 9 then (year(alu.fecha_ingreso) + 1) 
                        else year(alu.fecha_ingreso) end as fecha_ingreso,  
					(select count(*)
						from sga_req_cumplidos as req 
						where req.nro_inscripcion = per.nro_inscripcion
						and (req.exceptuado = 'S' or req.fecha_presentacion is null)
						and req.carrera = '01'
						and req.obligatoriedad = 'S') as adeuda
					from log_certif_pedidos as pedidos
					left join sga_personas as per on per.nro_inscripcion = pedidos.nro_inscripcion
					left join sga_alumnos as alu on alu.nro_inscripcion = per.nro_inscripcion
					where fecha_de_pedido > date(today -2)
					group by alu.legajo, per.nro_inscripcion, per.apellido, per.nombres, alu.fecha_ingreso 
					having mod(count(*), 2) <> 0 --numero impar de apariciones
					order by per.apellido, per.nombres";
		$resultado = $this->db->query($consulta);
		
		foreach ($resultado->result_array() as $key => $value) {
			//var_dump($value); die;
			$pendientes[] = array("legajo"          => $value['legajo'],
								  "agr"             => $value['nro_inscripcion'],
								  "apellido"        => utf8_encode($value['apellido']),
								  "nombres"         => utf8_encode($value['nombres']),
								  "fecha_ingreso"   => $value['fecha_ingreso'],
								  "adeuda"          => $value['adeuda']
								  );
		}
		//var_dump()
		return $pendientes;
	}

	public function get_datos_coneau($materia,$es_promocionable,$tipo_materia){
		$datos['inscriptos_nuevos'] = $this->get_inscriptos($materia,FALSE);
		$datos['inscriptos_recursantes'] = $this->get_inscriptos($materia,TRUE);
		
		if($tipo_materia == "obligatoria"){
			$datos['regulares_nuevos'] = $this->get_regularizados($materia,FALSE);
			$datos['regulares_recursantes'] = $this->get_regularizados($materia,TRUE);
			$datos['finales'] = $this->get_finales($materia);
		}

		if($es_promocionable){
			$datos['promocionados_nuevos'] = $this->get_promocionados($materia, FALSE);
			$datos['promocionados_recursantes'] = $this->get_promocionados($materia, TRUE);	
		}
		return $datos;

	} 

	//retorna la cantidad de alumnos regularizados de una materia en un año determinado
	public function get_regularizados($materia, $recursantes){
		$cantidades = array();
		
		for($anio = (date("Y") - 8); $anio <= date("Y"); $anio++ ) {
			$consulta = "select count(*) as cantidad from sga_det_acta_curs as det where det.acta in ".$this->get_actas('regular',$materia, $anio)." and det.resultado = 'A'";
			$subconsulta = "select legajo 
								from sga_insc_cursadas
								where comision in (select comision from sga_comisiones where materia in (".$materia.") and anio_academico < ".$anio." and sede = '00000') and estado = 'A'";
			if( $recursantes ){
				$consulta .= " and det.legajo in (".$subconsulta.")";
			}else{
				$consulta .= " and det.legajo not in (".$subconsulta.")";
			}
			//echo $consulta."<br>"; 
			$resultado = $this->db->query($consulta);
			$resultado = $resultado->result_array();
			$cantidad = $resultado[0]; 
			$cantidades[$anio] = $cantidad['cantidad'];
		}
		return $cantidades;
	}

	//retorna la cantidad de alumnos promocionados de una materia en un año determinado
	private function get_promocionados($materia, $recursantes){
		$cantidades = array();
		//$this->get_recursantes($materia); die;
		for($anio = (date("Y") - 8); $anio <= date("Y"); $anio++ ) {
			$consulta = "select count(*) as cantidad from sga_det_acta_promo as det where det.acta in ".$this->get_actas('promo',$materia, $anio)." and det.resultado = 'P' and det.rectificado = 'N'";
			$subconsulta = "select legajo 
								from sga_insc_cursadas
								where comision in (select comision from sga_comisiones where materia in (".$materia.") and anio_academico < ".$anio." and sede = '00000') and estado = 'A'";
			if( $recursantes ){
				$consulta .= " and det.legajo in (".$subconsulta.")";
			}else{
				$consulta .= " and det.legajo not in (".$subconsulta.")";
			}
			//echo $consulta."<br>"; 
			$resultado = $this->db->query($consulta);
			$resultado = $resultado->result_array();
			$cantidad = $resultado[0]; 
			$cantidades[$anio] = $cantidad['cantidad'];
		}
		
		return $cantidades;
	}
	//retorna la cantidad de alumnos inscriptos a cursar una determinada materia
	private function get_inscriptos($materia,$recursantes){
		$cantidades = array();
		//$this->get_recursantes($materia); die;
		for($anio = (date("Y") - 8); $anio <= date("Y"); $anio++ ) {
			$consulta = "select count(*) as cantidad 
						from sga_insc_cursadas 
						where comision in (
							select comision 
							from sga_comisiones 
							where materia in (".$materia.") 
							and anio_academico = ".$anio."
							and sede = '00000'
							and nombre not like '%O VA%'
						)";
			$subconsulta = "select legajo 
								from sga_insc_cursadas
								where comision in (select comision from sga_comisiones where materia in (".$materia.") and anio_academico < ".$anio." and sede = '00000') and estado = 'A'";			
			if( $recursantes ){
				$consulta .= " and legajo in (".$subconsulta.")";
			}else{
				$consulta .= " and legajo not in (".$subconsulta.")";
			}
			//echo $consulta."<br>"; 
			$resultado = $this->db->query($consulta);
			$resultado = $resultado->result_array();
			$cantidad = $resultado[0]; 
			$cantidades[$anio] = $cantidad['cantidad'];
		}
		
		return $cantidades;
	}

	//retorna la cantidad de alumnos promocionados de una materia en un año determinado
	public function get_finales($materia){
		$cantidades = array();
		
		for($anio = (date("Y") - 8); $anio <= date("Y"); $anio++ ) {
			$consulta = "select count(*) as cantidad, det.resultado
						from sga_detalle_acta as det
						left join sga_actas_examen as act on act.acta = det.acta
						where act.materia in (".$materia.")
						and act.anio_academico = ".$anio."
						and det.rectificado = 'N'
						and det.resultado <> 'U'
						group by det.resultado order by det.resultado";
			//echo $consulta."<br>"; 
			$resultado = $this->db->query($consulta);
			$resultado = $resultado->result_array();
			//var_dump($resultado); die;
			//examenes aprobados ese año
			if(array_key_exists(0,$resultado)){
				$cantidades[$anio]['aprobados'] = $resultado[0]['cantidad'];
			}else{
				$cantidades[$anio]['aprobados'] = 0;
			}
			//examenes desaprobados ese año
			if(array_key_exists(1,$resultado)){
				$cantidades[$anio]['reprobados'] = $resultado[1]['cantidad'];
			}else{
				$cantidades[$anio]['reprobados'] = 0;
			}
			
		}
		
		return $cantidades;
	}

	//retorna todas las actas de cursado de una materia en un año determinado
	private function get_actas($condicion, $materia, $anio){
		$actas = "";
		if($condicion == 'regular'){
			$tabla = "sga_actas_cursado";
		}else{
			$tabla = "sga_actas_promo";
		}
		$consulta = "select acta 
					from ".$tabla." where comision in (
						select comision 
						from sga_comisiones 
						where anio_academico = ".$anio."
						and materia in (".$materia.") 
						and sede = '00000'
					)"; 
		$resultado = $this->db->query($consulta);
        foreach($resultado->result_array() as $clave => $valor){
        	if(strlen($actas) == 0){
				$actas.= $valor['acta'];
			}else{
				$actas.= ",".$valor['acta'];
			}
		} 
		if(strlen($actas) > 0){
			return "(".$actas.")"; 	
		}else{
			return "(0)";
		}
    }

    public function get_nro_inscripcion($legajo,$carrera = '01'){
    	$consulta = "select first 1 nro_inscripcion from sga_alumnos where legajo = '".$legajo."' and carrera = '".$carrera."'";
    	$resultado = $this->db->query($consulta);
    	$agr = array_shift($resultado->result_array()[0]);
    	return $agr;
    }

    public function requisitos_adeuda($nro_inscripcion,$carrera='01'){
    	$consulta = "select count(*)
					from sga_req_cumplidos as req 
					where req.nro_inscripcion = '".$nro_inscripcion."'
					and (req.exceptuado = 'S' or req.fecha_presentacion is null)
					and req.carrera = '".$carrera."'
					and req.obligatoriedad = 'S'";
		$resultado = $this->db->query($consulta);
		$cantidad = $resultado->result_array();
		$cantidad = array_shift($cantidad);
		return array_shift($cantidad);

    }

    public function get_materias_plan($plan){
    	$consulta = "select p.version, p.materia, p.materia_nombre, p.tipo_materia, p.anio_de_cursada from vw_plan_estudios as p where p.plan = '".$plan."' and p.version = (select max(version) from vw_plan_estudios where plan = p.plan)";
    	$resultado = $this->db->query($consulta);
    	$materias = $resultado->result_array();
    	foreach ($materias as $key => $value) {
    		$materias[$key]['materia_nombre'] = $this->limpiar_acentos(utf8_encode($value['materia_nombre']));
    	}
    	
    	return $materias;
    }

	private function limpiar_acentos($string){
		return str_replace(array("á","é","í","ó","ú","Á","É","Í","Ó","Ú"),array("a","e","i","o","u","A","E","I","O","U"),$string);
	}

	public function eliminar($materia){
		
		for($anio = (date("Y")-10); $anio <= 2016; $anio++){
			$consulta = "SELECT ";
			$resultado = $this->db->query($consulta);
			$materias = $resultado->result_array();
		}

		
		return $materias;
	}


	public function tiempos_materias($anio_desde,$materia){
		//variable que contendra los resultados
		$retorno = array();

		//armo el string con los códigos de materia
		$codigos_materia = '(';
		foreach ($materia as $codigo) {
			if (strlen($codigos_materia) > 1){
				$codigos_materia .= ",'$codigo'";
			}else{
				$codigos_materia .= "'$codigo'";
			}
		}
		$codigos_materia .= ')';
		//var_dump($codigos_materia); die;

		//obtengo el nombre de materia
		$consulta = "select first 1 nombre from sga_materias as mat where materia in $codigos_materia";
		$resultado = $this->db->query($consulta);
		$resultado = $resultado->result_array(); 
		$resultado = array_shift($resultado);
		$retorno['materia'] = $resultado['nombre'];

		for($i = $anio_desde; $i <= ($anio_desde+3); $i++ ){
			
			// ---- CURSARON --------
			$consulta = "select count(*)
						from sga_det_acta_curs as det
						left join sga_actas_cursado as act on act.acta = det.acta
						left join sga_comisiones as com on com.comision = act.comision
						where com.materia in $codigos_materia
						and com.anio_academico = $i
						and com.nombre not like '%O VA%'
						and det.rectificado = 'N'";
			$resultado = $this->db->query($consulta);
			$resultado = $resultado->result_array(); 
			$resultado = array_shift($resultado);
			$retorno['anios'][$i]['cursaron'] = intval(array_shift($resultado));

			// ---- REGULARIZARON --------
			$consulta = "select distinct legajo
						from sga_det_acta_curs as det
						left join sga_actas_cursado as act on act.acta = det.acta
						left join sga_comisiones as com on com.comision = act.comision
						where com.materia in $codigos_materia
						and com.anio_academico = $i
						and com.nombre not like '%O VA%'
						and det.rectificado = 'N'
						and det.resultado = 'A'";
			$resultado = $this->db->query($consulta);
			$resultado = $resultado->result_array(); 
			$retorno['anios'][$i]['regularizaron'] = count($resultado);
			
			$legajos = '(';
			foreach ($resultado as $legajo) {
				//var_dump($legajo); die;
				if (strlen($legajos) > 1){
					$legajos .= ",'".$legajo['legajo']."'";
				}else{
					$legajos .= "'".$legajo['legajo']."'";
				}
			}
			$legajos .= ')';


			
			
			//se utiliza como referencia para armar el indice del array asociativo de "Aprobaron"
			$referencia_anio = 0;
			for($j = $i; $j <= ($i + 4); $j++){
				//aprobaron en cada año
				
				$consulta = "select count(*)
							from sga_detalle_acta as det
							right join sga_actas_examen as act on act.acta = det.acta
							where act.materia in $codigos_materia
							and act.anio_academico = $j
							and det.legajo in $legajos
							and det.resultado = 'A'
							and det.rectificado = 'N'";
				$resultado = $this->db->query($consulta);
				$resultado = $resultado->result_array(); 
				$retorno['anios'][$i]['aprobaron']['anio+'.$referencia_anio] = intval(array_shift($resultado[0]));
				$referencia_anio++;

				$consulta = "select count(*)
							from sga_detalle_acta as det
							right join sga_actas_examen as act on act.acta = det.acta
							where act.materia in $codigos_materia
							and act.anio_academico = $j
							and det.rectificado = 'N' 
							and legajo in $legajos
							and det.resultado = 'A'";
				$resultado = $this->db->query($consulta);
				$resultado = $resultado->result_array(); 
				//var_dump($resultado); 
				$retorno['anios'][$i]['rindieron_veces'][$j] = intval(array_shift($resultado[0]));

			}
		}
		var_dump($retorno); die;
		return $retorno;
	}

	function desercion($anios_anteriores){
		//desde una determinada cantidad de años anteriores, hasta el año actual
		for($cohorte = (date("Y") - $anios_anteriores); $cohorte <= date("Y"); $cohorte++){
			//obtengo los ingresantes de esa cohorte
			$consulta = "select count(distinct legajo) as ingresantes
						from sga_alumnos
						where fecha_ingreso between mdy(12,01,".($cohorte - 1).") and mdy(11,30,$cohorte)
						and carrera in ('01','02')";
			$resultado = $this->db->query($consulta);
			$resultado = $resultado->result_array()[0]; 
			$cursantes[$cohorte]['ingresantes'] = $resultado['ingresantes'];

			for($anio_academico = $cohorte + 1; $anio_academico <= date("Y"); $anio_academico++){
				//obtengo los cursantes de esa cohorte en el año academico actual
				$consulta = "select count(distinct legajo) as cursantes
							from sga_det_acta_curs as det
							left join sga_actas_cursado as act on act.acta = det.acta
							left join sga_comisiones as com on com.comision = act.comision
							where com.anio_academico = $anio_academico
							and legajo in (
								select distinct legajo
								from sga_alumnos
								where fecha_ingreso between mdy(12,01,".($cohorte - 1).") and mdy(11,30,$cohorte)
								and carrera in ('01','02')
							)";
				$resultado = $this->db->query($consulta);
				$resultado = $resultado->result_array()[0]; 
				
				$cursantes[$cohorte][$anio_academico] = $resultado['cursantes'];
			}
		}
		//var_dump($cursantes); 
		return $cursantes;
	}
}


/*
$datos = array('materia'=>"Matemática I",
					   'anios'  =>array('2011'=>array("cursaron"       =>120,
					   								  "regularizaron"  =>100,
													  'aprobaron'      =>array('anio+0'=>70,
													  				    	 'anio+1'=>20,
													  					     'anio+2'=>5,
													  					     'anio+3'=>3,
													  					     'anio+4'=>2),
													  'rindieron_veces'=>array('1'=>50,
													  						   '2'=>20,
													  						   '3'=>20,
													  						   '4'=>7,
													  						   '5'=>3)),
					   					'2012'=>array("cursaron"       =>160,
					   								  "regularizaron"  =>130,
													  'aprobaron'      =>array('anio+0'=>76,
													  				    	 'anio+1'=>23,
													  					     'anio+2'=>9,
													  					     'anio+3'=>32,
													  					     'anio+4'=>2),
													  'rindieron_veces'=>array('1'=>59,
													  						   '2'=>29,
													  						   '3'=>27,
													  						   '4'=>17,
													  						   '5'=>13) ),
					   					'2013'=>array("cursaron"       =>100,
					   								  "regularizaron"  =>95,
													  'aprobaron'      =>array('anio+0'=>76,
													  				    	 'anio+1'=>12,
													  					     'anio+2'=>1,
													  					     'anio+3'=>15,
													  					     'anio+4'=>3),
													  'rindieron_veces'=>array('1'=>48,
													  						   '2'=>32,
													  						   '3'=>36,
													  						   '4'=>19,
													  						   '5'=>12) ),
					   					'2014'=>array("cursaron"       =>160,
					   								  "regularizaron"  =>130,
													  'aprobaron'      =>array('anio+0'=>76,
													  				    	 'anio+1'=>23,
													  					     'anio+2'=>9,
													  					     'anio+3'=>32,
													  					     'anio+4'=>2),
													  'rindieron_veces'=>array('1'=>59,
													  						   '2'=>29,
													  						   '3'=>27,
													  						   '4'=>17,
													  						   '5'=>13) ),
					   					
				 ));*/
