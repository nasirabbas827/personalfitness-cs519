<?php
session_start();
include('config.php');

if (!isset($_SESSION["id"]) || $_SESSION["role"] !== 'trainer') {
    header("location: ../login.php");
    exit;
}

$trainer_id = $_SESSION["id"];
$trainer_name = $_SESSION["name"] ?? "Trainer";

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM public_routines WHERE id = $id");
}

$result = mysqli_query($conn, "SELECT * FROM public_routines ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Routines</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>All Public Routines</h3>
        <a href="add_public_routine.php" class="btn btn-primary">Add New</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Difficulty</th>
            <th>Duration</th>
            <th>Category</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['difficulty'] ?></td>
                <td><?= $row['duration_minutes'] ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>
                    <?php if($row['image_url']): ?>
                        <img src="../admin/<?= $row['image_url'] ?>" width="60">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_public_routine.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this routine?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>

    </table>

</div>

</body>
</html>
