var objBase = function () {var o = this;$(document).ready(function () {o.initialize();});}
var Base = objBase.prototype;

Base.initialize=function (){
this.TraerCampo(retorna);
this.TraerSiglas(tabla,campo,transaccion);
}
Base.TraerIden= function(retorna){
	var resultado="";
$.ajax({type:"POST",url: "rest.php",dataType: 'json',async: false,data:{"tipo":"CORE","modulo":"base","metodo":"traerIden","token":getToken(),"parametros":{"retorna":retorna}},success: function (data){resultado=data;}});
	return resultado;
}
Base.TraerSiglas= function(tabla,campo,transaccion){
	var resultado="";
$.ajax({type:"POST",url: "rest.php",dataType: 'json',async: false,data:{"tipo":"CORE","modulo":"base","metodo":"traerSiglas","token":getToken(),"parametros":{"tabla":tabla,"campo":campo,"transaccion":transaccion}},success: function (data){resultado=data;}});
	return resultado;
}
