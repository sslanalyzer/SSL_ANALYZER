$(document).ready(function() {


    $("#li_tab1").click(function() {
        $("#li_tab1").addClass("active");
        $("#li_tab2").removeClass("active");
        $("#li_tab3").removeClass("active");
        document.getElementById('tab1').style.display = 'block';
        document.getElementById('tab2').style.display = 'none';
        document.getElementById('tab3').style.display = 'none';
        $('.error_title').hide();


// Recorrer los elementos y hacer que onchange ejecute una funcion para comprobar el valor de ese input
        (function() {


            var formulario = document.getElementById('form1');
            var elementos = formulario.elements;


            for (var i = 0; i < elementos.length; i++) {
                if (elementos[i].value.length !== 0 && elementos[i].type !== 'submit') {
                    elementos[i].parentElement.children[1].className = "label active";
                }
            }
            // Funcion que se ejecuta cuando el evento click es activado

            var validarInputs = function() {
                for (var i = 0; i < elementos.length; i++) {
                    // Identificamos si el elemento es de tipo texto, email, password, radio o checkbox
                    if (elementos[i].type == "text" || elementos[i].type == "email" || elementos[i].type == "password") {
                        // Si es tipo texto, email o password vamos a comprobar que esten completados los input
                        if (elementos[i].value.length == 0) {
                            console.log('El campo ' + elementos[i].name + ' esta incompleto');
                            elementos[i].className = elementos[i].className + " error";
                            return false;
                        } else {
                            elementos[i].className = elementos[i].className.replace(" error", "");
                        }
                    }
                }

                // Comprobando que las contraseñas coincidan
                if (elementos.pass.value !== elementos.pass2.value) {
                    elementos.pass.value = "";
                    elementos.pass2.value = "";
                    elementos.pass.className = elementos.pass.className + " error";
                    elementos.pass2.className = elementos.pass2.className + " error";
                } else {
                    elementos.pass.className = elementos.pass.className.replace(" error", "");
                    elementos.pass2.className = elementos.pass2.className.replace(" error", "");
                }

                if (!/^[a-zA-Z0-9]+$/.test(elementos.name.value)) {
                    console.log('El campo ' + elementos.name.name + ' debe ser letras y numeros');
                    elementos.name.value = "";
                    elementos.name.className = elementos.name.className + " error";
                    $('.error_title').html("The name must be only letters and numbers");
                    elementos.name.value = "";
                    elementos.name.className = elementos.name.className + " error";
                    $('.error_title').show();
                    return false;
                }

                if (elementos.name.value.length >= 13) {
                    console.log('El campo ' + elementos.name.name + ' es demasiado largo');
                    elementos.name.value = "";
                    elementos.name.className = elementos.name.className + " error";
                    $('.error_title').html("Name too long (max 12)");
                    elementos.name.value = "";
                    elementos.name.className = elementos.name.className + " error";
                    $('.error_title').show();
                    return false;
                } else {
                    elementos.name.className = elementos.name.className.replace(" error", "");
                }
                return true;
            };

            var login =
                    function login() {

                        var name = $('input#name').val();
                        var pass = $('input#pass').val();
                        var formulario = document.getElementById('form1');
                        var elementos = formulario.elements;

                        $.ajax({
                            data: {name: name,
                                pass: pass,
                            },
                            url: 'login.php',
                            type: 'post',
                            beforeSend: function() {
                                //$("h2.title_two").html("Procesando, espere por favor...");
                            },
                            success: function(response) {
                                //$('.error').show();

                                $('.error_title').html(response)
                                if ($('.error_title').text().indexOf("Error: The user") >= 0) {
                                    elementos.name.value = "";
                                    elementos.name.className = elementos.name.className + " error";
                                    $('.error_title').show();
                                } else if ($('.error_title').text().indexOf("Error: The pass") >= 0) {
                                    elementos.pass.value = "";
                                    elementos.pass.className = elementos.pass.className + " error";
                                    $('.error_title').show();
                                } else {
                                    $('.error_title').hide();
                                    $('div.btn-login').hide();
                                    $('div.wrap').html(response);
                                    myVar = setTimeout(redirect, 3000);
                                }
                            }});
                    }

            var enviar = function(e) {
                if (!validarInputs()) {
                    console.log('Falto validar los Input');
                    e.preventDefault();
                } else {
                    if (!login()) {
                        console.log('Error al logear');
                        e.preventDefault();
                    } else {
                        console.log('Envia');
                        e.preventDefault();
                    }
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
                if (elementos[i].type == "text" || elementos[i].type == "email" || elementos[i].type == "password") {
                    elementos[i].addEventListener("focus", focusInput);
                    elementos[i].addEventListener("blur", blurInput);
                }
            }

        }())

    });

    $("#li_tab2").click(function() {
        $("#li_tab1").removeClass("active");
        $("#li_tab2").addClass("active");
        $("#li_tab3").removeClass("active");
        document.getElementById('tab1').style.display = 'none';
        document.getElementById('tab2').style.display = 'block';
        document.getElementById('tab3').style.display = 'none';
        $('.error_title').hide();

// Recorrer los elementos y hacer que onchange ejecute una funcion para comprobar el valor de ese input
        (function() {


            var formulario = document.getElementById('form2');
            var elementos = formulario.elements;



            for (var i = 0; i < elementos.length; i++) {
                if (elementos[i].value.length !== 0 && elementos[i].type !== 'submit') {
                    elementos[i].parentElement.children[1].className = "label active";
                }
            }
            // Funcion que se ejecuta cuando el evento click es activado

            var validarInputs = function() {
                for (var i = 0; i < elementos.length; i++) {
                    // Identificamos si el elemento es de tipo texto, email, password, radio o checkbox
                    if (elementos[i].type == "text" || elementos[i].type == "email" || elementos[i].type == "password") {
                        // Si es tipo texto, email o password vamos a comprobar que esten completados los input
                        if (elementos[i].value.length == 0) {
                            console.log('El campo ' + elementos[i].name + ' esta incompleto');
                            elementos[i].className = elementos[i].className + " error";
                            return false;
                        } else {
                            elementos[i].className = elementos[i].className.replace(" error", "");
                        }
                    }
                }

                if (!/^[a-zA-Z0-9]+$/.test(elementos.name1.value)) {
                    console.log('El campo ' + elementos.name1.name + ' debe ser letras y numeros');
                    elementos.name1.value = "";
                    elementos.name1.className = elementos.name1.className + " error";
                    $('.error_title').html("The name must be only letters and numbers");
                    elementos.name1.value = "";
                    elementos.name1.className = elementos.name1.className + " error";
                    $('.error_title').show();
                    return false;
                }

                if (elementos.name1.value.length >= 13) {
                    console.log('El campo ' + elementos.name1.name + ' es demasiado largo');
                    elementos.name1.value = "";
                    elementos.name1.className = elementos.name1.className + " error";
                    $('.error_title').html("Name too long (max 12)");
                    elementos.name1.value = "";
                    elementos.name1.className = elementos.name1.className + " error";
                    $('.error_title').show();
                    return false;
                } else {
                    elementos.name1.className = elementos.name1.className.replace(" error", "");
                }

                // Comprobando que las contraseñas coincidan
                if (elementos.pass1.value !== elementos.pass2.value) {
                    elementos.pass1.value = "";
                    elementos.pass2.value = "";
                    elementos.pass1.className = elementos.pass1.className + " error";
                    elementos.pass2.className = elementos.pass2.className + " error";
                    return false;
                } else {
                    elementos.pass1.className = elementos.pass1.className.replace(" error", "");
                    elementos.pass2.className = elementos.pass2.className.replace(" error", "");
                }

                return true;
            };

            var validarCheckbox = function() {
                var opciones = document.getElementsByName('terms'),
                        resultado = false;

                for (var i = 0; i < elementos.length; i++) {
                    if (elementos[i].type == "checkbox") {
                        for (var o = 0; o < opciones.length; o++) {
                            if (opciones[o].checked) {
                                resultado = true;
                                break;
                            }
                        }

                        if (resultado == false) {
                            elementos[i].parentNode.className = elementos[i].parentNode.className + " error";
                            console.log('El campo checkbox esta incompleto');
                            return false;
                        } else {
                            // Eliminamos la clase Error del checkbox
                            elementos[i].parentNode.className = elementos[i].parentNode.className.replace(" error", "");
                            return true;
                        }
                    }
                }
            };

            var register =
                    function register() {

                        var name = $('input#name1').val();
                        var pass = $('input#pass1').val();
                        var email = $('input#email').val();
                        var formulario = document.getElementById('form2');
                        var elementos = formulario.elements;

                        $.ajax({
                            data: {name: name,
                                pass: pass,
                                email: email
                            },
                            url: 'register.php',
                            type: 'post',
                            beforeSend: function() {
                                //$("h2.title_two").html("Procesando, espere por favor...");
                            },
                            success: function(response) {
                                //$('.error').show();

                                $('.error_title').html(response)
                                if ($('.error_title').text().indexOf("Error: the") >= 0) {
                                    if ($('.error_title').text().indexOf("Error: the email") >= 0) {
                                        elementos.email.value = "";
                                        elementos.email.className = elementos.email.className + " error";
                                    } else {
                                        elementos.name1.value = "";
                                        elementos.name1.className = elementos.name1.className + " error";
                                    }
                                    $('.error_title').show();
                                } else {
                                    $('.error_title').hide();
                                    $('div.btn-login').hide();
                                    $('div.wrap').html(response);
                                    myVar = setTimeout(redirect, 3000);
                                }
                            }});
                    }

            var enviar = function(e) {
                if (!validarInputs()) {
                    console.log('Falto validar los Input');
                    e.preventDefault();
                } else if (!validarCheckbox()) {
                    console.log('Falto validar Checkbox');
                    e.preventDefault();
                } else {
                    if (!register()) {
                        console.log('Error al regsitrar');
                        e.preventDefault();
                    } else {
                        console.log('Envia');
                        e.preventDefault();
                    }
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
            formulario.addEventListener("submit", enviar, register);

            for (var i = 0; i < elementos.length; i++) {
                if (elementos[i].type == "text" || elementos[i].type == "email" || elementos[i].type == "password") {
                    elementos[i].addEventListener("focus", focusInput);
                    elementos[i].addEventListener("blur", blurInput);
                }
            }
        }())
    });

    $("#li_tab3").click(function() {
        $("#li_tab1").removeClass("active");
        $("#li_tab2").removeClass("active");
        $("#li_tab3").addClass("active");
        document.getElementById('tab1').style.display = 'none';
        document.getElementById('tab2').style.display = 'none';
        document.getElementById('tab3').style.display = 'block';
        $('.error_title').hide();

// Recorrer los elementos y hacer que onchange ejecute una funcion para comprobar el valor de ese input
        (function() {


            var formulario = document.getElementById('form3');
            var elementos = formulario.elements;


            for (var i = 0; i < elementos.length; i++) {
                if (elementos[i].value.length !== 0 && elementos[i].type !== 'submit') {
                    elementos[i].parentElement.children[1].className = "label active";
                }
            }
            // Funcion que se ejecuta cuando el evento click es activado

            var validarInputs = function() {
                for (var i = 0; i < elementos.length; i++) {
                    // Identificamos si el elemento es de tipo texto, email, password, radio o checkbox
                    if (elementos[i].type == "text" || elementos[i].type == "email" || elementos[i].type == "password") {
                        // Si es tipo texto, email o password vamos a comprobar que esten completados los input
                        if (elementos[i].value.length == 0) {
                            console.log('El campo ' + elementos[i].name + ' esta incompleto');
                            elementos[i].className = elementos[i].className + " error";
                            return false;
                        } else {
                            elementos[i].className = elementos[i].className.replace(" error", "");
                        }
                    }
                }

                // Comprobando que las contraseñas coincidan
                if (elementos.pass.value !== elementos.pass2.value) {
                    elementos.pass.value = "";
                    elementos.pass2.value = "";
                    elementos.pass.className = elementos.pass.className + " error";
                    elementos.pass2.className = elementos.pass2.className + " error";
                } else {
                    elementos.pass.className = elementos.pass.className.replace(" error", "");
                    elementos.pass2.className = elementos.pass2.className.replace(" error", "");
                }

                return true;
            };


            var enviar = function(e) {
                if (!validarInputs()) {
                    console.log('Falto validar los Input');
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
                if (elementos[i].type == "text" || elementos[i].type == "email" || elementos[i].type == "password") {
                    elementos[i].addEventListener("focus", focusInput);
                    elementos[i].addEventListener("blur", blurInput);
                }
            }

        }())

    });

    if ($('#li_tab1').hasClass('active')) {
        //----------------  DEFAULT ---------------

        var formulario = document.getElementById('form1');
        var elementos = formulario.elements;

        for (var i = 0; i < elementos.length; i++) {
            if (elementos[i].value.length !== 0 && elementos[i].type !== 'submit') {
                elementos[i].parentElement.children[1].className = "label active";
            }
        }

        // Funcion que se ejecuta cuando el evento click es activado

        var validarInputs = function() {
            for (var i = 0; i < elementos.length; i++) {
                // Identificamos si el elemento es de tipo texto, email, password, radio o checkbox
                if (elementos[i].type == "text" || elementos[i].type == "email" || elementos[i].type == "password") {
                    // Si es tipo texto, email o password vamos a comprobar que esten completados los input
                    if (elementos[i].value.length == 0) {
                        console.log('El campo ' + elementos[i].name + ' esta incompleto');
                        elementos[i].className = elementos[i].className + " error";
                        return false;
                    } else {
                        elementos[i].className = elementos[i].className.replace(" error", "");
                    }
                }
            }

            if (!/^[a-zA-Z0-9]+$/.test(elementos.name.value)) {
                console.log('El campo ' + elementos.name.name + ' debe ser letras y numeros');
                elementos.name.value = "";
                elementos.name.className = elementos.name.className + " error";
                $('.error_title').html("The name must be only letters and numbers");
                elementos.name.value = "";
                elementos.name.className = elementos.name.className + " error";
                $('.error_title').show();
                return false;
            }

            if (elementos.name.value.length >= 13) {
                console.log('El campo ' + elementos.name.name + ' es demasiado largo');
                elementos.name.value = "";
                elementos.name.className = elementos.name.className + " error";
                $('.error_title').html("Name too long (max 12)");
                elementos.name.value = "";
                elementos.name.className = elementos.name.className + " error";
                $('.error_title').show();
                return false;
            } else {
                elementos.name.className = elementos.name.className.replace(" error", "");
            }

            return true;
        };

        var login =
                function login() {

                    var name = $('input#name').val();
                    var pass = $('input#pass').val();
                    var formulario = document.getElementById('form1');
                    var elementos = formulario.elements;

                    $.ajax({
                        data: {name: name,
                            pass: pass,
                        },
                        url: 'login.php',
                        type: 'post',
                        beforeSend: function() {
                            //$("h2.title_two").html("Procesando, espere por favor...");
                        },
                        success: function(response) {
                            //$('.error').show();

                            $('.error_title').html(response)
                            if ($('.error_title').text().indexOf("Error: The user") >= 0) {
                                elementos.name.value = "";
                                elementos.name.className = elementos.name.className + " error";
                                $('.error_title').show();
                            } else if ($('.error_title').text().indexOf("Error: The pass") >= 0) {
                                elementos.pass.value = "";
                                elementos.pass.className = elementos.pass.className + " error";
                                $('.error_title').show();
                            } else {
                                $('.error_title').hide();
                                $('div.btn-login').hide();
                                $('div.wrap').html(response);
                                myVar = setTimeout(redirect, 3000);
                            }
                        }});
                }

        var enviar = function(e) {
            if (!validarInputs()) {
                console.log('Falto validar los Input');
                e.preventDefault();
            } else {
                if (!login()) {
                    console.log('Error al logear');
                    e.preventDefault();
                } else {
                    console.log('Envia');
                    e.preventDefault();
                }
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
            if (elementos[i].type == "text" || elementos[i].type == "email" || elementos[i].type == "password") {
                elementos[i].addEventListener("focus", focusInput);
                elementos[i].addEventListener("blur", blurInput);
            }
        }

        //-----------------------------------------
    }

});


function getTerms() {
    $('div.emergent').show();
}

function close_aside() {
    $('div.emergent').hide();
}

function redirect() {
    location.href = 'index.html';
}