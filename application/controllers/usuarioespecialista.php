<?php

class UsuarioEspecialista extends CI_Controller 
{

   public function __construct()
   {
      parent::__construct();
      $this->load->model('usuarios_model');
      $this->load->model('casos_model');
   }

   public function index()
   {
        $this->load->helper('url');
   }

  public function ver_casos()
   {
        $this->load->helper('url');

        $datosesp = array(
         'titulo' => $this->casos_model->devolver_espectituloasignado(),
         'descripcion' => $this->casos_model->devolver_espdescasignada(),
         'fecharegistrocaso' => $this->casos_model->devolver_espfechaasignada(),
         'idcaso' => $this->casos_model->devolver_espidcasoasignado(),
         'estadocaso' => $this->casos_model->devolver_espestadocasoasignado(),
         'pasocaso' => $this->casos_model->devolver_esppasocasoasignado(),
        );

        $this->load->view('casosasignadosespecialista.html',$datosesp);
   }

   public function ver_estadisticas()
   {
        $this->load->helper('url');

        $idespecialista = $this->session->userdata('id');
        redirect(site_url().'/moduloestadistico/todoparagenerargraficos/'.$idespecialista);
        
   }

   public function dejarconclusiones_caso($idcaso)
  {
    $this->load->helper('url');

    $caso['id'] = $idcaso;
    $caso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);

    $this->load->view('conclusionesespecialista.html',$caso); 
  }

}

?>