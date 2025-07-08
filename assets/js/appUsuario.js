const modalUsuario = new bootstrap.Modal(document.querySelector('#modalUsuario'));
const frmUsuario = document.querySelector('#frmUsuario');
const btnCrearUsuario = document.querySelector('#btnCrearUsuario');
const usersTableBody = document.getElementById('usersTableBody');
// traigo los datos del formulario 
let nombreUsuario = document.querySelector('#nombre_usuario');
let emailUsuario = document.querySelector('#email_usuario');
let contrasenaUsuario = document.querySelector('#contrasena_usuario');
let idusuario;

var opcion = ""; // Variable para determinar si es crear o editar


document.addEventListener('DOMContentLoaded', function() {
function loadUsers() {
    fetch('../../controllers/admin/usuarios.php?action=get_all_users')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const usersTableBody = document.getElementById('usersTableBody');
            usersTableBody.innerHTML = ''; // Limpiar la tabla

            if (data.success && data.data.length > 0) {
                data.data.forEach(user => {
                    const row = `
                        <tr>
                            <td>${user.idusuarios}</td>
                            <td>${user.nombre_usuario}</td>
                            <td>${user.email_usuario}</td>
                            <td data-idrol="${user.idrol}">${user.nombre_rol}</td>
                            <td>
                                <button class="btn btn-sm btn-warning me-1 btnEditar"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger btnEliminar"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    usersTableBody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center">No hay usuarios para mostrar.</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error al cargar usuarios:', error);
            const usersTableBody = document.getElementById('usersTableBody');
            usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error al cargar usuarios: ${error.message}</td></tr>`;
        });
    }

    loadUsers(); // Cargar usuarios al cargar la página

function cargarRoles(idSeleccionado = null)
{
    fetch('../../controllers/admin/usuarios.php?action=traer_roles')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const selectRol = document.querySelector('#rolUsuario');
            selectRol.innerHTML = '<option value="">Seleccione un rol</option>'; // Limpiar el select

            if (data.success && data.data.length > 0) {
                data.data.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.idrol;
                    option.textContent = `${role.nombre_rol}`;
                    if(idSeleccionado && role.idrol == idSeleccionado){
                        option.selected = true;
                    }
                    selectRol.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.textContent = 'No hay roles disponibles';
                selectRol.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Error al cargar roles:', error);
        });
}

    btnCrearUsuario.addEventListener('click', () =>
        {
        document.querySelector('#modalUsuarioTitle').textContent = 'Crear Usuario';
        cargarRoles(); // Cargar roles al crear un usuario
        opcion = "crear";
        modalUsuario.show();
        })

    frmUsuario.addEventListener('submit', (e) => {
        e.preventDefault();
        const idRol = document.querySelector('#rolUsuario').value;
        if (opcion === "crear")
            {
                const formData = new FormData();
                formData.append('nombre_usuario', nombreUsuario.value);
                formData.append('email_usuario', emailUsuario.value);
                formData.append('contrasena_usuario', contrasenaUsuario.value);
                formData.append('rol_idrol', idRol);

                fetch('../../controllers/admin/usuarios.php?action=crear_usuario', {
                    method: 'POST',
                    body: formData
                })
                .then((response)=> response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Usuario creado exitosamente',
                            showConfirmButton: true,
                            timer: 1500
                        });
                        modalUsuario.hide();
                        loadUsers(); // Recargar la lista de usuarios
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al crear usuario',
                            text: data.message
                        });
                    }
                })
            }
        else if(opcion === "editar")
            {
                const formData = new FormData();

                formData.append('idusuario',idusuario)
                formData.append('nombre_usuario', nombreUsuario.value);
                formData.append('email_usuario', emailUsuario.value);
                formData.append('rol_idrol', idRol);

                fetch('../../controllers/admin/usuarios.php?action=editar',{
                    method:'POST',
                    body:formData
                })
                .then((response)=>response.json())
                .then((data)=>{
                    if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Artículo editado correctamente.',
                    }).then(() => {
                        modalUsuario.hide();
                        loadUsers(); // Recargar inventario
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

    usersTableBody.addEventListener('click', (e) => {
        const botonEliminar = e.target.closest('.btnEliminar');
        const botonEditar = e.target.closest('.btnEditar');
        if(botonEliminar)
        {
            const fila = botonEliminar.closest('tr')
            const idUsuario = fila.children[0].textContent;
            opcion = "eliminar"
            console.log(idUsuario);

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás deshacer esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result)=>{
                if(result.isConfirmed)
                {
                    const formData = new FormData();
                    formData.append('id',idUsuario);

                    fetch('../../controllers/admin/usuarios.php?action=eliminar',{
                        method:'POST',
                        body:formData
                    })
                    .then((response)=>response.json())
                    .then((data)=>{
                        if (data.success) {
                            Swal.fire(
                                'Eliminado!',
                                'El usuario ha sido eliminado.',
                                'success'
                            );
                            loadUsers(); // Recargar inventario
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message || 'No se pudo eliminar el artículo.',
                                'error'
                            );
                        }
                    })
                }
            })
        }
        else if(botonEditar){
            const fila = botonEditar.closest("tr");

            idusuario = fila.children[0].textContent;
            const nombre = fila.children[1].textContent;
            const email = fila.children[2].textContent;
            const rol = fila.children[3].dataset.idrol;

            console.log(idusuario)
            console.log(rol)

            document.querySelector("#nombre_usuario").value = nombre;
            document.querySelector("#email_usuario").value = email;
            cargarRoles(rol)

            opcion = "editar";
            document.querySelector("#contrasena_usuario").style.display = 'none';
            document.querySelector("#lblContrasena").style.display = "none";
            document.querySelector('#modalUsuarioTitle').textContent = 'Editar Usuario';
            modalUsuario.show();
        }
    })
});