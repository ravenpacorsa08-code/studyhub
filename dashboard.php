<?php
// dashboard.php — Student dashboard showing all courses + quiz status
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch all courses
$courses_result = mysqli_query($conn, "SELECT * FROM courses ORDER BY id ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

// For each course, check quiz status + latest score
foreach ($courses as &$course) {
    $cid  = (int)$course['id'];
    $stmt = mysqli_prepare($conn,
        "SELECT score, total, taken_at FROM quiz_results
         WHERE user_id = ? AND course_id = ?
         ORDER BY taken_at DESC LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $cid);
    mysqli_stmt_execute($stmt);
    $res    = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_assoc($res);

    $course['quiz_taken']   = $result ? true : false;
    $course['latest_score'] = $result ? $result['score'] : null;
    $course['latest_total'] = $result ? $result['total'] : null;

    // Count lessons
    $lcount = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM lessons WHERE course_id = $cid");
    $lrow   = mysqli_fetch_assoc($lcount);
    $course['lesson_count'] = $lrow['cnt'];

    // Get first lesson id for the Lessons button
    $fl_q = mysqli_query($conn, "SELECT id FROM lessons WHERE course_id = $cid ORDER BY position ASC, id ASC LIMIT 1");
    $fl   = mysqli_fetch_assoc($fl_q);
    $course['first_lesson_id'] = $fl ? $fl['id'] : null;

    // Count questions available
    $qcount = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM questions WHERE course_id = $cid");
    $qrow   = mysqli_fetch_assoc($qcount);
    $course['question_count'] = $qrow['cnt'];
}
unset($course);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudyHub — Dashboard</title>
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
    --text: #f0f4ff;
    --muted: #6b7280;
  }
  body {
    background: var(--bg); color: var(--text);
    font-family: 'DM Sans', sans-serif; min-height: 100vh;
    background-image: radial-gradient(ellipse at 10% 0%, rgba(74,222,128,0.05) 0%, transparent 50%),
                      radial-gradient(ellipse at 90% 100%, rgba(34,211,238,0.05) 0%, transparent 50%);
  }

  /* NAV */
  nav {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 2rem; border-bottom: 1px solid var(--border);
    background: rgba(13,15,20,0.9); backdrop-filter: blur(10px);
    position: sticky; top: 0; z-index: 100;
  }
  .logo {
    font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 800;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  }
  .nav-links { display: flex; align-items: center; gap: 0.4rem; }
  .nav-link {
    font-size: 0.82rem; font-weight: 600; padding: 0.4rem 0.9rem; border-radius: 8px;
    text-decoration: none; color: var(--muted); border: 1px solid transparent;
    transition: color 0.2s, border-color 0.2s;
  }
  .nav-link:hover { color: var(--text); border-color: var(--border); }
  .nav-link.active { color: var(--accent); border-color: rgba(74,222,128,0.3); }
  .logout-btn {
    font-size: 0.8rem; color: var(--muted); text-decoration: none;
    padding: 0.4rem 0.9rem; border: 1px solid var(--border); border-radius: 8px;
    transition: color 0.2s, border-color 0.2s; margin-left: 0.4rem;
  }
  .logout-btn:hover { color: var(--accent); border-color: var(--accent); }

  /* MAIN */
  main { max-width: 1100px; margin: 0 auto; padding: 2.5rem 1.5rem; }
  .page-header { margin-bottom: 2.5rem; }
  .page-header h1 { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; margin-bottom: 0.4rem; }
  .page-header p { color: var(--muted); }
  .page-header p strong { color: var(--text); }

  /* GRID */
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
  .course-card {
    background: var(--card); border: 1px solid var(--border); border-radius: 16px;
    padding: 1.75rem; display: flex; flex-direction: column; gap: 1rem;
    transition: border-color 0.25s, transform 0.25s;
  }
  .course-card:hover { border-color: rgba(74,222,128,0.35); transform: translateY(-3px); }

  .course-tag {
    display: inline-flex; align-items: center; gap: 0.4rem;
    font-size: 0.75rem; font-weight: 600; color: var(--accent2);
    background: rgba(34,211,238,0.08); border: 1px solid rgba(34,211,238,0.15);
    border-radius: 20px; padding: 0.25rem 0.75rem; width: fit-content;
  }
  .course-title { font-family: 'Syne', sans-serif; font-size: 1.2rem; font-weight: 700; line-height: 1.3; }
  .course-desc { font-size: 0.875rem; color: var(--muted); line-height: 1.6; flex: 1; }
  .course-meta { display: flex; gap: 1.25rem; font-size: 0.8rem; color: var(--muted); }

  /* CARD ACTIONS */
  .card-actions { display: flex; gap: 0.75rem; margin-top: auto; }
  .btn-lesson {
    flex: 1; display: block; text-align: center; background: var(--card2);
    border: 1px solid var(--border); color: var(--text); padding: 0.65rem 1rem;
    border-radius: 10px; font-size: 0.875rem; font-weight: 600; text-decoration: none;
    transition: border-color 0.2s, color 0.2s;
  }
  .btn-lesson:hover { border-color: var(--accent2); color: var(--accent2); }

  /* ALWAYS show "Start Quiz" in green — score badge shows previous attempt result */
  .btn-quiz {
    flex: 1; display: block; text-align: center;
    padding: 0.65rem 1rem; border-radius: 10px;
    font-size: 0.875rem; font-weight: 700; text-decoration: none;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    color: #0d0f14; border: none; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: opacity 0.2s, transform 0.15s;
  }
  .btn-quiz:hover { opacity: 0.85; transform: translateY(-1px); }

  .no-content { color: var(--muted); font-size: 0.82rem; font-style: italic; }
</style>
</head>
<body>

<nav>
  <div class="logo">StudyHub</div>
  <div class="nav-links">
    <a href="dashboard.php" class="nav-link active">📊 Dashboard</a>
    <a href="courses.php" class="nav-link">📚 Courses</a>
    <a href="quiz_results.php" class="nav-link">📋 My Results</a>
    <a href="logout.php" class="logout-btn">Sign out</a>
  </div>
</nav>

<main>
  <div class="page-header">
    <h1>My Courses</h1>
    <p>Welcome back, <strong><?= htmlspecialchars($user_name) ?></strong>! Pick up where you left off.</p>
  </div>

  <div class="grid">
    <?php foreach ($courses as $course): ?>
    <div class="course-card">
      <div class="course-tag">📚 Course</div>
      <div class="course-title"><?= htmlspecialchars($course['title']) ?></div>
      <div class="course-desc"><?= htmlspecialchars($course['description']) ?></div>

      <div class="course-meta">
        <span>📖 <?= $course['lesson_count'] ?> lesson<?= $course['lesson_count'] != 1 ? 's' : '' ?></span>
        <span>❓ <?= $course['question_count'] ?> question<?= $course['question_count'] != 1 ? 's' : '' ?></span>
      </div>



      <div class="card-actions">
        <?php if ($course['first_lesson_id']): ?>
          <a href="lesson.php?id=<?= $course['first_lesson_id'] ?>" class="btn-lesson">📖 Lessons</a>
        <?php else: ?>
          <span class="no-content">No lessons yet</span>
        <?php endif; ?>

        <?php if ($course['question_count'] > 0): ?>
          <!-- Always "Start Quiz" — score badge already communicates previous attempts -->
          <a href="quiz.php?id=<?= $course['id'] ?>" class="btn-quiz">🚀 Start Quiz</a>
        <?php else: ?>
          <span class="no-content">No quiz yet</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</main>

</body>
</html>