<?php
// Database connection details
$host = 'localhost';
$username = 'root'; // Your MariaDB username
$password = 'password123'; // Your MariaDB password
$database = 'iot_db';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to fetch temperature data and prepare frequency data
$tempQuery = "SELECT temperature, COUNT(*) as freq FROM SensorData GROUP BY temperature ORDER BY temperature";
$tempResult = $conn->query($tempQuery);

$tempData = [];
while ($row = $tempResult->fetch_assoc()) {
    $tempData[] = [(float)$row["temperature"], (int)$row["freq"]];
}

// SQL to fetch light level data and prepare frequency data
$lightQuery = "SELECT light_level, COUNT(*) as freq FROM SensorData GROUP BY light_level ORDER BY light_level";
$lightResult = $conn->query($lightQuery);

$lightData = [];
while ($row = $lightResult->fetch_assoc()) {
    $lightData[] = [(float)$row["light_level"], (int)$row["freq"]];
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sensor Data Visualization</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            drawTemperatureChart();
            drawLightChart();
        }

        function drawTemperatureChart() {
            var tempData = google.visualization.arrayToDataTable([
                ['Temperature', 'Frequency'],
                <?php foreach ($tempData as $item) echo "['" . $item[0] . "', " . $item[1] . "],"; ?>
            ]);

            var tempOptions = {
                title: 'Temperature Frequency',
                hAxis: {title: 'Temperature (°C)'},
                vAxis: {title: 'Frequency'},
                legend: 'none'
            };

            var tempChart = new google.visualization.ColumnChart(document.getElementById('temp_chart_div'));
            tempChart.draw(tempData, tempOptions);
        }

        function drawLightChart() {
            var lightData = google.visualization.arrayToDataTable([
                ['Light Level', 'Frequency'],
                <?php foreach ($lightData as $item) echo "['" . $item[0] . "', " . $item[1] . "],"; ?>
            ]);

            var lightOptions = {
                title: 'Light Level Frequency',
                hAxis: {title: 'Light Level'},
                vAxis: {title: 'Frequency'},
                legend: 'none'
            };

            var lightChart = new google.visualization.ColumnChart(document.getElementById('light_chart_div'));
            lightChart.draw(lightData, lightOptions);
        }
    </script>
</head>
<body>
    <h2>Temperature Data</h2>
    <div id="temp_chart_div" style="width: 900px; height: 500px;"></div>
    <h2>Light Level Data</h2>
    <div id="light_chart_div" style="width: 900px; height: 500px;"></div>
</body>
</html>
