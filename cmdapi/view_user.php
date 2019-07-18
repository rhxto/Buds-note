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
    if ($_SERVER["REQUEST_METHOD"] == "POST" && ($type = test_input($_POST["type"])) == $_POST["type"]) {
        if ($type == "search_user") {
          if (empty($_POST["phrase"]) || $_POST["phrase"] == "") {
            die(json_encode(["status"=>"Valori non validi!"]));
          } else {
            $phrase = test_input($_POST["phrase"]);
          }
          $result = user(connectDb(), $phrase, NULL, NULL, NULL, NULL, NULL);
	  if (!empty($result[0]["username"])) {	  
            $result = [
              "status"=> "success",
              "username"=> $result[0]["username"],
              "last_log"=> $result[0]["last_log"],
	      "left_rate"=> getLeftRate($phrase),
	      "left_comm"=> getLeftComm($phrase),
	      "note_num"=> getNoteNum($phrase)
            ];
          } else {
            //nota non trovata
            $result = ["status"=> "fail", "code"=>"400_user_not_found"];
          }
        } else {
          $result = [
            "status"=>"fail",
            "code"=> "55_type_not_valid"
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
