(function (document, window, $, Core) {
    (function () {

        return estimacion = {
            //VARIABLES GLOBAL
            Initialize: function () {
                var self = this;

                //EJECUTAR METODO
                self.traerProyectos('A');
                self.llenarClientes('A');
                $(".panel-detalle-proyecto").hide()

                //FORMA DE ASIGNAR UN CLICK AUN BOTON
                $("#nuevoProyecto").on("click", function (event) {
                    document.getElementById('frm-proyecto').reset();
                    $("#frm-proyecto .id-proyecto").val(0);
                    $(".panel-detalle-proyecto").show()
                });
                $("#actualizaProyecto").on("click", function (event) {
                    let frm =Core.FormToJSON("#frm-proyecto");
                    self.actualizaProyecto(frm);
                });
                $("#guardarInfluencias").on("click", function (event) {
                    self.actualizarInfluenciasProyecto();
                });
                $("#nuevoEntidad").on("click", function (event) {
                    self.actualizarEntidadProyecto();
                });
                $("#nuevoFuncion").on("click", function (event) {
                    self.actualizarFuncionProyecto();
                });
            },

            //METODO PARA Consultar proyectos
            traerProyectos: function (estado) {
                ajaxRequest({'estado': estado}, 'post', 'traerProyectos', 'estimacion').done(function (response) {

                    $("#tabla-proyectos tbody").html("");
                    $.each(response.data, function (index, val) {
                        $("#tabla-proyectos tbody").append('<tr>' +
                            '<td>' + val.id + '</td>' +
                            '<td>' + val.nombre + '</td>' +
                            '<td>' + val.descripcion + '</td>' +
                            '<td>' + val.cliente + '</td>' +
                            '<td class="text-right">' +
                            '<a href="javascript:void(0)" class="btn btn-icon-toggle" title="Editar proyecto" onclick="estimacion.editarProyecto('+val.id+')">' +
                            '   <i class="fas fa-lg fa-fw m-r-10 fa-pencil-alt"></i>' +
                            '</a>' +
                            '<a href="javascript:void(0)" class="btn btn-icon-toggle" title="Borrar proyecto" onclick="estimacion.borrarProyecto('+val.id+')">' +
                            '   <i class="fas fa-lg fa-fw m-r-10 fa-trash-alt"></i>' +
                            '</a>' +
                            '</td>' +
                            '</tr>');
                    });
                });
            },
            editarProyecto: function (idProyecto) {
                var self = this;
                ajaxRequest({'id': idProyecto}, 'post', 'traerProyecto', 'estimacion').done(function (response) {
                    let info = response.data;
                    $("#frm-proyecto .id-proyecto").val(info.id);
                    $("#frm-proyecto .nombre-proyecto").val(info.nombre);
                    $("#frm-proyecto .descripcion-proyecto").val(info.descripcion);
                    $("#frm-proyecto .cliente-proyecto").val(info.id_cliente);
                    $(".panel-detalle-proyecto").show()
                    self.estimarProyecto();
                });
                self.llenarEntidadesProyecto(idProyecto);
                self.llenarFuncionesProyecto(idProyecto);
                self.llenarInfluenciasProyecto(idProyecto);

            },
            borrarProyecto: function (idProyecto) {
                swal({
                        title: "Esta seguro?",
                        text: "Eliminará el proyecto seleccionado!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Si, estoy seguro!',
                        cancelButtonText: "No, cancelar!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm){

                        if (isConfirm){
                            ajaxRequest({'id': idProyecto}, 'post', 'borrarProyecto', 'estimacion').done(function (response) {
                                if(response.success){
                                    swal("Eliminado!", "Proyecto eliminado!", "success");
                                    estimacion.traerProyectos('A');
                                    document.getElementById('frm-proyecto').reset();
                                    $(".panel-detalle-proyecto").hide()
                                }else{
                                    swal("Error",response.mensaje,"error");
                                }
                            });

                        } else {
                            swal("Cancelado", "Proyecto no se eliminó", "error");
                            e.preventDefault();
                        }
                    });
            },
            actualizaProyecto: function (frm) {
                ajaxRequest(frm, 'post', 'actualizaProyecto', 'estimacion').done(function (response) {
                    if(response.success) {
                        swal("Exitoso", response.mensaje, "success");
                        if(response.data>1){
                            $("#frm-proyecto .id-proyecto").val(response.data);
                        }
                        estimacion.traerProyectos('A');
                    }else{
                        swal("Error",response.mensaje,"error");
                    }
                });
            },
            estimarProyecto: function () {
                let idProyecto = $("#frm-proyecto .id-proyecto").val();
                ajaxRequest({'id': idProyecto}, 'post', 'estimarProyecto', 'estimacion').done(function (response) {
                    if(response.success) {
                        let info = response.data;
                        $(".precio-estimacion").html('$'+Math.round(info.Precio).toLocaleString()+' COP');
                        $(".horas-estimacion").html(Math.round(info.EH).toLocaleString());
                        $(".semanas-estimacion").html(Math.round(info.Semanas).toLocaleString());
                    }
                });
            },

            llenarClientes: function (estado) {
                ajaxRequest({'estado':estado}, 'post', 'traerClientes', 'estimacion').done(function (response) {
                    $('.cliente-proyecto').empty().append($("<option></option>")
                        .attr("value", '')
                        .text('Seleccione'));
                    $.each(response.data, function (index, val) {
                        $('.cliente-proyecto').append($("<option></option>")
                            .attr("value", val.id)
                            .text(val.nombre+' '+val.apellido));
                    });

                });
            },

            llenarInfluenciasProyecto: function (idProyecto) {
                ajaxRequest({'id':idProyecto}, 'post', 'traerInfluenciasProyecto', 'estimacion').done(function (response) {
                    $("#tabla-influencias tbody").html("");
                    $.each(response.data, function (index, val) {
                        $("#tabla-influencias tbody").append('<tr>' +
                            '<td>' + val.id + '</td>' +
                            '<td>' + val.atributo + '</td>' +
                            '<td><input name="a'+val.id+'" type="range" value="' + parseInt(val.valor) + '" min="0" max="5" list="tickmarks"></td>' +
                            '</tr>');
                    });

                });
            },
            actualizarInfluenciasProyecto: function () {
                let idProyecto = $("#frm-proyecto .id-proyecto").val();
                let influencias = Core.FormToJSON("#frm-influencias");
                ajaxRequest({'id':idProyecto, 'influencias':influencias}, 'post', 'actualizarInfluenciasProyecto', 'estimacion').done(function (response) {
                    if(response.success){
                        swal("Actualizado!", "Influencias actualizadas!", "success");
                    }else{
                        swal("Error",response.mensaje,"error");
                    }
                });
            },

            llenarEntidadesProyecto: function (idProyecto) {
                ajaxRequest({'id':idProyecto}, 'post', 'traerEntidadesProyecto', 'estimacion').done(function (response) {
                    $("#tabla-entidades tbody").html("");
                    $.each(response.data, function (index, val) {
                        $("#tabla-entidades tbody").append('<tr>' +
                            '<td>' + val.id + '</td>' +
                            '<td>' + val.entidad + '</td>' +
                            '<td class="text-right">' +
                            '<a href="javascript:void(0)" class="btn btn-icon-toggle" title="Editar entidad" onclick="estimacion.editarEntidadProyecto('+val.id+',\''+val.entidad+'\')">' +
                            '   <i class="fas fa-lg fa-fw m-r-10 fa-pencil-alt"></i>' +
                            '</a>' +
                            '<a href="javascript:void(0)" class="btn btn-icon-toggle" title="Borrar entidad" onclick="estimacion.borrarEntidadProyecto('+val.id+')">' +
                            '   <i class="fas fa-lg fa-fw m-r-10 fa-trash-alt"></i>' +
                            '</a>' +
                            '</td>' +
                            '</tr>');
                    });

                });
            },
            actualizarEntidadProyecto: function () {
                var self = this;
                let form = Core.FormToJSON("#frm-entidades");
                form['id_proyecto']= $("#frm-proyecto .id-proyecto").val();
                ajaxRequest(form, 'post', 'actualizarEntidadProyecto', 'estimacion').done(function (response) {
                    if(response.success){
                        swal("Actualizado!", "Entidad actualizada!", "success");
                        self.llenarEntidadesProyecto(form['id_proyecto']);
                        document.getElementById('frm-entidades').reset();
                        $("#frm-entidades .id-entidad").val(0);
                    }else{
                        swal("Error",response.mensaje,"error");
                    }
                });
            },
            editarEntidadProyecto: function (id,entidad) {
                $("#frm-entidades .id-entidad").val(id);
                $("#frm-entidades .nombre-entidad").val(entidad).focus();
            },
            borrarEntidadProyecto: function (id) {
                var self = this;
                swal({
                        title: "Esta seguro?",
                        text: "Eliminará la entidad seleccionada!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Si, estoy seguro!',
                        cancelButtonText: "No, cancelar!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm){

                        if (isConfirm){
                            ajaxRequest({'id': id}, 'post', 'borrarEntidadProyecto', 'estimacion').done(function (response) {
                                if(response.success){
                                    swal("Eliminada!", "Entidad eliminada!", "success");
                                    self.llenarEntidadesProyecto($("#frm-proyecto .id-proyecto").val());
                                }else{
                                    swal("Error",response.mensaje,"error");
                                }
                            });

                        } else {
                            swal("Cancelado", "Entidad no se eliminó", "error");
                            e.preventDefault();
                        }
                    });
            },

            llenarFuncionesProyecto: function (idProyecto) {
                ajaxRequest({'id':idProyecto}, 'post', 'traerFuncionesProyecto', 'estimacion').done(function (response) {
                    $("#tabla-funciones tbody").html("");
                    $.each(response.data, function (index, val) {
                        $("#tabla-funciones tbody").append('<tr class="funcion-' + val.id + '">' +
                            '<td>' + (index+1) + '</td>' +
                            '<td>' + val.funcion + '</td>' +
                            '<td>' + val.entidades + '</td>' +
                            '<td>' + val.entradas + '</td>' +
                            '<td>' + val.salidas + '</td>' +
                            '<td>' + val.tipo + '</td>' +
                            '<td class="text-right">' +
                            '<a href="javascript:void(0)" class="btn btn-icon-toggle" title="Editar funcion" onclick="estimacion.editarFuncionProyecto('+val.id+')">' +
                            '   <i class="fas fa-lg fa-fw m-r-10 fa-pencil-alt"></i>' +
                            '</a>' +
                            '<a href="javascript:void(0)" class="btn btn-icon-toggle" title="Borrar funcion" onclick="estimacion.borrarFuncionProyecto('+val.id+')">' +
                            '   <i class="fas fa-lg fa-fw m-r-10 fa-trash-alt"></i>' +
                            '</a>' +
                            '</td>' +
                            '</tr>');
                        $(".funcion-"+val.id).data("info",val);
                    });

                });
            },
            actualizarFuncionProyecto: function () {
                var self = this;
                let form = Core.FormToJSON("#frm-funciones");
                form['id_proyecto']= $("#frm-proyecto .id-proyecto").val();
                ajaxRequest(form, 'post', 'actualizarFuncionProyecto', 'estimacion').done(function (response) {
                    if(response.success){
                        $("#frm-funciones .nombre-funcion").focus();
                        self.llenarFuncionesProyecto($("#frm-proyecto .id-proyecto").val());
                        document.getElementById('frm-funciones').reset();
                        $("#frm-funciones .id-funcion").val(0);
                        swal("Actualizado!", "Funcion actualizada!", "success");

                    }else{
                        swal("Error",response.mensaje,"error");
                    }
                });
            },
            editarFuncionProyecto: function (id) {
                let info=$(".funcion-"+id).data('info');
                console.log(info);
                $("#frm-funciones .id-funcion").val(id);
                $("#frm-funciones .nombre-funcion").val(info.funcion).focus();
                $("#frm-funciones .entidades-funcion").val(info.entidades);
                $("#frm-funciones .entradas-funcion").val(info.entradas);
                $("#frm-funciones .salidas-funcion").val(info.salidas);
                $("#frm-funciones .tipo-funcion").val(info.tipo);
            },
            borrarFuncionProyecto: function (id) {
                var self = this;
                swal({
                        title: "Esta seguro?",
                        text: "Eliminará la funcion seleccionada!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Si, estoy seguro!',
                        cancelButtonText: "No, cancelar!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm){

                        if (isConfirm){
                            ajaxRequest({'id': id}, 'post', 'borrarFuncionProyecto', 'estimacion').done(function (response) {
                                if(response.success){
                                    swal("Eliminada!", "Funcion eliminada!", "success");
                                    self.llenarFuncionesProyecto($("#frm-proyecto .id-proyecto").val());
                                }else{
                                    swal("Error",response.mensaje,"error");
                                }
                            });

                        } else {
                            swal("Cancelado", "Funcion no se eliminó", "error");
                            e.preventDefault();
                        }
                    });
            },
        }
    })()
    estimacion.Initialize()
})(document, window, jQuery, Core)

