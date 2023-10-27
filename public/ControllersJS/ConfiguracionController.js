/**
 * 
 * @autor xxx xxxx (xxxxxxx@lebon.com.co)
 * @fecha   
 * @version 
 */
(function (document, window, $, Core) {
    (function () {

        return configuracion = {

            //INICIALIZA COMPONENTES
            Initialize: function () {
                var self = this;

                //id de la tabla en la vista
                target_contenedor = $("#frm .row");
                template_contenedor = target_contenedor.find(".grupoVariables").clone(true);

                //forma de asignar un click a un boton
                $("#myBoton").on("click", function (event) {
                    self.saveParameters();
                });
                self.loadParameters();
            },

            //PETICION AJAX
            ajaxRequest: function (data, type, method) {
                return $.ajax({
                    url: 'rest.php',
                    data: data,
                    type: type,
                    dataType: 'json',
                    async: true,
                    data: {
                        "modulo": "configuracion",
                        "metodo": method,
                        "token": getToken(),
                        "parametros": data
                    }
                })
            },

            loadParameters: function () {
                var self = this;
                ajaxRequest({}, 'post', 'loadParameters', 'configuracion').done(function (response) {

                    template_tabla = target_contenedor.find("tbody").clone(true);
                    target_contenedor.html("");

                    $.each(response, function (categoria, valores) {
                        var cloned_linea = template_contenedor.clone(true);
                        cloned_linea.attr("id", + categoria);
                        cloned_linea.find("h4").html(categoria);
                        cloned_linea.find("tbody").html("");
                        $.each(valores, function (campo, valor) {
                            var cloned_tr = template_tabla.clone(true);
                            cloned_tr.find(".tdVariable").html(campo);
                            cloned_tr.find(".tdValor input").attr("value", valor);
                            cloned_tr.find(".tdValor input").attr("name", campo);
                            cloned_tr.find(".tdValor input").attr("id", campo);
                            cloned_tr.find(".tdValor input").attr("placeholder", campo);
                            cloned_linea.find("tbody").append(cloned_tr.html());
                        });
                        target_contenedor.append(cloned_linea);
                    });

                });
            },
            saveParameters: function () {
                var data = Core.FormToJSON("#frm");
                ajaxRequest({ data }, 'post', 'saveParameters', 'configuracion').done(function (response) {
                    notyf.success("Configuraci&oacute;n grabada");
                });
            },
        }
    })()
    configuracion.Initialize()
})(document, window, jQuery, Core)

