$(document).ready(function() {

    var formulario = document.getElementById('form_pass');
    var elementos = formulario.elements;


    for (var i = 0; i < elementos.length; i++) {
        if (elementos[i].value.length !== 0 && elementos[i].type !== 'submit') {
            elementos[i].parentElement.children[1].className = "label active";
        }
    }
    // Funcion que se ejecuta cuando el evento click es activado

    var validarInputs = function() {
        for (var i = 0; i < elementos.length; i++) {
            // Si es tipo texto, email o password vamos a comprobar que esten completados los input
            if (elementos[i].value.length == 0) {
                console.log('El campo ' + elementos[i].name + ' esta incompleto');
                elementos[i].className = elementos[i].className + " error";
                return false;
            } else {
                elementos[i].className = elementos[i].className.replace(" error", "");
            }
        }
        if (elementos.pass.value !== elementos.pass2.value) {
            elementos.pass2.value = "";
            elementos.pass2.className = elementos.pass2.className + " error";
            $('.error_title').html("Error: The confirmation pass is wrong");
            $('.error_title').show();
            return false;
        }

        return true;
    };

    var change =
            function change() {

                var passOld = $('input#passOld').val();
                var pass = $('input#pass').val();
                var pass2 = $('input#pass2').val();
                $.ajax({
                    data: {passOld: passOld,
                        pass: pass,
                        pass2: pass2,
                    },
                    url: 'changePass.php',
                    type: 'post',
                    beforeSend: function() {
                        //$("h2.title_two").html("Procesando, espere por favor...");
                    },
                    success: function(response) {
                        //$('.error').show();

                        $('.error_title').html(response)
                        if ($('.error_title').text().indexOf("Error: the new") >= 0) {
                            elementos.pass.value = "";
                            elementos.pass2.value = "";
                            elementos.pass2.className = elementos.pass2.className + " error";
                            elementos.pass.className = elementos.pass.className + " error";
                            $('.error_title').show();
                        } else if ($('.error_title').text().indexOf("Error: the confirmation") >= 0) {
                            elementos.pass2.value = "";
                            elementos.pass2.className = elementos.pass2.className + " error";
                            $('.error_title').show();
                        } else if ($('.error_title').text().indexOf("Error: the old") >= 0) {
                            elementos.passOld.value = "";
                            elementos.passOld.className = elementos.passOld.className + " error";
                            $('.error_title').show();
                        } else {
                            $('.error_title').hide();
                            $('div.btn-login').hide();
                            $('h3.title_three.pass').html("Password change correctly");
                            $('div#tab').hide();
                            //myVar = setTimeout(redirect, 3000);
                        }
                    }});
            }

    var enviar = function(e) {
        if (!validarInputs()) {
            console.log('Falto validar los Input');
            e.preventDefault();
        } else if (!change()) {
            console.log('Error in change pass');
            e.preventDefault();
        } else {
            console.log('Envia');
            e.preventDefault();
        }
    };

    var focusInput = function() {
        this.parentElement.children[1].className = "label active";
        this.parentElement.children[0].className = this.parentElement.children[0].className.replace("error", "");
    };

    var blurInput = function() {
        if (this.value <= 0) {
            this.parentElement.children[1].className = "label";
            this.parentElement.children[0].className = this.parentElement.children[0].className + " error";
        }
    };

    // --- Eventos ---
    formulario.addEventListener("submit", enviar);

    for (var i = 0; i < elementos.length; i++) {
        elementos[i].addEventListener("focus", focusInput);
        elementos[i].addEventListener("blur", blurInput);
    }

}());
