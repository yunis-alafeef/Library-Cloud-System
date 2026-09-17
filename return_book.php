<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'config/db_config.php';

$msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = $_POST['issue_id'];

    $stmt = $conn->prepare("UPDATE issued_books SET returned = 1, return_date = CURDATE() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $issue_id);
        if ($stmt->execute()) {
            $msg = "✅ Book returned successfully!";
        } else {
            $msg = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "❌ Prepare failed: " . $conn->error;
    }
}

// Fetch unreturned books
$issued = $conn->query("
    SELECT issued_books.id, books.title, issued_books.student_name 
    FROM issued_books 
    JOIN books ON issued_books.book_id = books.id 
    WHERE issued_books.returned = 0
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 40px;
        }
        h2 {
            text-align: center;
        }
        form {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        label, select, input {
            display: block;
            width: 100%;
            margin-bottom: 12px;
            font-size: 16px;
        }
        select, input[type=submit] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type=submit] {
            background: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background: #218838;
        }
        .msg {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }
        .msg.error {
            color: red;
        }
        .msg.success {
            color: green;
        }
    </style>
</head>
<body>
    <h2>📘 Return a Book</h2>

    <form method="POST" action="">
        <label>Select Issued Book:</label>
        <select name="issue_id" required>
            <option value="">-- Choose a Book --</option>
            <?php while ($row = $issued->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['title']) ?> (issued to <?= htmlspecialchars($row['student_name']) ?>)
                </option>
            <?php endwhile; ?>
        </select>
        <input type="submit" value="Return Book">
    </form>

    <?php if ($msg): ?>
        <div class="msg <?= str_starts_with($msg, '❌') ? 'error' : 'success' ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>
</body>
</html>
