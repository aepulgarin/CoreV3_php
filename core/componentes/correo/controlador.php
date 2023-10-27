<?php
include_once("class.phpmailer.php");
include_once("class.smtp.php");

class cCorreo extends PHPMailer{

	function __construct(){
		$this->IsSMTP();
        $this->IsHTML(true);
        $this->SMTPAuth = false;
        $this->Timeout = "130";
        
        $this->SetFrom('procesoslebon@lebon.com.co', "Procesos LeBon");
        $this->AddReplyTo("procesoslebon@lebon.com.co","soporte@lebon.com.co");
        $this->Host   = "mail.inscra.local";    // SMTP server
        $this->Port       = 25;
        $this->Username = "procesoslebon"; // SMTP username
        $this->Password = "IDSInfo1"; // SMTP password
	}
	/*
		Correo=$this->cargarComponente("correo");
		$Correo->AddAddress($correo);
		$Correo->MsgHTML($mensaje);
		$Correo->Subject = $asunto;
		$Correo->AltBody = $asunto;
		$Correo->Send();
	*/

	function enviarCorreo($parametros){
		$this->AddAddress($parametros['correo']);
        $this->MsgHTML($parametros['contenido']);
        $this->Subject=$parametros['asunto'];
        $this->AltBody=$parametros['asunto'];
        $this->Send();
    }

    function sanitize($parametros){
    	return $parametros;
    }
}	
