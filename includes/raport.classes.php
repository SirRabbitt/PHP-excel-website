<?php
class Raport extends Dbh
{

    // Metoda do pobierania danych raportu
    public function fetchRaportData($startDate, $endDate)
    {
        $stmt = $this->connect()->prepare('
        SELECT 
        g.nazwa AS nazwa_grupy,
        z.data,
        z.numer_zamowienia, 
        z.ilosc, 
        p.nazwa AS nazwa_produktu, 
        p.cena_netto, 
        p.vat
    FROM 
        zamowienia z
    JOIN 
        produkty p ON z.id_produkt = p.id
    JOIN 
        grupy_produktow g ON p.id_grupa = g.id
    WHERE 
        z.data BETWEEN ? AND ?
    GROUP BY 
        g.nazwa, z.data
    ORDER BY 
        z.data ASC,
        p.nazwa DESC;
        ');

        if (!$stmt->execute(array($startDate, $endDate))) {
            $stmt = null;
            return false; // W przypadku błędu zapytania
        }

        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false; // Brak danych do wyświetlenia
        }

        $raportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $raportData;
    }
    public function fetchRaport()
    {
        $stmt = $this->connect()->prepare('
            SELECT 
                g.nazwa AS nazwa_grupy,
                z.data,
                z.numer_zamowienia, 
                z.ilosc, 
                p.nazwa AS nazwa_produktu, 
                p.cena_netto, 
                p.vat
            FROM 
                zamowienia z
            JOIN 
                produkty p ON z.id_produkt = p.id
            JOIN 
                grupy_produktow g ON p.id_grupa = g.id
            GROUP BY 
                g.nazwa, z.data
            ORDER BY 
                z.data ASC,
                p.nazwa DESC;
        ');

        if (!$stmt->execute()) {
            $stmt = null;
            return false; // W przypadku błędu zapytania
        }

        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false; // Brak danych do wyświetlenia
        }

        $raportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $raportData;
    }
}
