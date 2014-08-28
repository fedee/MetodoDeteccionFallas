<?php

class DashboardUsuarioComun extends CI_Controller 
{

   public function __construct()
   {
      parent::__construct();
      $this->very_sesion();
   }

   public function index()
   {
   	  $this->load->helper('url');
        $this->load->view('indexusuariocomun.html');
   }

   function very_sesion()
   {
      $this->load->helper('url');

   	  if(!$this->session->userdata('usuario'))
   	  {
   	  	redirect(site_url().'/usuarios/');
   	  }
   }

}

?>