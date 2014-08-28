<?php
class Usuarios_model extends CI_Model{

   function __construct(){
      parent::__construct();
   }

   public function very($variable,$campo)
   {
      $consulta = $this->db->get_where('usuarios',array($campo=>$variable));
      
      if($consulta->num_rows() == 1)
      {
         return true;
      }
      else
      {
         return false;
      }
   }

   public function add_usuariocomun()
   {
      $this->db->insert('usuarios',array(
                                          'nombre'=>$this->input->post('nombre',TRUE),
                                          'apellido'=>$this->input->post('apellido',TRUE),
                                          'correo'=>$this->input->post('correo',TRUE),
                                          'domicilio'=>$this->input->post('domicilio',TRUE),
                                          'telefono'=>$this->input->post('telefono',TRUE),
                                          'usuario'=>$this->input->post('usuario',TRUE),
                                          'password'=>$this->input->post('pass',TRUE),
                                          'activo'=>'1',
                                          'tipo'=>'2'

                                          ));
   }

   public function add_usuarioespecialista()
   {
      $this->db->insert('usuarios',array(
                                          'nombre_empresa'=>$this->input->post('nombreempresa',TRUE),
                                          'responsable_uso'=>$this->input->post('respuso',TRUE),
                                          'correo'=>$this->input->post('correo',TRUE),
                                          'domicilio'=>$this->input->post('domicilio',TRUE),
                                          'telefono'=>$this->input->post('telefono',TRUE),
                                          'usuario'=>$this->input->post('usuario',TRUE),
                                          'password'=>$this->input->post('pass',TRUE),
                                          'activo'=>'0',
                                          'tipo'=>'1'

                                          ));
   }

   public function very_sesion($user)
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'usuario'=>$this->input->post('usuario',TRUE),
                                                         'password'=>$this->input->post('pass',TRUE)
                                                       ));
      if($consulta->num_rows() > 0)
      {
         return true;
      }
      else
      {
         return false;
      }
   }

   public function devolver_tipo($variable)
   {
      $consulta = $this->db->get_where('usuarios',array('usuario'=>$variable));
      $row = $consulta->row(1);
      $tipo = $row->tipo;

      return $tipo;
   }

   public function devolver_activo($variable)
   {
      $consulta = $this->db->get_where('usuarios',array('usuario'=>$variable));
      $row = $consulta->row(1);
      $activo = $row->activo;

      return $activo;
   }

   public function devolver_id($variable)
   {
      $consulta = $this->db->get_where('usuarios',array('usuario'=>$variable));
      $row = $consulta->row(1);
      $id = $row->id;

      return $id;
   }

   public function devolver_esppendientesnombre()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'0'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->nombre_empresa;
      }

      return $datos;
   }

   public function devolver_esppendientesresp()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'0'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->responsable_uso;
      }

      return $datos;
   }

   public function devolver_esppendientescorreo()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'0'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->correo;
      }

      return $datos;
   }

   public function devolver_esppendientesdomicilio()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'0'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->domicilio;
      }

      return $datos;
   }

   public function devolver_esppendientestelefono()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'0'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->telefono;
      }

      return $datos;
   }

   public function devolver_esppendientesfecharegistro()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'0'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->fecha_alta;
      }

      return $datos;
   }

   public function devolver_esppendientesid()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'0'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->id;
      }

      return $datos;
   }


   public function activar_especialista($id)
   {
      $data = array(
               'activo' => '1',
            );

      $this->db->where('id', $id);
      $this->db->update('usuarios', $data); 
      redirect(site_url().'/administrador/activar_esp');
   }

   public function devolver_espactivadosnombre()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'1'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->nombre_empresa;
      }

      return $datos;
   }

   public function devolver_espactivadosresp()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'1'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->responsable_uso;
      }

      return $datos;
   }

   public function devolver_espactivadoscorreo()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'1'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->correo;
      }

      return $datos;
   }

   public function devolver_espactivadosdomicilio()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'1'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->domicilio;
      }

      return $datos;
   }

   public function devolver_espactivadostelefono()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'1'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->telefono;
      }

      return $datos;
   }

   public function devolver_espactivadosfecharegistro()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'1'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->fecha_alta;
      }

      return $datos;
   }


   public function devolver_espactivadosid()
   {

      $consulta = $this->db->get_where('usuarios',array(
                                                         'tipo'=>'1',
                                                         'activo'=>'1'
                                                       ));
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->id;
      }

      return $datos;
   }
   
}
?>
