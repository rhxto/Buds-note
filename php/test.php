<?php
require_once "core.php";
require_once "funs.php";
require_once 'query_funs.php';
$conn = connectDb();
$response = searchNote($conn, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, "date", "desc");
print_r($response);
?>
