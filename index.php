<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'] ?? 0;
$search = $_GET['search'] ?? '';

// Escape search input
$search_sql = $conn->real_escape_string($search);

// Base SQL
$sql = "SELECT f.feedback_id, f.user_id, u.username, p.place_name, f.rating, f.comments, f.submitted_at 
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        JOIN places p ON f.place_id = p.place_id
        WHERE 1=1";

// Logged-in user: show only their feedback by default
if($user_id){
    $sql .= " AND f.user_id = $user_id";
}

// Apply search filter
if(!empty($search_sql)){
    $sql .= " AND (u.username LIKE '%$search_sql%' OR p.place_name LIKE '%$search_sql%' OR f.comments LIKE '%$search_sql%')";
}

$sql .= " ORDER BY f.submitted_at DESC";

$result = $conn->query($sql);
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
    $allFeedback[] = $row;
}

// Fetch all feedback for JS Show All button
$allFeedbackQuery = $conn->query("SELECT f.feedback_id, f.user_id, u.username, p.place_name, f.rating, f.comments, f.submitted_at 
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        JOIN places p ON f.place_id = p.place_id
        ORDER BY f.submitted_at DESC");
$allFeedbackJS = [];
while($row = $allFeedbackQuery->fetch_assoc()){
    $feedback_id = $row['feedback_id'];
    $images = $conn->query("SELECT image_path FROM feedback_images WHERE feedback_id=$feedback_id");
    $imgArray = [];
    if($images->num_rows > 0){
        while($img = $images->fetch_assoc()){
            $imgArray[] = "uploads/feedback/" . htmlspecialchars($img['image_path']);
        }
    }
    $row['images'] = $imgArray;
    $allFeedbackJS[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback.com - Full Page Review</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: linear-gradient(135deg,#FFD700,#000); font-family:'Poppins',sans-serif; color:#fff; margin:0; overflow-x:hidden; }
.navbar { background: #000; position:fixed; top:0; width:100%; z-index:200; }
.navbar .container { display:flex; justify-content:space-between; align-items:center; }
.fancy-logo { font-size:1.8rem; color:#FFD700; font-weight:bold; }
.btn-show-all { margin-left:20px; background:#FFD700; color:#000; font-weight:bold; border:none; border-radius:10px; padding:8px 15px; cursor:pointer; transition:0.3s; }
.btn-show-all:hover { transform:scale(1.05); box-shadow:0 0 12px #fff200; }
.carousel-container { scroll-snap-type:y mandatory; overflow-y:auto; height:calc(100vh - 70px); scroll-behavior:smooth; padding: 100px 0 40px 0; }
.feedback-card { width:80vw; height:60vh; background: linear-gradient(135deg,#fff8b0,#333); display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; scroll-snap-align:start; padding:20px; margin:0 auto 30px auto; border-radius:20px; box-shadow:0 8px 25px rgba(0,0,0,0.4); transition: transform 0.5s ease, opacity 0.5s ease; position:relative; }
.feedback-card::before { content:""; position:absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); border-radius:20px; z-index:0; }
.feedback-card * { position:relative; z-index:1; color:#fff; }
.feedback-image-container { width: 100%; max-height: 40%; margin: 15px 0; display: flex; justify-content: center; align-items: center; position: relative; }
.feedback-full-photo { width: 100%; max-height: 100%; object-fit: cover; border-radius: 15px; cursor: pointer; transition: transform 0.3s; }
.feedback-full-photo:hover { transform: scale(1.05); }
.no-photo { width: 100%; height: 100%; background: rgba(255,255,255,0.2); display:flex; justify-content:center; align-items:center; font-size:1.5rem; border-radius:15px; color:#fff; font-weight:bold; }
.feedback-actions { position: absolute; top: 10px; right: 10px; z-index: 2; font-weight:bold; font-size:1rem; }
.feedback-actions a { color:#FF4444; text-decoration:none; }
.feedback-actions a:hover { text-decoration:underline; }
.btn-glow { background: linear-gradient(90deg,#FFD700,#FFEA00); border:none; color:#000; font-weight:bold; border-radius:12px; padding:12px 25px; margin-top:20px; }
.btn-glow:hover { transform:scale(1.05); box-shadow:0 0 12px #fff200; }
.feedback-card.active { transform: scale(1.05); opacity:1; }
.feedback-card.inactive { transform: scale(0.95); opacity:0.7; }
.lightbox { display:none; position:fixed; z-index:1050; padding-top:50px; left:0; top:0; width:100%; height:100%; background: rgba(0,0,0,0.95); overflow:auto; }
.lightbox-content { max-width:90%; max-height:80vh; border-radius:10px; display:block; margin:0 auto; }
.close { position:absolute; top:20px; right:35px; color:#FFD700; font-size:40px; cursor:pointer; }
.lightbox-details { color:white; margin-top:20px; text-align:center; max-width:800px; margin-left:auto; margin-right:auto; }
.search-bar { padding:10px 0; text-align:center; position:sticky; top:70px; background: rgba(0,0,0,0.6); z-index:100; }
.search-bar input { width:300px; max-width:80%; padding:8px 12px; border-radius:10px; border:none; outline:none; }
.search-bar button { margin-left:10px; padding:8px 15px; border-radius:10px; border:none; background: linear-gradient(90deg,#FFD700,#FFEA00); color:#000; font-weight:bold; }
.search-bar button:hover { transform:scale(1.05); box-shadow:0 0 12px #fff200; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <div>
        <a class="fancy-logo" href="index.php">
            <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : "PROJECT"; ?>
        </a>
        <?php if(isset($_SESSION['user_id'])){ ?>
            <button class="btn-show-all" id="showAllBtn">SHOW ALL REVIEWS</button>
        <?php } ?>
    </div>
    <div class="d-flex">
      <?php if(!isset($_SESSION['user_id'])) { ?>
        <a href="login.php" class="btn btn-warning me-2">Login</a>
        <a href="signup.php" class="btn btn-outline-light">Sign Up</a>
      <?php } else { ?>
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
      <?php } ?>
    </div>
  </div>
</nav>

<!-- Search Bar -->
<div class="search-bar">
    <form method="GET">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by user, place, or comment...">
        <button type="submit">Search</button>
    </form>
</div>

<!-- Carousel -->
<div class="carousel-container" id="carousel">
    <?php foreach($allFeedback as $fb){ ?>
    <div class="feedback-card inactive" data-id="<?= $fb['feedback_id'] ?>" data-user="<?= $fb['user_id'] ?>">
        <h2><?= htmlspecialchars($fb['username']) ?></h2>
        <h5>at <?= htmlspecialchars($fb['place_name']) ?></h5>
        <div class="feedback-image-container">
            <?php 
            if(count($fb['images'])>0){
                echo "<img src='".$fb['images'][0]."' class='feedback-full-photo' onclick='openLightbox(".$fb['feedback_id'].",0)'>";
            } else {
                echo "<div class='no-photo'>No Photo</div>";
            }
            if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $fb['user_id']){
                echo "<div class='feedback-actions'><a href='delete_feedback.php?id=".$fb['feedback_id']."' onclick=\"return confirm('Are you sure?');\">Delete</a></div>";
            }
            ?>
        </div>
        <p style="font-size:1.5rem;"><?php for($i=1;$i<=5;$i++) echo $i<=$fb['rating']?"⭐":"☆"; ?></p>
        <p style="font-size:1.2rem;"><?= nl2br(htmlspecialchars($fb['comments'])) ?></p>
        <small><?= $fb['submitted_at'] ?></small>
    </div>
    <?php } ?>

    <!-- Add Feedback Button -->
    <?php if(isset($_SESSION['user_id'])){ ?>
    <div class="feedback-card">
        <a href="add_feedback.php" class="btn btn-lg btn-glow">+ Add Feedback</a>
    </div>
    <?php } ?>
</div>

<div id="lightbox" class="lightbox">
  <span class="close" onclick="closeLightbox()">&times;</span>
  <img class="lightbox-content" id="lightbox-img">
  <div class="lightbox-details" id="lightbox-details"></div>
</div>

<script>
let userFeedback = <?= json_encode($allFeedback) ?>;
let allFeedback = <?= json_encode($allFeedbackJS) ?>;

function openLightbox(feedbackId,index){
    const fb = allFeedback.find(f=>f.feedback_id==feedbackId);
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

document.getElementById('showAllBtn')?.addEventListener('click',()=>{
    const container = document.getElementById('carousel');
    container.innerHTML = '';
    allFeedback.forEach(fb=>{
        const div = document.createElement('div');
        div.className = 'feedback-card inactive';
        div.setAttribute('data-id', fb.feedback_id);
        div.innerHTML = `
            <h2>${fb.username}</h2>
            <h5>at ${fb.place_name}</h5>
            <div class="feedback-image-container">
                ${fb.images.length>0 ? "<img src='"+fb.images[0]+"' class='feedback-full-photo' onclick='openLightbox("+fb.feedback_id+",0)'>" : "<div class='no-photo'>No Photo</div>"}
                ${fb.user_id == <?= $user_id ?> ? "<div class='feedback-actions'><a href='delete_feedback.php?id="+fb.feedback_id+"' onclick=\"return confirm('Are you sure?');\">Delete</a></div>" : ""}
            </div>
            <p style='font-size:1.5rem;'>${'⭐'.repeat(fb.rating)}${'☆'.repeat(5-fb.rating)}</p>
            <p style='font-size:1.2rem;'>${fb.comments}</p>
            <small>${fb.submitted_at}</small>
        `;
        container.appendChild(div);
    });
    container.innerHTML += `<div class="feedback-card"><a href="add_feedback.php" class="btn btn-lg btn-glow">+ Add Feedback</a></div>`;
});

// Zoom effect
const observer = new IntersectionObserver(entries=>{
    entries.forEach(entry=>{
        if(entry.isIntersecting){
            entry.target.classList.add('active');
            entry.target.classList.remove('inactive');
        } else {
            entry.target.classList.remove('active');
            entry.target.classList.add('inactive');
        }
    });
},{threshold:0.5});
document.querySelectorAll('.feedback-card').forEach(c=>observer.observe(c));
</script>

</body>
</html>
