<?php
require_once '../config/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
$mysqli = new mysqli('localhost', 'root', '', 'bd_cafe');
if ($mysqli->connect_error) {
    die('Conexión fallida: ' . $mysqli->connect_error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restaurar Contraseña - Tienda de Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estiloMesero.css">
</head>
<body class="bg-coffee">
    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 w-100 px-4 px-lg-5">
        <div class="d-flex flex-column flex-lg-row justify-content-center align-items-center w-100 gap-2 mx-auto" style="max-width: 900px;">
            <header class="text-center text-lg-end mb-4 mb-lg-0 col-12 col-lg-auto">
                <h1 class="display-4 text-light fw-bold">
                    <i class="fas fa-mug-hot me-2"></i>Tienda de Café
                </h1>
                <h4 class="text-light opacity-75">Restaurar Contraseña</h4>
            </header>
            <div class="col-12 col-lg-4 col-md-6 mx-auto" style="max-width: 400px;">
                <div class="card shadow-lg border-0 rounded-4 bg-light">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-key me-2"></i>Restaurar Contraseña
                        </h5>
                        <?php
                        if (isset($_GET['correo']) && isset($_GET['codigo'])) {
                            $correo = $_GET['correo'];
                            $codigo = $_GET['codigo'];
                            $stmt = $mysqli->prepare("SELECT * FROM recuperacion WHERE correo_recuperacion = ? AND codigo_recuperacion = ?");
                            $stmt->bind_param("ss", $correo, $codigo);
                            $stmt->execute();
                            $resultado = $stmt->get_result();
                            if ($resultado->num_rows > 0) {
                        ?>
                        <form id="formNuevaContrasena">
                            <input type="hidden" name="correo" value="<?php echo htmlspecialchars($correo); ?>">
                            <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($codigo); ?>">
                            <div class="mb-4">
                                <label for="nueva_contrasena" class="form-label fw-semibold">Nueva Contraseña:</label>
                                <div class="input-group">
                                    <input type="password" name="nueva_contrasena" id="nueva_contrasena" class="form-control" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('nueva_contrasena', this)" title="Mostrar/Ocultar contraseña">👁</button>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="confirmar_contrasena" class="form-label fw-semibold">Confirmar Contraseña:</label>
                                <div class="input-group">
                                    <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" class="form-control" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirmar_contrasena', this)" title="Mostrar/Ocultar contraseña">👁</button>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-lg w-100 btn-primary">
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </form>
                        <?php
                            } else {
                                echo '<div class="alert alert-danger text-center">El enlace de recuperación ha expirado o es inválido.</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger text-center">Faltan los parámetros necesarios.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                btn.textContent = "🙈";
            } else {
                input.type = "password";
                btn.textContent = "👁";
            }
        }
        const form = document.getElementById('formNuevaContrasena');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const password = document.getElementById('nueva_contrasena').value;
                const confirm = document.getElementById('confirmar_contrasena').value;
                if (password.length < 5) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Contraseña muy corta',
                        text: 'La contraseña debe tener al menos 5 caracteres.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                if (password !== confirm) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Las contraseñas no coinciden',
                        text: 'Por favor, asegúrate de que ambas contraseñas sean iguales.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                fetch('../controllers/actualizar_contrasena.php', {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Contraseña Actualizada!',
                            text: data.message || 'Tu contraseña ha sido actualizada exitosamente.',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            window.location.href = './InicioSesion.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Hubo un error al actualizar la contraseña.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error al procesar tu solicitud.',
                        confirmButtonColor: '#3085d6'
                    });
                });
            });
        }
    </script>
</body>
</html> 