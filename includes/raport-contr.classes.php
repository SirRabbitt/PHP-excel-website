<?php

class RaportContr extends Raport {

    private $startDate;
    private $endDate;

    public function __construct($startDate, $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function generateRaport() {
        if ($this->emptyInput()) {
            header("location: ../index.php?error=emptyinput");
            exit();
        }

        $raportData = $this->fetchRaportData($this->startDate, $this->endDate);
        if (!$raportData) {
            return false; // Brak danych lub błąd zapytania
        }

        return $this->aggregateDataByGroupAndDate($raportData);
    }
    

    public function getAggregatedDataByGroup() {
        $raportData = $this->fetchRaportData($this->startDate, $this->endDate);
        if (!$raportData) {
            return false; // Brak danych lub błąd zapytania
        }

        $aggregatedData = [];
        foreach ($raportData as $row) {
            $group = $row['nazwa_grupy'];
            $date = $row['data'];

            if (!isset($aggregatedData[$group])) {
                $aggregatedData[$group] = ['kwota_netto' => 0, 'kwota_brutto' => 0];
            }
            $aggregatedData[$group]['kwota_netto'] += $row['cena_netto'] * $row['ilosc'];
            $aggregatedData[$group]['kwota_brutto'] += $row['cena_netto'] * $row['ilosc'] * (1 + $row['vat'] / 100);
        }

        // Zaokrąglenie skumulowanych wyników do dwóch miejsc po przecinku
        foreach ($aggregatedData as $group => $values) {
            $aggregatedData[$group]['kwota_netto'] = round($values['kwota_netto'], 2);
            $aggregatedData[$group]['kwota_brutto'] = round($values['kwota_brutto'], 2);
        }

        return $aggregatedData;
    }

    private function aggregateDataByGroupAndDate($data) {
        $aggregatedData = [];
        foreach ($data as $row) {
            $group = $row['nazwa_grupy'];
            $date = $row['data'];

            if (!isset($aggregatedData[$date][$group])) {
                $aggregatedData[$date][$group] = ['kwota_netto' => 0, 'kwota_brutto' => 0];
            }
            $aggregatedData[$date][$group]['kwota_netto'] += $row['cena_netto'] * $row['ilosc'];
            $aggregatedData[$date][$group]['kwota_brutto'] += $row['cena_netto'] * $row['ilosc'] * (1 + $row['vat'] / 100);
        }

        // Zaokrąglenie skumulowanych wyników do dwóch miejsc po przecinku
        foreach ($aggregatedData as $date => $groups) {
            foreach ($groups as $group => $values) {
                $aggregatedData[$date][$group]['kwota_netto'] = round($values['kwota_netto'], 2);
                $aggregatedData[$date][$group]['kwota_brutto'] = round($values['kwota_brutto'], 2);
            }
        }

        return $aggregatedData;
    }
   
    private function emptyInput() {
        return empty($this->startDate) || empty($this->endDate);
    }
}
