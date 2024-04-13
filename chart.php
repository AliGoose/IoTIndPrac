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
?>


<!DOCTYPE html>
<html>
<head>
    <title>Message Frequency Visualization</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

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
    </script>
</head>
<body>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
</body>
</html>
