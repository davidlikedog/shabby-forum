<?php

class msgDetailModel extends MYSQL{
    function getOneMsg($sql){
        $data=$this->db->getOneRow($sql);
        return $data;
    }
}