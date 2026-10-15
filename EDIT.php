<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$feedback_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch feedback
$result = $conn->query("SELECT * FROM feedback WHERE feedback_id=$feedback_id AND user_id=$user_id");
if($result->num_rows == 0){
    die("Feedback not found or you don't have permission.");
}
$feedback = $result->fetch_assoc();

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $rating = intval($_POST['rating']);
    $comments = $conn->real_escape_string($_POST['comments']);
    $conn->query("UPDATE feedback SET rating=$rating, comments='$comments' WHERE feedback_id=$feedback_id AND user_id=$user_id");
    header("Location: full_feedback.php"); // go back to feedback page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Feedback</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Your Feedback</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Rating (1-5)</label>
            <input type="number" name="rating" class="form-control" min="1" max="5" value="<?= $feedback['rating'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Comments</label>
            <textarea name="comments" class="form-control" rows="4" required><?= htmlspecialchars($feedback['comments']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Update Feedback</button>
        <a href="full_feedback.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
