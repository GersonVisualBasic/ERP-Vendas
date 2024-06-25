<?php
require_once('vendor/autoload.php');

$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Seu Nome');
$pdf->SetTitle('Relatório de Vendas');
$pdf->SetSubject('Relatório de Vendas em PDF');
$pdf->SetKeywords('TCPDF, PDF, relatório, vendas');

$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage();

// Cabeçalho
$pdf->Cell(0, 10, 'Relatório de Vendas', 0, 1, 'C');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "pdv";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT V.ID_VENDA, V.DATA_VENDA, C.NOME AS CLIENTE, P.NOME AS PRODUTO, IV.QUANTIDADE, IV.PRECO_UNITARIO
    FROM VENDAS V
    JOIN CLIENTES C ON V.ID_CLIENTE = C.ID_CLIENTE
    JOIN ITENS_VENDA IV ON V.ID_VENDA = IV.ID_VENDA
    JOIN PRODUTOS P ON IV.ID_PRODUTO = P.ID_PRODUTO
";
$result = $conn->query($sql);

// Tabela de dados
if ($result->num_rows > 0) {
    $pdf->SetFont('helvetica', 'B', 10);

    $pdf->Cell(20, 7, 'ID Venda', 1, 0, 'C');
    $pdf->Cell(40, 7, 'Data', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Cliente', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Produto', 1, 0, 'C');
    $pdf->Cell(10, 7, 'Qtd', 1, 0, 'C');
    $pdf->Cell(20, 7, 'Preco Unit.', 1, 1, 'C');

    $pdf->SetFont('helvetica', '', 10);

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 7, $row['ID_VENDA'], 1, 0, 'C');
        $pdf->Cell(40, 7, $row['DATA_VENDA'], 1, 0, 'C');
        $pdf->Cell(50, 7, $row['CLIENTE'], 1, 0, 'C');
        $pdf->Cell(50, 7, $row['PRODUTO'], 1, 0, 'C');
        $pdf->Cell(10, 7, $row['QUANTIDADE'], 1, 0, 'C');
        // Use MultiCell para o preço unitário para permitir quebra de linha se necessário
        $pdf->MultiCell(20, 7, $row['PRECO_UNITARIO'], 1, 'C');
    }

    $pdf->Output('relatorio_vendas.pdf', 'D');
} else {
    echo "Nenhum resultado encontrado.";
}



$conn->close();
