<?php
class Message extends Notification{
    private $messageContents;
    private $messageVisibility;
    public function __construct($reqestID, $fromWho, $toWho,$dateOfCreation,$messageContents,$messageVisibility)
    {
        parent::__construct($reqestID, $fromWho, $toWho,$dateOfCreation);
        $this->messageContents= $messageContents;
        $this->messageVisibility = $messageVisibility;
    }
    public function __get($key)
    {
        if($key==="requestID") return $this->requestID;
        if($key==="fromWho") return $this->fromWho;
        if($key==="toWho") return $this->toWho;
        if($key==="messageTitle") return $this->messageTitle;
        if($key==="messageContents") return $this->messageContents;
        if($key==="messageVisibility") return $this->messageVisibility;
        if($key==="dateOfCreation") return $this->dateOfCreation;
    }
    public function __set($key, $value)
    {
        if($key==="requestID") $this->requestID = $value;
        if($key==="fromWho")  $this->fromWho = $value;
        if($key==="toWho") $this->toWho = $value;
        if($key==="messageTitle") $this->messageTitle = $value;
        if($key==="messageContents") $this->messageContents = $value;
        if($key==="messageVisibility") $this->messageVisibility = $value;
        if($key==="dateOfCreation") $this->dateOfCreation = $value;
    }
}
?>