<?php

use Cake\I18n\FrozenDate;
use \App\Model\Entity\Pedido;

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido $pedido
 */
class PDF extends FPDF
{
    public $pedido;
    public $isFinished = false;

    public function setPedido($pedido)
    {
        $this->pedido = $pedido;
    }

    // Page header
    function Header()
    {
    }

    // Page footer
    function Footer()
    {
    }

    function TextDecode($text)
    {
        return utf8_decode($text);
    }
}

/* ---------- Instancia ---------- */
header("Content-Type: application/pdf");
$pdf = new PDF();
ob_end_clean();

/* ---------- Dados de criação do PDF ---------- */
$pdf->setTitle('etiqueta-produto');
$pdf->SetAuthor('Montenegro Express');
$pdf->SetCreator('Montenegro Express');
$pdf->SetSubject('Etiqueta com os dados de entrega para colar no objeto');

/* ---------- Dados básicos ---------- */
$pdf->setPedido($pedido);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 11);

$inicio = [
    'x' => $pdf->GetX(),
    'y' => $pdf->GetY(),
];

$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(47, 7, utf8_decode('DESTINATÁRIO'), 1, 1, 'C', '#000');

$pdf->SetTextColor(0, 0, 0);
$pdf->SetMargins($pdf->GetX() + 1, $pdf->GetX(), $pdf->GetX() + 1);
$pdf->SetY($pdf->GetY() + 2);

/* ---------- Meio de entrega ---------- */
$previsaoEntrega = $pdf->pedido->previsao_entrega;
$dataBase = new FrozenDate();
$dias = $dataBase->diffInDays($previsaoEntrega);

if ($dias > 1) {
    $meioEntrega = "{$pdf->pedido->meio_entrega} - Entrega em até {$dias} dias úteis";
} else {
    $meioEntrega = "{$pdf->pedido->meio_entrega} - Entrega em até {$dias} dia útil";
}
$pdf->MultiCell(98, 4, $pdf->TextDecode($meioEntrega));

$pdf->Ln(2);

/* ---------- Recebedor da entrega ---------- */
$recebedor = "{$pdf->pedido->objeto->nome_destinatario} - {$pdf->pedido->objeto->celular_destinatario}";
$pdf->MultiCell(98, 4, $pdf->TextDecode($recebedor));

$pdf->Ln(2);

/* ---------- Endereço de entrega ---------- */
$pdf->MultiCell(98, 4, $pdf->TextDecode(
    "{$pdf->pedido->objeto->endereco_entrega->logradouro} - {$pdf->pedido->objeto->endereco_entrega->numero}"
));

$pdf->MultiCell(98, 4, $pdf->TextDecode($pdf->pedido->objeto->endereco_entrega->bairro));

if (!empty($pdf->pedido->objeto->endereco_entrega->complemento)) {
    $pdf->MultiCell(98, 4, $pdf->TextDecode($pdf->pedido->objeto->endereco_entrega->complemento));
}
if (!empty($pdf->pedido->objeto->endereco_entrega->referencia)) {
    $pdf->MultiCell(98, 4, $pdf->TextDecode($pdf->pedido->objeto->endereco_entrega->referencia));
}
$pdf->MultiCell(98, 4, $pdf->TextDecode(
    $pdf->pedido->objeto->endereco_entrega->cidade->nome . " - " .
        $pdf->pedido->objeto->endereco_entrega->cidade->estado->nome
));

$pdf->Ln(5);
$pdf->MultiCell(98, 4, $pdf->TextDecode("Pedido #{$pdf->pedido->id}"));

$fim = [
    'x' => $pdf->GetX(),
    'y' => $pdf->GetY(),
];

$pdf->SetXY($inicio['x'], $inicio['y']);
$pdf->Cell(95, $fim['y'], "", 1, 1);
$pdf->SetXY($inicio['x'], $inicio['y']);

$pdf->isFinished = true;
$pdf->Output();
