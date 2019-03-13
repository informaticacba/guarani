<?php
class Generador_plan{
	private $nombres;
	private $apellido;
	private $dni;
	private $genero;
	private $carrera;
	private $ingreso;
	protected $plan;
	private $siglas;

	function set_datos($apellido, $nombres, $dni, $genero, $carrera, $ingreso, $siglas)
	{
		try{
			$this->nombres = $nombres;
			$this->apellido = $apellido;
			$this->dni = substr($dni,0,2).".".substr($dni,2,3).".".substr($dni,5,3);
			$this->genero = $genero;
			$this->ingreso = $ingreso;
			$this->carrera = $carrera;
			$this->siglas = $siglas;

		}catch(Exception $e){
			die("No se han definido todos los parametros necesarios para generar el reporte");
		}
		$this->plan = $this->get_plan();

	}
	private function get_plan()
	{

		switch ($this->carrera) {
			case '01':
				switch ($this->ingreso) {
					case ($this->ingreso < 2002):
						die('Raje!!! Alumno recontra viejo!');
						break;
					case ($this->ingreso >= 2002 && $this->ingreso < 2006):
						$archivo_plan = "./assets/json/planes/agr-2002.json"; 
						break;
					case ($this->ingreso == 2006):

						$archivo_plan = "./assets/json/planes/agr-2006.json";
						break;
					case ($this->ingreso > 2006 && $this->ingreso < 2013):
						$archivo_plan = "./assets/json/planes/agr-2006-intro.json"; 
						break;
					case ($this->ingreso >= 2013 && $this->ingreso < 2015):
						$archivo_plan = "./assets/json/planes/agr-2013.json";
						break;
					case ($this->ingreso >= 2013):
						$archivo_plan = "./assets/json/planes/agr-2013-2015.json";
						break;
				}
				break;
			case '08':
				switch ($this->ingreso) {
					case ($this->ingreso >= 2016):
						$archivo_plan = "./assets/json/planes/ind-2016.json"; 
						break;
				}
				break;
		}
				
		//obtengo la plantilla correspondiente
	
//echo $archivo_plan; die;	
$archivo = file_get_contents($archivo_plan);
//echo $archivo; die;		

return json_decode(utf8_encode($archivo));
	}

	public function generar_reporte()
	{
		//controla el orden que aparece a la izquierda de las materias
		$orden = 1;
		//indices y literales de a�os
		$anios = array('1'=>'PRIMER AÑO','2'=>'SEGUNDO AÑO','3'=>'TERCER AÑO','4'=>'CUARTO AÑO','5'=>'QUINTO AÑO');
		$meses = array('01'=>'enero',
				  '02'=>'febrero',
				  '03'=>'marzo',
				  '04'=>'abril',
				  '05'=>'mayo',
				  '06'=>'junio',
				  '07'=>'julio',
				  '08'=>'agosto',
				  '09'=>'septiembre',
				  '10'=>'octubre',
				  '11'=>'noviembre',
				  '12'=>'diciembre'); 

		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."assets/css/estilos_plan_estudios.css\">
				<div id=\"contenedor_plan\">
					<div id=\"cabecera\">
						<img src=\"".base_url()."assets/img/perm/logo_encabezado.png\" id=\"logo\">
						<!-- <img src=\"".base_url()."assets/img/perm/logo_encabezado.png\" id=\"logo\"> -->
						<p class=\"titulo centrado\">CARRERA ".$this->plan->carrera."</p>
						<p class=\"titulo centrado\">PLAN DE ESTUDIOS 2002";
		if($this->ingreso > 2006 && $this->ingreso < 2013){
			echo " (Modif. 2006)";
		}
if($this->ingreso >= 2013){
echo " (Modif. 2013)";
}
		echo "</p>";
		echo"</div>
					<div id=\"cuerpo_tabla\">
						<table id=\"plan\">
							<th>ORDEN</th>
							<th>REGIMEN</th>
							<th>ASIGNATURA</th>
							<th>CARGA HORARIA</th>";


		foreach ($this->plan->obligatorias as $anio => $materias){
			echo "<tr><td colspan=\"4\" class=\"divisor_anio\">".$anios[$anio]."</td></tr>";
			foreach ($materias as $materia){
				echo "<tr>
						<td class=\"centrado\">".$orden."</td>
						<td class=\"centrado\">".$materia->regimen."</td>
						<td>".$materia->materia."</td>
						<td class=\"centrado\">".$materia->carga."</td>
					</tr>";
				$orden++;
			}
		}

		echo "<tr>";
		$primer_optativa = true;
		foreach ($this->plan->optativas as $optativa){
			echo "<tr>";
			if($primer_optativa){
				echo"<td rowspan=\"".count($this->plan->optativas)."\">OPTATIVAS</td>
					<td class=\"centrado\">".$optativa->regimen."</td>
					<td>".$optativa->materia."</td>
					<td class=\"centrado\">".$optativa->carga."</td>";
				$primer_optativa = false;
			}else{	
				echo "<td class=\"centrado\">".$optativa->regimen."</td>
					<td>".$optativa->materia."</td>
					<td class=\"centrado\">".$optativa->carga."</td>";
			}
			echo "</tr>";
		}
		echo "</tr>
				<tr>
					<td colspan=3 class=\"centrado\"><b>".$this->plan->tfg->descripcion."</b></td>
					<td class='centrado'><b>".$this->plan->tfg->carga."</b></td>
				</tr>
			</table>
			</div>
			<div id=\"datos_alumno\">
				<b>PLAN DE ESTUDIOS</b> perteneciente ";
		echo (intval($this->genero) == 1) ? 'al alumno' : 'a la alumna'; 
		echo " de esta casa de estudios "; 
		echo (intval($this->genero) == 1) ? 'Sr. ' : 'Srta. ';
		echo "<b>".str_replace("-N-","Ñ",strtoupper($this->apellido)).", ".ucwords(str_replace('-n-','ñ',strtolower($this->nombres)))." (D.N.I.: Nº ".$this->dni.").-</b>
			</div>";
		if($this->ingreso > 2006 && $this->ingreso < 2013){
			echo "<div id='actualiz_escala'>
			*Modificado por Resolución Rectoral Nº 381/06 el 19 de agosto de 2006
			</div>";
		}
		echo "<div id=\"fecha\">
				<b>Dirección Gestión de Estudios</b>, ".date('d')." de ".$meses[date('m')]." de ".date('Y').".-
			</div>
		</div>
		<div id='siglas'>".$this->siglas."</div>";
		
	}


	
}

?>
