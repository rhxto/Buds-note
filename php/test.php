<?php
require "query_funs.php";

$conn = connectDb();
$r = subj($conn, NULL, "5");
print_r($r);
?>
