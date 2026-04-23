<?php
$_SESSION['admin'] = true;
include("config.php");
$sql = "SELECT id, name, email, phone, gender, age, created_at FROM patient ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)):
    echo "ID: " . $row['id'] . "\n";
    echo "Name: " . htmlspecialchars($row['name']) . "\n";
endwhile;
?>
