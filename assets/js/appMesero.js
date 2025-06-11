const mesas = ["Mesa 1", "Mesa 2", "Mesa 3"]; // simulando DB
const categorias = ["Bebidas", "Postres"];
const productos = {
  Bebidas: [{ id: 1, nombre: "Café Americano" }, { id: 2, nombre: "Capuccino" }],
  Postres: [{ id: 3, nombre: "Brownie" }, { id: 4, nombre: "Cheesecake" }],
};

let pedido = [];

document.addEventListener("DOMContentLoaded", () => {
  cargarOpciones();
});

function cargarOpciones() {
  const mesaSelect = document.getElementById("mesaSelect");
  mesas.forEach(m => mesaSelect.innerHTML += <option>${m}</option>);

  const categoriaSelect = document.getElementById("categoriaSelect");
  categorias.forEach(c => categoriaSelect.innerHTML += <option>${c}</option>);

  categoriaSelect.addEventListener("change", () => mostrarProductos(categoriaSelect.value));
}

function mostrarProductos(categoria) {
  const contenedor = document.getElementById("productosContainer");
  contenedor.innerHTML = "";
  productos[categoria].forEach(prod => {
    contenedor.innerHTML += `
      <div class="col-md-4 mb-3">
        <div class="card card-cafe p-3">
          <h5>${prod.nombre}</h5>
          <button class="btn btn-primary mt-2" onclick="abrirModal(${prod.id}, '${prod.nombre}')">Agregar</button>
        </div>
      </div>`;
  });
}

function abrirModal(id, nombre) {
  document.getElementById("productoSeleccionado").value = id;
  document.getElementById("comentarioInput").value = "";
  new bootstrap.Modal(document.getElementById("observacionModal")).show();
}

function agregarAlPedido() {
  const id = document.getElementById("productoSeleccionado").value;
  const comentario = document.getElementById("comentarioInput").value;
  const categoria = document.getElementById("categoriaSelect").value;
  const producto = productos[categoria].find(p => p.id == id);
  pedido.push({ ...producto, comentario, cantidad: 1 });
  actualizarLista();
  bootstrap.Modal.getInstance(document.getElementById("observacionModal")).hide();
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

function confirmarPedido() {
  const mesa = document.getElementById("mesaSelect").value;
  const pedidosActivos = document.getElementById("pedidosActivos");
  pedidosActivos.innerHTML += <li class="list-group-item">${mesa}: ${pedido.length} productos</li>;
  pedido = [];
  actualizarLista();
  // Aquí se podría marcar la mesa como ocupada en la DB
}