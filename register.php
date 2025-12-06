<?php
include('config.php');

$name = $email = $password = $date_of_birth = $gender = $height_cm = $weight_kg = "";
$name_err = $email_err = $password_err = "";
$registration_msg = "";

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate Name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your full name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate Email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $email_err = "This email is already registered.";
        }
        mysqli_stmt_close($stmt);
    }

    // Validate Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 3) {
        $password_err = "Password must be at least 3 characters long.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Other optional fields
    $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : NULL;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : NULL;
    $height_cm = !empty($_POST['height_cm']) ? $_POST['height_cm'] : NULL;
    $weight_kg = !empty($_POST['weight_kg']) ? $_POST['weight_kg'] : NULL;

    // Insert if no errors
    if (empty($name_err) && empty($email_err) && empty($password_err)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password_hash, role, date_of_birth, gender, height_cm, weight_kg)
                VALUES (?, ?, ?, 'user', ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssss", 
            $name, $email, $hashed_password, $date_of_birth, $gender, $height_cm, $weight_kg
        );

        if (mysqli_stmt_execute($stmt)) {
            $registration_msg = '<div class="alert alert-success text-center">Registration successful!</div>';
            // Clear form fields
            $name = $email = $password = $date_of_birth = $gender = $height_cm = $weight_kg = "";
        } else {
            $registration_msg = '<div class="alert alert-danger text-center">Error: Unable to register. Try again.</div>';
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto shadow-sm" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="text-center mb-4">User Registration</h3>
            <?php echo $registration_msg; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                        <span class="invalid-feedback"><?php echo $email_err; ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo $date_of_birth; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo ($gender=='male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($gender=='female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($gender=='other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Height (cm)</label>
                        <input type="number" step="0.01" name="height_cm" class="form-control" value="<?php echo $height_cm; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Weight (kg)</label>
                        <input type="number" step="0.01" name="weight_kg" class="form-control" value="<?php echo $weight_kg; ?>">
                    </div>
                </div>

                <div class="form-group text-center mt-4">
                    <input type="submit" class="btn btn-primary px-4" value="Register">
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
