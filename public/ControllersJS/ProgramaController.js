(function (document, window, $, Core) {
    (function () {
        return ProgramaController = {
            //INICIALIZACIÓN DE COMPONENTES
            Initialize: function () {
                let self = this;
                $("#limpiar-formulario").click(function (event) {
                    $("#programa").attr('readonly', false).focus();
                    $("#frm")[0].reset();
                });

                $("#grabar-programa").click(function (event) {
                    self.grabarPrograma();
                });

                self.traerMenus();
                self.listarProgramas();

                //carga controlador de programasOpciones
                $.getScript('public/ControllersJS/ProgramaOpcionController.js');
                $.getScript('public/ControllersJS/MenuController.js');

                $("#tabla-listado").DataTable({
                    responsive: true
                });
            },
            grabarPrograma: function () {
                let self = this;
                let frm = Core.FormToJSON('#frm')
                if (/*form.valid()*/1 == 1) {
                    if ($("#password1").val() != $("#password2").val()) {
                        notyf.error("Contrase&ntilde;as no concuerda, verifique");
                        return;
                    }

                    ajaxRequest(frm, 'post', 'grabarPrograma', 'Programa').done(function (response) {
                        if (response.success === true) {
                            notyf.success(response.data);
                            $("#limpiar-formulario").click();
                            self.listarProgramas();
                        } else {
                            notyf.error(response.mensaje);
                        }
                    });

                } else {
                    notyf.warning("El formulario aun contiene errores, verifique");
                }
                return;
            },
            listarProgramas: function () {
                ajaxRequest({}, 'post', 'traerLista', 'Programa').done(function (response) {
                    if (response.success === false) {
                        Core.ErrorSistema(response.mensaje);
                        return;
                    }
                    $("#tabla-listado").dataTable().fnDestroy();
                    $("#tabla-listado").DataTable({
                        "aaData": response.data,
                        "fnCreatedRow": function (nRow, aData, iDataIndex) {
                            $(nRow).attr('id', 'TR' + aData.id);
                            $(nRow).data('info', aData);
                        },
                        "aoColumns": [
                            { "mDataProp": "id" },
                            {
                                "mDataProp": "prog_icon", "render": function (data, type, row) {
                                    return " <i class='fa fa-" + row.prog_icon + "'></i> <small>" + row.prog_icon + "</small>";
                                }
                            },
                            { "mDataProp": "programa" },
                            { "mDataProp": "descripcion" },
                            { "mDataProp": "id_menu" },
                            { "mDataProp": "autenticado" },
                            { "mDataProp": "orden" },
                            {
                                "mDataProp": "id", "render": function (data, type, row) {
                                    return '<a href="javascript:void(0)" class="btn" title="Editar programa" onclick=ProgramaController.mostrarPrograma("' + row.id + '")>' +
                                        '<i class="fas fa-lg fa-fw m-r-10 fa-pencil-alt"></i>' +
                                        '</a>' +
                                        '<a href="javascript:void(0)" class="btn" title="Borrar programa" onclick=ProgramaController.borrarPrograma("' + row.id + '")>' +
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
            traerMenus: function () {
                ajaxRequest({}, 'post', 'traerMenus', 'Menu').done(function (response) {
                    if (response.success === false) {
                        Core.ErrorSistema(response.mensaje);
                        return;
                    }
                    $.each(response.data, function (index, val) {
                        $("#tab-programa #frm select[name=id_menu]")
                            .append($("<option></option>")
                                .attr("value", val.id)
                                .text(val.des_mod));
                    });

                });
            },
            mostrarPrograma: function (id_programa) {
                let data = $("#TR" + id_programa).data('info');
                $.each(data, function (index, val) {
                    switch (index) {
                        case 'autenticado':
                            let ch = (val == 'S');
                            $("#tab-programa #frm input[name=" + index + "]").prop('checked', ch);
                            break;
                        case 'id_menu':
                            $("#tab-programa #frm select[name=" + index + "]").val(val);
                            break;
                        default:
                            $("#tab-programa #frm input[name=" + index + "]").val(val);
                            break;
                    }

                });
                ProgramaOpcionController.listarOpcionesPrograma(id_programa);

                $('.nav-tabs a[href="#tab-2"]').tab('show');
            },
            borrarPrograma: function (id_programa) {
                let self = this;
                if(confirm("Esta seguro de eliminar este registro?")) {
                    ajaxRequest({'id': id_programa}, 'post', 'borrarPrograma', 'Programa').done(function (response) {
                        if (response.success === false) {
                            Core.ErrorSistema(response.mensaje);
                            return;
                        }
                        notyf.success(response.data);
                        self.listarProgramas();


                    });
                }
            }
        }
    })()

    if (Core.GetUrlParameter('modulo') == 'Programa') ProgramaController.Initialize()
})(document, window, jQuery, Core)