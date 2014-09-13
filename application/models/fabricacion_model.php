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

   public function devolver_cantidadcampos($idproceso)
   {
      $consulta = $this->db->like('id_proceso', $idproceso);
      $this->db->from('atributos');
      return $this->db->count_all_results();
   }

   public function devolver_todoslosatributos($idproceso)
   {

      $consulta = $this->db->get_where('atributos',array(
                                                         'id_proceso'=>$idproceso,
                                                       ));
      if ($consulta->num_rows() > 0) {
         $consulta = $consulta->result_array();
         return $consulta;
      }
   }

   public function devolver_atributosaprecargar($idatributo)
   {

      $consulta = $this->db->get_where('atributos_precargar',array(
                                                         'id_atributo'=>$idatributo,
                                                       ));
      if ($consulta->num_rows() > 0) {
         $consulta = $consulta->result_array();
         return $consulta;
      }
   }

   public function devolver_cantidadprecargados($idatributo)
   {
      $consulta = $this->db->like('id_atributo', $idatributo);
      $this->db->from('atributos_precargar');
      return $this->db->count_all_results();
   }

   public function devolver_cantidadcampossubtipo($idsubtipo)
   {
      $consulta = $this->db->like('id_subtipo', $idsubtipo);
      $this->db->from('atributos');
      return $this->db->count_all_results();
   }

   public function devolver_atributosespecificos($idsubtipo)
   {

      $consulta = $this->db->get_where('atributos',array(
                                                         'id_subtipo'=>$idsubtipo,
                                                       ));
      if ($consulta->num_rows() > 0) {
         $consulta = $consulta->result_array();
         return $consulta;
      }
   }

   public function devolver_precargadoporid($idprecargado)
   {
      $consulta = $this->db->get_where('precargados',array('id'=>$idprecargado));
      $row = $consulta->row(1);
      $nombre = $row->nombre_atributo;
      return $nombre;
   }

}

?>