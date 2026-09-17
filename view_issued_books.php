<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config/db_config.php';

$result = $conn->query("
    SELECT ib.id, b.title, ib.student_name, ib.issue_date, ib.return_date, ib.returned
    FROM issued_books ib
    JOIN books b ON ib.book_id = b.id
    ORDER BY ib.issue_date DESC
");

if (!$result) {
    die("❌ SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issued Books Log</title>
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
        .top-bar {
            width: 90%;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .top-bar input {
            width: 60%;
            max-width: 400px;
            padding: 8px;
            font-size: 16px;
        }
        .top-bar a {
            padding: 10px 15px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
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
        .returned {
            color: green;
            font-weight: bold;
        }
        .issued {
            color: #e67e22;
            font-weight: bold;
        }
        .empty {
            text-align: center;
            font-style: italic;
            color: #777;
        }
        @media (max-width: 600px) {
            table, th, td {
                font-size: 13px;
            }
            .top-bar {
                flex-direction: column;
                gap: 10px;
            }
            .top-bar input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h2>📋 Issued Books Log</h2>

    <div class="top-bar">
        <input type="text" id="searchInput" placeholder="🔍 Search title, student, or status...">
        <a href="export_issued_logs.php">⬇️ Download CSV</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>📘 Title</th>
                    <th>👩‍🎓 Student</th>
                    <th>📅 Issue Date</th>
                    <th>📆 Return Date</th>
                    <th>📌 Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= $row['issue_date'] ?></td>
                        <td><?= $row['return_date'] ?? '—' ?></td>
                        <td class="<?= $row['returned'] ? 'returned' : 'issued' ?>">
                            <?= $row['returned'] ? 'Returned' : 'Issued' ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">No issued books found.</p>
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
