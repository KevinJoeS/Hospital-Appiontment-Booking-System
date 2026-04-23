<?php
session_start();
include("../config.php");

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
}

$id = $_GET['id'];

$sql = "DELETE FROM doctor WHERE id='$id'";

mysqli_query($conn,$sql);

header("Location: manage-doctor.php");
?>