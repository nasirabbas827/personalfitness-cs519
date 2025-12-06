<?php
include('../config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

$user_id = $_SESSION["id"];

$user_id = $_SESSION['id'];

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM daily_workouts WHERE id=$id AND user_id=$user_id");
}

$result = mysqli_query($conn, "SELECT * FROM daily_workouts WHERE user_id=$user_id ORDER BY log_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Daily Workouts</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h3 class="mb-4">Your Daily Workouts</h3>
    <a href="add_workout.php" class="btn btn-primary mb-3">Add New Workout</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Duration</th>
                <th>Calories</th>
                <th>Steps</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['log_date']); ?></td>
                <td><?= htmlspecialchars($row['workout_type']); ?></td>
                <td><?= htmlspecialchars($row['duration_minutes']); ?></td>
                <td><?= htmlspecialchars($row['calories_burned']); ?></td>
                <td><?= htmlspecialchars($row['steps']); ?></td>
                <td><?= htmlspecialchars($row['notes']); ?></td>
                <td>
                    <a href="edit_workout.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this log?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
