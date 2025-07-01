<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tienda de Café</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/estiloMesero.css" />
</head>

<body class="bg-coffee">
  <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 w-100 px-4 px-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-center align-items-center w-100 gap-2 mx-auto" style="max-width: 900px;">
      <header class="text-center text-lg-end mb-4 mb-lg-0 col-12 col-lg-auto">
        <h1 class="display-4 text-light fw-bold">
          <i class="fas fa-mug-hot me-2"></i>Tienda de Café
        </h1>
        <h4 class="text-light opacity-75">Inicio de Sesión</h4>
      </header>
      <div class="col-12 col-lg-4 col-md-6 mx-auto" style="max-width: 400px;">
        <div class="card shadow-lg border-0 rounded-4 bg-light">
          <div class="card-body p-4">
            <h5 class="card-title mb-3">
              <i class="fas fa-user me-2"></i>Iniciar Sesión
            </h5>
            <form id="loginForm">
              <div class="mb-3">
                <label for="usuario" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" required />
              </div>
              <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="contrasena" required />
                  <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('contrasena', this)" title="Mostrar/Ocultar contraseña">👁</button>
                </div>
              </div>
              <div class="mb-3">
                <a href="./restaurarContrasena.php">¿Olvidaste tu contraseña?</a>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/appLogin.js"></script>
</body>
</html>
