<?php
  require_once '../php/core.php';
  require_once '../php/funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      die(json_encode(["status"=>"fail", "code"=>"600_values_not_valid"]));
    }
    return $data;
  }
  if (login(($username = test_input($_POST["username"])), hash("sha256", test_input($_POST["password"]))) == "true") {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      error_log("**DEBUG: note_id: " . $_POST["note_id"] . " rating: " . $_POST["rating"]);
      if (empty($_POST["note_id"]) || empty($_POST["rating"])) {
        $response = [
          "status"=>"fail",
          "code"=>"903_note_rating_not_valid"];
      } else {
        $note_id = test_input($_POST["note_id"]);
        $rating = test_input($_POST["rating"]);
        if (checkNote(connectDb(), $note_id)) {
          if ($rating == "true") {
            $rating = true;
          } else {
            $rating = false;
          }
          if ($response = rateNote($username, $note_id, $rating)) {
            $response = [
              "status"=>"success"
            ];
          } elseif ($response === "internalError") {
            $response = [
              "status"=>"fail",
              "code"=>"900_rate_failure"
            ];
          } else {
            $response = [
              "status"=>"fail",
              "code"=>"901_already_rated"
            ];
          }
        } else {
          $response = [
            "status"=>"fail",
            "code"=>"902_note_does_not_exist"
          ];
        }
      }
    } else {
      $response = [
        "status"=>"fail",
        "code"=> "54_operation_not_valid"
      ];
    }
  } else {
    $response = [
      "status"=>"fail",
      "code"=>"1_request_not_correct"
    ];
  }
  echo json_encode($response);
?>
