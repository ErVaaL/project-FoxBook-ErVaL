<?php
trait getUserCredentials{
    public function getCredentials($object){
        return $object->userFirstName.' '.$object->userLastName.'</br></br>'.$object->userUsername;
    }
}
class User {
    private $userUsername;
    private $userEmail;
    private $userDateOfBirth;
    private $userPassword;
    private $userFirstName;
    private $userLastName;
    public function __construct($userUsername, $userEmail, $userDateOfBirth, $userPassword, $userFirstName, $userLastName)
    {
        $this->userUsername = $userUsername;
        $this->userEmail = $userEmail;
        $this->userDateOfBirth = $userDateOfBirth;
        $this->userPassword = $userPassword;
        $this->userFirstName = $userFirstName;
        $this->userLastName = $userLastName;
    }
    public function __get($key)
    {
        if($key === "userEmail") return $this->userEmail;
        if($key === "userDateOfBirth") return $this->userDateOfBirth;
        if($key === "userUsername") return $this->userUsername;
        if($key === "userPassword") return $this->userPassword;
        if($key === "userFirstName") return $this->userFirstName;
        if($key === "userLastName") return $this->userLastName;

    }
    public function __set($key, $value)
    {
        if($key === "userEmail") $this->userEmail = $value;
        if($key === "userDateOfBirth") $this->userDateOfBirth = $value;
        if($key === "userUsername") $this->userUsername = $value;
        if($key === "userPassword") $this->userPassword = $value;
        if($key === "userFirstName") $this->userFirstName = $value;
        if($key === "userLastName") $this->userLastName = $value;
    }
    use getUserCredentials;
}
?>