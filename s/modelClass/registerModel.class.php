<?php

class registerModel extends MYSQL{
    function verifyName($sql){
        $data=$this->db->getOneData($sql);
        return $data;
    }
    function addOne($sql){
        $data=$this->db->exec($sql);
        return $data;
    }
}