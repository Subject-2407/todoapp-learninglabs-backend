<?php
require_once "../controller/taskController.php";

$task = new TaskController();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (!empty($_GET['todoid'])) {
            $id = intval($_GET['todoid']);
            $task->getTasksByTodoId($id);
        } else {
            $task->getTasks();
        }
        break;
    case 'POST':
        if (!empty($_GET['todoid'])) {
            $id = intval($_GET['todoid']);
            $task->insertTask($id);
        } else if (!empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $task->updateTask($id);
        }
        break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
        break;
}
    