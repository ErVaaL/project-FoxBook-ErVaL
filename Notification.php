<?php
abstract class Notification {
    protected $requestID;
    protected $fromWho;
    protected $toWho;
    protected $dateOfCreation;
    public function __construct($requestID, $fromWho, $toWho,$dateOfCreation)
    {
        $this->requestID = $requestID;
        $this->fromWho = $fromWho;
        $this->toWho = $toWho;
        $this->dateOfCreation = $dateOfCreation;
    }

    public function __get($key)
    {
        if($key==="requestID") return $this->requestID;
        if($key==="fromWho") return $this->fromWho;
        if($key==="toWho") return $this->toWho;
        if($key==="dateOfCreation") return $this->dateOfCreation;
    }

    public function __set($key, $value)
    {
        if($key==="requestID") $this->requestID = $value;
        if($key==="fromWho")  $this->fromWho = $value;
        if($key==="toWho") $this->toWho = $value;
        if($key==="dateOfCreation") $this->dateOfCreation = $value;
    }

}
?>