<?php
session_start();
include('db.php'); 

if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : "";

    if($search != ""){
        $filter = [
            '$or' => [
                ['registration_number' => ['$regex' => $search, '$options' => 'i']],
                ['name' => ['$regex' => $search, '$options' => 'i']],
                ['email' => ['$regex' => $search, '$options' => 'i']],
                ['phone' => ['$regex' => $search, '$options' => 'i']],
                ['department' => ['$regex' => $search, '$options' => 'i']]
            ]
        ];
    } else {
        $filter = [];
    }

    $options = ['sort' => ['_id' => -1]];
    $query = new MongoDB\Driver\Query($filter, $options);
    $students = $manager->executeQuery('uf_student_db.students', $query);

} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Students | TUF Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
    body { display: flex; background: #f4f7f6; min-height: 100vh; }
    .sidebar { width: 300px; background: #002244; color: white; position: fixed; height: 100%; z-index: 1000; }
    .sidebar-header { padding: 25px 15px; display: flex; align-items: center; background: #001a33; border-bottom: 2px solid #ffcc00;}
    .sidebar-header img { width: 65px; height: auto; }
    .header-text { border-left: 1px solid rgba(255,255,255,0.3); padding-left: 15px; margin-left: 10px;}
    .header-text h2 { color: white; font-size: 13px; font-weight: 700; line-height: 1.2; text-transform: uppercase;}
    .header-text p { color: #ffcc00; font-size: 10px; letter-spacing: 1px; font-weight: bold;}
    .nav-links { padding: 20px 0; }
    .nav-links a { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; font-size: 15px; transition: 0.3s; }
    .nav-links a:hover, .nav-links a.active { background: #003366; color: white; border-left: 5px solid #ffcc00; }
    .logout-section { padding: 20px; position: absolute; bottom: 0; width: 100%; }
    .logout-btn { display: block; padding: 10px; background: #d9534f; color: white; text-align: center; text-decoration: none; border-radius: 4px; font-weight: bold; }
    .main-content { margin-left: 300px; width: calc(100% - 300px); }
    header { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .content-body { padding: 30px; }
    .table-card { background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #ffcc00; padding: 20px; }
    .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap:10px;}
    .table-header h2 { color: #003366; font-size: 22px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    thead tr { background: #f8f9fa; border-bottom: 2px solid #eee; }
    th { padding: 15px; text-align: left; font-size: 13px; color: #003366; text-transform: uppercase; }
    td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #444; }
    tr:hover { background-color: #fcfcfc; }
    .btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-block; transition: 0.2s; }
    .edit-btn { background: #004a99; color: white; margin-right: 5px; }
    .edit-btn:hover { background: #003366; transform: scale(1.05); }
    .delete-btn { background: #e74c3c; color: white; }
    .delete-btn:hover { background: #c0392b; transform: scale(1.05); }
    .reg-no-badge { background: #eef2f7; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-weight: bold; color: #333; border: 1px solid #ddd; }

    .search-box input{
        padding:8px 12px;
        border:1px solid #ccc;
        border-radius:5px;
        width:250px;
    }
</style>
</head>

<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="tuf-logo.png">
        <div class="header-text">
            <h2>The University <br> of Faisalabad</h2>
            <p>ADMIN PORTAL</p>
        </div>
    </div>

    <div class="nav-links">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="add_student.php"><i class="fas fa-user-plus"></i> Add New Student</a>
        <a href="view_students.php" class="active"><i class="fas fa-users"></i> View All Students</a>
        <a href="stats.php"><i class="fas fa-chart-pie"></i> System Stats</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    </div>

    <div class="logout-section">
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
<header>
    <h2 style="color: #003366;">Student Records Management</h2>
    <div class="admin-profile" style="font-weight: 600; color: #555;">
        <i class="fas fa-user-shield"></i> Admin: <?php echo $_SESSION['username']; ?>
    </div>
</header>

<div class="content-body">
<div class="table-card">

    <div class="table-header">
        <h2><i class="fas fa-list"></i> Registered Students List</h2>

        <!-- 🔎 SEARCH BAR -->
        <form method="get" class="search-box">
            <input type="text" name="search" placeholder="Search student..." value="<?php echo $search; ?>">
        </form>

        <a href="add_student.php" style="background: #003366; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 13px;">
            <i class="fas fa-plus-circle"></i> Add New Student
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Reg No</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Dept & Semester</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach($students as $student){ ?>
            <tr>
                <td><span class="reg-no-badge"><?php echo $student->registration_number; ?></span></td>
                <td><strong><?php echo $student->name; ?></strong></td>
                <td><?php echo $student->email; ?></td>
                <td><?php echo isset($student->phone) ? $student->phone : '-'; ?></td>
                <td>
                    <small><strong><?php echo isset($student->department) ? $student->department : '-'; ?></strong></small><br>
                    <span style="color:#666; font-size: 11px;"><?php echo isset($student->semester) ? $student->semester : '-'; ?> Semester</span>
                </td>
                <td>
                    <a href="edit_student.php?id=<?php echo $student->_id; ?>" class="btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                    <a href="delete_student.php?id=<?php echo $student->_id; ?>" class="btn delete-btn" onclick="return confirm('Sachi mein delete karna hai?')"><i class="fas fa-trash"></i> Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>
</div>
</div>

</body>
</html>
