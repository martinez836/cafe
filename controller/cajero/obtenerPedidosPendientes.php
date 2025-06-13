<?php
require_once __DIR__ . '/../../models/consultas.php';
header('Content-Type: application/json');
$consultas = new consultas();
$pedidosPendientes = $consultas->traerPedidosPendinetes();


try {
    if (!$pedidosPendientes || $pedidosPendientes->num_rows === 0) {
        echo json_encode([]);
        exit;
    }

    $agrupado = [];

    while ($row = mysqli_fetch_assoc($pedidosPendientes)) {
        $id = $row['idpedidos'];

        // Si es la primera vez que aparece este pedido, lo creamos
        if (!isset($agrupado[$id])) {
            $agrupado[$id] = [
                'id' => (int)$id,
                'numero' => 'P' . $id,
                'cliente' => $row['nombre_mesa'],
                'mesero' => $row["nombre_usuario"], // puedes agregarlo si tienes el campo
                'hora' => date('g:i a', strtotime($row['fecha_hora_pedido'])),
                'productos' => [],
                'total' => 0
            ];
        }

        // Añadir producto al array de productos
        $agrupado[$id]['productos'][] = [
            'nombre' => $row['nombre_producto'] . ' x' . $row['cantidad_producto']
        ];

        // Sumar subtotal al total
        $agrupado[$id]['total'] += (int)$row['subtotal'];
    }

    // Convertir a array indexado
    $resultado = array_values($agrupado);

    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $th) {
    echo json_encode(['error' => 'Error al procesar pedidos']);
}