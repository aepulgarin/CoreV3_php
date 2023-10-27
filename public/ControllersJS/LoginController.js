(function (document, window, $, Core) {
  (function () {

    return LoginController = {

      usuario: "",
      //INICIALIZA COMPONENTES
      Initialize: function () {
        var self = this;

        //$("#db").parent().hide();
        localStorage.removeItem('menu');
        localStorage.removeItem('usuario');
        //self.ValidarMultipleDB();

        $("#password").keydown(function (e) {
          if (e.keyCode == '13') {
            self.autenticar();
          }
        });

        $("#btn-ingreso-app").click(function (event) {
          self.autenticar();
        });
        usuario = localStorage.getItem('username');
        if (usuario != null) {
          $('#username').val(usuario).change();
          $("#recordarme").prop('checked', true);
          $('#password').focus();
        }
      },

      ValidarMultipleDB: function () {
        var self = this;
        ajaxRequest({}, 'post', 'ValidarMultipleDB', 'login').done(function (response) {

          if (response.multiple == 'S') {
            $("#db").parent().show();
            $("#db").val(response.defecto);
          } else {
            $("#db").parent().remove();
          }

        });
      },

      autenticar: function () {
        var self = this;
        ajaxRequest({ "usuario": $('#username').val(), "password": sha1($('#password').val()), "db": $('#db').val() }, 'post', 'autenticar', 'login').done(function (response) {

          if (response.success === true) {
            localStorage.setItem('usuario', JSON.stringify(response.data));
            if ($("#recordarme").prop('checked')) {
              localStorage.setItem('username', $('#username').val());
            } else {
              localStorage.removeItem('username');
            }
            var redir = Core.GetUrlParameter('redir');
            if (redir) {
              redir = "&redirect=" + redir;
            } else {
              redir = "";
            }
            document.location = 'index.php?modulo=inicio' + redir;
          } else {
            //toastr.error(response.mensaje);
            alert(response.mensaje);
          }
        });
      },
    }
  })()
  LoginController.Initialize()
})(document, window, jQuery, Core)