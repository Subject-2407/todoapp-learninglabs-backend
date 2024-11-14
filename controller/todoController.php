<?php
require_once "../utils/connect.php";
require_once "../utils/response.php";

class TodoController {
    public $uploadDir = '../public/images/todo/';
    public function getTodos() {
        global $mysqli;
        $query = "
        SELECT * FROM todo
        ORDER BY 
            CASE 
                WHEN state = 'In Progress' THEN 1 
                WHEN state = 'Pending' THEN 2 
                WHEN state = 'Completed' THEN 3 
            END,
            updatedAt DESC
        ";
        $data = array();
        if ($result = $mysqli->query($query)) {
            while ($row = mysqli_fetch_object($result)) {
                $data[] = $row;
            }
            sendJsonResponse(200, 'Successfully retrieved todo list', $data);
        } else {
            sendJsonResponse(500, 'Failed to retrieve todo list : ' . $mysqli->error);
        }
    }
    public function getTodosById($id = 0) {
        global $mysqli;
        $query = "SELECT * FROM todo";
        if ($id != 0) {
            $query .= " WHERE id=" . $id . " LIMIT 1";
        }
        $data = array();
        if ($result = $mysqli->query($query)) {
            while ($row = mysqli_fetch_object($result)) {
                $data[] = $row;
            }
            if (empty($data) && $id != 0) {
                sendJsonResponse(404, 'Todo item with id ' . $id . ' not found.');
            } else {
                sendJsonResponse(200, 'Successfully retrieved todo list' . ($id != 0 ? ' of id ' . $id : ''), $data);
            }
        } else {
            sendJsonResponse(500, 'Faield to retrieve todo list: ' . $mysqli->error);
        }
    }
    public function insertTodo() { // form-data only!
        global $mysqli;
        
    
        if ($_POST['title'] && $_POST['state']) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $state = $_POST['state'];
    
            $imagePath = ''; 
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
    
                if (!is_dir($this->uploadDir)) {
                    mkdir($this->uploadDir, 0777, true);
                }
    
                $query = "INSERT INTO todo (title, description, state) VALUES (?, ?, ?)";
                if ($stmt = $mysqli->prepare($query)) {
                    $stmt->bind_param("sss", $title, $description, $state);
    
                    if ($stmt->execute()) {
                        $newTodoId = $mysqli->insert_id;

                        $newFileName = $newTodoId . '-' . basename($fileName);
                        $imagePath = $this->uploadDir . $newFileName;
    
                        if (move_uploaded_file($fileTmpPath, $imagePath)) {
                            $updateQuery = "UPDATE todo SET image = ? WHERE id = ?";
                            if ($updateStmt = $mysqli->prepare($updateQuery)) {
                                $updateStmt->bind_param("si", $newFileName, $newTodoId);
                                $updateStmt->execute();
                                $updateStmt->close();
                            } else {
                                sendJsonResponse(500, 'Error: Failed to update the todo image path.');
                                return;
                            }

                            $selectQuery = "SELECT id, title, updatedAt FROM todo WHERE id = ?";
                            $stmtSelect = $mysqli->prepare($selectQuery);
                            $stmtSelect->bind_param("i", $newTodoId);
                            $stmtSelect->execute();
                            $result = $stmtSelect->get_result();
                            $newTodo = $result->fetch_assoc();            
    
                            sendJsonResponse(201, "Successfully added a new todo with image attachment!", $newTodo);
                        } else {
                            sendJsonResponse(500, 'Error: Failed to move the uploaded image.');
                        }
                    } else {
                        sendJsonResponse(500, "Failed to insert a todo: " . $stmt->error);
                    }
    
                    $stmt->close();
                } else {
                    sendJsonResponse(500, "Error: Failed to prepare the SQL query.");
                }
            } else {
                $query = "INSERT INTO todo (title, description, state) VALUES (?, ?, ?)";
                if ($stmt = $mysqli->prepare($query)) {
                    $stmt->bind_param("sss", $title, $description, $state);
                    if ($stmt->execute()) {
                        $newTodoId = $mysqli->insert_id;
                        $selectQuery = "SELECT id, title, updatedAt FROM todo WHERE id = ?";
                        $stmtSelect = $mysqli->prepare($selectQuery);
                        $stmtSelect->bind_param("i", $newTodoId);
                        $stmtSelect->execute();
                        $result = $stmtSelect->get_result();
                        $newTodo = $result->fetch_assoc();   
                        sendJsonResponse(201, "Successfully added a new todo!", $newTodo);
                    } else {
                        sendJsonResponse(500, "Failed to insert a todo: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    sendJsonResponse(500, "Error: Failed to prepare the SQL query.");
                }
            }
        } else {
            sendJsonResponse(400, 'Please fill in the required fields!');
        }
    }
    public function updateTodo($id) {
        global $mysqli;
        $input = json_decode(file_get_contents("php://input"), true);
    
        $fields = [];
    
        if (isset($input['title'])) {
            $fields[] = "title = '".$input['title']."'";
        }
        if (isset($input['description'])) {
            $fields[] = "description = '".$input['description']."'";
        }
        if (isset($input['state'])) {
            $fields[] = "state = '".$input['state']."'";
        }
        if (isset($input['dueAt'])) {
            $fields[] = "dueAt = '".$input['dueAt']."'";
        }

        $imagePath = '';
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
    
            $newFileName = $id . '-todo-' . basename($fileName);
    
            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0777, true);
            }
    
            $imagePath = $this->uploadDir . $newFileName;
            if (!move_uploaded_file($fileTmpPath, $imagePath)) {
                sendJsonResponse(500, 'Error: Failed to move the uploaded image.');
                return;
            }
    
            $fields[] = "image = '".$newFileName."'";
        }
    
        if (empty($fields)) {
            sendJsonResponse(400, "No fields to update.");
            return;
        }
    
        $query = "UPDATE todo SET " . implode(", ", $fields) . " WHERE id = ".$id;
        
        $result = $mysqli->query($query);
        if ($result) {
            $query = "SELECT id, title, description, image, state, updatedAt FROM todo WHERE id = ?";
            $stmtSelect = $mysqli->prepare($query);
            $stmtSelect->bind_param("i", $id);
            $stmtSelect->execute();
            $selectResult = $stmtSelect->get_result();
            $updatedTodo = $selectResult->fetch_assoc();
            sendJsonResponse(200, "Todo item updated successfully.", $updatedTodo);
        } else {
            sendJsonResponse(500, "Failed to update todo: " . $mysqli->error);
        }
    }
    public function deleteTodo($id) {
        global $mysqli;
    
        $query = "SELECT * FROM todo WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            sendJsonResponse(404, "Todo item with ID $id not found.");
            return;
        }

        $query = "DELETE FROM todo WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            sendJsonResponse(200, "Todo item with ID $id deleted successfully.");
        } else {
            sendJsonResponse(500, "Failed to delete todo item with ID $id: " . $mysqli->error);
        }
    }    
    
}