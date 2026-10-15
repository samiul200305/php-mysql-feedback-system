<?php
session_start();
include "db.php";

// Fetch all feedback with images
$sql = "SELECT f.feedback_id, u.username, p.place_name, f.rating, f.comments, f.submitted_at 
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        JOIN places p ON f.place_id = p.place_id
        ORDER BY f.submitted_at DESC";

$result = $conn->query($sql);

// Store all feedback + images
$allFeedback = [];
while($row = $result->fetch_assoc()){
    $feedback_id = $row['feedback_id'];
    $images = $conn->query("SELECT image_path FROM feedback_images WHERE feedback_id=$feedback_id");
    $imgArray = [];
    if($images->num_rows > 0){
        while($img = $images->fetch_assoc()){
            $imgArray[] = "uploads/feedback/" . htmlspecialchars($img['image_path']);
        }
    }
    $row['images'] = $imgArray;
    $allFeedback[$feedback_id] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Feedback - FeedbackHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: linear-gradient(135deg,#FFD700,#000); min-height:100vh; font-family:'Poppins',sans-serif; color:#fff; }
.container { margin-top:50px; }
h2 { color:#FFD700; text-shadow:0 0 5px #000; margin-bottom:30px; text-align:center; }
.feedback-card {
    background: linear-gradient(135deg,#fff8b0,#333); color:#000;
    border-radius:16px; padding:20px; margin-bottom:20px;
    box-shadow:0 6px 15px rgba(0,0,0,0.3);
}
.feedback-thumb { width:70px; height:70px; object-fit:cover; border-radius:8px; margin:3px; cursor:pointer; transition:transform 0.2s; }
.feedback-thumb:hover { transform: scale(1.1); }
.btn-glow { background: linear-gradient(90deg,#FFD700,#FFEA00); border:none; color:#000; font-weight:bold; border-radius:12px; padding:10px 20px; transition:0.3s; }
.btn-glow:hover { transform:scale(1.05); box-shadow:0 0 12px #fff200; }
.lightbox { display:none; position:fixed; z-index:1050; padding-top:50px; left:0; top:0; width:100%; height:100%; background: rgba(0,0,0,0.95); overflow:auto; }
.lightbox-content { max-width:90%; max-height:80vh; border-radius:10px; display:block; margin:0 auto; }
.close { position:absolute; top:20px; right:35px; color:#FFD700; font-size:40px; cursor:pointer; }
.lightbox-details { color:white; margin-top:20px; text-align:left; max-width:800px; margin-left:auto; margin-right:auto; }
a.back-btn { display:inline-block; margin-bottom:20px; color:#fff; text-decoration:none; }
a.back-btn:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Home</a>
    <h2>All Feedback</h2>

    <div class="row">
        <?php foreach($allFeedback as $fb){ ?>
            <div class="col-lg-6">
                <div class="feedback-card">
                    <h5><?= htmlspecialchars($fb['username']) ?> 
                        <small class="text-muted">at <?= htmlspecialchars($fb['place_name']) ?></small>
                    </h5>
                    <p><?php for($i=1;$i<=5;$i++) echo $i <= $fb['rating'] ? "⭐" : "☆"; ?></p>
                    <p><?= nl2br(htmlspecialchars($fb['comments'])) ?></p>
                    <div>
                        <?php if(count($fb['images'])>0){
                            foreach($fb['images'] as $index=>$imgPath){
                                echo "<img src='$imgPath' class='feedback-thumb' onclick='openLightbox(".$fb['feedback_id'].",$index)'>";
                            }
                        } else { echo "<span class='text-muted'>No Photo</span>"; } ?>
                    </div>
                    <small class="text-muted"><?= $fb['submitted_at'] ?></small>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" class="lightbox">
    <span class="close" onclick="closeLightbox()">&times;</span>
    <div class="text-center">
        <img id="lightbox-img" class="lightbox-content">
        <div class="lightbox-details" id="lightbox-details"></div>
    </div>
</div>

<script>
const feedbackData = <?= json_encode($allFeedback) ?>;
let currentFeedback = null;
let currentIndex = 0;

function openLightbox(feedbackId,index){
    currentFeedback = feedbackId;
    currentIndex = index;
    const fb = feedbackData[feedbackId];

    document.getElementById('lightbox').style.display='block';
    document.getElementById('lightbox-img').src = fb.images[index];
    document.getElementById('lightbox-details').innerHTML = `
        <h4 style="color:#FFD700;">${fb.username} <small style="color:#fff;">at ${fb.place_name}</small></h4>
        <p>${'⭐'.repeat(fb.rating)}${'☆'.repeat(5-fb.rating)}</p>
        <p>${fb.comments}</p>
        <small style="color:#fff;">${fb.submitted_at}</small>
    `;
}

function closeLightbox(){ document.getElementById('lightbox').style.display='none'; }
</script>

</body>
</html>
