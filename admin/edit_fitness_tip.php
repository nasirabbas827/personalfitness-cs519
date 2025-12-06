<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$id = intval($_GET["id"]);

$query = mysqli_query($conn, "SELECT * FROM fitness_tips WHERE id=$id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Record not found");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];

    $image_url = $data['image_url'];

    if (!empty($_FILES["image"]["name"])) {
        if ($image_url && file_exists($image_url)) {
            unlink($image_url);
        }

        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image_url = $targetFile;
        }
    }

    $stmt = $conn->prepare("UPDATE fitness_tips SET title=?, content=?, image_url=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $content, $image_url, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: view_fitness_tips.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Fitness Tip</title>
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

            <h3 class="mb-4">Edit Fitness Tip</h3>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" required class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Content</label>
                        <textarea rows="4" name="content" required class="form-control"><?= htmlspecialchars($data['content']) ?></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Current Image</label><br>
                        <?php if ($data['image_url']): ?>
                            <img src="<?= $data['image_url'] ?>" height="80">
                        <?php endif; ?>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Change Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update</button>

                <a href="view_fitness_tips.php" class="btn btn-secondary mt-3">Back</a>

            </form>

        </div>
    </div>
</div>

</body>
</html>
