<?php
session_start();

include(__DIR__ . '/settings/db_class.php');
include(__DIR__ . '/settings/db_cred.php');
include(__DIR__ . '/settings/core.php'); 


if (!isLoggedIn()) {
    header("Location: login/login.php");
    exit;
}


$db = new db_connection();
if (!$db->db_connect()) {
    die("Database connection failed: " . mysqli_connect_error());
}

$logged_in = isLoggedIn();
$is_admin  = isAdmin();
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
            justify-content: space-between;
            align-items: center;
        }

        nav a,
        nav form button {
            margin-left: 15px;
            padding: 10px 14px;
            text-decoration: none;
            border: 1px solid black;
            border-radius: 400px;
            color: black;
            font-size: 14px;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        nav a:hover,
        nav form button:hover {
            background: black;
            color: white;
        }

        nav form {
            display: inline;
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
        <div><strong>Lab1</strong></div>
        <nav>
            <?php if (!$logged_in): ?>
                <a href="login/login.php">Login</a>
                <a href="login/register.php">Register</a>
            <?php else: ?>
                <?php if ($is_admin): ?>
                    <a href="admin/category.php">Category</a>
                <?php endif; ?>
                <form action="login/logout.php" method="post">
                    <button type="submit">Logout</button>
                </form>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h1>Welcome</h1>
        <p>
            <?php if ($logged_in): ?>
                You are logged in as <?php echo htmlspecialchars($_SESSION['user_name']); ?>.
            <?php else: ?>
                Choose an option from the menu to get started.
            <?php endif; ?>
        </p>
    </main>
</body>

</html>
