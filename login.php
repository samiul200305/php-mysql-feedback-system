<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = $_POST['role']; // take role from form

    $sql = "SELECT * FROM users WHERE email='$email' AND role='$role'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 

            if ($user['role'] == "admin") {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No $role account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - FeedbackHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #FFD700, #000000);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Poppins', sans-serif;
}

.card-login {
  background: linear-gradient(135deg, #fff8b0, #333333);
  color: #000;
  border-radius: 20px;
  padding: 30px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  transition: transform 0.3s, box-shadow 0.3s;
}
.card-login:hover {
  transform: scale(1.03);
  box-shadow: 0 12px 35px rgba(0,0,0,0.5);
}

h3 { color:#FFD700; text-align:center; margin-bottom:20px; text-shadow: 0 0 8px #000; }

.input-group-text {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #FFD700;
}

input.form-control, select.form-select {
  border-radius: 10px;
  border: 2px solid #FFD700;
}
input.form-control:focus, select.form-select:focus {
  border-color: #FFD700;
  box-shadow: 0 0 10px #FFD700;
  outline: none;
}

.btn-login {
  background: linear-gradient(90deg, #FFD700, #FFEA00);
  color: #000;
  font-weight: bold;
  border-radius: 12px;
  width: 100%;
  padding: 10px;
  transition: all 0.3s;
}
.btn-login:hover {
  box-shadow: 0 0 15px #FFD700, 0 0 25px #FFEA00;
  transform: scale(1.05);
}
</style>
</head>
<body>

<div class="card-login">
  <h3>Login</h3>

  <?php if (!empty($error)) { ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php } ?>

  <form method="POST">
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

    <div class="mb-3">
      <label>Login As</label>
      <select name="role" class="form-select" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>
    </div>

    <button type="submit" class="btn btn-login">Login</button>
  </form>

  <p class="mt-3 text-center">
    Don’t have an account? <a href="signup.php" style="color:#FFD700;">Sign up</a>
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
