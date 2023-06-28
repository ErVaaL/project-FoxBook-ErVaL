<?php
class Event{
    private $eventID;
    private $eventGroup;
    private $eventCreator;
    private $eventName;
    private $eventDate;
    private $eventDescription;
    public function __construct($eventID, $eventGroup, $eventCreator, $eventName, $eventDate, $eventDescription)
    {
        $this->eventID = $eventID;
        $this->eventGroup = $eventGroup;
        $this->eventCreator = $eventCreator;
        $this->eventName = $eventName;
        $this->eventDate = $eventDate;
        $this->eventDescription = $eventDescription;
    }
    public function __get($key)
    {
        if($key === "eventID") return $this->eventID;
        if($key === "eventGroup") return $this->eventGroup;
        if($key === "eventCreator") return $this->eventCreator;
        if($key === "eventName") return $this->eventName;
        if($key === "eventDate") return $this->eventDate;
        if($key === "eventDescription") return $this->eventDescription;
     }
    public function __set($key, $value)
    {
        if($key === "eventID") $this->eventID = $value;
        if($key === "eventGroup") $this->eventGroup = $value;
        if($key === "eventCreator") $this->eventCreator = $value;
        if($key === "eventName") $this->eventName = $value;
        if($key === "eventDate") $this->eventDate = $value;
        if($key === "eventDescription") $this->eventDescription = $value;
    }


}
?>