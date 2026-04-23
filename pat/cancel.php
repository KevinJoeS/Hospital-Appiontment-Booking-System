<?php
session_start();
include("../config.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: appointments.php");
    exit();
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE appointment SET status = 'Cancelled' WHERE id = ? AND patient_id = ? AND status IN ('Pending', 'Confirmed')"
);

mysqli_stmt_bind_param($stmt, "ii", $id, $patient_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: appointments.php");
exit();
?>
