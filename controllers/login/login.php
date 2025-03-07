
<?php
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
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION["user_id"] = $row["USER_ID"];
            $_SESSION["user_name"] = $row["USER_NAME"];
            $_SESSION["user_email"] = $row["USER_EMAIL"];
            $_SESSION["user_type"] = $row["USER_TYPE"];

            echo "success";

            return;
        }
        echo "notapproved";

       
        
    }else{

        echo "wrong";

    }

    return;
}


if(isset($_GET['register'])){


    $data=json_decode(file_get_contents("php://input"),true);

    $username = $data["username"];
    $email = $data["email"];
    $account_type = $data["account_type"];
    $password = $data["password"];
    $studentID = $data["student_id"];
    $password = password_hash($password,PASSWORD_DEFAULT);


    $invalid = false;
    
    print_r($username);

    // if(isEmailExists($conn,$email)==true){
    //     $invalid = true;
    // }
    // if($account_type == "STUDENT"){
    //     $studentID = $data["student_id"];
    //     if(isStudentIDExists($conn,$studentID)){
    //         $invalid = true;
    //     }
    // }else{
    //     $studentID = "";
    // }

    // print_r("is Inavalid:" . $invalid);
    // if($invalid){
    //     echo "false";
    //     return;
    // }
  
    $query = "INSERT INTO user (USER_NAME, USER_EMAIL, STUDENT_ID, USER_PASSWORD, USER_TYPE) VALUES ('$username', '$email', '$studentID', '$password', '$account_type')";
    print_r($query);
    $result = $conn->query($query);

    print_r($result);
    
    if($result){
        echo "success";
    }else{
        echo "false";
    }

    return;
}

if(isset($_GET["logout"])){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_destroy();
    $_SESSION = array();
    header("Location: ../../views/login/view.php");
    return;
}



?>