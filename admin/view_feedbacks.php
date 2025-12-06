<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle delete feedback
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM feedback WHERE id = $id");
    echo "<script>alert('Feedback deleted successfully.'); window.location='view_feedbacks.php';</script>";
    exit;
}

// Fetch all feedbacks with user and routine info
$result = mysqli_query($conn, "
    SELECT f.*, u.name AS user_name, r.title AS routine_title
    FROM feedback f
    LEFT JOIN users u ON f.user_id = u.id
    LEFT JOIN public_routines r ON f.routine_id = r.id
    ORDER BY f.created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Feedbacks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">Feedbacks & Trainer Responses</h2>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="row">
            <?php while($f = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($f['user_name']) ?> 
                                <small class="text-muted">(<?= $f['routine_title'] ?: 'N/A' ?>)</small>
                            </h5>
                            <p><strong>Rating:</strong> <?= $f['rating'] ?>/5</p>
                            <p><strong>Comment:</strong> <?= nl2br(htmlspecialchars($f['comment'])) ?></p>
                            <p><small class="text-muted">Posted on <?= $f['created_at'] ?></small></p>

                            <?php
                            // Fetch trainer response
                            $resp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tr.*, u.name AS trainer_name 
                                FROM trainer_responses tr 
                                LEFT JOIN users u ON tr.trainer_id = u.id
                                WHERE tr.feedback_id = {$f['id']}
                                ORDER BY tr.created_at ASC LIMIT 1
                            "));
                            ?>
                            <?php if($resp): ?>
                                <div class="border p-2 mt-2 bg-light">
                                    <p><strong>Trainer (<?= htmlspecialchars($resp['trainer_name']) ?>) Response:</strong></p>
                                    <p><?= nl2br(htmlspecialchars($resp['response_text'])) ?></p>
                                    <?php if($resp['suggested_modifications']): ?>
                                        <p><strong>Suggested Modifications:</strong> <?= nl2br(htmlspecialchars($resp['suggested_modifications'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2 text-right">
                                <a href="?delete=<?= $f['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this feedback?');">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No feedbacks available.</p>
    <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
