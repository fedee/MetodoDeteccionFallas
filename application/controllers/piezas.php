<?php

class Piezas extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->library('form_validation');
      $this->load->model('casos_model');
      $this->load->model('piezas_model');
      $this->load->model('fabricacion_model');
      $this->load->model('material_model');

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

      if($this->input->post('submit_editartitydesc'))
      {

        $caso['todoslosdatos'] = $this->casos_model->devolver_tituloydescporidcaso($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['mostrarhasta'] = 0;

        $this->load->view('edicionusuario.html',$caso);

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
         $this->form_validation->set_rules('siguiendonorma','Especificar si la pieza fue montada siguiendo la norma del fabricante o no','required');
        
         $this->form_validation->set_message('required','El campo %s es obligatorio.');
      

         if($this->form_validation->run() != FALSE)
         {
            $this->piezas_model->guardainfo_casocomponente1($idcaso);
            

                  for($i=1;$i<=7;$i++)
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
                                    if($i==6) $parathumb['queimagen'] = '8';
                                    if($i==7) $parathumb['queimagen'] = '9';
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

      if($this->input->post('submit_editarintroduccion'))
      {

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 1;

        $this->load->view('edicionusuario.html',$caso);   
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


      if($this->input->post('submit_editarcasocomponente1'))
      {

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 2;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);

        $this->load->view('edicionusuario.html',$caso);   
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

        $this->piezas_model->actualizarimagenes($parapiezasmodel);  

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

         $numpasoactual = $this->casos_model->devolver_numeropaso($idcaso);
         if($numpasoactual == 3)
         {
             $datosrevision = array(
             'procesos' => $this->casos_model->devolver_procesos($idcaso),
             'subprocesos' => $this->casos_model->devolver_subtipos($idcaso),
             'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
             'id' => $idcaso,
                                   );


            $this->casos_model->actualizarpaso($idcaso,'4');

            $this->load->view('revisionprocesosfabricacion.html',$datosrevision);
         }
         else
         {
            redirect(site_url().'/usuariocomun/completar_caso/'.$idcaso); 
         }
         
       
      }

      if($this->input->post('submit_editarcasocomponente2'))
      {
          $this->load->helper('url');
          $this->load->helper('form');
          
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 3;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();

          $this->load->view('edicionusuario.html',$caso);
       
      }
            
   }


   public function guardarinfo_ensayos($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $cantidadimagenes = 0;

      if($this->input->post('submit_guardarinfoensayos'))
      {

                  for($i=1;$i<=3;$i++)
                  { 
                        if($_FILES['imagenensayo'.$i]['name']!=''){

                            $config['upload_path'] = './uploads/';
                            $config['allowed_types'] = 'gif|jpg|png';
                            $config['max_size'] = '2000';
                            $config['max_width'] = '2024';
                            $config['max_height'] = '2008';

                            $this->load->library('upload', $config);
                            //SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA UPLOAD_VIEW
                            if (!$this->upload->do_upload('imagenensayo'.$i)) {
                                   $error = array('error' => $this->upload->display_errors());
                                   echo $_FILES['imagenensayo'.$i]['name'];
                                   echo 'Estoy en la iteracion: '.$i;
                                   echo print_r($error);
                                   //$this->load->view('upload_view', $error);
                                } 
                           else {
                                //EN OTRO CASO SUBIMOS LA IMAGEN, CREAMOS LA MINIATURA Y ENVIAMOS LOS DATOS AL MODELO PARA HACER LA 
                                //INSERCIÓN
                                    $file_info = $this->upload->data();
                                    //USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
                                    //ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
                                    if($i==1) {$parathumb['queimagen'] = '4'; $cantidadimagenes = $cantidadimagenes + 1;}
                                    if($i==2) {$parathumb['queimagen'] = '4'; $cantidadimagenes = $cantidadimagenes + 1;}
                                    if($i==3) {$parathumb['queimagen'] = '4'; $cantidadimagenes = $cantidadimagenes + 1;}

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

                  $this->piezas_model->guardainfo_ensayos($idcaso,$cantidadimagenes); 

                  $numpasoactual = $this->casos_model->devolver_numeropaso($idcaso);
                  if($numpasoactual == 6)
                  {
                     redirect(site_url().'/usuariocomun/iramoduloensayos/'.$idcaso); 
                  }
                  else
                  {
                    redirect(site_url().'/usuariocomun/completar_caso/'.$idcaso); 
                  }
                   

      }

      if($this->input->post('iramacrografia'))
      {
          $numpasoactual = $this->casos_model->devolver_numeropaso($idcaso);
           if($numpasoactual == 6)
           {
               redirect(site_url().'/usuariocomun/iramacrografia/'.$idcaso); 
           }
           else
           {
              redirect(site_url().'/usuariocomun/completar_caso/'.$idcaso); 
           }

      }

      if($this->input->post('submit_editarfabricacion'))
      {

          $this->load->helper('url');
          $this->load->helper('form');
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 4;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


          $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
          $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
          $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

          $this->load->view('edicionusuario.html',$caso);
      
      }
   }


   public function guardarinfo_macrografia($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      if($this->input->post('submit_guardarinfomacrografia'))
      {

                  for($i=1;$i<=3;$i++)
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
                                //EN OTRO CASO SUBIMOS LA IMAGEN, CREAMOS LA MINIATURA Y ENVIAMOS LOS DATOS AL MODELO PARA HACER LA 
                                //INSERCIÓN
                                    $file_info = $this->upload->data();
                                    //USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
                                    //ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
                                    if($i==1) {$parathumb['queimagen'] = '5'; $this->piezas_model->guardainfo_macrografia($idcaso,1);}
                                    if($i==2) {$parathumb['queimagen'] = '5'; $this->piezas_model->guardainfo_macrografia($idcaso,2);}
                                    if($i==3) {$parathumb['queimagen'] = '5'; $this->piezas_model->guardainfo_macrografia($idcaso,3);}

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

                  $numpasoactual = $this->casos_model->devolver_numeropaso($idcaso);
                  if($numpasoactual == 7)
                  {
                     redirect(site_url().'/usuariocomun/iramicrografia/'.$idcaso);
                  }
                  else
                  {
                    redirect(site_url().'/usuariocomun/completar_caso/'.$idcaso); 
                  }

                  

      }


      if($this->input->post('submit_editarensayos'))
      {

          $this->load->helper('url');
          $this->load->helper('form');
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 5;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


          $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
          $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
          $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

          $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
          $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
          $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

          $this->load->view('edicionusuario.html',$caso);
      
      }

   }

   public function guardarinfo_micrografia($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      if($this->input->post('submit_guardarinfomicrografia'))
      {

                  for($i=1;$i<=3;$i++)
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
                                //EN OTRO CASO SUBIMOS LA IMAGEN, CREAMOS LA MINIATURA Y ENVIAMOS LOS DATOS AL MODELO PARA HACER LA 
                                //INSERCIÓN
                                    $file_info = $this->upload->data();
                                    //USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
                                    //ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
                                    if($i==1) {$parathumb['queimagen'] = '6'; $this->piezas_model->guardainfo_micrografia($idcaso,1);}
                                    if($i==2) {$parathumb['queimagen'] = '6'; $this->piezas_model->guardainfo_micrografia($idcaso,2);}
                                    if($i==3) {$parathumb['queimagen'] = '6'; $this->piezas_model->guardainfo_micrografia($idcaso,3);}

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

                  $numpasoactual = $this->casos_model->devolver_numeropaso($idcaso);
                  if($numpasoactual == 8)
                  {
                     redirect(site_url().'/usuariocomun/iradiscusion/'.$idcaso);
                  }
                  else
                  {
                    redirect(site_url().'/usuariocomun/completar_caso/'.$idcaso); 
                  }

                   

      }

      if($this->input->post('submit_editarmacrografia'))
      {

          $this->load->helper('url');
          $this->load->helper('form');
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 6;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


          $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
          $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
          $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

          $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
          $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
          $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

          $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
          $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);          

          $this->load->view('edicionusuario.html',$caso);
      
      }

   }


   public function guardardiscusion($idcaso)
   {
      $this->load->helper('url');

      if($this->input->post('submit_guardardiscusion'))
      {     
            $this->piezas_model->guardainfo_discusion($idcaso);
            
            redirect(site_url().'/usuariocomun/irahipotesis/'.$idcaso);

      }

      if($this->input->post('submit_editarmicrografia'))
      {

          $this->load->helper('url');
          $this->load->helper('form');
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 7;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


          $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
          $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
          $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

          $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
          $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
          $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

          $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
          $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);

          $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);          

          $this->load->view('edicionusuario.html',$caso);
      
      }
   }

   public function guardarinfo_hipotesis($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $cantidadimagenes = 0;

      if($this->input->post('submit_guardarhipotesis'))
      {

                  for($i=1;$i<=3;$i++)
                  { 
                        if($_FILES['imagenhipo'.$i]['name']!=''){

                            $config['upload_path'] = './uploads/';
                            $config['allowed_types'] = 'gif|jpg|png';
                            $config['max_size'] = '2000';
                            $config['max_width'] = '2024';
                            $config['max_height'] = '2008';

                            $this->load->library('upload', $config);
                            //SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA UPLOAD_VIEW
                            if (!$this->upload->do_upload('imagenhipo'.$i)) {
                                   $error = array('error' => $this->upload->display_errors());
                                   echo $_FILES['imagenhipo'.$i]['name'];
                                   echo 'Estoy en la iteracion: '.$i;
                                   echo print_r($error);
                                   //$this->load->view('upload_view', $error);
                                } 
                           else {
                                //EN OTRO CASO SUBIMOS LA IMAGEN, CREAMOS LA MINIATURA Y ENVIAMOS LOS DATOS AL MODELO PARA HACER LA 
                                //INSERCIÓN
                                    $file_info = $this->upload->data();
                                    //USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
                                    //ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
                                    if($i==1) {$parathumb['queimagen'] = '7'; $cantidadimagenes = $cantidadimagenes + 1;}
                                    if($i==2) {$parathumb['queimagen'] = '7'; $cantidadimagenes = $cantidadimagenes + 1;}
                                    if($i==3) {$parathumb['queimagen'] = '7'; $cantidadimagenes = $cantidadimagenes + 1;}

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

                  $this->piezas_model->guardainfo_hipotesis($idcaso,$cantidadimagenes); 
                  redirect(site_url().'/usuariocomun/iramodulohipotesis/'.$idcaso); 

      }

      if($this->input->post('iraparetto'))
      {

          redirect(site_url().'/usuariocomun/iraparetto/'.$idcaso); 

      }

      if($this->input->post('submit_editardiscusion'))
      {

          $this->load->helper('url');
          $this->load->helper('form');
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 8;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


          $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
          $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
          $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

          $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
          $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
          $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

          $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
          $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);

          $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

          $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);          

          $this->load->view('edicionusuario.html',$caso);
      
      }
   }

   public function irasugerenciasdefallo($idcaso)
   {
      $this->load->helper('url');

      if($this->input->post('submit_irasugerenciasdefallo'))
      {     
            $numpasoactual = $this->casos_model->devolver_numeropaso($idcaso);
            if($numpasoactual == 11)
            {
                $this->casos_model->actualizarpaso($idcaso,'12');
                redirect(site_url().'/usuariocomun/completar_caso/'.$idcaso);
            }
            else
            {
               redirect(site_url().'/usuariocomun/completar_caso/'.$idcaso);
            }

      }

      if($this->input->post('submit_editarhipotesis'))
      {

          $this->load->helper('url');
          $this->load->helper('form');
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 9;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


          $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
          $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
          $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

          $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
          $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
          $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

          $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
          $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);

          $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

          $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);   

          $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
          $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);       

          $this->load->view('edicionusuario.html',$caso);
      
      }
   }

   public function iraconclusionesgenerales($idcaso)
   {
      $this->load->helper('url');

      if($this->input->post('submit_iraconclusionesgenerales'))
      {     
            
            redirect(site_url().'/usuariocomun/iraconclusionesgenerales/'.$idcaso);

      }

      if($this->input->post('submit_editarhipotesis'))
      {

          $this->load->helper('url');
          $this->load->helper('form');
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 9;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


          $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
          $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
          $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

          $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
          $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
          $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

          $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
          $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);

          $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

          $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);   

          $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
          $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);       

          $this->load->view('edicionusuario.html',$caso);
      
      }
   }

   public function guardarconclusion($idcaso)
   {
      $this->load->helper('url');

      if($this->input->post('submit_guardarconclusion'))
      {     
            $this->piezas_model->guardainfo_conclusion($idcaso);
            $this->casos_model->cambiarestadocaso($idcaso,3);
            
            $soyusuario = 1;

            $this->casos_model->actualizarpaso($idcaso,'14'); //el ultimo paso, lo uso para cargar el form completo con conclusion
                                                              //y despues para volver al panel de mis casos al poner "guardar edición"

            redirect(site_url().'/usuariocomun/ver_casossubidos/');
      
            //redirect(site_url().'/reportepdf/crearpdf/'.$idcaso.'/'.$soyusuario);
            
      }


      if($this->input->post('submit_editarhipotesis'))
      {

          $this->load->helper('url');
          $this->load->helper('form');
          $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
          $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
          $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
          $caso['todoslosdatos']['id'] = $idcaso;
          $caso['todoslosdatos']['opcionescheck'] = array(
                                                           '1',
                                                           '0',
                                                         );
          $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
          $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                       );
          $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                       );
          $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                      '3',
                                                                      '4',
                                                                      '5',
                                                                      '6',
                                                                       );
          $caso['todoslosdatos']['mostrarhasta'] = 9;

          $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
          $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
          $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
          $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
          $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


          $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
          $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
          $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

          $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
          $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
          $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

          $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
          $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);

          $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

          $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);   

          $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
          $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);       

          $this->load->view('edicionusuario.html',$caso);
      
      }

       

     
   }


   public function editarcasocompleto($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
      $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
      $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
      $caso['todoslosdatos']['id'] = $idcaso;
      $caso['todoslosdatos']['opcionescheck'] = array(
                                                       '1',
                                                       '0',
                                                     );
      $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                  '0',
                                                                  '1',
                                                                  '2',
                                                                   );
      $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                  '0',
                                                                  '1',
                                                                  '2',
                                                                  '3',
                                                                   );
      $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                  '0',
                                                                  '1',
                                                                  '2',
                                                                  '3',
                                                                  '4',
                                                                  '5',
                                                                   );
      $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                  '0',
                                                                  '1',
                                                                  '2',
                                                                  '3',
                                                                  '4',
                                                                  '5',
                                                                  '6',
                                                                   );
      $caso['todoslosdatos']['mostrarhasta'] = 10;

      $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
      $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
      $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
      $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
      $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();


      $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
      $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
      $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

      $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
      $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
      $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

      $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
      $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);

      $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

      $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);   

      $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
      $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso); 

      $caso['todoslosdatos']['conclusionusuario'] = $this->casos_model->devolver_conclusionparaedicion($idcaso);         

      $this->load->view('edicionusuario.html',$caso);
      
        
   }


   public function eliminarimgyvolveraedicion($idcaso,$idimagen,$volverenedicionhasta)
   {
      $this->load->helper('url');
      $this->load->helper('form');


      if($volverenedicionhasta == 2)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 2;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);

        $this->load->view('edicionusuario.html',$caso);   
      }


      if($volverenedicionhasta == 3)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 3;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 4)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector3opciones'] = array(
                                                                      '0',
                                                                      '1',
                                                                      '2',
                                                                       );

        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 4;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();

        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 5)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 5;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 6)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 6;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 7)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 7;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 8)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 8;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      } 

      if($volverenedicionhasta == 9)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 9;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }    

      if($volverenedicionhasta == 10)
      {
        $this->eliminarurlimagenporid($idimagen);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 10;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $caso['todoslosdatos']['conclusionusuario'] = $this->casos_model->devolver_conclusionparaedicion($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }    
   }


   public function eliminarurlimagenporid($idimagen)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $this->db->delete('imagenes', array('id' => $idimagen));

   }

   public function eliminarprocesoporidcasoynro($idcaso,$nroproceso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $this->db->delete('fabricacion_listaprocesos', array( 'id_caso'=>$idcaso,
                                                             'numero_proceso'=>$nroproceso,
                                                            ));

   }


   public function eliminarprocesoyvolveraedicion($idcaso,$numeroproceso,$volverenedicionhasta)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      if($volverenedicionhasta == 4)
      {
        $this->eliminarprocesoporidcasoynro($idcaso,$numeroproceso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 4;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 5)
      {
        $this->eliminarprocesoporidcasoynro($idcaso,$numeroproceso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 5;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 6)
      {
        $this->eliminarprocesoporidcasoynro($idcaso,$numeroproceso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 6;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 7)
      {
        $this->eliminarprocesoporidcasoynro($idcaso,$numeroproceso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 7;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);  

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);    

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 8)
      {
        $this->eliminarprocesoporidcasoynro($idcaso,$numeroproceso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 8;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);  

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);  

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 9)
      {
        $this->eliminarprocesoporidcasoynro($idcaso,$numeroproceso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 9;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);  

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);  

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 10)
      {
        $this->eliminarprocesoporidcasoynro($idcaso,$numeroproceso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 10;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);  

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);  

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $caso['todoslosdatos']['conclusionusuario'] = $this->casos_model->devolver_conclusionparaedicion($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      
   }

   public function eliminarinfodeensayos($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $this->db->delete('ensayos', array( 'id_caso'=>$idcaso,
                                        ));
   }

   public function eliminarimgdeensayos($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

      $this->db->delete('imagenes', array( 'id_pieza'=>$idpieza,
                                           'queimagen'=>'4',
                                         ));
   }

   public function eliminarensayosyvolveraedicion($idcaso,$volverenedicionhasta)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      if($volverenedicionhasta == 5)
      {
        $this->eliminarinfodeensayos($idcaso);
        $this->eliminarimgdeensayos($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 5;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 6)
      {
        $this->eliminarinfodeensayos($idcaso);
        $this->eliminarimgdeensayos($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 6;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 7)
      {
        $this->eliminarinfodeensayos($idcaso);
        $this->eliminarimgdeensayos($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 7;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 8)
      {
        $this->eliminarinfodeensayos($idcaso);
        $this->eliminarimgdeensayos($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 8;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 9)
      {
        $this->eliminarinfodeensayos($idcaso);
        $this->eliminarimgdeensayos($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 9;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 10)
      {
        $this->eliminarinfodeensayos($idcaso);
        $this->eliminarimgdeensayos($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 10;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $caso['todoslosdatos']['conclusionusuario'] = $this->casos_model->devolver_conclusionparaedicion($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      
   }

   public function eliminarinfodemacrografia($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $this->db->delete('macrografia', array( 'id_caso'=>$idcaso,
                                        ));
   }

   public function eliminarimgdemacrografia($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

      $this->db->delete('imagenes', array( 'id_pieza'=>$idpieza,
                                           'queimagen'=>'5',
                                         ));
   }

   public function eliminarmacrografiayvolveraedicion($idcaso,$volverenedicionhasta)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      if($volverenedicionhasta == 6)
      {
        $this->eliminarinfodemacrografia($idcaso);
        $this->eliminarimgdemacrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 6;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);      

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 7)
      {
        $this->eliminarinfodemacrografia($idcaso);
        $this->eliminarimgdemacrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 7;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso); 

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);     

        $this->load->view('edicionusuario.html',$caso);  

      }

      if($volverenedicionhasta == 8)
      {
        $this->eliminarinfodemacrografia($idcaso);
        $this->eliminarimgdemacrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 8;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso); 

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);   

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);  

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 9)
      {
        $this->eliminarinfodemacrografia($idcaso);
        $this->eliminarimgdemacrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 9;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso); 

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);   

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso); 

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso); 

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 10)
      {
        $this->eliminarinfodemacrografia($idcaso);
        $this->eliminarimgdemacrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 10;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso); 

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);   

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso); 

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso); 

        $caso['todoslosdatos']['conclusionusuario'] = $this->casos_model->devolver_conclusionparaedicion($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      
   }

   public function eliminarinfodemicrografia($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $this->db->delete('micrografia', array( 'id_caso'=>$idcaso,
                                        ));
   }

   public function eliminarimgdemicrografia($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

      $this->db->delete('imagenes', array( 'id_pieza'=>$idpieza,
                                           'queimagen'=>'6',
                                         ));
   }

   public function eliminarmicrografiayvolveraedicion($idcaso,$volverenedicionhasta)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      if($volverenedicionhasta == 7)
      {
        $this->eliminarinfodemicrografia($idcaso);
        $this->eliminarimgdemicrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 7;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);    

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 8)
      {
        $this->eliminarinfodemicrografia($idcaso);
        $this->eliminarimgdemicrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 8;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);    

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 9)
      {
        $this->eliminarinfodemicrografia($idcaso);
        $this->eliminarimgdemicrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 9;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);    

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 10)
      {
        $this->eliminarinfodemicrografia($idcaso);
        $this->eliminarimgdemicrografia($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 10;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);    

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $caso['todoslosdatos']['conclusionusuario'] = $this->casos_model->devolver_conclusionparaedicion($idcaso);

        $this->load->view('edicionusuario.html',$caso);  

      }

      
   }

   public function eliminarinfodehipotesis($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $this->db->delete('hipotesis', array( 'id_caso'=>$idcaso,
                                        ));
   }

   public function eliminarimgdehipotesis($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

      $this->db->delete('imagenes', array( 'id_pieza'=>$idpieza,
                                           'queimagen'=>'7',
                                         ));
   }

   public function eliminarparetto($idcaso)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

      $this->db->delete('imagenes', array( 'id_pieza'=>$idpieza,
                                           'queimagen'=>'10',
                                         ));
   }


   public function eliminarhipotesisyvolveraedicion($idcaso,$volverenedicionhasta)
   {
      $this->load->helper('url');
      $this->load->helper('form');

      if($volverenedicionhasta == 9)
      {
        $this->eliminarinfodehipotesis($idcaso);
        $this->eliminarimgdehipotesis($idcaso);
        $this->eliminarparetto($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 9;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);    

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);


        $this->load->view('edicionusuario.html',$caso);  

      }


      if($volverenedicionhasta == 10)
      {
        $this->eliminarinfodehipotesis($idcaso);
        $this->eliminarimgdehipotesis($idcaso);
        $this->eliminarparetto($idcaso);

        $caso['todoslosdatos'] = $this->piezas_model->devolver_todosobrelapieza($idcaso);
        $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
        $caso['todoslosdatos']['descripcion'] = $this->casos_model->devolver_descripcioncasoparaedicion($idcaso);
        $caso['todoslosdatos']['id'] = $idcaso;
        $caso['todoslosdatos']['opcionescheck'] = array(
                                                         '1',
                                                         '0',
                                                       );
        $caso['todoslosdatos']['opcionesselector4opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                     );
        $caso['todoslosdatos']['opcionesselector6opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                     );
        $caso['todoslosdatos']['opcionesselector7opciones'] = array(
                                                                    '0',
                                                                    '1',
                                                                    '2',
                                                                    '3',
                                                                    '4',
                                                                    '5',
                                                                    '6',
                                                                     );
        $caso['todoslosdatos']['mostrarhasta'] = 10;

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $caso['todosobreimagenes'] = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);
        $caso['materiales'] = $this->material_model->devolver_todoslosmateriales();
        $caso['submateriales'] = $this->material_model->devolver_todoslossubmateriales();
        $caso['especificos'] = $this->material_model->devolver_todoslosmaterialesesp();
        $caso['todoslosdatos']['procesosparatabla'] = $this->casos_model->devolver_procesos($idcaso);
        $caso['todoslosdatos']['subprocesosparatabla'] = $this->casos_model->devolver_subtipos($idcaso);
        $caso['todoslosdatos']['nrosprocesosparatabla'] = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);

        $caso['todoslosdatos']['nombreensayo'] = $this->casos_model->devolver_nombresensayos($idcaso);
        $caso['todoslosdatos']['descripcionensayo'] = $this->casos_model->devolver_descripcionesensayos($idcaso);
        $caso['todoslosdatos']['numeroensayo'] = $this->casos_model->devolver_numerosensayos($idcaso);

        $caso['todoslosdatos']['descripcionmacro'] = $this->casos_model->devolver_descripcionmacro($idcaso);
        $caso['todoslosdatos']['tipofracturamacro'] = $this->casos_model->devolver_tipofracturamacro($idcaso);    

        $caso['todoslosdatos']['descripcionmicro'] = $this->casos_model->devolver_descripcionmicro($idcaso);  

        $caso['todoslosdatos']['discusion'] = $this->casos_model->devolver_discusionparaedicion($idcaso);

        $caso['todoslosdatos']['titulohipotesis'] = $this->casos_model->devolver_titulohipotesis($idcaso);
        $caso['todoslosdatos']['descripcionhipotesis'] = $this->casos_model->devolver_descripcionhipotesis($idcaso);

        $caso['todoslosdatos']['conclusionusuario'] = $this->casos_model->devolver_conclusionparaedicion($idcaso);


        $this->load->view('edicionusuario.html',$caso);  

      }

      
   }


   public function verprocesodefabricaciondesdeedicion($idcaso,$numeroproceso)
   {

      $this->load->helper('url');
      $this->load->helper('form');
      
      $caso['todoslosdatos']['titulo'] = $this->casos_model->devolver_titulocasoparaedicion($idcaso);
      $caso['todoslosdatos']['id'] = $idcaso;
      $caso['todoslosdatos']['mostrarhasta'] = 4;
      $caso['todoslosdatos']['numeroproceso'] = $numeroproceso;
      $caso['datosproveedor'] = $this->piezas_model->devolver_todosobreelproveedor($idcaso,$numeroproceso);
      $caso['cantidadparamsgenerales'] = $this->piezas_model->devolver_cantidadprocesosgenerales($idcaso,$numeroproceso);


      $cantidadderows = (float)$caso['cantidadparamsgenerales']/5;
      $cantidadderowsentero = intval($caso['cantidadparamsgenerales']/5);
      if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

      $caso['cantidadderows'] = $cantidadderows;

      $caso['nombresparametrosgen'] = $this->casos_model->devolver_nombresparametrosgen($idcaso,$numeroproceso);
      $caso['idprocesoinvolucrado'] = $this->casos_model->devolver_idprocesopoidcasoynumeroproceso($idcaso,$numeroproceso);
      $caso['valoresparametrosgen'] = $this->casos_model->devolver_valoresparametrosgen($idcaso,$numeroproceso);

      redirect(site_url().'/reportepdf/verprocesodesdeedicion/'.$idcaso.'/'.$numeroproceso);     
            
   }


   public function guardarconclusionesp($idcaso)
   {
     
      if($this->input->post('submit_guardarconclusion'))
      {

        $this->load->helper('url');

        $this->casos_model->guardainfo_conclusionespecialista($idcaso);
        $this->casos_model->cambiarestadocaso($idcaso,4); 
        
        redirect(site_url().'/usuarioespecialista/ver_casos/');
      
      }

   }
        
      
      
   
}

?>