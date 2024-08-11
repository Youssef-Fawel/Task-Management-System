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

if (isset($_GET['index'])) {
    $indexToDelete = $_GET['index'];

    if (isset($_SESSION['tasks'][$indexToDelete])) {
        $taskIdToDelete = $_SESSION['tasks'][$indexToDelete]['id'];

        $sql = "DELETE FROM tasks_manageant WHERE id = '$taskIdToDelete'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
          
            unset($_SESSION['tasks'][$indexToDelete]);
            $_SESSION['tasks'] = array_values($_SESSION['tasks']);

            header("Location: PROJET.php");
            exit();
        } else {
            echo "Error deleting task: " . mysqli_error($conn);
            exit();
        }
    } else {
        echo "Task ID not found!";
        exit();
    }
}

echo "Task ID not found!";
?>
