<?php
require_once "../utils/connect.php";
require_once "../utils/response.php";

class TaskController {
    public $uploadDir = '../public/images/task/';
    public function getTasks() {
        global $mysqli;
        $query = "SELECT * FROM tasks";
        $data = array();
        if ($result = $mysqli->query($query)) {
            while ($row = mysqli_fetch_object($result)) {
                $data[] = $row;
            }
            sendJsonResponse(200, 'Successfully retrieved all tasks', $data);
        } else {
            sendJsonResponse(500, 'Failed to retrieve all tasks : ' . $mysqli->error);
        }
    }
    public function getTasksByTodoId($id = 0) {
        global $mysqli;
        $query = "SELECT * FROM tasks";
        if ($id != 0) {
            $query .= " WHERE todo_id=" . $id;
        }
        $data = array();
        if ($result = $mysqli->query($query)) {
            while ($row = mysqli_fetch_object($result)) {
                $data[] = $row;
            }
            if (empty($data) && $id != 0) {
                sendJsonResponse(404, 'Tasks from todo with id ' . $id . ' not found.');
            } else {
                sendJsonResponse(200, 'Successfully retrieved task list' . ($id != 0 ? ' of todo id ' . $id : ''), $data);
            }
        } else {
            sendJsonResponse(500, 'Faield to retrieve task list: ' . $mysqli->error);
        }
    }
    public function insertTask($todo_id) {
        global $mysqli;
    
        if (!isset($_POST['completed'])) {
            sendJsonResponse(400, 'The "completed" field is required.');
            return;
        }
    
        $description = isset($_POST['description']) ? $_POST['description'] : null;
        $completed = $_POST['completed'] ? 1 : 0;
    

        $checkTodoQuery = "SELECT id FROM todo WHERE id = ?";
        if ($stmt = $mysqli->prepare($checkTodoQuery)) {
            $stmt->bind_param("i", $todo_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                sendJsonResponse(404, 'Todo item not found.');
                return;
            }
            $stmt->close();
        } else {
            sendJsonResponse(500, 'Error validating the todo ID.');
            return;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
    
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024;
    
            if (!in_array($fileType, $allowedTypes)) {
                sendJsonResponse(400, 'Error: Only JPEG, PNG, and GIF images are allowed.');
                return;
            }
    
            if ($fileSize > $maxSize) {
                sendJsonResponse(400, 'Error: The file is too large. Maximum size allowed is 5MB.');
                return;
            }
    
            $newFileName = $todo_id . '-task-' . basename($fileName);
    
            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0777, true);
            }
    
            $imagePath = $this->uploadDir . $newFileName;
            if (!move_uploaded_file($fileTmpPath, $imagePath)) {
                sendJsonResponse(500, 'Error: Failed to move the uploaded image.');
                return;
            }
        }
    
        $query = "INSERT INTO tasks (description, image, completed, todo_id) VALUES (?, ?, ?, ?)";
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("ssii", $description, $newFileName, $completed, $todo_id);
            if ($stmt->execute()) {
                sendJsonResponse(201, "Successfully added a new task to the todo item.");
            } else {
                sendJsonResponse(500, "Failed to insert a task: " . $stmt->error);
            }
            $stmt->close();
        } else {
            sendJsonResponse(500, "Error: Failed to prepare the SQL query.");
        }
    }
    public function updateTask($task_id) {
        global $mysqli;
    
        $fields = [];

        $checkTaskQuery = "SELECT id FROM tasks WHERE id = ?";
        if ($stmt = $mysqli->prepare($checkTaskQuery)) {
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                sendJsonResponse(404, 'Task item not found.');
                return;
            }
            $stmt->close();
        } else {
            sendJsonResponse(500, 'Error validating the task ID.');
            return;
        }
    
        if (isset($_POST['description'])) {
            $fields[] = "description = '" . $mysqli->real_escape_string($_POST['description']) . "'";
        }
        
        if (isset($_POST['completed'])) {
            $completed = $_POST['completed'] ? 1 : 0;
            $fields[] = "completed = " . $completed;
        }
    
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; 

            if (!in_array($fileType, $allowedTypes)) {
                sendJsonResponse(400, 'Error: Only JPEG, PNG, and GIF images are allowed.');
                return;
            }

            if ($fileSize > $maxSize) {
                sendJsonResponse(400, 'Error: The file is too large. Maximum size allowed is 5MB.');
                return;
            }
    
            $newFileName = $task_id . '-' . basename($fileName);

            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0777, true);
            }

            $imagePath = $this->uploadDir . $newFileName;
            if (!move_uploaded_file($fileTmpPath, $imagePath)) {
                sendJsonResponse(500, 'Error: Failed to move the uploaded image.');
                return;
            }

            $fields[] = "image = '" . $newFileName . "'";
        }

        if (empty($fields)) {
            sendJsonResponse(400, "No fields to update.");
            return;
        }
    
        $query = "UPDATE tasks SET " . implode(", ", $fields) . " WHERE id = " . intval($task_id);
        
        $result = $mysqli->query($query);
        if ($result) {
            sendJsonResponse(200, "Task updated successfully.");
        } else {
            sendJsonResponse(500, "Failed to update task: " . $mysqli->error);
        }
    }    
}