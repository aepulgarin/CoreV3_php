<?php
namespace Core;
use App\Business\ProgramaBusiness;
class mainTemplate {
	public $modulo;
	public $template="core/template/layout.html";
	public $VistaModulo='vista.html';
	private $arrayXajaxDefault = array ("xajax_cargando()");
	private $arrayHead = array ();
	private $arrayFooter = array ();

	public function __construct(){
	}
	public function AgregarCSS($file){
		if(file_exists($file)){
			$templateCSS="<link rel='stylesheet' type='text/css' href='$file'/>";
			$this->AgregarHead($templateCSS);
			return true;
		}else{
			return false;
		}
	}
	public function AgregarJS($file){
		$templateCSS="<script type='text/javascript' src='$file?".date("Ymdh")."'></script>";
		$this->AgregarFooter($templateCSS);
	}

	private function AgregarHead ($html){
		$this->arrayHead[]=$html;
	}
	private function AgregarFooter ($html){
		$this->arrayFooter[]=$html;
	}
	
	public function getFile ($file){

		$data = '';
		$link = @fopen($file,'r');
		if ($link){
			$size=filesize($file);
			if($size==0) $size=1;
			$data = fread($link,$size);
			fclose($link);
		}
		return $data;
	}
	public function putFile ($file,$data,$method='a+'){
		$link = @fopen($file,$method);
		if ($link){
			fputs($link,$data);
			fclose($link);
		}
		@chmod($file, 0777);
	}
	public function makeDir($dir){
		if(!file_exists($dir)){
			if(mkdir($dir)!==false){

			}else{
				die("Problema con permisos en las  carpetas ($dir), verifique");
			}

			@chmod($dir, 0777);
		}
	}
	public function rmDir($dir){
		if(file_exists($dir)){
			$files = glob($dir . '/*', GLOB_MARK);
		    foreach ($files as $file) {
		        if (is_dir($file)) {
		            self::rmDir($file);
		        } else {
		            unlink($file);
		        }
		    }
			@rmdir($dir);
		}
	}

	public function cargarTemplate(){
		$Programa=new ProgramaBusiness();
		$Programa->crearLogAcceso();

		#trae template
		$fileTemplate = TEMPLATE_PATH.$this->template;
		$dataTemplate=$this->getFile($fileTemplate);

		#trae vista del modulo
		$fileVista = 'app/Views/'.$this->modulo."View.html";
		$dataVista=$this->getFile($fileVista);

		if($dataTemplate=='') $dataTemplate=$dataVista;

		$fileCSS = MODULE_PATH.'css/'.$this->modulo.".css";
		if(file_exists($fileCSS)){
			$this->AgregarCSS($fileCSS);
		}
		$ControllerJS = MODULE_PATH.'ControllersJS/'.$this->modulo."Controller.js";
		if(file_exists($ControllerJS)){
			$this->AgregarJS($ControllerJS);
		}
		
		$dataTemplate=str_replace("[[content]]", $dataVista, $dataTemplate);
		$dataTemplate=str_replace("</head>", implode("\n", $this->arrayHead)."</head>", $dataTemplate);
		$dataTemplate=str_replace("</body>", implode("\n", $this->arrayFooter)."</body>", $dataTemplate);
		$config=$_SESSION['config'];
		$config['general']['usuario']=$_SESSION['usuario']??'';
		$config['general']['nombreusu']=$_SESSION['nombreusu']??'';
		foreach ($config['general'] as $key => $value) {
			$dataTemplate=str_replace("[[".strtoupper($key)."]]", $value, $dataTemplate);
		}
		foreach ($config['apariencia'] as $key => $value) {
			$dataTemplate=str_replace("[[".strtoupper($key)."]]", $value, $dataTemplate);
		}

		//carga del menu de permisos
		$htmlMenu=$Programa->crearMenuLateral();
		$dataTemplate=str_replace("[[MENU_LATERAL]]", $htmlMenu, $dataTemplate);
		echo $dataTemplate;
	}  
}
