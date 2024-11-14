<?php
require_once "../controller/todoController.php";

$todo = new TodoController();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (!empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $todo->getTodosById($id);
        } else {
            $todo->getTodos();
        }
        break;
    case 'POST':
        if (!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            $todo->updateTodo($id);
        } else {
            $todo->insertTodo();
        }
        break;
    case 'DELETE':
        if (!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            $todo->deleteTodo($id);
        }
        break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
        break;
}