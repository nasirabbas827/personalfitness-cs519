<?php
session_start();
// NOTE: Assuming config.php establishes a valid mysqli connection in $conn
include('config.php');

// 1. Authentication Check and Admin ID setup
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    // Note: Assuming 'usertype' is set to 'admin' upon successful admin login
    header("Location: admin_login.php");
    exit;
}

// 2. Fetch Dashboard Statistics

// a. Total Users (Regular and Trainers)
$sql_total_users = "SELECT COUNT(id) AS total_users FROM users";
$total_users = $conn->query($sql_total_users)->fetch_assoc()['total_users'] ?? 0;

// b. Total Trainers
$sql_total_trainers = "SELECT COUNT(id) AS total_trainers FROM users WHERE role = 'trainer'";
$total_trainers = $conn->query($sql_total_trainers)->fetch_assoc()['total_trainers'] ?? 0;

// c. Total Public Routines
$sql_total_routines = "SELECT COUNT(id) AS total_routines FROM public_routines";
$total_routines = $conn->query($sql_total_routines)->fetch_assoc()['total_routines'] ?? 0;

// d. Total Fitness Tips
$sql_total_tips = "SELECT COUNT(id) AS total_tips FROM fitness_tips";
$total_tips = $conn->query($sql_total_tips)->fetch_assoc()['total_tips'] ?? 0;

// e. Total Workouts Logged (Lifetime)
$sql_total_workouts = "SELECT COUNT(id) AS total_workouts FROM daily_workouts";
$total_workouts = $conn->query($sql_total_workouts)->fetch_assoc()['total_workouts'] ?? 0;

// f. Total Meals Logged (Lifetime)
$sql_total_meals = "SELECT COUNT(id) AS total_meals FROM daily_meals";
$total_meals = $conn->query($sql_total_meals)->fetch_assoc()['total_meals'] ?? 0;

// 3. Fetch Data for Charts

// a. User Roles Data
$sql_roles_data = "SELECT role, COUNT(id) as count FROM users GROUP BY role";
$result_roles = $conn->query($sql_roles_data);
$user_roles_labels = [];
$user_roles_data = [];
while ($row = $result_roles->fetch_assoc()) {
    $user_roles_labels[] = ucfirst($row['role']);
    $user_roles_data[] = $row['count'];
}

// b. Weekly Workout Trend (Last 7 Days)
$orders_dates = [];
$orders_counts = [];
$date_range = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $orders_dates[] = date('D', strtotime($date)); // Day name for chart label
    $date_range[] = $date;
}

$date_list = "'" . implode("','", $date_range) . "'";
$sql_workouts_trend = "
    SELECT 
        log_date,
        COUNT(id) AS daily_workouts_count
    FROM daily_workouts 
    WHERE log_date IN ($date_list)
    GROUP BY log_date
    ORDER BY log_date ASC";

$result_trend = $conn->query($sql_workouts_trend);
$workouts_map = array_fill_keys($date_range, 0);

while ($row = $result_trend->fetch_assoc()) {
    $workouts_map[$row['log_date']] = $row['daily_workouts_count'];
}

// Populate the final chart data arrays in the correct chronological order
foreach ($date_range as $date) {
    $orders_counts[] = $workouts_map[$date];
}

// c. Feedback Rating Data
$sql_feedback_rating = "SELECT rating, COUNT(id) AS count FROM feedback GROUP BY rating ORDER BY rating ASC";
$result_feedback = $conn->query($sql_feedback_rating);
$reviews_rating_data = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
while ($row = $result_feedback->fetch_assoc()) {
    $reviews_rating_data[$row['rating']] = $row['count'];
}


// 4. Fetch Recent Activity

// a. Recent Users (Last 5)
$sql_recent_users = "SELECT name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users = $conn->query($sql_recent_users);

// b. Recent Tips (Last 5)
$sql_recent_tips = "SELECT title, created_at FROM fitness_tips ORDER BY created_at DESC LIMIT 5";
$recent_tips = $conn->query($sql_recent_tips);

// c. Recent Routine Feedback (Last 5)
$sql_recent_feedback = "
    SELECT 
        f.id AS feedback_id, 
        u.name AS user_name, 
        pr.title AS routine_title, 
        f.rating, 
        f.comment, 
        f.created_at
    FROM feedback f
    JOIN users u ON f.user_id = u.id
    LEFT JOIN public_routines pr ON f.routine_id = pr.id
    ORDER BY f.created_at DESC LIMIT 5";
$recent_feedback = $conn->query($sql_recent_feedback);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Fitness Tracker</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --bg-light: #f4f6f9;
            --card-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            --accent: #28a745; /* Changed accent color for fitness theme */
            --highlight: #17a2b8;
        }
        
        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--accent), #1f8a70);
            color: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }

        .card-stats {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-radius: 12px;
            border: none;
            box-shadow: var(--card-shadow);
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .card-stats .card-header-icon {
            background: linear-gradient(45deg, var(--accent), #45b78d);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            position: absolute;
            top: -20px;
            right: 20px;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-stats .stats-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: #343a40;
            line-height: 1;
        }
        .notification-list {
            max-height: 350px;
            overflow-y: auto;
        }
        .alert-info { border-left-color: var(--primary-color); }
        .alert-success { border-left-color: var(--success-color); }
        .alert-warning { border-left-color: var(--warning-color); }
        .alert-danger { border-left-color: var(--danger-color); }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container-fluid mt-4">
    <div class="card welcome-card mb-5 shadow">
        <div class="card-body text-center py-4">
            <h1 class="mb-1 font-weight-bold">Fitness Tracker Admin Center 🏋️</h1>
            <p class="lead mb-0">Welcome, <strong><?php echo htmlspecialchars($_SESSION["username"] ?? 'Admin'); ?>!</strong> Oversee the health of the system.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 col-lg-2 mb-3">
            <div class="card card-stats">
                <div class="card-header-icon" style="background: linear-gradient(45deg, #007bff, #17a2b8);"><i class="fas fa-users fa-2x"></i></div>
                <div class="card-body">
                    <h5 class="stats-title">Total Users</h5>
                    <h3 class="stats-value"><?php echo $total_users; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2 mb-3">
            <div class="card card-stats">
                <div class="card-header-icon" style="background: linear-gradient(45deg, #28a745, #45b78d);"><i class="fas fa-dumbbell fa-2x"></i></div>
                <div class="card-body">
                    <h5 class="stats-title">Workouts Logged</h5>
                    <h3 class="stats-value"><?php echo $total_workouts; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2 mb-3">
            <div class="card card-stats">
                <div class="card-header-icon" style="background: linear-gradient(45deg, #ffc107, #ff9800);"><i class="fas fa-utensils fa-2x"></i></div>
                <div class="card-body">
                    <h5 class="stats-title">Meals Logged</h5>
                    <h3 class="stats-value"><?php echo $total_meals; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2 mb-3">
            <div class="card card-stats">
                <div class="card-header-icon" style="background: linear-gradient(45deg, #dc3545, #c82333);"><i class="fas fa-running fa-2x"></i></div>
                <div class="card-body">
                    <h5 class="stats-title">Public Routines</h5>
                    <h3 class="stats-value"><?php echo $total_routines; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2 mb-3">
            <div class="card card-stats">
                <div class="card-header-icon" style="background: linear-gradient(45deg, #17a2b8, #138496);"><i class="fas fa-lightbulb fa-2x"></i></div>
                <div class="card-body">
                    <h5 class="stats-title">Fitness Tips</h5>
                    <h3 class="stats-value"><?php echo $total_tips; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2 mb-3">
            <div class="card card-stats">
                <div class="card-header-icon" style="background: linear-gradient(45deg, #6f42c1, #5a19b8);"><i class="fas fa-user-tie fa-2x"></i></div>
                <div class="card-body">
                    <h5 class="stats-title">Total Trainers</h5>
                    <h3 class="stats-value"><?php echo $total_trainers; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Role Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="userRoleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">Routine Feedback Rating</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="reviewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">Workout Trend (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="workoutsTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Registrations</h5>
                </div>
                <div class="card-body notification-list">
                    <?php if ($recent_users->num_rows > 0): ?>
                        <ul class="list-unstyled">
                            <?php while ($user = $recent_users->fetch_assoc()): ?>
                                <li class="alert alert-info mb-3">
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($user['name']); ?></strong> (<?php echo ucfirst($user['role']); ?>)</p>
                                    <p class="mb-1 small"><?php echo htmlspecialchars($user['email']); ?></p>
                                    <small>Joined: <?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?></small>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center">No recent users.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Latest Fitness Tips</h5>
                </div>
                <div class="card-body notification-list">
                    <?php if ($recent_tips->num_rows > 0): ?>
                        <ul class="list-unstyled">
                            <?php while ($tip = $recent_tips->fetch_assoc()): ?>
                                <li class="alert alert-success mb-3">
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($tip['title']); ?></strong></p>
                                    <small>Posted: <?php echo date('M d, Y H:i', strtotime($tip['created_at'])); ?></small>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center">No fitness tips posted.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Routine Feedback</h5>
                </div>
                <div class="card-body notification-list">
                    <?php if ($recent_feedback->num_rows > 0): ?>
                        <ul class="list-unstyled">
                            <?php while ($feedback = $recent_feedback->fetch_assoc()): ?>
                                <li class="alert alert-warning mb-3">
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($feedback['user_name']); ?></strong> on **<?php echo htmlspecialchars($feedback['routine_title'] ?? 'N/A'); ?>**</p>
                                    <p class="mb-1 small">Rating: <?php echo str_repeat('⭐', $feedback['rating']) . str_repeat('☆', 5 - $feedback['rating']); ?></p>
                                    <p class="mb-1 small text-muted font-italic">"<?php echo htmlspecialchars(substr($feedback['comment'], 0, 30)) . '..."'; ?></p>
                                    <small>Filed: <?php echo date('M d, Y H:i', strtotime($feedback['created_at'])); ?></small>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center">No recent routine feedback.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // PHP variables passed to JavaScript
    const userRoleLabels = <?php echo json_encode($user_roles_labels); ?>;
    const userRoleData = <?php echo json_encode($user_roles_data); ?>;
    const feedbackRatingData = <?php echo json_encode(array_values($reviews_rating_data)); ?>;
    const workoutsTrendLabels = <?php echo json_encode($orders_dates); ?>;
    const workoutsTrendData = <?php echo json_encode($orders_counts); ?>;


    // 1. User Role Distribution Chart
    const userRoleChartCtx = document.getElementById('userRoleChart').getContext('2d');
    new Chart(userRoleChartCtx, {
        type: 'pie',
        data: {
            labels: userRoleLabels,
            datasets: [{
                data: userRoleData,
                backgroundColor: ['#007bff', '#28a745', '#ffc107'], // Primary, Success, Warning
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 12 } } }
            }
        }
    });

    // 2. Routine Feedback Rating Chart
    const reviewsChartCtx = document.getElementById('reviewsChart').getContext('2d');
    new Chart(reviewsChartCtx, {
        type: 'pie',
        data: {
            labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
            datasets: [{
                label: 'Feedback Count',
                data: feedbackRatingData,
                backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#007bff'],
                borderColor: '#000000ff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 3. Workouts Trend Chart
    const workoutsTrendChartCtx = document.getElementById('workoutsTrendChart').getContext('2d');
    new Chart(workoutsTrendChartCtx, {
        type: 'line',
        data: {
            labels: workoutsTrendLabels,
            datasets: [{
                label: 'Daily Workouts Logged',
                data: workoutsTrendData,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                fill: true,
                tension: 0.4,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: {
                legend: { display: true, position: 'top' }
            }
        }
    });
</script>
</body>
</html>