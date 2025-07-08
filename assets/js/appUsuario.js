document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const modalUsuario = new bootstrap.Modal(document.querySelector('#modalUsuario'));
    const frmUsuario = document.querySelector('#frmUsuario');
    const btnCrearUsuario = document.querySelector('#btnCrearUsuario');
    const usersTableBody = document.getElementById('usersTableBody');
    let nombreUsuario = document.querySelector('#nombre_usuario');
    let emailUsuario = document.querySelector('#email_usuario');
    let contrasenaUsuario = document.querySelector('#contrasena_usuario');
    let idusuario;
    let opcion = ""; // crear o editar

    // Cargar usuarios
    function loadUsers() {
        fetch('../../controllers/admin/usuarios.php?action=get_all_users')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                usersTableBody.innerHTML = '';
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
                usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error al cargar usuarios: ${error.message}</td></tr>`;
            });
    }

    // Cargar roles
    function cargarRoles(idSeleccionado = null) {
        fetch('../../controllers/admin/usuarios.php?action=traer_roles')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const selectRol = document.querySelector('#rolUsuario');
                selectRol.innerHTML = '<option value="">Seleccione un rol</option>';
                if (data.success && data.data.length > 0) {
                    data.data.forEach(role => {
                        const option = document.createElement('option');
                        option.value = role.idrol;
                        option.textContent = `${role.nombre_rol}`;
                        if (idSeleccionado && role.idrol == idSeleccionado) option.selected = true;
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

    // Mostrar modal para crear usuario
    btnCrearUsuario.addEventListener('click', () => {
        document.querySelector('#modalUsuarioTitle').textContent = 'Crear Usuario';
        cargarRoles();
        opcion = "crear";
        // Mostrar campos de contraseña
        contrasenaUsuario.value = '';
        contrasenaUsuario.style.display = '';
        document.querySelector('#lblContrasena').style.display = '';
        frmUsuario.reset();
        modalUsuario.show();
    });

    // Enviar formulario (crear o editar)
    frmUsuario.addEventListener('submit', (e) => {
        e.preventDefault();
        const idRol = document.querySelector('#rolUsuario').value;
        if (opcion === "crear") {
            const formData = new FormData();
            formData.append('nombre_usuario', nombreUsuario.value);
            formData.append('email_usuario', emailUsuario.value);
            formData.append('contrasena_usuario', contrasenaUsuario.value);
            formData.append('rol_idrol', idRol);
            fetch('../../controllers/admin/usuarios.php?action=crear_usuario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario creado exitosamente',
                        showConfirmButton: true,
                        timer: 1500
                    });
                    modalUsuario.hide();
                    loadUsers();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al crear usuario',
                        text: data.message
                    });
                }
            });
        } else if (opcion === "editar") {
            const formData = new FormData();
            formData.append('idusuario', idusuario);
            formData.append('nombre_usuario', nombreUsuario.value);
            formData.append('email_usuario', emailUsuario.value);
            formData.append('rol_idrol', idRol);
            fetch('../../controllers/admin/usuarios.php?action=editar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Usuario editado correctamente.'
                    }).then(() => {
                        modalUsuario.hide();
                        loadUsers();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo editar el usuario.'
                    });
                }
            });
        }
    });

    // Editar o eliminar usuario
    usersTableBody.addEventListener('click', (e) => {
        const botonEliminar = e.target.closest('.btnEliminar');
        const botonEditar = e.target.closest('.btnEditar');
        const tabla = $('#tablaUsuarios').DataTable();

        if (botonEliminar || botonEditar) {
            // Encuentra el tr más cercano al botón
            let tr = $(e.target).closest('tr')[0];

            // Si es una child row, busca la fila principal (la siguiente o anterior con clase 'parent')
            if (tr && tr.classList.contains('child')) {
                // Busca hacia arriba y hacia abajo por si acaso
                let prev = tr.previousElementSibling;
                let next = tr.nextElementSibling;
                while (prev && !prev.classList.contains('parent')) prev = prev.previousElementSibling;
                while (next && !next.classList.contains('parent')) next = next.nextElementSibling;
                tr = prev && prev.classList.contains('parent') ? prev : (next && next.classList.contains('parent') ? next : null);
            }

            if (!tr) {
                Swal.fire('Error', 'No se pudo obtener la información de la fila.', 'error');
                return;
            }

            const row = tabla.row(tr);
            const data = row.data();
            if (!data) {
                console.log('row:', row);
            console.log('data:', data);
            console.log('tr:', tr);
            console.log('tbody HTML:', usersTableBody.innerHTML);
                Swal.fire('Error', 'No se pudo obtener la información de la fila.', 'error');
                return;
            }

            if (botonEliminar) {
                const idUsuario = data[0];
                opcion = "eliminar";
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
                                    'Error!'+idUsuario,
                                    data.message || 'No se pudo eliminar el artículo.',
                                    'error'
                                );
                            }
                        })
                    }
                })
            } else if (botonEditar) {
                idusuario = data[0];
                const nombre = data[1];
                const email = data[2];
                const rol = $(data[3]).data('idrol');
                document.querySelector("#nombre_usuario").value = nombre;
                document.querySelector("#email_usuario").value = email;
                cargarRoles(rol);
                opcion = "editar";
                document.querySelector("#contrasena_usuario").style.display = 'none';
                document.querySelector("#lblContrasena").style.display = "none";
                document.querySelector('#modalUsuarioTitle').textContent = 'Editar Usuario';
                modalUsuario.show();
            }
        }
    });

    // Inicial
    loadUsers();
});
