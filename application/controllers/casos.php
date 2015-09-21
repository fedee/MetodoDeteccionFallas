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
                              <br/><br/><br/>
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

            if($idcasodelselector == 1)
            {
               echo '
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="col-lg-7">
                              <br/><br/><br/>
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
                                    <h5><strong>Diámetro Interior (mm):</strong></h5>
                                 </div>
                                <div class="col-lg-4">
                                    <input type="text" name="diametrointerior" class="form-control" placeholder="Valor (formato: 0.0)">
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
                                 <img align="center" src="http://localhost/cafap/imagenes/barraseccioncircularh.jpg" 
                                 alt="Mountain View" style="width:350px;height:320px;" >
                              </div>
                           </div>
                        </div>
                     </div>';
            }

            if($idcasodelselector == 2)
            {
               echo '
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="col-lg-7">
                              <br/><br/><br/>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <h5><strong>Área de Sección Transversal (cm^2):</strong></h5>
                                 </div>
                                <div class="col-lg-4">
                                    <input type="text" name="areasec" class="form-control" placeholder="Valor (formato: 0.0)">
                                </div>
                              </div>
                              <br/>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <h5><strong>Fuerza Normal de Compresión (Kg/cm^2):</strong></h5>
                                 </div>
                                <div class="col-lg-4">
                                    <input type="text" name="fuerzanormalc" class="form-control" placeholder="Valor (formato: 0.0)">
                                </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-5">
                                 <img align="center" src="http://localhost/cafap/imagenes/barrapocoesbeltasc.jpg" 
                                 alt="Mountain View" style="width:350px;height:320px;" >
                              </div>
                           </div>
                        </div>
                     </div>';
            }

            if($idcasodelselector == 3)
            {
               echo '
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="col-lg-7">
                              <br/><br/><br/>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <h5><strong>Área de Sección Transversal (cm^2):</strong></h5>
                                 </div>
                                <div class="col-lg-4">
                                    <input type="text" name="areasec" class="form-control" placeholder="Valor (formato: 0.0)">
                                </div>
                              </div>
                              <br/>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <h5><strong>Fuerza Normal de Tracción (Kg/cm^2):</strong></h5>
                                 </div>
                                <div class="col-lg-4">
                                    <input type="text" name="fuerzanormalt" class="form-control" placeholder="Valor (formato: 0.0)">
                                </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-5">
                                 <img align="center" src="http://localhost/cafap/imagenes/barraseccualc.jpg" 
                                 alt="Mountain View" style="width:350px;height:320px;" >
                              </div>
                           </div>
                        </div>
                     </div>';
            }

            if($idcasodelselector == 4)
            {
               echo '
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="col-lg-7">
                              <br/>
                              <div class="row">
                                <div class="col-lg-3">
                                    <h5><strong>Tipo de apoyo:</strong></h5>
                                </div>
                                <div class="col-lg-7">
                                    <select name="apoyo" id="apoyo" class="form-control">
                                      <option value="-1">Elegir</option>
                                      <option value="0">Simplemente apoyada</option>
                                      <option value="1">Empotrada - Libre</option>
                                      <option value="2">Apoyada - Apoyo a Distancia X</option>
                                    </select>
                                </div>
                              </div>
                              <br/>
                              <div class="row">
                                <div class="col-lg-3">
                                    <h5><strong>Tipo de carga:</strong></h5>
                                </div>
                                <div class="col-lg-7">
                                    <select name="carga" id="carga" class="form-control">
                                      <option value="-1">Elegir</option>
                                      <option value="0">Puntual en el centro</option>
                                      <option value="1">Puntual en el extremo libre</option>
                                      <option value="2">Distribuida Uniforme</option>
                                      <option value="3">Puntual a una Distancia X</option>
                                      <option value="4">Momento aplicado al medio</option>
                                      <option value="5">Momento aplicado al extremo libre</option>
                                    </select>
                                </div>
                              </div>
                              <br/>

                              <div id ="viga">
                                          
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-5">
                                 <br/><br/><br/><br/>
                                 <img align="center" src="http://localhost/cafap/imagenes/vigaflexionsimple.jpg" 
                                 alt="Mountain View" style="width:350px;height:320px;" >
                              </div>
                           </div>
                           <br/>
                        </div>
                     </div>';
            }

         }

   } //fin func


   public function getcontenidoviga()
   {

         $idapoyo = (int)$this->input->post('id_apoyo');
         $idcarga = (int)$this->input->post('id_carga');
         //$idcarga = (int)$this->input->post('id_carga');

         if($idapoyo == 0 && $idcarga == 0)
         {
            echo '<div class="row">
                     <div class="col-lg-6">
                        <h5><strong>Carga Puntual (Kg):</strong></h5>
                     </div>
                    <div class="col-lg-4">
                        <input type="text" name="cargapuntual" class="form-control" placeholder="Valor (formato: 0.0)">
                    </div>
                  </div>
                  <br/>
                  <div class="row">
                     <div class="col-lg-6">
                        <h5><strong>Carga Distribuida (Kg/m):</strong></h5>
                     </div>
                    <div class="col-lg-4">
                        <input type="text" name="cargadistribuida" class="form-control" placeholder="Valor (formato: 0.0)">
                    </div>
                  </div>
                  <br/>
                  <div class="row">
                     <div class="col-lg-6">
                        <h5><strong>Luz de la Viga (m):</strong></h5>
                     </div>
                    <div class="col-lg-4">
                        <input type="text" name="luzdelaviga" class="form-control" placeholder="Valor (formato: 0.0)">
                    </div>
                  </div>
                  <br/>
                  <div class="row">
                     <div class="col-lg-6">
                        <h5><strong>Inercia de la sección transversal de la viga (cm^4):</strong></h5>
                     </div>
                    <div class="col-lg-4">
                        <input type="text" name="inerciaviga" class="form-control" placeholder="Valor (formato: 0.0)">
                    </div>
                  </div>
                  <br/>
                  <div class="row">
                     <div class="col-lg-6">
                        <h5><strong>Módulo Resistente de la sección (cm^3):</strong></h5>
                     </div>
                    <div class="col-lg-4">
                        <input type="text" name="moduloseccion" class="form-control" placeholder="Valor (formato: 0.0)">
                    </div>
                  </div>
                  <br/>
                  <div class="row">
                     <div class="col-lg-6">
                        <h5><strong>Altura máxima de la sección transversal (cm):</strong></h5>
                     </div>
                    <div class="col-lg-4">
                        <input type="text" name="alturamaxst" class="form-control" placeholder="Valor (formato: 0.0)">
                    </div>
                  </div>
                  <br/>
                  <div class="row">
                     <div class="col-lg-6">
                        <h5><strong>Distancia X de carga puntual (m):</strong></h5>
                     </div>
                    <div class="col-lg-4">
                        <input type="text" name="distanciacargap" class="form-control" placeholder="Valor (formato: 0.0)">
                    </div>
                  </div>
                  <br/>
                  <div class="row">
                     <div class="col-lg-6">
                        <h5><strong>Distancia de apoyo a extremo libre (m):</strong></h5>
                     </div>
                    <div class="col-lg-4">
                        <input type="text" name="distanciaapoyo" class="form-control" placeholder="Valor (formato: 0.0)">
                    </div>
                  </div>';
         }
         else
         {
            echo "";
         }

         

   } //fin func


}

?>