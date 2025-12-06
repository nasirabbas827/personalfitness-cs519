<?php
include('../config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

$user_id = $_SESSION["id"];

$user_id = $_SESSION['id'];
$id = intval($_GET['id']);
$msg = "";

// Fetch workout
$result = mysqli_query($conn, "SELECT * FROM daily_workouts WHERE id=$id AND user_id=$user_id");
if (mysqli_num_rows($result) == 0) {
    header("Location: view_workouts.php");
    exit;
}
$workout = mysqli_fetch_assoc($result);

// Initialize variables
$log_date = $workout['log_date'];
$workout_type = $workout['workout_type'];
$duration_minutes = $workout['duration_minutes'];
$calories_burned = $workout['calories_burned'];
$steps = $workout['steps'];
$notes = $workout['notes'];

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $log_date = $_POST['log_date'];
    $workout_type = $_POST['workout_type'];
    $duration_minutes = $_POST['duration_minutes'];
    $calories_burned = $_POST['calories_burned'];
    $steps = $_POST['steps'];
    $notes = $_POST['notes'];

    $sql = "UPDATE daily_workouts SET log_date=?, workout_type=?, duration_minutes=?, calories_burned=?, steps=?, notes=? WHERE id=? AND user_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $log_date, $workout_type, $duration_minutes, $calories_burned, $steps, $notes, $id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $msg = '<div class="alert alert-success text-center">Workout log updated successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger text-center">Error updating workout log.</div>';
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Daily Workout</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="text-center mb-4">Edit Daily Workout</h3>
            <?php echo $msg; ?>
            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date" name="log_date" class="form-control" value="<?= $log_date; ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Workout Type</label>
                        <input type="text" name="workout_type" class="form-control" value="<?= $workout_type; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" value="<?= $duration_minutes; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Calories Burned</label>
                        <input type="number" name="calories_burned" class="form-control" value="<?= $calories_burned; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Steps</label>
                        <input type="number" name="steps" class="form-control" value="<?= $steps; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2" class="form-control"><?= $notes; ?></textarea>
                </div>

                <div class="text-center">
                    <input type="submit" value="Update" class="btn btn-primary">
                    <a href="view_workouts.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
