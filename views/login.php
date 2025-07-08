<?php
require_once '../config/config.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gestión de Café</title>
    <link href="../assets/cssBootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/login.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-coffee"></i>
            <h2>Sistema de Gestión de Café</h2>
            <p class="text-muted">Inicia sesión para continuar</p>
        </div>

        <div id="loginAlert" style="display: none;"></div>

        <form id="loginForm" autocomplete="off">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="email" placeholder="Correo electrónico" required autocomplete="username">
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="contrasena" name="password" placeholder="Contraseña" required autocomplete="current-password">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('contrasena', this)" title="Mostrar/Ocultar contraseña">👁</button>
                </div>
            </div>
            <div class="mb-3">
                <a href="./restaurarContrasena.php">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-login w-100">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
    </div>

    <script src="../assets/jsBootstrap/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/login.js"></script>
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
</script>
</body>
</html> 