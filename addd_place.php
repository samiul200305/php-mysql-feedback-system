<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $place_name = $_POST['place_name'];
    $category_id = $_POST['category_id'];

    $sql = "INSERT INTO places (place_name, category_id) VALUES ('$place_name', '$category_id')";
    if ($conn->query($sql)) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Place</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
  <div class="card shadow p-4">
    <h3 class="mb-3">Add New Place</h3>

    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Place Name</label>
        <input type="text" name="place_name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          <option value="" disabled selected>Select Category</option>
          <?php while($c = $categories->fetch_assoc()) { ?>
            <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
          <?php } ?>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Add Place</button>
      <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>

</body>
</html>
