<?php

require_once "../../database/connection.php";
require_once "../util/checkAvailability.php";

if(isset($_GET["search"])){
    $search = $_GET["search"];
    $limit = 2;
    if(isset($_GET["limit"])){
        $limit = $_GET["limit"];
    }
    $subSql='LIMIT 3';
    if(isset($_GET["page"])){
        $page = $_GET["page"];
        $offset = ($page-1)*$limit;
        $subSql = "LIMIT $limit OFFSET $offset;";
    }

    $query = "SELECT * FROM EVENTS WHERE EVENT_NAME LIKE '%$search%' OR EVENT_TYPE LIKE '%$search%' OR CONDUCT_BY LIKE '%$search%' $subSql";
    $result = $conn->query($query);



    $count = "SELECT count(*) FROM EVENTS WHERE EVENT_NAME LIKE '%$search%' OR EVENT_TYPE LIKE '%$search%' OR CONDUCT_BY LIKE '%$search%'";
    $events = array();
    $response = array();
    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            array_push($events,$row);
        }
    }
    $response["data"] = $events;
    if(isset($page)){
        $response["total"] = $conn->query($count)->fetch_assoc()["count(*)"];
        $response["page"]=$page;
        $response["offset"] = ($page-1)*$limit;
    }
    echo json_encode($response);
    return;
}
if(isset($_GET["id"]) && isset($_GET["all"])){
    $id = $_GET["id"];
    $query = "SELECT * FROM EVENTS 
       INNER JOIN OCCUPIED ON EVENTS.ID = OCCUPIED.EVENT_ID
       INNER JOIN RESOURCE ON OCCUPIED.RESOURCE_ID = RESOURCE.ID
       INNER JOIN TIME_SLOT ON OCCUPIED.TIME_SLOT_ID = TIME_SLOT.ID
       WHERE EVENTS.ID = '$id'
    ";
    
    $result = $conn->query($query);
    $response = array();
    $allocations = array();
    while($event = $result->fetch_assoc()){
        $allocations[] = $event;
    };


    $response["data"] = $allocations;
    echo json_encode($response);
    return;

}

if(isset($_GET["id"])){
    $id = $_GET["id"];
    $query = "SELECT * FROM EVENTS WHERE ID = '$id'";
    $result = $conn->query($query);
    
    $event = $result->fetch_assoc();
    $response = array();
    $response["data"] = $event;
    echo json_encode($response);
    return;
}

if(isset($_GET["new"])){

    $data = json_decode(file_get_contents("php://input"),true);

    $eventName = $data["event_name"];
    $eventType = $data["event_type"];
    $conductBy = $data["conduct_by"];
    $optionalDetails = $data["optional_details"];

    print_r($eventName);

    
    $query = "INSERT INTO EVENTS(EVENT_NAME,EVENT_TYPE,CONDUCT_BY,OPTIONAL_DETAILS) VALUES('$eventName','$eventType','$conductBy','$optionalDetails')";

    print_r($query);

    if($conn->query($query)){
        echo json_encode("success");
    }else{
        echo json_encode("failed");
    }
    return;
}


if(isset($_GET["allocation"])){

    $eventID = $_GET["eventID"];

    $query = "SELECT * FROM OCCUPIED WHERE  EVENT_ID = '$eventID' GROUP BY RESOURCE_ID;";
    
    $result = $conn->query($query);

    $response = array();
    $resources = array();

    while($row = $result->fetch_assoc()){
        $resourceID = $row["RESOURCE_ID"];

        $queryResource = "SELECT * FROM OCCUPIED 
        INNER JOIN TIME_SLOT ON OCCUPIED.TIME_SLOT_ID = TIME_SLOT.ID
        WHERE RESOURCE_ID = '$resourceID' AND EVENT_ID = '$eventID'";

        $resultResource = $conn->query($queryResource);

        $resource = array();
        $resource["resource_id"] = $resourceID;
        $resource["resource_name"] = $conn->query("SELECT * FROM RESOURCE WHERE ID = '$resourceID'")->fetch_assoc()["RESOURCE_NAME"];
        $resource["allocations"] = array();
        while($rowResource = $resultResource->fetch_assoc()){
            $resource["allocations"][] = $rowResource;
        }
        $resources[] = $resource;
    }

    echo json_encode($resources);
 
    return;
}

if(isset($_GET["deallocate"])){
    $data = json_decode(file_get_contents("php://input"),true);
    $eventID = $data["eventID"];
    $resourceID = $data["resourceID"];
    $timeSlotID = $data["timeSlotID"];

    $query = "DELETE FROM OCCUPIED WHERE EVENT_ID = '$eventID' AND RESOURCE_ID = '$resourceID' AND TIME_SLOT_ID = '$timeSlotID'";

    $result = $conn->query($query);

    if($result){
        echo "success";
    }else{
        echo "false";
    }

    return;
}

if(isset($_GET["deactivate"])){
    $data = json_decode(file_get_contents("php://input"),true);
    $eventID = $data["eventID"];
    $resourceID = $data["resourceID"];
    $timeSlotID = $data["timeSlotID"];
    $activateType = $data["activateType"];


    $res = $conn->query("SELECT * FROM TIME_SLOT WHERE ID = '$timeSlotID'")->fetch_assoc();
    $start = $res["START_TIME"];
    $end = $res["END_TIME"];
    $date = $res["DAY"];



    if($activateType == "0"){
        $activateType = 1;
        $overlap = isAvailable($conn,$start,$end,$date,$resourceID);
        if(!empty($overlap)){
            echo "Time Slot Overlap Detected";
            return;
        }

    }else{
        $activateType = 0;
    }

    $query = "UPDATE OCCUPIED SET ACTIVE = $activateType WHERE EVENT_ID = '$eventID' AND RESOURCE_ID = '$resourceID' AND TIME_SLOT_ID = '$timeSlotID'";

    $result = $conn->query($query);

    if($result){
        echo "success";
    }else{
        echo "false";
    }

    return;
}



if(isset($_GET['delete'])){
    $eventID = $_GET["eventID"];
    $queryOccupied = "DELETE FROM OCCUPIED WHERE EVENT_ID = '$eventID'";
    $resultOccupied = $conn->query($queryOccupied);
    $queryRequest = "DELETE FROM REQUEST WHERE EVENT_ID = '$eventID'";
    $resultRequest = $conn->query($queryRequest);

    $query = "DELETE FROM EVENTS WHERE ID = '$eventID'";
    $result = $conn->query($query);

    if($result && $resultOccupied && $resultRequest){
        echo "success";
    }else{
        echo "false";
    }
    return;
}


if(isset($_GET["edit"])){
    $data = json_decode(file_get_contents("php://input"),true);
    $eventID = $data["eventID"];
    $eventName = $data["event_name"];
    $eventType = $data["event_type"];
    $conductBy = $data["conduct_by"];
    $optionalDetails = $data["optional_details"];

    $query = "UPDATE EVENTS SET EVENT_NAME = '$eventName', EVENT_TYPE = '$eventType', CONDUCT_BY = '$conductBy', OPTIONAL_DETAILS = '$optionalDetails' WHERE ID = '$eventID'";

    $result = $conn->query($query);

    if($result){
        echo "success";
    }else{
        echo "false";
    }

    return;
}




?>