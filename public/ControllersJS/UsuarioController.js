(function (document, window, $, Core) {
	(function () {
		return UsuarioController = {
			//INICIALIZACIÓN DE COMPONENTES
			Initialize: function () {
				let self = this;

				$("#limpiar-formulario").click(function (event) {
					$("#usuario").attr('readonly', false);
					$("#frm")[0].reset();
					$("#usuario").focus();
					$('#frm .help-block').remove();
					$('#frm .form-group').removeClass("has-error");
				});

				$("#grabar-usuario").click(function (event) {
					self.grabarUsuario();
				});

				$("#usuario").change(function (event) {
					self.buscarUsuario($(this).val());
				});

				$("#listar-usuarios").click(function (event) {
					self.listarUsuarios($(this).val());
				});

				$("#listar-usuarios").click();

				self.selectRoles();

				$("#tabla-listado").DataTable({
					responsive: true
				});
			},

			buscarUsuario: function (usuario) {
				ajaxRequest({ "usuario": usuario }, 'post', 'traerUsuario', 'Usuario').done(function (response) {
					if (response.success == false) {
						Core.ErrorSistema(response.mensaje);
						return;
					}
					data = response.data;
					if (data.existe == 'S') {
						$("#usuario").attr('readonly', 'readonly');
						$("#password1").removeProp("required").removeProp("data-rule-minlength");
						$("#password2").removeProp("required").removeProp("data-rule-minlength");
					}
					$("#nombre").val(data.nombre).change();
					$("#apellidos").val(data.apellidos).change();
					$("#correo").val(data.correo).change();
					var estado;
					if (data.estado == 'A') estado = true; else estado = false;
					$("#estado").prop('checked', estado);
				});
			},

			grabarUsuario: function () {
				let frm = Core.FormToJSON('#frm')
				if (/*form.valid()*/1 == 1) {
					if ($("#password1").val() != $("#password2").val()) {
						notyf.error("Contrase&ntilde;as no concuerda, verifique");
						return;
					}

					ajaxRequest(frm, 'post', 'grabarUsuario', 'Usuario').done(function (response) {
						if (response.success === true) {
							notyf.success("Usuario grabado");
							$("#limpiar-formulario").click();
							$("#listar-usuarios").click();
						} else {
							notyf.error(response.mensaje);
						}
					});

				} else {
					notyf.warning("El formulario aun contiene errores, verifique");
				}
				return;
			},

			listarUsuarios: function (estado) {
				ajaxRequest({ "estado": estado }, 'post', 'traerLista', 'Usuario').done(function (response) {
					if (response.success === false) {
						Core.ErrorSistema(response.mensaje);
						return;
					}
					$("#tabla-listado").dataTable().fnDestroy();
					$("#tabla-listado").DataTable({
						"aaData": response.data,
						"fnCreatedRow": function (nRow, aData, iDataIndex) {
							$(nRow).attr('id', 'TR' + aData.id_usuario);
							$(nRow).data('info', aData);
						},
						"aoColumns": [
							{ "mDataProp": "usuario" },
							{ "mDataProp": "nombre" },
							{ "mDataProp": "apellidos" },
							{ "mDataProp": "correo" },
							{
								"mDataProp": "usuario", "render": function (data, type, row) {
									return '<a href="javascript:void(0)" class="btn" title="Editar usuario" onclick=UsuarioController.mostrarUsuario("' + row.usuario + '")>' +
										'<i class="fas fa-lg fa-fw m-r-10 fa-pencil-alt"></i>' +
										'</a>';
								}
							},
						],
						"language": {
							"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
						},
						responsive: true,
					});

				});
			},

			mostrarUsuario: function (usuario) {
				$("#usuario").val(usuario).change();
				$('.nav-tabs a[href="#tab-1"]').tab('show');
			},
			selectRoles: function (estado) {
				ajaxRequest({"estado": estado}, 'post', 'traerRoles', 'Rol').done(function (response) {
					$("#tab-1 select[name=roles]").html("<option value=''>Seleccione</option>");
					$.each(response.data, function (index, val) {
						$("#tab-1 select[name=roles]")
							.append($("<option></option>")
								.attr("value", val.id)
								.text(val.nombre));
					});
					const choices = new Choices('.js-choice');
				});
			},
		}
	})()

	if (Core.GetUrlParameter('modulo') == 'Usuario') UsuarioController.Initialize()
})(document, window, jQuery, Core)