<?php

class DashboardUsuarioComun extends CI_Controller 
{

   public function __construct()
   {
      parent::__construct();
      $this->very_sesion();
      $this->load->model('casos_model');
      $this->load->model('mensajes_model');
   }

   public function index()
   {
   	  $this->load->helper('url');

        $datosuc = array(
         'cantidadcasosconespecialista' => ($this->casos_model->devolver_cantidadcasosenmarchauc() - $this->casos_model->devolver_cantidadcasossinesp()),
         'cantidadcasosenmarcha' => $this->casos_model->devolver_cantidadcasosenmarchauc(),
         'cantidadcasosfinalizados' => $this->casos_model->devolver_cantidadcasosfinalizadosuc(),
         'mensajessinleer' => $this->mensajes_model->devolver_mensajessinleer(),
        );

        $this->load->view('indexusuariocomun.html',$datosuc);
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