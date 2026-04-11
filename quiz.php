<?php
// quiz.php — Take or retake a quiz with RANDOM questions each attempt
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id   = $_SESSION['user_id'];
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$course_id) {
    header('Location: dashboard.php');
    exit;
}

// Validate course exists
$cstmt = mysqli_prepare($conn, "SELECT * FROM courses WHERE id = ?");
mysqli_stmt_bind_param($cstmt, 'i', $course_id);
mysqli_stmt_execute($cstmt);
$course = mysqli_fetch_assoc(mysqli_stmt_get_result($cstmt));
if (!$course) {
    header('Location: dashboard.php');
    exit;
}

$score       = null;
$total       = null;
$results     = [];
$submitted   = false;

// ─── HANDLE SUBMISSION ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $submitted = true;
    $score     = 0;

    // Retrieve question IDs that were shown
    $question_ids = isset($_POST['question_ids']) ? explode(',', $_POST['question_ids']) : [];
    $total = count($question_ids);

    foreach ($question_ids as $qid) {
        $qid = (int)$qid;
        $qr  = mysqli_query($conn, "SELECT * FROM questions WHERE id = $qid");
        $q   = mysqli_fetch_assoc($qr);
        if (!$q) continue;

        $user_answer    = isset($_POST["answer_$qid"]) ? strtolower(trim($_POST["answer_$qid"])) : '';
        $correct        = strtolower($q['correct_answer']);
        $is_correct     = ($user_answer === $correct);
        if ($is_correct) $score++;

        $results[] = [
            'question'    => $q['question_text'],
            'options'     => [
                'a' => $q['option_a'],
                'b' => $q['option_b'],
                'c' => $q['option_c'],
                'd' => $q['option_d'],
            ],
            'user_answer'    => $user_answer,
            'correct_answer' => $correct,
            'is_correct'     => $is_correct,
        ];
    }

    // Save result to DB (always insert — keeps history)
    $save = mysqli_prepare($conn,
        "INSERT INTO quiz_results (user_id, course_id, score, total) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($save, 'iiii', $user_id, $course_id, $score, $total);
    mysqli_stmt_execute($save);
}

// ─── LOAD RANDOM QUESTIONS (quiz form mode) ─────────────────────────
if (!$submitted) {
    // ORDER BY RAND() ensures different questions every attempt; LIMIT 10
    $qresult   = mysqli_query($conn,
        "SELECT * FROM questions WHERE course_id = $course_id ORDER BY RAND() LIMIT 10"
    );
    $questions = mysqli_fetch_all($qresult, MYSQLI_ASSOC);
}

// ─── LETTER LABELS ───────────────────────────────────────────────────
function letter_label($letter) {
    return strtoupper($letter);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz — <?= htmlspecialchars($course['title']) ?> — StudyHub</title>
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
    background: var(--bg);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    background-image: radial-gradient(ellipse at 80% 0%, rgba(34,211,238,0.05) 0%, transparent 50%);
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
  .back-link { font-size: 0.85rem; color: var(--muted); text-decoration: none; }
  .back-link:hover { color: var(--text); }

  main { max-width: 760px; margin: 0 auto; padding: 2.5rem 1.5rem; }

  /* QUIZ HEADER */
  .quiz-badge {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--accent2);
    background: rgba(34,211,238,0.08);
    border: 1px solid rgba(34,211,238,0.15);
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
    margin-bottom: 0.75rem;
  }
  h1 {
    font-family: 'Syne', sans-serif;
    font-size: 1.9rem;
    font-weight: 800;
    margin-bottom: 0.4rem;
    line-height: 1.2;
  }
  .quiz-sub { color: var(--muted); margin-bottom: 2rem; font-size: 0.9rem; }

  /* SCORE CARD */
  .score-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2.5rem;
    text-align: center;
    margin-bottom: 2.5rem;
  }
  .score-num-big {
    font-family: 'Syne', sans-serif;
    font-size: 4rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 0.5rem;
  }
  .score-label { font-size: 1rem; color: var(--muted); margin-bottom: 1.5rem; }
  .score-msg {
    font-family: 'Syne', sans-serif;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
  }
  .score-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
  .btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    font-weight: 700;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: opacity 0.2s;
  }
  .btn-primary { background: linear-gradient(135deg, var(--accent), var(--accent2)); color: #0d0f14; }
  .btn-secondary {
    background: var(--card2);
    color: var(--text);
    border: 1px solid var(--border);
  }
  .btn:hover { opacity: 0.85; }

  /* QUESTION CARDS */
  .question-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.75rem;
    margin-bottom: 1.5rem;
  }
  .q-num {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 0.6rem;
  }
  .q-text { font-size: 1rem; font-weight: 600; margin-bottom: 1.25rem; line-height: 1.5; }

  /* OPTION TILES */
  .options { display: flex; flex-direction: column; gap: 0.6rem; }
  .option-label {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 0.85rem 1rem;
    border: 1px solid var(--border);
    border-radius: 10px;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s;
    user-select: none;
  }
  .option-label:hover { border-color: var(--accent2); background: rgba(34,211,238,0.04); }
  .option-label input[type="radio"] { display: none; }
  .option-label input[type="radio"]:checked ~ .option-text { color: var(--accent2); }
  .option-label:has(input:checked) {
    border-color: var(--accent2);
    background: rgba(34,211,238,0.06);
  }
  .option-key {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: var(--card2);
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--muted);
    flex-shrink: 0;
  }
  .option-text { font-size: 0.9rem; }

  /* RESULT COLORING */
  .opt-correct { border-color: var(--accent) !important; background: rgba(74,222,128,0.07) !important; }
  .opt-correct .option-key { background: var(--accent); color: #0d0f14; }
  .opt-wrong   { border-color: var(--err) !important; background: rgba(248,113,113,0.07) !important; }
  .opt-wrong .option-key { background: var(--err); color: #0d0f14; }
  .opt-missed  { border-color: var(--accent) !important; background: rgba(74,222,128,0.04) !important; }
  .opt-missed .option-key { border: 1px solid var(--accent); color: var(--accent); }

  .q-verdict {
    margin-top: 1rem;
    font-size: 0.83rem;
    font-weight: 600;
    padding: 0.5rem 0.8rem;
    border-radius: 8px;
    display: inline-block;
  }
  .verdict-correct { background: rgba(74,222,128,0.1); color: var(--accent); }
  .verdict-wrong   { background: rgba(248,113,113,0.1); color: var(--err); }

  /* SUBMIT */
  .submit-wrap { text-align: center; margin-top: 2rem; }
  .btn-submit {
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    color: #0d0f14;
    border: none;
    padding: 1rem 3rem;
    border-radius: 12px;
    font-family: 'Syne', sans-serif;
    font-size: 1.1rem;
    font-weight: 800;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
  }
  .btn-submit:hover { opacity: 0.88; transform: translateY(-1px); }

  /* DIVIDER */
  .results-divider {
    border: none;
    border-top: 1px solid var(--border);
    margin: 2rem 0;
  }
  .results-heading {
    font-family: 'Syne', sans-serif;
    font-size: 1.2rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
  }
</style>
</head>
<body>

<nav>
  <a href="dashboard.php" class="logo">StudyHub</a>
  <a href="dashboard.php" class="back-link">← Dashboard</a>
</nav>

<main>
  <div class="quiz-badge">📝 Quiz</div>
  <h1><?= htmlspecialchars($course['title']) ?></h1>
  <p class="quiz-sub">
    <?php if ($submitted): ?>
      Here are your results. Review each question below.
    <?php else: ?>
      Answer all questions below. Questions are randomized every attempt.
    <?php endif; ?>
  </p>

  <?php if ($submitted): ?>
    <!-- ─── SCORE DISPLAY ─── -->
    <?php
      $pct = $total > 0 ? round($score / $total * 100) : 0;
      if ($pct >= 90) $msg = "🏆 Excellent work!";
      elseif ($pct >= 75) $msg = "👍 Great job!";
      elseif ($pct >= 50) $msg = "📚 Keep studying!";
      else $msg = "💪 Don't give up!";
    ?>
    <div class="score-card">
      <div class="score-num-big"><?= $score ?>/<?= $total ?></div>
      <div class="score-label"><?= $pct ?>% correct</div>
      <div class="score-msg"><?= $msg ?></div>
      <div class="score-actions">
        <a href="quiz.php?id=<?= $course_id ?>" class="btn btn-primary">🔄 Retake Quiz</a>
        <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
      </div>
    </div>

    <hr class="results-divider">
    <div class="results-heading">Question Review</div>

    <!-- Review each question -->
    <?php foreach ($results as $i => $r): ?>
    <div class="question-card">
      <div class="q-num">Question <?= $i + 1 ?></div>
      <div class="q-text"><?= htmlspecialchars($r['question']) ?></div>
      <div class="options">
        <?php foreach ($r['options'] as $key => $opt): ?>
          <?php
            $cls = '';
            if ($key === $r['correct_answer'] && $key === $r['user_answer']) $cls = 'opt-correct';
            elseif ($key === $r['user_answer'] && $key !== $r['correct_answer']) $cls = 'opt-wrong';
            elseif ($key === $r['correct_answer']) $cls = 'opt-missed';
          ?>
          <div class="option-label <?= $cls ?>" style="cursor:default">
            <span class="option-key"><?= strtoupper($key) ?></span>
            <span class="option-text"><?= htmlspecialchars($opt) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="q-verdict <?= $r['is_correct'] ? 'verdict-correct' : 'verdict-wrong' ?>">
        <?= $r['is_correct'] ? '✅ Correct' : '❌ Incorrect — Correct answer: ' . strtoupper($r['correct_answer']) ?>
      </div>
    </div>
    <?php endforeach; ?>

  <?php else: ?>
    <!-- ─── QUIZ FORM ─── -->
    <?php if (empty($questions)): ?>
      <div style="color:var(--muted); background:var(--card); border:1px solid var(--border); border-radius:16px; padding:2rem; text-align:center;">
        No questions available for this course yet.
        <br><br>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
      </div>
    <?php else: ?>
      <form method="POST">
        <!-- Hidden field: store IDs of shown questions to grade them on submit -->
        <input type="hidden" name="question_ids"
               value="<?= implode(',', array_column($questions, 'id')) ?>">

        <?php foreach ($questions as $i => $q): ?>
        <div class="question-card">
          <div class="q-num">Question <?= $i + 1 ?> of <?= count($questions) ?></div>
          <div class="q-text"><?= htmlspecialchars($q['question_text']) ?></div>
          <div class="options">
            <?php foreach (['a','b','c','d'] as $opt): ?>
              <?php if (!empty($q['option_' . $opt])): ?>
              <label class="option-label">
                <input type="radio" name="answer_<?= $q['id'] ?>" value="<?= $opt ?>">
                <span class="option-key"><?= strtoupper($opt) ?></span>
                <span class="option-text"><?= htmlspecialchars($q['option_' . $opt]) ?></span>
              </label>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>

        <div class="submit-wrap">
          <button type="submit" name="submit_quiz" class="btn-submit">Submit Quiz →</button>
        </div>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</main>

</body>
</html>