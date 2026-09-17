<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config/db_config.php';

$result = $conn->query("SELECT * FROM books ORDER BY added_on DESC");
if (!$result) {
    die("❌ SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library - View Books</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-bar input {
            padding: 8px;
            width: 60%;
            max-width: 400px;
            font-size: 16px;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f1f5ff;
        }
        tr:hover {
            background-color: #d8ecff;
        }
        .empty {
            text-align: center;
            margin-top: 50px;
            font-style: italic;
            color: #777;
        }
        @media (max-width: 600px) {
            table, th, td {
                font-size: 14px;
            }
            .search-bar input {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <h2>📚 Library Books</h2>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="🔍 Search by title, author, or genre...">
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>📖 Title</th>
                    <th>✍️ Author</th>
                    <th>🎯 Genre</th>
                    <th>🕓 Added On</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= htmlspecialchars($row['genre']) ?></td>
                        <td><?= htmlspecialchars($row['added_on']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">No books added yet.</p>
    <?php endif; ?>

    <script>
        const searchInput = document.getElementById("searchInput");
        searchInput.addEventListener("keyup", function () {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll("table tbody tr");

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    </script>
</body>
</html>
