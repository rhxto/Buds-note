<?php session_start();
  require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      echo '<script>window.location.href = "../html/login.php?errore=credenziali"</script>';
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Username"]) || $_POST["Username"] == "") {
      echo '<script>window.location.href = "../html/login.php?errore=credenziali"</script>';
    } else {
      $Username = test_input($_POST["Username"]);
    }

    if (empty($_POST["Password"]) || $_POST["Password"] == "") {
      echo '<script>window.location.href = "../html/login.php?errore=credenziali"</script>';
    } else {
      $Password = test_input($_POST["Password"]);
    }
  }
  if($_POST["Username"] != $Username) {
    echo '<script>window.location.href = "../html/login.php?errore=credenziali"</script>';
  }
  if ($_POST["Password"] != $Password) {
    echo '<script>window.location.href = "../html/login.php?errore=credenziali"</script>';
  }
  $Username = hash("sha256", $Username);
  $Password = hash("sha256", $Password);
  if (mysqlRetrieveCrd("localhost", "system", "the_best_admin_passwd", $Username, $Password)) {
    echo '<script>window.location.href = "../index.php"</script>';
    $_SESSION['logged_in'] = '1'; //1 = loggato, NULL no.
  } else {
    echo '<script>window.location.href = "../html/login.php?errore=credenziali"</script>';
  }
?>
