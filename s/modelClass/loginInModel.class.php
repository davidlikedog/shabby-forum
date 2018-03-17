<?php
class loginInModel extends MYSQL{
    function verify($sql){
        $data=$this->db->getOneData($sql);
        return $data;
    }
}