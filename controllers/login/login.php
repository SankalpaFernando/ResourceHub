<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../database/connection.php";
require_once "../util/util.php";

if(isset($_GET["login"])){

    $data = json_decode(file_get_contents("php://input"),true);

    $email = $data["email"];
    $password = $data["password"];

    $query = "SELECT * FROM user WHERE USER_EMAIL='$email'";

    $result = $conn->query($query);

    if($result && $result->num_rows > 0){
        $row = $result->fetch_assoc();
        if(!password_verify($password,$row["USER_PASSWORD"])){
            echo "wrong";
            return;
        }
        $query ="SELECT * FROM user WHERE USER_EMAIL='$email' AND APPROVED=1 ";

        $res = $conn->query($query);

        if($res && $res->num_rows > 0){
            $_SESSION["user_id"] = $row["USER_ID"];
            $_SESSION["user_name"] = $row["USER_NAME"];
            $_SESSION["user_email"] = $row["USER_EMAIL"];
            $_SESSION["user_type"] = $row["USER_TYPE"];

            echo "success";
            return;
        }
        echo "notapproved";
    } else {
        echo "wrong";
    }

    return;
}

if(isset($_GET['register'])){

    $data = json_decode(file_get_contents("php://input"),true);

    $username = $data["username"];
    $email = $data["email"];
    $account_type = $data["account_type"];
    $password = $data["password"];
    $studentID = $data["student_id"];
    $password = password_hash($password, PASSWORD_DEFAULT);

    $invalid = false;

    $query = "INSERT INTO user (USER_NAME, USER_EMAIL, STUDENT_ID, USER_PASSWORD, USER_TYPE) VALUES ('$username', '$email', '$studentID', '$password', '$account_type')";
    
    $result = $conn->query($query);
    
    if($result){
        echo "success";
    } else {
        echo "false";
    }

    return;
}

if(isset($_GET["logout"])){
    session_destroy();
    $_SESSION = array();
    header("Location: ../../views/login/view.php");
    exit(); // Make sure to stop further execution after header redirection.
    return;
}
?>
