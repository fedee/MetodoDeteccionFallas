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
      $this->irafea($idcaso);
    }

    if ($numpaso == 6)
    {
      $this->iraensayos($idcaso);
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
    $this->load->helper('url');

      $datosfea = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->casos_model->actualizarpaso($idcaso,'5');
    $this->load->view('modulofea.html',$datosfea);

  }

  public function iraensayos($idcaso)
  {
    $this->load->helper('url');

      $datosensayo = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
        );

    $this->casos_model->actualizarpaso($idcaso,'6');
    $this->load->view('ensayos.html',$datosensayo);

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

  public function iraparetto($idcaso)
  {
    
    $this->load->helper('url');

    redirect(site_url().'/graficosparetto/construir_paretto/'.$idcaso);
    

  }

  

  public function irasugerenciasdefallo($idcaso)
  {
    $this->load->helper('url');

      $datossugerencias = array(
         'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
         'id' => $idcaso,
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

}

?>