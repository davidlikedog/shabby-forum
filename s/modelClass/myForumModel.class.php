<?php

class myForumModel extends MYSQL{
    function getMyMsg($sql){
        $data=$this->db->getRows($sql);
        return $data;
    }
    function delOne($sql){
        $data=$this->db->exec($sql);
        return $data;
    }
    function modifyOne($sql){
        $data=$this->db->exec($sql);
        return $data;
    }
    function getModifyData($sql){
        $data=$this->db->getOneRow($sql);
        return $data;
    }
    function getOneMsgDetail($sql){
        $data=$this->db->getOneRow($sql);
        return $data;
    }
    function getOneData($sql){
        $data=$this->db->getOneData($sql);
        return $data;
    }
}