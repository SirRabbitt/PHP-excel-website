<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["startDate"]) && isset($_POST["endDate"])) {
        $startDate = $_POST["startDate"];
        $endDate = $_POST["endDate"];

        include "..\classes\dbh.classes.php"; 
        include "raport.classes.php";
        include "raport-contr.classes.php";

        $raport = new RaportContr($startDate, $endDate);

        // Pobierz dane
        $raportData = $raport->generateRaport();
        $aggregatedData = $raport->getAggregatedDataByGroup();

        echo json_encode(["raportData" => $raportData, "aggregatedData" => $aggregatedData]);

    } 
}
