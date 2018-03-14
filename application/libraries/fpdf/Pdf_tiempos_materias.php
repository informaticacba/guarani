<?php 
require_once 'Fpdf.php';

class Pdf_tiempos_materias extends Fpdf{
	protected $datos;

	//constructor de clase
	function __construct(){
		//constructor de la clase padre
		parent::__construct();
		//agrego una hoja al documento
		$this->Addpage('P','A4');
		//inicializa las propiedades de fuente
		$this->set_fuente();

	}

	private function set_fuente($params = array("familia"   => "Times",
												"estilo"    => "",
												"tamanio"   => 12,
												"colorFondo"=> array(255,255,255),
												"colorTexto"=> array(0,0,0)
												)
								)
	{
		if(array_key_exists("familia",$params))
		{
			$this->setFont($params['familia']);
		}
		if(array_key_exists("estilo",$params))
		{
			$this->setFont("",$params['estilo']);
		}
		if(array_key_exists("tamanio",$params))
		{
			$this->setFontSize($params['tamanio']);
		}
		if(array_key_exists("colorFondo",$params))
		{
			$this->setFillColor($params['colorFondo'][0],$params['colorFondo'][1],$params['colorFondo'][2]);
		}
		if(array_key_exists("colorTexto",$params))
		{
			$this->setTextColor($params['colorTexto'][0],$params['colorTexto'][1],$params['colorTexto'][2]);
		}
	}

	protected function set_datos($datos)
	{
		//var_dump($datos);die;
		if ( ! array_key_exists('materia',$datos)){
			$this->datos['materia'] = "Sin especificar";
		}else{
			$this->datos['materia'] = $datos['materia'];
		}
		if( array_key_exists('anios',$datos) ){
			foreach($datos['anios'] as $anio => $detalles){
				if ( ! array_key_exists('cursaron',$detalles)){
					$this->datos['anios'][$anio]['cursaron'] = 0;
				}else{
					$this->datos['anios'][$anio]['cursaron'] = $detalles['cursaron'];
				}
				if ( ! array_key_exists('regularizaron',$detalles)){
					$this->datos['anios'][$anio]['regularizaron'] = 0;
				}else{
					$this->datos['anios'][$anio]['regularizaron'] = $detalles['regularizaron'];
				}
				if ( ! array_key_exists('aprobaron',$detalles)){
					$this->datos['anios'][$anio]['aprobaron'] = array("anio+0"=>0,"anio+1"=>0,"anio+2"=>0,"anio+3"=>0,"anio+4"=>0);
				}else{
					$this->datos['anios'][$anio]['aprobaron'] = $detalles['aprobaron'];
				}
				if ( ! array_key_exists('rindieron_veces',$detalles)){
					$this->datos['anios'][$anio]['rindieron_veces'] = array("1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0);
				}else{
					$this->datos['anios'][$anio]['rindieron_veces'] = $detalles['rindieron_veces'];
				}	
			}
		}else{
			die("No se establecieron datos de los años considerados");
		}
		
	}

	//$destino puede recibir "pantalla" o "descarga"
	function mostrar($datos = array(), $destino = "pantalla")
	{
		$margen = 20;
		$alto_celda = 5;
		$this->SetLeftMargin($margen);
			/*$datos = array('materia'=>"Matemática I",
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
		//establezco los datos recibidos
		$this->set_datos($datos);

		//título del documento
		$this->setTitle(utf8_decode($this->datos['materia']));
		
		//Nombre de la Materia
		$this->set_fuente(array("tamanio"=>16,"estilo"=>"ub"));
		$this->Cell(0,$alto_celda,"Materia: ".utf8_decode($this->datos['materia']),0,1,"C",false);

		foreach ($this->datos['anios'] as $anio => $detalles) {
			//var_dump($detalles); die;
			//salto de linea
			$this->Ln();

			//Año que se está evaluando
			$this->set_fuente(array("tamanio"=>10,"colorTexto"=>array(50,50,50),"estilo"=>"","colorFondo"=>array(172,236,255)));
			$this->Cell(0,$alto_celda,utf8_decode("Año: ").$anio,1,1,'C',true);

			//Cuantos cursaron en el año considerado, y cuantos regularizaron
			$this->Cell(50,$alto_celda,"Cursaron: ".$detalles['cursaron'],0,0,'L',false);

			//cuadro que muestra el año
			$this->set_fuente(array("estilo"=>"b"));
			$this->Cell(50,$alto_celda,"Regularizaron: ".$detalles['regularizaron'],0,1,'L',false);

			//vuelvo a los parametros iniciales de fuente
			$this->set_fuente();
			//salto de linea
			$this->Ln();
			//guardo la ubicación vertical para mostrar el segundo cuadro a la derecha
			$y = $this->getY();

			//tabla de cantidad de años de aprobación
			$this->Cell(75,$alto_celda,"De los regularizados, aprobaron el Final:",1,1,'C',true);
			$tiempos = 0;
			$suma = 0;

			for($i=0; $i<5; $i++){
				$this->Cell(25,$alto_celda,"En ".($anio + $i),1,0,'C',false);
				$this->Cell(25,$alto_celda,$detalles['aprobaron']['anio+'.$i],1,0,'C',false);

				//pondero los tiempos que tardaron en aprobar, en funcion de las cantidades de alumnos
				$tiempos += ($i*$detalles['aprobaron']['anio+'.$i]);
				$suma += $detalles['aprobaron']['anio+'.$i];

				//si el número de regularizados es cero, se evita el error en la división
				if ($this->datos['anios'][$anio]['regularizaron']){
					$this->Cell(25,$alto_celda,round(($detalles['aprobaron']['anio+'.$i]*100/$detalles['regularizaron']),2).'%',1,1,'C',false);	
				}else{
					$this->Cell(25,$alto_celda,"0%",1,1,'C',false);
				}

			}

			$tiempo_promedio = $tiempos / $suma;

			$this->set_fuente(array("tamanio"=>10,"estilo"=>"bi"));
			$this->MultiCell(75,$alto_celda,utf8_decode("Promedio de años antes de lograr aprobar el final: ".round($tiempo_promedio,2)." años"),1,1,'L',false);
			$this->set_fuente();
			//desplazamiento del segundo cuadro
			$x = $margen + 85;
			$this->setXY($x,$y);

			//tabla de cantidad de mesas rendidas
			$this->Cell(75,$alto_celda,"De los regularizados, rindieron:",1,1,'C',false);
			$mesas = 0;
			$suma = 0;

			for($i=1; $i<=5; $i++){
				switch ($i) {
					case 1:
						$texto = "1 vez";
						break;
					case 5:
						$texto = "5+ veces";
						break;
					default:
						$texto = "$i veces";
						break;
				}
				$this->setX($x);
				$this->Cell(25,$alto_celda,$texto,1,0,'C',false);
				$this->Cell(25,$alto_celda,$detalles['rindieron_veces'][$i],1,0,'C',false);

				$mesas += ($i*$detalles['rindieron_veces'][$i]);
				$suma += $detalles['rindieron_veces'][$i];

				
				//si el número de regularizados es cero, se evita el error en la división
				if ($this->datos['anios'][$anio]['regularizaron']){
					$this->Cell(25,$alto_celda,round($detalles['rindieron_veces'][$i]*100/$detalles['regularizaron'],2).'%',1,1,'C',false);	
				}else{
					$this->Cell(25,$alto_celda,"0%",1,1,'C',false);
				}
				
			}
			//calculo la cantidad de mesas promedio que rinden antes de lograr aprobar
			$cantidad_promedio = $mesas / $suma;
			
			//establezco la ubicación, la fuente, y muestro el promedio de mesas		
			$this->SetX($x);
			$this->set_fuente(array("tamanio"=>10,"estilo"=>"bi"));
			$this->MultiCell(75,$alto_celda,utf8_decode("Promedio de mesas rendidas antes de lograr aprobar: ".round($cantidad_promedio,2)." mesas"),1,1,'L',false);

			
			
			


			
		}
		//opciones de visualización
		if (strtolower($destino) == 'pantalla'){
			$destino = 'I';		
		}else{
			$destino = 'D';
		}
		$this->Output($this->datos['materia']." - $anio.pdf",$destino);
		
	}
}

//$prueba = new PDF_tiempos_materias();
//$prueba->mostrar();

?>