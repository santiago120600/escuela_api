<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class DAO extends CI_Model{

    function __construct(){
        parent::__construct();
    }

    function selectEntity($entityName, $whereClause= array(),$isUnique = FALSE){
        if ($whereClause) {
            $this->db->where($whereClause);
        }
        $query = $this->db->get($entityName);
        if ($isUnique) {
           return $query->row();
        }else{
            return $query->result();
        }
    }

    function saveOrUpdate($entityName,$data,$whereClause = array()){
        if ($whereClause) {
            $this->db->where($whereClause);
            $this->db->update($entityName,$data);
        }else{
            $this->db->insert($entityName,$data);
        }
    }

    function deleteItemEntity($entityName,$whereClause){
        $this->db->where($whereClause);
        $this->db->delete($entityName);
    }

    

}