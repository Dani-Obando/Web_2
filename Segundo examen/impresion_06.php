<?php
header('Content-Type: text/html; charset=utf-8');

require('codigos/fpdf.php');
include_once('codigos/conexion2.inc');

class PDF extends FPDF {
   // Imprime encabezado

   function SepCategoria($Categoria, $TotalExistencias) {
      $this->Ln(8);
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(0, 6, 'Categoria ' . $Categoria . ': Total Existencias = ' . $TotalExistencias, 0, 1, 'L');
      $this->Ln(4);
  }
   function Header() {
       $this->SetFont('Arial', 'B', 15);
       $this->Cell(80);
       $this->Cell(20, 10, utf8_decode('Universidad Técnica Nacional'), 0, 0, 'C');
       $this->Ln(5);
       $this->Cell(80);
       $this->Cell(20, 10, 'Listado de Peliculas', 0, 0, 'C');
       $this->Ln(15);
       $this->Ln(9);
       $this->SetDrawColor(0, 0, 0); // Color negro
       $this->SetLineWidth(0.5); // Ancho de la línea
       $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 190, $this->GetY()); 
       $this->Ln(1);  
       $this->Cell(40, 6, 'ID', 0, 0, 'L');
       $this->Cell(1);  
       $this->Cell(55, 6, 'TITULO', 0, 0, 'L');
       $this->Cell(1);  
       $this->Cell(55, 6, 'CANTIDAD', 0, 0, 'L');
       $this->Cell(1);  
       $this->Cell(20, 6, 'ANIO', 0, 1, 'L');
       $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 190, $this->GetY()); 
       $this->Ln(10);
   }

    // Imprime el pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Imprime datos de películas y acumula las existencias por categoría
    function DatPeliculas($ID, $Nombre, $Cantidad, $Anio) {
        // Ancho de las columnas
        $ancho = array(30, 78, 23, 35);

        // Imprime los datos en negrita
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($ancho[0], 6, $ID);
        $this->Cell($ancho[1], 6, $Nombre);
        $this->Cell($ancho[2], 6, $Cantidad);
        $this->Cell($ancho[3], 6, $Anio, 0, 1, 'R');
        
        $this->Ln();
    }
}

// Agrega nueva instancia de PDF e inicializa el contador de páginas
$pdf = new PDF();
$pdf->AliasNbPages();

// Agrega página y define tipo de fuente
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Configura la conexión a la base de datos con UTF-8
mysqli_set_charset($conex, 'utf8');

// Define y ejecuta una línea de consulta sobre la BD.
$AuxSql = 'SELECT f.film_id as ID, f.title as Nombre, COUNT(*) as Cantidad, f.release_year as Anio ';
$AuxSql .= 'FROM film f ';
$AuxSql .= 'JOIN inventory i ON f.film_id = i.film_id ';
$AuxSql .= 'GROUP BY f.film_id, f.title, f.release_year ';
$AuxSql .= 'ORDER BY f.film_id';

$Regis = mysqli_query($conex, $AuxSql) or die(mysqli_error($conex));

// Declara variables para la impresión de la categoría y el total de existencias
$pdf->TotalExistencias = 0;

// Recorre las tuplas recuperadas de la consulta.
// Imprime juego de valores para el informe
while ($row_Regis = mysqli_fetch_assoc($Regis)) {
    // Invoca método para imprimir datos de películas
    $pdf->DatPeliculas(
        $row_Regis['ID'],
        $row_Regis['Nombre'],
        $row_Regis['Cantidad'],
        $row_Regis['Anio']
    );
}

// Finaliza la construcción del PDF y lo envía al navegador
$pdf->Output();

// Limpia el registro de datos
if (isset($Regis)) {
    mysqli_free_result($Regis);
}

// Cierra la conexión a la base de datos
mysqli_close($conex);
?>
