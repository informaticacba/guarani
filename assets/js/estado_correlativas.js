d = document;
d.addEventListener('DOMContentLoaded', e => {
	headers = new Headers({Authorization: 'Basic ' + btoa('cliente:ClienteGuarani')});
	url = 'http://10.20.253.90:8080/rest';
	//url = 'http://10.30.1.13:83/rest/estado_correlativas';
	$legajo = d.getElementById('lu_correlativas_alumno');
	$carrera = d.getElementById('carrera_correlativas_alumno');
	$materia = d.getElementById('materia_correlativas_alumno');
	$fecha_ref = d.getElementById('fecha_ref_correlativas_alumno');
	setearFechaHoy();
	$boton = d.getElementById('btn_correlativas_alumno_buscar');
	$boton = d.getElementById('btn_correlativas_alumno_buscar');

	$legajo.addEventListener('blur', e => {
		$boton.disabled  = ( ! $legajo.value.length) || $carrera.value == '--';
		if($carrera.value != '--') llenarComboMaterias()
	});
	$carrera.addEventListener('change', e => {
		$boton.disabled  = ( ! $legajo.value.length) || $carrera.value == '--';
		if($legajo.value.length) llenarComboMaterias()
	});

	$boton.addEventListener('click',procesar)

});

function armarTablaEstado(datos){
	d.getElementById('contenedor_estado_correlativas').innerHTML = '';
	correlativas = {'cursar':[],'aprobar':[]};
	
	
	//Este bucle ordena las condiciones
	datos.detalles.forEach( materia => {
		if(materia.condicion == 'A'){
			correlativas.aprobar.push(materia);
		}else{
			correlativas.cursar.push(materia);
		}
	});


	$template = d.getElementById('estado_correlativas').content;
	for(condicion in correlativas){
		clon = document.importNode($template,true);
		$titulo = clon.querySelector('.titulo_estado_correlativas');
		$lista = clon.querySelector('.lista_estado_correlativas');

		$titulo.textContent = (condicion == 'cursar') ? 'Correlativas para Cursar' : 'Correlativas para Aprobar/Promocionar';
		
		correlativas[condicion].forEach( materia => {
			let li = document.createElement('li');
			let cond_anterior = (materia.condicion_anterior == 'C') ? 'Cursada' : 'Aprobada'; 
			li.textContent = `${cond_anterior}: (${materia.materia_anterior}) ${materia.materia_anterior_nombre}`;
			if( ! materia.cumplido){
				li.classList.add('incumplido');
			}
			$lista.appendChild(li);
		})
		
		d.getElementById('contenedor_estado_correlativas').appendChild(clon);
	}



	console.log(correlativas);


}

async function llenarComboMaterias(){
	procesando(true);
	//guarda la opci�n seleccionada, y si vuelve a existir, se asigna como seleccionada
	seleccionPrevia = ($materia.selectedIndex != -1) ? $materia.options[$materia.selectedIndex].value : null;
	//se vacia el commbo
	$materia.innerHTML = '';
	//Fragmento para pegar las opciones que se traen del service
	$fragmento = d.createDocumentFragment();
	//Obtengo los datos
	const resultado = await fetch(`${url}/get_materias_plan_alumno/${$carrera.value}/${$legajo.value}`,{headers});
	
	if(resultado.ok){
		datos = await resultado.json();
		if( ! datos.length){
			alert('No se encontraron materias con los criterios ingresados');
			procesando(false);
			return;	
		} 

		datos.forEach( materia => {
			opt = d.createElement('option');
			opt.value = materia.materia;
			opt.textContent = materia.nombre
			$fragmento.appendChild(opt);
		})
		$materia.appendChild($fragmento);
		//Si existía alguna materia seleccionada antes de la carga, se la vuelve a seleccionar 
		//(facilita cuando se busca alumnos de la misma materia)
		if(seleccionPrevia){
			opcion = $materia.querySelectorAll(`option[value='${seleccionPrevia}']`);
			if(opcion) opcion[0].selected = true;
		}
	}else{
		alert('Error al obtener las materias. Revise la consola');
		console.log('Error al obtener las materias', resultado);
	}
	procesando(false);
}

function procesando(activo){
	etiqueta = d.getElementById('loader_estado_correlativas');

	if(activo && etiqueta.classList.contains('hidden')){
		etiqueta.classList.remove('hidden');
		$boton.disabled = true;
	}
	if( ! activo && ! etiqueta.classList.contains('hidden')){
		etiqueta.classList.add('hidden');	
		$boton.disabled = false;
	}
}

async function procesar(){
	procesando(true);
	validaciones = validarCampos();
	if(validaciones.error){
		console.log(validaciones)
		alert(validaciones.errores);
		return;
	}
	let resultado = await fetch(`${url}/estado_correlativas/${$legajo.value}/${$carrera.value}/${$materia.value}/${$fecha_ref.value}`,{headers});
	if(resultado.ok){
		datos = await resultado.json();
		if(datos.length){
			armarTablaEstado(datos);
		}else{
			alert('No se encontraron datos');
		}


	}else{
		alert('Error al obtener los datos. Revise la consola');
		console.log('Error al obtener los datos', resultado);
	}
	procesando(false);
}



function setearFechaHoy(){
	$fecha_ref.value = new Date().toLocaleDateString('es', { year: 'numeric', month: '2-digit', day: '2-digit' }).split('/').reverse().join('-');
}

function validarCampos(){
	let errores = [];
	let error = false;
	if($legajo.value.length == 0){
		errores.push('Debe ingresar un numero de legajo');
		error = true;
	}
	if($carrera.value == '--'){
		errores.push('Debe seleccionar una carrera');
		error = true;	
	}
	if( ! $fecha_ref){
		setearFechaHoy();
	}
	if(error){
		errores = errores.join("\n");
	}

	return {error, errores};
}



