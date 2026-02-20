<?php
session_start();
include("config.php");

// 1. Authentication Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$dateToday = date("Y-m-d");

// 2. Filter & Priority Logic
$filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : 'all';
$level = isset($_GET['level']) ? mysqli_real_escape_string($conn, $_GET['level']) : '';

// --- TASK QUERY LOGIC ---
if($filter == "completed"){
    $taskSql = "SELECT * FROM tasks WHERE user_id='$user_id' AND status='Completed' ORDER BY created_at DESC";
}
elseif($filter == "pending"){
    $taskSql = "SELECT * FROM tasks WHERE user_id='$user_id' AND status='Pending' ORDER BY created_at DESC";
}
elseif($filter == "priority" && in_array($level, ['High','Medium','Low'])){
    $taskSql = "SELECT * FROM tasks WHERE user_id='$user_id' AND priority='$level' ORDER BY created_at DESC";
}
else{
    $taskSql = "SELECT * FROM tasks WHERE user_id='$user_id' ORDER BY created_at DESC";
}
$taskQuery = mysqli_query($conn, $taskSql);

// --- HABIT QUERY LOGIC ---
if ($filter == "daily" || $filter == "weekly") {
    $habitSql = "SELECT * FROM habits WHERE user_id='$user_id' AND habit_type='$filter' ORDER BY created_at DESC";
} else {
    $habitSql = "SELECT * FROM habits WHERE user_id='$user_id' ORDER BY created_at DESC";
}
$habitQuery = mysqli_query($conn, $habitSql);

// --- FUNCTIONAL STATS FETCHING ---

$totalTasks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tasks WHERE user_id='$user_id'"))['total'];
$completedTasks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tasks WHERE user_id='$user_id' AND status='Completed'"))['total'];
$pendingTasks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tasks WHERE user_id='$user_id' AND status='Pending'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Productivity Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .stat-card { border: none; border-radius: 12px; transition: 0.3s; text-decoration: none !important; color: inherit; display: block; cursor: pointer; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; }
        .btn-filter-pill { font-size: 0.65rem; font-weight: 700; padding: 4px 12px; border-radius: 50px; text-decoration: none; display: inline-block; text-transform: uppercase; transition: 0.2s; }
        .btn-filter-pill:hover { opacity: 0.8; color: white; }
        .habit-item { border-radius: 12px; border: 1px solid #eee !important; margin-bottom: 8px; transition: 0.3s; background: #fff; }
        .habit-item:hover { border-color: #0d6efd !important; }
        .done-text { text-decoration: line-through; opacity: 0.6; }
        .action-btn { padding: 4px 8px; border-radius: 6px; transition: 0.2s; color: #6c757d; text-decoration: none; }
        .action-btn:hover { background: #f0f0f0; color: #0d6efd; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark shadow-sm py-3 mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-rocket-takeoff-fill text-primary me-2"></i>TASK TRACKER</a>
        <div class="ms-auto d-flex align-items-center text-white">
            <span class="me-3 d-none d-sm-block small">Hi, <strong><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'User'); ?></strong></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row g-3 mb-4">
        <div class="col-4">
            <a href="dashboard.php?filter=all" class="card stat-card shadow-sm border-0 text-center p-2">
                <small class="text-muted fw-bold">TOTAL</small>
                <div class="h5 mb-0 fw-bold"><?php echo $totalTasks; ?></div>
            </a>
        </div>
        <div class="col-4">
            <a href="dashboard.php?filter=completed" class="card stat-card shadow-sm border-0 text-center p-2 text-success">
                <small class="text-muted fw-bold">DONE</small>
                <div class="h5 mb-0 fw-bold"><?php echo $completedTasks; ?></div>
            </a>
        </div>
        <div class="col-4">
            <a href="dashboard.php?filter=pending" class="card stat-card shadow-sm border-0 text-center p-2 text-warning">
                <small class="text-muted fw-bold">PENDING</small>
                <div class="h5 mb-0 fw-bold"><?php echo $pendingTasks; ?></div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white pt-3 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold">My Tasks (<?php echo ucfirst($filter); ?>)</h5>
                        <a href="add_task.php" class="btn btn-primary btn-sm rounded-pill px-3"><i class="bi bi-plus-lg"></i> New Task</a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="dashboard.php?filter=priority&level=High" class="btn-filter-pill bg-danger text-white">High</a>
                        <a href="dashboard.php?filter=priority&level=Medium" class="btn-filter-pill bg-warning text-dark">Medium</a>
                        <a href="dashboard.php?filter=priority&level=Low" class="btn-filter-pill bg-info text-white">Low</a>
                        <a href="dashboard.php?filter=all" class="btn-filter-pill bg-secondary text-white">Reset</a>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4 py-3">TASK</th>
                                    <th>PRIORITY</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center pe-4">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($taskQuery) > 0): ?>
                                    <?php while($task = mysqli_fetch_assoc($taskQuery)): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold small <?php echo ($task['status'] == 'Completed') ? 'done-text' : ''; ?>">
                                                <?php echo htmlspecialchars($task['title']); ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-calendar"></i> <?php echo date("d M, Y", strtotime($task['due_date'])); ?></div>
                                        </td>
                                        <td>
                                            <?php 
                                                $pClass = ($task['priority'] == 'High') ? 'bg-danger' : (($task['priority'] == 'Medium') ? 'bg-warning text-dark' : 'bg-info'); 
                                            ?>
                                            <span class="badge <?php echo $pClass; ?> rounded-pill" style="font-size: 0.6rem;"><?php echo $task['priority']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if($task['status'] == 'Completed'): ?>
                                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <?php else: ?>
                                                <a href="complete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-success py-0 px-2 fw-bold" style="font-size: 0.65rem;">Mark Done</a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="action-btn" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="action-btn text-danger" onclick="return confirm('Are you sure you want to delete this task?')" title="Delete"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted small">NO TASK FOUND</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white pt-3 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold">Habits</h5>
                        <a href="add_habit.php" class="btn btn-success btn-sm rounded-circle"><i class="bi bi-plus"></i></a>
                    </div>
                    <div class="d-flex gap-1 pb-2">
                        <a href="dashboard.php?filter=daily" class="btn-filter-pill border text-primary">Daily</a>
                        <a href="dashboard.php?filter=weekly" class="btn-filter-pill border text-info">Weekly</a>
                        <a href="dashboard.php?filter=all" class="btn-filter-pill border text-secondary">All</a>
                    </div>
                </div>

                <div class="card-body">
                    <?php if(mysqli_num_rows($habitQuery) > 0): ?>
                        <?php while($habit = mysqli_fetch_assoc($habitQuery)): 
                            $h_id = $habit['id'];
                            $check = mysqli_query($conn, "SELECT * FROM habit_logs WHERE habit_id='$h_id' AND log_date='$dateToday'");
                            $isDone = mysqli_num_rows($check) > 0;
                        ?>
                        <div class="habit-item p-3 d-flex justify-content-between align-items-center">
                            <div class="overflow-hidden">
                                <div class="fw-bold small <?php echo $isDone ? 'done-text' : 'text-dark'; ?> text-truncate">
                                    <?php echo htmlspecialchars($habit['habit_name']); ?>
                                </div>
                                <span class="text-uppercase text-muted" style="font-size: 0.6rem;"><?php echo $habit['habit_type']; ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <?php if(!$isDone): ?>
                                    <a href="mark_habit.php?id=<?php echo $h_id; ?>" class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold" style="font-size: 0.6rem;">DONE</a>
                                <?php else: ?>
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <?php endif; ?>
                                <a href="edit_habit.php?id=<?php echo $h_id; ?>" class="text-warning small"><i class="bi bi-pencil"></i></a>
                                <a href="delete_habit.php?id=<?php echo $h_id; ?>" class="text-danger small" onclick="return confirm('Habit delete')"><i class="bi bi-trash"></i></a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">Koi habits nahi hain.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>