<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$feedback_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Verify feedback belongs to this user
$result = $conn->query("SELECT * FROM feedback WHERE feedback_id=$feedback_id AND user_id=$user_id");
if($result->num_rows == 0){
    die("Feedback not found or you don't have permission.");
}

// Delete images from server
$images = $conn->query("SELECT image_path FROM feedback_images WHERE feedback_id=$feedback_id");
while($img = $images->fetch_assoc()){
    $file = "uploads/feedback/" . $img['image_path'];
    if(file_exists($file)) unlink($file); // delete file
}

// Delete from feedback_images table
$conn->query("DELETE FROM feedback_images WHERE feedback_id=$feedback_id");

// Delete the feedback itself
$conn->query("DELETE FROM feedback WHERE feedback_id=$feedback_id AND user_id=$user_id");

// Redirect to homepage
header("Location: index.php");
exit();
?>
