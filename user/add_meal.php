<?php
include('../config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

$user_id = $_SESSION['id'];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $log_date = $_POST['log_date'];
    $meal_time = $_POST['meal_time'];
    $food_description = $_POST['food_description'];
    $calories = $_POST['calories'] ?? 0;
    $protein_g = $_POST['protein_g'] ?? 0;
    $carbs_g = $_POST['carbs_g'] ?? 0;
    $fats_g = $_POST['fats_g'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO daily_meals (user_id, log_date, meal_time, food_description, calories, protein_g, carbs_g, fats_g) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdddd", $user_id, $log_date, $meal_time, $food_description, $calories, $protein_g, $carbs_g, $fats_g);

    if ($stmt->execute()) {
        $msg = '<div class="alert alert-success text-center">Meal log added successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger text-center">Error adding meal log!</div>';
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Meal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="text-center mb-4">Add Meal Log</h3>
            <?php echo $msg; ?>
            

            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date" name="log_date" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Meal Time</label>
                        <select name="meal_time" class="form-control" required>
                            <option value="">Select Meal</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                            <option value="snack">Snack</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Food Description</label>
                    <textarea name="food_description" class="form-control" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Calories</label>
                        <input type="number" name="calories" class="form-control" step="1" min="0">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Protein (g)</label>
                        <input type="number" name="protein_g" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Carbs (g)</label>
                        <input type="number" name="carbs_g" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fats (g)</label>
                        <input type="number" name="fats_g" class="form-control" step="0.01" min="0">
                    </div>
                </div>
                <div class="form-group text-center mt-3">
                    <input type="submit" class="btn btn-primary px-4" value="Add Meal">
                <a href="view_meal.php" class="btn btn-secondary">View Meals</a>

                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
