<?php
include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='login.php'>login</a> to give feedback.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $place_id = $_POST['place_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $sql = "INSERT INTO feedback (user_id, place_id, rating, comments) 
            VALUES ('$user_id', '$place_id', '$rating', '$comments')";
    if ($conn->query($sql)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch places for dropdown
$places = $conn->query("SELECT * FROM places");
?>

<h2>Give Feedback</h2>
<form method="POST">
    Place:
    <select name="place_id" required>
        <?php while ($p = $places->fetch_assoc()): ?>
            <option value="<?= $p['place_id'] ?>"><?= $p['place_name'] ?></option>
        <?php endwhile; ?>
    </select><br>

    Rating:
    <select name="rating" required>
        <option value="1">1 ⭐</option>
        <option value="2">2 ⭐</option>
        <option value="3">3 ⭐</option>
        <option value="4">4 ⭐</option>
        <option value="5">5 ⭐</option>
    </select><br>

    Comments:<br>
    <textarea name="comments"></textarea><br>
    <button type="submit">Submit</button>
</form>
