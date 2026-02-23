<?php
if (!isset($_GET['cid'])) {
    header("Location: complaint.html");
    exit();
}

$complaint_id = htmlspecialchars($_GET['cid']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaint Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 450px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            color: green;
        }

        .cid-box {
            background: #eef6ff;
            padding: 12px;
            margin: 15px 0;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Complaint Submitted Successfully</h2>

    <p>Please save your Complaint ID:</p>

    <div class="cid-box">
        <?php echo $complaint_id; ?>
    </div>

    <a href="track.php" class="btn">Track Complaint</a>
    <br><br>
    <a href="complaint.html" class="btn">Submit Another Complaint</a>
</div>

</body>
</html>