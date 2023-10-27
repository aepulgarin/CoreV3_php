<?php
use App\Business\LoginBusiness;
$plantilla="clean.html";
class LoginController extends \Core\mainController{
    private $Business;
    private $Usuario;
    public function __construct () {
        $this->Business = new LoginBusiness();
    }
    
    function autenticar($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->autenticar((object)$parametros);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function logOut(){
    	$this->Business->logOut();
        $this->response('');
    }
}