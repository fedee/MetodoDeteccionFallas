<?php

class Casos extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->library('form_validation');
      $this->load->model('casos_model');
   }

   public function index() 
   {
      $this->load->helper('url');
   }

   public function asignar_caso()
   {
      $this->load->helper('url');

      if($this->input->post('asignar'))
      {
         $idesp= $_POST['especialista']; //Devuelvo el seleccionado del grupo de especialistas que envié por POST. 
         $idcaso= $_POST['caso'];

         $this->casos_model->asignar_caso($idesp,$idcaso);

      }
   }

   public function nuevo_caso()
   {
      $this->load->helper('url');

      if($this->input->post('submit_caso'))
      {
         $this->form_validation->set_rules('titulo','Titulo','required');
         $this->form_validation->set_rules('descripcion','Descripcion','required');
        
         $this->form_validation->set_message('required','El campo %s es obligatorio.');
      

         if($this->form_validation->run() != FALSE)
         {
            $this->casos_model->add_nuevocaso();
            redirect(site_url().'/usuariocomun/ver_casossubidos'); 
         }
         else
         {
            $this->load->view('nuevo_caso');
         }
      }

      else
      {
         redirect(site_url().'/panelusuariocomun');
      }
   }


}

?>