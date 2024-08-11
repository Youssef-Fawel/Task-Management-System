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

function getTaskById($taskId) {
    return isset($_SESSION['tasks'][$taskId]) ? $_SESSION['tasks'][$taskId] : null;
}

if (isset($_GET['index'])) {
    $taskId = $_GET['index'];
    $task = getTaskById($taskId);

    if (!$task) {
        echo "Task ID not found!";
        exit();
    }

  
    if (isset($_POST['modifyTask'])) {
        $newTask = $_POST['task'];
        $newDueDate = $_POST['dueDate'];
        $newPriority = isset($_POST['priority']) ? $_POST['priority'] : '';

        $_SESSION['tasks'][$taskId]['task'] = $newTask;
        $_SESSION['tasks'][$taskId]['due_date'] = $newDueDate;
        $_SESSION['tasks'][$taskId]['priority'] = $newPriority;

        
        $taskIdToUpdate = $_SESSION['tasks'][$taskId]['id'];
        $sql = "UPDATE tasks_manageant SET task = '$newTask', due_date = '$newDueDate', priority = '$newPriority' WHERE id = '$taskIdToUpdate'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            
            header("Location: PROJET.php");
            exit();
        } else {
            echo "Error updating task: " . mysqli_error($conn);
            exit();
        }
    }
} else {
    echo "Task ID not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="/Projet test/modify.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-5">
        <h1 class="mb-4">Modify Task</h1>

        <form action="" method="post">
            <div class="form-group">
                <label for="task">Task:</label>
                <input type="text" class="form-control" name="task" value="<?= isset($task['task']) ? $task['task'] : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="dueDate">Due Date:</label>
                <input type="date" class="form-control" name="dueDate" value="<?= isset($task['due_date']) ? $task['due_date'] : '' ?>">
            </div>
            <div class="form-group">
                <label for="priority">Priority:</label>
                <select class="form-control" name="priority">
                    <option value="High" <?= isset($task['priority']) && $task['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                    <option value="Medium" <?= isset($task['priority']) && $task['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="Low" <?= isset($task['priority']) && $task['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit" name="modifyTask">Modify Task</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
