<?php
// Simple test script
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback_system"; // <-- check DB name here

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "<p><b>✅ Database connected successfully!</b></p>";
}

// Test query
$sql = "SELECT * FROM feedback";
$result = $conn->query($sql);

if(!$result){
    die("Query failed: " . $conn->error);
}

if($result->num_rows > 0){
    echo "<h2>Feedback Found:</h2>";
    while($row = $result->fetch_assoc()){
        echo "<p>ID: ".$row['feedback_id']." | User: ".$row['user_id']." | Place: ".$row['place_id']." | Rating: ".$row['rating']." | Comment: ".$row['comment']."</p>";
    }
} else {
    echo "<p>⚠️ No feedback rows found in database.</p>";
}
?>
