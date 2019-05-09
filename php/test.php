<?php
require "core.php";

$conn = connectDb();
$r = subj($conn, NULL, "5");
print_r($r);
?>
