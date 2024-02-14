document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('salesReportForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Zapobieganie domyślnej akcji formularza

        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;
        generateTable(startDate, endDate);
        generateChart(startDate, endDate)
        showDownloadButton(startDate, endDate)
    });
});
function generateTable(startDate, endDate) {
    var container = document.getElementById('tableContainer');
    container.innerHTML = '';

    var tableContainer = document.createElement('div');
    tableContainer.classList.add('table-container');

    var formData = new FormData();
    formData.append('startDate', startDate);
    formData.append('endDate', endDate);

    fetch('includes/tabele.inc.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(responseData => {
        const raportData = responseData.raportData;
        
        const aggregatedData = responseData.aggregatedData;

        
        var table = document.createElement('table');
        table.classList.add('raport-table');

        var thead = table.createTHead();
        var headerRow = thead.insertRow();
        var headers = ["Grupa Produktów","Data", "Kwota Netto", "Kwota Brutto"];
        headers.forEach(headerText => {
            var headerCell = document.createElement("th");
            headerCell.textContent = headerText;
            headerRow.appendChild(headerCell);
        });

        var tbody = table.createTBody();
        var totalNetto = 0;
        var totalBrutto = 0;

        Object.keys(raportData).forEach(date => {
            Object.keys(raportData[date]).forEach(group => {
                var values = raportData[date][group];
                var row = tbody.insertRow();

                var cellGroup = row.insertCell();
                cellGroup.textContent = group;
                
                var cellDate = row.insertCell();
                cellDate.textContent = date;

                var cellNetto = row.insertCell();
                cellNetto.textContent = values.kwota_netto.toFixed(2);
                totalNetto += values.kwota_netto;

                var cellBrutto = row.insertCell();
                cellBrutto.textContent = values.kwota_brutto.toFixed(2);
                totalBrutto += values.kwota_brutto;
            });
        });

        tableContainer.appendChild(table);
        container.appendChild(tableContainer);

        // Dodawanie wiersza z napisem "Suma"
        var sumTable = document.createElement('table');
        sumTable.classList.add('sum-table');
        var tbodySum = sumTable.createTBody();
        var sumRow = tbodySum.insertRow();

        
        sumRow.insertCell(); // Pusta komórka

        var sumLabelCell = sumRow.insertCell();
        sumLabelCell.textContent = 'Suma';

        var cellSumNetto = sumRow.insertCell();
        cellSumNetto.textContent = totalNetto.toFixed(2);

        var cellSumBrutto = sumRow.insertCell();
        cellSumBrutto.textContent = totalBrutto.toFixed(2);

        container.appendChild(sumTable);
        
    })
    .catch(error => {
        console.error('Error:', error);
        container.textContent = 'Wystąpił błąd podczas ładowania danych.';
    });
}
function generateChart(startDate, endDate) {
    var formData = new FormData();
    formData.append('startDate', startDate);
    formData.append('endDate', endDate);

    fetch('includes/tabele.inc.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(responseData => {
        const aggregatedData = responseData.aggregatedData;
        var ctx = document.getElementById('myChart').getContext('2d');

        var labels = Object.keys(aggregatedData);
        var dataNetto = labels.map(label => aggregatedData[label].kwota_netto);
        var dataBrutto = labels.map(label => aggregatedData[label].kwota_brutto);

        if (window.myBarChart) {
            window.myBarChart.destroy(); // Usuń poprzedni wykres, jeśli istnieje
        }

        window.myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Suma z Kwota netto',
                    data: dataNetto,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Suma z Kwota Brutto',
                    data: dataBrutto,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'rgb(0, 0, 0)', // Ustawienie koloru tekstu legendy
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        grid: {
                            display: false // Ukrycie linii siatki na osi X
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 30,
                        bottom: 20
                    }
                },
                maintainAspectRatio: false, // Pozwala na elastyczne skalowanie wykresu
                responsive: true,
                backgroundColor: 'white' // Tło wykresu
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function showDownloadButton(startDate, endDate) {
    var downloadButton = document.createElement('button');
    downloadButton.textContent = 'Pobierz plik';
    downloadButton.addEventListener('click', function() {
        var formData = new FormData();
        formData.append('startDate', startDate);
        formData.append('endDate', endDate);
        directoryPath = 'uploads/';
        fetch('includes/raport.inc.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.file) {
                var downloadLink = document.createElement('a');
                window.location.href = directoryPath + data.file;
                downloadLink.download = data.file;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            } else {
                // Obsługa błędów, np. pokaż komunikat
            }
        })
        .catch(error => {
            console.error('Błąd:', error);
        });
    });

    var container = document.getElementById('tableContainer');
    container.appendChild(downloadButton);
}
