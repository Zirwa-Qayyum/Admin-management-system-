<?php
session_start();
include('db.php'); 

if(!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$success = "";
$error = "";

// --- 1. Student ka data fetch karein ---
try {
    $filter = ['_id' => new MongoDB\BSON\ObjectId($id)];
    $query = new MongoDB\Driver\Query($filter);
    $rows = $manager->executeQuery('uf_student_db.students', $query);
    $results = $rows->toArray();
    $student = !empty($results) ? $results[0] : null;

    if(!$student) {
        die("Student not found!");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// --- 2. Update Logic ---
if(isset($_POST['update'])){
    try {
        $bulk = new MongoDB\Driver\BulkWrite;
        $filter = ['_id' => new MongoDB\BSON\ObjectId($id)];
        $newData = [
            '$set' => [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'department' => $_POST['department'],
                'semester' => $_POST['semester'],
                'registration_number' => $_POST['registration_number']
            ]
        ];
        $bulk->update($filter, $newData);
        $result = $manager->executeBulkWrite('uf_student_db.students', $bulk);

        if($result->getModifiedCount() > 0 || $result->getMatchedCount() > 0){
            $success = "Student updated successfully!";
            header("Refresh:1"); 
        } else {
            $error = "No changes made!";
        }
    } catch (Exception $e) {
        $error = "Error updating: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | TUF Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background: #f4f7f6; min-height: 100vh; }

        /* --- SIDEBAR (Same as Dashboard) --- */
        .sidebar { width: 260px; background: #002244; color: white; position: fixed; height: 100%; }
        .sidebar-header { padding: 20px; text-align: center; background: #001a33; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header img { width: 50px; filter: brightness(0) invert(1); }
        .sidebar-header h3 { font-size: 14px; margin-top: 10px; color: #ffcc00; }
        .nav-links { padding: 20px 0; }
        .nav-links a { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; font-size: 15px; }
        .nav-links a:hover, .nav-links a.active { background: #003366; color: white; border-left: 4px solid #ffcc00; }
        .logout-section { padding: 20px; position: absolute; bottom: 0; width: 100%; }
        .logout-btn { display: block; padding: 10px; background: #d9534f; color: white; text-align: center; text-decoration: none; border-radius: 4px; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 260px; width: 100%; }
        header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

        .form-container { padding: 40px; max-width: 800px; margin: auto; }
        .form-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #004a99; }
        .form-card h2 { color: #003366; margin-bottom: 25px; font-size: 22px; display: flex; align-items: center; gap: 10px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; color: #555; }
        input[type="text"], input[type="email"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; outline: none; background: #fafafa; }
        input:focus { border-color: #004a99; background: #fff; }

        .btn-update { grid-column: span 2; padding: 14px; background: #004a99; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-update:hover { background: #003366; transform: translateY(-2px); }

        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .back-link { display: inline-block; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #004a99; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="uof_logo.png" alt="TUF Logo">
        <h3>ADMIN PORTAL</h3>
    </div>
    <div class="nav-links">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <header>
        <h2>Edit Student Record</h2>
        <div class="admin-profile"><i class="fas fa-edit"></i> Editing Mode</div>
    </header>

    <div class="form-container">
        <div class="form-card">
            <h2><i class="fas fa-user-edit"></i> Update Information</h2>

            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="input-group">
                        <label>Student Name</label>
                        <input type="text" name="name" value="<?php echo $student->name; ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo $student->email; ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo $student->phone; ?>">
                    </div>
                    <div class="input-group">
                        <label>Registration Number</label>
                        <input type="text" name="registration_number" value="<?php echo $student->registration_number; ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Department</label>
                        <input type="text" name="department" value="<?php echo $student->department; ?>">
                    </div>
                    <div class="input-group">
                        <label>Semester</label>
                        <input type="text" name="semester" value="<?php echo $student->semester; ?>">
                    </div>
                </div>
                <button type="submit" name="update" class="btn-update">
                    <i class="fas fa-sync-alt"></i> UPDATE RECORD
                </button>
            </form>
            <center><a href="view_students.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Student List</a></center>
        </div>
    </div>
</div>

</body>
</html>