<?php
session_start();
include('db.php'); // $manager yahan se aa raha hai

if(!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        // Native Driver mein delete karne ka sahi tareeqa
        $bulk = new MongoDB\Driver\BulkWrite;
        
        // Filter: Kis ID ko delete karna hai
        $filter = ['_id' => new MongoDB\BSON\ObjectId($id)];
        
        // Delete command add karein (limit 1 matlab sirf ek record)
        $bulk->delete($filter, ['limit' => 1]);

        // Execute karein: Database.Collection
        $manager->executeBulkWrite('uf_student_db.students', $bulk);

        // Delete hone ke baad wapas list par bhej dein
        header("Location: view_students.php?msg=deleted");
        exit();

    } catch (Exception $e) {
        die("Error deleting student: " . $e->getMessage());
    }
} else {
    header("Location: view_students.php");
    exit();
}
?>