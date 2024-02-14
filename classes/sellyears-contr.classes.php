<?php

class SimpleRaportContr extends Raport {

    // Metoda do pobierania i sumowania danych po grupie i roku
    public function getSummedDataByGroupAndYear() {
        $raportData = $this->fetchRaport();
        if (!$raportData) {
            return false; // Brak danych lub błąd zapytania
        }

        $summedData = [];
        foreach ($raportData as $row) {
            $group = $row['nazwa_grupy'];
            $year = date('Y', strtotime($row['data']));

            if (!isset($summedData[$group][$year])) {
                $summedData[$group][$year] = ['kwota_netto' => 0, 'kwota_brutto' => 0];
            }

            $summedData[$group][$year]['kwota_netto'] += $row['cena_netto'] * $row['ilosc'];
            $summedData[$group][$year]['kwota_brutto'] += $row['cena_netto'] * $row['ilosc'] * (1 + $row['vat'] / 100);
        }

        // Zaokrąglenie skumulowanych wyników do dwóch miejsc po przecinku
        foreach ($summedData as $group => $years) {
            foreach ($years as $year => $values) {
                $summedData[$group][$year]['kwota_netto'] = round($values['kwota_netto'], 2);
                $summedData[$group][$year]['kwota_brutto'] = round($values['kwota_brutto'], 2);
            }
        }

        return $summedData;
    }

    // Możesz dodać tu inne metody, które są specyficzne dla tej klasy
}
