let pedido = [];

// funcion para cargar productos al seleccionar una categoria de productos
document.addEventListener("DOMContentLoaded", function () {
  const select = document.querySelector("#categoriaSelect");
  const contenedor = document.querySelector("#productosContainer");

  select.addEventListener("change", function () {
    const idcategorias = select.value;

    if (!idcategorias) {
      contenedor.innerHTML = "";
      return;
    }

    fetch("../controllers/cargar_productos.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "idcategoria=" + encodeURIComponent(idcategorias),
    })
      .then((response) => response.text())
      .then((data) => {
        contenedor.innerHTML = data;
      })
      .catch((error) => {
        contenedor.innerHTML = "<p>Error al cargar productos.</p>";
        console.error(error);
      });
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

function agregarAlPedido() {
  const id = document.getElementById("productoSeleccionado").value;
  const comentario = document.getElementById("comentarioInput").value.trim();
  const precio = parseFloat(document.getElementById("productoSeleccionado").getAttribute("data-precio")); // ← aquí

  const cards = document.querySelectorAll(`#productosContainer .card`);
  let cantidad = 0;
  let nombre = "";

  cards.forEach(card => {
    const button = card.querySelector("button");
    if (button && button.onclick.toString().includes(`abrirModal(this, ${id},`)) {
      const input = card.querySelector("input[type='number']");
      cantidad = parseInt(input.value);
      nombre = card.querySelector("h5").textContent.trim();
    }
  });

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
      precio: precio // ← guardamos el precio real
    });
  }

  actualizarLista();
  bootstrap.Modal.getInstance(document.getElementById("observacionModal")).hide();
}




// funcion para agregar productos al pedido
function abrirModal(button, id_producto, nombre_producto) {
  const card = button.closest(".card");
  const inputCantidad = card.querySelector("input[type='number']");
  const cantidad = parseInt(inputCantidad.value);
  const precio = parseFloat(button.getAttribute("data-precio")); // ← obtenemos el precio

  if (!cantidad || cantidad <= 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Cantidad inválida',
      text: 'Por favor, ingrese una cantidad válida.',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  // Guardar precio para usarlo luego
  document.getElementById("productoSeleccionado").value = id_producto;
  document.getElementById("comentarioInput").value = "";
  document.getElementById("productoSeleccionado").setAttribute("data-precio", precio); // ← guardamos en un atributo

  new bootstrap.Modal(document.getElementById("observacionModal")).show();
  console.log(`Producto seleccionado: ${id_producto}, Cantidad: ${cantidad}, Precio: ${precio}`);
}



function actualizarLista() {
  const lista = document.getElementById("pedidoLista");
  lista.innerHTML = "";
  pedido.forEach((item, index) => {
    lista.innerHTML += `
      <li class="list-group-item d-flex justify-content-between align-items-center">
        ${item.nombre} (${item.comentario || "sin obs."}) x${item.cantidad}
        <div>
          <button class="btn btn-sm btn-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
          <button class="btn btn-sm btn-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
          <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">x</button>
        </div>
      </li>`;
  });
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

let pedidosPorMesa = {}; // Almacena pedidos activos por mesa

function confirmarPedido() {
  const mesa = document.getElementById("mesaSelect").value;
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

  // Guardar el pedido por mesa
  if (!pedidosPorMesa[mesa]) pedidosPorMesa[mesa] = [];
  pedidosPorMesa[mesa].push([...pedido]); // Copia por valor

  // 🔒 Deshabilitar la mesa seleccionada
  const mesaSelect = document.getElementById("mesaSelect");
  const option = mesaSelect.querySelector(`option[value="${mesa}"]`);
  if (option) {
    option.disabled = true;
  }

  // Mostrar en pedidos activos
  const pedidosActivos = document.getElementById("pedidosActivos");
  const item = document.createElement("li");
  item.className = "list-group-item d-flex justify-content-between align-items-center";
  item.innerHTML = `
    <span><i class="fas fa-chair me-2"></i>${mesa}</span>
    <button class="btn btn-sm btn-info" onclick="verDetallePedido('${mesa}', ${pedidosPorMesa[mesa].length - 1})">
      Ver detalle
    </button>
  `;
  pedidosActivos.appendChild(item);

  // Limpiar estado
  pedido = [];
  actualizarLista();
  Swal.fire({
    icon: 'success',
    title: 'Pedido registrado',
    text: `Pedido guardado para la mesa ${mesa}`,
  });
}


function verDetallePedido(mesa, index) {
  const detalleLista = document.getElementById("detallePedidoLista");
  const totalSpan = document.getElementById("detallePedidoTotal");
  detalleLista.innerHTML = "";

  const pedidoDetalle = pedidosPorMesa[mesa][index];
  let total = 0;

  pedidoDetalle.forEach(item => {
  // Calcular subtotal para cada producto
  const precioUnitario = item.precio;
  const subtotal = precioUnitario * item.cantidad;
  total += subtotal;

    detalleLista.innerHTML += `
      <li class="list-group-item d-flex justify-content-between">
        <div>
          <strong>${item.nombre}</strong> x${item.cantidad}
          <br><small class="text-muted">${item.comentario || 'sin obs.'}</small>
        </div>
        <span>$${subtotal.toLocaleString()}</span>
      </li>`;
  });

  totalSpan.textContent = `$${total.toLocaleString()}`;
  new bootstrap.Modal(document.getElementById("detallePedidoModal")).show();
}

// Deshabilitar la mesa en el <select>
const mesaSelect = document.getElementById("mesaSelect");
const option = mesaSelect.querySelector(`option[value="${mesa}"]`);
if (option) {
  option.disabled = true;
}



