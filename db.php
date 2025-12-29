<?php
// Humne require 'vendor/autoload.php' aur 'use MongoDB\Client' hata diya hai
// Kyunke hum direct driver use karenge

try {
    // MongoDB connection (Native Driver)
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    
    // Database aur Collections ke raste (Paths)
    $dbName = "uf_student_db";
    $usersColl = "$dbName.users";
    $studentsColl = "$dbName.students";

    

} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
?>