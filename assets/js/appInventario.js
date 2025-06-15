const modalArticulo = new bootstrap.Modal(document.querySelector('#modalArticulo'))
const frmArticulo = document.querySelector('#frmArticulo');
const btnCrearArticulo = document.querySelector('#btnCrearArticulo');
const inventoryTableBody = document.getElementById('inventoryTableBody');

//traigo los datos del formulario
let articulo = document.querySelector('#articulo');
let stock = document.querySelector('#stock');
let idInventario = 0;

var opcion = "crear"


document.addEventListener('DOMContentLoaded', function() {
    function loadInventory() {
        fetch('../../controllers/admin/inventario.php?action=get_all_inventory')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                
                inventoryTableBody.innerHTML = ''; // Limpiar la tabla

                if (data.success && data.data.length > 0) {
                    data.data.forEach(item => {
                        const row = `
                            <tr>
                                <td>${item.idinventario}</td>
                                <td>${item.articulo}</td>
                                <td>${item.stock}</td>
                                <td data-idunidad_medida="${item.idunidad_medida}">${item.nombre} - ${item.abreviatura}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1 btnEditar"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger btnEliminar"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `;
                        inventoryTableBody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    inventoryTableBody.innerHTML = `<tr><td colspan="5" class="text-center">No hay artículos en el inventario para mostrar.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error al cargar inventario:', error);
                const inventoryTableBody = document.getElementById('inventoryTableBody');
                inventoryTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error al cargar inventario: ${error.message}</td></tr>`;
            });
    }

    loadInventory(); // Cargar inventario al cargar la página


    btnCrearArticulo.addEventListener('click', () => {
        document.querySelector('#modalArticuloTitle').textContent = 'Crear Artículo';
        opcion = "crear";
        cargarUnidadesDeMedida(); // Cargar unidades de medida al crear un artículo
        modalArticulo.show();
    })


    frmArticulo.addEventListener('submit', (e) => {
        e.preventDefault();
        const idUnidadMedida = document.querySelector('#unidadDeMedida').value;
        const datos = [
            {
                articulo: articulo.value,
                stock: stock.value,
                estado: 1,
                idunidad_medida: idUnidadMedida
            }
        ]
        if(opcion === "crear")
            {
                const formData = new FormData();
                formData.append('articulo', articulo.value);
                formData.append('stock', stock.value);
                formData.append('unidadMedida', idUnidadMedida);

                fetch('../../controllers/admin/inventario.php?action=crear', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Artículo creado correctamente.',
                        }).then(() => {
                            modalArticulo.hide();
                            loadInventory(); // Recargar inventario
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo crear el artículo.'
                        });
                    }
                })
            }
        else if(opcion === "editar")
        {
            const formData = new FormData();
            formData.append('idInventario', idInventario);
            formData.append('articulo', articulo.value);
            formData.append('stock', stock.value);
            formData.append('unidadMedida', idUnidadMedida);

            fetch('../../controllers/admin/inventario.php?action=editar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Artículo editado correctamente.',
                    }).then(() => {
                        modalArticulo.hide();
                        loadInventory(); // Recargar inventario
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo editar el artículo.'
                    });
                }
            })
        }
    })


    ///////////////////////////////////////

    inventoryTableBody.addEventListener('click', (e) => {
        const boton = e.target.closest('.btnEditar');
        const botonEliminar = e.target.closest('.btnEliminar');
        if (botonEliminar) {
            const fila = botonEliminar.closest("tr");
            const idInventario = fila.children[0].textContent;
            opcion = "eliminar";

            console.log(idInventario);
            console.log(opcion);

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás deshacer esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('idInventario', idInventario);

                    fetch('../../controllers/admin/inventario.php?action=eliminar', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Eliminado!',
                                'El artículo ha sido eliminado.',
                                'success'
                            );
                            loadInventory(); // Recargar inventario
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message || 'No se pudo eliminar el artículo.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
        if (boton) {
            const fila = boton.closest("tr");

            idInventario = fila.children[0].textContent;
            const articulo = fila.children[1].textContent;
            const stock = fila.children[2].textContent;
            const unidadMedida = fila.children[3].textContent;
            const idUnidadMedida = fila.children[3].dataset.idunidad_medida;

            // Aquí puedes rellenar los campos del modal con los valores obtenidos
            document.querySelector('#articulo').value = articulo;
            document.querySelector('#stock').value = stock;
            // Si tienes un select para unidad de medida, selecciona la opción correspondiente
            cargarUnidadesDeMedida(idUnidadMedida); // Cargar unidades de medida y seleccionar la correspondiente

            opcion = "editar";
            document.querySelector('#modalArticuloTitle').textContent = 'Editar Artículo';
            modalArticulo.show();
        }
    })
});

function cargarUnidadesDeMedida(idSeleccionado = null) {
    fetch('../../controllers/admin/inventario.php?action=traer_unidad_medida')
        .then(response => response.json())
        .then(datos => {
            const select = document.querySelector('#unidadDeMedida');
            select.innerHTML = '<option value="">Seleccione una unidad</option>';
            if (datos.success && datos.data.length > 0) {
                datos.data.forEach(unidad => {
                    const option = document.createElement('option');
                    option.value = unidad.idunidad_medida;
                    option.textContent = `${unidad.nombre} - ${unidad.abreviatura}`;
                    if (idSeleccionado && unidad.idunidad_medida == idSeleccionado) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">No hay unidades disponibles</option>';
            }
        });
}