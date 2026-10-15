<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role     = $_POST['role']; // get role from form

    $sql = "INSERT INTO users (username, email, password, role) 
            VALUES ('$username', '$email', '$password', '$role')";
    if ($conn->query($sql)) {
        header("Location: login.php");
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
<title>Signup - FeedbackHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
/* --- keep your same design --- */
body {
  background: linear-gradient(135deg, #FFD700, #000000);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Poppins', sans-serif;
}
.card-signup {
  background: linear-gradient(135deg, #fff8b0, #333333);
  color: #000;
  border-radius: 20px;
  padding: 30px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  transition: transform 0.3s, box-shadow 0.3s;
}
.card-signup:hover { transform: scale(1.03); box-shadow: 0 12px 35px rgba(0,0,0,0.5); }
h3 { color:#FFD700; text-align:center; margin-bottom:20px; text-shadow: 0 0 8px #000; }
.input-group-text { background: transparent; border: none; cursor: pointer; color: #FFD700; }
input.form-control { border-radius: 10px; border: 2px solid #FFD700; }
input.form-control:focus { border-color: #FFD700; box-shadow: 0 0 10px #FFD700; outline: none; }
.btn-signup { background: linear-gradient(90deg, #FFD700, #FFEA00); color: #000; font-weight: bold; border-radius: 12px; width: 100%; padding: 10px; transition: all 0.3s; }
.btn-signup:hover { box-shadow: 0 0 15px #FFD700, 0 0 25px #FFEA00; transform: scale(1.05); }
</style>
</head>
<body>

<div class="card-signup">
  <h3>Sign Up</h3>

  <?php if (!empty($error)) { ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php } ?>

  <form method="POST">
    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Password</label>
      <div class="input-group">
        <input type="password" name="password" id="password" class="form-control" required>
        <span class="input-group-text" onclick="togglePassword()">
          <i class="fa-regular fa-eye" id="eye-icon"></i>
        </span>
      </div>
    </div>

    <!-- Role selection -->
    <div class="mb-3">
      <label>Role</label>
      <select name="role" class="form-control" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>
    </div>

    <button type="submit" class="btn btn-signup">Register</button>
  </form>

  <p class="mt-3 text-center">
    Already have an account? <a href="login.php" style="color:#FFD700;">Login</a>
  </p>
</div>

<script>
function togglePassword(){
  const pass = document.getElementById('password');
  const icon = document.getElementById('eye-icon');
  if(pass.type === 'password'){
    pass.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    pass.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
</script>

</body>
</html>
