<?php
class Group{
    private $groupID;
    private $groupCreatorID;
    private $groupName;
    private $groupDateOfCreation;
    public function __construct($groupID, $groupCreatorID, $groupName, $groupDateOfCreation)
    {
        $this->groupID = $groupID;
        $this->groupCreatorID = $groupCreatorID;
        $this->groupName = $groupName;
        $this->groupDateOfCreation = $groupDateOfCreation;
    }
    public function __get($key)
    {
        if($key==="groupID") return $this->groupID;
        if($key==="groupCreatorID") return $this->groupCreatorID;
        if($key==="groupName") return $this->groupName;
        if($key==="groupDateOfCreation") return $this->groupDateOfCreation;
    }
    public function __set($key, $value)
    {
        if($key==="groupID") $this->groupID = $value;
        if($key==="groupCreatorID") $this->groupCreatorID = $value;
        if($key==="groupName") $this->groupName = $value;
        if($key==="groupDateOfCreation") $this->groupDateOfCreation = $value;
    }
}
?>