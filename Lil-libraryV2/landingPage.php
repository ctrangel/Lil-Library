<?php
session_start();

//Securing Connection
$https = filter_input(INPUT_SERVER, 'HTTPS');

if (!$https)
{
    $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
    $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');

    $url = 'https://' . $host . $uri;
    header("Location: " . $url);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap');


*, *:before, *:after {
  box-sizing: border-box;
}

html {
  overflow-y: scroll; 
}

body {
  background: #f4f1ea;
  font-family: 'Merriweather', serif !important;
  background-image: url("/Lil-libraryV2/media/hfn210qr.bmp"); /* update accordingly */
  color: #000000;
  margin: 0;
  padding: 0;
}

a {
  text-decoration: none;
  color: #c6c8bb;
  transition: 0.5s ease;
}
a:hover {
  color: #c6c8bb73;
}

.form {
  background: rgb(76, 44, 19);
  padding: 40px;
  max-width: 600px;
  margin: 40px auto;
  border-radius: 10px;
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2); 
}

.tab-group {
  list-style: none;
  padding: 0;
  margin: 0 0 40px 0;
}
.tab-group:after {
  content: "";
  display: table;
  clear: both;
}
.tab-group li a {
  display: block;
  text-decoration: none;
  padding: 15px;
  background: rgba(222, 224, 210, 0.5);
  color: #000000;
  font-size: 20px;
  float: left;
  width: 50%;
  text-align: center;
  cursor: pointer;
  transition: 0.5s ease;
}
.tab-group li a:hover {
  background: #dee0d2;
  color: #000000;
}
.tab-group .active a {
  background: #c6c8bb;
  color: #000000;
}

.tab-content > div:last-child {
  display: none;
}

h1 {
  text-align: center;
  color: #000000;
  font-weight: 300;
  margin: 0 0 40px;
  background-color: #c6c8bb;
  padding: 10px;
  border-radius: 10px;
}

label {

  color: white;
  font-size: 22px;
}

input {
    font-family: "Merriweather", serif;
}

input, textarea {
  font-size: 22px;
  
  display: block;
  width: 100%;
  height: 100%;
  padding: 5px 10px;
  background: none;
  border: 1px solid #dee0d2;
  color: #ffffff;
  transition: all 0.5s ease;
}
input:focus, textarea:focus {
  outline: 0;
  border-color: #c6c8bb;
  background-color: #00000036;
}

textarea {
  border: 2px solid #dee0d2;
  resize: vertical;

}

.field-wrap {
  position: relative;
  margin-bottom: 40px;
}

.top-row:after {
  content: "";
  display: table;
  clear: both;
}

.top-row > div {
  float: left;
  width: 48%;
  margin-right: 4%;
}
.top-row > div:last-child {
  margin-right: 0;
}

.button {
  border: 0;
  font-family: 'Merriweather', serif;
  outline: none;
  border-radius: 10px;
  padding: 15px 0;
  font-size: 1.5rem;
  background: #c6c8bb;
  color: #000000;
  transition: all 0.5s ease;
  -webkit-appearance: none;
}
.button:hover, .button:focus {
  background: #c6c8bb73;
}

.button-block {
  display: block;
  width: 100%;
}

.forgot {
  margin-top: -20px;
  text-align: right;
  color: #000000;
}

.forgot a {
  color: #c6c8bb;
}

.forgot a:hover {
  color: #c6c8bb73;
}
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lil Library | Log In</title>
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV2/styles/loginStyles.css"> <!-- UPDATE ACCORDINGLY -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
     <script>
         //Check if the user is already logged in (via localStorage) and redirect to dashboard
        const token = localStorage.getItem('authToken');
        if (token) {
            window.location.href = 'dashboard.php';
        }
    </script>
    <?php
    // check if the user is already logged in and redirect to dashboard php version type beat
   // session_start();
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
            <li class="tab"><a href="#admin">Admin Log In</a></li>
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

             <!-- User Login -->

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
            
            <!-- Admin Login -->

            <div id="admin">
                <h1>Admin Login</h1>
                <form onsubmit="admin(event)">
                    <div class="field-wrap">
                        <label>Username</label>
                        <input type="text" id="admin_username" required autocomplete="off" />
                    </div>

                    <div class="field-wrap">
                        <label>Password</label>
                        <input type="password" id="admin_password" required autocomplete="off" />
                    </div>

                    <div class="field-wrap" id="login_usertype">
                     

                    </div>


                    <button type="submit" class="button button-block">Admin Log In</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function login(event) {
            event.preventDefault();
            const username = document.getElementById('login_username').value;
            const password = document.getElementById('login_password').value;

            fetch('http://localhost/Lil-Library/Lil-libraryV2/login.php', {
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

        function admin(event) {
            event.preventDefault();
            const adminUsername = document.getElementById('admin_username').value;
            const adminPassword = document.getElementById('admin_password').value;
            const usertype = document.getElementById('login_usertype').v;

                
            fetch('http://localhost/Lil-Library/Lil-libraryV2/adminLogin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        adminUsername,
                        adminPassword
                       // usertype
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
                            window.location.href = 'adminDashboard.php';
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