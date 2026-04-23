<?php
session_start();
include("../config.php");

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $sql = "DELETE FROM appointment WHERE id = $id";
    mysqli_query($conn, $sql);
}

header("Location: view-appiontments.php");
exit();
?>
