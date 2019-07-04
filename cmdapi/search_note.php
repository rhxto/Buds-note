<?php
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      //echo 'nonAN';
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["type"]) || $_POST["type"] == "") {
      $result = [
        "status"=>"fail",
        "code"=>"55_type_not_valid"
      ];
    } else {
      $type = test_input($_POST["type"]);
    }

    if ($type == "search_note"){
      if ((!isset($_POST["title"]) || $_POST["title"] == "") || $_POST["title"] == "undefined") {
        $title =  NULL;
      } else {
        $title = test_input($_POST["title"]);
        if ($_POST["title"] != $title) {
          //echo 'nonAN';
          $result = [
            "status"=>"fail",
            "code"=>"56_title_not_valid"
          ];
        }
      }
      if ((!isset($_POST["user"]) || $_POST["user"] == "") || $_POST["user"] == "undefined") {
        $user =  NULL;
      } else {
        $user = test_input($_POST["user"]);
        if ($_POST["user"] != $user) {
          //echo 'nonAN';
          $result = [
            "status"=>"fail",
            "code"=>"57_user_not_valid"
          ];
        }
      }
      if ((!isset($_POST["subj"]) || $_POST["subj"] == "") || $_POST["subj"] == "undefined") {
        $subj =  NULL;
      } else {
        $subj = test_input($_POST["subj"]);
        if ($_POST["subj"] != $subj) {
          $result = [
            "status"=>"fail",
            "code"=>"58_subj_not_valid"
          ];
        }
      }
      if ((!isset($_POST["year"]) || $_POST["year"] == "") || $_POST["year"] == "undefined") {
        $year =  NULL;
      } else {
        $year = test_input($_POST["year"]);
        if ($_POST["year"] != $year) {
          $result = [
            "status"=>"fail",
            "code"=>"59_year_not_valid"
          ];
        }
      }
      if ((!isset($_POST["dept"]) || $_POST["dept"] == "") || $_POST["dept"] == "undefined") {
        $dept =  NULL;
      } else {
        $dept = test_input($_POST["dept"]);
        if ($_POST["dept"] != $dept) {
          //echo 'nonAN';
          $result = [
            "status"=>"fail",
            "code"=>"60_dept_not_valid"
          ];
        }
      }

      if ((!isset($_POST["datefrom"]) || $_POST["datefrom"] == "") || $_POST["datefrom"] == "undefined") {
        $datefrom =  NULL;
      } else {
        $datefrom = test_input($_POST["datefrom"]);
        if ($_POST["datefrom"] != $datefrom) {
          //echo 'nonAN';
          $result = [
            "status"=>"fail",
            "code"=>"61_date_from_not_valid"
          ];
        }
      }
      if ((!isset($_POST["dateto"]) || $_POST["dateto"] == "") || $_POST["dateto"] == "undefined") {
        $dateto =  NULL;
      } else {
        $dateto = test_input($_POST["dateto"]);
        if ($_POST["dateto"] != $dateto) {
          $result = [
            "status"=>"fail",
            "code"=>"62_date_to_not_valid"
          ];
        }
      }
      if ((!isset($_POST["order"]) || $_POST["order"] == "") || $_POST["order"] == "undefined") {
        $order =  NULL;
      } else {
        $order = test_input($_POST["order"]);
        if ($_POST["order"] != $order) {
          $result = [
            "status"=>"fail",
            "code"=>"63_order_not_valid"
          ];
        }
      }
      if ((!isset($_POST["orderby"]) || $_POST["orderby"] == "") || $_POST["orderby"] == "undefined") {
        $orderby =  NULL;
      } else {
        $orderby = test_input($_POST["orderby"]);
        if ($_POST["orderby"] != $orderby) {
          $result = [
            "status"=>"fail",
            "code"=>"64_order_by_not_valid"
          ];
        }
        if ($orderby == "Titolo") {
          $orderby = "title";
        } elseif ($orderby == "Username") {
          $orderby = "user";
        } elseif ($orderby == "Materia") {
          $orderby = "subj";
        } elseif ($orderby == "Anno") {
          $orderby = "year";
        } elseif ($orderby == "Indirizzo") {
          $orderby = "dept";
        } elseif ($orderby == "Data") {
          $orderby = "date";
        } else {
          $orderby = NULL;
        }
      }
    }
    if ($type == "search_note"  && empty($result["status"])) {
      require_once "../php/core.php";
      require_once "../php/funs.php";
      $response = searchNote(connectDb(), $title, NULL, $user, $subj, $year, $dept, $datefrom, $dateto, $orderby, $order);
      if ($response == "internalError") {
        $result = [
          "status"=> "fail",
          "code"=> "500_internal_error"
        ];
      } else {
        if (!isset($response[0]["title"])) {
          $result = [
            "status"=> "fail",
            "code"=> "404_not_found"
          ];
        } else {
          $result = [
            "status"=>"success",
            "search_results"=>$response
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
  }
?>
