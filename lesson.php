<?php
// lesson.php — View a lesson with BACK and NEXT navigation
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$lesson_id) {
    header('Location: dashboard.php');
    exit;
}

// Fetch current lesson
$stmt = mysqli_prepare($conn, "SELECT * FROM lessons WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $lesson_id);
mysqli_stmt_execute($stmt);
$lesson = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$lesson) {
    header('Location: dashboard.php');
    exit;
}

$course_id = (int)$lesson['course_id'];
$position  = (int)$lesson['position'];

// Fetch course info
$cstmt = mysqli_prepare($conn, "SELECT * FROM courses WHERE id = ?");
mysqli_stmt_bind_param($cstmt, 'i', $course_id);
mysqli_stmt_execute($cstmt);
$course = mysqli_fetch_assoc(mysqli_stmt_get_result($cstmt));

// PREVIOUS lesson: highest position that is less than current, within same course
$prev_stmt = mysqli_prepare($conn,
    "SELECT id, title FROM lessons
     WHERE course_id = ? AND position < ?
     ORDER BY position DESC LIMIT 1"
);
mysqli_stmt_bind_param($prev_stmt, 'ii', $course_id, $position);
mysqli_stmt_execute($prev_stmt);
$prev_lesson = mysqli_fetch_assoc(mysqli_stmt_get_result($prev_stmt));

// Fallback: if no position-based prev, try by id
if (!$prev_lesson) {
    $prev_stmt2 = mysqli_prepare($conn,
        "SELECT id, title FROM lessons
         WHERE course_id = ? AND id < ?
         ORDER BY id DESC LIMIT 1"
    );
    mysqli_stmt_bind_param($prev_stmt2, 'ii', $course_id, $lesson_id);
    mysqli_stmt_execute($prev_stmt2);
    $prev_lesson = mysqli_fetch_assoc(mysqli_stmt_get_result($prev_stmt2));
}

// NEXT lesson: lowest position greater than current
$next_stmt = mysqli_prepare($conn,
    "SELECT id, title FROM lessons
     WHERE course_id = ? AND position > ?
     ORDER BY position ASC LIMIT 1"
);
mysqli_stmt_bind_param($next_stmt, 'ii', $course_id, $position);
mysqli_stmt_execute($next_stmt);
$next_lesson = mysqli_fetch_assoc(mysqli_stmt_get_result($next_stmt));

if (!$next_lesson) {
    $next_stmt2 = mysqli_prepare($conn,
        "SELECT id, title FROM lessons
         WHERE course_id = ? AND id > ?
         ORDER BY id ASC LIMIT 1"
    );
    mysqli_stmt_bind_param($next_stmt2, 'ii', $course_id, $lesson_id);
    mysqli_stmt_execute($next_stmt2);
    $next_lesson = mysqli_fetch_assoc(mysqli_stmt_get_result($next_stmt2));
}

// Count lesson index
$idx_q = mysqli_query($conn,
    "SELECT COUNT(*) as cnt FROM lessons WHERE course_id = $course_id AND position <= $position"
);
$idx_row = mysqli_fetch_assoc($idx_q);
$lesson_index = $idx_row['cnt'];

$total_q = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM lessons WHERE course_id = $course_id");
$total_row = mysqli_fetch_assoc($total_q);
$total_lessons = $total_row['cnt'];

// Quiz status for this course
$user_id = $_SESSION['user_id'];
$qstmt = mysqli_prepare($conn,
    "SELECT id FROM quiz_results WHERE user_id = ? AND course_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($qstmt, 'ii', $user_id, $course_id);
mysqli_stmt_execute($qstmt);
$quiz_taken = mysqli_num_rows(mysqli_stmt_get_result($qstmt)) > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($lesson['title']) ?> — StudyHub</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0d0f14;
    --card: #161921;
    --card2: #1c2030;
    --border: #252a35;
    --accent: #4ade80;
    --accent2: #22d3ee;
    --warn: #f59e0b;
    --text: #f0f4ff;
    --muted: #6b7280;
  }
  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    background-image: radial-gradient(ellipse at 10% 0%, rgba(74,222,128,0.04) 0%, transparent 50%);
  }
  nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 2rem;
    border-bottom: 1px solid var(--border);
    background: rgba(13,15,20,0.9);
    backdrop-filter: blur(10px);
    position: sticky; top: 0; z-index: 100;
  }
  .logo {
    font-family: 'Syne', sans-serif;
    font-size: 1.4rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-decoration: none;
  }
  .back-link {
    font-size: 0.85rem;
    color: var(--muted);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transition: color 0.2s;
  }
  .back-link:hover { color: var(--text); }

  main { max-width: 860px; margin: 0 auto; padding: 2.5rem 1.5rem; }

  /* BREADCRUMB */
  .breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--muted);
    margin-bottom: 1.5rem;
  }
  .breadcrumb a { color: var(--accent2); text-decoration: none; }
  .breadcrumb a:hover { text-decoration: underline; }

  /* PROGRESS */
  .progress-bar-wrap {
    background: var(--border);
    border-radius: 99px;
    height: 5px;
    margin-bottom: 1.5rem;
    overflow: hidden;
  }
  .progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    border-radius: 99px;
    transition: width 0.4s ease;
  }
  .progress-label { font-size: 0.78rem; color: var(--muted); margin-bottom: 0.5rem; }

  /* LESSON HEADER */
  .lesson-header { margin-bottom: 2rem; }
  .lesson-num {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--accent2);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.5rem;
  }
  .lesson-title {
    font-family: 'Syne', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.2;
  }

  /* LESSON CONTENT */
  .lesson-body {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 2rem;
    line-height: 1.8;
    font-size: 1rem;
    color: #cbd5e1;
    margin-bottom: 2.5rem;
  }
  .lesson-body p { margin-bottom: 1rem; }
  .lesson-body pre {
    background: #0a0c10;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 1.2rem;
    overflow-x: auto;
    margin: 1rem 0;
    font-size: 0.875rem;
    line-height: 1.6;
    color: var(--accent);
  }
  .lesson-body code {
    background: rgba(74,222,128,0.08);
    border: 1px solid rgba(74,222,128,0.15);
    border-radius: 4px;
    padding: 0.1em 0.4em;
    font-size: 0.875em;
    color: var(--accent);
  }
  .lesson-body pre code {
    background: none;
    border: none;
    padding: 0;
    color: var(--accent);
  }

  /* NAVIGATION BUTTONS */
  .nav-buttons {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .nav-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid var(--border);
    transition: border-color 0.2s, background 0.2s;
    cursor: pointer;
  }
  .nav-btn-back {
    background: var(--card);
    color: var(--text);
  }
  .nav-btn-back:hover { border-color: var(--muted); }
  .nav-btn-next {
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    color: #0d0f14;
    border-color: transparent;
    font-weight: 700;
  }
  .nav-btn-next:hover { opacity: 0.88; }
  .nav-btn-disabled {
    color: var(--muted);
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
    background: var(--card);
  }

  /* QUIZ CTA */
  .quiz-cta {
    margin-top: 2rem;
    background: linear-gradient(135deg, rgba(74,222,128,0.08), rgba(34,211,238,0.08));
    border: 1px solid rgba(74,222,128,0.2);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
  }
  .quiz-cta-text h3 { font-family: 'Syne', sans-serif; font-size: 1.1rem; margin-bottom: 0.25rem; }
  .quiz-cta-text p { font-size: 0.85rem; color: var(--muted); }
  .btn-quiz {
    display: inline-block;
    padding: 0.7rem 1.5rem;
    border-radius: 10px;
    font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    text-decoration: none;
    color: #0d0f14;
    white-space: nowrap;
  }
  .btn-quiz-start { background: linear-gradient(135deg, var(--accent), var(--accent2)); }
  .btn-quiz-retake { background: linear-gradient(135deg, var(--warn), #f97316); }
</style>
</head>
<body>

<nav>
  <a href="dashboard.php" class="logo">StudyHub</a>
  <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</nav>

<main>
  <!-- Breadcrumb -->
  <div class="breadcrumb">
    <a href="dashboard.php">Dashboard</a>
    <span>›</span>
    <span><?= htmlspecialchars($course['title']) ?></span>
    <span>›</span>
    <span><?= htmlspecialchars($lesson['title']) ?></span>
  </div>

  <!-- Progress -->
  <div class="progress-label">Lesson <?= $lesson_index ?> of <?= $total_lessons ?></div>
  <div class="progress-bar-wrap">
    <div class="progress-bar-fill" style="width: <?= round($lesson_index / max($total_lessons, 1) * 100) ?>%"></div>
  </div>

  <!-- Lesson Header -->
  <div class="lesson-header">
    <div class="lesson-num">Lesson <?= $lesson_index ?></div>
    <h1 class="lesson-title"><?= htmlspecialchars($lesson['title']) ?></h1>
  </div>

  <!-- Lesson Content -->
  <div class="lesson-body">
    <?= $lesson['content'] /* Already HTML in DB */ ?>
  </div>

  <!-- BACK / NEXT Navigation -->
  <div class="nav-buttons">
    <?php if ($prev_lesson): ?>
      <a href="lesson.php?id=<?= $prev_lesson['id'] ?>" class="nav-btn nav-btn-back">
        ← <?= htmlspecialchars($prev_lesson['title']) ?>
      </a>
    <?php else: ?>
      <span class="nav-btn nav-btn-back nav-btn-disabled">← No previous lesson</span>
    <?php endif; ?>

    <?php if ($next_lesson): ?>
      <a href="lesson.php?id=<?= $next_lesson['id'] ?>" class="nav-btn nav-btn-next">
        <?= htmlspecialchars($next_lesson['title']) ?> →
      </a>
    <?php else: ?>
      <span class="nav-btn nav-btn-next nav-btn-disabled" style="background:#2a2f3d; color:var(--muted);">
        Last lesson ✓
      </span>
    <?php endif; ?>
  </div>

  <!-- Quiz CTA at bottom of last lesson -->
  <div class="quiz-cta">
    <div class="quiz-cta-text">
      <h3>Ready to test your knowledge?</h3>
      <p>Take the quiz for <strong><?= htmlspecialchars($course['title']) ?></strong></p>
    </div>
    <?php if (!$quiz_taken): ?>
      <a href="quiz.php?id=<?= $course_id ?>" class="btn-quiz btn-quiz-start">🚀 Start Quiz</a>
    <?php else: ?>
      <a href="quiz.php?id=<?= $course_id ?>" class="btn-quiz btn-quiz-retake">🔄 Retake Quiz</a>
    <?php endif; ?>
  </div>
</main>

</body>
</html>