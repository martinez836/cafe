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
    const tablaUsuarios = $('#tablaUsuarios').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    function loadUsers() {
        fetch('../../controllers/admin/usuarios.php?action=get_all_users')
            .then(response => response.json())
            .then(data => {
                tablaUsuarios.clear(); // Limpia la tabla de DataTables

                if (data.success && data.data.length > 0) {
                    data.data.forEach(user => {
                        // Determinar el color del badge según el estado
                        const estadoClass = user.estados_idestados == 5 ? 'badge bg-success' : 'badge bg-danger';
                        
                        tablaUsuarios.row.add([
                            user.idusuarios,
                            user.nombre_usuario,
                            user.email_usuario,
                            `<span data-idrol="${user.idrol}">${user.nombre_rol}</span>`,
                            `<span class="${estadoClass}" data-idestado="${user.estados_idestados}">${user.estado}</span>`,
                            `<button class="btn btn-sm btn-warning me-1 btnEditar"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger btnEliminar"><i class="fas fa-trash"></i></button>`
                        ]);
                    });
                }
                tablaUsuarios.draw(); // Redibuja la tabla
            })
            .catch(error => {
                tablaUsuarios.clear();
                tablaUsuarios.row.add([
                    '',
                    '',
                    `<span class="text-danger" colspan="6">Error al cargar usuarios: ${error.message}</span>`,
                    '',
                    '',
                    ''
                ]).draw();
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
        .catch(error => showSwalError('Error al cargar roles.'));
}

function cargarEstados(idSeleccionado = null)
{
    fetch('../../controllers/admin/usuarios.php?action=traer_estados')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const selectEstado = document.querySelector('#estadoUsuario');
            selectEstado.innerHTML = '<option value="">Seleccione un estado</option>'; // Limpiar el select

            if (data.success && data.data.length > 0) {
                data.data.forEach(estado => {
                    const option = document.createElement('option');
                    option.value = estado.idestados;
                    option.textContent = `${estado.estado}`;
                    if(idSeleccionado && estado.idestados == idSeleccionado){
                        option.selected = true;
                    }
                    selectEstado.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.textContent = 'No hay estados disponibles';
                selectEstado.appendChild(option);
            }
        })
        .catch(error => showSwalError('Error al cargar estados.'));
}

    btnCrearUsuario.addEventListener('click', () =>
        {
        document.querySelector('#modalUsuarioTitle').textContent = 'Crear Usuario';
        cargarRoles(); // Cargar roles al crear un usuario
        cargarEstados(); // Cargar estados al crear un usuario
        opcion = "crear";
        modalUsuario.show();
        })

    frmUsuario.addEventListener('submit', (e) => {
        e.preventDefault();
        const idRol = document.querySelector('#rolUsuario').value;
        const idEstado = document.querySelector('#estadoUsuario').value;
        
        if (opcion === "crear")
            {
                const formData = new FormData();
                formData.append('nombre_usuario', nombreUsuario.value);
                formData.append('contrasena_usuario', contrasenaUsuario.value);
                formData.append('email_usuario', emailUsuario.value);
                formData.append('estado_idestado', idEstado);
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
                formData.append('estado_idestado', idEstado);

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
                        text: 'Usuario editado correctamente.',
                    }).then(() => {
                        modalUsuario.hide();
                        loadUsers(); // Recargar inventario
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo editar el usuario.'
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
            const estado = fila.children[4].dataset.idestado;

            console.log(idusuario)
            console.log(rol)
            console.log(estado)

            document.querySelector("#nombre_usuario").value = nombre;
            document.querySelector("#email_usuario").value = email;
            cargarRoles(rol)
            cargarEstados(estado)

            opcion = "editar";
            document.querySelector("#contrasena_usuario").style.display = 'none';
            document.querySelector("#lblContrasena").style.display = "none";
            document.querySelector('#modalUsuarioTitle').textContent = 'Editar Usuario';
            modalUsuario.show();
        }
    })
});

function showSwalError(msg) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: msg || 'Ocurrió un error.',
        confirmButtonText: 'Aceptar'
    });
}