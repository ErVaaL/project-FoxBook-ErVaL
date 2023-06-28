<?php
class Alert extends Notification{
    private $alertSeen;
    private $alertMessage;

    public function __construct($requestID, $fromWho, $toWho, $dateOfCreation,$alertSeen,$alertMessage)
    {
        parent::__construct($requestID, $fromWho, $toWho, $dateOfCreation);
        $this->alertSeen = $alertSeen;
        $this->alertMessage = $alertMessage;
    }

    public function __get($key)
    {
        if($key==="requestID") return $this->requestID;
        if($key==="fromWho") return $this->fromWho;
        if($key==="toWho") return $this->toWho;
        if($key==="dateOfCreation") return $this->dateOfCreation;
        if($key ==="alertSeen") return $this->alertSeen;
        if($key === "alertMessage") return $this->alertMessage;

    }
    public function __set($key, $value)
    {
        if($key==="requestID") $this->requestID = $value;
        if($key==="fromWho")  $this->fromWho = $value;
        if($key==="toWho") $this->toWho = $value;
        if($key==="dateOfCreation") $this->dateOfCreation = $value;
        if($key ==="alertSeen") $this->alertSeen = $value;
        if($key==="alertMessage") $this->alertMessage = $value;
    }

}
?>