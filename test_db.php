<?php
$conn = new mysqli('localhost:3307', 'root', '', 'hospital_db');
$res = $conn->query('DESCRIBE patient');
while($r = $res->fetch_assoc()) {
    print_r($r);
}
$res = $conn->query('SELECT * FROM patient');
echo "Number of patients: " . $res->num_rows . "\n";
while($r = $res->fetch_assoc()) {
    print_r($r);
}
?>
