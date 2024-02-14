<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
       

        include "..\classes\dbh.classes.php"; 
        include "raport.classes.php";
        include "..\classes\sellyears-contr.classes.php";

        $raport = new SimpleRaportContr;

        // Pobierz dane
        $raportData = $raport->getSummedDataByGroupAndYear();
       

        echo json_encode( $raportData );

   
}
