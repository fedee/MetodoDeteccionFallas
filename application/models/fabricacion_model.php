<?php

class Fabricacion_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function devolver_todoslosprocesos()
   {

      $datos = $this->db->get('procesosfab_generales');
      return $datos->result_array();
   }

   public function devolver_subtiposporidproceso($idproceso)
   {
      $consulta = $this->db->get_where('procesosfab_especificos',array(
                                                         'id_procgeneral'=>$idproceso,
                                                       ));
      if ($consulta->num_rows() > 0) {
         $consulta = $consulta->result_array();
         return $consulta;
      }      
   }

   public function devolver_nombreprocesoporid($idproceso)
   {
      $consulta = $this->db->get_where('procesosfab_generales',array('id'=>$idproceso));
      $row = $consulta->row(1);
      $nombre = $row->nombre;
      return $nombre;
   }

   public function devolver_nombresubtipoporid($idsubtipo)
   {
      $consulta = $this->db->get_where('procesosfab_especificos',array('id'=>$idsubtipo));
      $row = $consulta->row(1);
      $nombre = $row->nombre;
      return $nombre;
   }

}

?>