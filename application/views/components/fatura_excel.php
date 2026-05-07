<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

$nome = "REDE MAIS CRÉDITO";
$data = "JULHO";
$tabela = "Informações Gerais";


$spreadsheet = new Spreadsheet();
$activeWorksheet = $spreadsheet->getActiveSheet();

$activeWorksheet->setCellValue('A1', 'REDE MAIS CRÉDITO');
$activeWorksheet->mergeCells('A1:E1');
$activeWorksheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('c3d698');
$activeWorksheet->getStyle('A1:E1')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

$activeWorksheet->setCellValue('A2', $data);
$activeWorksheet->mergeCells('A2:E2');
$activeWorksheet->getStyle('A2:E2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fcd6b4');
$activeWorksheet->getStyle('A2:E2')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
$activeWorksheet->setCellValue('A3', $tabela);
$activeWorksheet->mergeCells('A3:E3');
$activeWorksheet->getStyle('A3:E3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92cddb');
$activeWorksheet->getStyle('A3:E3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

$activeWorksheet->setCellValue('A4', "SERVIÇOS");
$activeWorksheet->getStyle('A4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
$activeWorksheet->setCellValue('B4', "FAIXA");
$activeWorksheet->getStyle('B4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
$activeWorksheet->setCellValue('C4', "VALOR");
$activeWorksheet->getStyle('C4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
$activeWorksheet->setCellValue('D4', "QUANTIDADE (UNIT)");
$activeWorksheet->getStyle('D4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
$activeWorksheet->setCellValue('E4', "SUBTOTAL");
$activeWorksheet->getStyle('E4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

for($i = 0; $i < 15; $i++){
    $activeWorksheet->setCellValue('A'.(5+$i), 'CONSULTA X');
    $activeWorksheet->getStyle('A'.(5+$i))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
    $activeWorksheet->setCellValue('B'.(5+$i), 'ÚNICA');
    $activeWorksheet->getStyle('B'.(5+$i))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
    $activeWorksheet->setCellValue('C'.(5+$i), 'R$ 5,00');
    $activeWorksheet->getStyle('C'.(5+$i))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
    $activeWorksheet->setCellValue('D'.(5+$i), '122');
    $activeWorksheet->getStyle('D'.(5+$i))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
    $activeWorksheet->setCellValue('E'.(5+$i), 'R$ 650,70');
    $activeWorksheet->getStyle('E'.(5+$i))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
}

$activeWorksheet->getColumnDimension('A')->setAutoSize(true);
$activeWorksheet->getColumnDimension('B')->setAutoSize(true);
$activeWorksheet->getColumnDimension('C')->setAutoSize(true);
$activeWorksheet->getColumnDimension('D')->setAutoSize(true);
$activeWorksheet->getColumnDimension('E')->setAutoSize(true);

$writer = new Xlsx($spreadsheet);
$fileName = "teste.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
$writer->save('php://output');
exit;