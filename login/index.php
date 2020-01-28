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
  <script src="../jquery/jquery.min.js"></script>
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="login.js"></script>
  <link rel="stylesheet" href="login.css" type="text/css" />
</head>
<body>
<div class="wrapper fadeInDown">
  <div class="localWarn alert alert-danger alert-dismissible" hidden>
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Warning! </strong><span id="localWarn"></span>
  </div>
  <div id="formContent">
    <div class="fadeIn first">
      <img src="../bootstrap/Logotest.png" id="icon" alt="User Icon" />
    </div>
    <input type="text" id="Username" class="fadeIn second" name="login" placeholder="Username">
    <input type="text" id="Password" class="fadeIn third" name="login" placeholder="Password">
    <input type="submit" class="fadeIn fourth" value="Log In" onclick="testInput()">

    <div id="formFooter">
      <a class="underlineHover" href="#">Forgot Password?</a>
    </div>

  </div>
</div>

</body>
</html>
