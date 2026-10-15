<?php
session_start();
include "db.php";

// Check if admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Handle delete request
if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM places WHERE place_id=$delete_id");
    header("Location: admin_dashboard.php");
    exit();
}

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
<title>Admin Dashboard - FeedbackHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#1a1a2e; color:#fff; min-height:100vh; font-family:'Poppins',sans-serif; }
h3 { margin-top:30px; color:#FFD700; }
.table thead { background:#0f3460; color:#FFD700; }
.btn-glow { background:#0f3460; color:#FFD700; font-weight:bold; border-radius:10px; transition:0.3s; }
.btn-glow:hover { box-shadow:0 0 10px #FFD700; transform:scale(1.05); }
.btn-delete { background:#ff4d4d; color:#fff; font-weight:bold; border:none; border-radius:6px; padding:5px 10px; transition:0.3s; }
.btn-delete:hover { transform:scale(1.05); box-shadow:0 0 8px #ff9999; }
a.logout { float:right; margin-top:10px; color:#FFD700; text-decoration:none; }
a.logout:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="container">
    <a href="logout.php" class="logout">Logout</a>
    <h3>Admin Dashboard</h3>
    <a href="add_place.php" class="btn btn-glow mb-3">+ Add New Place</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Place Name</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()){ ?>
            <tr>
                <td><?= $row['place_id'] ?></td>
                <td><?= htmlspecialchars($row['place_name']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td>
                    <a href="admin_dashboard.php?delete_id=<?= $row['place_id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this place?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
