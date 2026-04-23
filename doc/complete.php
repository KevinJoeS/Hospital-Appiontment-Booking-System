<?php
include("../config.php");

$id = $_GET['id'];

$sql = "UPDATE appointment SET status='Completed' WHERE id='$id'";

mysqli_query($conn,$sql);

header("Location: dashboard.php");
?>
