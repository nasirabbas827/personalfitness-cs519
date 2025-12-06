<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $imgQuery = mysqli_query($conn, "SELECT image_url FROM fitness_tips WHERE id=$id");
    $img = mysqli_fetch_assoc($imgQuery);

    if ($img && $img['image_url'] && file_exists($img['image_url'])) {
        unlink($img['image_url']);
    }

    mysqli_query($conn, "DELETE FROM fitness_tips WHERE id = $id");
}

$result = mysqli_query($conn, "SELECT * FROM fitness_tips ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Fitness Tips</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">

    <h2 class="mb-4">All Fitness Tips</h2>

    <a href="add_fitness_tip.php" class="btn btn-primary mb-3">Add New</a>

    <table class="table table-bordered table-striped table-responsive-sm">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Title</th>
            <th>Content</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id'] ?></td>

                <td>
                    <?php if ($row['image_url']): ?>
                        <img src="<?= $row['image_url'] ?>" height="60">
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars(substr($row['content'], 0, 80)) ?>...</td>
                <td><?= $row['created_at'] ?></td>

                <td>
                    <a href="edit_fitness_tip.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>"
                       onclick="return confirm('Delete this tip?')"
                       class="btn btn-sm btn-danger">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>

    </table>
</div>

</body>
</html>
