<?php
session_start();
// //Securing Connection
// $https = filter_input(INPUT_SERVER, 'HTTPS');

// if (!$https)
// {
//     $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
//     $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');

//     $url = 'https://' . $host . $uri;
//     header("Location: " . $url);
//     exit();
// }
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lil Library | Log In</title>
    <link rel="stylesheet" href="/Lil-libraryV3/styles/loginStyles.css"> <!-- UPDATE ACCORDINGLY -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
    <!-- <script>
        // Check if the user is already logged in (via localStorage) and redirect to dashboard
        const token = localStorage.getItem('authToken');
        if (token) {
            window.location.href = 'dashboard.php';
        }
    </script> -->
    <?php
    // check if the user is already logged in and redirect to dashboard php version type beat

    if (isset($_SESSION['username'])) {
        header('Location: dashboard.php');
    }
    ?>
</head>

<body>
 
    <div class="form">
        <ul class="tab-group">
            <li class="tab active"><a href="#signup">Sign Up</a></li>
            <li class="tab"><a href="#login">Log In</a></li>
        </ul>

        <div class="tab-content">
            <div id="signup">
                <h1>Sign Up</h1>
                <form onsubmit="signup(event)">
                    <div class="field-wrap">
                        <label>Username</label>
                        <input type="text" id="signup_username" required autocomplete="off" />
                    </div>

                    <div class="field-wrap">
                        <label>Set A Password</label>
                        <input type="password" id="signup_password" required autocomplete="off" />
                    </div>

                    <button type="submit" class="button button-block">Get Reading</button>
                </form>
            </div>

            <div id="login">
                <h1>Login</h1>
                <form onsubmit="login(event)">
                    <div class="field-wrap">
                        <label>Username</label>
                        <input type="text" id="login_username" required autocomplete="off" />
                    </div>

                    <div class="field-wrap">
                        <label>Password</label>
                        <input type="password" id="login_password" required autocomplete="off" />
                    </div>

                    <p class="forgot"><a href="contactAdmin.php">Forgot Password?</a></p>

                    <button type="submit" class="button button-block">Log In</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function login(event) {
            event.preventDefault();
            const username = document.getElementById('login_username').value;
            const password = document.getElementById('login_password').value;

            fetch('http://localhost/Lil-libraryV3/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username,
                        password
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful',
                            text: 'You will be redirected shortly.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'dashboard.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: data.message || 'Invalid username or password.',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong during login. Please try again.',
                    });
                });
        }


        function signup(event) {
            event.preventDefault();
            const username = document.getElementById('signup_username').value;
            const password = document.getElementById('signup_password').value;

            fetch('http://localhost:4000/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username,
                        password,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: 'You can now log in.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            document.getElementById('signup_username').value = '';
                            document.getElementById('signup_password').value = '';
                            document.querySelector('.tab a[href="#login"]').click();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: data.message || 'Please try again.',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong during registration. Please try again.',
                    });
                });
        }

        $(document).ready(function() {
            $('.tab a').on('click', function(e) {
                e.preventDefault();

                $(this).parent().addClass('active');
                $(this).parent().siblings().removeClass('active');

                let target = $(this).attr('href');
                $('.tab-content > div').hide();
                $(target).fadeIn(600);
            });

            // By default, show the signup tab
            $('#signup').show();
            $('#login').hide();
        });
    </script>
</body>

</html>