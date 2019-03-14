<!DOCTYPE html>
<html lang="en">
<head>
	
	<title>Certificado</title>
	<style>
		*{
			font-family: "Times New Roman", Times, serif;
		}
		body{
			/*background-color: #CCCCCC;*/
		}
		#contenedor{
			width: 215.9mm;
			height: 250.4mm;
			margin: auto;
			background-color: #FFFFFF;
			padding-top: 7mm;
		}
		
		#cuerpo{
			margin: 9mm 0mm 0mm 0mm;
			padding: 0mm 18mm 0mm 18mm;
		}
		#cuerpo p{
			text-indent: 15mm;
			word-wrap: break-word;
			line-height: 10mm;
			text-align: justify;
			font-style: italic;
			font-size: 1.05em;
			margin: 0px;

		}
		#firma{
			padding: 0mm 30mm 0mm 30mm;
		}
		#firma p{
			text-align: right;
		}
		#linea_firma{
			margin-top: 20mm;
		}
		#autor{
			font-size: 0.8em;
			margin: 15mm 0mm 0mm 30mm;
			font-style: italic;
		}
		
	</style>

	<?php //extract($_GET); ?>
	<?php $carrera = ($carrera == '01') ? 'INGENIERÍA AGRONÓMICA' : 'INGENIERÍA INDUSTRIAL'; ?>
	<?php $anio = substr($fecha,0,4); $mes = substr($fecha,5,2); $dia = substr($fecha,8,2);?>
	<?php $meses = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'); ?>
	<?php $escala = array(1=>'Insuficiente',2=>'Insuficiente',3=>'Insuficiente',4=>'Insuficiente',5=>'Insuficiente',6=>'Aprobado',7=>'Bueno',8=>'Muy Bueno',9=>'Distinguido',10=>'Sobresaliente'); ?>
</head>
<body>
	<div id="contenedor">
		<center><img src="<?php echo base_url(); ?>assets/img/perm/logo_encabezado.png" alt="Facultad de Ciencias Agrarias - UNNE" width='600mm' height="140mm;"></center>
		
		<div id="cuerpo">
			<p>La que suscribe <b>D</b>irectora <b>G</b>esti&oacute;n <b>E</b>studios de la <b>F</b>ACULTAD <b>D</b>E <b>C</b>IENCIAS <b>A</b>GRARIAS dependiente de la <b>UNNE, HACE CONSTAR Que: <?php echo str_replace(array("_","-N-"),array(" ","Ñ"),strtoupper($apellido)); ?>, <?php echo ucwords(strtolower(str_replace(array("_","-N-"),array(" ","Ñ"),strtoupper($nombres)))); ?> (D.N.I. Nº <?php echo substr($dni, 0,2).".".substr($dni, 2,3).".".substr($dni, 5,3); ?>), Legajo Nº <?php echo $legajo; ?></b>, alumno/a de la carrera <b>“<?php echo $carrera; ?>”</b> ha rendido la asignatura <b>“<?php echo str_replace("_"," ",strtoupper($materia)); ?>”</b> el día <?php echo $dia; ?> de <?php echo $meses[$mes-1]; ?> del año <?php echo $anio; ?> con nota <?php echo $escala[$nota]; ?> (<?php echo $nota; ?>).-</p>
			<p>A pedido de parte interesada y a los efectos de ser presentada ante las autoridades que lo requieran, se extiende la presente que sella y firma en la ciudad de Corrientes, el día <?php echo date("d"); ?> de <?php echo $meses[date('n')-1]; ?> de <?php echo date("Y"); ?>.-</p>
		</div>
		<div id="firma">
			
			<p id="linea_firma">______________________</p>
		</div>
		<div id="autor">
			<?php 
				if(isset($autor)){
					for($i = 0; $i < strlen($autor); $i++ ){
						echo substr($autor,$i,1)."."; 	
					}	
				}
			?>
		</div>
	</div>
	<?php include("btn_imprimir.html"); ?>	
</body>


</html>