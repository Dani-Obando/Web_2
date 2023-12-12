<?php
$proceso = false;
$minEdad = PHP_INT_MAX;
$maxEdad = PHP_INT_MIN;

if (isset($_POST["oc_Control"])) {
    // Procesa los datos generales del archivo recibido.
    $archivo1 = $_FILES["txtArchi1"]["tmp_name"];
    $tamanio1 = $_FILES["txtArchi1"]["size"];
    $tipo1 = $_FILES["txtArchi1"]["type"];
    $nombre1 = $_FILES["txtArchi1"]["name"];

    // Valida que el tamaño del archivo 1 sea mayor que 0.
    if ($tamanio1 > 0) {
        // Procesa el contenido del archivo recibido.
        $archi1 = fopen($archivo1, "rb");
        $encabezados1 = explode(';', fgets($archi1));

        $contenido1 = array();
        $posi1 = 0;
        while ($linea1 = fgets($archi1)) {
            $contenido1[$posi1++] = explode(';', $linea1);
        }

        // Cierra el archivo 1.
        fclose($archi1);

        // Cambia el estado del proceso.
        $proceso = true;

        // Calcular el rango de edades
        $edades = array(); // Inicializa un array para almacenar las edades válidas
        foreach ($contenido1 as $registro) {
            $edadString = isset($registro[3]) ? $registro[3] : null;
            // Extrae solo los dígitos numéricos
            $edadString = preg_replace('/[^0-9]/', '', $edadString);
            $edad = is_numeric($edadString) ? intval($edadString) : null;
            if ($edad !== null) {
                $edades[] = $edad;
            }
        }
    }

    if (!empty($edades)) {
        $minEdad = min($edades);
        $maxEdad = max($edades);
    } else {
        $minEdad = "N/A"; // Si no se encontraron edades válidas
        $maxEdad = "N/A";
    }

    $proceso2 = false;
    $archivo2 = $_FILES["txtArchi2"]["tmp_name"];
    $tamanio2 = $_FILES["txtArchi2"]["size"];
    $tipo2 = $_FILES["txtArchi2"]["type"];
    $nombre2 = $_FILES["txtArchi2"]["name"];

    // Valida que el tamaño del archivo 2 sea mayor que 0.
    if ($tamanio2 > 0) {
        // Procesa el contenido del archivo 2 recibido.
        $archi2 = fopen($archivo2, "rb");
        $encabezados2 = explode(';', fgets($archi2));

        $contenido2 = array();
        $posi2 = 0;
        while ($linea2 = fgets($archi2)) {
            $contenido2[$posi2++] = explode(';', $linea2);
        }

        // Cierra el archivo 2.
        fclose($archi2);

        // Cambia el estado del proceso.
        $proceso2 = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include_once("segmentos/encabe.inc");
    ?>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
    <title>Proceso de datos</title>
</head>

<style>
    th{
        background-color: #05075f ;
        font: 150%;
        color: white;
    }
</style>

<body class="container">
    <header class="row">
        <?php
        include_once("segmentos/menu.inc");
        ?>
    </header>

    <main class="row">
        <div class="linea_sep">
            <h3>Procesando archivo.</h3>
            <br>
            <?php
            if (!$proceso) {
                // En caso que el archivo .csv no pudiese ser procesado
                echo '<div class="alert alert-danger" role="alert">';
                echo '  El archivo no puede ser procesado, verifique sus datos.....!';
                echo '</div>';
            } else {

                $numeroDeEntradas1 = 0;
                $numeroDeEntradas2 = 0;

                // Cuenta las entradas del archivo 1
                if ($proceso) {
                    foreach ($contenido1 as $fila) {
                        $numeroDeEntradas1++;
                    }
                }

                // Cuenta las entradas del archivo 2
                if ($proceso2) {
                    foreach ($contenido2 as $fila) {
                        $numeroDeEntradas2++;
                    }
                }
                // En caso que el archivo1 .csv pudiese ser procesado
                if ($proceso) {
                    echo "<h4>Datos Generales de los Archivos:</h4>";
                    echo "<table class='table table-bordered table-hover'>";
                    echo "<tr><td>ARCHIVO</td><td>TIPO</td><td>PESO</td><td>ENTRADAS</td></tr>";

                    // Datos del archivo 1
                    echo "<tr><td>$nombre1</td><td>$tipo1</td><td>" . number_format((($tamanio1) / 1024) / 1024, 2, '.', ',') . " MBs</td><td>$numeroDeEntradas1 entradas</td></tr>";

                    // Datos del archivo 2
                    echo "<tr><td>$nombre2</td><td>$tipo2</td><td>" . number_format((($tamanio2) / 1024) / 1024, 2, '.', ',') . " MBs</td><td>$numeroDeEntradas2 entradas</td></tr>";

                    echo "</table>";
                }

                $campos = [
                    "ID",
                    "GENERO",
                    "ESTADO CIVIL",
                    "EDAD",
                    "ESCOLARIDAD",
                    "SALARIO",
                    "PROVINCIA",
                    "PARTIDO POLITICO",
                    "ORIENTACION SEXUAL"
                ];

                $minEdad = PHP_INT_MAX;
                $maxEdad = PHP_INT_MIN;

                echo "<h4>Clasificación de Campos:</h4>";
                echo "<table class='table table-bordered table-hover' id='tblCampos'>";
                echo "<thead><tr><th>CAMPO</th><th>TIPO</th><th>USO</th><th>VALOR</th></tr></thead>";
                echo "<tbody>";

                foreach ($campos as $indice => $campo) {
                    $valor = isset($contenido1[0][$indice]) ? explode('&', $contenido1[0][$indice])[3] : "";
                    $tipo = is_numeric($valor) ? (is_int($valor) ? "entero" : "decimal") : "cadena";
                    $uso = in_array($campo, ["ID", "EDAD", "SALARIO"]) ? "cuantitativo" : "cualitativo";

                    echo "<tr><td>$campo</td><td>$tipo</td><td>$uso</td><td>$valor</td></tr>";
                }
                echo "</tbody></table>";

                if ($proceso) {
                    echo "<h4>Tabla de Escolaridad:</h4>";
                    echo "<table class='table table-bordered table-hover' id='tablaEscolaridad'>";

                    echo "<tbody>";
                
                    $escolaridadAgregadas = array();
                    $escolaridadOrdenadas = array();
                    $escolaridadFrecuencia = array();
                    $totalEscolaridad = 0;
                
                    foreach ($contenido1 as $linea) {
                        $linea = implode('&', $linea);
                        $datos = explode('&', $linea);
                
                        $escolaridad = isset($datos[4]) ? $datos[4] : "";
                        $valorAbsoluto = 1;
                        $valorPorcentual = "Valor Porcentual";
                
                        if (!in_array($escolaridad, $escolaridadAgregadas)) {
                            $escolaridadAgregadas[] = $escolaridad;
                            $escolaridadOrdenadas[] = array('escolaridad' => $escolaridad, 'longitud' => strlen($escolaridad));
                        }
                
                        if (isset($escolaridadFrecuencia[$escolaridad])) {
                            $escolaridadFrecuencia[$escolaridad]++;
                        } else {
                            $escolaridadFrecuencia[$escolaridad] = 1;
                        }
                
                        $totalEscolaridad += $valorAbsoluto;
                    }
                
                    usort($escolaridadOrdenadas, function ($a, $b) {
                        return $b['longitud'] - $a['longitud'];
                    });
                
                    echo "<tr><th>Variable</th>";
                    foreach ($escolaridadOrdenadas as $escolaridadOrdenada) {
                        $escolaridad = $escolaridadOrdenada['escolaridad'];
                        echo "<th>$escolaridad</th>";
                    }
                    echo "<th>Total</th></tr>";
                
                    echo "<tr><td>Valores Absolutos</td>";
                    $valoresTotales = array();
                    foreach ($escolaridadOrdenadas as $escolaridadOrdenada) {
                        $escolaridad = $escolaridadOrdenada['escolaridad'];
                        $valorAbsoluto = isset($escolaridadFrecuencia[$escolaridad]) ? $escolaridadFrecuencia[$escolaridad] : 0;
                        echo "<td>$valorAbsoluto</td>";
                        $valoresTotales[] = $valorAbsoluto;
                    }
                    $totalEscolaridad = array_sum($valoresTotales);
                    echo "<td>$totalEscolaridad</td></tr>";
                
                    echo "<tr><td>Porcentaje</td>";
                    foreach ($escolaridadOrdenadas as $escolaridadOrdenada) {
                        $escolaridad = $escolaridadOrdenada['escolaridad'];
                        $valorAbsoluto = isset($escolaridadFrecuencia[$escolaridad]) ? $escolaridadFrecuencia[$escolaridad] : 0;
                        $porcentaje = ($valorAbsoluto / $totalEscolaridad) * 100;
                        echo "<td>" . number_format($porcentaje, 2) . "%</td>";
                    }
                    echo "<td>100%</td></tr>";
                
                    echo "</tbody></table>";
                
                    // Tabla de Orientación Sexual
                    echo "<h4>Tabla de Orientación Sexual:</h4>";
                    echo "<table class='table table-bordered table-hover' id='tablaOrientacionSexual'>";
                    echo "<tbody>";
                
                    $orientacionSexualAgregadas = array();
                    $orientacionSexualOrdenadas = array();
                    $orientacionSexualFrecuencia = array();
                    $totalOrientacionSexual = 0;
                
                    foreach ($contenido1 as $linea) {
                        $linea = implode('&', $linea);
                        $datos = explode('&', $linea);
                
                        $orientacionSexual = isset($datos[8]) ? $datos[8] : "";
                        $valorAbsoluto = 1;
                        $valorPorcentual = "Valor Porcentual";
                
                        if (!in_array($orientacionSexual, $orientacionSexualAgregadas)) {
                            $orientacionSexualAgregadas[] = $orientacionSexual;
                            $orientacionSexualOrdenadas[] = array('orientacionSexual' => $orientacionSexual, 'longitud' => strlen($orientacionSexual));
                        }
                
                        if (isset($orientacionSexualFrecuencia[$orientacionSexual])) {
                            $orientacionSexualFrecuencia[$orientacionSexual]++;
                        } else {
                            $orientacionSexualFrecuencia[$orientacionSexual] = 1;
                        }
                
                        $totalOrientacionSexual += $valorAbsoluto;
                    }
                
                    usort($orientacionSexualOrdenadas, function ($a, $b) {
                        return $b['longitud'] - $a['longitud'];
                    });
                
                    echo "<tr><th>Variable</th>";
                    foreach ($orientacionSexualOrdenadas as $orientacionSexualOrdenada) {
                        $orientacionSexual = $orientacionSexualOrdenada['orientacionSexual'];
                        echo "<th>$orientacionSexual</th>";
                    }
                    echo "<th>Total</th></tr>";
                
                    echo "<tr><td>Valores Absolutos</td>";
                    $valoresTotales = array();
                    foreach ($orientacionSexualOrdenadas as $orientacionSexualOrdenada) {
                        $orientacionSexual = $orientacionSexualOrdenada['orientacionSexual'];
                        $valorAbsoluto = isset($orientacionSexualFrecuencia[$orientacionSexual]) ? $orientacionSexualFrecuencia[$orientacionSexual] : 0;
                        echo "<td>$valorAbsoluto</td>";
                        $valoresTotales[] = $valorAbsoluto;
                    }
                    $totalOrientacionSexual = array_sum($valoresTotales);
                    echo "<td>$totalOrientacionSexual</td></tr>";
                
                    echo "<tr><td>Porcentaje</td>";
                    foreach ($orientacionSexualOrdenadas as $orientacionSexualOrdenada) {
                        $orientacionSexual = $orientacionSexualOrdenada['orientacionSexual'];
                        $valorAbsoluto = isset($orientacionSexualFrecuencia[$orientacionSexual]) ? $orientacionSexualFrecuencia[$orientacionSexual] : 0;
                        $porcentaje = ($valorAbsoluto / $totalOrientacionSexual) * 100;
                        echo "<td>" . number_format($porcentaje, 2) . "%</td>";
                    }
                    echo "<td>100%</td></tr>";
                
                    echo "</tbody></table>";
                    
                }

                if ($proceso2) {
                    echo "<h4>Tabla de Provincias:</h4>";
                    echo "<table class='table table-bordered table-hover' id='tablaProvincias'>";
                    echo "<tbody>";
                
                    $provinciasAgregadas = array();
                    $provinciasOrdenadas = array();
                    $provinciasFrecuencia = array();
                    $totalProvincias = 0;
                
                    foreach ($contenido2 as $linea) {
                        $linea = implode('&', $linea);
                        $datos = explode('&', $linea);
                
                        $provincia = isset($datos[6]) ? $datos[6] : "";
                        $valorAbsoluto = 1;
                        $valorPorcentual = "Valor Porcentual";
                
                        if (!in_array($provincia, $provinciasAgregadas)) {
                            $provinciasAgregadas[] = $provincia;
                            $provinciasOrdenadas[] = array('provincia' => $provincia, 'longitud' => strlen($provincia));
                        }
                
                        if (isset($provinciasFrecuencia[$provincia])) {
                            $provinciasFrecuencia[$provincia]++;
                        } else {
                            $provinciasFrecuencia[$provincia] = 1;
                        }
                
                        $totalProvincias += $valorAbsoluto;
                    }
                
                    usort($provinciasOrdenadas, function ($a, $b) {
                        return $b['longitud'] - $a['longitud'];
                    });
                
                    echo "<tr><th>Provincia</th>";
                    foreach ($provinciasOrdenadas as $provinciaOrdenada) {
                        $provincia = $provinciaOrdenada['provincia'];
                        echo "<th>$provincia</th>";
                    }
                    echo "<th>Total</th></tr>";
                
                    echo "<tr><td>Valores Absolutos</td>";
                    $valoresTotales = array();
                    foreach ($provinciasOrdenadas as $provinciaOrdenada) {
                        $provincia = $provinciaOrdenada['provincia'];
                        $valorAbsoluto = isset($provinciasFrecuencia[$provincia]) ? $provinciasFrecuencia[$provincia] : 0;
                        echo "<td>$valorAbsoluto</td>";
                        $valoresTotales[] = $valorAbsoluto;
                    }
                    $totalProvincias = array_sum($valoresTotales);
                    echo "<td>$totalProvincias</td></tr>";
                
                    echo "<tr><td>Porcentaje</td>";
                    foreach ($provinciasOrdenadas as $provinciaOrdenada) {
                        $provincia = $provinciaOrdenada['provincia'];
                        $valorAbsoluto = isset($provinciasFrecuencia[$provincia]) ? $provinciasFrecuencia[$provincia] : 0;
                        $porcentaje = ($valorAbsoluto / $totalProvincias) * 100;
                        echo "<td>" . number_format($porcentaje, 2) . "%</td>";
                    }
                    echo "<td>100%</td></tr>";
                
                    echo "</tbody></table>";
                
                    // Tabla de Delictivos
                    echo "<h4>Tabla de Delictivos:</h4> ";
                    echo "<table class='table table-bordered table-hover' id='tablaDelictivos'> ";

                    echo "<tbody>";
                
                    $delictivosAgregados = array();
                    $delictivosOrdenados = array();
                    $delictivosFrecuencia = array();
                    $totalDelictivos = 0;
                
                    foreach ($contenido2 as $linea) {
                        $linea = implode('&', $linea);
                        $datos = explode('&', $linea);
                
                        $delictivo = isset($datos[9]) ? $datos[9] : "";
                        $valorAbsoluto = 1;
                        $valorPorcentual = "Valor Porcentual ";
                
                        if (!in_array($delictivo, $delictivosAgregados)) {
                            $delictivosAgregados[] = $delictivo;
                            $delictivosOrdenados[] = array('delictivo' => $delictivo, 'longitud' => strlen($delictivo));
                        }
                
                        if (isset($delictivosFrecuencia[$delictivo])) {
                            $delictivosFrecuencia[$delictivo]++;
                        } else {
                            $delictivosFrecuencia[$delictivo] = 1;
                        }
                
                        $totalDelictivos += $valorAbsoluto;
                    }
                
                    usort($delictivosOrdenados, function ($a, $b) {
                        return $b['longitud'] - $a['longitud'];
                    });
                
                    echo "<tr><th>Delictivo</th>";
                    foreach ($delictivosOrdenados as $delictivoOrdenado) {
                        $delictivo = $delictivoOrdenado['delictivo'];
                        echo "<th>$delictivo</th>";
                    }
                    echo "<th>Total</th></tr>";
                
                    echo "<tr><td>Valores Absolutos</td>";
                    $valoresTotales = array();
                    foreach ($delictivosOrdenados as $delictivoOrdenado) {
                        $delictivo = $delictivoOrdenado['delictivo'];
                        $valorAbsoluto = isset($delictivosFrecuencia[$delictivo]) ? $delictivosFrecuencia[$delictivo] : 0;
                        echo "<td>$valorAbsoluto</td>";
                        $valoresTotales[] = $valorAbsoluto;
                    }
                    $totalDelictivos = array_sum($valoresTotales);
                    echo "<td>$totalDelictivos</td></tr>";
                
                    echo "<tr><td>Porcentaje</td>";
                    foreach ($delictivosOrdenados as $delictivoOrdenado) {
                        $delictivo = $delictivoOrdenado['delictivo'];
                        $valorAbsoluto = isset($delictivosFrecuencia[$delictivo]) ? $delictivosFrecuencia[$delictivo] : 0;
                        $porcentaje = ($valorAbsoluto / $totalDelictivos) * 100;
                        echo "<td>" . number_format($porcentaje, 2) . "%</td>";
                    }
                    echo "<td>100%</td></tr>";
                
                    echo "</tbody></table>";
                }
            }
            ?>
        </div>
    </main>

    <footer class="row pie">
        <?php
        include_once("segmentos/pie.inc");
        ?>
    </footer>

    <!-- jQuery necesario para los efectos de bootstrap -->
    <script src="formatos/bootstrap/js/jquery-1.11.3.min.js"></script>
    <script src="formatos/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tblDatos').dataTable({
                "language": {
                    "url": "dataTables.Spanish.lang"
                }
            });
        });
    </script>
</body>

</html>
