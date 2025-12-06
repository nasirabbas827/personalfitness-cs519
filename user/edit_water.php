<?php
include('../config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

$user_id = $_SESSION['id'];
$id = intval($_GET['id']);
$msg = "";

// Fetch existing data
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM daily_water_intake WHERE id=$id AND user_id=$user_id"));
if (!$row) {
    header("location: view_water.php");
    exit;
}

$log_date = $row['log_date'];
$water_ml = $row['water_ml'];

// Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $log_date = $_POST['log_date'];
    $water_ml = intval($_POST['water_ml']);

    $stmt = mysqli_prepare($conn, "UPDATE daily_water_intake SET log_date=?, water_ml=? WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($stmt, "siii", $log_date, $water_ml, $id, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $msg = '<div class="alert alert-success text-center">Water log updated successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger text-center">Error updating log.</div>';
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Water Log</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="text-center mb-4">Edit Daily Water Intake</h4>
            <?php echo $msg; ?>
            <form method="post" action="">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="log_date" class="form-control" value="<?= $log_date; ?>" required>
                </div>
                <div class="form-group">
                    <label>Water (ml)</label>
                    <input type="number" name="water_ml" class="form-control" value="<?= $water_ml; ?>" required>
                </div>
                <div class="text-center mt-3">
                    <input type="submit" class="btn btn-primary" value="Update Log">
                    <a href="view_water.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
