<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    echo "<script>alert('User deleted successfully.'); window.location='view_users.php';</script>";
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">All Users</h2>

    <a href="add_users.php" class="btn btn-success mb-3">Add New User</a>

    <table class="table table-bordered table-striped table-responsive-sm">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Height</th>
            <th>Weight</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['role'] ?></td>
                <td><?= $row['date_of_birth'] ?></td>
                <td><?= $row['gender'] ?></td>
                <td><?= $row['height_cm'] ?></td>
                <td><?= $row['weight_kg'] ?></td>

                <td>
                    <a href="edit_users.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary m-1">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>"
                       class="btn btn-sm btn-danger m-1"
                       onclick="return confirm('Are you sure you want to delete this user?');">
                        Delete
                    </a>
                    <?php if($row['role'] === 'user'): ?>
                        <a href="view_user_logs.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info m-1">
                            View Logs
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>
</div>

</body>
</html>
