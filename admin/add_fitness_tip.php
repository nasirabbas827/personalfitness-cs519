<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];


    $image_url = null;

    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image_url = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO fitness_tips (title, content, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $image_url, );
    $stmt->execute();
    $stmt->close();

    header("Location: view_fitness_tips.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Fitness Tip</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="mb-4">Add Fitness Tip</h3>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Title</label>
                        <input type="text" name="title" required class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Content</label>
                        <textarea name="content" rows="4" required class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>

                <a href="view_fitness_tips.php" class="btn btn-secondary mt-3">View Tips</a>

            </form>
        </div>
    </div>
</div>

</body>
</html>
