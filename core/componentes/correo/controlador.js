var objCorreo = function () {var o = this;$(document).ready(function () {o.initialize();});}
var Correo = objCorreo.prototype;

Correo.initialize=function (){
this.EnviarCorreo(correo, asunto, contenido);
}
Correo.EnviarCorreo= function(correo, asunto, contenido){
	var resultado="";
$.ajax({type:"POST",url: "rest.php",dataType: 'json',async: false,data:{"tipo":"CORE","modulo":"correo","metodo":"enviarCorreo","token":getToken(),"parametros":{"correo":correo, "asunto":asunto, "contenido":contenido}},success: function (data){resultado=data;}});
	return resultado;
}

