<?php
session_start();
include('../config.php');

if (!isset($_SESSION["id"]) || $_SESSION["role"] !== 'user') {
    header("location: ../login.php");
    exit;
}

$user_id = $_SESSION["id"];
$routine_id = intval($_GET['id'] ?? 0);

if ($routine_id <= 0) {
    header("location: user_dashboard.php");
    exit;
}

// Handle Feedback Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    
    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, routine_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $routine_id, $rating, $comment);
        $stmt->execute();
        $stmt->close();
        header("Location: routine_details.php?id=$routine_id");
        exit;
    }
}

// Fetch Routine Details
$routine = $conn->query("
    SELECT pr.*, u.name AS trainer_name 
    FROM public_routines pr 
    LEFT JOIN users u ON pr.trainer_id = u.id 
    WHERE pr.id = $routine_id
")->fetch_assoc();

if (!$routine) {
    echo "Routine not found.";
    exit;
}

// Fetch Feedbacks
$feedbacks = $conn->query("
    SELECT f.*, u.name AS user_name, tr.response_text, tr.suggested_modifications
    FROM feedback f
    LEFT JOIN users u ON f.user_id = u.id
    LEFT JOIN trainer_responses tr ON f.id = tr.feedback_id
    WHERE f.routine_id = $routine_id
    ORDER BY f.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($routine['title']) ?> - Routine Details</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<style>
.card-hover { transition: all 0.3s; }
.card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
.feedback-card { border-left: 5px solid #007bff; margin-bottom: 15px; }
.trainer-response { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 5px; }
</style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container py-5">

    <!-- Routine Details -->
    <div class="card shadow-lg card-hover mb-5">
        <?php if ($routine['image_url']): ?>
            <img src="../admin/<?= htmlspecialchars($routine['image_url']) ?>" class="card-img-top" style="height:300px; object-fit:cover;">
        <?php endif; ?>
        <div class="card-body">
            <h2 class="card-title text-success"><?= htmlspecialchars($routine['title']) ?></h2>
            <p class="text-muted mb-2">
                <i class="fas fa-user"></i> <?= $routine['trainer_name'] ?? 'Admin' ?> | 
                <i class="fas fa-clock"></i> <?= $routine['duration_minutes'] ?? 0 ?> min | 
                <span class="badge badge-<?= $routine['difficulty']=='beginner'?'success':($routine['difficulty']=='intermediate'?'warning':'danger')?> text-uppercase"><?= ucfirst($routine['difficulty']) ?></span>
            </p>
            <p><?= nl2br(htmlspecialchars($routine['description'] ?? $routine['instructions'])) ?></p>
        </div>
    </div>

    <!-- Feedback Form -->
    <div class="card shadow-lg mb-5">
        <div class="card-body">
            <h4 class="card-title text-primary"><i class="fas fa-comment-dots"></i> Leave Your Feedback</h4>
            <form method="post">
                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" class="form-control" required>
                        <option value="">Select Rating</option>
                        <?php for($i=1;$i<=5;$i++): ?>
                            <option value="<?= $i ?>"><?= $i ?> Star<?= $i>1?'s':'' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" name="submit_feedback" class="btn btn-success"><i class="fas fa-paper-plane"></i> Submit Feedback</button>
            </form>
        </div>
    </div>

    <!-- Feedbacks -->
    <h3 class="mb-4 text-center text-info"><i class="fas fa-comments"></i> Feedbacks</h3>
    <div class="row">
        <?php
        $count = 0;
        while ($fb = $feedbacks->fetch_assoc()):
            if($count % 3 === 0 && $count > 0) echo '</div><div class="row">';
            $count++;
        ?>
            <div class="col-md-4">
                <div class="card feedback-card shadow p-3">
                    <p class="font-weight-bold"><?= htmlspecialchars($fb['user_name']) ?></p>
                    <p>
                        <?= str_repeat('★',$fb['rating']) . str_repeat('☆', 5 - $fb['rating']) ?>
                    </p>
                    <p><?= nl2br(htmlspecialchars($fb['comment'])) ?></p>
                    <?php if($fb['response_text']): ?>
                        <div class="trainer-response">
                            <strong>Trainer Response:</strong>
                            <p><?= nl2br(htmlspecialchars($fb['response_text'])) ?></p>
                            <?php if($fb['suggested_modifications']): ?>
                                <small><strong>Suggestions:</strong> <?= nl2br(htmlspecialchars($fb['suggested_modifications'])) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <small class="text-muted d-block mt-2"><?= date('M d, Y H:i', strtotime($fb['created_at'])) ?></small>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
