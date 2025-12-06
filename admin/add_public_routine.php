<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $difficulty = $_POST['difficulty'];
    $duration = $_POST['duration'];
    $category = $_POST['category'];
    $instructions = $_POST['instructions'];
    $created_by = 1;  
    $trainer_id = $_POST['trainer_id'] !== "" ? $_POST['trainer_id'] : NULL;

    $image_url = NULL;

    if (!empty($_FILES['image']['name'])) {
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target = "uploads/" . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
            $image_url = $target;
        }
    }

    $stmt = $conn->prepare("INSERT INTO public_routines 
        (title, description, difficulty, duration_minutes, category, instructions, image_url, created_by, trainer_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssisssii", $title, $description, $difficulty, $duration, $category, $instructions, $image_url, $created_by, $trainer_id);
    $stmt->execute();
    $stmt->close();

    $message = "Routine added successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Routine</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">

    <div class="card mx-auto" style="max-width: 700px;">
        <div class="card-body">
            <h3 class="mb-3">Add New Public Routine</h3>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Title</label>
                        <input type="text" name="title" required class="form-control">
                    </div>

                    <div class="form-group col-md-12">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Difficulty</label>
                        <select name="difficulty" class="form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Category</label>
                        <input type="text" name="category" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Trainer ID (optional)</label>
                        <input type="number" name="trainer_id" class="form-control">
                    </div>

                    <div class="form-group col-md-12">
                        <label>Instructions</label>
                        <textarea name="instructions" class="form-control"></textarea>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                </div>

                <button type="submit" class="btn btn-success mt-3">Add Routine</button>
                <a href="view_public_routines.php" class="btn btn-secondary mt-3">View Routines</a>


            </form>

        </div>
    </div>
</div>

</body>
</html>
