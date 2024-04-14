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
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Timestamp');
            data.addColumn('number', 'Temperature');
            data.addRows(<?php echo json_encode($tempData); ?>);

            var options = {
                title: 'Temperature Over Time',
                hAxis: {title: 'Time'},
                vAxis: {title: 'Temperature (°C)'},
                legend: 'none'
            };

            var chart = new google.visualization.LineChart(document.getElementById('temp_chart_div'));
            chart.draw(data, options);
        }

        function drawLightChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Timestamp');
            data.addColumn('number', 'Light Level');
            data.addRows(<?php echo json_encode($lightData); ?>);

            var options = {
                title: 'Light Level Over Time',
                hAxis: {title: 'Time'},
                vAxis: {title: 'Light Level'},
                legend: 'none'
            };

            var chart = new google.visualization.LineChart(document.getElementById('light_chart_div'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <div id="temp_chart_div" style="width: 900px; height: 500px;"></div>
    <div id="light_chart_div" style="width: 900px; height: 500px;"></div>
</body>
</html>

<?php
// Database connection details
$host = 'localhost';
$username = 'root'; // Your MariaDB username
$password = 'password123'; // Your MariaDB password
$database = 'test';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to fetch temperature data
$tempQuery = "SELECT temperature, timestamp FROM SensorData ORDER BY timestamp DESC LIMIT 100";
$tempResult = $conn->query($tempQuery);

$tempData = [];
while ($row = $tempResult->fetch_assoc()) {
    $tempData[] = [$row["timestamp"], (float) $row["temperature"]];
}

// SQL to fetch light level data
$lightQuery = "SELECT light_level, timestamp FROM SensorData ORDER BY timestamp DESC LIMIT 100";
$lightResult = $conn->query($lightQuery);

$lightData = [];
while ($row = $lightResult->fetch_assoc()) {
    $lightData[] = [$row["timestamp"], (int) $row["light_level"]];
}

$conn->close();
?>