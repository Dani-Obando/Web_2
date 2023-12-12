<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include_once("segmentos/encabe.inc");
    ?>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

    <script type="text/javascript">
        var datos = $.ajax({
            url: 'datos.php',
            type: 'post',
            dataType: 'json',
            async: false
        }).responseText;

        datos = JSON.parse(datos);

        google.load("visualization", "1", {packages:["corechart"]});

        google.setOnLoadCallback(creaGrafico1);

        function creaGrafico1() {
            var data1 = google.visualization.arrayToDataTable([datos[0], datos[1]]);

            var opciones1 = {
                title: 'Nivel de escolaridad',
                hAxis: {title: 'Escolaridad', titleTextStyle: {color: 'green'}},
                vAxis: {title: 'Encuestados', titleTextStyle: {color: '#FF0000'}},
                backgroundColor: '#ffffcc',
                legend: {position: 'bottom', textStyle: {color: 'blue', fontSize: 13}},
                width: 900,
                height: 500
            };

            var grafico1 = new google.visualization.ColumnChart(document.getElementById('grafica1'));
            grafico1.draw(data1, opciones1);
        }

        google.setOnLoadCallback(creaGrafico2);

        function creaGrafico2() {
            var data2 = google.visualization.arrayToDataTable([datos[2], datos[3]]);

            var opciones2 = {
                title: 'Orientación sexual',
                hAxis: {title: 'Orientacion', titleTextStyle: {color: 'green'}},
                vAxis: {title: 'Encuestados', titleTextStyle: {color: '#FF0000'}},
                backgroundColor: '#ffffcc',
                legend: {position: 'bottom', textStyle: {color: 'blue', fontSize: 13}},
                width: 900,
                height: 500
            };

            var grafico2 = new google.visualization.ColumnChart(document.getElementById('grafica2'));
            grafico2.draw(data2, opciones2);
        }

        google.setOnLoadCallback(creaGrafico3);

        function creaGrafico3() {
            var data3 = google.visualization.arrayToDataTable([datos[4], datos[5]]);

            var opciones3 = {
                title: 'Cantidad de personas por provincia',
                hAxis: { title: 'Provincias', titleTextStyle: { color: 'green' } },
                vAxis: { title: 'Encuestados', titleTextStyle: { color: '#FF0000' } },
                backgroundColor: '#ffffcc',
                legend: { position: 'bottom', textStyle: { color: 'blue', fontSize: 13 } },
                width: 900,
                height: 500
            };

            var grafico3 = new google.visualization.ColumnChart(document.getElementById('grafica3'));
            grafico3.draw(data3, opciones3);
        }

        google.setOnLoadCallback(creaGrafico4);

function creaGrafico4() {
    var data4 = google.visualization.arrayToDataTable([datos[6], datos[7]]);

    var opciones4 = {
        title: 'Casos de Delincuencia',
        hAxis: { title: 'Respuestas', titleTextStyle: { color: 'green' } },
        vAxis: { title: 'Cantidad', titleTextStyle: { color: '#FF0000' } },
        backgroundColor: '#ffffcc',
        legend: { position: 'bottom', textStyle: { color: 'blue', fontSize: 13 } },
        width: 900,
        height: 500
    };

    var grafico4 = new google.visualization.ColumnChart(document.getElementById('grafica4'));
    grafico4.draw(data4, opciones4);
}
    </script>
</head>
<body class="container">
    <header class="row">
        <?php
            include_once("segmentos/menu.inc");
        ?>
    </header>

    <main class="row">
        <div class="linea_sep">
            <div id="grafica1"> </div>
        </div>
        <div class="linea_sep">
            <div id="grafica2"> </div>
        </div>
        <div class="linea_sep">
            <div id="grafica3"> </div>
        </div>
        <div class="linea_sep">
            <div id="grafica4"> </div>
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
</body>
</html>

