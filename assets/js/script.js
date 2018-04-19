var detalle_materias;
$(document).ready(function(){

	//aplica el formato de tabs
	$("#tabs").tabs();
	
	//campo principal de busqueda
	$("#txt_legajo_tfg").focus();

	//obtengo datos para filtros
	get_materias_result_curs();
	get_turnos_examen();

	//obtengo los certificados pendientes de impresion
	cert_pendientes();

	/* ---------------------------------------- ESTADO PARA EL TFG --------------------------------------------*/
	//al presionar enter se busca el estado para TFD
	$("#txt_legajo_tfg").on('keypress',function(e){
		//si el usuario presiona "Enter"
		if(e.keyCode == 13 ){
			get_estado_tfg();
		}
	})
	//lo mismo ocurre al hacer click al boton "Buscar"
	$("#cmd_cant_materias").on("click",function(){
		get_estado_tfg();
	})

	//primero reviso si ingresó un legajo para buscar
	function get_estado_tfg(){
		if($("#txt_legajo_tfg").prop("value").length == 0){
			return false;
		}
		estado_tfg($("#txt_legajo_tfg").prop("value"));
	}
	/* ---------------------------------------- ------------------ --------------------------------------------*/

	

	/* ---------------------------------------- EGRESADOS --------------------------------------------*/
	$("#get_egresados").on("click",function(){
		$("#tabla_egresados").children().remove();
		$.ajax({
			url: "./index.php/ajax/get_egresados",
			dataType: 'json',
			method: 'POST',
			beforeSend: function(){
				$(".imagen_procesando").css("display","block");
				$("#get_egresados").prop("value","Calculando...");
				$("#get_egresados").attr("disabled","disabled");
			},
			complete: function(){
				$(".imagen_procesando").css("display","none");
				$("#get_egresados").removeAttr('disabled');
				$("#get_egresados").prop("value","Calcular egresados!");
			},
			error: function(err){
				console.log(err)
			},
			success: function(r){
				cabecera = "<tr class='cabecera_tabla'><td>Cohorte/Egreso</td>";
				for(cohorte in r){
					for(anio in r[cohorte]){
						cabecera = cabecera + "<td class='centrado'>" + anio + "</td>";
					}
					cabecera = cabecera + "<td>Total</td>";
					break;
				}
				cabecera = cabecera + "</tr>";

				$("#tabla_egresados").append(cabecera);
				for(cohorte in r){
					sumafila = 0;
					var fila = "";
					fila = fila+"<tr><td class='centrado'>"+cohorte+"</td>";
					for(anio in r[cohorte]){
						sumafila += r[cohorte][anio];
						fila = fila+"<td class='centrado'>"+r[cohorte][anio]+"</td>";
					}
					fila = fila + "<td class='centrado'>"+sumafila+"</td></tr>";
					$("#tabla_egresados").append(fila);
				}
				
			}
		})
		
	})
	/* ----------------------------------------------------------------------------------------------------------*/
	/* ---------------------------------------- DESERCION --------------------------------------------*/
		$("#btn_desercion").on("click",function(e){
			actual = new Date().getFullYear();
			desde = actual - $("#anios_anteriores").prop("value");
			$.ajax({
				url: "./index.php/ajax/desercion",
				dataType: 'json',
				method: "POST",
				data: {anios_anteriores: $("#anios_anteriores").prop("value")},

			beforeSend: function(){
				modo_procesando(true);
			},
			complete: function(){
				modo_procesando(false);
			},
			error: function(err){
				console.log(err)
			},
			success: function(r){
				$("#tabla_desercion").empty();
				fila = "<th>Cohorte</th><th>Ingresantes</th>";
				for(j = new Date().getFullYear() - $("#anios_anteriores").prop("value"); j <= new Date().getFullYear(); j++){
					fila += "<th>"+j+"</th>";
				}
				fila = "<tr>"+fila+"</tr>";
				$("#tabla_desercion").append(fila);

				$.each(r, function(cohorte,datos){
					
					fila = "<tr>";
					fila += "<td>"+cohorte+"</td>";
					fila += "<td>"+datos.ingresantes+"</td>";
					for(var i = actual - $("#anios_anteriores").prop("value"); i <= actual; i++ ){
						if(i<=cohorte){
							fila += "<td style='background-color:grey;'></td>";
						}else{
							fila += "<td>"+datos[i]+"</td>";	
						}
						
					}
					fila += "</tr>";
					$("#tabla_desercion").append(fila);


				})
				
			}
				
			})
		})
	/* ---------------------------------------- CONSTANCIA DE EXAMEN --------------------------------------------*/
	//al presionar enter se buscan las ultimas materias aprobadas
	$("#txt_legajo_const_exa").on('keypress',function(e){
		if(e.keyCode == 13 ){
			e.preventDefault();
			ultimos_examenes( $("#txt_legajo_const_exa").prop("value"), 3);
		}
	})
	$("#cmd_const_examen").on("click",function(){
		ultimos_examenes( $("#txt_legajo_const_exa").prop("value"), 3);
	})
	/* ---------------------------------------- ------------------ --------------------------------------------*/

	


	/* ---------------------------------------- MOSTRAR BOTON PARA GENERAR PDF --------------------------------------------*/
	$("#cmd_gen_pdf").on("click",function(evento){
		evento.preventDefault()
		$("#contenido_imprimir").prop("value",$("#zona_imprimir").html());
		$("#form_pdf").submit();
	});

	/* ---------------------------------------- ------------------ --------------------------------------------*/


	/* ---------------------------------------- RESULTADOS DE CURSADAS -----------------------------------------*/	
	$("#btn_historial_curs").on("click",function(){
		get_historial_materia();

	})


	/* ---------------------------------------- ------------------ --------------------------------------------*/

	/* ---------------------------------------- ACTUALIZAR LISTA DE TURNOS DE EXAMEN -----------------------------------------*/	
	$("#anio_examen").on("change",function(){
		get_turnos_examen();
	})
	$("#turno_examen").on("change",function(){
		get_llamados_examen();
	})
	$("#cmd_resumen_turno_exa").on("click",function(){
		get_reporte_examenes();
	})
	/* ---------------------------------------- ------------------ --------------------------------------------*/
	
	/* --------------------------------------------------------------------------------------------------*/
	$("#publicar_inscritos").on("click",function(e){
		if( $("#publicar").prop("value") == 1 ){
			if( ! confirm("Publicar lista de inscriptos en la web de Anuncios?")){
				e.preventDefault();
			}	
		}
		

	})

	/* --------------------------------------------------------------------------------------------------*/

	$("#cmd_leg_prog").on("click",function(){
		$.ajax({
			url: "./index.php/ajax/get_fechas_regularizadas/"+$("#lu_leg_prog").prop("value"),
			dataType: 'json',
			method: 'POST',
			error: function(err){
				//console.log(err)
			},
			
			success: function(r){
				//console.log(r); return false;
				$("#contenedor_fechas_regularizadas").children().remove();
				tabla = document.createElement("table")
				tabla.id = "tabla_fechas_regularizadas";
				tabla.class += (" pepe") ;
				$(tabla).append("<th>Materia</th><th>Fecha</th><th>En esta fecha...</th>");
				//console.log(r); return false;
				
				for(var j=0; j<r.length; j++){
					fecha = r[j].fecha.substr(0,2)+"/"+r[j].fecha.substr(5,2)+"/"+r[j].fecha.substr(0,4);
					$(tabla).append("<tr><td>"+r[j].nombre+"</td><td>"+fecha+"</td><td>"+r[j].forma_aprov+"</td>");
				}
				$("#contenedor_fechas_regularizadas").append(tabla)
			}
		})
	})

	$("#lu_leg_prog").on('keypress',function(evento){
		if(evento.keyCode == 13){
			$("#cmd_leg_prog").click();
		}
	})

	/* ----------------------------- OBTIENE EL HISTORIAL DE UNA MATERIA PARA DATOS CONEAU */
	
	$("#cmd_coneau_buscar").on("click",function(){
		$.each(detalle_materias, function(index,detalle){
			//console.log(detalle.materia)
			if(detalle.materia == $("#coneau_materia option:selected").text()){
				cod = "";
				$.each(detalle.codigos, function(index, value){
					if(cod.length == 0){
						cod += "'"+String(value)+"'";	
					}else{
						cod += ",'"+String(value)+"'";	
					}
					
				})
				//console.log(detalle);
				$.ajax({
					url: "./index.php/ajax/get_datos_coneau",
					data: {codigos:cod,promocionable:detalle.promocionable,tipo_materia:detalle.tipo},
					dataType: 'json',
					method: 'POST',
					beforeSend: function(){
						$("#coneau .procesando").css("display","block");
						$("#tabla_coneau .info").remove();
					},
					complete: function(){	
						$("#coneau .procesando").css("display","none");
					},
					error: function(err){
						$("#contenedor_error").html(err.responseText);
					},
					success: function(r){
						//console.log(r); 
						//obtengo el año actual (se trabaja con hasta 8 años antes)
						actual = new Date().getFullYear();
						//variables que van a contener los inscriptos por categoria
						insc    = "";
						regu    = "";
						promo   = "";
						finales = "";
						
						//porcentajes
						porcentajes = "";
						max_porcentaje = 0;
						min_porcentaje = 100;

						//totales
						suma_porcentajes = 0;
						anios_computados = 0;


						$cant_anios_atras = 8;
						for(var anio = (actual-$cant_anios_atras); anio <= actual; anio++){
							reg_o_promo = 0;
							inscriptos = 0;
							// ------------------------- INSCRIPTOS ------------------------------
							if(r.inscriptos_nuevos){
								insc += "<td>"+r.inscriptos_nuevos[anio]+"</td>";
								inscriptos += parseInt(r.inscriptos_nuevos[anio]);
							}else{
								insc += "<td>0</td>";
							}

							if(r.inscriptos_recursantes){
								insc += "<td>"+r.inscriptos_recursantes[anio]+"</td>";
								inscriptos += parseInt(r.inscriptos_recursantes[anio]); 
							}else{
								insc += "<td>0</td>";
							}
							//----------------------------REGULARIZADOS-----------------------------
							if(r.regulares_nuevos){
								regu += "<td>"+r.regulares_nuevos[anio]+"</td>";
								reg_o_promo += parseInt(r.regulares_nuevos[anio]);
							}else{
								regu += "<td>0</td>";
							}
							if(r.regulares_recursantes){
								regu += "<td>"+r.regulares_recursantes[anio]+"</td>";
								reg_o_promo += parseInt(r.regulares_recursantes[anio]);
							}else{
								regu += "<td>0</td>";
							}
							//----------------------------PROMOCIONARON-----------------------------
							if(r.promocionados_nuevos){
								promo += "<td>"+r.promocionados_nuevos[anio]+"</td>";
								reg_o_promo += parseInt(r.promocionados_nuevos[anio]);
							}else{
								promo += "<td>0</td>";
							}
							if(r.promocionados_recursantes){
								promo += "<td>"+r.promocionados_recursantes[anio]+"</td>";
								reg_o_promo += parseInt(r.promocionados_recursantes[anio]);
							}else{
								promo += "<td>0</td>";
							}
							//----------------------------FINALES-----------------------------
							if(r.finales){
								if(r.finales[anio].aprobados){
									finales += "<td>"+r.finales[anio].aprobados+"</td>";
								}else{
									finales += "<td>0</td>";
								}
								//console.log(anio+": "+parseInt(r.finales[anio].reprobados));
								finales += "<td>"+parseInt(parseInt(r.finales[anio].reprobados)+parseInt(r.finales[anio].aprobados))+"</td>";
								
							}else{
								finales = "<td colspan=18 class='centrado'>No corresponde</td>";
							}
							//console.log(reg_o_promo+" ---- "+inscriptos); return false;
							// ------------------------- PORCENTAJE DE REGULARIZACION/PROMOCION --------------------------
							
							porc_actual = ((reg_o_promo/inscriptos)*100);

							if(porc_actual > max_porcentaje){
								max_porcentaje = porc_actual;
							}
							if(porc_actual != 0 && ! isNaN(porc_actual)){
								console.log(porc_actual);
								suma_porcentajes += parseFloat(porc_actual);
								anios_computados++;
								if(porc_actual < min_porcentaje){
									min_porcentaje = porc_actual;
								}
							}
							if(!isNaN(porc_actual)){
								porcentajes += "<td colspan='2'>"+porc_actual.toFixed(2)+"%</td>";	
							}else{
								porcentajes += "<td colspan='2'>0%</td>";	
							}
							
							
						}

						porcentaje_promedio = (suma_porcentajes/anios_computados);
						console.log(suma_porcentajes+" --- "+anios_computados);
						$("#tabla_coneau").append("<tr class='info'><td>INSCRIPTOS</td>"+insc+"</tr>");
						$("#tabla_coneau").append("<tr class='info'><td>REGULARIZARON</td>"+regu+"</tr>");
						$("#tabla_coneau").append("<tr class='info'><td>PROMOCIONARON</td>"+promo+"</tr>");
						$("#tabla_coneau").append("<tr class='info'><td>PORCENTAJE DE REG/POROM</td>"+porcentajes+"</tr>");
						$("#tabla_coneau").append("<tr class='info'><td>Porcentajes límites de regularización/promoción: </td><td colspan='2'>Mínimo:</td><td colspan='2' style='color:red; font-weight:bolder;'>"+min_porcentaje.toFixed(2)+"%</td><td colspan='2'>Máximo</td><td colspan='2' style='color:green; font-weight:bolder;'>"+max_porcentaje.toFixed(2)+"%</td><td colspan='2'>Promedio:</td><td colspan='2' style='color:blue; font-weight:bolder;'>"+porcentaje_promedio.toFixed(2)+"%</td></tr>");
						$("#tabla_coneau").append("<tr class='subtitulo_tabla info'><td rowspan=2>FINALES</td><td>Apr</td><td>Total</td><td>Apr</td><td>Total</td><td>Apr</td><td>Total</td><td>Apr</td><td>Total</td><td>Apr</td><td>Total</td><td>Apr</td><td>Total</td><td>Apr</td><td>Total</td><td>Apr</td><td>Total</td><td>Apr</td><td>Total</td></tr>");
						$("#tabla_coneau").append("<tr class='info'>"+finales+"</tr>");



					}


				})

				
			}
			
		})
		
	})
	/*---------------------------------------------------------------------------------------*/
	$("#txt_legajo_plan_estudios").on("change",function(){
		actualizar_enlace_generar_plan_estudios();
	})
	$("#select_carrera_plan_estudios").on("change",function(){
		actualizar_enlace_generar_plan_estudios();
	})



})

function actualizar_enlace_generar_plan_estudios(){
	var legajo = $("#txt_legajo_plan_estudios").prop('value');
	var carrera = $("#select_carrera_plan_estudios").prop('value');
	var enlace = 'index.php/inicio/generar_plan_estudios/'+legajo+"/"+carrera+"/mfa";
	$("#btn_generar_plan_estudios").attr('href',enlace);
	
}

function datos_constancia(tipo_constancia){
	
	console.clear()
	if( alumnos[ $('#apellido_ingresante').prop('value') ] ){
		var nombres = alumnos[$('#apellido_ingresante').prop('value')].datos.nombres;
		var apellido = alumnos[$('#apellido_ingresante').prop('value')].datos.apellido;
		var nro_inscripcion = alumnos[$('#apellido_ingresante').prop('value')].datos.nro_inscripcion;
		var legajo = alumnos[$('#apellido_ingresante').prop('value')].datos.legajo;
		var tipodoc = alumnos[$('#apellido_ingresante').prop('value')].datos.tipodoc;
		var nro_documento = alumnos[$('#apellido_ingresante').prop('value')].datos.nro_documento;
		var sexo = alumnos[$('#apellido_ingresante').prop('value')].datos.sexo;
		$("#apellido_ingresante").prop("value","");	
		//console.log(apellido);
		window.open("./index.php/inicio/pdf_const_ingresante/"+legajo+"/"+nro_inscripcion+"/"+tipodoc+"/"+nro_documento+"/"+apellido+"/"+nombres+"/"+sexo+"/"+tipo_constancia,'_blank');
	}else{
		alert("El alumno no existe");
	}
}

function no_disponible(){
	alert("Funcionalidad no disponible!");
}


function estado_tfg(legajo_tfg){
	$.ajax({
		url: "./index.php/ajax/condicion_tfg/"+legajo_tfg,
		data: {legajo_tfg: legajo_tfg },
		dataType: 'json',
		method: 'POST',
		error: function(err){
			alert("Error, revisar consola");
			console.log(err.responseText);
		},
		success: function(r){
			

			if(r.datos_personales.nombres){
				$("#datos_personales").html(r.datos_personales.apellido.replace("-N-","&Ntilde;")+', '+r.datos_personales.nombres.replace("-N-","Ñ")+' (D.N.I.:'+r.datos_personales.nro_documento+')')
				$("#plan").html(r.datos_personales.plan);	
				$("#obligatorias").html(r.obligatorias);	
				$("#optativas_vegetal").html(r.optativas.produccion_vegetal+' de 4');	

				
				if(r.optativas.produccion_vegetal >= 4){
					$("#optativas_vegetal").css("color","#0CAD2A");
				}else{
					$("#optativas_vegetal").css("color","#FF0F0F");
				}
				$("#optativas_animal").html(r.optativas.produccion_animal+' de 2');
				if(r.optativas.produccion_animal >= 2){
					$("#optativas_animal").css("color","#0CAD2A");
				}else{
					$("#optativas_animal").css("color","#FF0F0F");
				}
				$("#optativas_otras_areas").html(r.optativas.otras_areas+' de 4');
				if(r.optativas.otras_areas >= 4){
					$("#optativas_otras_areas").css("color","#0CAD2A");
				}else{
					$("#optativas_otras_areas").css("color","#FF0F0F");
				}
				var total = parseInt(r.obligatorias)+parseInt(r.optativas.produccion_vegetal)+parseInt(r.optativas.produccion_animal)+parseInt(r.optativas.otras_areas);
				var total_optativas = +parseInt(r.optativas.produccion_vegetal)+parseInt(r.optativas.produccion_animal)+parseInt(r.optativas.otras_areas);
				$("#total").html(total);	

				if(r.tercer_anio){
					$("#tercer_anio").html("Completo");		
					$("#tercer_anio").css("color","#0CAD2A");
				}else{
					$("#tercer_anio").html("Incompleto!!");
					$("#tercer_anio").css("color","#FF0F0F");
				}
				if(r.regular){
					$("#condicion").html("Regular");		
				}else{
					$("#condicion").html("NO REGULAR!!");
				}
				
				switch(r.calidad){
					case 'E':
						$("#calidad").html("Egresado")
						break;
					case 'A':
						$("#calidad").html("Activo")
						break;
					case 'P':
						$("#calidad").html("Pasivo")
						break;
					case 'N':
						$("#calidad").html("Abandono")
						break;
				}
				$("#cant_req_adeuda").html(r.requisitos_adeuda);
				
				$("#tfg .oculto").each(function(){
					$(this).css("display","inline-block");
				})	
				
				if(r.datos_personales.sexo == 1){
					titulo = "sr";
				}else{
					titulo = "srta";
				}
				
				$("#cmd_realizar_pasantia").prop("href","./index.php/inicio/ver_reporte/realizar_pasantia/"+r.datos_personales.apellido.replace(/ /g,"_")+"/"+r.datos_personales.nombres.replace(/ /g,"_")+"/"+r.datos_personales.nro_documento+"/pasantia/"+r.datos_personales.sexo+"/"+parseInt(r.obligatorias)+"/"+parseInt(total_optativas)+"/0/"+"mfa");
				$("#cmd_realizar_tesina").prop("href","./index.php/inicio/ver_reporte/realizar_tesina/"+r.datos_personales.apellido.replace(/ /g,"_")+"/"+r.datos_personales.nombres.replace(/ /g,"_")+"/"+r.datos_personales.nro_documento+"/tesina/"+r.datos_personales.sexo+"/"+parseInt(r.obligatorias)+"/"+parseInt(total_optativas)+"/0/"+"mfa");
				$("#cmd_rendir_pasantia").prop("href","./index.php/inicio/ver_reporte/rendir_pasantia/"+r.datos_personales.apellido.replace(/ /g,"_")+"/"+r.datos_personales.nombres.replace(/ /g,"_")+"/"+r.datos_personales.nro_documento+"/pasantia/"+r.datos_personales.sexo+"/"+parseInt(r.obligatorias)+"/"+parseInt(total_optativas)+"/0/"+"mfa");
				$("#cmd_rendir_tesina").prop("href","./index.php/inicio/ver_reporte/rendir_tesina/"+r.datos_personales.apellido.replace(/ /g,"_")+"/"+r.datos_personales.nombres.replace(/ /g,"_")+"/"+r.datos_personales.nro_documento+"/tesina/"+r.datos_personales.sexo+"/"+parseInt(r.obligatorias)+"/"+parseInt(total_optativas)+"/0/"+"mfa");
				
			}else{
				alert('Legajo inexistente!');
			}
		}
	})
}

function ultimos_examenes(legajo_const_exa, cantidad){
	$(".hist_exa td").html("");

	$.ajax({
		url: "./index.php/ajax/ultimos_examenes/"+legajo_const_exa+"/"+cantidad,
		dataType: 'json',
		method: 'POST',
		error: function(err){
			alert("Legajo Inexistente o el alumno no es activo");
			console.log(err);
		},
		success: function(r){
			//console.log(r)
			if( r.length == 0 ){
				//console.log(r);
				alert("No tiene historial de examenes.");
				return false;
			}
			
			for(var i = 0; i < r.length; i++){
				$("#fecha"+i).html(r[i].fecha_de_examen.substr(8,2)+'/'+r[i].fecha_de_examen.substr(5,2)+'/'+r[i].fecha_de_examen.substr(0,4));
				$("#materia"+i).html(r[i].materia);
				if(r[i].resultado == 'A' || r[i].resultado == 'R'){
				switch(r[i].nota) {
				    case '1':
						var literal = 'Insuficiente'
						break;
					case '2':
						var literal = 'Insuficiente'
						break;
					case '3':
						var literal = 'Insuficiente'
						break;
					case '4':
						var literal = 'Insuficiente'
						break;
					case '5':
						var literal = 'Insuficiente'
						break;
					case '6':
						var literal = 'Aprobado'
						break;
					case '7':
						var literal = 'Bueno'
						break;
					case '8':
						var literal = 'Muy Bueno'
						break;
					case '9':
						var literal = 'Distinguido'
						break;
					case '10':
						var literal = 'Sobresaliente'
						break;
				}
					$("#resultado"+i).html(literal+" ("+r[i].nota+")");
					$("#enlace"+i).html("<a target='_BLANK' href='./index.php/inicio/ver_reporte/examen/"+r[i].apellido.replace(/ /g,"_")+"/"+r[i].nombres.replace(/ /g,"_")+"/"+r[i].nro_documento+"/0/0/0/0/"+r[i].nota+"/mfa/"+r[i].materia.replace(/ /g,"_")+"/"+r[i].fecha_de_examen+"/"+legajo_const_exa+"'>Generar</a>");
				}else{ //cuando resultado = 'U'
					$("#resultado"+i).html('Ausente');
				}
				
			}
			
		}
	})
}

/*function buscar_alcances(){
	$('#select_alcances').children().remove();
	$("#tabla_respuestas").children().remove();
	$("#observaciones").children().remove();
	$.ajax({
		url: "./index.php/ajax/get_alcances",
		dataType: 'json',
		method: 'POST',
		error: function(err){
			console.log(err)
		},
		success: function(r){
			$.each(r, function (index, valor) {
				$('#select_alcances').append($('<option value='+valor.alcance+'>'+valor.titulo+'</option>'));
			});
			encuestas_get_materias()
		}
	})
}*/

function encuestas_get_materias(){
	$('#select_materias').children().remove();
	/*$('#select_comisiones').children().remove();*/
	$('#select_docentes').children().remove();
	$("#tabla_respuestas").children().remove();
	$("#observaciones").children().remove();
	$.ajax({
		url: "./index.php/ajax/encuestas_get_materias",
		data: {id_alcance: $('#select_alcances').prop("value")},
		dataType: 'json',
		method: 'POST',
		beforeSend: function(){$("#encuestas .procesando").css("display","block")},
		complete: function(){$("#encuestas .procesando").css("display","none")},
		error: function(err){
			//console.log(err)
		},
		success: function(r){
			if(r.length > 0){
				$.each(r, function (index, valor) {
					$('#select_materias').append($('<option value='+valor.materia+'>('+valor.materia+') '+valor.nombre+'</option>'));
				})
				/*encuestas_get_comisiones();*/
				encuestas_get_docentes();
				$("#ver_resultados").css("display","inline");
			}else{
				$("#ver_resultados").css("display","none");
			}
			
		}

	})
}


function encuestas_get_docentes(){
	$('#select_docentes').children().remove();
	$("#tabla_respuestas").children().remove();
	$("#observaciones").children().remove();
	$.ajax({
		url: "./index.php/ajax/encuestas_get_docentes",
		data: {id_alcance: $('#select_alcances').prop("value"), id_materia: $('#select_materias').prop("value")},
		dataType: 'json',
		method: 'POST',
		beforeSend: function(){$(".procesando").css("display","block")},
		complete: function(){$(".procesando").css("display","none")},
		error: function(err){
			//console.log(err)
		},
		success: function(r){
			
			if(r.length > 0 ){
				
				$.each(r, function (index, valor) {
					if(valor.legajo){
						$('#select_docentes').append($('<option value='+valor.legajo+'>'+valor.nombre+'</option>'));
					}
				})
				$('#select_docentes').prepend('<option value=0>No buscar por docente</option>');
			}

			
			
		}
	})
}


function mostrar_observaciones(){
	$("#observaciones").children().remove();
	$('#observaciones').append($('<h3>OBSERVACIONES</h3>'));
	$.ajax({
		url: "./index.php/ajax/obtener_observaciones",
		data: {id_alcance: $('#select_alcances').prop("value"), 
			   id_materia: $('#select_materias').prop("value"),
			   legajo_docente: $('#select_docentes').prop("value")
			   
			},
		dataType: 'json',
		method: 'POST',
		beforeSend: function(){$(".procesando").css("display","block")},
		complete: function(){$(".procesando").css("display","none")},
		error: function(err){
			//console.log(err)
		},
		success: function(r){
			//console.log(r)
			if(r.length > 0){
				
				$.each(r, function (index, valor) {
					$('#observaciones').append($('<div class="observacion">'+valor.observacion+"</div>"));
				})
			}
			
			
		}
	})
	
}



function get_materias_result_curs(){
	$("#curs_materias").children().remove();
	$("#coneau_materia").children().remove();
	
	$.ajax({
		url: "./index.php/ajax/get_materias_curs",
		dataType: 'json',
		method: 'POST',
		beforeSend: function(){$(".procesando").css("display","block")},
		complete: function(){$(".procesando").css("display","none")},
		error: function(err){
			console.log(err)
		},
		success: function(r){
			//console.log(r)
			if(r.length > 0){
				detalle_materias = r;
				//console.log(r);
				$.each(r, function (index, valor) {
					opcion = '<option value="(';
					for(var i=0; i<valor.codigos.length; i++){
						if(i == 0){
							opcion = opcion+"'"+valor.codigos[i].toString()+"'";
						}else{
							opcion = opcion+",'"+valor.codigos[i].toString()+"'";
						}
					}
					opcion = opcion+')">'+valor.materia+"</option>";
					$("#curs_materias").append(opcion);
					
					//completo el select de materias para ver inscriptos a examenes
					if(r[index].tiene_final){
						$("#insc_materias_examen").append(opcion);
					}

					//completo el select para materias de Historial CONEAU
					$("#coneau_materia").append(opcion);
					
				})
			}
		}
	})
}

function get_historial_materia(){
	$.ajax({
		url: "./index.php/ajax/get_historial_materia",
		data: {materia: $("#curs_materias").prop("value"),
				anio: $("#curs_anio").prop("value"),
				curs_periodo: $("#curs_periodo").prop("value")},
		dataType: 'json',
		method: 'POST',
		beforeSend: function(){$(".procesando").css("display","block")},
		complete: function(){$(".procesando").css("display","none")},
		error: function(err){
			console.log(err)
		},
		success: function(r){
			//console.log(r);
			total = parseInt(r.recursantes_promocionaron)+parseInt(r.nuevos_promocionaron)+
					parseInt(r.recursantes_libres)+parseInt(r.nuevos_libres)+
					parseInt(r.recursantes_regularizaron)+parseInt(r.nuevos_regularizaron);
			
			nuevos = parseInt(r.nuevos_regularizaron)+parseInt(r.nuevos_libres)+parseInt(r.nuevos_promocionaron);
			
			recursantes = parseInt(r.recursantes_regularizaron)+parseInt(r.recursantes_libres)+parseInt(r.recursantes_promocionaron);
			

			$("#resultados_hist_curs").css("display","block");
			//alumnos que cursaron por primera vez y regularizaron
			$("#nuereg").html(r.nuevos_regularizaron);
			//alumnos recursantes que regularizaron
			$("#recreg").html(r.recursantes_regularizaron);
			//total de alumnos que regularizaron ese año
			$("#totreg").html(parseInt(r.recursantes_regularizaron)+parseInt(r.nuevos_regularizaron));
			//alumnos que cursaron por primera vez, y quedaron libres
			$("#nuelib").html(r.nuevos_libres);
			//alumnos recursantes que volvieron a quedar libres
			$("#reclib").html(r.recursantes_libres);
			//total de alumnos que quedaron libres ese año
			$("#totlib").html(parseInt(r.recursantes_libres)+parseInt(r.nuevos_libres));
			//alumnos que cursaron por primera vez y promocionaron
			$("#nuepro").html(r.nuevos_promocionaron);
			//alumnos recursantes que promocionaron
			$("#recpro").html(r.recursantes_promocionaron);
			//total de alumnos que promocionaron ese año
			$("#totpro").html(parseInt(r.recursantes_promocionaron)+parseInt(r.nuevos_promocionaron));
			
			total = parseInt(r.recursantes_promocionaron)+parseInt(r.nuevos_promocionaron)+
					parseInt(r.recursantes_libres)+parseInt(r.nuevos_libres)+
					parseInt(r.recursantes_regularizaron)+parseInt(r.nuevos_regularizaron);
			
			nuevos = parseInt(r.nuevos_regularizaron)+parseInt(r.nuevos_libres)+parseInt(r.nuevos_promocionaron);
			
			recursantes = parseInt(r.recursantes_regularizaron)+parseInt(r.recursantes_libres)+parseInt(r.recursantes_promocionaron);
			parseInt(r.finales_aprobados)+parseInt(r.finales_ausentes)+parseInt(r.finales_reprobados)
			$("#nuetot").html(nuevos);
			$("#rectot").html(recursantes);
			$("#tottot").html(total);
			total_finales = parseInt(r.finales_aprobados)+parseInt(r.finales_reprobados)+parseInt(r.finales_ausentes);
			$("#aprfin").html(r.finales_aprobados);			
			$("#desfin").html(r.finales_reprobados);
			$("#ausfin").html(r.finales_ausentes);
			$("#totfin").html(total_finales+" ("+(total_finales - r.finales_ausentes)+")" );
		}
	})
}

function get_turnos_examen(){
	$.ajax({
		url: "./index.php/ajax/get_turnos_examen",
		data: {anio: $("#anio_examen").prop("value")},
		dataType: 'json',
		method: 'POST',
		beforeSend: function(){$(".procesando").css("display","block")},
		complete: function(){
			$(".procesando").css("display","none")
			get_llamados_examen($("#anio_examen").prop("value"), $("#turno_examen").prop("value"));
		},
		error: function(err){
			console.log(err)
		},
		success: function(r){
			//console.log(r);
			$("#turno_examen").children().remove();
			for (var i = 0; i < r.length; i++) {
				$("#turno_examen").append("<option value='"+r[i].turno_examen+"'>"+r[i].nombre+"</option>")
				$("#insc_turno_examen").append("<option value='"+r[i].turno_examen+"'>"+r[i].nombre+"</option>")
			};
			
		}
	})
	
}

function get_llamados_examen(){
	$("#llamado_examen").children().remove();
	$.ajax({
		url: "./index.php/ajax/get_llamados_examen",
		data: {anio: $("#anio_examen").prop("value"),
				turno: $("#turno_examen").prop("value")},
		dataType: 'json',
		method: 'POST',
		beforeSend: function(){$(".procesando").css("display","block")},
		complete: function(){$(".procesando").css("display","none")},
		error: function(err){
			console.log(err)
		},
		success: function(r){
			//console.log(r);
			
			for (var i = 0; i < r.length; i++) {
				$("#llamado_examen").append("<option value='"+r[i].llamado+"'>"+r[i].llamado+"</option>")
			};
			
		}
	})
}


function get_reporte_examenes(){
	$(".materia").remove();
		
	$.ajax({
		url: "./index.php/ajax/get_reporte_examenes",
		data: {anio: $("#anio_examen").prop("value"),
				turno: $("#turno_examen").prop("value"),
				llamado: $("#llamado_examen").prop("value")},
		dataType: 'json',
		method: 'POST',
		beforeSend: function(){$(".procesando").css("display","block")},
		complete: function(){$(".procesando").css("display","none")},
		error: function(err){
			//$("body").append("<div>"+err.responseText+"</div>");
			console.log(err.responseText);
		},
		success: function(r){
			$("#tabla_resumen_turno_exa").css("display","block");
			for(var i = 0; i < r.length; i++ ){
				
				if(!r[i].ausentes){
					r[i].ausentes = 0;
				}
				if(!r[i].aprobados){
					r[i].aprobados = 0;
				}
				if(!r[i].reprobados){
					r[i].reprobados = 0;
				}
				total = parseInt(r[i].ausentes)+parseInt(r[i].aprobados)+parseInt(r[i].reprobados);
				$("#tabla_resumen_turno_exa").append("<tr class='materia'><td>"+r[i].materia+"</td><td class='centrado'>"+r[i].ausentes+"</td><td class='centrado'>"+r[i].aprobados+"</td><td class='centrado'>"+r[i].reprobados+"</td><td class='centrado'>"+total+"</td></tr>");
			}			
		}
	}).complete(function(){
		$.ajax({
			url: "./index.php/ajax/horarios_llamado",
			data: {anio: $("#anio_examen").prop("value"),
					turno: $("#turno_examen").prop("value"),
					llamado: $("#llamado_examen").prop("value")},
			dataType: 'json',
			method: 'POST',
			beforeSend: function(){$(".procesando").css("display","block")},
			complete: function(){$(".procesando").css("display","none")},
			error: function(err){
				console.log(err)
			},
			success: function(horarios){
				console.log(horarios);
				$("#tabla_horarios_examen").css("display","block");
				$("#titulo_horarios").css("display","block");
				/*console.log(horarios);
				return false;*/
				for(var i = 0; i < horarios.length; i++ ){
					fecha = horarios[i].fecha.substr(8,2)+'/'+horarios[i].fecha.substr(5,2)+'/'+horarios[i].fecha.substr(0,4);
					$("#tabla_horarios_examen").append("<tr class='materia'><td>"+horarios[i].nombre+"</td><td class='centrado'>"+fecha+"</td><td class='centrado'>"+horarios[i].hora_inicio+"</td></tr>");
				}			
			}
		})
	})
}

function cert_pendientes(){
	
	$.ajax({
		url: "./index.php/ajax/get_cert_pendientes",
		dataType: 'json',
		method: 'POST',
		error: function(err){
			//console.log(err)
		},
		success: function(r){
			//console.log(r)
			$.each(r, function (index, valor) {
				clase="";
				anio = new Date().getFullYear();
				registro = "";
				if( (parseInt(anio) == parseInt(valor['fecha_ingreso'])) || parseInt(valor['adeuda']) > 0 ){
					clase = "fila_ingresante"
				};
				registro = $("<tr class='"+clase+"'><td>"+valor['legajo']+
								 "</td><td>"+valor['agr']+
								 "</td><td>"+valor['apellido']+
								 "</td><td>"+valor['nombres']+
								 "</td><td class='centrado'>"+valor['fecha_ingreso']+
								 "</td><td class='centrado'>"+valor['adeuda']+
								 "</td></tr>");
				$("#tabla_cert_pendientes").append(registro);
			});
			
		}
	})

	
}

function modo_procesando(habilitado){
	//si se está activando el modo "Procesando..."
	if(habilitado){
		//pantalla de calculo de egresados
		if($(".imagen_procesando")){
			$(".imagen_procesando").css("display","block");	
			$("#get_egresados").prop("value","Calculando...");
			$("#get_egresados").attr("disabled","disabled");
		}
		$(".procesando").css("display","block");
		
	}else{
		//pantalla de calculo de egresados
		if($(".imagen_procesando")){
			$(".imagen_procesando").css("display","none");	
			$("#get_egresados").prop("value","Calcular egresados!");
			$("#get_egresados").removeAttr('disabled');
		}
		$(".procesando").css("display","none");
	}
	
	
	
}