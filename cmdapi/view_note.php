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
      $result = array();
        if ($type == "read_note") {
          if (empty($_POST["note_id"]) || $_POST["note_id"] === "") {
            die(json_encode(["status"=>"Valori non validi!", "code"=>"600_values_not_valid"]));
          } else {
            $note_id = test_input($_POST["note_id"]);
          }
          $note = searchNote(connectDb(), NULL, $note_id, NULL, NULL, NULL, ["true", "true", "true", "true", "true"], NULL, NULL, NULL, NULL, NULL);
          if (!empty($note[0]["title"])) {
            $content = array();
            foreach (getNote(connectDb(), $note_id) as $row) {
              str_replace("\n", "", $row);
              array_push($content, $row);
            }
            $comments = array();
            $commentsProps = searchRevw(connectDb(), NULL, NULL, $note_id, NULL, NULL, NULL, NULL, NULL);
            foreach ($commentsProps as $comment) {
              array_push($comments, $comment["review"] . " - " . $comment["user"] . " - " . $comment["date"]);
            }
            if (($likes = getLikes($note_id)) === false || ($dislikes = getDislikes($note_id)) === false) {
              $result  = [
                "status"=> "fail",
                "code"=> "800_rating_fetch_failure"
              ];
            } else {
              $result = [
                "status"=> "success",
                "Title"=> $note[0]["title"],
                "Author"=> $note[0]["user"],
                "Subj"=> $note[0]["subj"],
                "Dept"=> $note[0]["dept"],
                "Content"=> $content,
                "Comments"=> $comments,
                "Likes"=> $likes,
                "Dislikes"=> $dislikes
              ];
            }
          } else {
            //nota non trovata
            $result = ["status"=> "fail", "code"=>"404_not_found"];
          }
        } elseif ($type == "view_recent_notes") {
          $response = searchNote(connectDb(), NULL, NULL, NULL, NULL, NULL, ["true", "true", "true", "true", "true"], NULL, NULL, NULL, "date", "desc");
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
              $titles = array();
              $ids = array();
              foreach ($response as $title) {
                array_push($titles, $title["title"]);
                array_push($ids, $title["id"]);
              }
              $result = [
                "status"=>"success",
                "titles"=> $titles,
                "ids"=> $ids
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
