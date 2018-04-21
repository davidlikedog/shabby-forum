<?php

class msgDetailModel extends MYSQL{
    function getOneMsg($sql){
        $data=$this->db->getOneRow($sql);
        return $data;
    }
    function addOne($sql){
        $data=$this->db->exec($sql);
        return $data;
    }
    function getOneData($sql){
        $data=$this->db->getOneData($sql);
        return $data;
    }
}