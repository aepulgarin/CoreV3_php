<?php
class cBase extends mainController{
	function traerIden($parametros){
		$retorna=explode(",",$parametros['retorna']);
		$modelo= new base();
		$modelo->getIden();
		foreach ($retorna as $key => $campo) {
			if($campo!='')
				$resultado->$campo=$modelo->$campo;
			else
				$resultado=$modelo;
		}
		$this->response($resultado);
	}
	function traerSiglas($parametros){
		$modelo= new base();
		$modelo->tabla=$parametros['tabla'];
		$modelo->campo=$parametros['campo'];
		$resultado=$modelo->getSiglas();
		$this->response($resultado);
	}
}	
