<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'login';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function updateDatabaseForCompletion($taskId, $conn) {
    $taskId = (int)$taskId; 

    if (!isset($_SESSION['tasks'][$taskId])) {
        echo "Task ID not found!";
        return;
    }

    $_SESSION['tasks'][$taskId]['completed'] = true;

    $taskIdToUpdate = $_SESSION['tasks'][$taskId]['id'];
    $sql = "UPDATE tasks_manageant SET completed = true WHERE id = $taskIdToUpdate";

    if (mysqli_query($conn, $sql)) {
        header("Location: PROJET.php?completed=true");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}


if (isset($_GET['index'])) {
    $taskId = $_GET['index'];
    updateDatabaseForCompletion($taskId, $conn);
} else {
    echo "Task ID not found!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark as Completed</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-5">
        <h1 class="mb-4">Mark as Completed</h1>

    
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
