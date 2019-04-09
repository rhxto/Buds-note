<?php
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      error_log("Username or password invalid after script chars trim, ignoring.");
      echo "Inserire username e password validi.";
      die("Invalid username or password");
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Username"]) || $_POST["Username"] == "") {
      error_log("POST username value null, ignoring.");
      echo "Inserire username e password validi.";
      die("Invalid username or password");
    } else {
      $Username = test_input($_POST["Username"]);
    }

    if (empty($_POST["Password"]) || $_POST["Password"] == "") {
      error_log("POST password value null, ignoring.");
      echo "Inserire username e password validi.";
      die("Invalid username or password");
    } else {
      $Password = test_input($_POST["Password"]);
    }
  }

  $UsernameDb = '"' . $Username . '"';
  $PasswordDb = '"' . $Password . '"';

  $mysqlServer = "localhost";
  $mysqlUser = "system";
  $mysqlPasswd = "the_best_admin_passwd";

  try {
    $conn = new PDO("mysql:host=$mysqlServer;dbname=Logins", $mysqlUser, $mysqlPasswd);
    echo "Connected successfully to mysql!";
    $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Excepion errmode set!";
    $conn->exec("USE Logins;");
    echo "Database selected successfully!";
    $conn->exec("INSERT INTO Logins (Username, Password) VALUES ($UsernameDb, $PasswordDb)");
    echo "Done!";
    $conn = null;
  } catch(PDOException $e) {
    echo "Connection failure: " . $e->getMessage();
    $conn = null;
  }
?>
