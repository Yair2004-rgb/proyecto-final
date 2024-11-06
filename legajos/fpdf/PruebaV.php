<?php
require('./fpdf.php');
include('../db.php'); // Ajusta la ruta según la ubicación real

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->Image('logo.png', 200, 5, 40, 20); // Logo de la empresa
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(10);

        $this->SetTextColor(228, 100, 0);
        $this->Cell(0, 10, utf8_decode("REPORTE MENSUAL"), 0, 1, 'C');
        $this->Ln(5);

        // Encabezados de la tabla
        $this->SetFillColor(200, 200, 200); // Fondo gris claro
        $this->SetTextColor(0, 0, 0); // Texto negro
        $this->SetDrawColor(163, 163, 163); // Color del borde
        $this->SetFont('Arial', 'B', 10); // Tamaño de fuente más pequeño

        $cellWidth = 25;
        $idCellWidth = 15;
        $tipoCellWidth = 15;

        // Encabezados
        $this->Cell($idCellWidth, 7, utf8_decode('ID'), 1, 0, 'C', 1);
        $this->Cell($cellWidth * 2, 7, utf8_decode('Nombres y Apellidos'), 1, 0, 'C', 1);
        $this->Cell($cellWidth, 7, utf8_decode('DNI'), 1, 0, 'C', 1);
        $this->Cell($cellWidth, 7, utf8_decode('N° de Folios'), 1, 0, 'C', 1);
        $this->Cell($cellWidth, 7, utf8_decode('Fecha'), 1, 0, 'C', 1);
        $this->Cell($cellWidth, 7, utf8_decode('N° de Caja'), 1, 0, 'C', 1);
        $this->Cell($tipoCellWidth, 7, utf8_decode('Tipo'), 1, 0, 'C', 1);
        $this->Cell($cellWidth * 4, 7, utf8_decode('DETALLES'), 1, 1, 'C', 1);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-25);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $hoy = date('d/m/Y', strtotime('-1 day'));
        $this->Cell(0, 10, utf8_decode($hoy), 0, 1, 'C');
    }

    // Ajustamos las celdas uniformemente
    function Row($data)
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 7 * $nb;
        $this->CheckPageBreak($h);
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 7, $data[$i], 0, $a);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

$pdf = new PDF('L'); // Orientación horizontal
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(array(15, 50, 25, 25, 25, 25, 15, 100));

$query = "SELECT * FROM legajos";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $detalles = "";
    if (!empty($row['origen'])) {
        $detalles .= "Lugar de Origen: " . utf8_decode($row['origen']) . "\n";
    }
    if (!empty($row['registro_entrada'])) {
        $detalles .= "Encargado de Registro: " . utf8_decode($row['registro_entrada']) . "\n";
    }
    if (!empty($row['envio'])) {
        $detalles .= "Encargado de Envío: " . utf8_decode($row['envio']) . "\n";
    }
    if (!empty($row['motivo_entrada'])) {
        $detalles .= "Motivo de Entrada: " . utf8_decode($row['motivo_entrada']);
    }
    if (!empty($row['salida'])) {
        $detalles .= "Lugar de Salida: " . utf8_decode($row['salida']) . "\n";
    }
    if (!empty($row['registro_salida'])) {
        $detalles .= "Encargado de Registro: " . utf8_decode($row['registro_salida']) . "\n";
    }
    if (!empty($row['retiro'])) {
        $detalles .= "Encargado de Retiro: " . utf8_decode($row['retiro']) . "\n";
    }
    if (!empty($row['motivo_salida'])) {
        $detalles .= "Motivo de Salida: " . utf8_decode($row['motivo_salida']);
    }

    $pdf->Row(array(
        utf8_decode($row['id']),
        utf8_decode($row['nombres']),
        utf8_decode($row['dni']),
        utf8_decode($row['folios']),
        utf8_decode($row['fecha']),
        utf8_decode($row['caja']),
        ucfirst(utf8_decode($row['tipo'])),
        utf8_decode(strip_tags($detalles))
    ));
}

$pdf->Output('Reporte.pdf', 'I');
?>
