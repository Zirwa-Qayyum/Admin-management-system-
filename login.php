<?php
session_start();
include('db.php');

$error = ""; 

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $filter = ['username' => $username, 'password' => $password];
        $query = new MongoDB\Driver\Query($filter);
        $rows = $manager->executeQuery('uf_student_db.users', $query);
        $results = $rows->toArray();
        $user = !empty($results) ? $results[0] : null;

        if($user){
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Access Denied: Invalid Admin Credentials!";
        }
    } catch (Exception $e) {
        $error = "System Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | TUF Student Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* AAPKI ORIGINAL STYLING */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f0f2f5; display: flex; flex-direction: column; min-height: 100vh; }

        header {
            background: #002244;
            padding: 12px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .logo-section { display: flex; align-items: center; gap: 15px; }
        
        /* LOGO FIX: Filter hata diya taake white na ho */
        .logo-section img { width: 50px; filter: none; } 
        
        .logo-section h1 { font-size: 16px; letter-spacing: 1px; }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('https://www.tuf.edu.pk/images/bg-pattern.png');
        }
        .login-box {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-top: 5px solid #ffcc00;
        }
        .login-box i.admin-icon { font-size: 50px; color: #003366; margin-bottom: 15px; }
        .login-box h2 { color: #333; margin-bottom: 5px; font-size: 22px; text-align: center; }
        .login-box p { color: #777; font-size: 13px; text-align: center; margin-bottom: 25px; }
        
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: #003366; text-transform: uppercase; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 12px; top: 14px; color: #aaa; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 12px 12px 12px 35px; border: 1px solid #ddd; border-radius: 3px; font-size: 14px;
        }
        
        input[type="submit"] {
            width: 100%; padding: 14px; background: #003366; color: #fff; border: none;
            border-radius: 3px; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        input[type="submit"]:hover { background: #001122; }
        
        .error { color: #a94442; background: #f2dede; padding: 12px; border-radius: 3px; margin-bottom: 20px; font-size: 13px; border: 1px solid #ebccd1; }

        footer { background: #1a1a1a; color: #999; padding: 20px 50px; font-size: 12px; text-align: center; }
        .footer-links a { color: #ffcc00; text-decoration: none; margin: 0 10px; }
    </style>
</head>
<body>

<header>
    <div class="logo-section">
        <img src="tuf-logo.png" alt="TUF Logo">
        <h1>TUF | ADMIN MANAGEMENT SYSTEM</h1>
    </div>
    <div class="header-right">
        <small><i class="fas fa-lock"></i> Secure Access</small>
    </div>
</header>

<div class="main-content">
    <div class="login-box">
        <center><i class="fas fa-user-shield admin-icon"></i></center>
        <h2>Admin Login</h2>
        <p>Enter your credentials to manage students</p>

        <?php if(!empty($error)): ?>
            <div class="error"><i class="fas fa-ban"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Admin Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
            </div>
            <div class="input-group">
                <label>Access Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
            </div>
            <input type="submit" name="login" value="AUTHORIZED LOGIN">
        </form>
    </div>
</div>

<footer>
    <div class="footer-links">
        <a href="#">Support Desk</a> | <a href="#">System Status</a> | <a href="#">IT Policy</a>
    </div>
    <p style="margin-top:10px;">&copy; 2025 The University of Faisalabad - IT Department</p>
</footer>

</body>
</html>