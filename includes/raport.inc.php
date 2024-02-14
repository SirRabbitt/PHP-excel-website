<?php
session_start();
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Title;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["startDate"]) && isset($_POST["endDate"])) {
        $startDate = $_POST["startDate"];
        $endDate = $_POST["endDate"];

        include "..\classes\dbh.classes.php"; 
        include "raport.classes.php";
        include "raport-contr.classes.php";

        $raport = new RaportContr($startDate, $endDate);

        // Pobierz dane
        $data = $raport->generateRaport();
        $dataW = $raport->getAggregatedDataByGroup();

        // Tworzenie arkusza
        $spreadsheet = new Spreadsheet();

        // Dodanie ukrytego arkusza z danymi dla wykresu
        $dataSheet = $spreadsheet->createSheet();
        $dataSheet->setTitle("DaneWykresu");
        $row = 1;
        foreach ($dataW as $group => $values) {
            $dataSheet->setCellValue('A' . $row, $group);
            $dataSheet->setCellValue('B' . $row, $values['kwota_netto']);
            $dataSheet->setCellValue('C' . $row, $values['kwota_brutto']);
            $row++;
        }
        $dataSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        // Powrót do głównego arkusza
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        // Utworzenie wykresu na podstawie danych z ukrytego arkusza
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'DaneWykresu!$B$1', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'DaneWykresu!$C$1', null, 1)
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'DaneWykresu!$A$1:$A$' . ($row - 1), null, $row - 1),
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'DaneWykresu!$B$1:$B$' . ($row - 1), null, $row - 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'DaneWykresu!$C$1:$C$' . ($row - 1), null, $row - 1)
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Wykres Sprzedaży');

        $chart = new Chart(
            'chart1',
            $title,
            $legend,
            $plotArea
        );

        $chartStartRow = 1;
        $chart->setTopLeftPosition('E' . ($chartStartRow + 2));
        $chart->setBottomRightPosition('L' . ($chartStartRow + 15));
        $worksheet->addChart($chart);

        // Dodanie tabeli z danymi na głównym arkuszu
        $tableStartRow = 1;
        $worksheet->fromArray([['Grupa Produktów', 'Data', 'Kwota Netto', 'Kwota Brutto']], null, 'A' . $tableStartRow);
        $tableRow = $tableStartRow + 1;
        foreach ($data as $date => $groups) {
            foreach ($groups as $group => $values) {
                $worksheet->fromArray([$group, $date, $values['kwota_netto'], $values['kwota_brutto']], null, 'A' . $tableRow);
                $tableRow++;
            }
        }

        // Zapisywanie arkusza jako plik XLSX
        $directoryPath = '../uploads/';
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
        $filename = $directoryPath . 'raport_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($filename);

        // Zwracanie pliku
        echo json_encode(['file' => $filename]);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "Brak wymaganych danych"]);
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(["error" => "Nieprawidłowa metoda żądania"]);
}
