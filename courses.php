<?php
// courses.php — Manage courses, lessons, and questions (admin panel)
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$msg       = '';
$msg_type  = 'success';

// ─── HANDLE ACTIONS ──────────────────────────────────────────────────────────

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ADD COURSE
if ($action === 'add_course' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);
    if ($title) {
        $s = mysqli_prepare($conn, "INSERT INTO courses (title, description) VALUES (?, ?)");
        mysqli_stmt_bind_param($s, 'ss', $title, $desc);
        mysqli_stmt_execute($s);
        $msg = 'Course added successfully.';
    } else {
        $msg = 'Course title is required.'; $msg_type = 'error';
    }
}

// EDIT COURSE
if ($action === 'edit_course' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int)$_POST['course_id'];
    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);
    if ($title && $id) {
        $s = mysqli_prepare($conn, "UPDATE courses SET title=?, description=? WHERE id=?");
        mysqli_stmt_bind_param($s, 'ssi', $title, $desc, $id);
        mysqli_stmt_execute($s);
        $msg = 'Course updated.';
    }
}

// DELETE COURSE
if ($action === 'delete_course' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM courses WHERE id=$id");
    $msg = 'Course deleted.';
}

// ADD LESSON
if ($action === 'add_lesson' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid     = (int)$_POST['course_id'];
    $title   = trim($_POST['lesson_title']);
    $content = trim($_POST['lesson_content']);
    // Auto position: max + 1
    $maxpos  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(position) as m FROM lessons WHERE course_id=$cid"));
    $pos     = ($maxpos['m'] ?? 0) + 1;
    if ($title && $cid) {
        $s = mysqli_prepare($conn, "INSERT INTO lessons (course_id, title, content, position) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($s, 'issi', $cid, $title, $content, $pos);
        mysqli_stmt_execute($s);
        $msg = 'Lesson added.';
    } else {
        $msg = 'Lesson title is required.'; $msg_type = 'error';
    }
}

// DELETE LESSON
if ($action === 'delete_lesson' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM lessons WHERE id=$id");
    $msg = 'Lesson deleted.';
}

// ADD QUESTION
if ($action === 'add_question' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid     = (int)$_POST['course_id'];
    $qtext   = trim($_POST['question_text']);
    $oa      = trim($_POST['option_a']);
    $ob      = trim($_POST['option_b']);
    $oc      = trim($_POST['option_c']);
    $od      = trim($_POST['option_d']);
    $correct = strtolower(trim($_POST['correct_answer']));
    if ($qtext && $oa && $ob && $correct) {
        $s = mysqli_prepare($conn,
            "INSERT INTO questions (course_id, question_text, option_a, option_b, option_c, option_d, correct_answer)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($s, 'issssss', $cid, $qtext, $oa, $ob, $oc, $od, $correct);
        mysqli_stmt_execute($s);
        $msg = 'Question added.';
    } else {
        $msg = 'Question text, options A & B, and correct answer are required.'; $msg_type = 'error';
    }
}

// DELETE QUESTION
if ($action === 'delete_question' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM questions WHERE id=$id");
    $msg = 'Question deleted.';
}

// ─── FETCH DATA ───────────────────────────────────────────────────────────────
$courses_result = mysqli_query($conn, "SELECT * FROM courses ORDER BY id ASC");
$courses        = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

// Active tab (which course to expand)
$active_course = isset($_GET['course']) ? (int)$_GET['course'] : ($courses[0]['id'] ?? 0);

// Fetch lessons and questions for active course
$lessons   = [];
$questions = [];
if ($active_course) {
    $lr = mysqli_query($conn, "SELECT * FROM lessons WHERE course_id=$active_course ORDER BY position ASC, id ASC");
    $lessons = mysqli_fetch_all($lr, MYSQLI_ASSOC);
    $qr = mysqli_query($conn, "SELECT * FROM questions WHERE course_id=$active_course ORDER BY id ASC");
    $questions = mysqli_fetch_all($qr, MYSQLI_ASSOC);
}

// Find active course title
$active_course_title = '';
foreach ($courses as $c) {
    if ($c['id'] == $active_course) { $active_course_title = $c['title']; break; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Courses — StudyHub</title>
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
  body { background:var(--bg); color:var(--text); font-family:'DM Sans',sans-serif; min-height:100vh; }

  /* NAV */
  nav {
    display:flex; align-items:center; justify-content:space-between;
    padding:1rem 2rem; border-bottom:1px solid var(--border);
    background:rgba(13,15,20,0.9); backdrop-filter:blur(10px);
    position:sticky; top:0; z-index:100;
  }
  .logo {
    font-family:'Syne',sans-serif; font-size:1.4rem; font-weight:800;
    background:linear-gradient(135deg,var(--accent),var(--accent2));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    text-decoration:none;
  }
  .nav-links { display:flex; align-items:center; gap:0.5rem; }
  .nav-link {
    font-size:0.82rem; font-weight:600; padding:0.4rem 0.9rem; border-radius:8px;
    text-decoration:none; color:var(--muted); border:1px solid transparent;
    transition:color 0.2s, border-color 0.2s;
  }
  .nav-link:hover, .nav-link.active { color:var(--text); border-color:var(--border); }
  .nav-link.active { color:var(--accent); border-color:rgba(74,222,128,0.3); }
  .logout-btn {
    font-size:0.8rem; color:var(--muted); text-decoration:none; padding:0.4rem 0.9rem;
    border:1px solid var(--border); border-radius:8px; transition:color 0.2s, border-color 0.2s;
  }
  .logout-btn:hover { color:var(--accent); border-color:var(--accent); }

  /* LAYOUT */
  .layout { display:grid; grid-template-columns:260px 1fr; min-height:calc(100vh - 61px); }

  /* SIDEBAR */
  .sidebar {
    border-right:1px solid var(--border);
    padding:1.5rem 1rem;
    background:var(--card);
    overflow-y:auto;
  }
  .sidebar-title {
    font-family:'Syne',sans-serif; font-size:0.75rem; font-weight:700;
    color:var(--muted); text-transform:uppercase; letter-spacing:0.1em;
    margin-bottom:1rem; padding:0 0.5rem;
  }
  .course-item {
    display:flex; align-items:center; justify-content:space-between;
    padding:0.7rem 0.75rem; border-radius:10px; margin-bottom:0.3rem;
    cursor:pointer; text-decoration:none; color:var(--text);
    font-size:0.875rem; font-weight:500; border:1px solid transparent;
    transition:background 0.15s, border-color 0.15s;
  }
  .course-item:hover { background:var(--card2); }
  .course-item.active { background:rgba(74,222,128,0.07); border-color:rgba(74,222,128,0.2); color:var(--accent); }
  .course-item-actions { display:flex; gap:0.3rem; opacity:0; transition:opacity 0.15s; }
  .course-item:hover .course-item-actions { opacity:1; }
  .icon-btn {
    background:none; border:none; cursor:pointer; font-size:0.85rem; padding:0.15rem 0.3rem;
    border-radius:4px; color:var(--muted); transition:color 0.15s, background 0.15s;
  }
  .icon-btn:hover { background:var(--border); color:var(--text); }
  .icon-btn.del:hover { color:var(--err); }
  .add-course-btn {
    display:flex; align-items:center; gap:0.5rem; justify-content:center;
    margin-top:1rem; padding:0.65rem; border-radius:10px;
    background:rgba(74,222,128,0.07); border:1px dashed rgba(74,222,128,0.25);
    color:var(--accent); font-size:0.85rem; font-weight:600; cursor:pointer;
    text-decoration:none; transition:background 0.15s;
  }
  .add-course-btn:hover { background:rgba(74,222,128,0.13); }

  /* MAIN CONTENT */
  .content { padding:2rem 2.5rem; overflow-y:auto; }
  .content-header {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:2rem; flex-wrap:wrap; gap:1rem;
  }
  .content-header h1 { font-family:'Syne',sans-serif; font-size:1.6rem; font-weight:800; }
  .content-header p { color:var(--muted); font-size:0.875rem; margin-top:0.2rem; }

  /* TABS */
  .tabs { display:flex; gap:0.5rem; margin-bottom:1.75rem; border-bottom:1px solid var(--border); }
  .tab-btn {
    padding:0.6rem 1.2rem; border:none; background:none; color:var(--muted);
    font-family:'DM Sans',sans-serif; font-size:0.875rem; font-weight:600;
    cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px;
    transition:color 0.15s, border-color 0.15s;
  }
  .tab-btn:hover { color:var(--text); }
  .tab-btn.active { color:var(--accent); border-bottom-color:var(--accent); }
  .tab-panel { display:none; }
  .tab-panel.active { display:block; }

  /* FLASH MESSAGE */
  .flash {
    padding:0.75rem 1rem; border-radius:10px; margin-bottom:1.5rem; font-size:0.875rem; font-weight:500;
  }
  .flash-success { background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.25); color:var(--accent); }
  .flash-error   { background:rgba(248,113,113,0.1); border:1px solid rgba(248,113,113,0.25); color:var(--err); }

  /* FORMS */
  .form-card {
    background:var(--card); border:1px solid var(--border); border-radius:14px;
    padding:1.5rem; margin-bottom:1.5rem;
  }
  .form-card h3 { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; margin-bottom:1.25rem; }
  .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
  .form-group { margin-bottom:1rem; }
  .form-group label { display:block; font-size:0.8rem; font-weight:600; color:var(--muted); margin-bottom:0.4rem; }
  .form-group input, .form-group textarea, .form-group select {
    width:100%; background:var(--bg); border:1px solid var(--border); border-radius:8px;
    padding:0.65rem 0.9rem; color:var(--text); font-family:'DM Sans',sans-serif; font-size:0.875rem;
    outline:none; transition:border-color 0.2s;
  }
  .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color:var(--accent2); }
  .form-group textarea { resize:vertical; min-height:90px; }
  .form-group select option { background:var(--card2); }
  .required-star { color:var(--err); }
  .submit-row { display:flex; justify-content:flex-end; }
  .btn-add {
    background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#0d0f14;
    border:none; border-radius:8px; padding:0.6rem 1.4rem;
    font-family:'DM Sans',sans-serif; font-size:0.875rem; font-weight:700;
    cursor:pointer; transition:opacity 0.2s;
  }
  .btn-add:hover { opacity:0.88; }

  /* DATA TABLE */
  .data-table { width:100%; border-collapse:collapse; }
  .data-table th {
    text-align:left; font-size:0.75rem; font-weight:700; text-transform:uppercase;
    letter-spacing:0.07em; color:var(--muted); padding:0.6rem 1rem;
    border-bottom:1px solid var(--border);
  }
  .data-table td {
    padding:0.85rem 1rem; border-bottom:1px solid rgba(37,42,53,0.6);
    font-size:0.875rem; vertical-align:middle;
  }
  .data-table tr:last-child td { border-bottom:none; }
  .data-table tr:hover td { background:rgba(255,255,255,0.02); }
  .badge {
    display:inline-block; padding:0.2rem 0.6rem; border-radius:20px;
    font-size:0.72rem; font-weight:600;
  }
  .badge-blue { background:rgba(34,211,238,0.1); color:var(--accent2); border:1px solid rgba(34,211,238,0.2); }
  .badge-green { background:rgba(74,222,128,0.1); color:var(--accent); border:1px solid rgba(74,222,128,0.2); }
  .badge-warn  { background:rgba(245,158,11,0.1); color:var(--warn); border:1px solid rgba(245,158,11,0.2); }
  .del-link {
    color:var(--err); text-decoration:none; font-size:0.8rem; font-weight:600;
    padding:0.25rem 0.6rem; border-radius:6px; border:1px solid rgba(248,113,113,0.2);
    transition:background 0.15s;
  }
  .del-link:hover { background:rgba(248,113,113,0.1); }
  .empty-state {
    text-align:center; padding:3rem; color:var(--muted); font-size:0.9rem;
  }
  .empty-state .icon { font-size:2.5rem; margin-bottom:0.75rem; }

  /* MODAL */
  .modal-overlay {
    display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7);
    backdrop-filter:blur(4px); z-index:200; align-items:center; justify-content:center;
  }
  .modal-overlay.open { display:flex; }
  .modal {
    background:var(--card); border:1px solid var(--border); border-radius:18px;
    padding:2rem; width:100%; max-width:500px; max-height:90vh; overflow-y:auto;
    box-shadow:0 30px 80px rgba(0,0,0,0.5);
  }
  .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
  .modal-header h2 { font-family:'Syne',sans-serif; font-size:1.2rem; font-weight:800; }
  .modal-close {
    background:none; border:none; color:var(--muted); font-size:1.2rem;
    cursor:pointer; padding:0.25rem; border-radius:6px;
  }
  .modal-close:hover { color:var(--text); }

  @media (max-width: 768px) {
    .layout { grid-template-columns:1fr; }
    .sidebar { border-right:none; border-bottom:1px solid var(--border); }
    .content { padding:1.5rem 1rem; }
    .form-row { grid-template-columns:1fr; }
  }
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="dashboard.php" class="logo">StudyHub</a>
  <div class="nav-links">
    <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
    <a href="courses.php" class="nav-link active">📚 Courses</a>
    <a href="quiz_results.php" class="nav-link">📋 My Results</a>
    <a href="logout.php" class="logout-btn">Sign out</a>
  </div>
</nav>

<!-- LAYOUT -->
<div class="layout">

  <!-- SIDEBAR: course list -->
  <aside class="sidebar">
    <div class="sidebar-title">All Courses</div>

    <?php foreach ($courses as $c): ?>
      <div style="display:flex; align-items:center; gap:0.3rem; margin-bottom:0.3rem;">
        <a href="courses.php?course=<?= $c['id'] ?>"
           class="course-item <?= $c['id'] == $active_course ? 'active' : '' ?>"
           style="flex:1;">
          <span style="flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
            <?= htmlspecialchars($c['title']) ?>
          </span>
          <span class="course-item-actions">
            <button class="icon-btn" onclick="openEditCourse(<?= $c['id'] ?>, '<?= addslashes($c['title']) ?>', '<?= addslashes($c['description']) ?>')" title="Edit">✏️</button>
            <a href="courses.php?action=delete_course&id=<?= $c['id'] ?><?= $active_course ? '&course='.$active_course : '' ?>"
               class="icon-btn del" title="Delete"
               onclick="return confirm('Delete this course and ALL its lessons and questions?')">🗑️</a>
          </span>
        </a>
      </div>
    <?php endforeach; ?>

    <a href="#" class="add-course-btn" onclick="openAddCourse(); return false;">
      ＋ Add Course
    </a>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="content">

    <?php if ($msg): ?>
      <div class="flash flash-<?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (!$active_course || empty($courses)): ?>
      <div class="empty-state">
        <div class="icon">📚</div>
        <p>No courses yet. Add your first course from the sidebar.</p>
      </div>
    <?php else: ?>

      <div class="content-header">
        <div>
          <h1><?= htmlspecialchars($active_course_title) ?></h1>
          <p>Manage lessons and quiz questions for this course.</p>
        </div>
      </div>

      <!-- TABS -->
      <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('lessons', this)">📖 Lessons (<?= count($lessons) ?>)</button>
        <button class="tab-btn" onclick="switchTab('questions', this)">❓ Questions (<?= count($questions) ?>)</button>
      </div>

      <!-- LESSONS TAB -->
      <div id="tab-lessons" class="tab-panel active">

        <!-- Add Lesson Form -->
        <div class="form-card">
          <h3>➕ Add New Lesson</h3>
          <form method="POST" action="courses.php?course=<?= $active_course ?>">
            <input type="hidden" name="action" value="add_lesson">
            <input type="hidden" name="course_id" value="<?= $active_course ?>">
            <div class="form-group">
              <label>Lesson Title <span class="required-star">*</span></label>
              <input type="text" name="lesson_title" placeholder="e.g. Introduction to Variables" required>
            </div>
            <div class="form-group">
              <label>Content <small style="color:var(--muted)">(HTML allowed)</small></label>
              <textarea name="lesson_content" placeholder="<p>Lesson content here...</p>&#10;<pre><code>// code example</code></pre>"></textarea>
            </div>
            <div class="submit-row">
              <button type="submit" class="btn-add">Add Lesson</button>
            </div>
          </form>
        </div>

        <!-- Lessons Table -->
        <?php if (empty($lessons)): ?>
          <div class="empty-state"><div class="icon">📄</div><p>No lessons yet for this course.</p></div>
        <?php else: ?>
          <div class="form-card" style="padding:0; overflow:hidden;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Position</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lessons as $i => $l): ?>
                <tr>
                  <td style="color:var(--muted); width:40px;"><?= $i + 1 ?></td>
                  <td>
                    <span style="font-weight:600;"><?= htmlspecialchars($l['title']) ?></span>
                    <div style="font-size:0.78rem; color:var(--muted); margin-top:0.2rem;">
                      <?= strlen(strip_tags($l['content'])) ?> chars of content
                    </div>
                  </td>
                  <td><span class="badge badge-blue">Pos <?= $l['position'] ?></span></td>
                  <td>
                    <a href="lesson.php?id=<?= $l['id'] ?>" class="del-link" style="color:var(--accent2); border-color:rgba(34,211,238,0.2);" target="_blank">View</a>
                    &nbsp;
                    <a href="courses.php?action=delete_lesson&id=<?= $l['id'] ?>&course=<?= $active_course ?>"
                       class="del-link"
                       onclick="return confirm('Delete this lesson?')">Delete</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div><!-- /tab-lessons -->

      <!-- QUESTIONS TAB -->
      <div id="tab-questions" class="tab-panel">

        <!-- Add Question Form -->
        <div class="form-card">
          <h3>➕ Add New Question</h3>
          <form method="POST" action="courses.php?course=<?= $active_course ?>&tab=questions">
            <input type="hidden" name="action" value="add_question">
            <input type="hidden" name="course_id" value="<?= $active_course ?>">
            <div class="form-group">
              <label>Question Text <span class="required-star">*</span></label>
              <textarea name="question_text" placeholder="e.g. What does PHP stand for?" required style="min-height:70px;"></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Option A <span class="required-star">*</span></label>
                <input type="text" name="option_a" placeholder="First choice" required>
              </div>
              <div class="form-group">
                <label>Option B <span class="required-star">*</span></label>
                <input type="text" name="option_b" placeholder="Second choice" required>
              </div>
              <div class="form-group">
                <label>Option C</label>
                <input type="text" name="option_c" placeholder="Third choice (optional)">
              </div>
              <div class="form-group">
                <label>Option D</label>
                <input type="text" name="option_d" placeholder="Fourth choice (optional)">
              </div>
            </div>
            <div class="form-group" style="max-width:200px;">
              <label>Correct Answer <span class="required-star">*</span></label>
              <select name="correct_answer" required>
                <option value="">— Select —</option>
                <option value="a">A</option>
                <option value="b">B</option>
                <option value="c">C</option>
                <option value="d">D</option>
              </select>
            </div>
            <div class="submit-row">
              <button type="submit" class="btn-add">Add Question</button>
            </div>
          </form>
        </div>

        <!-- Questions Table -->
        <?php if (empty($questions)): ?>
          <div class="empty-state"><div class="icon">❓</div><p>No questions yet for this course.</p></div>
        <?php else: ?>
          <div class="form-card" style="padding:0; overflow:hidden;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Question</th>
                  <th>Options</th>
                  <th>Correct</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($questions as $i => $q): ?>
                <tr>
                  <td style="color:var(--muted); width:40px;"><?= $i + 1 ?></td>
                  <td style="max-width:260px;"><?= htmlspecialchars(mb_strimwidth($q['question_text'], 0, 80, '…')) ?></td>
                  <td style="font-size:0.78rem; color:var(--muted); line-height:1.6;">
                    A: <?= htmlspecialchars($q['option_a']) ?><br>
                    B: <?= htmlspecialchars($q['option_b']) ?><br>
                    <?php if ($q['option_c']): ?>C: <?= htmlspecialchars($q['option_c']) ?><br><?php endif; ?>
                    <?php if ($q['option_d']): ?>D: <?= htmlspecialchars($q['option_d']) ?><?php endif; ?>
                  </td>
                  <td><span class="badge badge-green"><?= strtoupper($q['correct_answer']) ?></span></td>
                  <td>
                    <a href="courses.php?action=delete_question&id=<?= $q['id'] ?>&course=<?= $active_course ?>&tab=questions"
                       class="del-link"
                       onclick="return confirm('Delete this question?')">Delete</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div><!-- /tab-questions -->

    <?php endif; ?>
  </div><!-- /content -->
</div><!-- /layout -->

<!-- ADD COURSE MODAL -->
<div class="modal-overlay" id="addCourseModal">
  <div class="modal">
    <div class="modal-header">
      <h2>Add New Course</h2>
      <button class="modal-close" onclick="closeModal('addCourseModal')">✕</button>
    </div>
    <form method="POST" action="courses.php">
      <input type="hidden" name="action" value="add_course">
      <div class="form-group">
        <label>Course Title <span class="required-star">*</span></label>
        <input type="text" name="title" placeholder="e.g. Introduction to JavaScript" required>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" placeholder="Brief description of the course..." style="min-height:80px;"></textarea>
      </div>
      <div class="submit-row">
        <button type="submit" class="btn-add">Create Course</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT COURSE MODAL -->
<div class="modal-overlay" id="editCourseModal">
  <div class="modal">
    <div class="modal-header">
      <h2>Edit Course</h2>
      <button class="modal-close" onclick="closeModal('editCourseModal')">✕</button>
    </div>
    <form method="POST" action="courses.php?course=<?= $active_course ?>">
      <input type="hidden" name="action" value="edit_course">
      <input type="hidden" name="course_id" id="editCourseId">
      <div class="form-group">
        <label>Course Title <span class="required-star">*</span></label>
        <input type="text" name="title" id="editCourseTitle" required>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" id="editCourseDesc" style="min-height:80px;"></textarea>
      </div>
      <div class="submit-row">
        <button type="submit" class="btn-add">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
// TAB SWITCHING
function switchTab(tab, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  btn.classList.add('active');
}

// Auto-open questions tab if redirected with ?tab=questions
<?php if (isset($_GET['tab']) && $_GET['tab'] === 'questions'): ?>
document.addEventListener('DOMContentLoaded', () => {
  const btns = document.querySelectorAll('.tab-btn');
  switchTab('questions', btns[1]);
});
<?php endif; ?>

// MODALS
function openAddCourse() {
  document.getElementById('addCourseModal').classList.add('open');
}
function openEditCourse(id, title, desc) {
  document.getElementById('editCourseId').value = id;
  document.getElementById('editCourseTitle').value = title;
  document.getElementById('editCourseDesc').value = desc;
  document.getElementById('editCourseModal').classList.add('open');
}
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}
// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', e => {
    if (e.target === overlay) overlay.classList.remove('open');
  });
});
</script>
</body>
</html>