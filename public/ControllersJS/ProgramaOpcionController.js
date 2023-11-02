(function (document, window, $, Core) {
    (function () {
        return ProgramaOpcionController = {
            //INICIALIZACIÓN DE COMPONENTES
            Initialize: function () {
                let self = this;
                $("#grabar-opcion").click(function (event) {
                    self.grabarOpcionPrograma();
                });

            },
            listarOpcionesPrograma: function (id_programa) {
                $("#frm-opciones input[name=id-programa]").val(id_programa);
                ajaxRequest({'id_programa':id_programa}, 'post', 'traerOpciones', 'ProgramaOpcion').done(function (response) {
                    if (response.success === false) {
                        Core.ErrorSistema(response.mensaje);
                        return;
                    }
                    $("#tabla-opciones").dataTable().fnDestroy();
                    $("#tabla-opciones").DataTable({
                        "aaData": response.data,
                        "fnCreatedRow": function (nRow, aData, iDataIndex) {
                            $(nRow).attr('id', 'TR' + aData.id);
                            $(nRow).data('info', aData);
                        },
                        "aoColumns": [
                            { "mDataProp": "id" },
                            { "mDataProp": "id_programa" },
                            { "mDataProp": "opcion" },
                            { "mDataProp": "descripcion" },
                            {
                                "mDataProp": "id", "render": function (data, type, row) {
                                    return '<a href="javascript:void(0)" class="btn" title="Borrar opcion programa" onclick=ProgramaOpcionController.borrarOpcionPrograma("' + row.id + '")>' +
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
            grabarOpcionPrograma: function () {
                let self = this;
                let frm = Core.FormToJSON('#frm-opciones')
                if (/*form.valid()*/1 == 1) {
                    ajaxRequest(frm, 'post', 'grabarOpcionPrograma', 'ProgramaOpcion').done(function (response) {
                        if (response.success === true) {
                            notyf.success("Opcion de programa grabado. "+response.data);
                            $("#frm-opciones input[name=opcion]").val('');
                            $("#frm-opciones input[name=descripcion]").val('');
                            self.listarOpcionesPrograma($("#frm-opciones input[name=id-programa]").val());
                        } else {
                            notyf.error(response.mensaje);
                        }
                    });

                } else {
                    notyf.warning("El formulario aun contiene errores, verifique");
                }
                return;
            },
            borrarOpcionPrograma: function (id) {
                let self = this;
                if(confirm("Esta seguro de eliminar este registro?")) {
                    ajaxRequest({'id': id}, 'post', 'borrarOpcionPrograma', 'ProgramaOpcion').done(function (response) {
                        if (response.success === false) {
                            Core.ErrorSistema(response.mensaje);
                            return;
                        }
                        notyf.success("Opcion eliminada. " + response.data);
                        self.listarOpcionesPrograma($("#frm-opciones input[name=id-programa]").val());


                    });
                }
            }
        }
    })()

    if (Core.GetUrlParameter('modulo') == 'Programa') ProgramaOpcionController.Initialize()
})(document, window, jQuery, Core)