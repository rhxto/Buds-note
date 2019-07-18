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
  if (login(test_input($_POST["username"]), hash("sha256", test_input($_POST["password"]))) == "true") {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && ($type = test_input($_POST["type"])) == $_POST["type"]) {
        if ($type == "write_note") {
          if (empty($_POST["title"]) || empty($_POST["subj"]) || empty($_POST["dept"]) || empty($_POST["content"])) {
            die(json_encode(["status"=>"fail", "code"=>"600_values_not_valid"]));
          } else {
            $title = test_input($_POST["title"]);
            if (!checkNote(connectDb(), $title)) {
              $user = test_input($_POST["username"]);
              $subj = test_input($_POST["subj"]);
              $dept = test_input($_POST["dept"]);
              $content = test_input($_POST["content"]);
              if ($title != $_POST["title"] || $subj != $_POST["subj"] || $dept != $_POST["dept"]) {
                die(json_encode(["status"=>"fail", "code"=>"600_values_not_valid"]));
              }
              writeNote(connectDb(), $title, $user, $subj, $dept, $content);
              if (checkNote(connectDb(), $title)) {
                $result = [
                  "status"=> "success"
                ];
              } else {
                $result = [
                  "status"=> "fail",
                  "code"=>"601_write_error"
                ];
              }
            } else {
              $result = [
                "status"=> "fail",
                "code"=>"602_note_exists"
              ];
            }
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
