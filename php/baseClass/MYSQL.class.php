<?php
class MYSQL{
    protected $db=null;
    private static $config=array(
        "host"=>"localhost",
        "port"=>3306,
        "name"=>"root",
        "password"=>"root",
        "charset"=>"utf8",
        "dbname"=>"david"
    );
    function __construct(){
        $this->db=MYSQLDB::GetInstance(self::$config);
    }
}