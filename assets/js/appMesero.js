let pedido = [];

// funcion para cargar productos al seleccionar una categoria de productos
document.addEventListener("DOMContentLoaded", function () {
  const select = document.querySelector("#categoriaSelect");
  const contenedor = document.querySelector("#productosContainer");
  const mesaSelect = document.querySelector("#mesaSelect");

  // Validar que se haya seleccionado una mesa antes de cargar productos
  select.addEventListener("change", function () {
    if (!mesaSelect.value) {
      Swal.fire({
        icon: 'warning',
        title: 'Seleccione una mesa',
        text: 'Por favor, seleccione una mesa antes de ver los productos.',
      });
      select.value = '';
      return;
    }

    const idcategorias = select.value;
    if (!idcategorias) {
      contenedor.innerHTML = "";
      return;
    }

    // Mostrar loading
    contenedor.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

    fetch("../controllers/cargar_productos.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "idcategorias=" + encodeURIComponent(idcategorias),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          contenedor.innerHTML = data.html;
          // Agregar event listeners a los botones de agregar
          document.querySelectorAll('#productosContainer .btn-primary').forEach(btn => {
            btn.addEventListener('click', function() {
              const card = this.closest('.card');
              const id = card.getAttribute('data-id');
              const nombre = card.querySelector('h5').textContent.trim();
              const precio = parseFloat(this.getAttribute('data-precio'));
              const input = card.querySelector('input[type=number]');
              const cantidad = parseInt(input.value);

              if (!cantidad || cantidad <= 0) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Cantidad inválida',
                  text: 'Por favor, ingrese una cantidad válida.',
                  confirmButtonText: 'Entendido'
                });
                return;
              }

              // Actualizar el modal con los datos del producto
              document.getElementById("productoSeleccionado").value = id;
              document.getElementById("productoNombreSeleccionado").textContent = nombre;
              document.getElementById("productoCantidadSeleccionada").textContent = cantidad;
              document.getElementById("productoPrecioSeleccionado").textContent = `$${precio.toFixed(2)}`;
              document.getElementById("comentarioInput").value = "";
              document.getElementById("productoSeleccionado").setAttribute("data-precio", precio);
              document.getElementById("productoSeleccionado").setAttribute("data-nombre", nombre);
              document.getElementById("productoSeleccionado").setAttribute("data-cantidad", cantidad);

              // Mostrar el modal
              const modalElement = document.getElementById("observacionModal");
              const modal = new bootstrap.Modal(modalElement);
              modal.show();
            });
          });
        } else {
          contenedor.innerHTML = data.html;
          console.error('Error:', data.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
        contenedor.innerHTML = `
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            Error al cargar productos. Por favor, intente nuevamente.
          </div>`;
      });
  });

  // Validar selección de mesa
  mesaSelect.addEventListener("change", function() {
    if (select.value) {
      select.dispatchEvent(new Event('change'));
    }
  });
});

// funcion para buscar productos desde el input buscador
document.addEventListener("DOMContentLoaded", function () {
  const buscador = document.querySelector("#buscadorProductos");

  buscador.addEventListener("input", function () {
    const filtro = buscador.value.toLowerCase();
    const productos = document.querySelectorAll("#productosContainer .card");

    productos.forEach(card => {
      const nombre = card.querySelector("h5").textContent.toLowerCase();
      card.parentElement.style.display = nombre.includes(filtro) ? "" : "none";
    });
  });
});

function abrirModal(button, id_producto, nombre_producto) {
  const card = button.closest(".card");
  const inputCantidad = card.querySelector("input[type='number']");
  const cantidad = parseInt(inputCantidad.value);
  const precio = parseFloat(button.getAttribute("data-precio"));

  if (!cantidad || cantidad <= 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Cantidad inválida',
      text: 'Por favor, ingrese una cantidad válida.',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  // Actualizar el modal con los datos del producto
  document.getElementById("productoSeleccionado").value = id_producto;
  document.getElementById("productoNombreSeleccionado").textContent = nombre_producto;
  document.getElementById("productoCantidadSeleccionada").textContent = cantidad;
  document.getElementById("productoPrecioSeleccionado").textContent = `$${precio.toFixed(2)}`;
  document.getElementById("comentarioInput").value = "";
  document.getElementById("productoSeleccionado").setAttribute("data-precio", precio);
  document.getElementById("productoSeleccionado").setAttribute("data-nombre", nombre_producto);
  document.getElementById("productoSeleccionado").setAttribute("data-cantidad", cantidad);

  // Mostrar el modal
  new bootstrap.Modal(document.getElementById("observacionModal")).show();
}

function agregarAlPedido() {
  const id = document.getElementById("productoSeleccionado").value;
  const comentario = document.getElementById("comentarioInput").value.trim();
  const precio = parseFloat(document.getElementById("productoSeleccionado").getAttribute("data-precio"));
  const nombre = document.getElementById("productoSeleccionado").getAttribute("data-nombre");
  const cantidad = parseInt(document.getElementById("productoSeleccionado").getAttribute("data-cantidad"));

  if (!cantidad || cantidad <= 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Cantidad inválida',
      text: 'Por favor, ingrese una cantidad válida.',
    });
    return;
  }

  const existente = pedido.find(p => p.id === id && p.comentario === comentario);

  if (existente) {
    existente.cantidad += cantidad;
  } else {
    pedido.push({
      id: id,
      nombre: nombre,
      cantidad: cantidad,
      comentario: comentario,
      precio: precio
    });
  }

  actualizarLista();
  bootstrap.Modal.getInstance(document.getElementById("observacionModal")).hide();
}

function actualizarLista() {
  const lista = document.getElementById("pedidoLista");
  lista.innerHTML = "";
  let total = 0;

  pedido.forEach((item, index) => {
    const subtotal = item.precio * item.cantidad;
    total += subtotal;

    lista.innerHTML += `
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <strong>${item.nombre}</strong>
          <br>
          <small class="text-muted">${item.comentario || "sin obs."}</small>
          <br>
          <small>$${item.precio.toFixed(2)} x ${item.cantidad}</small>
        </div>
        <div class="text-end">
          <div class="mb-2">$${subtotal.toFixed(2)}</div>
          <div>
            <button class="btn btn-sm btn-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
            <button class="btn btn-sm btn-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
            <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">x</button>
          </div>
        </div>
      </li>`;
  });

  // Agregar el total al final de la lista
  lista.innerHTML += `
    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
      <strong>Total</strong>
      <strong>$${total.toFixed(2)}</strong>
    </li>`;
}

function cambiarCantidad(index, delta) {
  pedido[index].cantidad += delta;
  if (pedido[index].cantidad <= 0) eliminarProducto(index);
  else actualizarLista();
}

function eliminarProducto(index) {
  pedido.splice(index, 1);
  actualizarLista();
}

function confirmarPedido() {
  const mesa = document.getElementById("mesaSelect").value;
  const mesaSelect = document.getElementById("mesaSelect");
  const mesaNombre = mesaSelect.options[mesaSelect.selectedIndex].text;

  if (!mesa) {
    Swal.fire({
      icon: 'warning',
      title: 'Mesa no seleccionada',
      text: 'Por favor, seleccione una mesa antes de confirmar el pedido.',
    });
    return;
  }

  if (pedido.length === 0) {
    Swal.fire({
      icon: 'info',
      title: 'Sin productos',
      text: 'Agrega al menos un producto al pedido.',
    });
    return;
  }

  // Calcular el total del pedido
  const total = pedido.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);

  fetch("../controllers/confirmar_pedido.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      mesa_id: parseInt(mesa),
      productos: pedido,
      total: total
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Pedido registrado',
          text: data.message,
        });
        pedido = [];
        actualizarLista();
        // Limpiar los inputs seleccion de mesa y categoria
        document.getElementById("mesaSelect").value = "";
        document.getElementById("categoriaSelect").value = "";
        document.getElementById("productosContainer").innerHTML = "";
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message,
        });
      }
    })
    .catch((error) => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo guardar el pedido.',
      });
      console.error(error);
    });
}

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
  })
  .catch(error => {
    console.error('Error al generar token:', error);
    Swal.fire('Error', 'No se pudo generar el token.', 'error');
  });
}

// Exportar funciones globales para el HTML
document.addEventListener("DOMContentLoaded", function () {
  window.confirmarPedido = confirmarPedido;
  window.generarTokenMesa = generarTokenMesa;
  window.cargarTokensActivosGlobal = cargarTokensActivosGlobal;
  window.cancelarTokenGlobal = cancelarTokenGlobal;
  
  // Cargar tokens activos al iniciar la página
  cargarTokensActivosGlobal();
});

// Función para cargar tokens activos globalmente
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
    })
    .catch(error => {
      console.error('Error al cargar tokens activos:', error);
      const cont = document.getElementById('tokensActivosGlobal');
      cont.innerHTML = '<div class="alert alert-danger">Error al cargar tokens activos.</div>';
    });
}

// Función para cancelar token global
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
      })
      .catch(error => {
        console.error('Error al cancelar token:', error);
        Swal.fire('Error', 'No se pudo cancelar el token.', 'error');
      });
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const correo = document.getElementById('correo').value;
      const contrasena = document.getElementById('contrasena').value;
      try {
        const response = await fetch('../controllers/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ correo, contrasena })
        });
        const data = await response.json();
        if (data.success) {
          window.location.href = 'mesero.php';
        } else {
          Swal.fire('Error', data.message || 'Credenciales incorrectas', 'error');
        }
      } catch (err) {
        Swal.fire('Error', 'Error de conexión', 'error');
      }
    });
  }
});