<?php
include 'session.php';
include '../../includes/database.php';

// Total Donations
$totalDonationsQuery = "SELECT COUNT(*) AS total FROM donations WHERE status = 'claimed'";
$totalDonationsResult = mysqli_query($conn, $totalDonationsQuery);
$totalDonations = mysqli_fetch_assoc($totalDonationsResult)['total'] ?? 0;

// Donations by status
$statusQuery = "SELECT status, COUNT(*) AS count FROM donations GROUP BY status";
$statusResult = mysqli_query($conn, $statusQuery);
$statusLabels = [];
$statusCounts = [];
while ($row = mysqli_fetch_assoc($statusResult)) {
    $statusLabels[] = ucfirst($row['status']);
    $statusCounts[] = $row['count'];
}

// Top Donors
$topDonorsQuery = "SELECT donor_id, COUNT(*) AS donation_count FROM donations GROUP BY donor_id ORDER BY donation_count DESC LIMIT 5";
$topDonorsResult = mysqli_query($conn, $topDonorsQuery);

// Top Receivers
$topReceiversQuery = "SELECT receiver_id, COUNT(*) AS donation_count FROM donations WHERE status = 'claimed' GROUP BY receiver_id ORDER BY donation_count DESC LIMIT 5";
$topReceiversResult = mysqli_query($conn, $topReceiversQuery);

// Monthly Donations
$monthlyQuery = "SELECT MONTH(created_at) AS month, COUNT(*) AS count FROM donations WHERE status = 'claimed' GROUP BY MONTH(created_at)";
$monthlyResult = mysqli_query($conn, $monthlyQuery);
$monthlyData = array_fill(1, 12, 0); // Jan to Dec
while ($row = mysqli_fetch_assoc($monthlyResult)) {
    $monthlyData[(int)$row['month']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SAHYOG Admin Report</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        h2 {
            font-size: 1.6rem;
        }
        .card-title {
            font-size: 1.1rem;
        }
        .display-5 {
            font-size: 2rem;
            color: #fff !important;
        }
        .btn-report-link {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        .card {
            border-radius: 0.75rem;
        }
        canvas {
            max-height: 250px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container my-4 position-relative">
        <a href="admin.php" class="btn btn-outline-secondary btn-sm btn-report-link">← Back to Dashboard</a>
        <h2 class="text-center mb-4">📊 SAHYOG - Donation Statistics Report</h2>

        <div class="row g-4">

            <!-- Total Donations -->
            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm" style="min-height: 120px;">
                    <div class="card-body text-center d-flex flex-column justify-content-center p-3">
                        <h5 class="card-title mb-2">Total Donations</h5>
                        <p class="display-5 m-0"><?php echo $totalDonations; ?></p>
                    </div>
                </div>
            </div>

            <!-- Donations by Status Chart -->
            <div class="col-md-8">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center">Donations by Status</h5>
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Donors -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center">🏅 Top 5 Donors</h5>
                        <ul class="list-group list-group-flush">
                            <?php while ($row = mysqli_fetch_assoc($topDonorsResult)) { ?>
                                <li class="list-group-item small">Donor ID: <strong><?php echo $row['donor_id']; ?></strong> – Donations: <?php echo $row['donation_count']; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Top Receivers -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center">🎖️ Top 5 Receivers</h5>
                        <ul class="list-group list-group-flush">
                            <?php while ($row = mysqli_fetch_assoc($topReceiversResult)) { ?>
                                <li class="list-group-item small">Receiver ID: <strong><?php echo $row['receiver_id']; ?></strong> – Received: <?php echo $row['donation_count']; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Monthly Donations Chart -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center">📅 Monthly Donations (This Year)</h5>
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

<script>
    // Donations by Status - Pie Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($statusLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($statusCounts); ?>,
                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Monthly Donations - Bar Chart
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Donations',
                data: <?php echo json_encode(array_values($monthlyData)); ?>,
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>

</body>
</html>
