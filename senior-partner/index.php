<?php
$page = 'dashboard';
include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Senior Partner Dashboard</h1>
</div>

<!-- Widgets -->
<div class="dashboard-widgets">
    <div class="widget-card">
        <div class="widget-icon">
            <i class="fas fa-handshake"></i>
        </div>
        <div class="widget-info">
            <h3>15</h3>
            <p>Active Partners</p>
        </div>
    </div>
    
    <div class="widget-card">
        <div class="widget-icon green">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="widget-info">
            <h3>₹45,200</h3>
            <p>Total Earnings</p>
        </div>
    </div>
    
    <div class="widget-card">
        <div class="widget-icon orange">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="widget-info">
            <h3>120</h3>
            <p>Team Sales</p>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="charts-row">
    <div class="chart-card">
        <div class="card-header">
            <h3 class="card-title">Earnings Overview</h3>
        </div>
        <canvas id="earningsChart" height="200"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            datasets: [{
                label: 'Earnings (₹)',
                data: [12000, 19000, 15000, 25000, 45200],
                borderColor: '#2271b1',
                backgroundColor: 'rgba(34, 113, 177, 0.1)',
                fill: true
            }]
        },
        options: { responsive: true }
    });
</script>

<?php include 'includes/footer.php'; ?>
