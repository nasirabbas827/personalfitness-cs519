<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: view_users.php");
    exit;
}

$id = intval($_GET['id']);
$message = "";

$query = $conn->prepare("SELECT * FROM users WHERE id=?");
$query->bind_param("i", $id);
$query->execute();
$user = $query->get_result()->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $role    = $_POST['role'];
    $dob     = $_POST['date_of_birth'];
    $gender  = $_POST['gender'];
    $height  = $_POST['height_cm'];
    $weight  = $_POST['weight_kg'];

    $stmt = $conn->prepare("UPDATE users SET email=?, name=?, role=?, date_of_birth=?, gender=?, height_cm=?, weight_kg=? WHERE id=?");
    $stmt->bind_param("sssssssi", $email, $name, $role, $dob, $gender, $height, $weight, $id);

    if ($stmt->execute()) {
        $message = "User updated successfully.";
        $query->execute();
        $user = $query->get_result()->fetch_assoc();
    } else {
        $message = "Error updating user.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
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

            <h3 class="text-center mb-4">Edit User</h3>

            <?php if ($message): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="row">

                    <div class="col-12 mb-3">
                        <label>Full Name</label>
                        <input type="text" required name="name" class="form-control" value="<?= $user['name'] ?>">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Email</label>
                        <input type="email" required name="email" class="form-control" value="<?= $user['email'] ?>">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="user"    <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="trainer" <?= $user['role'] == 'trainer' ? 'selected' : '' ?>>Trainer</option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?= $user['date_of_birth'] ?>">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="male"   <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other"  <?= $user['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="col-6 mb-3">
                        <label>Height (cm)</label>
                        <input type="number" step="0.01" name="height_cm" class="form-control" value="<?= $user['height_cm'] ?>">
                    </div>

                    <div class="col-6 mb-3">
                        <label>Weight (kg)</label>
                        <input type="number" step="0.01" name="weight_kg" class="form-control" value="<?= $user['weight_kg'] ?>">
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="view_users.php" class="btn btn-secondary  mt-2">Back</a>

            </form>

        </div>
    </div>

</div>

</body>
</html>
