<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include "db.php";

// Fetch all places
$sql = "SELECT p.place_id, p.place_name, c.category_name 
        FROM places p
        JOIN categories c ON p.category_id = c.category_id
        ORDER BY c.category_name, p.place_name ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Manage Places</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Manage Places</h3>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>

  <a href="add_place.php" class="btn btn-success mb-3">+ Add New Place</a>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Place Name</th>
        <th>Category</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?= $row['place_id'] ?></td>
        <td><?= htmlspecialchars($row['place_name']) ?></td>
        <td><?= htmlspecialchars($row['category_name']) ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

</body>
</html>
