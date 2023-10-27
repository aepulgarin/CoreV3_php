<?php
namespace Core;
use Doctrine\SqlFormatter\SqlFormatter;
abstract class mainModel
{
	private $DB;
	private $conexion=array(
		"motor"=>"",
		"host"=>"",
		"db"=>"",
		"usuario"=>"",
		"password"=>"",
		"puerto"=>"",
		"server"=>"",
		"status"=>"off",
		"default"=>false
	);
	private $exceptionMode ="throw";
    public string $table;
    private string $primary;
    private string $columnsCreate;
    private string $columnsValues;
    private string $columnsUpdate;
    private array $columnsName;
    private string $columnsSelect;

    function __construct($conexion_=""){
        $this->Conectarse($conexion_);
	}
	function Conectarse($conexion_=''){
		$this->conexion=(object) $this->conexion;
		#Conexion personalizada desde archivo de configuracion .ini

		if($conexion_!=''){


			$file=CORE."/config/".trim($conexion_).".ini";
			if(file_exists($file)){
				$config=parse_ini_file($file,true);
				$config=$config['database'];
				$this->conexion->motor=$config['motor'];
				$this->conexion->host=$config['servidor'];
				$this->conexion->db=$config['base'];
				$this->conexion->usuario=$config['usuario'];
				$this->conexion->password=$config['clave'];
				$this->conexion->puerto=$config['puerto'];
				$this->conexion->server=$config['server'];
			}

		#Conexion por defecto segun .ini que se determina por el dominio	
		}else if($this->conexion->host==''){
			$this->conexion->default=true;
			$config=$_SESSION['config']['database']??[];
			$this->conexion->motor=$config['motor']??'';
			$this->conexion->host=$config['servidor']??'';
			$this->conexion->db=$config['base']??'';
			$this->conexion->usuario=$config['usuario']??'';
			$this->conexion->password=$config['clave']??'';
			$this->conexion->puerto=$config['puerto']??'';
			$this->conexion->server=$config['server']??'';
			
			if(count($config)==0){
				$_SESSION['config']=null;
			}
		}

		if(!isset($GLOBALS['DB']) || $this->conexion->default==false){

			#realiza conexion a la base de datos correspondiente
			try{
				switch ($this->conexion->motor) {
					case 'mysql':
					$dbHandle = new \PDO("mysql:host={$this->conexion->host}; dbname={$this->conexion->db}", $this->conexion->usuario, $this->conexion->password);
					$dbHandle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
					$dbHandle->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
					//$dbHandle->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
					break;
					case 'mssql':
					$dbHandle = new \PDO("dblib:host={$this->conexion->host}:{$this->conexion->puerto}; dbname={$this->conexion->db}", $this->conexion->usuario, $this->conexion->password);
					$dbHandle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
					$dbHandle->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
					break;
					case 'informix':
					$dbHandle = new \PDO("informix:host={$this->conexion->host};service={$this->conexion->puerto};database={$this->conexion->db};server={$this->conexion->server};protocol=onsoctcp;EnableScrollableCursors=1",$this->conexion->usuario,$this->conexion->password);
					$dbHandle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
					$dbHandle->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
					break;

					case 'postgresql':
                        $dbHandle = new \PDO("pgsql:host={$this->conexion->host};port={$this->conexion->puerto};dbname={$this->conexion->db}",$this->conexion->usuario,$this->conexion->password);
                        $dbHandle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                        $dbHandle->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
                        break;

					default:
					die("Motor -{$this->conexion->motor}- no soportado aun");
					break;
				}
				$this->DB= $dbHandle;
				$this->conexion->status='on';
			}catch( PDOException $exception ){
				$this->error_PDO($exception,print_r($this->conexion,true));
			}
			if($this->conexion->default){
				$GLOBALS['DB']=$this->DB;
			}
		}else{
			if($this->conexion->default){
				$this->DB=$GLOBALS['DB'];
			}
		}
        $this->configureModel();
	}
    function lee_prepare($query,$params){
        $rows=[];
        $modulo_=$_REQUEST['modulo']??'';
        $metodo_=$_REQUEST['metodo']??'';
        $query = "-- ".trim($_SESSION['usuario']??'')." ".($_SERVER['SCRIPT_NAME']??'')." modulo:$modulo_ metodo:$metodo_ "."
		".$query;
        try{
            $statement = $this->DB->prepare($query);
            $statement->execute($params);
        }catch( PDOException $exception ){
            error_PDO($exception,$query);
        }

        $colcount = $statement->columnCount();
        $encontrado = "no";
        $arr_bool = Array(); $arr_fechas = Array(); $arr_lchar=Array();
        # FIX fechas o booleanos, compatibilidad APPS viejas
        if(in_array($this->conexion->motor, array("mysql","informix"))){
            for ($i=1; $i <= $colcount; $i++) {
                $meta = $statement->getColumnMeta(($i-1));
                if($meta['native_type'] == "DATE"){
                    $encontrado = "si";
                    $arr_fechas[] = $meta['name'];
                } else if($meta['native_type'] == "BOOLEAN"){
                    $encontrado = "si";
                    $arr_bool[] = $meta['name'];
                }
                if($meta['name']==''){
                    $encontrado = "si";
                    $campo_vacio = "si";
                }
                if($meta['native_type'] == "CHAR" || $meta['native_type'] == "VARCHAR"){
                    $encontrado = "si";
                    $arr_lchar[] = $meta['name'];
                }
            }
        }
        try{
            $rows = $statement->fetchAll(\PDO::FETCH_CLASS);
        }catch( \PDOException $exception ){
            $this->error_PDO($exception,$query);
        }
        # Si encuentra campos del FIX los recorre para realizar la correccion
        if($encontrado == "si"){
            for ($i=0; $i < count($rows); $i++) {
                // para corregir las fechas - siempre retorne
                if(count($arr_fechas) > 0){
                    $count_fechas=count($arr_fechas);
                    for ($j=0; $j < $count_fechas; $j++) {
                        $registros='';
                        @preg_match ("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $rows[$i]->{$arr_fechas[$j]}, $registros);
                        if(count($registros)>0){//fix para campos tipo fecha nulos
                            $rows[$i]->{$arr_fechas[$j]} = $registros[2]."/".$registros[3]."/".$registros[1];
                        }
                    }
                }
                // para corregir los booleanos, unifica f/t
                if(count($arr_bool) > 0){
                    $count_arr_bool=count($arr_bool);
                    for ($j=0; $j < $count_arr_bool; $j++) {
                        $rows[$i]->$arr_bool[$j] = $rows[$i]->$arr_bool[$j]==0?"f":"t";
                    }
                }
                // para corregir los lvarchar -  sin espacios
                if(count($arr_lchar) > 0){
                    $count_arr_lchar=count($arr_lchar);
                    for ($j=0; $j < $count_arr_lchar; $j++) {
                        $rows[$i]->{$arr_lchar[$j]}=(trim($rows[$i]->{$arr_lchar[$j]}));
                    }
                }
            }
        }
        return $rows;
    }
	function ejecuta_prepare($queri,$params,$retorna='count'){
        $valor=0;
        $queri = "-- ".trim($_SESSION['usuario']??'')." ".($_SERVER['SCRIPT_NAME']??'')."
		".$queri;
        try{
            $conex = $this->DB->prepare($queri);
            $conex->execute($params);
            $cant=$conex->rowCount();
            if($retorna=='count'){
                $valor= $cant;
            }else {
                $valor = $this->DB->lastInsertId();
            }
        }catch( PDOException $exception ){
            $this->error_PDO($exception,$queri, print_r($params,true));
        }
        return $valor;
    }
	function begin_work(){
		$this->DB->beginTransaction();
	}
	function commit(){
		$this->DB->commit();
	}
	function rollback(){
		$this->DB->rollback();
	}
	function ejecuta_sp($queri){

		if (isset($_REQUEST['modulo'])){
			$modulo_=$_REQUEST['modulo'];
		}else{
			$modulo_='';
		}

		if (isset($_REQUEST['metodo'])){
			$metodo_=$_REQUEST['metodo'];
		}else{
			$metodo_='';
		}

		$queri = "-- ".trim($_SESSION['usuario']).$_SESSION['datos_adicionales']."=>".trim($_SESSION['nombreusu'])." (".$_SERVER['REMOTE_ADDR'].") [".$_SERVER['SCRIPT_NAME']." modulo:$modulo_ metodo:$metodo_] ".date("h:i:s a")."
		".$queri;
		try{
			$statement = $this->DB->query($queri);
		}catch( PDOException $exception ){
			$this->error_PDO($exception,$queri);

		}
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		for ($i=0; $i <count($rows) ; $i++) { 
			$result[]=trim($rows[$i]['']);
		} 
		if(count($rows)==1) $result=$result[0];
		return $result;
	}

	function lee_uno($query,$params){
		$mat=$this->lee_prepare($query,$params);
		return $mat['0']??(object)[];
	}

	function isolation($tipo){
		switch ($this->conexion->motor) {
			case 'informix':
				switch (strtolower($tipo)) {
					case 'dirty':
					    $sql_add="DIRTY READ";
					    break;
					case 'committed':
					    $sql_add="COMMITTED READ";
					    break;
                    default:
                        $sql_add='';
                        break;
				}
				$this->ejecuta_query("SET ISOLATION TO $sql_add");
				break;
			default:
				# code...
				break;
		}
		
	}
	function wait(){
		switch ($this->conexion->motor) {
			case 'informix':
				$this->ejecuta_query("SET LOCK MODE TO WAIT");
				break;
			default:
				# code...
				break;
		}
	}

	/*Funcion para cargar los datos de un array  a atributos del modelo*/
	function atributos($datos,$destino){
		foreach ((array)$datos as $key => $value) {
			$this->$destino->$key=trim($value);
		}
	}
    function error_PDO($exception,$query, $params=''){
        $trace=$exception->getTrace();
        for ($a=0;$a < count($trace);$a++){
            if($trace[$a]['function']=='lee_todo' || $trace[$a]['function']=='ejecuta_query' || $trace[$a]['function']=='ejecuta_prepare'){
                $error=explode("]",$exception->getMessage());
                $texto_error =(new SqlFormatter())->format(($error[1]??'').($trace[$a]['args'][0]??print_r($error,true)));

                if(($error[1]??'')==''){
                    $texto_error .= (new SqlFormatter())->format($query);
                }
                $texto_error .= $params;

                $respuesta= "<div style='border: 1px solid #AFAFAF; padding: 3px; margin: 3px; background-color: #F4F3F3;'>
                                <span style='color: #3B3838; font-size: 22px'>
                                    <b>Ruta:</b>".$trace[$a]['file']."<b>:".$trace[$a]['line']."</b><br>
                                    SESSION_ID: <b>".$_SESSION['id_conexion']."</b><br>
                                    <b>".$trace[$a]['function']."():</b>
                                </span>".$texto_error."
                            </div>";

                $resu=guardar_log_errores('holding',($trace[$a]['file']??''),$respuesta);
                echo $respuesta.' -- '.$resu;
                die ;
            }
        }
        // casos excepcionales que no tengan trace
        echo "<pre>";
        print_r($query);
        header(':', true, '202'); //forza a que el error no salga como respuesta exitosa web
        die();
    }

    private function getColumns(){
        switch($this->conexion->motor){
            case 'mysql':
                $sql="SELECT column_name, DATA_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH, COLUMN_KEY  
                        from information_schema.columns
                         where table_schema = ? and TABLE_NAME =?
                        order by ordinal_position";
                break;
            default:
                die("Database {$this->conexion->motor} no implemented yet");
                break;
        }
        return $this->lee_prepare($sql,[
            $this->conexion->db,
            $this->table
        ]);
    }
    private function configureModel(){
        if($this->table!=''){
            $columns = $this->getColumns();
            $this->prepareCreateStatement($columns);
            $this->prepareReadStatement($columns);
            $this->prepareUpdateStatement($columns);
        }
    }
    private function prepareCreateStatement($columns):void{
        $this->columnsName = [];
        $this->columnsCreate='';
        $this->columnsValues='';
        foreach ($columns as $column){
            $this->columnsName[] = $column->column_name;
            if($column->column_key!='PRI') {
                $this->columnsCreate .=", ".$column->column_name;
                $this->columnsValues .=", ?";
            }
        }
        $this->columnsCreate= substr($this->columnsCreate,1);
        $this->columnsValues= substr($this->columnsValues,1);
    }
    private function prepareReadStatement($columns):void{
        $this->columnsSelect='';
        foreach ($columns as $column){
            $this->columnsSelect .=", ".$column->column_name;
            if($column->column_key=='PRI'){
                $this->primary = $column->column_name;
            }
        }
        $this->columnsSelect= substr($this->columnsSelect,1);
    }
    private function prepareUpdateStatement($columns):void{
        $this->columnsUpdate='';
        foreach ($columns as $column){
            if($column->column_key!='PRI') {
                $this->columnsUpdate .= ", " . $column->column_name.'=?';
            }
        }
        $this->columnsUpdate= substr($this->columnsUpdate,1);
    }

    public function createOne(array $data):int{
        return $this->ejecuta_prepare(
            "INSERT INTO {$this->table} ({$this->columnsCreate})
                    VALUES ({$this->columnsValues})",$data,'id');
    }

    public function readOneByColumn(string $column, string $value):object{
        return $this->lee_uno("SELECT * from {$this->table} where $column=?",[
            $value
        ]);
    }
    public function readOneById(int $id):object{
        return $this->lee_uno("SELECT * from {$this->table} where {$this->primary}=?",[
            $id
        ]);
    }
    public function readManyByColumn(object $dat, string $order):array{
        $columnsWhere='';
        $values=[];
        foreach($dat as $key => $dat){
            $values[]=$dat;
            $columnsWhere .=', '.$key.'=?';
        }
        $columnsWhere=substr($columnsWhere,1);

        return $this->lee_prepare("SELECT * from {$this->table} where $columnsWhere",
            $values);
    }
    public function updateOneById(array $data, int $id):int{
        $data[]=$id;
        return $this->ejecuta_prepare(
            "UPDATE {$this->table} SET {$this->columnsUpdate}
                    where {$this->primary}=?",$data);
    }
    public function deleteOneById(int $id):int{
        return $this->ejecuta_prepare("DELETE from {$this->table} where {$this->primary}=?",[
            $id
        ]);
    }
    public function deleteManyByColumn(object $dat, string $order):array{
        $columnsWhere='';
        $values=[];
        foreach($dat as $key => $dat){
            $values[]=$dat;
            $columnsWhere .=', '.$key.'=?';
        }
        $columnsWhere=substr($columnsWhere,1);

        return $this->ejecuta_prepare("DELETE from {$this->table} where $columnsWhere",
            $values);
    }
}

