<?php
  require '../php/ips.php';
  $ip = $_SERVER['REMOTE_ADDR'];
  loginCheck($ip);
 ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Bud's note upload</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="../jquery/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="home.css" />
  <script src="../main/errorHandler.js"></script>
  <script src="home.js"></script>
</head>
<body>
  <div class="localWarn alert alert-danger alert-dismissible fade show">
    <strong>Warning! </strong><span id="localWarn"></span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="pageContainer">
    <div class="wrapper fadeInDown">
      <div id="formContent">
      
      </div>
    </div>
  </div>
</body>
</html>
