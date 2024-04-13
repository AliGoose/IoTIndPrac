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

// SQL to fetch data
$sql = "SELECT message FROM Ard";
$result = $conn->query($sql);

// Prepare data for chart
$numericValues = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Extracting numeric value from message
        preg_match('/\d+/', $row["message"], $matches);
        $numericValue = $matches[0] ?? 0; // Default to 0 if no numeric part found
        if (!isset($numericValues[$numericValue])) {
            $numericValues[$numericValue] = 0;
        }
        $numericValues[$numericValue]++;
    }
} else {
    echo "0 results";
}

// Sort numeric values by key (numeric value)
ksort($numericValues);

// Statistics
$mean = $median = $mode = 0;
$values = array_keys($numericValues);
$frequencies = array_values($numericValues);
$totalValues = array_sum($frequencies);
$countValues = count($values);

if ($countValues > 0) {
    $mean = array_sum($values) / count($values);

    $mid = floor((count($values) - 1) / 2);
    $median = ($values[$mid] + $values[$mid + (count($values) % 2)]) / 2;

    $maxFreq = max($frequencies);
    $modes = array_keys($numericValues, $maxFreq);
    $mode = implode(", ", $modes);
}

$conn->close();

// Prepare chart data
$chartData = [];
foreach ($numericValues as $value => $freq) {
    $chartData[] = [(string)$value, $freq];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Message Value Analysis</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        var mean = <?php echo json_encode($mean); ?>;
        var median = <?php echo json_encode($median); ?>;
        var mode = <?php echo json_encode($mode); ?>;

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Message Value', 'Frequency'],
                <?php foreach ($chartData as $item) {
                    echo "[" . $item[0] . ", " . $item[1] . "],";
                } ?>
            ]);

            var options = {
                title: 'Frequency of Message Values',
                hAxis: {title: 'values', titleTextStyle: {color: '#333'}},
                vAxis: {title: 'Frequency', minValue: 0},
                legend: 'none',
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
    <button onclick="displayStatistics()">Generate Data Analytics</button>
    <div id="mean"></div>
    <div id="median"></div>
    <div id="mode"></div>
</body>
</html>
