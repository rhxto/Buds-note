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
    <div>
      <br/>
      <h4>Search note:</h4>
    </div>
    <input type="text" id="Title" class="fadeIn first" name="login" placeholder="Title">
    <p/>
    <div class="dropdown fadeIn second">
      <button type="button" class="btn btn-primary dropdown-toggle" onclick="selectSubj(this);" id="Subject" data-toggle="dropdown">
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
    <div class="dropdown fadeIn second">
      <button type="button" class="btn btn-primary dropdown-toggle" onclick="selectDept(this);" id="Dept" data-toggle="dropdown">
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
      <div class="container fadeIn third">
        <p>Year:</p>
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
          <label class="btn btn-info">
            <input type="radio" name="options" id="year_1" autocomplete="off"> 1
          </label>
          <label class="btn btn-info">
            <input type="radio" name="options" id="year_2" autocomplete="off"> 2
          </label>
          <label class="btn btn-info">
            <input type="radio" name="options" id="year_3" autocomplete="off"> 3
          </label>
          <label class="btn btn-info">
            <input type="radio" name="options" id="year_4" autocomplete="off"> 4
          </label>
          <label class="btn btn-info">
            <input type="radio" name="options" id="year_5" autocomplete="off"> 5
          </label>
        </div>
      </br>
      </div>
    </p>
    <input type="text" id="User" class="fadeIn third" name="login" placeholder="User">
    <input type="submit" class="fadeIn fourth" value="Search" onclick="testInput()">
  </div>
  <h2>Your note feed:</h2>
  <div id="formContent" style="visibility : visible">
    <!-- Da qui fino alla fine del div metteremo la parte per le risposte con le anteprime delle note -->
    <!-- Ora creo un esempio di come dovrà essere una nota -->
      <hr>
        <div class="row">
          <a id="noteName" href="Note page link" class="text-decoration-none text-left fadeIn second">Here note name</a>
          <a id="noteOwner" href="User page link" class="text-decoration-none text-right fadeIn second">User name here</a>
        </div>
        <div class="row">
          <p class="noteDate"></p>
        </div>
  </div>
</div>

</body>
</html>
