<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM public_routines WHERE id=$id"));

if (!$data) {
    die("Invalid routine.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $difficulty = $_POST['difficulty'];
    $duration = $_POST['duration'];
    $category = $_POST['category'];
    $instructions = $_POST['instructions'];
    $trainer_id = $_POST['trainer_id'] !== "" ? $_POST['trainer_id'] : NULL;

    $image_url = $data['image_url'];

    if (!empty($_FILES['image']['name'])) {
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target = "uploads/" . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
            $image_url = $target;
        }
    }

    $stmt = $conn->prepare("UPDATE public_routines 
        SET title=?, description=?, difficulty=?, duration_minutes=?, category=?, instructions=?, image_url=?, trainer_id=? 
        WHERE id=?");

    $stmt->bind_param("sssisssii", $title, $description, $difficulty, $duration, $category, $instructions, $image_url, $trainer_id, $id);
    $stmt->execute();
    $stmt->close();

    $message = "Routine updated.";
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM public_routines WHERE id=$id"));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Routine</title>
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

            <h3 class="mb-3">Edit Routine</h3>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-row">

                    <div class="form-group col-md-12">
                        <label>Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" class="form-control" required>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Description</label>
                        <textarea name="description" class="form-control"><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Difficulty</label>
                        <select name="difficulty" class="form-control">
                            <option value="beginner"     <?= $data['difficulty']=='beginner'?'selected':'' ?>>Beginner</option>
                            <option value="intermediate" <?= $data['difficulty']=='intermediate'?'selected':'' ?>>Intermediate</option>
                            <option value="advanced"     <?= $data['difficulty']=='advanced'?'selected':'' ?>>Advanced</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Duration</label>
                        <input type="number" name="duration" value="<?= $data['duration_minutes'] ?>" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Category</label>
                        <input type="text" name="category" value="<?= htmlspecialchars($data['category']) ?>" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Trainer ID</label>
                        <input type="number" name="trainer_id" value="<?= $data['trainer_id'] ?>" class="form-control">
                    </div>

                    <div class="form-group col-md-12">
                        <label>Instructions</label>
                        <textarea name="instructions" class="form-control"><?= htmlspecialchars($data['instructions']) ?></textarea>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <?php if($data['image_url']): ?>
                        <div class="form-group col-md-12 mt-2">
                            <img src="<?= $data['image_url'] ?>" width="120">
                        </div>
                    <?php endif; ?>

                </div>

                <button type="submit" class="btn btn-warning">Update Routine</button>
                <a href="view_public_routines.php" class="btn btn-secondary">Cancel</a>

            </form>
        </div>
    </div>

</div>

</body>
</html>
