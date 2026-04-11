<?php
// quiz_results.php — Student's complete quiz history across all courses
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch all quiz results for this user, joined with course info, newest first
$stmt = mysqli_prepare($conn,
    "SELECT qr.id, qr.score, qr.total, qr.taken_at,
            c.id AS course_id, c.title AS course_title
     FROM quiz_results qr
     JOIN courses c ON qr.course_id = c.id
     WHERE qr.user_id = ?
     ORDER BY qr.taken_at DESC"
);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$all_results = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// Compute summary stats
$total_attempts = count($all_results);
$courses_taken  = count(array_unique(array_column($all_results, 'course_id')));
$avg_pct        = 0;
$best_pct       = 0;

if ($total_attempts > 0) {
    $pcts    = array_map(fn($r) => $r['total'] > 0 ? round($r['score'] / $r['total'] * 100) : 0, $all_results);
    $avg_pct = round(array_sum($pcts) / count($pcts));
    $best_pct = max($pcts);
}

// Group by course for the breakdown section
$by_course = [];
foreach ($all_results as $r) {
    $cid = $r['course_id'];
    if (!isset($by_course[$cid])) {
        // Get first lesson id for this course
        $fl_q = mysqli_query($conn,
            "SELECT id FROM lessons WHERE course_id = $cid ORDER BY position ASC, id ASC LIMIT 1"
        );
        $fl = mysqli_fetch_assoc($fl_q);

        $by_course[$cid] = [
            'title'          => $r['course_title'],
            'attempts'       => 0,
            'best_pct'       => 0,
            'latest'         => null,
            'first_lesson_id'=> $fl ? $fl['id'] : null,
        ];
    }
    $pct = $r['total'] > 0 ? round($r['score'] / $r['total'] * 100) : 0;
    $by_course[$cid]['attempts']++;
    if ($pct > $by_course[$cid]['best_pct']) $by_course[$cid]['best_pct'] = $pct;
    if ($by_course[$cid]['latest'] === null) $by_course[$cid]['latest'] = $r; // already DESC order
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Results — StudyHub</title>
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
    --err: #f87171;
    --text: #f0f4ff;
    --muted: #6b7280;
  }
  body {
    background: var(--bg); color: var(--text);
    font-family: 'DM Sans', sans-serif; min-height: 100vh;
    background-image: radial-gradient(ellipse at 85% 10%, rgba(34,211,238,0.05) 0%, transparent 50%);
  }

  /* NAV */
  nav {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 2rem; border-bottom: 1px solid var(--border);
    background: rgba(13,15,20,0.9); backdrop-filter: blur(10px);
    position: sticky; top: 0; z-index: 100;
  }
  .logo {
    font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    text-decoration: none;
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

  main { max-width: 1050px; margin: 0 auto; padding: 2.5rem 1.5rem; }

  /* PAGE HEADER */
  .page-header { margin-bottom: 2rem; }
  .page-header h1 { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; margin-bottom: 0.4rem; }
  .page-header p { color: var(--muted); }

  /* STAT CARDS */
  .stats-row {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
    gap: 1rem; margin-bottom: 2.5rem;
  }
  .stat-card {
    background: var(--card); border: 1px solid var(--border); border-radius: 14px;
    padding: 1.25rem 1.5rem;
  }
  .stat-label { font-size: 0.75rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 0.5rem; }
  .stat-value { font-family: 'Syne', sans-serif; font-size: 2.2rem; font-weight: 800; line-height: 1; margin-bottom: 0.25rem; }
  .stat-value.green  { color: var(--accent); }
  .stat-value.cyan   { color: var(--accent2); }
  .stat-value.yellow { color: var(--warn); }
  .stat-sub { font-size: 0.75rem; color: var(--muted); }

  /* SECTION TITLE */
  .section-title {
    font-family: 'Syne', sans-serif; font-size: 1.05rem; font-weight: 800;
    margin-bottom: 1rem; color: var(--text);
  }

  .pill-cyan  { background: rgba(34,211,238,0.09); color: var(--accent2); border: 1px solid rgba(34,211,238,0.2); }
  .pill-green { background: rgba(74,222,128,0.09); color: var(--accent); border: 1px solid rgba(74,222,128,0.2); }
  .pill-red   { background: rgba(248,113,113,0.09); color: var(--err); border: 1px solid rgba(248,113,113,0.2); }

  /* HISTORY TABLE */
  .table-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden; margin-bottom: 2rem;
  }
  .table-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.5rem; border-bottom: 1px solid var(--border);
  }
  .table-header h2 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 800; }
  .table-header span { font-size: 0.8rem; color: var(--muted); }

  table { width: 100%; border-collapse: collapse; }
  th {
    text-align: left; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.07em; color: var(--muted); padding: 0.65rem 1.25rem;
    border-bottom: 1px solid var(--border); background: rgba(255,255,255,0.01);
  }
  td {
    padding: 0.9rem 1.25rem; border-bottom: 1px solid rgba(37,42,53,0.5);
    font-size: 0.875rem; vertical-align: middle;
  }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: rgba(255,255,255,0.015); }

  .score-big { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1rem; }
  .score-big.high { color: var(--accent); }
  .score-big.low  { color: var(--err); }

  .inline-bar { display: flex; align-items: center; gap: 0.6rem; }
  .inline-bar-track { flex: 1; min-width: 80px; height: 5px; background: var(--border); border-radius: 99px; overflow: hidden; }
  .inline-bar-fill { height: 100%; border-radius: 99px; }
  .inline-bar-pct { font-size: 0.78rem; font-weight: 700; min-width: 36px; text-align: right; }

  .date-main { font-weight: 500; }
  .date-time { font-size: 0.75rem; color: var(--muted); margin-top: 0.15rem; }

  .action-link {
    color: var(--accent2); text-decoration: none; font-size: 0.8rem; font-weight: 600;
    padding: 0.25rem 0.65rem; border-radius: 6px; border: 1px solid rgba(34,211,238,0.2);
    transition: background 0.15s; white-space: nowrap;
  }
  .action-link:hover { background: rgba(34,211,238,0.08); }

  /* EMPTY STATE */
  .empty-state { text-align: center; padding: 5rem 2rem; }
  .empty-icon { font-size: 3.5rem; margin-bottom: 1rem; }
  .empty-state h3 { font-family: 'Syne', sans-serif; font-size: 1.3rem; margin-bottom: 0.5rem; }
  .empty-state p { color: var(--muted); margin-bottom: 1.75rem; font-size: 0.9rem; }
  .btn-go {
    display: inline-block; padding: 0.75rem 1.75rem; border-radius: 12px; font-weight: 700;
    text-decoration: none; color: #0d0f14;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    font-family: 'DM Sans', sans-serif; font-size: 0.95rem;
  }

  @media (max-width: 640px) {
    th, td { padding: 0.65rem 1rem; }
    .stats-row { grid-template-columns: repeat(2, 1fr); }
  }
</style>
</head>
<body>

<nav>
  <a href="dashboard.php" class="logo">StudyHub</a>
  <div class="nav-links">
    <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
    <a href="courses.php" class="nav-link">📚 Courses</a>
    <a href="quiz_results.php" class="nav-link active">📋 My Results</a>
    <a href="logout.php" class="logout-btn">Sign out</a>
  </div>
</nav>

<main>

  <div class="page-header">
    <h1>📋 My Quiz Results</h1>
    <p>Your complete quiz history — track your progress across all courses.</p>
  </div>

  <?php if (empty($all_results)): ?>

    <div class="empty-state">
      <div class="empty-icon">🎯</div>
      <h3>No quiz attempts yet</h3>
      <p>Go to a course and take your first quiz to see your results here.</p>
      <a href="dashboard.php" class="btn-go">Browse Courses →</a>
    </div>

  <?php else: ?>

    <!-- ── SUMMARY STATS ─────────────────────────────── -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-label">Total Attempts</div>
        <div class="stat-value cyan"><?= $total_attempts ?></div>
        <div class="stat-sub">quiz submissions</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Courses Taken</div>
        <div class="stat-value green"><?= $courses_taken ?></div>
        <div class="stat-sub">unique courses</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Average Score</div>
        <div class="stat-value <?= $avg_pct >= 50 ? 'green' : '' ?>"><?= $avg_pct ?>%</div>
        <div class="stat-sub">across all attempts</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Personal Best</div>
        <div class="stat-value green"><?= $best_pct ?>%</div>
        <div class="stat-sub">highest score</div>
      </div>
    </div>

    <!-- ── FULL HISTORY TABLE ─────────────────────────── -->
    <div class="section-title">Full Attempt History</div>
    <div class="table-card">
      <div class="table-header">
        <h2>All Attempts</h2>
        <span><?= $total_attempts ?> record<?= $total_attempts != 1 ? 's' : '' ?></span>
      </div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Course</th>
            <th>Score</th>
            <th>Progress</th>
            <th>Result</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($all_results as $i => $r):
            $pct    = $r['total'] > 0 ? round($r['score'] / $r['total'] * 100) : 0;
            $passed = $pct >= 50;
            $color  = $pct >= 50 ? 'high' : 'low';
            $bar_color = $pct >= 50 ? 'var(--accent)' : 'var(--err)';
          ?>
          <tr>
            <td style="color:var(--muted); width:40px;"><?= $i + 1 ?></td>
            <td style="font-weight:600;"><?= htmlspecialchars($r['course_title']) ?></td>
            <td>
              <span class="score-big <?= $color ?>"><?= $r['score'] ?>/<?= $r['total'] ?></span>
            </td>
            <td style="min-width:130px;">
              <div class="inline-bar">
                <div class="inline-bar-track">
                  <div class="inline-bar-fill" style="width:<?= $pct ?>%; background:<?= $bar_color ?>;"></div>
                </div>
                <span class="inline-bar-pct" style="color:<?= $bar_color ?>;"><?= $pct ?>%</span>
              </div>
            </td>
            <td>
              <?php if ($passed): ?>
                <span class="pill pill-green">✅ Passed</span>
              <?php else: ?>
                <span class="pill pill-red">❌ Failed</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="date-main"><?= date('M d, Y', strtotime($r['taken_at'])) ?></div>
              <div class="date-time"><?= date('h:i A', strtotime($r['taken_at'])) ?></div>
            </td>
            <td>
              <a href="quiz.php?id=<?= $r['course_id'] ?>" class="action-link">🚀 Start Quiz</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <?php endif; ?>
</main>

</body>
</html>