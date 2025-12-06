<?php
include('config.php');
session_start();

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name        = $_POST['name'];
    $email       = $_POST['email'];
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role        = $_POST['role'];
    $dob         = $_POST['date_of_birth'];
    $gender      = $_POST['gender'];
    $height      = $_POST['height_cm'];
    $weight      = $_POST['weight_kg'];

    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, name, role, date_of_birth, gender, height_cm, weight_kg) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdd", $email, $password, $name, $role, $dob, $gender, $height, $weight);

    if ($stmt->execute()) {
        $message = "User added successfully.";
    } else {
        $message = "Error: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
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
            <h3 class="text-center mb-4">Add User</h3>

            <?php if ($message): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="row">
                    <div class="col-12 mb-3">
                        <label>Full Name</label>
                        <input type="text" required class="form-control" name="name">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Email</label>
                        <input type="email" required class="form-control" name="email">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Password</label>
                        <input type="password" required class="form-control" name="password">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Role</label>
                        <select class="form-control" name="role">
                            <option value="user">User</option>
                            <option value="trainer">Trainer</option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label>Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Gender</label>
                        <select class="form-control" name="gender">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="col-6 mb-3">
                        <label>Height (cm)</label>
                        <input type="number" step="0.01" class="form-control" name="height_cm">
                    </div>

                    <div class="col-6 mb-3">
                        <label>Weight (kg)</label>
                        <input type="number" step="0.01" class="form-control" name="weight_kg">
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">Add User</button>
                <a href="view_users.php" class="btn btn-secondary">View Users</a>

            </form>

        </div>
    </div>

</div>

</body>
</html>
