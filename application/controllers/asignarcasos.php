<?php

class AsignarCasos extends CI_Controller 
{

   public function __construct()
   {
      parent::__construct();
   }

   public function index()
   {
   	  //echo "Hola usuario especialista: ".$this->session->userdata('usuario');
        $this->load->helper('url');
        $this->load->view('forms.html');
   }

}

?>