<?php
session_start();
include "db.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get place_id from URL
$place_id = $_GET['place_id'] ?? null;

if ($place_id) {
    // Fetch place details
    $place = $conn->query("SELECT p.place_name, c.category_name 
                           FROM places p 
                           JOIN categories c ON p.category_id = c.category_id 
                           WHERE p.place_id = $place_id")->fetch_assoc();

    // Fetch feedbacks
    $feedbacks = $conn->query("SELECT f.*, u.username 
                               FROM feedback f 
                               JOIN users u ON f.user_id = u.user_id 
                               WHERE f.place_id = $place_id
                               ORDER BY f.submitted_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Feedbacks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
  <?php if ($place) { ?>
    <h3>Feedbacks for <?= htmlspecialchars($place['place_name']) ?> (<?= htmlspecialchars($place['category_name']) ?>)</h3>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <?php if ($feedbacks->num_rows > 0) { ?>
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>User</th>
            <th>Rating</th>
            <th>Comments</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php while($fb = $feedbacks->fetch_assoc()) { ?>
          <tr>
            <td><?= htmlspecialchars($fb['username']) ?></td>
            <td><?= $fb['rating'] ?> ⭐</td>
            <td><?= htmlspecialchars($fb['comments']) ?></td>
            <td><?= $fb['submitted_at'] ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p class="text-muted">No feedback yet for this place.</p>
    <?php } ?>

  <?php } else { ?>
    <div class="alert alert-danger">Invalid place selected
