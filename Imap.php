<?php

class Imap{

    private $_password;

    public $host;
    public $user;
    public $inbox;
    public static $searchKeys = array(
        "ALL",
        "ANSWERED",
        "BCC",
        "BEFORE",
        "BODY",
        "CC",
        "DELETED",
        "FLAGGED",
        "FROM",
        "KEYWORD",
        "NEW",
        "OLD",
        "ON",
        "RECENT",
        "SEEN",
        "SINCE",
        "SUBJECT",
        "TEXT",
        "TO",
        "UNANSWERED",
        "UNDELETED",
        "UNFLAGGED",
        "UNKEYWORD",
        "UNSEEN"
    );

    public function __construct($host,$user,$password,$port = 143)
    {
        $this->user = $user;
        $this->_password = $password;
        $this->port = $port;
        $this->host = "{" . $host . ":" . $this->port . "/ssl}";
        $this->_connect();
    }
    protected function _connect(){
        $this->inbox = imap_open($this->host,$this->user,$this->_password);
        if(!$this->inbox){
            throw new Exception('Cannot open mail box');
        }
        return $this;
    }
    public function search($key = 'ALL',$query = ''){
        $queryStr = $key . " " . $query;
        $data = array();
        $results = \imap_search($this->inbox,$queryStr);
        if(!$results){
            return false;
        }
        foreach($results as $result){
            $data[] = \imap_fetch_overview($this->inbox,$result,0);
        }
        return $data;
    }
    public function getMsg($msgNum,$partNumber = null){
        $m = imap_fetchbody($this->inbox, $msgNum, $partNumber);
        return $m;
    }
    public function getMailBoxes(){
        $data = array();
        $results = \imap_list($this->inbox,$this->host,"*");
        $pattern = "/" . $this->host . "/";
        if(!is_array($results)){
            throw new Exception('Failed to gather mailbox list');
        }
        foreach($results as $result){
            $data[] = preg_replace($pattern,'',$result);
        }
        return $data;
    }
    public function getHeaders($id){
      return imap_fetchheader($this->inbox, $id);
    }
    public function getMime($id){
      return imap_fetchmime($this->inbox,$id,FT_PEEK);
    }
}
