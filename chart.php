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

// SQL to fetch and count data
$sql = "SELECT message, COUNT(*) as frequency FROM Ard GROUP BY message";
$result = $conn->query($sql);

// Prepare data for chart
$chartData = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $chartData[] = [(string)$row["message"], (int)$row["frequency"]];
    }
} else {
    echo "0 results";
}

$conn->close();

$mean = 0;
$median = 0;
$mode = 0;

$frequencies = array_column($chartData, 1);
$totalValues = array_sum($frequencies);
$countValues = count($frequencies);

if ($countValues > 0) {
    $mean = $totalValues / $countValues;

    sort($frequencies);
    $mid = floor(($countValues - 1) / 2);
    $median = ($frequencies[$mid] + $frequencies[$mid + ($countValues % 2)]) / 2;

    $frequencyCounts = array_count_values($frequencies);
    $maxFrequency = max($frequencyCounts);
    $modes = array_keys($frequencyCounts, $maxFrequency);
    $mode = implode(", ", $modes);
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Message Frequency Visualization</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        var mean = <?php echo json_encode($mean); ?>;
        var median = <?php echo json_encode($median); ?>;
        var mode = <?php echo json_encode($mode); ?>;

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Message');
            data.addColumn('number', 'Frequency');
            data.addRows(<?php echo json_encode($chartData); ?>);

            var options = {
                title: 'Message Frequency',
                hAxis: {title: 'Message'},
                vAxis: {title: 'Frequency'},
                legend: 'none',
                bar: {groupWidth: "95%"},
                chartArea: {width: "50%", height: "70%"}
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }

        function displayStatistics() {
            document.getElementById('mean').textContent = 'Mean: ' + mean.toFixed(2);
            document.getElementById('median').textContent = 'Median: ' + median.toFixed(2);
            document.getElementById('mode').textContent = 'Mode: ' + mode;
        }
    </script>
</head>
<body>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
    <button onclick="displayStatistics()">Generate</button>
    <div id="mean"></div>
    <div id="median"></div>
    <div id="mode"></div>
</body>
</html>
