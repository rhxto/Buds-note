<?php
  require_once '../php/core.php';
  require_once '../php/funs.php';
  if (login($_POST["username"], hash("sha256", $_POST["password"])) == "true") {
    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      if ($data == "") {
        die(json_encode(["status"=>"Valori non validi!"]));
      }
      return $data;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
          if (empty($_POST["username"]) || empty($_POST["note_id"]) || empty($_POST["commento"])) {
            die(json_encode(["status"=>"fail", "code"=> "600_values_not_valid"]));
          } else {
            postComment(connectDb(), $_POST["username"], test_input($_POST["note_id"]), $_POST["commento"]);
            $result = [
              "status"=> "success"
            ];
          }
    } else {
      $result = [
        "status"=>"fail",
        "code"=> "54_operation_not_valid"
      ];
    }
  } else {
    $result = [
      "status"=>"fail",
      "code"=>"1_request_not_correct"
    ];
  }
  echo json_encode($result);
?>
