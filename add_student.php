<?php
session_start();
// Aapne bataya tha ke file login.php hai
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('db.php'); 

$success = "";
$error = "";

if(isset($_POST['add'])){
    try {
        $bulk = new MongoDB\Driver\BulkWrite;
        $studentData = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'department' => $_POST['department'],
            'semester' => $_POST['semester'],
            'registration_number' => $_POST['registration_number']
        ];
        $bulk->insert($studentData);
        $manager->executeBulkWrite('uf_student_db.students', $bulk);
        $success = "Mubarak ho! Student ka data save ho gaya.";
    } catch (Exception $e) {
        $error = "Masla aa gaya: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student | TUF Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background: #f4f7f6; min-height: 100vh; }

        /* --- SIDEBAR --- */
        .sidebar { width: 260px; background: #002244; color: white; position: fixed; height: 100%; }
        .sidebar-header { padding: 20px; text-align: center; background: #001a33; border-bottom: 2px solid #ffcc00; }
        
        /* Filter hata diya gaya hai taake logo sahi dikhe */
        .sidebar-header img { width: 80px; filter: none; } 
        
        .sidebar-header h3 { font-size: 14px; margin-top: 10px; color: #ffcc00; }
        .nav-links { padding: 20px 0; }
        .nav-links a { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; font-size: 15px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: #003366; color: white; border-left: 4px solid #ffcc00; }
        
        .logout-section { padding: 20px; position: absolute; bottom: 0; width: 100%; }
        .logout-btn { display: block; padding: 10px; background: #d9534f; color: white; text-align: center; text-decoration: none; border-radius: 4px; font-weight: bold; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 260px; width: 100%; padding-bottom: 50px; }
        header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .form-container { padding: 40px; max-width: 800px; margin: auto; }
        .form-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #003366; }
        .form-card h2 { color: #003366; margin-bottom: 25px; font-size: 22px; border-bottom: 1px solid #eee; padding-bottom: 10px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; color: #555; }
        input[type="text"], input[type="email"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; outline: none; transition: 0.3s; }
        input:focus { border-color: #003366; box-shadow: 0 0 5px rgba(0,34,68,0.1); }

        .btn-save { grid-column: span 2; padding: 12px; background: #003366; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; margin-top: 10px; transition: 0.3s; }
        .btn-save:hover { background: #001a33; transform: translateY(-2px); }

        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="tuf-logo.png" alt="TUF Logo">
        <h3>ADMIN PORTAL</h3>
    </div>
    <div class="nav-links">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="add_student.php" class="active"><i class="fas fa-user-plus"></i> Add New Student</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View All Students</a>
        <a href="stats.php"><i class="fas fa-chart-bar"></i> System Stats</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <header>
        <h2>Register New Student</h2>
        <div class="admin-profile"><i class="fas fa-user-shield"></i> Admin: <?php echo $_SESSION['username']; ?></div>
    </header>

    <div class="form-container">
        <div class="form-card">
            <h2><i class="fas fa-graduation-cap"></i> Student Information</h2>

            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="name" placeholder="e.g. Ali Ahmed" required>
                    </div>
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="ali@example.com" required>
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="0300-1234567">
                    </div>
                    <div class="input-group">
                        <label>Registration Number</label>
                        <input type="text" name="registration_number" placeholder="TUF-2024-001" required>
                    </div>
                    <div class="input-group">
                        <label>Department</label>
                        <input type="text" name="department" placeholder="Computer Science">
                    </div>
                    <div class="input-group">
                        <label>Semester</label>
                        <input type="text" name="semester" placeholder="e.g. 3rd">
                    </div>
                </div>
                <button type="submit" name="add" class="btn-save">
                    <i class="fas fa-save"></i> SAVE STUDENT RECORD
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>