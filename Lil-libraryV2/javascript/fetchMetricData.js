async function fetchMetricData()
{
    const response = await fetch('../fetchMetrics.php');
    const data = await response.json();
    return data;
}

async function renderChart()
{
    const metricData = await fetchMetricData();

    //Processing Data
    const labels = metricData.map(item => item.metric_length + ' (' + new Date(item.created_at).toLocaleDateString() + ')');
    const values = metricData.map(item => item.reading_time);

    //Create Chart
    const ctx = document.getElementById('metricChart').getContext('2d');
    new Chart(ctx, {
        type: 'grid',
        data: {
            labels: labels,
            datasets: [{
                label: 'Metric Value',
                data: values,
                borderWidth: 1,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)'
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