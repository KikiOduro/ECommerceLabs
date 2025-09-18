<!DOCTYPE html>
<html lang="en">
<head>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
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
      width: 300px;
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
    <h2>Register</h2>
    <form method="post" id="register-form" enctype="multipart/form-data">
      <input type="text" id="name" name="name" placeholder="Full Name" required><br>
      <input type="email" id="email" name="email" placeholder="Email" required><br>
      <input type="password" id="password" name="password" placeholder="Password" required><br>
      <input type="text" id="country" name="country" placeholder="Country" required><br>
      <input type="text" id="city" name="city" placeholder="City" required><br>
      <input type="text" id="phone_number" name="phone_number" placeholder="Contact Number" required><br>
      <input type="file" id="image" name="image"><br>
      <input type="hidden" id="role" name="role" value="2">
      <button type="submit" id="registerBtn">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>
  <script src="../js/register.js">
  </script>
</body>
</html>
