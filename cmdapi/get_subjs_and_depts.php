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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $result = subj(connectDb(), NULL, NULL);
      $result2 = dept(connectDb(), NULL, NULL);
      if (empty($result[0][0]) || empty($result2[0][0])) {
        $result = [
          "status"=> "fail",
          "code"=>"700_no_subj_found"
        ];
      } else {
        $subjs = array();
        foreach ($result[1] as $subj) {
          array_push($subjs, $subj);
        }
        $depts = array();
        foreach ($result2[1] as $dept) {
          array_push($depts, $dept);
        }
        $result = [
          "status"=> "success",
          "subjs"=> $subjs,
          "depts"=> $depts
        ];
      }
    }
  } else {
    $result = [
      "status"=>"fail",
      "code"=>"1_request_not_correct"
    ];
  }
  echo json_encode($result);
?>
