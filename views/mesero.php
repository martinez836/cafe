<?php
/* session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
} */

require_once '../models/consultas.php';
require_once '../config/config.php';

try {
    $consultas = new ConsultasMesero();
    $mesas = $consultas->traerMesas();
    $categorias = $consultas->traerCategorias();
} catch (Exception $e) {
    die("Error al cargar datos iniciales: " . $e->getMessage());
}
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
  <div class="container py-4">
    <header class="text-center mb-5">
      <h1 class="display-4 text-light fw-bold">
        <i class="fas fa-mug-hot me-2"></i>Tienda de Café
      </h1>
      <p class="text-light opacity-75">Sistema de Gestión de Pedidos</p>
    </header>

    <div class="row g-4">
      <!-- Panel de Selección -->
      <div class="col-lg-4">
        <div class="card shadow-lg border-0 rounded-4 bg-light">
          <div class="card-body p-4">
            <!-- Selección de Mesa -->
            <div class="mb-4">
              <h5 class="card-title mb-3">
                <i class="fas fa-chair me-2"></i>Mesa Actual
              </h5>
              <select id="mesaSelect" class="form-select form-select-lg rounded-3">
                <option value="">Seleccione una mesa</option>
                <?php 
                if ($mesas && is_array($mesas)) {
                    foreach ($mesas as $mesa) {
                        $disabled = ($mesa['estados_idestados'] == 3 || $mesa['tiene_token_activo'] > 0 || $mesa['tiene_pedido_activo'] > 0) ? 'disabled' : '';
                        $msg = $mesa['estados_idestados'] == 3 ? ' (Ocupada)' : 
                               ($mesa['tiene_token_activo'] > 0 ? ' (Token activo)' : 
                               ($mesa['tiene_pedido_activo'] > 0 ? ' (Pedido activo)' : ''));
                        echo '<option value="' . (int)$mesa['idmesas'] . '" ' . $disabled . '>' . htmlspecialchars($mesa['nombre']) . $msg . '</option>';
                    }
                }
                ?>
              </select>
              <button class="btn btn-warning mt-2 w-100" onclick="generarTokenMesa()">
                <i class="fas fa-key me-2"></i>Generar Token para la Mesa
              </button>
            </div>

            <!-- Categorías -->
            <div class="mb-4">
              <h5 class="card-title mb-3">
                <i class="fas fa-tags me-2"></i>Categoría
              </h5>
              <select id="categoriaSelect" class="form-select form-select-lg rounded-3">
                <option value="">Seleccione una categoría</option>
                <?php 
                if ($categorias && is_array($categorias)) {
                    foreach ($categorias as $categoria) {
                        echo '<option value="' . (int)$categoria['idcategorias'] . '">' . 
                             htmlspecialchars($categoria['nombre_categoria']) . '</option>';
                    }
                }
                ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel de Productos -->
      <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-4 bg-light">
          <div class="card-body p-4">
            <h5 class="card-title mb-4">
              <i class="fas fa-coffee me-2"></i>Productos Disponibles
            </h5>
            <div class="mb-3">
              <input type="text" id="buscadorProductos" class="form-control" placeholder="Buscar producto...">
            </div>
            <div class="row g-3" id="productosContainer">
              <!-- Los productos se cargarán dinámicamente -->
            </div>
          </div>
        </div>

        <!-- Pedido Actual -->
        <div class="card shadow-lg border-0 rounded-4 bg-light mt-4">
          <div class="card-body p-4">
            <h5 class="card-title mb-4">
              <i class="fas fa-shopping-cart me-2"></i>Pedido Actual
            </h5>
            <ul class="list-group mb-3" id="pedidoLista">
              <!-- Los items del pedido se cargarán dinámicamente -->
            </ul>
            <button class="btn btn-success w-100" onclick="confirmarPedido()">
              <i class="fas fa-check me-2"></i>Confirmar Pedido
            </button>
          </div>
        </div>

        <!-- Tokens Activos en Mesas -->
        <div class="card shadow-lg border-0 rounded-4 bg-light mt-4">
          <div class="card-body p-4">
            <h5 class="card-title mb-4">
              <i class="fas fa-key me-2"></i>Tokens Activos en Mesas
            </h5>
            <div id="tokensActivosGlobal"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Observación -->
  <div class="modal fade" id="observacionModal" tabindex="-1" aria-labelledby="observacionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="observacionModalLabel">Agregar Observaciones</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Producto:</label>
            <p id="productoNombreSeleccionado" class="form-control-static"></p>
          </div>
          <div class="mb-3">
            <label class="form-label">Cantidad:</label>
            <p id="productoCantidadSeleccionada" class="form-control-static"></p>
          </div>
          <div class="mb-3">
            <label class="form-label">Precio:</label>
            <p id="productoPrecioSeleccionado" class="form-control-static"></p>
          </div>
          <div class="mb-3">
            <label for="comentarioInput" class="form-label">Observaciones:</label>
            <textarea class="form-control" id="comentarioInput" rows="3" placeholder="Ingrese observaciones del producto..."></textarea>
          </div>
          <input type="hidden" id="productoSeleccionado">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="agregarAlPedido()">Agregar al Pedido</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/appMesero.js"></script>
  <script>
    function generarTokenMesa() {
      const mesaId = document.getElementById('mesaSelect').value;
      if (!mesaId) {
        Swal.fire('Seleccione una mesa', 'Debe seleccionar una mesa para generar el token', 'warning');
        return;
      }
      fetch('../controllers/generar_token.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'mesa_id=' + mesaId
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: 'Token generado',
            html: 'El token para la mesa es: <b>' + data.token + '</b><br>Expira a las: <b>' + (new Date(data.expira).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})) + '</b>',
            icon: 'success'
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      });
    }

    function cargarTokensActivosGlobal() {
      fetch('../controllers/generar_token.php?activos=1')
        .then(res => res.json())
        .then(data => {
          const cont = document.getElementById('tokensActivosGlobal');
          if (data.success && data.tokens.length > 0) {
            let html = '<div class="card mt-2"><div class="card-body p-2"><ul class="list-group">';
            data.tokens.forEach(token => {
              html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                <span><b>${token.token}</b> <span class="badge bg-secondary ms-2">${token.estado_token}</span><br><small class="text-muted">Mesa: ${token.mesa_nombre} (${token.idmesas})<br>Expira: ${(new Date(token.fecha_hora_expiracion)).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small></span>
                <button class="btn btn-sm btn-danger" onclick="cancelarTokenGlobal('${token.token}')"><i class='fas fa-times'></i></button>
              </li>`;
            });
            html += '</ul></div></div>';
            cont.innerHTML = html;
          } else {
            cont.innerHTML = '<div class="alert alert-info">No hay tokens activos actualmente.</div>';
          }
        });
    }

    function cancelarTokenGlobal(token) {
      Swal.fire({
        title: '¿Cancelar token?',
        text: '¿Está seguro de cancelar este token? El usuario ya no podrá usarlo.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('../controllers/generar_token.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'cancelar_token_por_valor=' + encodeURIComponent(token)
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Cancelado', 'El token fue cancelado.', 'success');
              cargarTokensActivosGlobal();
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          });
        }
      });
    }

    // Llamar al cargar la página
    cargarTokensActivosGlobal();
  </script>
</body>
</html>
