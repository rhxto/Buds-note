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
  <title>Bud's note search</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="../jquery/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="search.js"></script>
  <link rel="stylesheet" href="search.css" type="text/css" />
</head>
<body>
<div class="wrapper fadeInDown">
  <div id="formContent">
    <div class="fadeIn first">
      <p>Search note</p>
    </div>
    <input type="text" id="Title" class="fadeIn second" name="login" placeholder="Title">
    <p/>
    <div class="dropdown">
      <button type="button" class="btn btn-primary dropdown-toggle" id="Subject" data-toggle="dropdown">
        Subject
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <?php
          require_once "../php/core.php";
          require_once '../php/funs.php';
          $response = subj(connectDb(), NULL, NULL);
          foreach ($response[1] as $subj) {
            echo "<a class='dropdown-item' onclick='selectSubj(this)'>" . $subj . "</a>";
          }
        ?>
      </div>
    </div><p/>
    <div class="dropdown">
      <button type="button" class="btn btn-primary dropdown-toggle" id="Dept" data-toggle="dropdown">
        Department
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <?php
          $response = dept(connectDb(), NULL, NULL);
          foreach ($response[1] as $dept) {
            echo "<a class='dropdown-item' onclick='selectDept(this)'>" . $dept . "</a>";
          }
        ?>
      </div>
    </div><p/>
    <div class="dropdown">
      <button class="btn btn-primary dropdown-toggle fadeIn third" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Year
      </button>
      <p/>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <div class="funkyradio">
          <div class="funkyradio-primary">
              <input type="checkbox" name="checkbox" id="inputyear_1" checked/>
              <label for="checkbox2">1</label>
              <input type="checkbox" name="checkbox" id="inputyear_2" checked/>
              <label for="checkbox2">2</label>
              <input type="checkbox" name="checkbox" id="inputyear_3" checked/>
              <label for="checkbox2">3</label>
              <input type="checkbox" name="checkbox" id="inputyear_4" checked/>
              <label for="checkbox2">4</label>
              <input type="checkbox" name="checkbox" id="inputyear_5" checked/>
              <label for="checkbox2">5</label>
          </div>
        </div>
      </div>
    </div>

    <input type="text" id="User" class="fadeIn third" name="login" placeholder="User">
    <input type="submit" class="fadeIn fourth" value="Search" onclick="testInput()">

  </div>
</div>

</body>
</html>
