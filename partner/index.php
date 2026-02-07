<?php
$page = 'dashboard';
include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Partner Dashboard</h1>
</div>

<!-- Widgets -->
<div class="dashboard-widgets">
    <div class="widget-card">
        <div class="widget-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="widget-info">
            <h3>42</h3>
            <p>Total Sales</p>
        </div>
    </div>
    
    <div class="widget-card">
        <div class="widget-icon green">
            <i class="fas fa-rupee-sign"></i>
        </div>
        <div class="widget-info">
            <h3>₹8,450</h3>
            <p>My Earnings</p>
        </div>
    </div>
    
    <div class="widget-card">
        <div class="widget-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="widget-info">
            <h3>5</h3>
            <p>Pending Orders</p>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="charts-row">
    <div class="chart-card">
        <div class="card-header">
            <h3 class="card-title">Sales Performance</h3>
        </div>
        <canvas id="salesChart" height="200"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Sales (₹)',
                data: [1500, 2300, 1800, 2850],
                backgroundColor: '#2271b1',
                borderRadius: 4
            }]
        },
        options: { responsive: true }
    });
</script>

<?php include 'includes/footer.php'; ?>
