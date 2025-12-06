<?php
include('../config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

$user_id = $_SESSION['id'];
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM daily_meals WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$meal = $result->fetch_assoc();
$stmt->close();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $log_date = $_POST['log_date'];
    $meal_time = $_POST['meal_time'];
    $food_description = $_POST['food_description'];
    $calories = $_POST['calories'] ?? 0;
    $protein_g = $_POST['protein_g'] ?? 0;
    $carbs_g = $_POST['carbs_g'] ?? 0;
    $fats_g = $_POST['fats_g'] ?? 0;

    $stmt = $conn->prepare("UPDATE daily_meals SET log_date=?, meal_time=?, food_description=?, calories=?, protein_g=?, carbs_g=?, fats_g=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sssddddii", $log_date, $meal_time, $food_description, $calories, $protein_g, $carbs_g, $fats_g, $id, $user_id);

    if ($stmt->execute()) {
        $msg = '<div class="alert alert-success text-center">Meal log updated successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger text-center">Error updating meal log!</div>';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Meal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="text-center mb-4">Edit Meal Log</h3>
            <?php echo $msg; ?>
            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date" name="log_date" class="form-control" value="<?= htmlspecialchars($meal['log_date']); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Meal Time</label>
                        <select name="meal_time" class="form-control" required>
                            <option value="breakfast" <?= $meal['meal_time']=='breakfast'?'selected':'' ?>>Breakfast</option>
                            <option value="lunch" <?= $meal['meal_time']=='lunch'?'selected':'' ?>>Lunch</option>
                            <option value="dinner" <?= $meal['meal_time']=='dinner'?'selected':'' ?>>Dinner</option>
                            <option value="snack" <?= $meal['meal_time']=='snack'?'selected':'' ?>>Snack</option>
                            <option value="other" <?= $meal['meal_time']=='other'?'selected':'' ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Food Description</label>
                    <textarea name="food_description" class="form-control" required><?= htmlspecialchars($meal['food_description']); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Calories</label>
                        <input type="number" name="calories" class="form-control" value="<?= $meal['calories']; ?>" step="1" min="0">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Protein (g)</label>
                        <input type="number" name="protein_g" class="form-control" value="<?= $meal['protein_g']; ?>" step="0.01" min="0">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Carbs (g)</label>
                        <input type="number" name="carbs_g" class="form-control" value="<?= $meal['carbs_g']; ?>" step="0.01" min="0">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fats (g)</label>
                        <input type="number" name="fats_g" class="form-control" value="<?= $meal['fats_g']; ?>" step="0.01" min="0">
                    </div>
                </div>
                <div class="text-center mt-3">
                    <input type="submit" class="btn btn-primary px-4" value="Update Meal">
                    <a href="view_meal.php" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
