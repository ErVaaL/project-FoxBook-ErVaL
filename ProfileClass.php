<?php
include_once "fordatabaseconnection.php";
class Profile{

    private $showBirthDate;
    private $showAddress;
    private $seeProfile;
    private $address;

    public function __construct($showBirthDate,$showAddress,$seeProfile,$address){
        $this->showBirthDate = $showBirthDate;
        $this->showAddress = $showAddress;
        $this->seeProfile = $seeProfile;
        $this->address = $address;
    }
    public function __get($key)
    {
        if($key === "showBirthDate") return $this->showBirthDate;
        if($key === "showAddress") return $this->showAddress;
        if($key === "seeProfile") return $this->seeProfile;
        if($key === "address") return $this->address;
    }
    public function __set($key, $value)
    {
        if($key === "showBirthDate") $this->showBirthDate = $value;
        if($key === "showAddress") $this->showAddress = $value;
        if($key === "seeProfile") $this->seeProfile = $value;
        if($key === "address") $this->address = $value;
    }
}
?>