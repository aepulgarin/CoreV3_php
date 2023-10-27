<?php
namespace Core;
class mainController {
	public $debug='N';
	public $tiempo_debug;
	private $ruta_componente;
	public $medio="servicio";
	public $response;

	public function cargarComponente($componente){
		$this->ruta_componente=CORE."/componentes/$componente/";
		$controlador=$this->ruta_componente."controlador.php";
		$modelo=$this->ruta_componente."modelo.php";
		if(file_exists($controlador)){
			include_once("$controlador"); 
		}else{
			die("componente $componente no encontrado");
		}
		if(file_exists($modelo)){
			include_once("$modelo");
		}
		$clase="c".ucfirst(strtolower($componente));
		$obj= new $clase('local');
		return $obj;
	}

	public function cargarModulo($modulo){
		$modulo=strtolower($modulo);
		$mPrograma= new programa($modulo);
		$menu=$mPrograma->menu;
		$controlador=ROOT_PATH."/modulos/$menu/".$modulo."/controlador.php";
		$negocio=ROOT_PATH."/modulos/$menu/".$modulo."/negocio.php";
		$modelo=ROOT_PATH."/modulos/$menu/".$modulo."/modelo.php";

		if(file_exists($controlador)){
			include_once("$controlador"); 
		}else{
			if(!class_exists($modulo)){
				die("componente $modulo no encontrado.. $controlador $modelo");
			}
		}
		if(file_exists($negocio)){
			include_once("$negocio");
		}
		if(file_exists($modelo)){
			include_once("$modelo");
		}
		$clase=strtolower($modulo)."Controller";
		$obj= new $clase('local');
		return $obj;
	}

	public function cargarModelo($modulo){
		$modulo=strtolower($modulo);
		$mPrograma= new programa($modulo);
		$menu=$mPrograma->menu;
		$modelo=ROOT_PATH."/modulos/$menu/".$modulo."/modelo.php";

		if(file_exists($modelo)){
			include_once("$modelo");
		}else{
			die("componente $modulo no encontrado.. $modelo");
		}
	}

	public function response($resultado){
		if($this->debug=='S'){
			$this->file_log("************\n".date("Y-m-d H:i"));
			$this->file_log("Request:\n".$this->prettyPrint(json_encode($_REQUEST)));
			$this->file_log("Response:\n".$this->prettyPrint(json_encode($resultado)));
			$this->file_log("Tiempo:".number_format($this->diff_microtime2($this->tiempo_debug,microtime(true)),4)) ;
		}
		if($this->medio=='local'){
			$this->response= $resultado;
		}
		if($this->medio=='servicio'){
            echo json_encode($resultado);
		}
	}
	public function filter($variable, $tipo){
		switch ($tipo) {
			case 'email':
				$variable=filter_var($variable,FILTER_SANITIZE_EMAIL);
				break;
			case 'int':
				$variable=filter_var($variable,FILTER_SANITIZE_NUMBER_INT);
				break;
			case 'float':
				$variable=filter_var($variable,FILTER_SANITIZE_NUMBER_FLOAT);
				break;
			case 'string':
				$variable=utf8_decode(filter_var($variable, FILTER_SANITIZE_STRING));
				break;
			case 'sql':
				$variable  = preg_replace(array("/(select )/i","/(delete )/i","/(insert )/i","/(truncate )/i","/(drop )/i","/(union )/i","/(create )/i"),  '', $variable);	
				break;
			case 'url':
				if(filter_var($variable, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)==true)
					$variable=filter_var($variable, FILTER_VALIDATE_URL);

				break;
			case 'raw':
				$variable=filter_var($variable, FILTER_UNSAFE_RAW);
				break;
			default:
				die("Sanitize:Tipo=$tipo No existe");
				break;

		}
		return $variable;
	}

	public function sanitize($parametros){
		if(isset($_POST['sanitize'])){
			$sanitize=$_POST['sanitize'];
			foreach ($sanitize as $key => $tipo) {
				if(!is_array($tipo)){
					if(isset($parametros[$key])) $parametros[$key]=$this->filter($parametros[$key],$tipo);
				}
			}
		}else{
			$sanitize=array();
		}
		if(is_array($parametros)){
			$campo_sin_sanitizar=array_diff(array_keys($parametros), array_keys($sanitize));
			foreach ($campo_sin_sanitizar as $key => $campo){
				if(!is_array($parametros[$campo])){
					$parametros[$campo]=$this->filter($parametros[$campo],'string');
					$parametros[$campo]=$this->filter($parametros[$campo],'sql');
				}else{
					$parametros[$campo]=$this->sanitize($parametros[$campo]);
				}
			}
		}
		return $parametros;
	}

	public function getFile ($file){
		$link = @fopen($file,'r');
		if ($link){
			$size=filesize($file);
			if($size==0) $size=1;
			$data = fread($link,$size);
			fclose($link);
            return $data;
		}
	}

	public function core_log_programa($modulo,$parametros){
		$clase="c".strtolower($modulo);
		$obj= new $clase('local');
		$obj->core_log_programa($modulo, $parametros);
	}

	public function file_log($nota){
        $usuario=$_SESSION['usuario'];
        $programa=$_REQUEST['modulo'];
        $archivoplano = fopen("/tmp/".$programa."_".$usuario.".log", "a+");
        fwrite($archivoplano,$nota."\n");
        fclose($archivoplano);
	}

	public function diff_microtime2($mt_old,$mt_new){
	    list($old_usec, $old_sec) = explode(' ',$mt_old);
	    list($new_usec, $new_sec) = explode(' ',$mt_new);
	    $old_mt = ((float)$old_usec + (float)$old_sec);
	    $new_mt = ((float)$new_usec + (float)$new_sec);
	    return $new_mt - $old_mt;
	}
	public function prettyPrint( $json ){
	    $result = '';
	    $level = 0;
	    $in_quotes = false;
	    $in_escape = false;
	    $ends_line_level = NULL;
	    $json_length = strlen( $json );

	    for( $i = 0; $i < $json_length; $i++ ) {
	        $char = $json[$i];
	        $new_line_level = NULL;
	        $post = "";
	        if( $ends_line_level !== NULL ) {
	            $new_line_level = $ends_line_level;
	            $ends_line_level = NULL;
	        }
	        if ( $in_escape ) {
	            $in_escape = false;
	        } else if( $char === '"' ) {
	            $in_quotes = !$in_quotes;
	        } else if( ! $in_quotes ) {
	            switch( $char ) {
	                case '}': case ']':
	                    $level--;
	                    $ends_line_level = NULL;
	                    $new_line_level = $level;
	                    break;

	                case '{': case '[':
	                    $level++;
	                case ',':
	                    $ends_line_level = $level;
	                    break;

	                case ':':
	                    $post = " ";
	                    break;

	                case " ": case "\t": case "\n": case "\r":
	                    $char = "";
	                    $ends_line_level = $new_line_level;
	                    $new_line_level = NULL;
	                    break;
	            }
	        } else if ( $char === '\\' ) {
	            $in_escape = true;
	        }
	        if( $new_line_level !== NULL ) {
	            $result .= "\n".str_repeat( "\t", $new_line_level );
	        }
	        $result .= $char.$post;
	    }

	    return $result;
	}
	public function catchError(ErrorException $e){
		$respuesta= array(
			"codigo"=>$e->getCode(),
			"mensaje"=>$e->getMessage(),
			"archivo"=>$e->getFile(),
			"linea"=>$e->getLine()
		);
		http_response_code(403);
		$this->response($respuesta);
	}
    public function defaultMethod($data):void{
        $result= [];
        try {
            $result['data'] = $data;
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
}
