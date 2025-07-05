<?php
session_start();
include('db.php');

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Fetch user by username & role
    $sql = "SELECT * FROM users WHERE username = ? AND role = ?";
    $params = array($username, $role);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if ($user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $msg = '<div class="alert alert-danger">Invalid password.</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger">Invalid username or role.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- ✅ Bootstrap Icons CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex flex-column justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow" style="width: 22rem;">
    <h3 class="card-title mb-3 text-center">Sign In</h3>
    <?= $msg ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          <option value="" disabled selected>-- Choose Role --</option>
          <option value="organizer">Event Organizer</option>
          <option value="attendee">Attendee</option>
        </select>
      </div>
      <button class="btn btn-primary w-100">Login</button>
    </form>
    <p class="mt-3 text-center">No account? <a href="register_user.php">Sign up</a></p>
  </div>

  <!-- ✅ Back to index with arrow -->
  <div class="mt-4 text-center">
    <a href="index.php" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left"></i> Back to Home
    </a>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
