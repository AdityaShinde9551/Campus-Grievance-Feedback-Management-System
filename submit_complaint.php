<?php
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 🔹 Collect & Trim Input
    $college = trim($_POST['college']);
    $role = trim($_POST['role']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $is_anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $name = $is_anonymous ? NULL : trim($_POST['name']);

    // 🔹 Basic Validation
    if (empty($college) || empty($role) || empty($category) || empty($description)) {
        die("All required fields must be filled.");
    }

    if (!$is_anonymous && empty($name)) {
        die("Name is required if not anonymous.");
    }

    // 🔹 Generate Complaint ID
    $unique_id = "CLG-" . rand(100000, 999999);

    // 🔹 Insert Using Prepared Statement
    $stmt = $conn->prepare("INSERT INTO complaints 
        (complaint_id, college, role, category, description, is_anonymous, name, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");

    $stmt->bind_param(
        "sssssis",
        $unique_id,
        $college,
        $role,
        $category,
        $description,
        $is_anonymous,
        $name
    );

    if ($stmt->execute()) {

        // ✅ Redirect back to styled success page
        header("Location: success.php?cid=" . urlencode($unique_id));
        exit();

    } else {
        echo "Error submitting complaint.";
    }

    $stmt->close();
}
?>