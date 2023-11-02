<?php
//namespace App;
$plantilla="layout.html";
use App\Business\ProgramaOpcionBusiness;
class ProgramaOpcionController extends \Core\mainController{
    private ProgramaOpcionBusiness $ProgramaOpcion;
    public function __construct () {
        $this->ProgramaOpcion = new ProgramaOpcionBusiness();
    }
    public function traerOpciones($params){
        $this->defaultMethod(
            $this->ProgramaOpcion->traerOpciones((object)$params)
        );
    }
    public function grabarOpcionPrograma($params){
        $this->defaultMethod(
            $this->ProgramaOpcion->grabarOpcionPrograma((object) $params)
        );
    }
    public function borrarOpcionPrograma($params){
        $this->defaultMethod(
            $this->ProgramaOpcion->borrarOpcionPrograma((object) $params)
        );
    }

	function grabarGeneral($parametros){

		#validaciones
		$programa=strtoupper($parametros['form']['programa']);
		$descripcion=strtoupper($parametros['form']['descripcion']);
		$menu=$parametros['form']['menu'];
		$controladorJs=$parametros['form']['controladorJs'];
		$autenticado=$parametros['form']['autenticado'];

		$myProg = new programa($programa);

		$myProg->descripcion=$descripcion;
		$menu_ant=$myProg->menu;//backup id menu anterior
		$myProg->menu=$menu;

		$myProg->begin_work();
		$myProg->grabarGeneral();

		#CREAMOS TEMPLATES
		if($myProg->existe=='N'){
			$rutaTemplate=MODULE_PATH."1/programa/template/";

			$carpetaMenu=MODULE_PATH.$myProg->menu;
			Template::makeDir($carpetaMenu);

			$carpetaModulo=MODULE_PATH.$myProg->menu."/".strtolower($programa);
			Template::makeDir($carpetaModulo);			

			###---MODELO
			$data=Template::getFile($rutaTemplate."modelo.php");
			$data=str_replace("[[programa]]",strtolower($programa), $data);
			$archivoModelo=$carpetaModulo."/modelo.php";
			Template::putFile($archivoModelo,$data);

			###---NEGOCIO
			$data=Template::getFile($rutaTemplate."negocio.php");
			$data=str_replace("[[programa]]",strtolower($programa), $data);
			$archivoModelo=$carpetaModulo."/negocio.php";
			Template::putFile($archivoModelo,$data);

			###---CONTROLADOR PHP
			if($controladorJs=='S'){
				$data1=Template::getFile($rutaTemplate."controlador_servicio.php");	
			}

			$data=str_replace("[[programa]]",strtolower($programa), '<?php'."\n".'$plantilla="default/default.html";'."\n".$data1."\n".$data2.'?>');
			$archivoControlador=$carpetaModulo."/controlador.php";
			Template::putFile($archivoControlador,$data);

			###---CONTROLADOR JS
			if($controladorJs=='S'){
				$data=Template::getFile($rutaTemplate."controlador.js");
				$data=str_replace("[[programa]]",strtolower($programa), $data);
				$mfuncionesJS=explode(",",str_replace(" ", "", $funcionesJs));
				if(is_array($mfuncionesJS)){
					foreach ($mfuncionesJS as $nombre) {
						if($nombre!=''){
							$data.="\n".'function '.$nombre.'(){'."\n".'	alert("funcion '.$nombre.'");'."\n".'}'."\n";
						}
					}					
				}
				$archivoModelo=$carpetaModulo."/controlador.js";
				Template::putFile($archivoModelo,$data);
			}

			###---VISTA
			$data=Template::getFile($rutaTemplate."vista.html");
			$data=str_replace("[[programa]]",strtolower($programa), $data);
			$data=str_replace("[[titulo]]",ucwords(strtolower($programa)), $data);
			$data=str_replace("[[subtitulo]]",ucfirst(strtolower($descripcion)), $data);
			$archivoModelo=$carpetaModulo."/vista.html";
			Template::putFile($archivoModelo,$data);

			### Crea primer opcion automaticamente
			$myProg->agregarPermiso('A','Acceso');
			$myProg->grabarPermisos();
			$respuesta = array(
				'programa' => $programa,
				'permiso' => $myProg->id."-A-".$myProg->id_p
			);		
		}else{

			if($menu_ant!='' && $menu != $menu_ant){
				if(!file_exists(MODULE_PATH."$menu/")){
					mkdir(MODULE_PATH."$menu/");
					chmod(MODULE_PATH."$menu/", 0777);
				}
				rename(MODULE_PATH."$menu_ant/".strtolower($programa), MODULE_PATH."$menu/".strtolower($programa));
			}

			if(!file_exists(MODULE_PATH.$archivo_id)){
				touch(MODULE_PATH.$archivo_id);
				chmod(MODULE_PATH.$archivo_id, 0777);
			}
			$respuesta = $programa;
		}

		$myProg->commit();		
		$this->response($respuesta);	
}

function traerComponentes(){
	$gestor=opendir(COMPONENT_PATH);
	while (false !== ($componente=readdir($gestor))) {
		$file_ini=COMPONENT_PATH."/$componente/config.ini";	
		if(file_exists($file_ini)){
			$config=parse_ini_file($file_ini);
			$mcomponentes[$componente]=$config;
		}
	}
	$this->response($mcomponentes); 
}

function buscarPrograma($parametros){
	$programa=strtoupper($parametros['programa']);
	$myProg= new programa($programa);
	$myProg->getPermisos();
	$this->response($myProg);
}

function eliminarPrograma($parametros){
	$programa=$parametros['programa'];
	$myProg= new programa($programa);
	$carpetaModulo=MODULE_PATH.$myProg->menu."/".strtolower($programa);
	Template::rmDir($carpetaModulo);
	$myProg->eliminarPrograma();
	$this->response(array("resultado"=>"success")); 	
}

function GrabarPermisos($parametros){
	$programa = strtoupper($parametros['programa']);		
	$permisos =  explode(",",$parametros['form']['permiso']);
	$descripciones =  explode(",",$parametros['form']['descripcion']);
	$myProg = new programa($programa);

	foreach ($permisos as $key => $permiso) {
		$permiso=strtoupper($permiso);
		$descripcion=trim($descripciones[$key]);
		if($permiso!=''){
			$myProg->agregarPermiso(trim($permiso),trim($descripcion));				
		}
	}

	$myProg->begin_work();
	$myProg->grabarPermisos();
	$myProg->commit();
	$this->response($programa);	
}

function grabarComponentes($parametros){
	$componentes = explode(",",$parametros['form']['componentes']);
	$programa = $parametros['programa'];

	$myProg= new programa($parametros['programa']);
	$componetes_anteriores=Template::getFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/componentes.ini");

	if($componetes_anteriores!=''){
		$componetes_anteriores= explode("\n",$componetes_anteriores);
	}else{
		$componetes_anteriores=array();
	}		
	#crear archivo de configuracion de cada programa
	if(is_array($componentes)){
		foreach ($componentes as $key => $value) {
			$contenido.=$value.PHP_EOL;
		}
	}
	Template::putFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/componentes.ini",$contenido,'w+');

	if(is_array($componentes)){
		foreach ($componentes as $key => $value) {
			if(!in_array($value,$componetes_anteriores)){
				#cargar la JS demo del componente
				$componenteJS=COMPONENT_PATH."/$value/config.ini";
				if(file_exists($componenteJS)){
					$config=parse_ini_file($componenteJS);
					if($config['templateJS']!=''){
						$config['templateJS']=str_replace("[[programa]]", strtolower($programa), $config['templateJS']);
						$dataAnt=Template::getFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/controlador.js");
						$dataAnt=str_replace('$(document).ready(function() {', '$(document).ready(function() {'.$config['templateJS'], $dataAnt);
						Template::putFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/controlador.js",$dataAnt,'w+');
					}
				}
				#cargar template el demo del componente
				$templateHTML=COMPONENT_PATH."/$value/template.html";
				if(file_exists($templateHTML)){
					$templateHTML=Template::getFile($templateHTML);
					$templateHTML=str_replace("[[programa]]", strtolower($programa), $templateHTML);
					Template::putFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/vista.html",$templateHTML);
				}
			}
			$contenido.=$value.PHP_EOL;
		}
	}		
	//$myProg->grabarPermisos();
	$this->response($programa);	
}

function getOpciones(){
	$programa= new programa();
	$opciones=$programa->getOpciones();
	for ($i=0; $i <count($opciones) ; $i++) { 
		$opciones[$i]->nombre=utf8_encode($opciones[$i]->nombre);
	}
	$this->response($opciones); 		
}

function getMenuProgramas(){
	$programa= new programa();
	$menu=$programa->getMenuProgramas(0);
	$this->response($menu); 		
}
function getPermiso($parametros){
	$programa= new programa(strtoupper($parametros['programa']));
	$tienePermiso=$programa->getPermiso(strtoupper($parametros['opcion']),false);
	$this->response(array("permiso"=>$tienePermiso,"listado"=>$programa->permisos)); 	
}

}//FIN CLASS



