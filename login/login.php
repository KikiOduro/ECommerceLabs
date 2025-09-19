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

        input,
        button {
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
        <form id="login-form" method="post" action="#">
            <input id="email" type="email" name="email" placeholder="Email" required><br>
            <input id="password" type="password" name="password" placeholder="Password" required><br>
            <button id="loginBtn" type="submit">Login</button>
        </form>

        <p>No account? <a href="register.php">Register</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            $('#login-form').on('submit', function(e) {
                e.preventDefault();

                let email = $('#email').val().trim();
                let password = $('#password').val();

                if (!email || !password) {
                    Swal.fire('Oops...', 'Email and password are required.', 'error');
                    return;
                }

                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    Swal.fire('Oops...', 'Please enter a valid email address.', 'error');
                    return;
                }

                let $btn = $('#loginBtn').prop('disabled', true).text('Logging in...');

                $.post('../actions/login_customer.php', {
                        email,
                        password
                    }, function(res) {
                        console.log('API response:', res);
                        if (res.status === 'success') {
                            Swal.fire('Success', res.message, 'success').then(() => {
                                window.location.href = '../index.php';
                            });
                        } else {
                            Swal.fire('Oops...', res.message || 'Invalid login.', 'error');
                        }
                    }, 'json')
                    .fail(() => {
                        Swal.fire('Error', 'Server error. Please try again later.', 'error');
                    })
                    .always(() => {
                        $btn.prop('disabled', false).text('Login');
                    });
            });
        });
    </script>

</body>

</html>