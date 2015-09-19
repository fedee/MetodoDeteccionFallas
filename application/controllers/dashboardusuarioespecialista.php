<?php

class DashboardUsuarioEspecialista extends CI_Controller 
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
   	  //echo "Hola usuario especialista: ".$this->session->userdata('usuario');
        $this->load->helper('url');

        $datosesp = array(
         'cantidadcasosasignados' => $this->casos_model->devolver_cantidadcasosasignadosesp(),
         'cantidadcasosenmarcha' => ($this->casos_model->devolver_cantidadcasosasignadosesp()-$this->casos_model->devolver_cantidadcasosfinalizadosesp()),
         'cantidadcasosfinalizados' => $this->casos_model->devolver_cantidadcasosfinalizadosesp(),
         'mensajessinleer' => $this->mensajes_model->devolver_mensajessinleer(),
        );

        $this->load->view('indexusuarioespecialista.html',$datosesp);
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