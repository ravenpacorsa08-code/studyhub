<?php
// login.php — User login page
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    // Supports both MD5 (sample data) and plain for dev ease
    if ($user && ($user['password'] === md5($password) || $user['password'] === $password)) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudyHub — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0d0f14;
    --card: #161921;
    --border: #252a35;
    --accent: #4ade80;
    --accent2: #22d3ee;
    --text: #f0f4ff;
    --muted: #6b7280;
  }
  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background-image: radial-gradient(ellipse at 20% 50%, rgba(74,222,128,0.06) 0%, transparent 60%),
                      radial-gradient(ellipse at 80% 20%, rgba(34,211,238,0.06) 0%, transparent 60%);
  }
  .card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
  }
  .logo {
    font-family: 'Syne', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.25rem;
  }
  .subtitle { color: var(--muted); font-size: 0.9rem; margin-bottom: 2rem; }
  label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--muted); margin-bottom: 0.4rem; }
  input {
    width: 100%;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem;
    margin-bottom: 1.2rem;
    transition: border-color 0.2s;
    outline: none;
  }
  input:focus { border-color: var(--accent); }
  .btn {
    width: 100%;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    border: none;
    border-radius: 10px;
    padding: 0.85rem;
    color: #0d0f14;
    font-family: 'Syne', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
  }
  .btn:hover { opacity: 0.9; transform: translateY(-1px); }
  .error {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.3);
    color: #f87171;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 1.2rem;
    font-size: 0.875rem;
  }
  .demo-info {
    margin-top: 1.5rem;
    padding: 0.9rem;
    background: rgba(74,222,128,0.05);
    border: 1px solid rgba(74,222,128,0.15);
    border-radius: 10px;
    font-size: 0.8rem;
    color: var(--muted);
    text-align: center;
  }
  .demo-info strong { color: var(--accent); }
</style>
</head>
<body>
<div class="card">
  <div class="logo">StudyHub</div>
  <p class="subtitle">Your personal learning space</p>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">

    <button type="submit" class="btn">Sign In →</button>
  </form>

  <div class="demo-info">
    Demo account: <strong>juan@studyhub.com</strong> / <strong>password123</strong>
  </div>

  <div style="margin-top:1.25rem; text-align:center; font-size:0.875rem; color:var(--muted);">
    Don't have an account?
    <a href="register.php" style="color:var(--accent2); font-weight:600; text-decoration:none;">Create one</a>
  </div>
</div>
</body>
</html>