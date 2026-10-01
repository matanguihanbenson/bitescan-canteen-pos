<script>
document.addEventListener('DOMContentLoaded', function() {
    loadStockTrendData();

    const categoryCtx = document.getElementById('categoryDistributionChart');
    if (categoryCtx) {
        fetch('/categories/distribution')
            .then(response => response.json())
            .then(categories => {
                const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
                const total = categories.reduce((sum, cat) => sum + cat.count, 0);
                
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categories.map(cat => {
                            const percentage = ((cat.count / total) * 100).toFixed(1);
                            return `${cat.name} (${percentage}%)`;
                        }),
                        datasets: [{
                            data: categories.map(cat => cat.count),
                            backgroundColor: colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    boxWidth: 12,
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length > 6) {
                                            return data.labels.slice(0, 6).map((label, i) => ({
                                                text: label,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                lineWidth: 0,
                                                index: i
                                            }));
                                        }
                                        return data.labels.map((label, i) => ({
                                            text: label,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            lineWidth: 0,
                                            index: i
                                        }));
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        const label = context.label.split(' (')[0];
                                        return `${label}: ${value} products (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    },
                    plugins: [{
                        id: 'doughnutLabels',
                        afterDraw(chart) {
                            const {ctx, data} = chart;
                            ctx.save();
                            const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
                            
                            ctx.font = '16px Arial';
                            ctx.fillStyle = '#666';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(`Total Products`, chart.getDatasetMeta(0).data[0].x, chart.getDatasetMeta(0).data[0].y - 15);
                            ctx.fillText(`${total}`, chart.getDatasetMeta(0).data[0].x, chart.getDatasetMeta(0).data[0].y + 15);
                            
                            ctx.restore();
                        }
                    }]
                });
            })
            .catch(error => {
                categoryCtx.parentElement.innerHTML = '<div class="alert alert-danger">Error loading category distribution data</div>';
            });
    }

    $('#inventoryTable').DataTable({
        pageLength: 10,
        order: [[3, 'desc']],
        language: {
            emptyTable: "No inventory items found"
        }
    });

    fetch('/inventory/summary')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            $('#totalInventoryValue').text('₱' + data.total_value);
            $('#totalProducts').text(data.total_products);
            $('#lowStockCount').text(data.low_stock);
            $('#outOfStockCount').text(data.out_of_stock);
            
            const changeValue = data.value_change;
            const changeElement = $('#valueChange');
            changeElement.text((changeValue > 0 ? '+' : '') + changeValue + '%');
            changeElement.removeClass('text-success text-danger');
            changeElement.addClass(changeValue >= 0 ? 'text-success' : 'text-danger');

            // Toggle empty inventory banner
            if ((parseInt(data.total_products, 10) || 0) === 0) {
                $('#inventory-empty-banner').removeClass('d-none');
            } else {
                $('#inventory-empty-banner').addClass('d-none');
            }
        })
        .catch(error => {
            $('.card-body').prepend(
                '<div class="alert alert-warning">Error loading summary data</div>'
            );
        });
});

function loadStockTrendData(period = 'weekly') {
    const container = document.getElementById('stockTrendChartContainer');
    const oldCanvas = document.getElementById('stockTrendChart');
    const loading = document.getElementById('stockTrendLoading');
    const error = document.getElementById('stockTrendError');

    oldCanvas.style.display = 'none';
    error.classList.add('d-none');
    loading.classList.remove('d-none');

    if (window.stockTrendChart instanceof Chart) {
        window.stockTrendChart.destroy();
        window.stockTrendChart = null;
    }

    const newCanvas = document.createElement('canvas');
    newCanvas.id = 'stockTrendChart';
    oldCanvas.parentNode.replaceChild(newCanvas, oldCanvas);

    fetch(`/inventory/stock-trends?period=${period}`)
        .then(async response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data || !data.datasets) {
                throw new Error('No data available');
            }

            loading.classList.add('d-none');
            newCanvas.style.display = 'block';

            window.stockTrendChart = new Chart(newCanvas, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value} units`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Stock Movement Quantity'
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            loading.classList.add('d-none');
            newCanvas.style.display = 'none';
            const errorElement = document.getElementById('stockTrendError');
            errorElement.classList.remove('d-none');
            errorElement.querySelector('p').textContent = `Error loading stock trend data: ${error.message}`;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const periodButtons = document.querySelectorAll('.period-selector button');
    periodButtons.forEach(button => {
        button.addEventListener('click', function() {
            periodButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            loadStockTrendData(this.dataset.period);
        });
    });
    
    loadStockTrendData();
});
</script>
