<?php


?>


<html>
    <head>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Library</title>
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV3/styles/styles.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <center>
    <h1>Book Metric Charts</h1>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <script>
        async function fetchMetricData()
{
    const response = await fetch('fetchMetric.php');
    const data = await response.json();
    return data;
}

//id, read_time, page_length, book_rating

async function renderChart()
{
    const metricData = await fetchMetricData();

    //Processing Data
    const labels = metricData.map(item => item.id + item.page_length);
    const values = metricData.map(item => item.reading_time + item.book_rating);

    //Create Chart
    const ctx = document.getElementById('metricChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Book Metric Value',
                data: values,
                borderWidth: 1,
                backgroundColor: grey,
                borderColor: blue
            }]
        },

        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

renderChart();
    </script>    


    <center>
    <button type="submit" class="button is-primary" onclick="window.location.href='dashboard.php'">Dashboard</button>
    </center>

    </head>

    <body>
    <canvas id="metricChart" style="width=400%;max-width: 500px"> </canvas>

    </center>
    </body>
</html>