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


            redirect(site_url().'/usuariocomun/iraintroduccion/'); 
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


   public function getcontenidocasosimplificado()
   {

         $idcasodelselector = (int)$this->input->post('id_casosimp');

         if($idcasodelselector == -1)
         {
            echo "";
         }
         else
         {

            if($idcasodelselector == 0)
            {
               echo '
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="col-lg-7">
                              <br/><br/><br/><br/><br/>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <h5><strong>Diámetro Exterior (mm):</strong></h5>
                                 </div>
                                <div class="col-lg-4">
                                    <input type="text" name="diametroexterior" class="form-control" placeholder="Valor (formato: 0.0)">
                                </div>
                              </div>
                              <br/>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <h5><strong>Momento Torsor o Torque (Kg*m):</strong></h5>
                                 </div>
                                <div class="col-lg-4">
                                    <input type="text" name="momentotorsor" class="form-control" placeholder="Valor (formato: 0.0)">
                                </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-5">
                                 <img align="center" src="http://localhost/cafap/imagenes/barraseccioncircularm.jpg" 
                                 alt="Mountain View" style="width:350px;height:320px;" >
                              </div>
                           </div>
                        </div>
                     </div>';
            }

         }

   } //fin func


}

?>