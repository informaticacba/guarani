<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Encuesta</title>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
	<style>
		*{
			font-family: "Times New Roman", Times, serif;
		}
		body{
		}
		#contenedor{
			width: 215.9mm;
			min-height: 250.4mm;
			margin: auto;
			background-color: #FFFFFF;
			padding-top: 7mm;
		}
		#cuerpo{
			margin: 0mm 0mm 0mm 0mm;
			padding: 0mm 20mm 10mm 20mm;
		}
		#cuerpo h2{
			text-align: center;
			padding: 0mm;
			margin: 2mm 0mm 2mm 0mm;
			font-family: tahoma;
			font-size: 1.2em;
		}
		#datos_encuesta{
			color: #444444;
			padding: 2.5mm 0mm 2.5mm 10mm;
			background-color: #D2E6ED;
			
		}
		
		#datos_encuesta #catedra, #docente{
			font-size: 1.1em;
			
			font-weight: bold;
			line-height: 1.5em;
			
		}
		#resultados{
			margin: 4mm 0mm 0mm 0mm;
		}
		#resultados #preguntas{

		}
		#resultados #preguntas table{
			border: 1px solid #111111;
			width: 100%;
		}
		#resultados #preguntas table th{
			background-color: #DDD;
			padding: 2mm 0mm 2mm 0mm;
			
		}
		#resultados #preguntas table tr td{
			border-bottom: 1px solid #222222;
			font-size: 0.8em;
			padding: 1mm 0mm 1mm 0mm;
		}
		#resultados #preguntas table tr td:nth-child(1){
			font-weight: bolder;
		}
		#resultados #preguntas table tr td:nth-child(3), td:nth-child(4){
			text-align: center;
		}
		#resultados #observaciones{
			
		}
		#resultados #observaciones .observacion{
			border: 1px solid #777777;
			padding: 1mm 1mm 1mm 3mm;
			margin: 0mm 0mm 1mm 0mm;
			border-radius: 6px;

		}

		
	</style>
	
</head>
<body>
	<?php 
		
		if(count($datos) > 0){
			//estas variables sirven para mantener la pregunta actual y no volver a imprimirla mas de una vez
			$pregunta = '';
			$docente = '';
			//array de docentes para listarlos en el encabezado
			$docentes = "";
			//variable que controla los ids para completar los porcentajes
			$indice = 0;
			//array que controla los porcentajes
			$porcentajes = array();
			//variable que controla el total de alumnos que contestaron una determinada pregunta (para calcular porcentajes)
			$total = 0;
		}

	?>
	<div id="contenedor">
		<center><img src="<?php echo base_url(); ?>assets/img/perm/membrete.png" alt="Facultad de Ciencias Agrarias - UNNE" width='550mm' height="120mm;"></center>
		<div id="cuerpo">
			<h2>REPORTE DE ENCUESTAS A ALUMNOS</h2>
			<div id="datos_encuesta">
				
				<div id="catedra">
					<?php 
						if(isset($materia)){ 
							echo "Cátedra: ".strtoupper($materia); 
						} 
					?>
				</div>
				<div id="docente">
					<?php 
					 	if(isset($datos_docente)){ 
							echo "Docente: ".utf8_encode(strtoupper($datos_docente['apellido'])).", ".ucwords(strtolower($datos_docente['nombres'])); 
						} 
					?>
				</div>
			</div>
			<div id="resultados">

			
			<div id="respuestas">
				<div id="docentes"></div>
				<div id="preguntas">
					<table>
						<tr><th>Pregunta</th><th>Opción</th><th>Cant.</th><th>&nbsp;&nbsp;&nbsp;&nbsp;%&nbsp;&nbsp;&nbsp;&nbsp;</th></tr>
						<?php 
							$pregunta = $datos[0]['pregunta'];
							foreach ($datos as $value) {
								if(strtolower($value['pregunta']) == strtolower($pregunta)){
									$total = $total + $value['cantidad'];
								}
							}
							$pregunta = "";
						?>
						<?php //var_dump($datos); ?>
						<?php foreach ($datos as $value) {
							//no se listan las preguntas de observaciones
							if(strtolower($value['pregunta']) == 'observaciones'){
								continue;
							}
							if($pregunta != $value['pregunta'] || $docente != $value['apellido'].$value['nombres']){
								echo '<tr class="respuesta"><td>'.$value['pregunta'].'</td><td>'.$value['opcion'].'</td><td class="centrado">'.$value['cantidad'].'</td><td id="nodo'.$indice.'" class="indice centrado">'.round(intval($value['cantidad']) / intval($total) * intval(100), 2).'%</td></tr>';
								$pregunta = $value['pregunta'];
								$docente = $value['apellido'].$value['nombres'];
								$nombre = $value['apellido'].", ".$value['nombres'];
								//var_dump($docentes); die;
								//si el docente todavia no figura en la lista, se lo agrega al array para mostrarlo en el encabezado
								
								if(strpos($docentes,$nombre)){
									$docentes .= $nombre;
								}
							}else{
								echo '<tr><td></td><td>'.$value['opcion'].'</td><td class="centrado">'.$value['cantidad'].'</td><td id="nodo'.$indice.'" class="indice centrado">'.round(intval($value['cantidad']) / intval($total) * intval(100), 2).'%</td></tr>';
							}
						}	
						?>
				</table>
			</div>	
			
			<div id="observaciones">
			<?php //var_dump($observaciones); die; ?>
					<h2>Observaciones</h2>
					<?php if(count($observaciones) > 0 ): ?>
					<?php foreach($observaciones as $value): ?>
						<div class="observacion">
							<?php echo utf8_encode($value['observaciones']); ?>
						</div>
					<?php endforeach; ?>
					<?php else: ?>
						<div class="observacion">
							No se han registrado observaciones.
						</div>
					<?php endif; ?>

					
				</div>
			</div>
		</div>
	</div>
	<?php include("btn_imprimir.html"); ?>
</body>
</html>



