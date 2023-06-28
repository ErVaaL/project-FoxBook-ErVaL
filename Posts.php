<?php
class Posts{
    private $postSender;
    private $postTitle;
    private $postContents;
    private $postDate;
    private $postLikes;
    private $postEdited;
    public function __construct($postSender, $postTitle, $postContents,$postDate,$postLikes,$postEdited)
    {
        $this->postSender = $postSender;
        $this->postTitle = $postTitle;
        $this->postContents = $postContents;
        $this->postDate = $postDate;
        $this->postLikes = $postLikes;
        $this->postEdited = $postEdited;
    }
    public function __get($key)
    {
        if($key=="postSender") return $this->postSender;
        if($key=="postTitle") return $this->postTitle;
        if($key=="postContents") return $this->postContents;
        if($key=="postDate") return $this->postDate;
        if($key=="postLikes") return $this->postLikes;
        if($key=="postEdited") return $this->postEdited;
    }
    public function __set($key, $value)
    {
        if($key=="postSender") $this->postSender = $value;
        if($key=="postTitle") $this->postTitle = $value;
        if($key=="postContents") $this->postContents = $value;
        if($key=="postDate") $this->postDate = $value;
        if($key=="postLikes") $this->postLikes = $value;
        if($key=="postEdited") $this->postEdited = $value;
    }
}
?>