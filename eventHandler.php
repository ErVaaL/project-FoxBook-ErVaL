<?php
$allEvents = getAllEvents();
echo getEventStatus(1);
foreach ($allEvents as $object) {
    date_default_timezone_set("Europe/Warsaw");
    $currentDate = date("Y-m-d H:i");
    if($currentDate > date("Y-m-d H:i",strtotime($object->eventDate)) && getEventStatus($object->eventID) != 2){
            setEventPassed($object->eventID);
    }else if($currentDate < date("Y-m-d H:i:s",strtotime($object->eventDate))  && getEventStatus($object->eventID) == 2){
        setEventActive($object->eventID);
    }
}
?>