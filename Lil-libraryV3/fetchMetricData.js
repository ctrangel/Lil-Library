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
        type: 'doughnut',
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