d = document;
d.addEventListener('DOMContentLoaded', e => {
	headers = new Headers({Authorization: 'Basic ' + btoa('cliente:ClienteGuarani')});
	//url = 'http://10.20.253.90:8080/rest';
	url = 'http://10.30.1.13:83/rest/estado_correlativas';
	$legajo = d.getElementById('lu_correlativas_alumno');
	$carrera = d.getElementById('carrera_correlativas_alumno');
	$materia = d.getElementById('materia_correlativas_alumno');
	$fecha_ref = d.getElementById('fecha_ref_correlativas_alumno');
	setearFechaHoy();
	$boton = d.getElementById('btn_correlativas_alumno_buscar');
	$boton = d.getElementById('btn_correlativas_alumno_buscar');

	$legajo.addEventListener('blur', e => {
		$boton.disabled  = ( ! $legajo.value.length) || $carrera.value == '--';
		if($carrera.value.length) llenarComboMaterias()
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
	d.getElementById('loader_estado_correlativas').classList.toggle('hidden');
	$materia.innerHTML = '';
	$fragmento = d.createDocumentFragment();
	const resultado = await fetch(`${url}/get_materias_plan_alumno/${$carrera.value}/${$legajo.value}`,{headers});
	if(resultado.ok){
		datos = await resultado.json();
		datos.forEach( materia => {
			opt = d.createElement('option');
			opt.value = materia.materia;
			opt.textContent = materia.nombre
			$fragmento.appendChild(opt);
		})
		$materia.appendChild($fragmento);
	}else{
		alert('Error al obtener las materias. Revise la consola');
		console.log('Error al obtener las materias', resultado);
	}
	d.getElementById('loader_estado_correlativas').classList.toggle('hidden');
}

async function procesar(){
	d.getElementById('loader_estado_correlativas').classList.toggle('hidden');
	validaciones = validarCampos();
	if(validaciones.error){
		console.log(validaciones)
		alert(validaciones.errores);
		return;
	}
	let resultado = await fetch(`${url}/estado_correlativas/${$legajo.value}/${$carrera.value}/${$materia.value}/${$fecha_ref.value}`,{headers});
	if(resultado.ok){
		datos = await resultado.json();
		armarTablaEstado(datos);

	}else{
		alert('Error al obtener los datos. Revise la consola');
		console.log('Error al obtener los datos', resultado);
	}
	d.getElementById('loader_estado_correlativas').classList.toggle('hidden');
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



