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

// Prepare data for chart and extract numeric values
$chartData = [];
$numericValues = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Extracting numeric value from message assuming format "Keyword: Value"
        // Adjust regex as per your actual data format
        preg_match('/\d+/', $row["message"], $matches);
        $numericValue = $matches[0] ?? 0; // Default to 0 if no numeric part found
        $numericValues[] = (int) $numericValue;
        $chartData[] = [$row["message"], (int) $numericValue];
    }
} else {
    echo "0 results";
}

// Calculate statistics
$mean = $median = $mode = 0;
if (count($numericValues) > 0) {
    $mean = array_sum($numericValues) / count($numericValues);

    sort($numericValues);
    $mid = floor((count($numericValues) - 1) / 2);
    $median = ($numericValues[$mid] + $numericValues[$mid + (count($numericValues) % 2)]) / 2;

    $valuesCount = array_count_values($numericValues);
    $maxFreq = max($valuesCount);
    $modes = array_keys($valuesCount, $maxFreq);
    $mode = implode(", ", $modes);
}

$conn->close();
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
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Message');
            data.addColumn('number', 'Value');

            data.addRows(<?php echo json_encode($chartData); ?>);

            var options = {
                title: 'Message Values',
                hAxis: {title: 'Message'},
                vAxis: {title: 'Value'},
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
