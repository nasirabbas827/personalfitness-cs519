<?php
session_start();
include('config.php'); // Your DB connection: $conn = new mysqli(...)

if (!isset($_SESSION["id"]) || $_SESSION["role"] !== 'user') {
    header("location: ../login.php");
    exit;
}

$user_id = $_SESSION["id"];
$name    = $_SESSION["name"] ?? 'User';

// ======================== FETCH USER STATS ========================
$total_workouts = $conn->query("SELECT COUNT(*) FROM daily_workouts WHERE user_id = $user_id")->fetch_row()[0];
$total_days_logged = $conn->query("SELECT COUNT(DISTINCT log_date) FROM daily_workouts WHERE user_id = $user_id")->fetch_row()[0];
$total_calories_burned = $conn->query("SELECT COALESCE(SUM(calories_burned),0) FROM daily_workouts WHERE user_id = $user_id")->fetch_row()[0];
$total_water_intake = $conn->query("SELECT COALESCE(SUM(water_ml),0) FROM daily_water_intake WHERE user_id = $user_id")->fetch_row()[0];
$total_meals_logged = $conn->query("SELECT COUNT(*) FROM daily_meals WHERE user_id = $user_id")->fetch_row()[0];

// Weekly Workout Minutes (Last 7 Days)
$weekly_minutes = $conn->query("
    SELECT COALESCE(SUM(duration_minutes),0) 
    FROM daily_workouts 
    WHERE user_id = $user_id 
      AND log_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
")->fetch_row()[0];

// Chart Data: Last 7 Days Calories Burned
$chart_labels = [];
$chart_calories = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('D', strtotime($date));
    $chart_labels[] = $label;

    $cal = $conn->query("SELECT COALESCE(SUM(calories_burned),0) FROM daily_workouts WHERE user_id = $user_id AND log_date = '$date'")->fetch_row()[0];
    $chart_calories[] = $cal;
}

// Fetch All Fitness Tips
$tips = $conn->query("SELECT * FROM fitness_tips ORDER BY created_at DESC LIMIT 6");

// Fetch Public Routines with Trainer Name & Feedback Count
$routines = $conn->query("
    SELECT pr.*, u.name as trainer_name,
           COUNT(f.id) as feedback_count
    FROM public_routines pr
    LEFT JOIN users u ON pr.trainer_id = u.id
    LEFT JOIN feedback f ON pr.id = f.routine_id
    GROUP BY pr.id
    ORDER BY pr.created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Fitness Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-hover { transition: all 0.3s; }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
        .stat-card { background: white; border-radius: 15px; }
        .routine-card { border: none; border-radius: 15px; overflow: hidden; }
        .badge-difficulty { font-size: 0.8rem; padding: 5px 10px; border-radius: 20px; }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container py-5">

    <!-- Welcome -->
    <div class="text-center mb-5">
        <h1 class="display-4 text-primary font-weight-bold">Welcome back, <?= htmlspecialchars($name) ?>!</h1>
        <p class="lead">Track your fitness journey and stay motivated!</p>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card stat-card shadow-lg card-hover text-center p-4">
                <i class="fas fa-dumbbell fa-3x text-primary mb-3"></i>
                <h3><?= $total_workouts ?></h3>
                <p class="text-muted">Total Workouts</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card stat-card shadow-lg card-hover text-center p-4">
                <i class="fas fa-fire fa-3x text-danger mb-3"></i>
                <h3><?= number_format($total_calories_burned) ?></h3>
                <p class="text-muted">Calories Burned</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card stat-card shadow-lg card-hover text-center p-4">
                <i class="fas fa-tint fa-3x text-info mb-3"></i>
                <h3><?= number_format($total_water_intake) ?> ml</h3>
                <p class="text-muted">Water Consumed</p>
            </div>
        </div>
    </div>

    <!-- Chart: Last 7 Days Calories -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h4 class="card-title text-center"><i class="fas fa-chart-line"></i> Calories Burned (Last 7 Days)</h4>
                    <canvas id="caloriesChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fitness Tips -->
    <h2 class="mb-4 text-center text-primary"><i class="fas fa-lightbulb"></i> Daily Fitness Tips</h2>
    <div class="row mb-5">
        <?php while ($tip = $tips->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow card-hover">
                    <?php if ($tip['image_url']): ?>
                        <img src="../admin/<?= htmlspecialchars($tip['image_url']) ?>" class="card-img-top" alt="Tip Image" style="height:180px; object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($tip['title']) ?></h5>
                        <p class="card-text flex-grow-1"><?= nl2br(htmlspecialchars(substr($tip['content'], 0, 150))) ?>...</p>
                        <a href="tip_details.php?id=<?= $tip['id'] ?>" class="btn btn-outline-primary btn-sm mt-auto">Read More</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Public Routines -->
    <h2 class="mb-4 text-center text-success"><i class="fas fa-running"></i> Popular Workout Routines</h2>
    <div class="row">
        <?php while ($routine = $routines->fetch_assoc()): 
            $difficulty_color = $routine['difficulty'] == 'beginner' ? 'success' : ($routine['difficulty'] == 'intermediate' ? 'warning' : 'danger');
        ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card routine-card shadow-lg card-hover h-100">
                    <?php if ($routine['image_url']): ?>
                        <img src="../admin/<?= htmlspecialchars($routine['image_url']) ?>" class="card-img-top" alt="Routine" style="height:200px; object-fit:cover;">
                    <?php else: ?>
                        <div class="bg-light text-center py-5">
                            <i class="fas fa-dumbbell fa-4x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($routine['title']) ?></h5>
                        <p class="text-muted small">
                            <i class="fas fa-user"></i> <?= $routine['trainer_name'] ? htmlspecialchars($routine['trainer_name']) : 'Admin' ?>
                        </p>
                        <p class="card-text"><?= htmlspecialchars(substr($routine['description'] ?? $routine['instructions'], 0, 100)) ?>...</p>
                        
                        <div class="mt-auto">
                            <span class="badge badge-<?= $difficulty_color ?> badge-difficulty text-uppercase">
                                <?= ucfirst($routine['difficulty']) ?>
                            </span>
                            <span class="badge badge-info"><i class="fas fa-clock"></i> <?= $routine['duration_minutes'] ?> min</span>
                            <span class="badge badge-secondary"><i class="fas fa-comments"></i> <?= $routine['feedback_count'] ?> Feedback</span>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <a href="routine_details.php?id=<?= $routine['id'] ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-play"></i> View Routine & Give Feedback
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</div>

<script>
// Calories Burned Chart
new Chart(document.getElementById('caloriesChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [{
            label: 'Calories Burned',
            data: <?= json_encode($chart_calories) ?>,
            borderColor: '#e74c3c',
            backgroundColor: 'rgba(231, 76, 60, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>