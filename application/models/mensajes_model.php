<?php

class Mensajes_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function num_mensajes() 
   {
      $idusuario = $this->session->userdata('id');

      $consulta = $this->db->get_where('mensajes',array(
                                                         'id_paraquien'=>$idusuario,
                                                         'id_msjpadre'=>'0',
                                                       ));

      return $consulta->num_rows();
   }

   public function get_mensajes($per_page) 
   {
        $idusuario = $this->session->userdata('id');

        $this->db->select('id');
        $this->db->select('fechaenvio');
        $this->db->select('id_dequien');
        $this->db->select('nombre_dequien');
        $this->db->select('id_paraquien');
        $this->db->select('id_msjpadre');
        $this->db->select('titulo');
        $this->db->select('descripcion');
        $this->db->where('id_paraquien', $idusuario);
        $this->db->where('id_msjpadre', '0'); 
        $this->db->order_by('fechaenvio', 'desc');

   	  $datos = $this->db->get('mensajes',$per_page,$this->uri->segment(3));
   	  return $datos->result_array();
   }


   public function devolver_todosobremsj($idmensaje)
   {
      $consulta = $this->db->get_where('mensajes',array('id'=>$idmensaje));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos['id'] = $row->id;
        $datos['id_dequien'] = $row->id_dequien;
        $datos['nombre_dequien'] = $row->nombre_dequien;
        $datos['id_paraquien'] = $row->id_paraquien;
        $datos['titulo'] = $row->titulo;
        $datos['descripcion'] = $row->descripcion;
        $datos['fechaenvio'] = $row->fechaenvio;
      }
      return $datos;
   }

   public function insertar_respuesta($idpadre,$idparaquien)
   {
      $iddequien = $this->session->userdata('id');
      $nombreusuario = $this->devolver_nombreusuario($iddequien);
      $apellidousuario = $this->devolver_apellidousuario($iddequien);
      $nombredequien = $nombreusuario." ".$apellidousuario;
      $fechaactual = date('Y-m-d H:i:s');
      
      $this->db->insert('mensajes',array(
                                        'id_dequien'=> $iddequien,
                                        'nombre_dequien'=> $nombredequien,
                                        'id_paraquien'=> $idparaquien,
                                        'id_msjpadre' => $idpadre,
                                        'descripcion' => $this->input->post('respuesta',TRUE),
                                        'fechaenvio' => $fechaactual,
                                        ));
      

   }

   public function devolver_nombreusuario($idusuario)
   {
      $consulta = $this->db->get_where('usuarios',array('id'=>$idusuario));
      $row = $consulta->row(1);

      $nom = $row->nombre;

      return $nom;
   }

   public function devolver_apellidousuario($idusuario)
   {
      $consulta = $this->db->get_where('usuarios',array('id'=>$idusuario));
      $row = $consulta->row(1);

      $ap = $row->apellido;

      return $ap;
   }

   public function devolver_respuestasamsj($idpadre)
   {
      $this->db->select('nombre_dequien');
      $this->db->select('descripcion');
      $this->db->select('fechaenvio');
      $this->db->where('id_msjpadre', $idpadre); 
      $this->db->order_by('fechaenvio', 'asc');
      $consulta = $this->db->get('mensajes');
      
      return $consulta->result_array();
   }

   public function insertar_nuevomensaje($idcaso)
   {
      $iddequien = $this->session->userdata('id');
      $nombreusuario = $this->devolver_nombreusuario($iddequien);
      $apellidousuario = $this->devolver_apellidousuario($iddequien);
      $nombredequien = $nombreusuario." ".$apellidousuario;

      $idparaquien = $this->devolver_idusuarioporidcaso($idcaso);

      $fechaactual = date('Y-m-d H:i:s');
      
      $this->db->insert('mensajes',array(
                                        'id_dequien'=> $iddequien,
                                        'nombre_dequien'=> $nombredequien,
                                        'id_paraquien'=> $idparaquien,
                                        'id_msjpadre' => '0',
                                        'titulo' => $this->input->post('titulomensaje',TRUE),
                                        'descripcion' => $this->input->post('cuerpomensaje',TRUE),
                                        'fechaenvio' => $fechaactual,
                                        ));
      

   }

   public function devolver_idusuarioporidcaso($idcaso)
   {
      $consulta = $this->db->get_where('casos',array('id'=>$idcaso));
      $row = $consulta->row(1);

      $idu = $row->id_usuario;

      return $idu;
   }


  
}

?>