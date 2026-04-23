<?php
$conn = new mysqli('localhost:3307', 'root', '', 'hospital_db');
$res = $conn->query("ALTER TABLE patient ADD COLUMN dob DATE NULL");
if ($res) echo "Added dob\n";
else echo $conn->error . "\n";
?>