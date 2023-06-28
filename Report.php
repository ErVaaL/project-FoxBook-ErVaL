<?php
class Report{
    private $reportID;
    private $reportedUser;
    private $reportingUser;
    private $reportDate;
    private $reportType;
    private $reportDescription;
    public function __construct($reportID, $reportedUser, $reportingUser, $reportDate, $reportType, $reportDescription)
    {
        $this->reportID = $reportID;
        $this->reportedUser = $reportedUser;
        $this->reportingUser = $reportingUser;
        $this->reportDate = $reportDate;
        $this->reportType = $reportType;
        $this->reportDescription = $reportDescription;
    }
    public function __get($key)
    {
       if($key==="reportID") return $this->reportID;
       if($key==="reportedUser") return $this->reportedUser;
       if($key==="reportingUser") return $this->reportingUser;
       if($key==="reportDate") return $this->reportDate;
       if($key==="reportType") return $this->reportType;
       if($key==="reportDescription") return $this->reportDescription;
    }
    public function __set($key, $value)
    {
        if($key==="reportID") $this->reportID = $value;
        if($key==="reportedUser") $this->reportedUser = $value;
        if($key==="reportingUser") $this->reportingUser = $value;
        if($key==="reportDate") $this->reportDate = $value;
        if($key==="reportType") $this->reportType = $value;
        if($key==="reportDescription") $this->reportDescription = $value;
    }
}
?>