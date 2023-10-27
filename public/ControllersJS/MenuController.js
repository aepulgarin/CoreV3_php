$(document).ready(function() {

	$('.selectpicker').selectpicker();
	$('.select2').select2();
	$("#nombre-modulo").change(function(event) {
		buscarModulo($(this).val());
	});

	$("#btn-grabar-modulo").click(function(event) {
		grabarModulo();
	});

	traerMenus();

});

function buscarModulo(modulo){

	ajaxRequest({"modulo":modulo},'post','buscarModulo','menu').done(function (response){

		console.log(response);

		if(response.existe=='S'){
			$("#nombre-modulo").attr('readonly', 'readonly');
		}
		$("#nombre-modulo").val(response.modulo);
		$("#menu-modulo").val(response.id_sub).change();
		$("#icono-modulo").val(response.icono).change();
		$("#orden-modulo").val(response.orden).change();

	});
}
function grabarModulo(){

	if($("#frm").valid()){
		ajaxRequest({"frm":Core.FormToJSON('#frm')},'post','grabarModulo','menu').done(function (response){

			if(response.mensaje=='exitoso'){
				toastr.success("Modulo grabado");
			}else{
				toastr.error(response.mensaje);
			}

		});
	}else{
		toastr.warning("El formulario aun contiene errores, verifique");
	}
}

function traerMenus(){
	$.ajax
	({
		type: "POST",
		url: "rest.php",
		dataType: 'json',
		async: true,
		data: { "modulo":"menu", 
		"metodo":"traerMenu",
		"token":getToken()
	},
	success: function (data){
		$("#menu-modulo").html('<option>/</option>');
		$("#menu-modulo").html('<option>/</option>');
		$.each(data, function(index, val) {
			$("#menu-modulo").append('<option>/'+val.nombre+'</option>');
			$.each(val.sub, function(index, val1) {
				$("#menu-modulo").append('<option>/'+val.nombre+'/'+val1.nombre+'</option>');
				$.each(val1.sub, function(index, val2) {
					$("#menu-modulo").append('<option>/'+val.nombre+'/'+val1.nombre+'/'+val2.nombre+'</option>');
					$.each(val2.sub, function(index, val3) {
						$("#menu-modulo").append('<option>/'+val.nombre+'/'+val1.nombre+'/'+val2.nombre+'/'+val3.nombre+'</option>');
					});
				});
			});
		});
		$("#menu-modulo").trigger("change");
	}
});
}
