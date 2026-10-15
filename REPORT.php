<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle search query
$search = $_GET['search'] ?? '';

// Fetch feedback summary
$sql = "SELECT p.place_name, COUNT(f.feedback_id) AS total_feedback, 
               ROUND(AVG(f.rating),2) AS avg_rating,
               SUM((SELECT COUNT(*) FROM feedback_images fi WHERE fi.feedback_id=f.feedback_id)) AS total_photos
        FROM feedback f
        JOIN places p ON f.place_id = p.place_id";

if ($search != "") {
    $searchEscaped = $conn->real_escape_string($search);
    $sql .= " WHERE p.place_name LIKE '%$searchEscaped%'";
}

$sql .= " GROUP BY p.place_id ORDER BY total_feedback DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Same color theme */
body { background-color: #f1f5f9; }
.fancy-logo {
    font-size: 1.8rem;
    color: #1e3a8a;
    font-weight: bold;
    text-shadow: 0 0 8px #3b82f6;
}
.fancy-logo:hover {
    text-shadow: 0 0 15px #3b82f6;
    transform: scale(1.05);
}
.glow-box {
    background-color: #1e3a8a;
    display:inline-block;
    padding: 20px 40px;
    border-radius: 15px;
    box-shadow: 0 0 15px #3b82f6;
}
.glow-text {
    color:white;
    font-weight:bold;
    text-shadow: 0 0 8px #3b82f6;
}
table th, table td {
    text-align:center;
}
form input[type="text"] {
    border: 2px solid #3b82f6;
    box-shadow: 0 0 8px #3b82f6;
    transition: all 0.3s;
    border-radius:5px;
}
form input[type="text"]:focus {
    border-color: #2563eb;
    box-shadow: 0 0 12px #3b82f6;
    outline: none;
}
form button {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color:white;
    transition: all 0.3s;
    border-radius:5px;
}
form button:hover {
    background-color: #2563eb;
    box-shadow: 0 0 10px #3b82f6, 0 0 15px #3b82f6;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fancy-logo" href="index.php">FeedbackHub</a>
    <div class="d-flex">
      <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<!-- Report Heading and Search -->
<div class="container d-flex justify-content-between align-items-center my-5">
    <div class="glow-box">
        <h2 class="glow-text mb-0">Feedback Report</h2>
    </div>
    <form method="GET" class="d-flex ms-3" style="max-width:400px;">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control me-2" placeholder="Search by place...">
        <button type="submit" class="btn">Search</button>
    </form>
</div>

<!-- Report Table -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="table-responsive shadow rounded bg-white p-3">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Place</th>
                            <th>Total Feedback</th>
                            <th>Average Rating</th>
                            <th>Total Photos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0){ 
                            while($row = $result->fetch_assoc()){ ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['place_name']) ?></td>
                                    <td><?= $row['total_feedback'] ?></td>
                                    <td><?= $row['avg_rating'] ?> ⭐</td>
                                    <td><?= $row['total_photos'] ?></td>
                                </tr>
                        <?php } } else { ?>
                            <tr><td colspan="4" class="text-center text-muted">No data found</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
<p class="mb-0">&copy; <?= date("Y") ?> FeedbackHub. All rights reserved.</p>
</footer>

</body>
</html>