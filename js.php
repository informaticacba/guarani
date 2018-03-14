<!DOCTYPE html>
<html>
<head>
	<meta charset='UTF-8'>
	<title>Pagina de prueba</title>
</head>
<body>
	<div id="mi_primer_div">
		<input type="text" id="contenido"><input type="button" value="generar" id="generar">
	</div>
</body>
</html>


<script type="text/javascript">
	window.onload = function(){
		console.log(esMayor("#445655","#945566"));
	}
	
	numero = 255;
	console.log(parseInt("44FF55".toString(16)));
	//colorUno = parseInt(colorUno, 16);
	//colorDos = parseInt(colorDos, 16);
	//console.log(numero.toString(16))
	//funcion que devuelve 1 si el primer color es el mayor, en cambio, 
	//devuelve 2 si el segundo color es el mayor. Devuelve 0 si los numeros son iguales
	function esMayor(colorUno, colorDos){
		if( parseInt(colorUno.toString(16)) > parseInt(colorUno.toString(16))) {
			return 1;
		}
		if( parseInt(colorUno.toString(16)) < parseInt(colorUno.toString(16))) {
			return 2;
		}else{
			return 0;
		}
	}
</script>