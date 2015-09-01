<?php

class Piezas extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->library('form_validation');
      $this->load->model('casos_model');
      $this->load->model('piezas_model');
      $this->load->model('fabricacion_model');
   }

   public function index() 
   {
      $this->load->helper('url');
   }

   public function guardarinfo_casointroduccion($idcaso)
   {
      $this->load->helper('url');

      if($this->input->post('submit_intro'))
      {
         $this->form_validation->set_rules('fallo','Fallo','required');
         $this->form_validation->set_rules('ttrabajo','Tiempo de trabajo','required');
         $this->form_validation->set_rules('ctrabajo','Cantidad de trabajo','required');
         $this->form_validation->set_rules('tvidautil','Tiempo de vida util','required');
         $this->form_validation->set_rules('cvidautil','Cantidad de tiempo de vida util','required');
         $this->form_validation->set_rules('faseciclo','Fase de ciclo de vida','required');
        
         $this->form_validation->set_message('required','El campo %s es obligatorio.');
      

         if($this->form_validation->run() != FALSE)
         {
            $this->piezas_model->guardainfo_casointroduccion($idcaso);
            $this->casos_model->actualizarestado($idcaso,'0');
            $this->casos_model->actualizarpaso($idcaso,'1');
            redirect(site_url().'/usuariocomun/irapaso2componente/'.$idcaso); 
         }
         else
         {
            $this->load->helper('url');

            $caso['id'] = $idcaso;
            $caso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
            $this->load->view('casointroduccion.html',$caso);
         }

      }
   }


   public function guardarinfo_casocomponente1($idcaso)
   {
      $this->load->helper('url');
       $this->load->helper('form');

      if($this->input->post('submit_comp1'))
      {

         $this->form_validation->set_rules('nombregen','Nombre genérico de la pieza','required');
         $this->form_validation->set_rules('codigoint','Código interno de la pieza','required');
         $this->form_validation->set_rules('cantpiezas','Cantidad de piezas','required');
         $this->form_validation->set_rules('usopieza','Uso de la pieza','required');
         $this->form_validation->set_rules('maqomec','Máquina o mecanismo donde se encuentra montada la pieza','required');
         $this->form_validation->set_rules('especmontaje','Especificaciones del montaje','required');
         $this->form_validation->set_rules('siguiendonorma','Especificar si la pieza fue montada siguiendo la norma del fabricante o no','required');
        
         $this->form_validation->set_message('required','El campo %s es obligatorio.');
      

         if($this->form_validation->run() != FALSE)
         {
            $this->piezas_model->guardainfo_casocomponente1($idcaso);
            

                  for($i=1;$i<=5;$i++)
                  { 
                        if($_FILES['imagen'.$i]['name']!=''){

                            $config['upload_path'] = './uploads/';
                            $config['allowed_types'] = 'gif|jpg|png';
                            $config['max_size'] = '2000';
                            $config['max_width'] = '2024';
                            $config['max_height'] = '2008';

                            $this->load->library('upload', $config);
                            //SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA UPLOAD_VIEW
                            if (!$this->upload->do_upload('imagen'.$i)) {
                                   $error = array('error' => $this->upload->display_errors());
                                   echo $_FILES['imagen'.$i]['name'];
                                   echo 'Estoy en la iteracion: '.$i;
                                   echo print_r($error);
                                   //$this->load->view('upload_view', $error);
                                } 
                           else {
                                //EN OTRO CASO SUBIMOS LA IMAGEN, CREAMOS LA MINIATURA Y HACEMOS 
                                //ENVÍAMOS LOS DATOS AL MODELO PARA HACER LA INSERCIÓN
                                    $file_info = $this->upload->data();
                                    //USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
                                    //ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
                                    if($i==1) $parathumb['queimagen'] = '1';
                                    if($i==2) $parathumb['queimagen'] = '2';
                                    if($i==3) $parathumb['queimagen'] = '2';
                                    if($i==4) $parathumb['queimagen'] = '2';
                                    if($i==5) $parathumb['queimagen'] = '3';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  /*else
                  {
                      echo 'Seleccione una foto';
                  }*/
                  }

                  $this->casos_model->actualizarestado($idcaso,'0');
                  $this->casos_model->actualizarpaso($idcaso,'2');
                  redirect(site_url().'/usuariocomun/irapaso2componentecontinuacion/'.$idcaso); 

         }
         else
         {
            $this->load->helper('url');
             $this->load->helper('form');

            $caso['id'] = $idcaso;
            $caso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
            $this->load->view('casocomponente.html',$caso);
         }
      
      }
   }


   public function guardarinfo_casocomponente2($idcaso)
   {
      $this->load->helper('url');
       $this->load->helper('form');

      if($this->input->post('submit_comp2'))
      {

         $this->form_validation->set_rules('material','Material es requerido','required');
         /*$this->form_validation->set_rules('codigoint','Código interno de la pieza','required');
         $this->form_validation->set_rules('cantpiezas','Cantidad de piezas','required');
         $this->form_validation->set_rules('usopieza','Uso de la pieza','required');
         $this->form_validation->set_rules('maqomec','Máquina o mecanismo donde se encuentra montada la pieza','required');
         $this->form_validation->set_rules('especmontaje','Especificaciones del montaje','required');
         $this->form_validation->set_rules('siguiendonorma','Especificar si la pieza fue montada siguiendo la norma del fabricante o no','required');
         $this->form_validation->set_message('required','El campo %s es obligatorio.');*/
      

         if($this->form_validation->run() != FALSE)
         {
            $this->piezas_model->guardainfo_casocomponente2($idcaso);
            $this->casos_model->actualizarestado($idcaso,'0');
            $this->casos_model->actualizarpaso($idcaso,'3');
            redirect(site_url().'/usuariocomun/irapaso3fabricacion/'.$idcaso); 

         }
         else
         {
            $this->load->helper('url');
            $this->load->helper('form');

            $caso['id'] = $idcaso;
            $caso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
            $this->load->view('casocomponentecont.html',$caso);
         }
      
      }
   }

   function _create_thumbnail($parathumb){

        $parapiezasmodel['queimagen'] =  $parathumb['queimagen'];
        $parapiezasmodel['idpieza'] =  $parathumb['idpieza'];
        $parapiezasmodel['filename'] =  $parathumb['filename'];

        $config['image_library'] = 'gd2';
        //CARPETA EN LA QUE ESTÁ LA IMAGEN A REDIMENSIONAR
        $config['source_image'] = 'uploads/'.$parathumb['filename'];
        
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        //CARPETA EN LA QUE GUARDAMOS LA MINIATURA
        $config['new_image']='uploads/thumbs/';
        $config['width'] = 150;
        $config['height'] = 150;

        $this->piezas_model->actualizarimagenescomp1($parapiezasmodel);  

        $this->load->library('image_lib'); 
        $this->image_lib->initialize($config);
        $this->image_lib->resize();

    }


   public function guardarinfo_casofabricacion($idcaso)
   {
       if($this->input->post('submit_comp1'))
      {

         $this->load->helper('url');
         $this->piezas_model->guardainfo_casofabricacion($idcaso);

         $paso['id'] = $idcaso;
         $paso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
         $paso['tiposprocesos'] = $this->fabricacion_model->devolver_todoslosprocesos();
         $paso['numeroproceso'] = "Agregar otro proceso.";
         $this->load->view('casofabricacion.html',$paso);

      }

      if($this->input->post('irarevision'))
      {

         $this->load->helper('url');

         $datosrevision = array(
         'procesos' => $this->casos_model->devolver_procesos($idcaso),
         'subprocesos' => $this->casos_model->devolver_subtipos($idcaso),
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );


        $this->casos_model->actualizarpaso($idcaso,'4');

        $this->load->view('revisionprocesosfabricacion.html',$datosrevision);


        //$this->load->view('revisionprocesosfabricacion.html',$paso);
       
      }

            
   }
        
      
      
   
}

?>