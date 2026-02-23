<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {

    $id = $_POST['id'];

    // Validate ID format (basic protection)
    if (!preg_match("/^CLG-\d+$/", $id)) {
        header("Location: dashboard.php");
        exit();
    }

    // Check if complaint exists
    $check_stmt = $conn->prepare("SELECT complaint_id FROM complaints WHERE complaint_id = ?");
    $check_stmt->bind_param("s", $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {

        $update_stmt = $conn->prepare("UPDATE complaints SET status = 'Resolved' WHERE complaint_id = ?");
        $update_stmt->bind_param("s", $id);
        $update_stmt->execute();
        $update_stmt->close();
    }

    $check_stmt->close();
}

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
header("Location: dashboard.php?page=" . $page);
exit();
?>
