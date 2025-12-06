<?php
session_start();
include('config.php');

// Fetch public routines
$public_routines = mysqli_query($conn, "
    SELECT r.*, u.name AS trainer_name
    FROM public_routines r
    LEFT JOIN users u ON r.trainer_id = u.id
    ORDER BY r.created_at DESC
    LIMIT 6
");

// Fetch fitness tips
$fitness_tips = mysqli_query($conn, "
    SELECT *
    FROM fitness_tips
    ORDER BY created_at DESC
    LIMIT 6
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tracker</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        :root {
            --accent: #007bff;
            --highlight: #28a745;
        }
        .hero-section {
            height: 600px;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('./images/hotel.jpg') center/cover no-repeat;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border-radius: 0 0 30px 30px;
        }
        .hero-section .btn-primary { 
            background-color: var(--accent); border-color: var(--accent); padding: 12px 30px; font-size: 1.2rem; border-radius: 20px; 
        }
        .hero-section .btn-primary:hover { 
            background-color: var(--highlight); border-color: var(--highlight); transform: scale(1.05);
        }
        .card { border-radius: 15px; box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .card img { height: 200px; object-fit: cover; border-radius: 15px 15px 0 0; }
        .section-title { color: var(--highlight); margin-bottom: 40px; font-weight: 700; position: relative; }
        .section-title::after { content: ''; width: 60px; height: 4px; background-color: var(--accent); position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); }
        footer { background-color: #343a40; color: white; padding: 30px 0; border-top: 4px solid var(--accent); }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<!-- Hero Section -->
<div class="hero-section">
    <h1 class="display-4 fw-bold text-white">Fitness Tracker</h1>
    <p class="lead mb-4">Track, Learn, and Stay Fit!</p>
    <?php if(!isset($_SESSION['id'])): ?>
        <a href="login.php" class="btn btn-primary"><i class="fas fa-user-plus mr-2"></i>Register / Login</a>
    <?php else: ?>
        <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard</a>
    <?php endif; ?>
</div>

<!-- Public Routines Section -->
<div class="section bg-light">
    <div class="container">
        <h2 class="text-center section-title">Popular Workout Routines</h2>
        <div class="row">
            <?php if(mysqli_num_rows($public_routines) > 0): ?>
                <?php while($routine = mysqli_fetch_assoc($public_routines)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card card-hover h-100">
                            <?php if($routine['image_url']): ?>
                                <img src="admin/<?= htmlspecialchars($routine['image_url']) ?>" alt="<?= htmlspecialchars($routine['title']) ?>">
                            <?php else: ?>
                                <img src="./images/no_image.png" alt="No Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($routine['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($routine['description'],0,100)) ?>...</p>
                                <p class="small text-muted">Difficulty: <?= ucfirst($routine['difficulty']) ?> | Duration: <?= $routine['duration_minutes'] ?> mins</p>
                                <p class="small text-muted">Trainer: <?= $routine['trainer_name'] ?: 'N/A' ?></p>
                                <a href="login.php" class="btn btn-primary btn-sm">View Routine</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12"><div class="alert alert-info text-center">No routines found.</div></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Fitness Tips Section -->
<div class="section">
    <div class="container">
        <h2 class="text-center section-title">Health & Fitness Tips</h2>
        <div class="row">
            <?php if(mysqli_num_rows($fitness_tips) > 0): ?>
                <?php while($tip = mysqli_fetch_assoc($fitness_tips)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card card-hover h-100">
                            <?php if($tip['image_url']): ?>
                                <img src="admin/<?= htmlspecialchars($tip['image_url']) ?>" alt="<?= htmlspecialchars($tip['title']) ?>">
                            <?php else: ?>
                                <img src="./images/no_image.png" alt="No Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($tip['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($tip['content'],0,100)) ?>...</p>
                                <a href="login.php" class="btn btn-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12"><div class="alert alert-info text-center">No tips available.</div></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="section bg-light">
    <div class="container">
        <h2 class="text-center section-title">Why Choose Fitness Tracker?</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="fas fa-dumbbell fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Browse Routines & Tips</h5>
                        <p class="card-text">Access free workout routines and health tips for all users.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="fas fa-user-plus fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Personal Plan & Progress</h5>
                        <p class="card-text">Registered users can create a personal plan and track their daily progress.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Track Achievements</h5>
                        <p class="card-text">Monitor your workouts, meals, and water intake efficiently.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center">
    <div class="container">
        <p class="mb-0">© 2025 Fitness Tracker. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
