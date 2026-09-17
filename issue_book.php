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
    $book_id = $_POST['book_id'];
    $student = trim($_POST['student']);

    if ($book_id && $student) {
        $stmt = $conn->prepare("INSERT INTO issued_books (book_id, student_name) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("is", $book_id, $student);

            if ($stmt->execute()) {
                $msg = "✅ Book issued to $student.";
            } else {
                $msg = "❌ Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $msg = "❌ Prepare failed: " . $conn->error;
        }
    } else {
        $msg = "⚠️ Please select a book and enter a student name.";
    }
}

// Fetch book list
$books = $conn->query("SELECT id, title FROM books");
if (!$books) {
    die("❌ SQL error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Book</title>
    <style>
        body { font-family: Arial; margin: 40px; background-color: #f9f9f9; }
        h2 { text-align: center; }
        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        label, select, input {
            display: block;
            width: 100%;
            margin-bottom: 12px;
            font-size: 16px;
        }
        select, input[type=text] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type=submit] {
            background: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        input[type=submit]:hover {
            background-color: #0056b3;
        }
        .msg {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            color: green;
        }
        .msg.error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>📚 Issue a Book</h2>

    <form method="POST" action="">
        <label>Select Book:</label>
        <select name="book_id" required>
            <option value="">-- Choose a Book --</option>
            <?php while ($row = $books->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Student Name:</label>
        <input type="text" name="student" required>

        <input type="submit" value="Issue Book">
    </form>

    <?php if ($msg): ?>
        <p class="msg <?= str_starts_with($msg, '❌') || str_starts_with($msg, '⚠️') ? 'error' : '' ?>">
            <?= htmlspecialchars($msg) ?>
        </p>
    <?php endif; ?>
</body>
</html>
