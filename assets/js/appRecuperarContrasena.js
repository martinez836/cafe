RecuperarContrasenaForm = document.querySelector('#RecuperarContrasenaForm');
RecuperarContrasenaForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const correo = document.querySelector("#correo").value;
    console.log('Formulario enviado, correo:', correo);
    
    fetch('../controllers/recuperacion_contrasena.php', {
        method: "POST",
        body: JSON.stringify({ correo }),
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(respuesta => {
        console.log('Respuesta recibida:', respuesta);
        return respuesta.json();
    })
    .then(datos => {
        console.log('Datos recibidos del backend:', datos);
        if (datos.success) {
            Swal.fire({
                icon: 'success',
                title: 'Correo enviado',
                text: 'Se ha enviado un correo con las instrucciones para recuperar su contraseña.',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                window.location.href = './login.php';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.message || 'Error al enviar el correo. Por favor, intente nuevamente.',
                confirmButtonColor: '#3085d6'
            });
        }
    })
    .catch((error) => {
        console.error('Error en fetch o parseo JSON:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo conectar con el servidor.',
            confirmButtonColor: '#3085d6'
        });
    });
});
