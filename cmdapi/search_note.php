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
        $title = NULL;
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
        $user = NULL;
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
        $subj = NULL;
      } else {
        $subj = test_input($_POST["subj"]);
        if ($_POST["subj"] != $subj) {
          $result = [
            "status"=>"fail",
            "code"=>"58_subj_not_valid"
          ];
        }
      }
      if ((!isset($_POST["years"]) || $_POST["years"] === "") || $_POST["years"] === "undefined") {
        $years_array = ["true", "true", "true", "true", "true"];
      } else {
        $years = test_input($_POST["years"]);
        if (strlen($years) === 0) {
          $result = [
            "status"=>"fail",
            "code"=>"59_year_not_valid"
          ];
        } else {
          $years_array = [
            "false",
            "false",
            "false",
            "false",
            "false"
          ];
          for ($i = 0; $i < strlen($years); $i++) {
            if (!in_array($years[$i], ["1","2","3","4","5"], true)) {
              $result = [
                "status"=>"fail",
                "code"=>"59_year_not_valid"
              ];
              break; //usciamo
            } else {
              switch ($years[$i]) {
                case "1":
                  $years_array[0] = "true";
                  break;
                case "2":
                  $years_array[1] = "true";
                  break;
                case "3":
                  $years_array[2] = "true";
                  break;
                case "4":
                  $years_array[3] = "true";
                  break;
                case "5":
                  $years_array[4] = "true";
                  break;
                default:
                  error_log("***CASO DEFAULT ESEGUITO IN search_note.php NON DOVREBBE SUCCEDERE: years[i]=" . $years[$i]);
                  die();
              }
            }
          }
        }
      }
      if ((!isset($_POST["dept"]) || $_POST["dept"] == "") || $_POST["dept"] == "undefined") {
        $dept = NULL;
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
        $datefrom = NULL;
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
        $dateto = NULL;
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
        $order = NULL;
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
        $orderby = NULL;
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
      error_log(print_r($years_array, true));
      $response = searchNote(connectDb(), $title, NULL, $user, $subj, $years_array, $dept, $datefrom, $dateto, $orderby, $order);
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
      $code = $result["code"];
      $result = [
        "status"=>"fail",
        "code"=>"1_request_not_correct(additional info: status: $code )"
      ];
    }
    echo json_encode($result);
  }
?>
