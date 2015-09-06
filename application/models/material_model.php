<?php

class Material_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function devolver_todoslosmateriales()
   {

      $datos = $this->db->get('material');
      return $datos->result_array();
   }

   public function devolver_submaterialesporidmaterial($idmaterial)
   {
      $consulta = $this->db->get_where('submaterial',array(
                                                         'id_material'=>$idmaterial,
                                                       ));
	if ($consulta->num_rows() > 0) {
		$consulta = $consulta->result_array();
		return $consulta;
	}      
   }

   public function devolver_matespporidsubmaterial($idsubmaterial)
   {
      $consulta = $this->db->get_where('material_especifico',array(
                                                         'id_submaterial'=>$idsubmaterial,
                                                       ));
	if ($consulta->num_rows() > 0) {
		$consulta = $consulta->result_array();
		return $consulta;
	}      
   }

   public function devolver_propmatesp($idmatesp)
   {
      $consulta = $this->db->get_where('material_especifico',array(
                                                         'id'=>$idmatesp,
                                                       ));
   if ($consulta->num_rows() > 0) {
      $consulta = $consulta->result_array();
      return $consulta;
   }      
   }

   public function devolver_nombrematsubesp($id,$tabla)
   {
      $consulta = $this->db->get_where($tabla,array('id'=>$id));
      $row = $consulta->row(1);
      $nombre = $row->nombre;
      return $nombre;
   }

}

?>