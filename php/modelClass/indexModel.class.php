<?php

class indexModel extends MYSQL{
    function getAllMsg($sql){
        $data=$this->db->getRows($sql);
        return $data;
    }
}