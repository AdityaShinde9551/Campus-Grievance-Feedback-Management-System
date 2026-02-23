<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $complaint_id = mysqli_real_escape_string($conn, $_POST['id']);
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    // Safety: delete only if resolved
    $check = mysqli_query($conn, 
        "SELECT status FROM complaints WHERE complaint_id='$complaint_id'");
    $row = mysqli_fetch_assoc($check);

    if ($row && $row['status'] === "Resolved") {
        mysqli_query($conn, 
            "DELETE FROM complaints WHERE complaint_id='$complaint_id'");
    }

    header("Location: dashboard.php?page=" . $page);
    exit();
}
?>