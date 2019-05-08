<?php
require "query_funs.php";

$conn = connectDb();
$r = dept($conn, NULL, NULL);
print_r($r);
?>
