<?php
session_start();
include "db.php";

// Check if admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $place_name = $_POST['place_name'];
    $category_id = $_POST['category_id'];

    $sql = "INSERT INTO places (place_name, category_id) VALUES ('$place_name','$category_id')";
    if($conn->query($sql)){
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Error: ".$conn->error;
    }
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Place - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#1a1a2e; color:#fff; font-family:'Poppins',sans-serif; min-height:100vh; display:flex; justify-content:center; align-items:center; }
.card { background:#0f3460; padding:30px; border-radius:15px; width:100%; max-width:500px; box-shadow:0 6px 15px rgba(0,0,0,0.3); }
h3 { color:#FFD700; text-align:center; margin-bottom:20px; }
input, select { border-radius:10px; border:none; padding:10px; width:100%; margin-bottom:15px; }
input:focus, select:focus { outline:none; box-shadow:0 0 8px #FFD700; }
.btn-glow { background:#FFD700; color:#0f3460; font-weight:bold; width:100%; padding:10px; border:none; border-radius:10px; transition:0.3s; }
.btn-glow:hover { transform:scale(1.05); box-shadow:0 0 12px #fff200; }
a.back { display:block; text-align:center; margin-top:15px; color:#FFD700; text-decoration:none; }
a.back:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="card">
    <h3>Add New Place</h3>
    <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="place_name" placeholder="Place Name" required>
        <select name="category_id" required>
            <option value="" disabled selected>Select Category</option>
            <?php while($c = $categories->fetch_assoc()){ ?>
            <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
            <?php } ?>
        </select>
        <button type="submit" class="btn btn-glow">Add Place</button>
    </form>
    <a href="admin_dashboard.php" class="back">&larr; Back to Dashboard</a>
</div>

</body>
</html>
