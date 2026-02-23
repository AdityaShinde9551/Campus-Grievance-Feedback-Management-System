<?php
session_start();
include "../db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* ===========================
   🔹 Complaint Counters
=========================== */

$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM complaints");
$total_row = mysqli_fetch_assoc($total_result);
$total_complaints = $total_row['total'];

$pending_result = mysqli_query($conn, "SELECT COUNT(*) AS pending FROM complaints WHERE status='Pending'");
$pending_row = mysqli_fetch_assoc($pending_result);
$pending_complaints = $pending_row['pending'];

$resolved_result = mysqli_query($conn, "SELECT COUNT(*) AS resolved FROM complaints WHERE status='Resolved'");
$resolved_row = mysqli_fetch_assoc($resolved_result);
$resolved_complaints = $resolved_row['resolved'];

/* ===========================
   🔹 Search + Filtering Logic
=========================== */

$status_filter = "";
$search_id = "";

if (isset($_GET['search']) && $_GET['search'] != "") {

    $search_id = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM complaints 
            WHERE complaint_id LIKE '%$search_id%' 
            ORDER BY created_at DESC";

} elseif (isset($_GET['status']) && $_GET['status'] != "") {

    $status_filter = mysqli_real_escape_string($conn, $_GET['status']);
    $sql = "SELECT * FROM complaints 
            WHERE status='$status_filter' 
            ORDER BY created_at DESC";

} else {

    $sql = "SELECT * FROM complaints 
            ORDER BY created_at DESC";
}

// 🔹 Pagination Setup

$limit = 5; // number of complaints per page

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// Count total rows for pagination
$count_query = mysqli_query($conn, str_replace("SELECT *", "SELECT COUNT(*) as total", $sql));
$count_row = mysqli_fetch_assoc($count_query);
$total_rows = $count_row['total'];

$total_pages = ceil($total_rows / $limit);


// Add LIMIT to main query
$sql .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eef2f7;
            margin: 0;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
        }
        h2 {
            margin-bottom: 20px;
        }

        .logout {
            float: right;
        }
        .logout a {
            background: #dc3545;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .logout a:hover {
            background: #c82333;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-box {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .total { background: linear-gradient(45deg, #007bff, #0056b3); }
        .pending { background: linear-gradient(45deg, #ff9800, #e68900); }
        .resolved { background: linear-gradient(45deg, #28a745, #1e7e34); }

        .stat-box h2 {
            margin: 10px 0 0 0;
            font-size: 28px;
        }

        .filter-box,
        .search-box {
            margin-bottom: 15px;
        }

        input[type="text"],
        select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .pagination a {
            margin: 5px;
            padding: 6px 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .pagination a:hover {
            background: #0056b3;
        }

    </style>
</head>
<body>

<div class="container">

    <h2>Admin Dashboard</h2>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

    <!-- 🔹 Statistics Section -->
    <div class="stats">
        <div class="stat-box total">
            <h3>Total</h3>
            <h2><?php echo $total_complaints; ?></h2>
        </div>

        <div class="stat-box pending">
            <h3>Pending</h3>
            <h2><?php echo $pending_complaints; ?></h2>
        </div>

        <div class="stat-box resolved">
            <h3>Resolved</h3>
            <h2><?php echo $resolved_complaints; ?></h2>
        </div>
    </div>
    <!-- 🔎 SEARCH SECTION (PASTE HERE) -->
    <div style="margin-bottom:15px;">
        <form method="GET">
            <label>Search by Complaint ID:</label>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_id); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <!-- 🔹 Filter Section -->
    <div class="filter-box">
        <form method="GET">
            <label>Filter by Status:</label>
            <select name="status">
                <option value="">All</option>
                <option value="Pending" <?php if($status_filter=="Pending") echo "selected"; ?>>Pending</option>
                <option value="Resolved" <?php if($status_filter=="Resolved") echo "selected"; ?>>Resolved</option>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Complaint ID</th>
            <th>College</th>
            <th>Name</th>
            <th>College Id</th>
            <th>Role</th>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
            <th>Date</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['complaint_id']); ?></td>
            <td><?php echo htmlspecialchars($row['college']); ?></td>
            <td>
            <?php 
            if($row['is_anonymous'] == 1){
                echo "Anonymous";
            } else {
                echo htmlspecialchars($row['name']);
            }
            ?>
            </td>
            <td>
            <?php 
            if($row['is_anonymous'] == 1){
                echo "Hidden";
            } else {
                echo htmlspecialchars($row['college_id']);
            }
            ?>
            </td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td>
            <?php if($row['status'] == "Pending"): ?>
                <span style="background: orange; color:white; padding:5px 10px; border-radius:12px;">
                    Pending
                </span>
            <?php else: ?>
                <span style="background: green; color:white; padding:5px 10px; border-radius:12px;">
                    Resolved
                </span>
            <?php endif; ?>
            </td>

            <td>

            <?php if($row['status'] == "Pending"): ?>

                <!-- Mark Resolved Button -->
                <form method="POST" action="update_status.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['complaint_id']); ?>">
                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                    <button type="submit" 
                        style="background: green; color:white; padding:5px 10px; border:none; border-radius:4px; cursor:pointer;">
                        Mark Resolved
                    </button>
                </form>

            <?php else: ?>

                <!-- Delete Button (Only for Resolved Complaints) -->
                <form method="POST" action="delete_complaint.php" style="display:inline;"
                      onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['complaint_id']); ?>">
                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                    <button type="submit" 
                        style="background: red; color:white; padding:5px 10px; border:none; border-radius:4px; cursor:pointer;">
                        Delete
                    </button>
                </form>

            <?php endif; ?>

            </td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php endwhile; ?>

    </table>
<div style="margin-top:20px; text-align:center;">
    <?php for($i = 1; $i <= $total_pages; $i++): ?>

        <a href="?page=<?php echo $i; ?>
        <?php echo $status_filter ? '&status='.$status_filter : ''; ?>
        <?php echo $search_id ? '&search='.$search_id : ''; ?>"
        
        style="
            margin:5px;
            padding:6px 12px;
            text-decoration:none;
            border-radius:6px;
            background: <?php echo ($page == $i) ? '#333' : '#007bff'; ?>;
            color:white;
        ">
            <?php echo $i; ?>
        </a>

    <?php endfor; ?>
</div>
</div>

</body>
</html>
