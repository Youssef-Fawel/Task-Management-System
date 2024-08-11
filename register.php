<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'login';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $checkUsernameQuery = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $checkUsernameQuery);

    if (mysqli_num_rows($result) > 0) {
        $registrationMessage = "Username already exists. Please choose a different username.";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

        if (mysqli_query($conn, $sql)) {
            $registrationMessage = "Registration successful. <a href='login.php' class='alert-link'>Login</a>.";
        } else {
            $registrationMessage = "Error: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="/Projet test/register.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
        }
        .card {
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h1 class="mb-0">Register</h1>
            </div>
            <div class="card-body">
                <?php if (isset($registrationMessage)) : ?>
                    <?php if (strpos($registrationMessage, 'successful') !== false) : ?>
                        <div class="alert alert-success" role="alert">
                    <?php else : ?>
                        <div class="alert alert-danger" role="alert">
                    <?php endif; ?>
                        <?php echo $registrationMessage; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit" name="register">Register</button>
                </form>
                <div class="mt-3 text-center">
                    Already have an account? <a href="login.php">Login here.</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
