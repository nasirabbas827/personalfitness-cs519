<?php
session_start();
include('../config.php');

if (!isset($_SESSION["id"]) || $_SESSION["role"] !== 'user') {
    header("location: ../login.php");
    exit;
}

$tip_id = intval($_GET['id'] ?? 0);
if ($tip_id <= 0) {
    header("location: user_dashboard.php");
    exit;
}

// Fetch tip details
$tip = $conn->query("SELECT * FROM fitness_tips WHERE id = $tip_id")->fetch_assoc();
if (!$tip) {
    echo "Tip not found.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($tip['title']) ?> - Fitness Tip</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<style>
.card-hover { transition: all 0.3s; }
.card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
</style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container py-5">

    <div class="card shadow-lg card-hover">
        <?php if ($tip['image_url']): ?>
            <img src="../admin/<?= htmlspecialchars($tip['image_url']) ?>" class="card-img-top" style="max-height:400px; object-fit:cover;">
        <?php endif; ?>
        <div class="card-body">
            <h2 class="card-title text-primary"><?= htmlspecialchars($tip['title']) ?></h2>
            <p class="card-text"><?= nl2br(htmlspecialchars($tip['content'])) ?></p>
        </div>
        <div class="card-footer text-muted">
            Posted on: <?= date('M d, Y', strtotime($tip['created_at'])) ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
