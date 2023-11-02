(function (document, window, $, Core) {
	(function () {
		return MenuController = {
			//INICIALIZACIÓN DE COMPONENTES
			Initialize: function () {
				let self = this;
				$("#grabar-menu").click(function (event) {
					self.grabarMenu();
				});

				self.listarMenus();
				self.traerMenusSelect();

			},
			traerMenusSelect: function () {
				ajaxRequest({}, 'post', 'traerMenus', 'Menu').done(function (response) {
					if (response.success === false) {
						Core.ErrorSistema(response.mensaje);
						return;
					}
					$.each(response.data, function (index, val) {
						if(val.id_sub===0) {
							$("#frm-menu select[name=id_sub]")
								.append($("<option></option>")
									.attr("value", val.id)
									.text(val.des_mod));
						}

					});

				});
			},
			listarMenus: function () {
				ajaxRequest({}, 'post', 'traerMenus', 'Menu').done(function (response) {
					if (response.success === false) {
						Core.ErrorSistema(response.mensaje);
						return;
					}

					$("#tabla-menus").dataTable().fnDestroy();
					$("#tabla-menus").DataTable({
						"aaData": response.data,
						"fnCreatedRow": function (nRow, aData, iDataIndex) {
							$(nRow).attr('id', 'TR' + aData.id);
							$(nRow).data('info', aData);
						},
						"aoColumns": [
							{ "mDataProp": "id" },
							{ "mDataProp": "des_mod" },
							{ "mDataProp": "id_sub" },
							{ "mDataProp": "orden" },
							{
								"mDataProp": "id", "render": function (data, type, row) {
									return '<a href="javascript:void(0)" class="btn" title="Borrar opcion programa" onclick=MenuController.borrarMenu("' + row.id + '")>' +
										'<i class="fas fa-lg fa-fw m-r-10 fa-trash-alt"></i>' +
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
			grabarMenu: function () {
				let self = this;
				let frm = Core.FormToJSON('#frm-menu')
				if (/*form.valid()*/1 == 1) {
					ajaxRequest(frm, 'post', 'grabarMenu', 'Menu').done(function (response) {
						if (response.success === true) {
							notyf.success("Menu grabado. "+response.data);
							$("#frm-menu")[0].reset();
							self.listarMenus();
						} else {
							notyf.error(response.mensaje);
						}
					});

				} else {
					notyf.warning("El formulario aun contiene errores, verifique");
				}
				return;
			},
			borrarMenu: function (id) {
				let self = this;
				if(confirm("Esta seguro de eliminar este registro?")) {
					ajaxRequest({'id': id}, 'post', 'borrarMenu', 'Menu').done(function (response) {
						if (response.success === false) {
							Core.ErrorSistema(response.mensaje);
							return;
						}
						notyf.success("Menu eliminado. " + response.data);
						self.listarMenus();


					});
				}
			}
		}
	})()

	if (Core.GetUrlParameter('modulo') == 'Programa') MenuController.Initialize()
})(document, window, jQuery, Core)