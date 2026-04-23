<?php
session_start();
include("../config.php");

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Optionally delete related appointments first if no cascade
    $sql1 = "DELETE FROM appointment WHERE patient_id = $id";
    mysqli_query($conn, $sql1);
    
    $sql2 = "DELETE FROM patient WHERE id = $id";
    mysqli_query($conn, $sql2);
}

header("Location: manage-patients.php");
exit();
?>
