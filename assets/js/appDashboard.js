document.addEventListener('DOMContentLoaded', function() {
            // Función para cargar los datos del dashboard
            function loadDashboardData() {
                fetch('../../controllers/admin/dashboard.php?action=get_dashboard_data')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const dashboardData = data.data;

                            // Actualizar tarjetas de resumen
                            document.querySelector('.card.bg-primary h3').textContent = dashboardData.totalPedidos.toLocaleString();
                            document.querySelector('.card.bg-success h3').textContent = `$${dashboardData.ingresosMesActual.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                            document.querySelector('.card.bg-warning h3').textContent = dashboardData.nuevosUsuariosMesActual.toLocaleString();

                            // Actualizar gráfica de Ventas Diarias
                            updateVentasDiariasChart(dashboardData.ventasDiarias.labels, dashboardData.ventasDiarias.data);

                            // Actualizar últimos pedidos
                            const ultimosPedidosList = document.querySelector('#ultimosPedidosList');
                            if (ultimosPedidosList) {
                                ultimosPedidosList.innerHTML = '';
                                if (dashboardData.ultimosPedidos.length > 0) {
                                    dashboardData.ultimosPedidos.forEach(pedido => {
                                        const li = document.createElement('li');
                                        li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                                        let statusClass = '';
                                        if (pedido.status === 'Pendiente') {
                                            statusClass = 'bg-info';
                                        } else if (pedido.status === 'Completado') {
                                            statusClass = 'bg-success';
                                        }
                                        li.innerHTML = `
                                            Pedido #${pedido.id} - ${pedido.table}
                                            <span class="badge ${statusClass} rounded-pill">${pedido.status}</span>
                                        `;
                                        ultimosPedidosList.appendChild(li);
                                    });
                                } else {
                                    ultimosPedidosList.innerHTML = '<li class="list-group-item text-center">No hay pedidos recientes.</li>';
                                }
                            }

                            // Comentarios recientes (si tienes datos para ellos)
                            const comentariosRecientesList = document.querySelector('#comentariosRecientesList');
                            if (comentariosRecientesList) {
                                comentariosRecientesList.innerHTML = '';
                                if (dashboardData.comentariosRecientes.length > 0) {
                                    dashboardData.comentariosRecientes.forEach(comentario => {
                                        const li = document.createElement('li');
                                        li.classList.add('list-group-item');
                                        li.textContent = `"${comentario.texto}" - ${comentario.autor}`;
                                        comentariosRecientesList.appendChild(li);
                                    });
                                } else {
                                    comentariosRecientesList.innerHTML = '<li class="list-group-item text-center">No hay comentarios recientes.</li>';
                                }
                            }

                        } else {
                            console.error('Error al cargar datos del dashboard:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error de red al cargar el dashboard:', error);
                    });
            }

            // Chart para Ventas Diarias (inicialización y actualización)
            const ventasDiariasCtx = document.getElementById('ventasDiariasChart').getContext('2d');
            let ventasDiariasChart = new Chart(ventasDiariasCtx, {
                type: 'line',
                data: {
                    labels: [], // Se llenarán con datos reales
                    datasets: [{
                        label: 'Ventas ($)',
                        data: [], // Se llenarán con datos reales
                        borderColor: '#8B5E3C',
                        backgroundColor: 'rgba(139, 94, 60, 0.2)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: false,
                            text: 'Ventas Diarias'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            function updateVentasDiariasChart(labels, data) {
                ventasDiariasChart.data.labels = labels;
                ventasDiariasChart.data.datasets[0].data = data;
                ventasDiariasChart.update();
            }

            // Cargar datos al iniciar la página
            loadDashboardData();
        });