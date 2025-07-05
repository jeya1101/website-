<?php
session_start();
include('db.php');

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Prepare and execute parameterized query
    $sql = "SELECT * FROM users WHERE username = ? AND password = ? AND role = ?";
    $params = array($username, $password, $role);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch user
    if ($user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $msg = '<div class="alert alert-danger">Invalid login credentials.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center vh-100">
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
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
