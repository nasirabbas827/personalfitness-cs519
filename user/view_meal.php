<?php
include('../config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

$user_id = $_SESSION["id"];

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM daily_meals WHERE id=$id AND user_id=$user_id");
}

$result = mysqli_query($conn, "SELECT * FROM daily_meals WHERE user_id=$user_id ORDER BY log_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Meals</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h3 class="mb-4">Your Meals</h3>
    <div class="text-right mb-3">
        <a href="add_meal.php" class="btn btn-primary">Add New Meal</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Meal Time</th>
                <th>Description</th>
                <th>Calories</th>
                <th>Protein (g)</th>
                <th>Carbs (g)</th>
                <th>Fats (g)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['log_date']); ?></td>
                <td><?= htmlspecialchars($row['meal_time']); ?></td>
                <td><?= htmlspecialchars($row['food_description']); ?></td>
                <td><?= htmlspecialchars($row['calories']); ?></td>
                <td><?= htmlspecialchars($row['protein_g']); ?></td>
                <td><?= htmlspecialchars($row['carbs_g']); ?></td>
                <td><?= htmlspecialchars($row['fats_g']); ?></td>
                <td>
                    <a href="edit_meal.php?id=<?= $row['id']; ?>" class="m-2 btn btn-sm btn-warning">Edit</a>
                    <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this meal?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
