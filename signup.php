<?php
include('db.php'); // Isme $manager mojood hai

if(isset($_POST['signup'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['fullname'];

    try {
        $bulk = new MongoDB\Driver\BulkWrite;
        
        $newUser = [
            '_id' => new MongoDB\BSON\ObjectId,
            'fullname' => $name,
            'username' => $username,
            'password' => $password 
        ];

        $bulk->insert($newUser);
        $manager->executeBulkWrite('uf_student_db.users', $bulk);
        
        echo "<script>alert('Account Created! Ab aap Login kar sakte hain.'); window.location='login.php';</script>";
    } catch (Exception $e) {
        $error = "Registration Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account | TUF</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        
        body {
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: #002244;
            padding: 12px 50px;
            display: flex;
            align-items: center;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo-section img { width: 50px; filter: brightness(0) invert(1); }
        .logo-section h1 { font-size: 16px; letter-spacing: 1px; text-transform: uppercase; }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #e9ecef;
            padding: 20px;
        }

        .signup-box {
            background: #fff;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-top: 5px solid #ffcc00;
        }

        .signup-box h2 { 
            color: #003366; 
            margin-bottom: 10px; 
            font-size: 24px; 
            text-align: center; 
        }

        .signup-box p.subtitle { 
            color: #777; 
            font-size: 14px; 
            text-align: center; 
            margin-bottom: 30px; 
        }

        .input-group { margin-bottom: 20px; }
        .input-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-size: 12px; 
            font-weight: 700; 
            color: #003366; 
            text-transform: uppercase; 
        }

        .input-wrapper { position: relative; }
        .input-wrapper i { 
            position: absolute; 
            left: 12px; 
            top: 14px; 
            color: #aaa; 
        }

        input[type="text"], 
        input[type="password"] { 
            width: 100%; 
            padding: 12px 12px 12px 35px; 
            border: 1px solid #ddd; 
            border-radius: 3px; 
            font-size: 14px; 
            outline: none;
        }

        input:focus { border-color: #003366; box-shadow: 0 0 5px rgba(0,51,102,0.1); }

        input[type="submit"] { 
            width: 100%; 
            padding: 14px; 
            background: #003366; 
            color: #fff; 
            border: none; 
            border-radius: 3px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: 0.3s; 
            font-size: 16px;
        }

        input[type="submit"]:hover { background: #001122; transform: translateY(-1px); }

        .form-footer { 
            margin-top: 25px; 
            text-align: center; 
            font-size: 14px; 
            color: #666; 
        }

        .form-footer a { 
            color: #003366; 
            text-decoration: none; 
            font-weight: bold; 
        }

        .form-footer a:hover { text-decoration: underline; }

        .error { 
            color: #a94442; 
            background: #f2dede; 
            padding: 12px; 
            border-radius: 3px; 
            margin-bottom: 20px; 
            font-size: 13px; 
            border: 1px solid #ebccd1; 
        }

        footer { 
            background: #1a1a1a; 
            color: #999; 
            padding: 20px 50px; 
            font-size: 12px; 
            text-align: center; 
        }
    </style>
</head>
<body>

<header>
    <div class="logo-section">
        <img src="uof_logo.png" alt="TUF Logo">
        <h1>TUF | ADMIN REGISTRATION</h1>
    </div>
</header>

<div class="main-content">
    <div class="signup-box">
        <h2>Create Account</h2>
        <p class="subtitle">Join the Student Record Management System</p>

        <?php if(isset($error)): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Full Name</label>
                <div class="input-wrapper">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="fullname" placeholder="Enter your full name" required>
                </div>
            </div>

            <div class="input-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Choose a unique username" required>
                </div>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Create a strong password" required>
                </div>
            </div>

            <input type="submit" name="signup" value="REGISTER ADMIN ACCOUNT">
            
            <div class="form-footer">
                Already have an account? <a href="index.php">Login here</a>
            </div>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2025 The University of Faisalabad - IT Department</p>
</footer>

</body>
</html>