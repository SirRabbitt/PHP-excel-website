<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="tabale.css">


</head>

<body>
    <?php
    if (isset($_SESSION["userid"])) {
        // Menu dla zalogowanych użytkowników
    ?>
        <header>
            <nav>
                <div>
                    <ul class="menu-main">
                        <li><a href="index.php">Zmiana hasła użytkownika</a></li>
                        <li><a href="raport.php">Raport sprzedaży wg grup produktów w wybranym zakresie dat</a></li>
                        <li><a href="sellyears.php">Zestawienie sprzedaży wg grup produktów, rok do roku</a></li>
                    </ul>
                </div>
                <ul class="menu-member">
                    <li><a href="#"> <?php echo $_SESSION["useruid"]; ?></a></li>
                    <li><a href="includes/logout.inc.php" class="header-login-a">Wyloguj</a></li>
                </ul>
            </nav>
        </header>

        <form id="salesReportForm">
            <div class="form-group">
                <label for="startDate">Od dnia:</label>
                <input type="date" id="startDate" name="startDate" required>
            </div>
            <div class="form-group">
                <label for="endDate">Do dnia:</label>
                <input type="date" id="endDate" name="endDate" required>
            </div>
            <button type="submit" id="generateReport">Wykonaj Raport</button>
        </form>

        <div class="flex-container">
            <div id="tableContainer">
                <!-- Tu pojawi się tabela -->
            </div>
            <canvas id="myChart" width="100" height="100"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="generateTable.js"></script>


    <?php
    }
    // Menu dla niezalogowanych użytkowników
    ?>
</body>
<style>
    .flex-container {
        display: flex;
        justify-content: center;

    }

    #tableContainer,
    #myChart {
        flex: 1;
        margin: 10px;
        max-width: 50%;
        background-color: #f4f4f4;
        max-height: 50%;
    }

    #salesReportForm {
        background-color: #f2f2f2;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-right: 20px;
    }

    label,
    input,
    button {
        font-size: 1.2em;
    }

    input[type="date"],
    button {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }

    button {
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>

</html>