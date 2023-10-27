<?php
/**
 * Explicacion corta del contenido de archivos y funciones
 *
 * @author       Alvaro Pulgarin <aepulgarin@lebon.com.co>
 * @copyright    Alvaro Pulgarin Y-M-D
 * @category     Area
 * @package      Modulo
 * @subpackage   SubModulo
 * @version         Version
 */
class correo extends mainModel {  
	public $iva;  
	                                                                    
	public function __construct()  
	{  
		$this->Conectarse();
		$this->ejecuta_query("SET ISOLATION TO DIRTY READ");
	}
	}  
