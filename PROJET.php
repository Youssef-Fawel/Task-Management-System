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

$welcomeMessage = '';
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = array();
}
if (isset($_POST['addTask'])) {
    $task = $_POST['task'];
    $dueDate = $_POST['dueDate'];
    $priority = isset($_POST['priority']) ? $_POST['priority'] : '';
    $userId = $_SESSION['user_id'];

    if (!empty($dueDate) && !DateTime::createFromFormat('Y-m-d', $dueDate)) {
        echo "<div class='alert alert-danger mt-4' role='alert'>
                Invalid due date format. Please use YYYY-MM-DD.
              </div>";
    } else {
        $sql = "INSERT INTO tasks_manageant (user_id, task, due_date, priority, completed) 
                VALUES (?, ?, ?, ?, false)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isss", $userId, $task, $dueDate, $priority);

            if (mysqli_stmt_execute($stmt)) {
            } else {
                echo "Error executing statement: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['index'])) {
    $index = $_GET['index'];
    if (isset($_SESSION['tasks'][$index])) {
        $taskName = $_SESSION['tasks'][$index]['task'];
        unset($_SESSION['tasks'][$index]);

        echo "<div class='alert alert-warning mt-4' role='alert'>
                Task '$taskName' has been deleted.
              </div>";

        echo '<script>';
        echo 'removeReminder(' . $index . ');';
        echo '</script>';
    }
}

if (isset($_POST['logout'])) {
    $userId = $_SESSION['user_id'];
    $tasks = $_SESSION['tasks'];

    foreach ($tasks as $task) {
        $taskText = mysqli_real_escape_string($conn, $task['task']);
        $dueDate = $task['due_date'];
        $priority = isset($task['priority']) ? $task['priority'] : '';
        $completed = $task['completed'];

        $completed = $completed ? 1 : 0;
        $checkSql = "SELECT * FROM tasks_manageant WHERE user_id = '$userId' AND task = '$taskText' AND due_date = '$dueDate'";
        $checkResult = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkResult) > 0) {
            $updateSql = "UPDATE tasks_manageant SET priority = '$priority', completed = '$completed' WHERE user_id = '$userId' AND task = '$taskText' AND due_date = '$dueDate'";
            mysqli_query($conn, $updateSql);
        } else {
            $insertSql = "INSERT INTO tasks_manageant (user_id, task, due_date, priority, completed) 
                    VALUES ('$userId', '$taskText', '$dueDate', '$priority', '$completed')";
            mysqli_query($conn, $insertSql);
        }
    }

    $_SESSION['tasks'] = array();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = array();
}

$sql = "SELECT * FROM tasks_manageant WHERE user_id = {$_SESSION['user_id']}";
$result = mysqli_query($conn, $sql);

if ($result) {
    $_SESSION['tasks'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="/Projet test/task.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">


</head>
<body onload="hideSuccessMessage()">

    <div class="container mt-5">
        <h1 class="mb-4"><i class="fa fa-tasks mr-2"></i> Task Management System</h1>

        <?php echo "<p class='mt-3'>$welcomeMessage</p>"; ?>

        <?php
        echo '<script>';
        echo 'function showReminder(message, index) {';
        echo 'var alertDiv = document.createElement("div");';
        echo 'alertDiv.id = "reminder_" + index;';
        echo 'alertDiv.className = "alert alert-info mt-2";';
        echo 'alertDiv.setAttribute("role", "alert");';
        echo 'alertDiv.innerHTML = message;';
        echo 'document.getElementById("addTaskForm").appendChild(alertDiv);';
        echo 'setTimeout(function() {';
        echo 'alertDiv.remove();';
        echo '}, 10000);';
        echo '}';
        echo '</script>';
        ?>

        <?php
        echo '<script>';
        echo 'function removeReminder(index) {';
        echo 'var reminderElement = document.getElementById("reminder_" + index);';
        echo 'if (reminderElement) {';
        echo 'reminderElement.remove();';
        echo '}';
        echo '}';
        echo '</script>';
        ?>

        <?php
        echo '<script>';
        echo 'function hideSuccessMessage() {';
        echo 'var successMessage = document.getElementById("successMessage");';
        echo 'if (successMessage) {';
        echo 'setTimeout(function() {';
        echo 'successMessage.style.display = "none";';
        echo '}, 5000);';
        echo '}';
        echo '}';
        echo '</script>';
        ?>

        <form id="addTaskForm" action="" method="post">
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="task">Task:</label>
                    <input type="text" class="form-control" name="task" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="dueDate">Due Date:</label>
                    <input type="date" class="form-control" name="dueDate" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="priority">Priority:</label>
                    <select class="form-control" name="priority">
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary" type="submit" name="addTask"><i class="fa fa-plus"></i> Add Task</button>
        </form>

        
        <?php
        function getPriorityEmoji($priority) {
            switch ($priority) {
                case 'High':
                    return '🔴 High';
                case 'Medium':
                    return '🟠 Medium';
                case 'Low':
                    return '🟢 Low';
                default:
                    return '';
            }
        }
        
        
        if (!empty($_SESSION['tasks'])) {
            $totalTasks = count($_SESSION['tasks']);
            $completedTasks = array_reduce($_SESSION['tasks'], function ($carry, $task) {
                return $carry + ($task['completed'] ? 1 : 0);
            }, 0);

            echo "<p class='mt-3'><strong>Total Tasks:</strong> $totalTasks</p>";
            echo "<p class='mt-3'><strong>Completed Tasks:</strong> $completedTasks";

            echo "<table class='table mt-4 table-bordered table-hover'>";
            echo "<thead class='thead-light'>
                    <tr>
                        <th scope='col'>Task</th>
                        <th scope='col'>Due Date</th>
                        <th scope='col'>Priority</th>
                        <th scope='col'>Status</th>
                        <th scope='col'>Actions</th>
                    </tr>
                  </thead><tbody>";

            foreach ($_SESSION['tasks'] as $index => $task) {
                $status = $task['completed'] ? '✅ Yes' : '❌ No';
                $priority = isset($task['priority']) ? getPriorityEmoji($task['priority']) : '';

                echo "<tr>
                        <td>{$task['task']}</td>
                        <td>{$task['due_date']}</td>
                        <td>$priority</td>
                        <td>$status</td>
                        <td>
                            <a class='btn btn-info btn-sm mr-2' href='modify.php?index=$index'><i class='fa fa-edit'></i> Modify</a>
                            <a class='btn btn-success btn-sm mr-2' href='completed.php?index=$index'><i class='fa fa-check'></i> Mark as Completed</a>
                            <a class='btn btn-danger btn-sm' href='delete.php?index=$index'><i class='fa fa-trash'></i> Delete</a>
                        </td>
                      </tr>";
            }

            echo "</tbody></table>";

        } else {
            echo "<p class='mt-4'><i class='fa fa-info-circle mr-2'></i> No tasks added yet!</p>";
        }
        ?>
        <form action="" method="post">
            <button class="btn btn-danger mt-4" type="submit" name="logout"><i class="fa fa-sign-out"></i> Logout</button>
        </form>

        <div id="dateTime" class="mt-3" style="position: fixed; bottom: 0; right: 0;"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v5.15.2/js/all.js"></script>

    <?php
    echo '<script>';
    echo 'document.addEventListener("DOMContentLoaded", function() {';
    if (!empty($_SESSION['tasks'])) {
        foreach ($_SESSION['tasks'] as $index => $task) {
            $dueDate = $task['due_date'];
            $taskId = $task['id'];

            $dueDateTime = new DateTime($dueDate);
            $now = new DateTime();

            $tomorrow = new DateTime('tomorrow');

            $isCompleted = $task['completed'];

            if (!$isCompleted && $dueDateTime == $tomorrow) {
                $reminderMessage = "📅 Friendly Reminder: The task '{$task['task']}' is due tomorrow. Please ensure it is completed on time.";
                echo 'showReminder("' . $reminderMessage . '", ' . $index . ');';
            }
        }
    }
    echo '});';
    echo '</script>';
    ?>


    <script>
        var dateTimeElement = document.getElementById("dateTime");
        var dateTime = new Date();
        var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', timeZoneName: 'short' };
        var formattedDateTime = dateTime.toLocaleDateString('en-US', options);
        dateTimeElement.innerHTML = "<strong>Date - Time:</strong> " + formattedDateTime;
    </script>

</body>
</html>
