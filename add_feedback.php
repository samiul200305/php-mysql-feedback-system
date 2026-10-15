<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all places
$placesResult = $conn->query("SELECT place_id, place_name FROM places ORDER BY place_name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $place_id = $_POST['place_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $sql = "INSERT INTO feedback (user_id, place_id, rating, comments, submitted_at) 
            VALUES ($user_id, $place_id, $rating, '$comments', NOW())";

    if ($conn->query($sql)) {
        $feedback_id = $conn->insert_id;

        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = "uploads/feedback/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['images']['name'][$key]);
                $targetFile = $uploadDir . time() . "_" . $fileName;

                if (move_uploaded_file($tmp_name, $targetFile)) {
                    $sqlImg = "INSERT INTO feedback_images (feedback_id, image_path, uploaded_at) 
                               VALUES ($feedback_id, '". $conn->real_escape_string(time() . "_" . $fileName) ."', NOW())";
                    $conn->query($sqlImg);
                }
            }
        }

        header("Location: index.php");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Feedback - FeedbackHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #FFD700, #000000);
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
}
.container {
    max-width: 600px;
    margin-top: 50px;
    background: linear-gradient(135deg, #fff8b0, #333333);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
}
h3 {
    text-align: center;
    font-weight: bold;
    color: #000;
    text-shadow: 0 0 5px #FFD700;
    margin-bottom: 30px;
}
.form-control, select {
    border-radius: 10px;
    border: 2px solid #FFD700;
    box-shadow: 0 0 5px #000;
    transition: all 0.3s;
}
.form-control:focus, select:focus {
    border-color: #FFEA00;
    box-shadow: 0 0 10px #FFD700;
    outline: none;
}
.btn-submit {
    background: linear-gradient(90deg, #FFD700, #FFEA00);
    color: black;
    font-weight: bold;
    border-radius: 12px;
    width: 100%;
    transition: all 0.3s;
}
.btn-submit:hover {
    box-shadow: 0 0 15px #FFD700, 0 0 25px #FFEA00;
    transform: scale(1.05);
}
#preview img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 10px;
    margin-top: 10px;
    border: 2px solid #FFD700;
}
</style>
</head>
<body>

<div class="container">
<h3>Add Your Feedback</h3>

<?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<!-- Back Button -->
<a href="index.php" class="btn btn-submit mb-3" style="width:auto;">&#8592; Back</a>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Select Place</label>
        <select name="place_id" class="form-control" required>
            <option value="">-- Choose a place --</option>
            <?php while($place = $placesResult->fetch_assoc()): ?>
                <option value="<?= $place['place_id'] ?>"><?= htmlspecialchars($place['place_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Rating (1-5)</label>
        <input type="number" name="rating" min="1" max="5" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Comments</label>
        <textarea name="comments" class="form-control" rows="3" required></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Upload Images</label>
        <input type="file" name="images[]" class="form-control" multiple id="images">
        <div id="preview"></div>
    </div>

    <button type="submit" class="btn btn-submit">Submit Feedback</button>
</form>
</div>

<script>
// Image preview
document.getElementById('images').addEventListener('change', function(event) {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    for(let i=0; i<event.target.files.length; i++){
        const file = event.target.files[i];
        const reader = new FileReader();
        reader.onload = function(e){
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>
