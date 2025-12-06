<?php
session_start();
include('config.php');

if (!isset($_SESSION["id"]) || $_SESSION["role"] !== 'trainer') {
    header("location: ../login.php");
    exit;
}

$trainer_id = $_SESSION["id"];
$trainer_name = $_SESSION["name"] ?? "Trainer";

// Handle Reply Submission (Same Page)
if ($_POST['action'] ?? '' === 'reply') {
    $feedback_id = intval($_POST['feedback_id']);
    $response_text = trim($_POST['response_text']);
    $suggested_modifications = trim($_POST['suggested_modifications'] ?? '');

    if (!empty($response_text)) {
        $stmt = $conn->prepare("INSERT INTO trainer_responses (feedback_id, trainer_id, response_text, suggested_modifications) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $feedback_id, $trainer_id, $response_text, $suggested_modifications);
        
        if ($stmt->execute()) {
            $_SESSION['msg'] = "<div class='alert alert-success'>Reply sent successfully!</div>";
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Failed to send reply.</div>";
        }
        $stmt->close();
    }
    header("Location: dashboard.php");
    exit;
}

// Stats
$total_routines = $conn->query("SELECT COUNT(*) FROM public_routines WHERE trainer_id = $trainer_id")->fetch_row()[0];
$total_feedback = $conn->query("
    SELECT COUNT(f.id) FROM feedback f 
    JOIN public_routines pr ON f.routine_id = pr.id 
    WHERE pr.trainer_id = $trainer_id
")->fetch_row()[0];

$unreplied = $conn->query("
    SELECT COUNT(f.id) FROM feedback f 
    JOIN public_routines pr ON f.routine_id = pr.id 
    LEFT JOIN trainer_responses tr ON f.id = tr.feedback_id
    WHERE pr.trainer_id = $trainer_id AND tr.id IS NULL
")->fetch_row()[0];

// Fetch Feedback + Replies
$feedbacks = $conn->query("
    SELECT 
        f.id AS feedback_id,
        f.rating,
        f.comment,
        f.created_at AS feedback_date,
        u.name AS user_name,
        pr.title AS routine_title,
        tr.response_text,
        tr.suggested_modifications,
        tr.created_at AS reply_date
    FROM feedback f
    JOIN users u ON f.user_id = u.id
    JOIN public_routines pr ON f.routine_id = pr.id
    LEFT JOIN trainer_responses tr ON f.id = tr.feedback_id AND tr.trainer_id = $trainer_id
    WHERE pr.trainer_id = $trainer_id
    ORDER BY f.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard - Feedback Center</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body { 
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            
    background: linear-gradient(135deg, #141e30, #243b55); color: #fff; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
    .navbar {
    background-color: var(--dark);
    box-shadow: 0 4px 12px var(--shadow);
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar-brand,
.nav-link {
    color: white !important;
    font-weight: 500;
    transition: var(--transition);
}

.navbar-brand:hover,
.nav-link:hover {
    color: var(--accent) !important;
    transform: translateY(-1px);
}

        .card { border-radius: 15px; overflow: hidden; }
        .stat-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: none; }
        .feedback-card { background: #fff; color: #333; transition: all 0.3s; }
        .feedback-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.3); }
        .reply-form { background: #f8f9fa; border-radius: 12px; }
        .rating { color: #ffc107; font-size: 1.3rem; }
        .btn-reply { border-radius: 30px; padding: 10px 25px; }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 font-weight-bold">Trainer Dashboard</h1>
        <p class="lead">Hello, <strong><?= htmlspecialchars($trainer_name) ?></strong> — Manage feedback & guide your trainees!</p>
    </div>

    <!-- Success/Error Message -->
    <?php if (isset($_SESSION['msg'])) { echo $_SESSION['msg']; unset($_SESSION['msg']); } ?>

    <!-- Stats -->
    <div class="row mb-5 text-center">
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <i class="fas fa-dumbbell fa-3x mb-3"></i>
                <h3><?= $total_routines ?></h3>
                <p>My Routines</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <h3><?= $total_feedback ?></h3>
                <p>Total Feedback</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <i class="fas fa-bell fa-3x mb-3 text-warning"></i>
                <h3><?= $unreplied ?></h3>
                <p>Pending Replies</p>
            </div>
        </div>
    </div>

    <!-- Feedback List -->
    <h2 class="text-center mb-4">
        <i class="fas fa-comment-dots"></i> Feedback from Your Trainees
    </h2>

    <?php if ($feedbacks->num_rows > 0): ?>
        <div class="row">
            <?php while ($f = $feedbacks->fetch_assoc()): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card feedback-card shadow-lg">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="text-primary font-weight-bold">
                                        <?= htmlspecialchars($f['routine_title']) ?>
                                    </h5>
                                    <p class="mb-1">
                                        <strong><?= htmlspecialchars($f['user_name']) ?></strong>
                                        <small class="text-muted">• <?= date('M j, Y', strtotime($f['feedback_date'])) ?></small>
                                    </p>
                                </div>
                                <div class="text-warning rating">
                                    <?= str_repeat('★', $f['rating']) . str_repeat('☆', 5 - $f['rating']) ?>
                                </div>
                            </div>

                            <blockquote class="border-left pl-3 border-primary">
                                "<?= nl2br(htmlspecialchars($f['comment'])) ?>"
                            </blockquote>

                            <!-- Trainer Reply (if exists) -->
                            <?php if ($f['response_text']): ?>
                                <div class="reply-form p-3 mt-3 border-left border-success">
                                    <p class="mb-2"><strong>Your Reply:</strong></p>
                                    <p class="mb-1"><?= nl2br(htmlspecialchars($f['response_text'])) ?></p>
                                    <?php if ($f['suggested_modifications']): ?>
                                        <p class="mb-0 text-success"><strong>Suggestions:</strong><br>
                                            <?= nl2br(htmlspecialchars($f['suggested_modifications'])) ?>
                                        </p>
                                    <?php endif; ?>
                                    <small class="text-muted">Sent on <?= date('M j, Y g:i A', strtotime($f['reply_date'])) ?></small>
                                </div>
                            <?php else: ?>
                                <!-- Reply Form -->
                                <form method="POST" class="mt-4 reply-form p-4">
                                    <input type="hidden" name="action" value="reply">
                                    <input type="hidden" name="feedback_id" value="<?= $f['feedback_id'] ?>">
                                    
                                    <div class="form-group">
                                        <label><strong>Reply to this trainee:</strong></label>
                                        <textarea name="response_text" class="form-control" rows="3" placeholder="Thank you for your feedback! Here's my advice..." required></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Suggested Modifications (optional):</label>
                                        <textarea name="suggested_modifications" class="form-control" rows="3" placeholder="E.g., Try 4 sets instead of 3, add 10 min cardio, increase protein intake..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-reply">
                                        Send Reply
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-5x text-white opacity-50 mb-4"></i>
            <h3>No feedback yet</h3>
            <p>Your trainees haven't left feedback on your routines yet. Keep inspiring them!</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>