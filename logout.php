<?php
session_start();
session_destroy();
header("Location: login.php"); // Ab ye index.php par hi jayega
exit();
?>