<?php

class DashboardAdmin extends CI_Controller 
{

   public function __construct()
   {
      parent::__construct();
      $this->very_sesion();
      $this->load->model('usuarios_model');
   }

   public function index()
   {
   	  //echo "Hola usuario especialista: ".$this->session->userdata('usuario');
        $this->load->helper('url');
        $this->load->view('indexadmin.html');
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
      $this->load->helper('url');

      $this->usuarios_model->activar_especialista($id);
   }

}

?>