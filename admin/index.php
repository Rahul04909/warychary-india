<?php
$page = 'dashboard';
include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <a href="#" class="btn-admin">
        <i class="fas fa-plus"></i> Add New
    </a>
</div>

<!-- Widgets -->
<div class="dashboard-widgets">
    <div class="widget-card">
        <div class="widget-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="widget-info">
            <h3>1,250</h3>
            <p>Total Users</p>
        </div>
    </div>
    
    <div class="widget-card">
        <div class="widget-icon green">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="widget-info">
            <h3>340</h3>
            <p>Published Posts</p>
        </div>
    </div>
    
    <div class="widget-card">
        <div class="widget-icon orange">
            <i class="fas fa-comments"></i>
        </div>
        <div class="widget-info">
            <h3>85</h3>
            <p>Pending Comments</p>
        </div>
    </div>
    
    <div class="widget-card">
        <div class="widget-icon red">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="widget-info">
            <h3>5</h3>
            <p>System Alerts</p>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="charts-row">
    <div class="chart-card">
        <div class="card-header">
            <h3 class="card-title">Traffic Overview</h3>
            <select style="padding: 4px; border: 1px solid #ddd; border-radius: 4px;">
                <option>Last 30 Days</option>
                <option>Last 7 Days</option>
            </select>
        </div>
        <canvas id="trafficChart" height="200"></canvas>
    </div>
    
    <div class="chart-card">
        <div class="card-header">
            <h3 class="card-title">User Signups</h3>
        </div>
        <canvas id="userChart" height="200"></canvas>
    </div>
</div>

<!-- Recent Activity -->
<div class="charts-row">
    <div class="table-card" style="grid-column: span 2;">
        <div class="card-header" style="padding: 20px;">
            <h3 class="card-title">Recent Activity</h3>
            <a href="#" style="font-size: 13px; color: var(--primary-color);">View All</a>
        </div>
        <div class="table-responsive">
            <table class="wp-list-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Oct 24, 2023</td>
                        <td><strong>John Doe</strong></td>
                        <td>Created a new post "Top 10 Health Tips"</td>
                        <td><span class="status-badge status-published">Published</span></td>
                        <td><a href="#" style="color: var(--primary-color);">View</a></td>
                    </tr>
                    <tr>
                        <td>Oct 24, 2023</td>
                        <td><strong>Sarah Smith</strong></td>
                        <td>Updated profile information</td>
                        <td><span class="status-badge status-pending">Pending</span></td>
                        <td><a href="#" style="color: var(--primary-color);">Review</a></td>
                    </tr>
                    <tr>
                        <td>Oct 23, 2023</td>
                        <td><strong>Mike Jones</strong></td>
                        <td>Commented on "Wellness Guide"</td>
                        <td><span class="status-badge status-draft">Approved</span></td>
                        <td><a href="#" style="color: var(--primary-color);">Edit</a></td>
                    </tr>
                    <tr>
                        <td>Oct 22, 2023</td>
                        <td><strong>Admin</strong></td>
                        <td>System backup completed</td>
                        <td><span class="status-badge status-published">Success</span></td>
                        <td><a href="#" style="color: var(--primary-color);">Details</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Initialize Charts
    const ctxTraffic = document.getElementById('trafficChart').getContext('2d');
    const trafficChart = new Chart(ctxTraffic, {
        type: 'line',
        data: {
            labels: ['1 Oct', '5 Oct', '10 Oct', '15 Oct', '20 Oct', '25 Oct'],
            datasets: [{
                label: 'Visitors',
                data: [120, 190, 300, 250, 400, 450],
                borderColor: '#2271b1',
                backgroundColor: 'rgba(34, 113, 177, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f0f1' } },
                x: { grid: { display: false } }
            }
        }
    });

    const ctxUser = document.getElementById('userChart').getContext('2d');
    const userChart = new Chart(ctxUser, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            datasets: [{
                label: 'Signups',
                data: [12, 19, 3, 5, 10],
                backgroundColor: [
                    '#2271b1', '#135e96', '#72aee6', '#2271b1', '#135e96'
                ],
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
