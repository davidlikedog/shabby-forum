<?php
class MYSQLDB{
    private $link;
    private $host;
    private $port;
    private $name;
    private $password;
    private $charset;
    private $dbname;

    private static $instance=null;
    static function GetInstance($config){
        if(!isset(self::$instance)){
            self::$instance=new self($config);
        }
        return self::$instance;
    }

    private function __clone(){}

    private function __construct($config){
        $this->host=!empty($config['host'])?$config['host']:'localhost';
        $this->port=!empty($config['port'])?$config['port']:'3306';
        $this->name=!empty($config['name'])?$config['name']:'root';
        $this->password=!empty($config['password'])?$config['password']:'root';
        $this->charset=!empty($config['charset'])?$config['charset']:'utf8';
        $this->dbname=!empty($config['dbname'])?$config['dbname']:'david';

        $this->link= mysql_connect("$this->host:$this->port","$this->name","$this->password") or die();

        $this->setCharset($this->charset);

        $this->chooseDB($this->dbname);
    }
    function setCharset($charset){
        mysql_query("set names $charset",$this->link);
    }
    function chooseDB($dbName){
        mysql_query("use $dbName",$this->link);
    }
    function closeDB(){
        mysql_close($this->link);
    }

    function getRows($sql){
        $result=$this->query($sql);
        $arr=array();
        while ( $rec=mysql_fetch_assoc($result) ){
            $arr[]=$rec;
        }
        mysql_free_result($result);//提前释放结果集
        return $arr;
    }
    function exec($sql){
        $result=$this->query($sql);
        return true;
    }
    function getOneRow($sql){
        $result=$this->query($sql);
        $rec=mysql_fetch_assoc($result);
        mysql_free_result($result);
        return $rec;
    }
    function getOneData($sql){
        $result=$this->query($sql);
        $rec=mysql_fetch_row($result);
        $data=$rec[0];
        mysql_free_result($result);
        return $data;
    }
    private function query($sql){
        $result=mysql_query($sql,$this->link);
        if($result===false){
            echo "<p>sql语句执行失败，请参考如下信息：";
            echo "<br/>错误代号：".mysql_errno();
            echo "<br/>错误信息：".mysql_error();
            echo "<br/>错误语句：".$sql;
            echo "</p>";
            die();
        }
        return $result;
    }
}



