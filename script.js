//Validaciones del login
const formLogin = document.getElementById("formLogin");

if (formLogin) {

    formLogin.addEventListener("submit", function(event) {

        const usuario = document.getElementById("usuario").value.trim();
        const clave = document.getElementById("clave").value.trim();

        if (usuario === "" || clave === "") {

            event.preventDefault();

            alert("Debe completar el usuario y la contraseña.");

        }

    });

}

//Validaciones al agregar un contacto
const formContacto = document.getElementById("formContacto");

if (formContacto) {

    formContacto.addEventListener("submit", function(event) {

        const nombre = document.getElementById("nombre").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const direccion = document.getElementById("direccion").value.trim();
        const empresa = document.getElementById("empresa").value.trim();
        const ubicacion = document.getElementById("ubicacion").value.trim();

        if (
            nombre === "" ||
            telefono === "" ||
            correo === "" ||
            direccion === "" ||
            empresa === "" ||
            ubicacion === ""
        ) {

            event.preventDefault();

            alert("Debe completar todos los campos.");

            return;
        }

        if (!/^\d+$/.test(telefono)) {

            event.preventDefault();

            alert("El teléfono debe contener únicamente números.");

            return;
        }

        if (!correo.includes("@")) {

            event.preventDefault();

            alert("Ingrese un correo electrónico válido.");

            return;
        }

    });

}



//Mensaje para confirmar guardado de datos
setTimeout(function() {

    const mensaje = document.getElementById("mensajeExito");

    if (mensaje) {

        mensaje.style.opacity = "0";

        setTimeout(function() {
            mensaje.remove();
        }, 500);

    }

}, 3000);



//Mensaje de login
setTimeout(function() {

    const mensaje = document.getElementById("mensajeLogin");

    if (mensaje) {

        mensaje.style.opacity = "0";
        mensaje.style.transition = "opacity 0.5s ease";

        setTimeout(function() {
            mensaje.remove();
        }, 500);

    }

}, 2000);