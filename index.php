<?php session_start();
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  if (isset($_SESSION["logged_in"])) {
  	if ($_SESSION["logged_in"] === "1" && $_SESSION["username"] != null) {
  		echo "<script>window.location.href='home/';</script>";
  	}
  }
?>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Welcome to Bud's note</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="main/welcome.css" type="text/css" />
  	</head>
	<body>
		<div class="container-fluid">
			<div class="wrapper fadeInDown">
				<div class="row">
					<div class="col-sm-12">
						<img src="bootstrap/Logotest.png" alt="Logo di Bud's note" class="logo"></img>
					</div>
				</div>
			</br>
				<div id="formContent">
				</br>
					<div class="row">
						<div class="col-sm-12">
							<p class="text-center">Welcome to Bud's Note!</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 text-center with-padding">
							<button onclick="window.location.href='login/'" type="button" class="btn btn-info btn-lg button">Login</button>
						</div>
					</div>
					<div class="row">
					<div class="col-sm-12 text-center">
							<button onclick="window.location.href='register'" type="button" class="btn btn-info btn-lg button">Register</button>
						</div>
					</div>
				</br>
				</div>
			</div>
		<script src="jquery/jquery.min.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
