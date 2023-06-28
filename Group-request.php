<?php
class GroupRequest extends Notification {
    private $requestStatus;
    private $requestMessage;

    public function __construct($requestID, $fromWho, $toWho,$dateOfCreation,$requestStatus,$requestMessage)
    {
        parent::__construct($requestID, $fromWho, $toWho,$dateOfCreation);
        $this->requestStatus = $requestStatus;
        $this->requestMessage = $requestMessage;
    }
    public function __get($key)
    {
        if($key==="requestID") return $this->requestID;
        if($key==="fromWho") return $this->fromWho;
        if($key==="toWho") return $this->toWho;
        if($key==="requestStatus") return $this->requestStatus;
        if($key==="requestMessage") return $this->requestMessage;
    }
    public function __set($key, $value)
    {
        if($key==="requestID") $this->requestID = $value;
        if($key==="fromWho")  $this->fromWho = $value;
        if($key==="toWho") $this->toWho = $value;
        if($key==="requestStatus") $this->requestStatus = $value;
        if($key==="requestMessage") $this->requestMessage = $value;
    }
}
?>