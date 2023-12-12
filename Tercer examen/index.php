<?php

$Datos = array(
        "estudiante" => array(
            "apellidos" => "Obando Navarro",
            "celular" => 61693896,
            "email" => "daobandona@est.utn.ac.cr",
            "id" => 504560972,
            "nivel" => "Estudiante",
            "nombre" => "Daniel"
        ),
);


deliver_response(200, "OK", $Datos);
?>