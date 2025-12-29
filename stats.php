<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
include('db.php');

try {
    // MongoDB Aggregation: Department ke hisab se group kar ke count karna
    $command = new MongoDB\Driver\Command([
        'aggregate' => 'students',
        'pipeline' => [
            ['$group' => ['_id' => '$department', 'count' => ['$sum' => 1]]]
        ],
        'cursor' => new stdClass,
    ]);
    
    $cursor = $manager->executeCommand('uf_student_db', $command);
    $stats = $cursor->toArray();
} catch (Exception $e) {
    $stats = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Department Stats | TUF</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background: #f4f7f6; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: #002244; color: white; position: fixed; height: 100%; }
        .sidebar-header { padding: 30px 20px; text-align: center; border-bottom: 2px solid #ffcc00; }
        .nav-links a { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; }
        .nav-links a.active { background: #003366; color: white; border-left: 5px solid #ffcc00; }

        /* Content */
        .main-content { margin-left: 260px; width: 100%; padding: 40px; }
        .stats-card { background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; max-width: 700px; }
        .stats-header { background: #002244; color: white; padding: 20px; font-size: 18px; font-weight: bold; }
        
        .stats-table { width: 100%; border-collapse: collapse; }
        .stats-table th, .stats-table td { padding: 15px 20px; text-align: left; border-bottom: 1px solid #eee; }
        .stats-table th { background: #f8f9fa; color: #002244; font-size: 14px; text-transform: uppercase; }
        .stats-table tr:hover { background: #f1f1f1; }
        
        .dept-badge { background: #eef2f7; color: #003366; padding: 5px 12px; border-radius: 4px; font-weight: bold; }
        .count-circle { background: #ffcc00; color: #002244; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><h3 style="color:white">TUF ADMIN</h3></div>
    <div class="nav-links">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
        <a href="stats.php" class="active"><i class="fas fa-chart-bar"></i> System Stats</a>
    </div>
</div>

<div class="main-content">
    <div class="stats-card">
        <div class="stats-header">
            <i class="fas fa-chart-pie"></i> Students Enrollment by Department
        </div>
        <table class="stats-table">
            <thead>
                <tr>
                    <th>Department Name</th>
                    <th>No. of Students</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($stats)): ?>
                    <?php foreach($stats as $row): ?>
                        <tr>
                            <td><span class="dept-badge"><?php echo htmlspecialchars($row->_id ?: 'Not Assigned'); ?></span></td>
                            <td><div class="count-circle"><?php echo $row->count; ?></div></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="text-align:center; color:red;">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>