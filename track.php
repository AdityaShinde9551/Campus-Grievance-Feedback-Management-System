<?php
include "db_connect.php";

$result_data = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $complaint_id = trim($_POST['complaint_id']);

    if (empty($complaint_id)) {
        $error = "Please enter a Complaint ID.";
    } else {

        $stmt = $conn->prepare("SELECT * FROM complaints WHERE complaint_id = ?");
        $stmt->bind_param("s", $complaint_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $result_data = $result->fetch_assoc();
        } else {
            $error = "No complaint found with this ID.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track Complaint</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
        }

        .container {
            width: 450px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            margin-bottom: 15px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .result-box {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 12px;
            color: white;
            font-weight: bold;
        }

        .pending { background: orange; }
        .resolved { background: green; }

        .error {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        .nav {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Track Your Complaint</h2>

    <form method="POST">
        <label>Enter Complaint ID</label>
        <input type="text" name="complaint_id" required>
        <button type="submit">Check Status</button>
    </form>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($result_data): ?>
        <div class="result-box">
            <p><strong>Complaint ID:</strong> <?php echo htmlspecialchars($result_data['complaint_id']); ?></p>
            <p><strong>College:</strong> <?php echo htmlspecialchars($result_data['college']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($result_data['category']); ?></p>
            <p><strong>Status:</strong>
                <?php if ($result_data['status'] == "Pending"): ?>
                    <span class="badge pending">Pending</span>
                <?php else: ?>
                    <span class="badge resolved">Resolved</span>
                <?php endif; ?>
            </p>
            <p><strong>Date Submitted:</strong> <?php echo htmlspecialchars($result_data['created_at']); ?></p>
        </div>
    <?php endif; ?>

    <div class="nav">
        <a href="complaint.html">Submit New Complaint</a>
    </div>
</div>

</body>
</html>