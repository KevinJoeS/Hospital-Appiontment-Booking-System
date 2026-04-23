<?php
session_start();
include("../config.php");

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = (int) $_SESSION['doctor_id'];
$appointment_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$allowed_statuses = ['Confirmed', 'Declined'];

if ($appointment_id <= 0 || !in_array($status, $allowed_statuses, true)) {
    header("Location: dashboard.php?status=error");
    exit();
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE appointment SET status = ? WHERE id = ? AND doctor_id = ? AND status = 'Pending'"
);

if (!$stmt) {
    header("Location: dashboard.php?status=error");
    exit();
}

mysqli_stmt_bind_param($stmt, "sii", $status, $appointment_id, $doctor_id);
$updated = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
mysqli_stmt_close($stmt);

$redirect_status = 'error';
if ($updated) {
    $redirect_status = $status === 'Confirmed' ? 'accepted' : 'declined';
}

header("Location: dashboard.php?status=" . $redirect_status);
exit();
?>
