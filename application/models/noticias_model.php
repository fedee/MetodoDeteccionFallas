<?php

class Noticias_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function num_noticias() 
   {
      return $this->db->get('noticias')->num_rows();
   }

   public function get_noticias($per_page) 
   {
   	  $datos = $this->db->get('noticias',$per_page,$this->uri->segment(3));
   	  return $datos->result_array();
   }

  
}

?>