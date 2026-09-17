<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Simple hardcoded login — you can replace this with DB check later
    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "❌ Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f2f2f2;
            text-align: center;
            padding-top: 80px;
        }
        form {
            background: #fff;
            padding: 30px;
            max-width: 350px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 12px 0;
            font-size: 15px;
        }
        button {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            font-weight: bold;
            border-radius: 4px;
        }
        .error {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <h2>🔐 Admin Login</h2>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>

</body>
</html>
