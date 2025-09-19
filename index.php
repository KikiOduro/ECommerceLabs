<?php
session_start();

include(__DIR__ . '/settings/db_class.php');
include(__DIR__ . '/settings/db_cred.php');

$db = new db_connection();

if (!$db->db_connect()) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab1</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #333;
        }

        header {
            display: flex;
            padding: 30px 30px;
            background: pink;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
        }

        nav a {
            margin-left: 15px;
            padding: 10px 14px;
            text-decoration: none;
            border: 1px solid black;
            border-radius: 400px;
            color: black;
            font-size: 14px;
            transition: all 0.2s ease-in-out;
        }

        nav a:hover {
            background: black;
            color: white;
        }

        main {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 50vh;
            text-align: center;
        }

        h1 {
            margin-bottom: 10px;
        }

        p {
            color: #555;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <a href="login/login.php">Login</a>
            <a href="login/register.php">Register</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="login/logout.php">Logout</a>
            <?php endif; ?>
        </nav>

    </header>

    <main>
        <h1>Welcome</h1>
        <p>Choose an option from the menu to get started.</p>
    </main>
</body>

</html>