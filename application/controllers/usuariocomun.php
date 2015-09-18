<?php

class UsuarioComun extends CI_Controller 
{

   public function __construct()
   {
      parent::__construct();
      $this->load->model('usuarios_model');
      $this->load->model('casos_model');
      $this->load->model('piezas_model');
      $this->load->model('material_model');
      $this->load->model('fabricacion_model');
   }

   public function index()
   {
        $this->load->helper('url');
   }

  public function ver_casossubidos()
   {
        $this->load->helper('url');

        $datosesp = array(
         'titulo' => $this->casos_model->devolver_comuntituloasignado(),
         'descripcion' => $this->casos_model->devolver_comundescasignada(),
         'fecharegistrocaso' => $this->casos_model->devolver_comunfechaasignada(),
         'idcaso' => $this->casos_model->devolver_comunidcasoasignado(),
         'estado' => $this->casos_model->devolver_comunestadocaso(),
        );

        $this->load->view('miscasosusuariocomun.html',$datosesp);
   }

   public function subir_caso()
  {
    $this->load->helper('url');
    $this->load->view('subircaso.html');
  }

  public function completar_caso($idcaso)
  {
    $this->load->helper('url');

    $caso['id'] = $idcaso;
    $caso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
    $numpaso = $this->casos_model->devolver_numeropaso($idcaso);

    if ($numpaso == 0)
    {
      $this->load->view('casointroduccion.html',$caso);
    }

    if ($numpaso == 1)
    {
      $this->irapaso2componente($idcaso);
    }

    if ($numpaso == 2)
    {
      $this->irapaso2componentecontinuacion($idcaso);
    }

    if ($numpaso == 3)
    {
      $this->irapaso3fabricacion($idcaso);
    }

    if ($numpaso == 4)
    {
      $this->irarevision($idcaso);
    }

    if ($numpaso == 5)
    {
      $this->iramodulofea($idcaso);
    }

    if ($numpaso == 6)
    {
      $this->iramoduloensayos($idcaso);
    }

    if ($numpaso == 7)
    {
      $this->iramacrografia($idcaso);
    }

    if ($numpaso == 8)
    {
      $this->iramicrografia($idcaso);
    }

    if ($numpaso == 9)
    {
      $this->iradiscusion($idcaso);
    }

    if ($numpaso == 10)
    {
      $this->irahipotesis($idcaso);
    }

    if ($numpaso == 11)
    {
      $this->iraparetto($idcaso);
    }

    if ($numpaso == 12)
    {
      $this->irasugerenciasdefallo($idcaso);
    }

    if ($numpaso == 13)
    {
      $this->iraconclusionesgenerales($idcaso);
    }

    if ($numpaso == 14)
    {
      redirect(site_url().'/usuariocomun/ver_casossubidos/');
    }
    
  }

  public function iraintroduccion()
  {
    $this->load->helper('url');

    $idcaso = $this->casos_model->devolver_idcaso();

    $caso['id'] = $idcaso;
    $caso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
    $this->load->view('casointroduccion.html',$caso);
  }

  public function irapaso2componente($idcaso)
  {
    $this->load->helper('url');

    $caso['id'] = $idcaso;
    $caso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
    $this->load->view('casocomponente.html',$caso);
  }

  public function irapaso2componentecontinuacion($idcaso)
  {
    $this->load->helper('url');

    $paso['id'] = $idcaso;
    $paso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
    $paso['materiales'] = $this->material_model->devolver_todoslosmateriales();
    $this->load->view('casocomponentecont.html',$paso);
  }

  public function irapaso3fabricacion($idcaso)
  {
    $this->load->helper('url');

    $paso['id'] = $idcaso;
    $paso['titulo'] =  $this->casos_model->devolver_tituloporid($idcaso);
    $paso['tiposprocesos'] = $this->fabricacion_model->devolver_todoslosprocesos();
    $paso['numeroproceso'] = "Nuevo proceso.";
    $this->load->view('casofabricacion.html',$paso);
  }

  public function irarevision($idcaso)
  {
    $this->load->helper('url');

    $datosrevision = array(
         'procesos' => $this->casos_model->devolver_procesos($idcaso),
         'subprocesos' => $this->casos_model->devolver_subtipos($idcaso),
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->load->view('revisionprocesosfabricacion.html',$datosrevision);
  }

  public function irafea($idcaso)
  {
    if($this->input->post('submit_irafea'))
      {

        $this->load->helper('url');

        $datosfea = array(
           'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
           'id' => $idcaso,
          );

       $this->casos_model->actualizarpaso($idcaso,'5');
       $this->load->view('modulofea.html',$datosfea);
      
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


  public function iramodulofea($idcaso)
  {

      $this->load->helper('url');

      $datosfea = array(
           'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
           'id' => $idcaso,
          );

      $this->casos_model->actualizarpaso($idcaso,'5');
      $this->load->view('modulofea.html',$datosfea);
      
  }
    
   public function iramoduloensayos($idcaso)
  {

      $this->load->helper('url');

      $datosensayo = array(
           'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
           'id' => $idcaso,
          );

      $this->casos_model->actualizarpaso($idcaso,'6');
      $this->load->view('ensayos.html',$datosensayo);
      
  }

  public function iraensayosdesdeedicion($idcaso)
  {

      $this->load->helper('url');

      $datosensayo = array(
           'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
           'id' => $idcaso,
          );
      $this->load->view('ensayos.html',$datosensayo);
      
  }

  public function iraensayos($idcaso)
  {
    if($this->input->post('submit_iraensayos'))
    {
        $this->load->helper('url');

        $datosensayo = array(
           'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
           'id' => $idcaso,
          );

        $this->casos_model->actualizarpaso($idcaso,'6');
        $this->load->view('ensayos.html',$datosensayo);

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

  public function iramacrografia($idcaso)
  {
    $this->load->helper('url');

      $datosmacro = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->casos_model->actualizarpaso($idcaso,'7');
    $this->load->view('macrografia.html',$datosmacro);

  }

  public function iramacrografiadesdeedicion($idcaso)
  {
    $this->load->helper('url');

      $datosmacro = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->load->view('macrografia.html',$datosmacro);

  }

  public function iramicrografia($idcaso)
  {
    $this->load->helper('url');

      $datosmicro = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->casos_model->actualizarpaso($idcaso,'8');
    $this->load->view('micrografia.html',$datosmicro);

  }

  public function iramicrografiadesdeedicion($idcaso)
  {
    $this->load->helper('url');

      $datosmicro = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->load->view('micrografia.html',$datosmicro);

  }



  public function iradiscusion($idcaso)
  {
    $this->load->helper('url');

      $datosdisc = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->casos_model->actualizarpaso($idcaso,'9');
    $this->load->view('discusion.html',$datosdisc);

  }

  public function irahipotesis($idcaso)
  {
    $this->load->helper('url');

      $datoshipo = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->casos_model->actualizarpaso($idcaso,'10');
    $this->load->view('hipotesis.html',$datoshipo);

  }

  public function iramodulohipotesis($idcaso)
  {
    $this->load->helper('url');

      $datoshipo = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->load->view('hipotesis.html',$datoshipo);

  }

  public function irahipotesisdesdeedicion($idcaso)
  {
    $this->load->helper('url');

      $datoshipo = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->load->view('hipotesis.html',$datoshipo);

  }

  public function iraparetto($idcaso)
  {
    
    $this->load->helper('url');

    redirect(site_url().'/graficosparetto/construir_paretto/'.$idcaso);
    
  }

  

  public function irasugerenciasdefallo($idcaso)
  {
    $this->load->helper('url');

    //Primera parte... Encontrando los procesos similares.
    /* Similitud con 100% de los procesos: 50 puntos.
       Similitud con el tipo de fractura: 20 puntos.
       Similitud con fase del ciclo de vida donde falló la pieza: 9 puntos.
       Si coincide con... 
                          Material: 7 puntos.
                          Submaterial: 11 puntos.
                          Material específico: 15 puntos.
       Similitud con elementos en suspensión: 3 puntos.
       Similitud con modificaciones en condiciones de trabajo: 3 puntos. */

    //Procesos y subprocesos de fabricación (50):
    $idsprocesoscasoactual = $this->casos_model->devolver_procesosparasugerenciafallo($idcaso);
    $idssubprocesoscasoactual = $this->casos_model->devolver_subprocesosparasugerenciafallo($idcaso);
    //Tipo de fractura (20):
    $tipofracturacasoactual = $this->casos_model->devolver_tipofracturaporidcaso($idcaso);
    //Fase del ciclo de vida donde falló la pieza (9):
    $idpiezacasoactual = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
    $faseciclovidacasoactual = $this->casos_model->devolver_faseciclovida($idpiezacasoactual);
    //Material (7), Submaterial (11) y Material Específico (15):
    $materialcasoactual = $this->casos_model->devolver_materialparasugerencia($idpiezacasoactual);
    $submaterialcasoactual = $this->casos_model->devolver_submaterialparasugerencia($idpiezacasoactual);
    $materialespcasoactual = $this->casos_model->devolver_materialespparasugerencia($idpiezacasoactual);
    //Elementos en suspensión (3):
    $elemsuspcasoactual = $this->casos_model->devolver_elemsuspparasugerencia($idpiezacasoactual);
    //Modificaciones en condiciones de trabajo (3):
    $modifcondcasoactual = $this->casos_model->devolver_modifcondtrabparasugerencia($idpiezacasoactual);


    $totalafinidad = 0;

    $casosafines = array(
                  "id" => "",
                  "afinidad" => "",
                  );

    $casosfinalizados = $this->casos_model->devolver_finalizadosconconclusion();

    for($k = 0; $k <count($casosfinalizados); $k++)
    {
        //Procesos y subprocesos de fabricación (50):
        $idsprocesoscasoacomparar = $this->casos_model->devolver_procesosparasugerenciafallo($casosfinalizados[$k]);
        $idssubprocesoscasoacomparar = $this->casos_model->devolver_subprocesosparasugerenciafallo($casosfinalizados[$k]);
        //Tipo de fractura (20):
        $tipofracturacasoacomparar = $this->casos_model->devolver_tipofracturaporidcaso($casosfinalizados[$k]);
        //Fase del ciclo de vida donde falló la pieza (9):
        $idpiezacasoacomparar = $this->piezas_model->devolver_idpiezaporidcaso($casosfinalizados[$k]);
        $faseciclovidacasoacomparar = $this->casos_model->devolver_faseciclovida($idpiezacasoacomparar);
        //Material (7), Submaterial (11) y Material Específico (15):
        $materialcasoacomparar = $this->casos_model->devolver_materialparasugerencia($idpiezacasoacomparar);
        $submaterialcasoacomparar = $this->casos_model->devolver_submaterialparasugerencia($idpiezacasoacomparar);
        $materialespcasoacomparar = $this->casos_model->devolver_materialespparasugerencia($idpiezacasoacomparar);
        //Elementos en suspensión (3):
        $elemsuspcasoacomparar = $this->casos_model->devolver_elemsuspparasugerencia($idpiezacasoacomparar);
        //Modificaciones en condiciones de trabajo (3):
        $modifcondcasoacomparar = $this->casos_model->devolver_modifcondtrabparasugerencia($idpiezacasoacomparar);


        $afinidadentreprocesos = 0;

        for($i = 0; $i <count($idsprocesoscasoactual); $i++)
        {
            for($j = 0; $j <count($idsprocesoscasoacomparar); $j++)
            {
                if($idsprocesoscasoactual[$i]==$idsprocesoscasoacomparar[$j])
                {
                    if($idssubprocesoscasoactual[$i]==$idssubprocesoscasoacomparar[$j])
                    {
                        $afinidadentreprocesos++;
                        $idsprocesoscasoacomparar[$i] = -1;
                        $idssubprocesoscasoacomparar[$i] = -1;
                    }          
                } 
            }
        }

        $cantidadprocesoscasoactual = count($idsprocesoscasoactual);
        $porcentajeafinidadprocesos = ($afinidadentreprocesos*50)/$cantidadprocesoscasoactual;

        $totalafinidad = $porcentajeafinidadprocesos;

        if($tipofracturacasoactual == $tipofracturacasoacomparar) $totalafinidad += 20;
        if($faseciclovidacasoactual == $faseciclovidacasoacomparar) $totalafinidad += 9;

        $puntajematerial = 0;

        if($materialcasoactual == $materialcasoacomparar) $puntajematerial = 7;
        if($submaterialcasoactual == $submaterialcasoacomparar) $puntajematerial = 11;
        if($materialespcasoactual == $materialespcasoacomparar) $puntajematerial = 15;

        $totalafinidad += $puntajematerial;

        if($elemsuspcasoactual = $elemsuspcasoacomparar) $totalafinidad += 3;
        if($modifcondcasoactual = $modifcondcasoacomparar) $totalafinidad += 3;

        $casosafines['id'][$k] = $casosfinalizados[$k];
        $casosafines['afinidad'][$k] = $totalafinidad;
    //fin for
    }

    $this->casos_model->insertar_afinidades($casosafines);

    $afinidadesordenadas['idcasos'] = $this->casos_model->devolver_idsafinidadesord();
    $afinidadesordenadas['afinidades'] = $this->casos_model->devolver_afinidadesord();

    $this->casos_model->eliminar_afinidades();

    //Fin del paso 1 de obtener las afinidades ordenadas, ahora obtenemos el bloque más relevante junto con conclusión general.

    for($i = 0; $i <count($afinidadesordenadas['idcasos']); $i++)
    {
      $conclusiongeneral[$i] = $this->casos_model->devolver_conclusiongeneralparasugerencia($afinidadesordenadas['idcasos'][$i]);;
      $bloquehipotesismasrelevante[$i] = $this->casos_model->devolver_bloquehipotesismasrelevante($afinidadesordenadas['idcasos'][$i]);
      $concluhipotesismasrelevante[$i] = $this->casos_model->devolver_concluhipotesismasrelevante($afinidadesordenadas['idcasos'][$i],$bloquehipotesismasrelevante[$i]);;

      if ($i==10) $i = count($afinidadesordenadas['idcasos']);
    }

    $datossugerencias = array(
       'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
       'id' => $idcaso,
       'idscasossimilaresconafinidades' => $afinidadesordenadas,
       'conclusionesgeneralescasossimilares' => $conclusiongeneral,
       'bloquesmasrelevantescasossimilares' => $bloquehipotesismasrelevante,
       'conclusionbloquemasrelevantecasossim' => $concluhipotesismasrelevante,
      );

    $this->casos_model->actualizarpaso($idcaso,'12');
    $this->load->view('sugerenciasdefallo.html',$datossugerencias);

  }

 


  public function iraconclusionesgenerales($idcaso)
  {
    $this->load->helper('url');

      $datosconclusiones = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->casos_model->actualizarpaso($idcaso,'13');
    $this->load->view('conclusionesgenerales.html',$datosconclusiones);

  }


  public function guardaredicion($idcaso,$guardarhasta)
  {
    $this->load->helper('url');

    if($guardarhasta==0)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->completar_caso($idcaso);
    }

    if($guardarhasta==1)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarintroduccion($idcaso);
      $this->completar_caso($idcaso);
    }

    if($guardarhasta==2)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarcomponente1($idcaso);

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
                                    if($i==2) $parathumb['queimagen'] = '8';
                                    if($i==3) $parathumb['queimagen'] = '9';
                                    if($i==4) $parathumb['queimagen'] = '3';
                                    if($i==5) $parathumb['queimagen'] = '2';
                                    if($i==6) $parathumb['queimagen'] = '2';
                                    if($i==7) $parathumb['queimagen'] = '2';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  }//fin for

      $this->completar_caso($idcaso);
    }

    if($guardarhasta==3 || $guardarhasta==4)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarcomponente2($idcaso);

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
                                    if($i==2) $parathumb['queimagen'] = '8';
                                    if($i==3) $parathumb['queimagen'] = '9';
                                    if($i==4) $parathumb['queimagen'] = '3';
                                    if($i==5) $parathumb['queimagen'] = '2';
                                    if($i==6) $parathumb['queimagen'] = '2';
                                    if($i==7) $parathumb['queimagen'] = '2';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  }//fin for

      $this->completar_caso($idcaso);
    }


    if($guardarhasta==5)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarcomponente2($idcaso);

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
                                    if($i==2) $parathumb['queimagen'] = '8';
                                    if($i==3) $parathumb['queimagen'] = '9';
                                    if($i==4) $parathumb['queimagen'] = '3';
                                    if($i==5) $parathumb['queimagen'] = '2';
                                    if($i==6) $parathumb['queimagen'] = '2';
                                    if($i==7) $parathumb['queimagen'] = '2';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  }//fin for

      $this->completar_caso($idcaso);
    }

    if($guardarhasta==6)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarcomponente2($idcaso);

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
                                    if($i==2) $parathumb['queimagen'] = '8';
                                    if($i==3) $parathumb['queimagen'] = '9';
                                    if($i==4) $parathumb['queimagen'] = '3';
                                    if($i==5) $parathumb['queimagen'] = '2';
                                    if($i==6) $parathumb['queimagen'] = '2';
                                    if($i==7) $parathumb['queimagen'] = '2';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  }//fin for

      $this->completar_caso($idcaso);
    }

    if($guardarhasta==7)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarcomponente2($idcaso);

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
                                    if($i==2) $parathumb['queimagen'] = '8';
                                    if($i==3) $parathumb['queimagen'] = '9';
                                    if($i==4) $parathumb['queimagen'] = '3';
                                    if($i==5) $parathumb['queimagen'] = '2';
                                    if($i==6) $parathumb['queimagen'] = '2';
                                    if($i==7) $parathumb['queimagen'] = '2';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  }//fin for

      $this->completar_caso($idcaso);
    }

    if($guardarhasta==8)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarcomponente2($idcaso);
      $this->casos_model->editardiscusion($idcaso);

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
                                    if($i==2) $parathumb['queimagen'] = '8';
                                    if($i==3) $parathumb['queimagen'] = '9';
                                    if($i==4) $parathumb['queimagen'] = '3';
                                    if($i==5) $parathumb['queimagen'] = '2';
                                    if($i==6) $parathumb['queimagen'] = '2';
                                    if($i==7) $parathumb['queimagen'] = '2';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  }//fin for

      $this->completar_caso($idcaso);
    }


    if($guardarhasta==9)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarcomponente2($idcaso);
      $this->casos_model->editardiscusion($idcaso);

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
                                    if($i==2) $parathumb['queimagen'] = '8';
                                    if($i==3) $parathumb['queimagen'] = '9';
                                    if($i==4) $parathumb['queimagen'] = '3';
                                    if($i==5) $parathumb['queimagen'] = '2';
                                    if($i==6) $parathumb['queimagen'] = '2';
                                    if($i==7) $parathumb['queimagen'] = '2';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  }//fin for

      $this->completar_caso($idcaso);
    }

    if($guardarhasta==10)
    {
      $this->casos_model->editartituloydescripcion($idcaso);
      $this->casos_model->editarcomponente2($idcaso);
      $this->casos_model->editardiscusion($idcaso);
      $this->casos_model->editarconclusionusuario($idcaso);

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
                                    if($i==2) $parathumb['queimagen'] = '8';
                                    if($i==3) $parathumb['queimagen'] = '9';
                                    if($i==4) $parathumb['queimagen'] = '3';
                                    if($i==5) $parathumb['queimagen'] = '2';
                                    if($i==6) $parathumb['queimagen'] = '2';
                                    if($i==7) $parathumb['queimagen'] = '2';

                                    $parathumb['idpieza'] =  $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
                                    $parathumb['filename'] =  $file_info['file_name'];

                                    $this->_create_thumbnail($parathumb);  
                                    
                                    $data = array('upload_data' => $this->upload->data());
                                    $imagen = $file_info['file_name'];    
                                    $data['imagen'] = $imagen;
                                    //$this->load->view('imagen_subida_view', $data);
                           }
                        }
                  }//fin for

      $this->completar_caso($idcaso);
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

}

?>