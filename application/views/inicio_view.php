<!DOCTYPE html>
<html lang="es">
	<head>
			
		<title>SIU-Guaraní</title>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/script.js"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/jquery-ui.min.css"></style>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/estilos.css"/>
		<link rel="shortcut icon" href="favicon.ico">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.0/Chart.bundle.js"></script>

		
	</head>
	<body>
		<div id="contenedor_error"></div>
		<div id="logo">
			<img src="<?php echo base_url(); ?>assets/img/perm/logo_siu.jpg" alt="SIU-Guaraní" >
			
		</div>
		<div id="tabs">
			<ul>
				<li><a href="#tfg">Condiciones TFG</a></li>
				<li><a href="#const_examen">Const. Examen</a></li>
				<li><a href="#plan_estudios">Plan de Estudios</a></li>
				<li><a href="#correlativas_alumno">Correlativas Alumno</a></li>
				<li><a href="#encuestas">Encuestas</a></li>
				<li><a href="#coneau">CONEAU</a></li>
				<li><a href="#cursadas">Result. Cursadas</a></li>
				<li><a href="#examenes">Result. Examenes</a></li>
				<li><a href="#egresados">Egresados</a></li>
				<!-- <li><a href="#insc_examen">Inscritos Examen</a></li> -->
				<!-- <li><a href="#const_ingresante">Const. Ingresante</a></li> -->
				<!-- <li><a href="#leg_prog">Leg. Progr.</a></li> -->
				<!-- <li><a href="#cert_pendientes">Cert. Pend.</a></li> -->
				<li><a href="#desercion">Deserción</a></li>
				
			</ul>
			<div id="tfg">
				<hr>
				Legajo: <input type="text" name="legajo_tfg" id="txt_legajo_tfg">
				<select name="carrera_tfg" id="carrera_tfg">
					<option value="01">Agronomía</option>
					<option value="08">Industrial</option>
				</select>
				<input type="button" id="cmd_cant_materias" value="Buscar">
				<hr>
				<div class="centrado" id="datos_personales"></div>
				<table>
					<tr><td class="derecha">Plan:</td><td id="plan"></td></tr>
					<tr><td class="derecha">Materias obligatorias:</td><td id="obligatorias"></td></tr>
					<tr><td class="derecha">Optativas (Prod. Vegetal):</td><td id="optativas_vegetal"></td></tr>
					<tr><td class="derecha">Optativas (Prod. Animal):</td><td id="optativas_animal"></td></tr>
					<tr><td class="derecha">Optativas (Otras Areas):</td><td id="optativas_otras_areas"></td></tr>
					<tr><td class="derecha">Total:</td><td id="total"></td></tr>
					<tr><td class="derecha">Tercer año completo?:</td><td id="tercer_anio"></td></tr>
					<tr><td class="derecha">Condicion:</td><td id="condicion"></td></tr>
					<tr><td class="derecha">Calidad:</td><td id="calidad"></td></tr>
					<tr><td class="derecha">Cantidad de requisitos que adeuda:</td><td id="cant_req_adeuda"></td></tr>

					<tr>
						<td class="derecha">Pasantía:</td>
						<td>
							<a href="#" target="_BLANK" class="enlace oculto centrado" id="cmd_rendir_pasantia">RENDIR</a>
							<a href="#" target="_BLANK" class="enlace oculto centrado" id="cmd_realizar_pasantia">REALIZAR</a>
						</td>
					</tr>
					<tr>
						<td class="derecha">Tesina:</td>
						<td class="centrado">
							<a href="#" target="_BLANK" class="enlace oculto centrado" id="cmd_rendir_tesina">RENDIR</a>
							<a href="#" target="_BLANK" class="enlace oculto centrado" id="cmd_realizar_tesina">REALIZAR</a>
						</td>
					</tr>
					
				</table>
				
			</div>
			<div id="const_examen">
				<hr>
				Carrera: <select id="select_carrera_const_examen">
					<option value="01">Agronomía</option>
					<option value="08">Industrial</option>
				</select>
				Legajo: <input type="text" name="legajo_const_exa" id="txt_legajo_const_exa"><input type="button" id="cmd_const_examen" value="Generar">
				<hr>
				<div id="ultimos_examenes" class="ultimos_examenes">
					<table id="tabla_examenes">
						<tr><td>Fecha</td><td>Materia</td><td>Resultado</td><td>Acción</td></tr>
						<tr class="hist_exa"><td id="fecha0"></td><td id="materia0"></td><td id="resultado0"></td><td id="enlace0"></td></tr>
						<tr class="hist_exa"><td id="fecha1"></td><td id="materia1"></td><td id="resultado1"></td><td id="enlace1"></td></tr>
						<tr class="hist_exa"><td id="fecha2"></td><td id="materia2"></td><td id="resultado2"></td><td id="enlace2"></td></tr>
						<tr class="hist_exa"><td id="fecha3"></td><td id="materia3"></td><td id="resultado3"></td><td id="enlace3"></td></tr>
						<tr class="hist_exa"><td id="fecha4"></td><td id="materia4"></td><td id="resultado4"></td><td id="enlace4"></td></tr>
						
					</table>
				</div>
			</div>
			<div id="plan_estudios">
				<hr>
				Legajo: <input type="text" id="txt_legajo_plan_estudios">
				Carrera: <select id="select_carrera_plan_estudios">
					<option value="01">Agronomía</option>
					<option value="08">Industrial</option>
				</select>
				<a href="" id="btn_generar_plan_estudios" target="_BLANK" class="enlace centrado">Generar</a>
			</div>
			<div id="encuestas">
				<div id="encuestas">
					<div id="barra"><div class="centrado procesando">Buscando...</div></div>
						<form action="<?php echo base_url(); ?>index.php/inicio/informe_encuesta" method="POST" TARGET="_blank">
							Alcance:
							<select name="alcance" id="select_alcances">
								
							</select>
							
							<br>
							Materias: 
							<select name="materia" id="select_materias">
								
							</select>
							<br>
							<!--Comisiones: 
							<select name="select_comisiones" id="select_comisiones">
								
							</select>
							<br>-->
							Docentes (de la materia): 
							<select name="docente" id="select_docentes">
								
							</select>
							<br>
							<div id="botones" class="derecha">
							
							<input type="submit" value="Ver Resultados" id="ver_resultados">
							</div>
						</form>
					
				</div>
			</div>
			<div id="coneau">
				<div id="hist_materia">
					<div id="barra"><div class="centrado procesando">Calculando...</div></div>
					Materia: 
					<select name="coneau_materia" id="coneau_materia">
					<input type="button" id="cmd_coneau_buscar" value="Buscar">
							
					</select>
					<label id="promocionable"></label><label id="tiene_final"></label><label id="tipo_materia"></label>

					<table class="tabla_flat" id="tabla_coneau">
						<tr class="cabecera_tabla">
							<td></td>
							<?php for($i = (date("Y") - 8); $i <= date("Y"); $i++ ):  ?>
							<td colspan=2><?php echo $i; ?></td>

							<?php endfor; ?>
						</tr>
						<tr>
							<td></td>
							<?php for($i = (date("Y") - 8); $i <= date("Y"); $i++ ):  ?>
							<td>C</td><td>R</td>
							<?php endfor; ?>
						</tr>
					</table>	
				</div>
				
			</div>
			<div id="cursadas">
				<div id="barra"><div class="centrado procesando">Buscando...</div></div>
				Materia: 
				<select name="curs_materias" id="curs_materias">
						
				</select>
				Año: 
				<select name="curs_anio" id="curs_anio">
					<?php for($i=0; $i<10; $i++): ?>
						<option value="<?php echo (date("Y") - $i); ?>"><?php echo (date("Y") - $i); ?></option>
					<?php endfor; ?>
				</select>
				<select name="curs_periodo" id="curs_periodo">
					<option value="1° bimestre">1° bimestre</option>
					<option value="1° cuatrimestre">1° cuatrimestre</option>
					<option value="1° semestre">1° semestre</option>
					<option value="1° trimestre">1° trimestre</option>
					<option value="2° bimestre">2° bimestre</option>
					<option value="2° cuatrimestre">2° cuatrimestre</option>
					<option value="2° semestre">2° semestre</option>
					<option value="2° trimestre">2° trimestre</option>
					<option value="3° bimestre">3° bimestre</option>
					<option value="3° cuatrimestre">3° cuatrimestre</option>
					<option value="3° semestre">3° semestre</option>
					<option value="3° trimestre">3° trimestre</option>
					<option value="3er. Semestre">3er. Semestre</option>
					<option value="1° anual">ANUAL</option>
				</select>
				<input type="button" value="Buscar" id="btn_historial_curs">
				<div id="resultados_hist_curs" class="oculto">
					<table class="tabla_flat">
						<tr class="cabecera_tabla">
							<td colspan="4" class="centrado">CURSADA</td>
						</tr>
						<tr class="cabecera_tabla">
							<td></td><td>Nuevos Alumnos</td><td>Recursantes</td><td>Total</td>
						</tr>
						<tr>
							<td>Totales</td>
							<td id="nuetot" class="centrado"></td>
							<td id="rectot" class="centrado"></td>
							<td id="tottot" class="centrado"></td>
						</tr>
						<tr>
							<td>Regularizaron</td>
							<td id="nuereg" class="centrado"></td>
							<td id="recreg" class="centrado"></td>
							<td id="totreg" class="centrado"></td>
						</tr>
						<tr>
							<td>Promocionaron</td>
							<td id="nuepro" class="centrado"></td>
							<td id="recpro" class="centrado"></td>
							<td id="totpro" class="centrado"></td>
						</tr>
						<tr>
							<td>Libres</td>
							<td id="nuelib" class="centrado"></td>
							<td id="reclib" class="centrado"></td>
							<td id="totlib" class="centrado"></td>
						</tr>
					</table>
					<table id="hist_examenes" class="tabla_flat">
						<tr class="cabecera_tabla"><td colspan="2">EXAMENES FINALES (REPORTE ANUAL)</td></tr>
						<tr>
							<td>Total</td>
							<td id="totfin" class="centrado"></td>
						</tr>
						<tr>
							<td>Aprobaron</td>
							<td id="aprfin" class="centrado"></td>
						</tr>
						<tr>
							<td>Desaprobaron</td>
							<td id="desfin" class="centrado"></td>
						</tr>
						<tr>
							<td>Ausentes</td>
							<td id="ausfin" class="centrado"></td>
						</tr>
						
					</table>
				</div>
			</div>
			<div id="examenes">
				<div id="barra"><div class="centrado procesando">Buscando...</div></div>
				Año: 
				<select name="anio_examen" id="anio_examen">
					<?php for($i = date("Y"); $i > date("Y")-9; $i--): ?>
					<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php endfor; ?>
				</select>
				Turno: 
				<select name="turno_examen" id="turno_examen">
					
				</select>
				Llamado: 
				<select name="llamado_examen" id="llamado_examen">
					
				</select>
				<input type="button" value="Buscar" id="cmd_resumen_turno_exa">
				<hr>
				<table id="tabla_resumen_turno_exa" class="oculto">
					<tr class="encabezado">
					<td class="centrado">Materia</td>
					<td class="centrado">Ausentes</td>
					<td class="centrado">Aprobados</td>
					<td class="centrado">Reprobados</td>
					<td class="centrado">Total</td>
					</tr>
				</table>
				<h1 class="centrado oculto" id="titulo_horarios">Horarios del Llamado</h1>
				<table id="tabla_horarios_examen" class="oculto">
					<tr class="encabezado">
						<td class="centrado">Materia</td>
						<td class="centrado">Fecha</td>
						<td class="centrado">Hora</td>
					</tr>
				</table>
			</div>
			<div id="egresados">
				<div id="contenedor_egresados">
					
					<input type="button" id="get_egresados" value="Calcular egresados!">
					
					<table id="tabla_egresados" class="tabla_flat">
						
					</table>
					<div class="centrado imagen_procesando" style="display:none;">
						<img src="assets/img/perm/cargando.gif">
					</div>

				</div>
			</div>
			<!-- <div id="insc_examen">
				<div id="lista_materias">
					<form action="<?php echo base_url(); ?>index.php/inicio/pdf_inscritos_examen" method="POST" target="_BLANK">
						Materia: 
						<select id="insc_materias_examen" name="insc_materia">
							
						</select>
						<br><br>
						Turno: 
						<select name="insc_turno" id="insc_turno_examen">
							
						</select>
						<br><br>
						Llamado: 
						<select name="insc_llamado" id="insc_llamado">
							<option value="1">1</option>
							<option value="2">2</option>
						</select>
						<br><br>
						<select name="publicar" id="publicar">
							<option value="1" selected>Publicar</option>
							<option value="0">Solo Generar</option>
						</select>
						<input type="submit" value="Publicar Inscritos" id="publicar_inscritos">
						
					</form>
				</div>
			</div> -->

			<!-- <div id="const_ingresante">
				<div id="filtro_const_ingresante">
					Alumno: <input type="text" id="apellido_ingresante" placeholder="Ingrese parte del nombre o del apellido del alumno"></input>
					<input type="button" value="Para YO VOY" id="cmd_gen_const_yovoy">
					<input type="button" value="Alumno Ingresante" id="cmd_gen_const_ingr">
				</div>
			</div>

			<div id="leg_prog">
				Legajo: <input type="text" id="lu_leg_prog"><input type="button" value="Buscar" id="cmd_leg_prog">
				<div id="contenedor_fechas_regularizadas">
					
				</div>
			</div>
			<div id="cert_pendientes">
				<table id="tabla_cert_pendientes">
					<th>Legajo</th>
					<th>AGR</th>
					<th>Apellido</th>
					<th>Nombres</th>
					<th>Año Ingreso</th>
					<th>Cant. Req. Adeuda</th>
				</table>
				<div id="imprimir_cert" class="derecha">
					<a href="./index.php/inicio/cert_pendientes" target="_BLANK">Imprimir</a>
				</div>

			</div> -->
			<div id="desercion">
				<div class="centrado procesando">Buscando...</div>
				Años anteriores <input type="number" name="anios_anteriores" id="anios_anteriores" value=10>
				<input type="button" name="btn_desercion" id="btn_desercion" value="Calcular deserción">
				<table id="tabla_desercion" border=1>
					
				</table>
			</div>

			<div id="correlativas_alumno">
				<div class="formulario">
					<div class="contenedor_ef">
						<label for="legajo">Legajo:</label>
						<input type="text" id="lu_correlativas_alumno">
					</div>
					<div class="contenedor_ef">
						<label for="carrera_correlativas_alumno">Carrera:</label>
						<select id="carrera_correlativas_alumno">
							<option value="--">-- Seleccione --</option>
							<option value="01">Ingeniería Agronómica</option>
							<option value="08">Ingeniería Industrial</option>
						</select>
					</div>
					<div class="contenedor_ef">
						<label for="materia_correlativas_alumno">Materia:</label>
						<select id="materia_correlativas_alumno">
							
						</select>
					</div>
					<div class="contenedor_ef">
						<label for="fecha_ref_correlativas_alumno">Fecha Referencia:</label>
						<input type="date" id="fecha_ref_correlativas_alumno">
					</div>
					<div class="contenedor_ef">
						<input type="button" id="btn_correlativas_alumno_buscar" value="Buscar" disabled>
					</div>
				</div>
				<div id="contenedor_estado_correlativas">
				</div>
				<template id="estado_correlativas">
					<div class="contenedor_lista_correlativas">
						<div class="titulo_estado_correlativas"></div>
						<ul class="lista_estado_correlativas">
						</ul>
					</div>
				</template>
				<div id="loader_estado_correlativas" class="hidden centrado"><img src="<?php echo base_url(); ?>assets/img/perm/loader-dots.svg" ></div>
					<script src="<?php echo base_url(); ?>assets/js/estado_correlativas.js"></script>
					
				</div>
			</div>
			
		</div>
	
	</body>
</html>

