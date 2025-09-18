<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background: #f9f9f9;
      text-align: center;
    }
    .box {
      padding: 30px;
    }
    input, button {
      margin: 8px 0;
      padding: 8px;
      width: 100%;
    }
    button {
      background: pink;
      color: #fff;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background: black;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Login</h2>
    <form method="post" action="#">
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Login</button>
    </form>
    <p>No account? <a href="register.php">Register</a></p>
  </div>
</body>
</html>
