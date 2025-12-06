<?php
include('../config.php');
session_start();

// Ensure only users can access
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

$user_id = $_SESSION["id"];

$user_id = $_SESSION['id'];
$log_date = $workout_type = $duration_minutes = $calories_burned = $steps = $notes = "";
$msg = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $log_date = $_POST['log_date'];
    $workout_type = $_POST['workout_type'];
    $duration_minutes = $_POST['duration_minutes'];
    $calories_burned = $_POST['calories_burned'];
    $steps = $_POST['steps'];
    $notes = $_POST['notes'];

    $sql = "INSERT INTO daily_workouts (user_id, log_date, workout_type, duration_minutes, calories_burned, steps, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issiiis", $user_id, $log_date, $workout_type, $duration_minutes, $calories_burned, $steps, $notes);

    if (mysqli_stmt_execute($stmt)) {
        $msg = '<div class="alert alert-success text-center">Workout log added successfully!</div>';
        $log_date = $workout_type = $duration_minutes = $calories_burned = $steps = $notes = "";
    } else {
        $msg = '<div class="alert alert-danger text-center">Error adding workout log.</div>';
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Daily Workout</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="text-center mb-4">Add Daily Workout</h3>
            <?php echo $msg; ?>
            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date" name="log_date" class="form-control" value="<?php echo $log_date; ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Workout Type</label>
                        <input type="text" name="workout_type" class="form-control" value="<?php echo $workout_type; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" value="<?php echo $duration_minutes; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Calories Burned</label>
                        <input type="number" name="calories_burned" class="form-control" value="<?php echo $calories_burned; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Steps</label>
                        <input type="number" name="steps" class="form-control" value="<?php echo $steps; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2" class="form-control"><?php echo $notes; ?></textarea>
                </div>

                <div class="text-center">
                    <input type="submit" value="Add Workout" class="btn btn-primary">
                    <a href="view_workouts.php" class="btn btn-secondary">View Logs</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
