<?php

$orden_alfabeto = 'zyxwvutsrqponmlkjihgfedcba';
$orden_alfabeto = strrev($orden_alfabeto);
//var_dump( comparar('a','aa',$orden_alfabeto)); die;
$desordenadas = array('aaa','a','eqlq','pxsog', 'abc','cba','pepe');
//$desordenadas = array('estoy','waltwerulio','queso');

var_dump(ordenar_extraterrestre($desordenadas,$orden_alfabeto));

function ordenar_extraterrestre($desordenadas, $orden_alfabeto){
	$orden_alfabeto = strtolower($orden_alfabeto);
	
	
	do{
		$permuta = FALSE;
		for($j = 0; $j < count($desordenadas) -1 ; $j++){
			echo "Comparando si ".$desordenadas[$j]." va antes de ".$desordenadas[$j+1];
			if(  comparar($desordenadas[$j], $desordenadas[$j+1], $orden_alfabeto) > 0 ){ 
				$aux = $desordenadas[$j];
				$desordenadas[$j] = $desordenadas[$j+1];
				$desordenadas[$j+1] = $aux;
				$permuta = TRUE;
				echo "SI SE PERMUTA!!<br>";
			}else{
				echo "NO SE PERMUTA!!<br>";
			} 
		}
	} while ($permuta);
	return $desordenadas;
}



//devuelve -1 si "palabra_uno" aparece antes, 1 si "palabra_dos" aparece antes, y 0 si son iguales
function comparar($palabra_uno, $palabra_dos, $orden_alfabeto){
	$palabra_uno = strtolower($palabra_uno);
	$palabra_dos = strtolower($palabra_dos);
	
	//determino cual de las palabras es la mas corta
	if(strlen($palabra_uno) < strlen($palabra_dos)){
		$long = strlen($palabra_uno);
		$palabra = -1;	
	}elseif(strlen($palabra_uno) > strlen($palabra_dos)){
		$palabra = 1;
		$long = strlen($palabra_dos);	
	}else{
		$palabra = 0;
		$long = strlen($palabra_dos);	
	}
	
	//determino cual de las palabras aparece primero segun el orden del alfabeto
	for($i = 0; $i < $long ; $i++){
		if( strpos($orden_alfabeto, substr($palabra_uno,$i,1)) < strpos($orden_alfabeto, substr($palabra_dos,$i,1)) ){
			return -1;
		}elseif( strpos($orden_alfabeto, substr($palabra_uno,$i,1)) > strpos($orden_alfabeto, substr($palabra_dos,$i,1)) ){
			return 1;
		}else{
			//si ya no quedan caracteres por comparar
			if($i == ($long - 1) ){
				switch ($palabra) {
					case -1:
						return -1;
						break;
					case 1:
						return 1;
						break;
					case 0:
						return 0;
						break;
				}
			}else{
				$palabra_uno = substr($palabra_uno, 1, strlen($palabra_uno) - 1 );
				$palabra_dos = substr($palabra_dos, 1, strlen($palabra_dos) - 1 );
				continue;
			}
		}
	}
}
/*function ordenar_extraterrestre($desordenadas, $orden_alfabeto){
  $ordenadas = array();
    foreach($desordenadas as $palabra){
        $ordenada = '';
        for($i = 0; $i < strlen($palabra); $i++){
            $ordenada .= obtener_par(strpos($orden_alfabeto, substr($palabra,$i,1) ) );
        }
        array_push($ordenadas,$ordenada);
    }
    return $ordenadas;
}
function obtener_par($posicion){
	$alfabeto = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');	
    return $alfabeto[$posicion];
}*/


?>