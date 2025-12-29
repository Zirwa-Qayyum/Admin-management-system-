<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('db.php');

try {
    $countCommand = new MongoDB\Driver\Command(['count' => 'students']);
    $countRes = $manager->executeCommand('uf_student_db', $countCommand);
    $totalStudents = $countRes->toArray()[0]->n;
} catch (Exception $e) { $totalStudents = 0; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TUF Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background: #f4f7f6; min-height: 100vh; }
        .sidebar { width: 260px; background: #002244; color: white; position: fixed; height: 100%; }
        .sidebar-header { padding: 30px 20px; text-align: center; border-bottom: 2px solid #ffcc00; }
        /* Filter hata diya gaya hai */
        .sidebar-header img { width: 100px; filter: none; } 
        .nav-links a { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: #003366; color: white; border-left: 5px solid #ffcc00; }

        .main-content { margin-left: 260px; width: 100%; }
        header { background: white; padding: 20px 40px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .dashboard-grid { padding: 40px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        
        .menu-card {
            background: white; padding: 30px; border-radius: 12px; text-align: center;
            text-decoration: none; color: #333; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: 0.3s; border-bottom: 5px solid #003366;
        }
        .menu-card:hover { transform: translateY(-8px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .menu-card i { font-size: 40px; color: #003366; margin-bottom: 15px; }
        .menu-card h3 { font-size: 18px; margin-bottom: 10px; }
        .menu-card .count { font-size: 24px; font-weight: bold; color: #ffcc00; background: #002244; display: inline-block; padding: 5px 15px; border-radius: 20px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="tuf-logo.png" alt="TUF">
        <h3 style="font-size: 14px; margin-top:10px; color: #ffcc00;">TUF ADMIN</h3>
    </div>
    <div class="nav-links">
        <a href="dashboard.php" class="active"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
        <a href="stats.php"><i class="fas fa-chart-bar"></i> System Stats</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php" style="color: #ff4d4d;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <header>
        <h2>Welcome Admin</h2>
        <div><i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?></div>
    </header>

    <div class="dashboard-grid">
        <a href="add_student.php" class="menu-card">
            <i class="fas fa-plus-circle"></i>
            <h3>Add Student</h3>
            <p>Enroll new students</p>
        </a>
        <a href="view_students.php" class="menu-card">
            <i class="fas fa-list-ul"></i>
            <h3>View Records</h3>
            <div class="count"><?php echo $totalStudents; ?></div>
        </a>
        <a href="stats.php" class="menu-card" style="border-bottom-color: #ffcc00;">
            <i class="fas fa-chart-line" style="color: #ffcc00;"></i>
            <h3>System Stats</h3>
            <p>Enrollment by Dept</p>
        </a>
        <a href="settings.php" class="menu-card" style="border-bottom-color: #27ae60;">
            <i class="fas fa-tools" style="color: #27ae60;"></i>
            <h3>Settings</h3>
            <p>Change Password</p>
        </a>
    </div>
</div>
</body>
</html>