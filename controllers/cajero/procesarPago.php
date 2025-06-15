<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../models/consultasCajero.php';

class ProcesarPagoCajero
{
    
    public function procesarPago()
    {
        try {
             
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['numero'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Faltan datos para cambiar el estado del pedido']);
                exit;
            }

            $idpedido = (int)str_replace('P', '', $input['numero']);
            $consultas = new consultasCajero();

            
            $resultado = $consultas->cambiarEstadoPedido($idpedido, 2);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Estado del pedido actualizado correctamente']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado del pedido']);
            }
        } catch (\Throwable $th) {
            //throw $th;
             http_response_code(500);
            echo json_encode(['error' => 'Error al procesar pedidos: ' . $th->getMessage()]);
        }
    }
}

$pagos = new ProcesarPagoCajero();
$pagos->procesarPago();
