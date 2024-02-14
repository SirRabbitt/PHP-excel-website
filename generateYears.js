function initialize() {
   
        

        
        generateTable();
        generateChart()
        
    
};
function generateTable() {
    var container = document.getElementById('tableContainer');
    container.innerHTML = '';

    var tableContainer = document.createElement('div');
    tableContainer.classList.add('table-container');

    fetch('includes/sellyears.inc.php', {
        method: 'POST',
    })
    .then(response => response.json())
    .then(responseData => {
        const raportData = responseData;

        var table = document.createElement('table');
        table.classList.add('raport-table');

        var thead = table.createTHead();
        var headerRowTop = thead.insertRow();
        var headerRowBottom = thead.insertRow();

        // Pierwszy wiersz nagłówków
        var groupHeaderCell = document.createElement("th");
        groupHeaderCell.textContent = "Grupa Produktów";
        groupHeaderCell.setAttribute("rowspan", "2");
        headerRowTop.appendChild(groupHeaderCell);

        ["2019", "2020", "2021"].forEach(year => {
            var yearHeaderCell = document.createElement("th");
            yearHeaderCell.textContent = year;
            yearHeaderCell.setAttribute("colspan", "2");
            headerRowTop.appendChild(yearHeaderCell);
        });

        // Drugi wiersz nagłówków
        ["Netto", "Brutto", "Netto", "Brutto", "Netto", "Brutto"].forEach(headerText => {
            var headerCell = document.createElement("th");
            headerCell.textContent = headerText;
            headerRowBottom.appendChild(headerCell);
        });

        var tbody = table.createTBody();
        var totalNetto = {};
        var totalBrutto = {};

        // Przetwarzanie danych i wypełnianie tabeli
        Object.keys(raportData).forEach(group => {
            var row = tbody.insertRow();
            var cellGroup = row.insertCell();
            cellGroup.textContent = group;

            ["2019", "2020", "2021"].forEach(year => {
                if (!totalNetto[year]) totalNetto[year] = 0;
                if (!totalBrutto[year]) totalBrutto[year] = 0;

                var cellNetto = row.insertCell();
                var cellBrutto = row.insertCell();

                var values = raportData[group][year] || { kwota_netto: 0, kwota_brutto: 0 };
                cellNetto.textContent = values.kwota_netto.toFixed(2) + ' zł';
                cellBrutto.textContent = values.kwota_brutto.toFixed(2) + ' zł';

                totalNetto[year] += values.kwota_netto;
                totalBrutto[year] += values.kwota_brutto;
            });
        });

        // Dodawanie wiersza sumującego
        var sumRow = tbody.insertRow();
        sumRow.insertCell().textContent = 'SUMA';
        ["2019", "2020", "2021"].forEach(year => {
            var cellSumNetto = sumRow.insertCell();
            cellSumNetto.textContent = totalNetto[year].toFixed(2) + ' zł';

            var cellSumBrutto = sumRow.insertCell();
            cellSumBrutto.textContent = totalBrutto[year].toFixed(2) + ' zł';
        });

        tableContainer.appendChild(table);
        container.appendChild(tableContainer);
    })
    .catch(error => {
        console.error('Error:', error);
        container.textContent = 'Wystąpił błąd podczas ładowania danych.';
    });
}


function generateChart() {
    fetch('includes/sellyears.inc.php', {
        method: 'POST',
    })
    .then(response => response.json())
    .then(responseData => {
        const raportData = responseData;

        // Przygotowanie danych do wykresu
        var labels = ["2019", "2020", "2021"]; // Lata
        var datasets = [];

        Object.keys(raportData).forEach(group => {
            var data = labels.map(year => {
                return raportData[group][year] ? raportData[group][year].kwota_netto : 0;
            });

            datasets.push({
                label: group,
                data: data,
                fill: false,
                borderColor: getRandomColor(), // Funkcja do generowania losowego koloru
                tension: 0.1
            });
        });

        var ctx = document.getElementById('myChart').getContext('2d');
        if (window.myLineChart) {
            window.myLineChart.destroy(); // Usuń poprzedni wykres, jeśli istnieje
        }

        window.myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'rgb(0, 0, 0)' // Ustawienie koloru tekstu legendy
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                maintainAspectRatio: false,
                responsive: true
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Funkcja do generowania losowego koloru
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
document.addEventListener('DOMContentLoaded', initialize);