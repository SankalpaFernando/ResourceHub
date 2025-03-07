<?php

require_once "../../database/connection.php";
require_once "../util/checkAvailability.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET["search"])){
    $search = $_GET["search"];
    $page = $_GET["page"];
    $limit = 10;

    $user_type = $_SESSION["user_type"];
    $uid = $_SESSION["user_id"];
    $subSQL="";

    if($user_type !== "ADMIN" && $user_type!=="SUPER_ADMIN"){
        $subSQL = "AND request.USER_ID = '$uid'";
    }

    $offset = ($page-1)*$limit;

    $count = 
    "
    SELECT COUNT(*) FROM request 
    INNER JOIN USER ON request.USER_ID = USER.USER_ID
    INNER JOIN resource ON request.RESOURCE_ID = resource.ID
    INNER JOIN TIME_SLOT ON request.TIME_SLOT_ID = TIME_SLOT.ID
    INNER JOIN EVENTS ON request.EVENT_ID = EVENTS.ID
    WHERE
    (user.USER_NAME LIKE '%$search%' OR resource.RESOURCE_NAME LIKE '%$search%'
    OR EVENTS.EVENT_NAME LIKE '%$search%') $subSQL 
    ";

    $query = 
    "
    SELECT * FROM request 
        INNER JOIN USER ON request.USER_ID = USER.USER_ID
        INNER JOIN resource ON request.RESOURCE_ID = resource.ID
        INNER JOIN TIME_SLOT ON request.TIME_SLOT_ID = TIME_SLOT.ID
        INNER JOIN EVENTS ON request.EVENT_ID = EVENTS.ID
        WHERE
        (user.USER_NAME LIKE '%$search%' OR resource.RESOURCE_NAME LIKE '%$search%'
        OR EVENTS.EVENT_NAME LIKE '%$search%') $subSQL
        ORDER BY request.ID DESC
        LIMIT $limit OFFSET $offset 
    ";




    $result = $conn->query($query);



    $events = array();
    $response = array();

    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            array_push($events,$row);
        }
    }
    $response["data"] = $events;
    $response["total"] = $conn->query($count)->fetch_assoc()["COUNT(*)"];
    $response["page"] = $page;
    $response["offset"] = $offset;
    echo json_encode($response);
    return;

}


if(isset($_GET["id"])){
    $id = $_GET["id"];
    $query = "
    SELECT * FROM request 
        INNER JOIN USER ON request.USER_ID = USER.USER_ID
        INNER JOIN resource ON request.RESOURCE_ID = resource.ID
        INNER JOIN TIME_SLOT ON request.TIME_SLOT_ID = TIME_SLOT.ID
        INNER JOIN EVENTS ON request.EVENT_ID = EVENTS.ID
    WHERE request.REQUEST_ID = '$id'

    ";


    
    $result = $conn->query($query);
    $response = array();
    if($result->num_rows>0){
        $response["data"] = $result->fetch_assoc();
        echo json_encode($response);
    }else{
        echo "false";
    }
    return;
}

if(isset($_GET["decline"])){
    $data = json_decode(file_get_contents("php://input"),true);
    $requestID = $data["request_id"];

    $queryUpdateRequest = "UPDATE request SET REQUEST_APPROVED=-1 WHERE REQUEST_ID='$requestID'";
    $resultUpdateRequest = $conn->query($queryUpdateRequest);

    if($resultUpdateRequest){
        echo "success";
    }else{
        echo "false";
    }
    return;
}


if(isset($_GET["approve"])){
    $data = json_decode(file_get_contents("php://input"),true);
    $resourceID = $data["resource_id"];
    $requestID = $data["request_id"];
    $eventID = $data["event_id"];
    $timeSlotID = $data["time_slot_id"];
    $recurring = $data["recurring"];
    $date = $data["date"];

    $query ="";

    //Get start and end time from time_slot
    $queryGetTimeSlot = "SELECT * FROM time_slot WHERE ID = '$timeSlotID'";
    $resultGetTimeSlot = $conn->query($queryGetTimeSlot);
    $result = $resultGetTimeSlot->fetch_assoc();
    $start = $result["START_TIME"];
    $end = $result["END_TIME"];


    if(isAvailable($conn,$start,$end,$date,$resourceID)){
        echo "overlap";
        return;
    }

    if($recurring){
        $query = "INSERT INTO occupied (EVENT_ID,RESOURCE_ID,TIME_SLOT_ID,ACTIVE) VALUES ('$eventID','$resourceID','$timeSlotID',1)";
    }else{
        $query = "INSERT INTO occupied (EVENT_ID,RESOURCE_ID,TIME_SLOT_ID,OCCUPIED_DATE,ACTIVE) VALUES ('$eventID','$resourceID','$timeSlotID','$date',1)";
    }

    $result = $conn->query($query);

    $queryUpdate = "UPDATE EVENTS SET TEMP='FALSE' WHERE ID='$eventID'";
    $resultUpdate = $conn->query($queryUpdate);


    $queryUpdateRequest = "UPDATE request SET REQUEST_APPROVED=1 WHERE REQUEST_ID='$requestID'";
    $resultUpdateRequest = $conn->query($queryUpdateRequest);



    if($result && $resultUpdate && $resultUpdateRequest ){
        echo "success";
    }else{
        echo "false";
    }
    return;
}






if(isset($_GET["request_add"])){
    $data = json_decode(file_get_contents("php://input"),true);
    $isRecurring = $_POST["is_recurring"];
    $eventID = $_POST["event_id"];
    if($_POST["isNewEvent"]=="true"){
        $newEventQuery = "INSERT INTO EVENTS (EVENT_NAME,EVENT_TYPE,CONDUCT_BY,OPTIONAL_DETAILS,TEMP,RECURRING) VALUES ('".$_POST["event_name"]."','".$_POST["event_type"]."','".$_POST["conduct_by"]."','".$_POST["optional_details"]."','TRUE','$isRecurring')";
        $newEventResult = $conn->query($newEventQuery);
        $eventID = $conn->insert_id;
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $resourceID = $_POST["resource_id"];
    $startTime = $_POST["start_time"];
    $endTime = $_POST["end_time"];
    $date = $_POST["date"];
    $day = date('l',strtotime($date));
    $uid = $_SESSION["user_id"];
    $request_message = $_POST["request_message"];


    //Check whether timeslot already exists 
    $query = "SELECT * FROM time_slot WHERE START_TIME = '$startTime' AND END_TIME = '$endTime' AND DAY = '$day'";
    $result = $conn->query($query);

    $timeSlotID = null;
    
    if($result->num_rows<=0){
        $query = "INSERT INTO time_slot (START_TIME,END_TIME,DAY) VALUES ('$startTime','$endTime','$day')";
        $conn->query($query);
        $timeSlotID = $conn->insert_id;
    }else{
        $timeSlotID = $result->fetch_assoc()["ID"];
    }

    //Create new Request 
    $priority="0";
    switch($_SESSION["user_type"]){
        case "ADMIN":
            $priority = "2";
            break;
        case "LECTURER":
            $priority = "1";
            break;
        case "STUDENT":
            $priority = "0";
            break;

    }



    $query = "INSERT INTO request (EVENT_ID,RESOURCE_ID,TIME_SLOT_ID,REQUEST_DATE,REQUEST_APPROVED,USER_ID,PRIORITY,REQUEST_MESSAGE) VALUES ('$eventID','$resourceID','$timeSlotID','$date',0,$uid,'$priority','$request_message')";
    $result = $conn->query($query);
    if($result){
        echo "success";
    }else{
        echo "false";
    }


}



?>