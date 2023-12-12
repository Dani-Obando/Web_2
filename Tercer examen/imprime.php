<?php
session_start();

$method = $_SERVER['REQUEST_METHOD'];
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

// Verificar si la variable de sesión está definida
if (!isset($_SESSION['post_data_numeric'])) {
    $_SESSION['post_data_numeric'] = [];
}

// Obtener la referencia a la variable de sesión para facilitar el acceso
$post_data_numeric =& $_SESSION['post_data_numeric'];

switch ($method) {
    case 'GET':
        if ($request[0] == 'estudiante') {
            include_once("index.php");
        } elseif ($request[0] == 'estadistica') {
            // Verificar si hay datos numéricos disponibles
            if (!empty($post_data_numeric)) {
                // Realizar el cálculo de estadísticas
                $nValores = count($post_data_numeric);
                $suma = array_sum($post_data_numeric);
                $promedio = $nValores > 0 ? $suma / $nValores : 0;

                // Obtener el mayor y el menor de forma correcta
                $mayor = null;
                $menor = null;

                foreach ($post_data_numeric as $valor) {
                    // Inicializar $mayor y $menor con el primer valor
                    if ($mayor === null) {
                        $mayor = $valor;
                        $menor = $valor;
                    } else {
                        // Actualizar $mayor y $menor según sea necesario
                        $mayor = max($mayor, $valor);
                        $menor = min($menor, $valor);
                    }
                }

                // Construir la respuesta con estadísticas
                $response_data["estadisticas"]["mayor"] = $mayor;
                $response_data["estadisticas"]["menor"] = $menor;
                $response_data["estadisticas"]["nValores"] = $nValores;
                $response_data["estadisticas"]["promedio"] = $promedio;
                $response_data["estadisticas"]["suma"] = $suma;
                $response["status"] = 200;
                $response["status_message"] = "OK";
                $response["data"] = $response_data;

                deliver_response(200, "OK", $response);
            } else {
                deliver_response(400, "Bad Request", "No numeric data provided for statistics");
            }
        } else {
            deliver_response(404, "Not Found", "Endpoint not found");
        }
        break;
    case 'POST':
        if ($request[0] == 'datos') {
            // Leer el contenido del cuerpo de la solicitud
            $post_data = json_decode(file_get_contents("php://input"), true);

            // Filtrar y almacenar solo datos numéricos
            $numeric_values = array_filter($post_data, 'is_numeric');
            $post_data_numeric = array_merge($post_data_numeric, $numeric_values);

            // Verificar si se proporcionaron datos numéricos en el POST
            if (!empty($numeric_values)) {
                // Construir la respuesta
                $response_data["datos"]["nValores"] = count($numeric_values);
                $response["status"] = 201;
                $response["status_message"] = "Numeric Data created";
                $response["data"] = $response_data;

                deliver_response(201, "Numeric Data created", $response);
            } else {
                deliver_response(400, "Bad Request", "No numeric data provided");
            }
        } elseif ($request[0] == 'elevar') {
            // Verificar si hay datos numéricos disponibles para elevar a la potencia
            if (!empty($post_data_numeric)) {
                // Obtener la potencia especificada (o utilizar 2 por defecto)
                $power = isset($request[1]) && is_numeric($request[1]) ? (int)$request[1] : 2;

                // Elevar a la potencia especificada
                $powered_values = array_map(function ($valor) use ($power) {
                    return pow($valor, $power);
                }, $post_data_numeric);

                // Construir la respuesta con los valores elevados a la potencia
                $response_data["valores_elevados"] = $powered_values;
                $response["status"] = 200;
                $response["status_message"] = "OK";
                $response["data"] = $response_data;

                deliver_response(200, "OK", $response);
            } else {
                deliver_response(400, "Bad Request", "No numeric data available");
            }
        } elseif ($request[0] == 'limpiar') {
            // Limpiar los datos numéricos
            $_SESSION['post_data_numeric'] = [];
            $response["status"] = 200;
            $response["status_message"] = "OK";
            $response["data"] = "Numeric data cleared";

            deliver_response(200, "OK", $response);
        } else {
            deliver_response(404, "Not Found", "Endpoint not found");
        }
        break;
    default:
        deliver_response(405, "Method not allowed", "");
        break;
}

function deliver_response($status, $status_message, $data)
{
    if (!headers_sent()) {
        header("HTTP/1.1 $status $status_message");
        header('Content-Type: application/json');
    }

    $response["status"] = $status;
    $response["status_message"] = $status_message;
    $response["author"] = "Daniel";

    // Si existe una clave "valores_elevados", se agrega a la respuesta
    if (isset($data["valores_elevados"])) {
        $response["data"]["valores_elevados"] = $data["valores_elevados"];
    } else {
        $response["data"] = $data;
    }

    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo $json_response;
}
?>
