<?php

class Mensajes_model extends CI_Model {

   public function __construct()
   {
      parent::__construct();
   }

   public function num_mensajes() 
   {

      $idusuario = $this->session->userdata('id'); 
      $this->db->where("((id_paraquien='".$idusuario."' OR id_dequien='".$idusuario."') AND id_msjpadre='0')", NULL, FALSE);
      $this->db->from('mensajes');
      $cantidad = $this->db->count_all_results();
      return $cantidad;

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
        $this->db->select('leido');
        $this->db->where("((id_paraquien='".$idusuario."' OR id_dequien='".$idusuario."') AND id_msjpadre='0')", NULL, FALSE);
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
        $datos['leido'] = $row->leido;
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
                                        'leido' => '0',
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
      $this->db->select('leido');
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
                                        'leido' => '0',
                                        'fechaenvio' => $fechaactual,
                                        ));
      

   }

   public function insertar_nuevomensajeaesp($idesp,$idcaso)
   {
      $iddequien = $this->session->userdata('id');
      $nombreusuario = $this->devolver_nombreusuario($iddequien);
      $apellidousuario = $this->devolver_apellidousuario($iddequien);
      $nombredequien = $nombreusuario." ".$apellidousuario;

      $idparaquien = $idesp;

      $fechaactual = date('Y-m-d H:i:s');
      
      $this->db->insert('mensajes',array(
                                        'id_dequien'=> $iddequien,
                                        'nombre_dequien'=> $nombredequien,
                                        'id_paraquien'=> $idparaquien,
                                        'id_msjpadre' => '0',
                                        'titulo' => $this->input->post('titulomensaje',TRUE),
                                        'descripcion' => $this->input->post('cuerpomensaje',TRUE),
                                        'leido' => '0',
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

   public function actualizarestadomsj($idmensaje,$estado)
   {
      $data = array(
               'leido' => $estado,
            );

      $this->db->where('id', $idmensaje);
      $this->db->update('mensajes', $data); 
   }

   public function devolver_paraquien($idmensaje)
   {
      $consulta = $this->db->get_where('mensajes',array('id'=>$idmensaje));
      $row = $consulta->row(1);

      $idpq = $row->id_paraquien;

      return $idpq;
   }

   public function devolver_cantidadrespuestas($idmensaje) 
   {

      $this->db->where("(id_msjpadre='".$idmensaje."')", NULL, FALSE);
      $this->db->from('mensajes');
      $cantidad = $this->db->count_all_results();
      return $cantidad;

   }

   public function devolver_mensajessinleer()
   {
      $this->db->where('id_paraquien',$this->session->userdata('id'));
      $this->db->where('leido =','0');
      $this->db->where('id_msjpadre =','0');
      $this->db->from('mensajes');
      $cantidad = $this->db->count_all_results();
      return $cantidad;
   }


  
}

?>