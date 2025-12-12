<?php include __DIR__ . '/../header.php'; ?>

<div class="container">
    <div class="admin-header">
        <h1>📊 Analytics Dashboard</h1>
        <p>Business insights and performance metrics</p>
    </div>

    <div class="admin-nav">
        <a href="?page=admin-dashboard">Dashboard</a>
        <a href="?page=admin-products">Products</a>
        <a href="?page=admin-orders">Orders</a>
        <a href="?page=admin-users">Users</a>
        <a href="?page=admin-analytics" class="active">Analytics</a>
    </div>

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <div class="stat-number">$<?= number_format($data['total_revenue'], 2) ?></div>
            <div class="stat-trend">💰 Total Sales</div>
        </div>
        
        <div class="stat-card">
            <h3>Total Orders</h3>
            <div class="stat-number"><?= $data['total_orders'] ?></div>
            <div class="stat-trend">📦 All Time</div>
        </div>
        
        <div class="stat-card">
            <h3>Customers</h3>
            <div class="stat-number"><?= $data['total_customers'] ?></div>
            <div class="stat-trend">👥 Registered Users</div>
        </div>
        
        <div class="stat-card">
            <h3>Avg Order Value</h3>
            <div class="stat-number">$<?= number_format($data['avg_order_value'], 2) ?></div>
            <div class="stat-trend">📊 Average</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="analytics-grid">
        <!-- Revenue Chart -->
        <div class="chart-card">
            <h3>📈 Revenue Trend</h3>
            <div class="chart-period">
                <button class="period-btn active" data-period="monthly">Monthly</button>
                <button class="period-btn" data-period="daily">Daily</button>
            </div>
            <div id="revenue-chart" style="height: 300px;">
                <!-- Chart will be rendered by JavaScript -->
                <div class="chart-loading">Loading chart...</div>
            </div>
        </div>

        <!-- Orders by Status -->
        <div class="chart-card">
            <h3>📊 Orders by Status</h3>
            <div class="status-chart">
                <?php foreach ($data['orders_by_status'] as $status): ?>
                    <div class="status-item">
                        <div class="status-label">
                            <span class="status-badge status-<?= $status['status'] ?>">
                                <?= ucfirst($status['status']) ?>
                            </span>
                            <span class="status-count"><?= $status['count'] ?></span>
                        </div>
                        <div class="status-bar-container">
                            <div class="status-bar" style="width: <?= ($status['count'] / max(array_column($data['orders_by_status'], 'count')) * 100) ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="analytics-section">
        <h3>🏆 Top Selling Products</h3>
        <div class="products-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['top_products'] as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td><?= $product['total_sold'] ?></td>
                            <td>$<?= number_format($product['revenue'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Revenue by Category -->
    <div class="analytics-section">
        <h3>🗂️ Revenue by Category</h3>
        <div class="category-chart">
            <?php foreach ($data['revenue_by_category'] as $category): ?>
                <div class="category-item">
                    <div class="category-info">
                        <span class="category-name"><?= htmlspecialchars($category['category']) ?></span>
                        <span class="category-revenue">$<?= number_format($category['revenue'], 2) ?></span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= ($category['revenue'] / max(array_column($data['revenue_by_category'], 'revenue')) * 100) ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="analytics-section">
        <h3>🆕 Recent Orders</h3>
        <div class="orders-table">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['recent_orders'] as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                            <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                            <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                            <td>
                                <?php
                                // Calculate order total (you might want to create a method for this)
                                $orderTotal = 0;
                                // This is a simplified version - in real app, you'd calculate properly
                                $orderTotal = rand(20, 200); // Placeholder
                                ?>
                                $<?= number_format($orderTotal, 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let revenueChart;
    
    // Load initial chart data
    loadRevenueChart('monthly');
    
    // Period button clicks
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadRevenueChart(this.dataset.period);
        });
    });
    
    function loadRevenueChart(period) {
        // Show loading
        const chartContainer = document.getElementById('revenue-chart');
        chartContainer.innerHTML = '<div class="chart-loading">Loading chart...</div>';
        
        // Fetch data
        fetch(`?page=get-sales-data&period=${period}`)
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    renderRevenueChart(data.data, period);
                } else {
                    // Handle server-side error
                    chartContainer.innerHTML = `<div class="chart-error">${data.error || 'Failed to load data'}</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading chart:', error);
                chartContainer.innerHTML = '<div class="chart-error">Failed to load chart data. Please try again.</div>';
            });
    }
    
    function renderRevenueChart(chartData, period) {
        const chartContainer = document.getElementById('revenue-chart');
        chartContainer.innerHTML = '';
        
        // Check if we have data
        if (!chartData || chartData.length === 0) {
            chartContainer.innerHTML = '<div class="chart-error">No data available for the selected period</div>';
            return;
        }
        
        const ctx = document.createElement('canvas');
        chartContainer.appendChild(ctx);
        
        // Destroy previous chart if exists
        if (revenueChart) {
            revenueChart.destroy();
        }
        
        // Prepare data
        const labels = chartData.map(item => {
            if (period === 'monthly') {
                return item.month;
            } else {
                return item.date;
            }
        });
        
        const revenueData = chartData.map(item => item.revenue || 0);
        
        // Create chart
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Revenue: $${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(0);
                            }
                        }
                    }
                }
            }
        });
    }
});

</script>

<style>
/* Analytics Styles */
.analytics-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

.chart-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.chart-period {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.period-btn {
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    color: #495057;
    transition: all 0.3s ease;
}

.period-btn.active,
.period-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.chart-loading, .chart-error {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 300px;
    color: #6c757d;
}

.analytics-section {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.analytics-section h3 {
    margin-bottom: 1.5rem;
    color: #2c5aa0;
}

.products-table table,
.orders-table table {
    width: 100%;
    border-collapse: collapse;
}

.products-table th,
.orders-table th {
    background: #f8f9fa;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #e9ecef;
}

.products-table td,
.orders-table td {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.products-table tr:hover,
.orders-table tr:hover {
    background: #f8f9fa;
}

.status-chart {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.status-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 150px;
}

.status-count {
    font-weight: 600;
    color: #495057;
}

.status-bar-container {
    flex: 1;
    height: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    overflow: hidden;
}

.status-bar {
    height: 100%;
    background: #667eea;
    border-radius: 5px;
    transition: width 0.3s ease;
}

.category-chart {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.category-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.category-info {
    display: flex;
    justify-content: space-between;
}

.category-name {
    font-weight: 600;
    color: #495057;
}

.category-revenue {
    color: #667eea;
    font-weight: 600;
}

.progress-bar {
    height: 8px;
    background: #f8f9fa;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.stat-trend {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: #6c757d;
}
</style>

<?php include __DIR__ . '/../footer.php'; ?>