<?php
require "core.php";
require "query_funs.php";
$conn = connectDb();
$r = searchNote($conn, "ttl", NULL, NULL, NULL, NULL, NULL, NULL, NULL);
print_r($r);
?>
