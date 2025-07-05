<?php
include('db.php');
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $contact  = $_POST['contact'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $repass   = $_POST['repassword'];
    $role     = $_POST['role'];

    // Check if username already exists
    $checkSql = "SELECT id FROM users WHERE username = ?";
    $checkParams = array($username);
    $checkStmt = sqlsrv_query($conn, $checkSql, $checkParams);

    if ($checkStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
        $msg = '<div class="alert alert-danger">Username already exists.</div>';
    } elseif ($password !== $repass) {
        $msg = '<div class="alert alert-danger">Passwords do not match.</div>';
    } elseif (strlen($password) < 8 || 
              !preg_match("/[A-Z]/", $password) || 
              !preg_match("/[0-9]/", $password)) {
        $msg = '<div class="alert alert-danger">
                Password must be at least 8 characters long, include an uppercase letter and a number.
                </div>';
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $insertSql = "INSERT INTO users (name, contact, username, password, role) VALUES (?, ?, ?, ?, ?)";
        $insertParams = array($name, $contact, $username, $hashedPassword, $role);
        $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);

        if ($insertStmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $msg = '<div class="alert alert-success">
                Registration successful. <a href="login.php">Login here</a>
                </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- ✅ Bootstrap Icons CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container position-relative vh-100 d-flex justify-content-center align-items-center">

  <!-- ✅ Top left back arrow -->
  <a href="index.php" class="position-absolute top-0 start-0 m-4 btn btn-outline-primary">
    <i class="bi bi-arrow-left"></i> Back
  </a>

  <div class="card p-4 shadow" style="width: 28rem;">
    <h3 class="card-title mb-3 text-center">Create Account</h3>
    <?= $msg ?>
    <form method="POST">
      <div class="mb-3"><label class="form-label">Full Name</label><input name="name" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Contact</label><input name="contact" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="repassword" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          <option value="" disabled selected>-- Choose Role --</option>
          <option value="organizer">Event Organizer</option>
          <option value="attendee">Attendee</option>
        </select>
      </div>
      <button class="btn btn-success w-100">Sign up</button>
    </form>
    <p class="mt-3 text-center">Already have an account? <a href="login.php">Sign in</a></p>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
