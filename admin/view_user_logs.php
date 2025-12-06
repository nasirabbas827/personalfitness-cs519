<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$user_id = intval($_GET['id']);

// Fetch user info
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id AND role='user'"));
if (!$user) {
    die("Invalid user or user is not a regular user.");
}

// Fetch workouts
$workouts = mysqli_query($conn, "SELECT * FROM daily_workouts WHERE user_id=$user_id ORDER BY log_date DESC");

// Fetch water intake
$water = mysqli_query($conn, "SELECT * FROM daily_water_intake WHERE user_id=$user_id ORDER BY log_date DESC");

// Fetch meals
$meals = mysqli_query($conn, "SELECT * FROM daily_meals WHERE user_id=$user_id ORDER BY log_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($user['name']) ?> - Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">Logs for <?= htmlspecialchars($user['name']) ?></h2>

    <h4 class="mt-4">Workouts</h4>
    <?php if(mysqli_num_rows($workouts) > 0): ?>
        <div class="row">
            <?php while($w = mysqli_fetch_assoc($workouts)): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($w['workout_type'] ?: 'Workout') ?></h5>
                            <p><strong>Date:</strong> <?= $w['log_date'] ?></p>
                            <p><strong>Duration:</strong> <?= $w['duration_minutes'] ?> mins</p>
                            <p><strong>Calories:</strong> <?= $w['calories_burned'] ?></p>
                            <p><strong>Steps:</strong> <?= $w['steps'] ?></p>
                            <?php if($w['notes']): ?><p><strong>Notes:</strong> <?= htmlspecialchars($w['notes']) ?></p><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No workouts logged.</p>
    <?php endif; ?>

    <h4 class="mt-4">Water Intake</h4>
    <?php if(mysqli_num_rows($water) > 0): ?>
        <div class="row">
            <?php while($w = mysqli_fetch_assoc($water)): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p><strong>Date:</strong> <?= $w['log_date'] ?></p>
                            <p><strong>Water:</strong> <?= $w['water_ml'] ?> ml</p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No water intake logs.</p>
    <?php endif; ?>

    <h4 class="mt-4">Meals</h4>
    <?php if(mysqli_num_rows($meals) > 0): ?>
        <div class="row">
            <?php while($m = mysqli_fetch_assoc($meals)): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p><strong>Date:</strong> <?= $m['log_date'] ?></p>
                            <p><strong>Meal Time:</strong> <?= ucfirst($m['meal_time']) ?></p>
                            <p><strong>Food:</strong> <?= htmlspecialchars($m['food_description']) ?></p>
                            <p><strong>Calories:</strong> <?= $m['calories'] ?></p>
                            <p><strong>Protein:</strong> <?= $m['protein_g'] ?> g</p>
                            <p><strong>Carbs:</strong> <?= $m['carbs_g'] ?> g</p>
                            <p><strong>Fats:</strong> <?= $m['fats_g'] ?> g</p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No meal logs.</p>
    <?php endif; ?>

    <a href="view_users.php" class="btn btn-secondary mt-3">Back to Users</a>
</div>

</body>
</html>
