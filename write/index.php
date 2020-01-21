<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Bud's note login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="../jQuery.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="writeNote.css" />
</head>
<body>
<div class="wrapper fadeInDown">
  <div id="formContent">
  </br>
    <h4>Upload your note!</h4>
    <form>
      <br/>
      <input type="text" id="title" class="fadeIn second" name="title" placeholder="Title:"/>
      <br/>
    <div class="container">
      <p>Year:</p>
      <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-info">
          <input type="radio" name="options" id="1" autocomplete="off" checked> 1
        </label>
        <label class="btn btn-info">
          <input type="radio" name="options" id="2" autocomplete="off"> 2
        </label>
        <label class="btn btn-info">
          <input type="radio" name="options" id="3" autocomplete="off"> 3
        </label>
        <label class="btn btn-info">
          <input type="radio" name="options" id="4" autocomplete="off"> 4
        </label>
        <label class="btn btn-info">
          <input type="radio" name="options" id="5" autocomplete="off"> 5
        </label>
      </div>
    </br>
    </div>
    <div class="dropdown">
      <br/>
        <button type="button" class="btn btn-primary dropdown-toggle" id="subject" data-toggle="dropdown">
          Subject
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <!--Qui vanno messi in modo dinamici i nomi delle materie possibili-->
        <?php
          $response = dept($conn, NULL, NULL);
          for ($response as $subj) {
            echo "<a class='dropdown-item'>" . $subj["name"] . "</a>";
          }
        ?>
        <a class="dropdown-item" href="#">Link 1</a>
        <a class="dropdown-item" href="#">Link 2</a>
        <a class="dropdown-item" href="#">Link 3</a>
      </div>
    </div>
    <div class="dropdown">
      <br/>
      <button type="button" class="btn btn-primary dropdown-toggle" id="dept" data-toggle="dropdown">
        Department
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <!--Qui vanno messi in modo dinamici i nomi dei possibili indirizzi-->
        <a class="dropdown-item" href="#">Link 1</a>
        <a class="dropdown-item" href="#">Link 2</a>
        <a class="dropdown-item" href="#">Link 3</a>
      </div>
    </div>
    <br/>
      <textarea class="form-control" rows="25" id="comment" placeholder="Write here your note..."></textarea>
      <!--Qui ci va il form di upload delle foto-->
      <input type="submit" class="fadeIn fifth" value="Upload">
    </form>


</div>

</body>
</html>
