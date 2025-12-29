<?php
session_start();
// Agar session nahi hai to login.php par bhejo
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('db.php');

$message = "";
$error = "";

// Password Change Logic
if(isset($_POST['update_password'])){
    $current_username = $_SESSION['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($new_password !== $confirm_password){
        $error = "Passwords do not match!";
    } else {
        try {
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                ['username' => $current_username],
                ['$set' => ['password' => $new_password]], 
                ['multi' => false, 'upsert' => false]
            );
            $manager->executeBulkWrite('uf_student_db.users', $bulk);
            $message = "Password updated successfully!";
        } catch (Exception $e) {
            $error = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | TUF Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background: #f4f7f6; min-height: 100vh; }
        
        /* --- SIDEBAR (Updated to match other pages) --- */
        .sidebar { width: 300px; background: #002244; color: white; position: fixed; height: 100%; z-index: 1000; }
        
        .sidebar-header { 
            padding: 25px 15px; 
            display: flex; 
            align-items: center; 
            background: #001a33; 
            border-bottom: 2px solid #ffcc00;
        }
        
        .sidebar-header img { 
            width: 65px; 
            height: auto; 
            filter: none; /* Original colors */
        }
        
        .header-text {
            border-left: 1px solid rgba(255,255,255,0.3);
            padding-left: 15px;
            margin-left: 10px;
        }

        .header-text h2 { 
            color: white; 
            font-size: 13px; 
            font-weight: 700; 
            line-height: 1.2;
            text-transform: uppercase;
        }

        .header-text p {
            color: #ffcc00;
            font-size: 10px;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .nav-links { padding: 20px 0; }
        .nav-links a { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; font-size: 15px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: #003366; color: white; border-left: 5px solid #ffcc00; }

        /* --- MAIN CONTENT (Keeping your settings design) --- */
        .main-content { margin-left: 300px; width: calc(100% - 300px); padding: 40px; }
        .settings-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); max-width: 500px; border-top: 6px solid #27ae60; margin: auto; }
        
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #002244; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; }
        
        .btn-save { background: #27ae60; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; transition: 0.3s; }
        .btn-save:hover { background: #219150; }
        
        .msg { padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f2dede; color: #a94442; border: 1px solid #ebccd1; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="tuf-logo.png" alt="TUF Logo">
        <div class="header-text">
            <h2>The University <br> of Faisalabad</h2>
            <p>ADMIN PORTAL</p>
        </div>
    </div>
    <div class="nav-links">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
        <a href="stats.php"><i class="fas fa-chart-bar"></i> System Stats</a>
        <a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
    </div>
</div>

<div class="main-content">
    <div class="settings-card">
        <h2 style="margin-bottom: 20px; color: #002244;"><i class="fas fa-user-shield"></i> Admin Settings</h2>
        
        <?php if($message): ?> <div class="msg success"><?php echo $message; ?></div> <?php endif; ?>
        <?php if($error): ?> <div class="msg error"><?php echo $error; ?></div> <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Admin Username</label>
                <input type="text" value="<?php echo $_SESSION['username']; ?>" readonly style="background: #f0f0f0; color: #666; cursor: not-allowed;">
            </div>
            <div class="input-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password" required>
            </div>
            <div class="input-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Re-type new password" required>
            </div>
            <button type="submit" name="update_password" class="btn-save">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>