<?php session_start();
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Bud's note login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="../jquery/jquery.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="register.js"></script>
  <link rel="stylesheet" href="register.css" type="text/css" />
</head>
<body>
<div class="wrapper fadeInDown">
  <div id="formContent">
    <div class="fadeIn first">
      <img src="../bootstrap/Logotest.png" id="icon" alt="User Icon" />
    </div>
    <br/>
    <input type="text" id="Username" class="fadeIn second" name="username" placeholder="Username" />
    <input type="text" id="Email" class="fadeIn third" name="e-mail" placeholder="E-mail" />
    <input type="text" id="Password" class="fadeIn fourth" name="password" placeholder="Password" />
    <input type="text" id="RepeatPassword" class="fadeIn fourth" name="password" placeholder="Repeat password" />
    <input type="submit" onclick="testInput()" class="fadeIn fifth" value="Register">
  </div>
</div>

</body>
</html>
