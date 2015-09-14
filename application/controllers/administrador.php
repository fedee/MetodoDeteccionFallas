<?php

class Administrador extends CI_Controller 
{

   public function __construct()
   {
      parent::__construct();
      $this->very_sesion();
      $this->load->model('usuarios_model');
      $this->load->model('casos_model');
   }

   public function index()
   {
        $this->load->helper('url');
   }

   public function activar_esp()
   {
        $this->load->helper('url');

        $datos = array(
         'titulo' => 'Panel de administrador',
         'nombrependientes' => $this->usuarios_model->devolver_esppendientesnombre(),
         'responsables' => $this->usuarios_model->devolver_esppendientesresp(),
         'correo' => $this->usuarios_model->devolver_esppendientescorreo(),
         'domicilio' => $this->usuarios_model->devolver_esppendientesdomicilio(),
         'telefono' => $this->usuarios_model->devolver_esppendientestelefono(),
         'fecharegistro' => $this->usuarios_model->devolver_esppendientesfecharegistro(),
         'id' => $this->usuarios_model->devolver_esppendientesid(),
        );

        $this->load->view('activarespecialistas.html',$datos);
   }

   public function asignar_casos()
   {
        $this->load->helper('url');

        $datosesp = array(
         'nombreactivados' => $this->usuarios_model->devolver_espactivadosnombre(),
         'responsables' => $this->usuarios_model->devolver_espactivadosresp(),
         'correo' => $this->usuarios_model->devolver_espactivadoscorreo(),
         'domicilio' => $this->usuarios_model->devolver_espactivadosdomicilio(),
         'telefono' => $this->usuarios_model->devolver_espactivadostelefono(),
         'fecharegistro' => $this->usuarios_model->devolver_espactivadosfecharegistro(),
         'idesp' => $this->usuarios_model->devolver_espactivadosid(),
         'titulo' => $this->casos_model->devolver_tituloscasossinasignar(),
         'descripcion' => $this->casos_model->devolver_descripcioncaso(),
         'fecharegistrocaso' => $this->casos_model->devolver_fecharegistrocaso(),
         'idcaso' => $this->casos_model->devolver_idscasossinasignar(),
        );

        $this->load->view('asignarcasos.html',$datosesp);
   }

   function very_sesion()
   {
      $this->load->helper('url');

   	  if(!$this->session->userdata('usuario'))
   	  {
   	  	redirect(site_url().'/usuarios/');
   	  }
   }

   function activar_especialista($id)
   {
      $this->usuarios_model->activar_especialista($id);
     
   }

}

?>