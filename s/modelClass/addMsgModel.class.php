<?php

class addMsgModel extends MYSQL{
    function addMsg($sql){
        $data=$this->db->exec($sql);
        return $data;
    }
    function getId($sql){
        $data=$this->db->getOneData($sql);
        return $data;
    }
}