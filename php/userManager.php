<?php session_start();
  require_once 'core.php';
  require_once 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      die(json_encode(["status"=>"USERNV"]));
    }
    return $data;
  }
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"]) && $_SESSION['logged_in'] == '1' && isset($_POST["type"])) {
    $type = test_input($_POST["type"]);
    if ($_POST["phrase"] !== ($phrase = test_input($_POST["phrase"]))) {
      die(json_encode(["status"=>"USERSNV"]));
    }
    switch ($type) {
      case "search":
        if (mysqlChckUsr($phrase)) {
          if (($results = user(connectDb(), $phrase, NULL, NULL, NULL, NULL, NULL)) === "internalError") {
            $result = [
              "status"=>"USERSIE"
            ];
          } else {
            $result = [
              "status"=>"success",
              "results"=>$results
            ];
          }
        } else {
          $result = [
            "status"=>"nrt"
          ];
        }
        break;
      default:
          die(json_encode(["status"=>"USERANV"]));
        break;
    }
    echo json_encode($result);
  } else {
    die(json_encode(["status"=>"USERNL"]));
  }
 ?>
