<?php
// Datos para la primera gráfica (nivel de escolaridad)
$nivel_esc = array("Nivel", "Uni Inc", "Uni Comp", "Secund Incomp", "Primar Incomp", "Educ Tec", "Secund Comp", "Primar Comp", "NA");
$niveles = array("Niveles", 237, 244, 264, 261, 253, 239, 242, 259);

// Datos para la segunda gráfica (orientación sexual)
$orientacion_sexual = array("Orientación Sexual", "Metrosexual", "Heterosexual", "Antrosexual", "Graysexual", "Homosexual","Transexual","Demisexual","Pansexual","Bisexual","Asexual");
$cantidad_orientacion = array("Cantidad", 201, 231, 202, 180, 215, 206, 211, 191, 169, 193);

// Datos para la tercera gráfica (Pronvincias)
$provincias = array("Provincias", "Guanacaste", "Puntarenas", "San Jose", "Alajuela", "Cartago", "Heredia", "Limon");
$cant_pers = array("Cantidad", 510, 458, 521, 527, 529, 485, 469,);

// Datos para la cuarta gráfica (Casos delincuencia)
$casos = array("Casos", "SI", "NO");
$cant_casos = array("Cantidad", 1758, 1741);


// Combinar los datos en un arreglo
$datos = array($nivel_esc, $niveles, $orientacion_sexual, $cantidad_orientacion,$provincias,$cant_pers,$casos,$cant_casos);

echo json_encode($datos);
?>