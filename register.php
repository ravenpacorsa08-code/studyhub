<?php
// register.php — New user registration
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'An account with that email already exists.';
        } else {
            // Insert new user (MD5 to match login.php hashing)
            $hashed = md5($password);
            $insert = mysqli_prepare($conn,
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($insert, 'sss', $name, $email, $hashed);

            if (mysqli_stmt_execute($insert)) {
                $success = 'Account created! You can now sign in.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudyHub — Create Account</title>
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
    background-image: radial-gradient(ellipse at 80% 20%, rgba(74,222,128,0.06) 0%, transparent 60%),
                      radial-gradient(ellipse at 20% 80%, rgba(34,211,238,0.06) 0%, transparent 60%);
  }
  .card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 440px;
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
  .alert {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 1.2rem;
    font-size: 0.875rem;
  }
  .alert-error {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.3);
    color: #f87171;
  }
  .alert-success {
    background: rgba(74,222,128,0.1);
    border: 1px solid rgba(74,222,128,0.3);
    color: var(--accent);
  }
  .login-link {
    margin-top: 1.5rem;
    text-align: center;
    font-size: 0.875rem;
    color: var(--muted);
  }
  .login-link a {
    color: var(--accent2);
    text-decoration: none;
    font-weight: 600;
  }
  .login-link a:hover { text-decoration: underline; }
  .divider {
    border: none;
    border-top: 1px solid var(--border);
    margin: 1.5rem 0;
  }
</style>
</head>
<body>
<div class="card">
  <div class="logo">StudyHub</div>
  <p class="subtitle">Create your free account</p>

  <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <a href="login.php" class="btn" style="display:block; text-align:center; text-decoration:none;">Sign In →</a>
  <?php else: ?>
    <form method="POST">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name"
             placeholder="e.g. Juan dela Cruz"
             value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
             required autocomplete="name">

      <label for="email">Email Address</label>
      <input type="email" id="email" name="email"
             placeholder="you@example.com"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
             required autocomplete="email">

      <label for="password">Password</label>
      <input type="password" id="password" name="password"
             placeholder="At least 6 characters"
             required autocomplete="new-password">

      <label for="confirm">Confirm Password</label>
      <input type="password" id="confirm" name="confirm"
             placeholder="Repeat your password"
             required autocomplete="new-password">

      <button type="submit" class="btn">Create Account →</button>
    </form>
  <?php endif; ?>

  <hr class="divider">
  <div class="login-link">
    Already have an account? <a href="login.php">Sign in</a>
  </div>
</div>
</body>
</html>