<?php
class Casos_model extends CI_Model{

   function __construct(){
      parent::__construct();
   }

   public function devolver_titulocaso()
   {
      $idusuario = $this->session->userdata('id');

      $consulta = $this->db->get_where('casos',array(
                                                         'id_usuario'=>$idusuario,
                                                       ));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->titulo;
      }
      return $datos;
   }

   public function devolver_tituloydescporidcaso($idcaso)
   {
      $consulta = $this->db->get_where('casos',array('id'=>$idcaso));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos['titulo'] = $row->titulo;
        $datos['descripcion'] = $row->descripcion;
      }
      return $datos;
   }


   public function devolver_nombreusuarioporidcaso($idcaso)
   {
      $consulta = $this->db->get_where('casos',array('id'=>$idcaso));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $idusuario = $row->id_usuario;
      }

      $nombreusuario = $this->devolver_nombreusuarioporid($idusuario);

      return $nombreusuario;
   }

   public function devolver_nombreusuarioporid($idusuario)
   {
      $consulta = $this->db->get_where('usuarios',array('id'=>$idusuario));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos['nombre'] = $row->nombre;
        $datos['apellido'] = $row->apellido;
      }
      return $datos;
   }

   public function devolver_descripcioncaso()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>'0'));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->descripcion;
      }
      return $datos;
   }

   public function devolver_tituloscasossinasignar()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>'0'));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->titulo;
      }
      return $datos;
   }

   public function devolver_idscasossinasignar()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>'0'));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->id;
      }
      return $datos;
   }

   public function devolver_idcaso()
   {
      $idusuario = $this->session->userdata('id');

      $this->db->select('id');
      $this->db->where('id_usuario', $idusuario); 
      $this->db->order_by('id', 'desc');
      $consulta = $this->db->get('casos');

      $row = $consulta->row(0);
      $idcaso = $row->id;
      return $idcaso;
   }

    public function devolver_fecharegistrocaso()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>'0'));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->fecha_alta;
      }
      return $datos;
   }

   public function asignar_caso($idesp,$idcaso)
   {
      $data = array(
               'id_asignado' => $idesp,
            );

      $this->db->where('id', $idcaso);
      $this->db->update('casos', $data); 
     redirect(site_url().'/administrador/asignar_casos');
   }

   public function add_nuevocaso()
   {
      $fechaactual = date('Y-m-d H:i:s');

      $var = $this->session->userdata('id');
      $this->db->insert('casos',array(
                                          'titulo'=>$this->input->post('titulo',TRUE),
                                          'descripcion'=>$this->input->post('descripcion',TRUE),
                                          'id_usuario'=>$var,
                                          'id_asignado'=>'0',
                                          'estado' => '0', //0= incompleto, 1=no aplicable, 2=finalizado
                                          'paso' => '0', //el numero del paso en el que estoy. por defecto, no he hecho ninguno. 
                                          'fecha_alta' => $fechaactual,
                                          ));
   }

   public function devolver_espectituloasignado()
   {
      $var = $this->session->userdata('id');
      $consulta = $this->db->get_where('casos',array('id_asignado'=>$var));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->titulo;
      }
      return $datos;
   }

   public function devolver_espdescasignada()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>$this->session->userdata('id')));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->descripcion;
      }
      return $datos;
   }

   public function devolver_espfechaasignada()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>$this->session->userdata('id')));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->fecha_alta;
      }
      return $datos;
   }

   public function devolver_espidcasoasignado()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>$this->session->userdata('id')));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->id;
      }
      return $datos;
   }

   public function devolver_comuntituloasignado()
   {
      $var = $this->session->userdata('id');
      $consulta = $this->db->get_where('casos',array('id_usuario'=>$var));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->titulo;
      }
      return $datos;
   }

   public function devolver_comundescasignada()
   {
      $consulta = $this->db->get_where('casos',array('id_usuario'=>$this->session->userdata('id')));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->descripcion;
      }
      return $datos;
   }

   public function devolver_comunfechaasignada()
   {
      $consulta = $this->db->get_where('casos',array('id_usuario'=>$this->session->userdata('id')));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->fecha_alta;
      }
      return $datos;
   }

   public function devolver_comunidcasoasignado()
   {
      $consulta = $this->db->get_where('casos',array('id_usuario'=>$this->session->userdata('id')));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->id;
      }
      return $datos;
   }

   public function devolver_comunestadocaso()
   {
      $consulta = $this->db->get_where('casos',array('id_usuario'=>$this->session->userdata('id')));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->estado;
      }
      return $datos;
   }

   public function devolver_tituloporid($idcaso)
   {
      $consulta = $this->db->get_where('casos',array('id'=>$idcaso));
      $row = $consulta->row(1);
      $titulo = $row->titulo;
      return $titulo;
   }

   public function actualizarestado($idcaso,$estado)
   {
      $data = array(
               'estado' => $estado,
            );

      $this->db->where('id', $idcaso);
      $this->db->update('casos', $data); 
   }

   public function devolver_cantidadcasosasignadosesp()
   {
      $this->db->like('id_asignado', $this->session->userdata('id'));
      $this->db->from('casos');
      $cantidad = $this->db->count_all_results();
      return $cantidad;
   }

   public function devolver_numeropaso($idcaso)
   {
      $consulta = $this->db->get_where('casos',array('id'=>$idcaso));
      $row = $consulta->row(1);
      $paso = $row->paso;
      return $paso;
   }

    public function actualizarpaso($idcaso,$paso)
   {
      $data = array(
               'paso' => $paso,
            );

      $this->db->where('id', $idcaso);
      $this->db->update('casos', $data); 
   }

   public function devolver_procesos($idcaso)
   {

      $this->db->distinct();
      $this->db->select('numero_proceso');
      $this->db->select('proceso');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('fabricacion_listaprocesos');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $this->devolver_nombreprocesoporid($row->proceso);
      }
      return $datos;
   }


   public function devolver_numeroprocesoparatabla($idcaso)
   {

      $this->db->distinct();
      $this->db->select('numero_proceso');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('fabricacion_listaprocesos');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->numero_proceso;
      }
      return $datos;
   }

   public function devolver_nombreprocesoporid($idproceso)
   {
      $consulta = $this->db->get_where('procesosfab_generales',array('id'=>$idproceso));
      $row = $consulta->row(1);
      $nombre = $row->nombre;
      return $nombre;
   }

   public function devolver_subtipos($idcaso)
   {

      $this->db->distinct();
      $this->db->select('numero_proceso');
      $this->db->select('subtipo');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('fabricacion_listaprocesos');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        if($row->subtipo == 0)
           {
             $datos[] = "Ninguno";
           }
        else
           {
            $datos[] = $this->devolver_nombresubtipoporid($row->subtipo);
           }
      }
      return $datos;
   }

   public function devolver_nombresubtipoporid($idproceso)
   {
      $consulta = $this->db->get_where('procesosfab_especificos',array('id'=>$idproceso));
      $row = $consulta->row(1);
      $nombre = $row->nombre;
      return $nombre;
   }

   public function devolver_nombresubtipoporidparaedicion($idsub)
   {
      if($idsub == 0)
           {
             return "Ninguno";
           }
        else
           {
            $consulta = $this->db->get_where('procesosfab_especificos',array('id'=>$idsub));
            $row = $consulta->row(1);
            $nombre = $row->nombre;
            return $nombre;
           }
      
   }

   public function devolver_tituloparaparetto($idcaso)
   {

      $this->db->select('titulo');
      $this->db->select('valoracion');
      $this->db->where('id_caso', $idcaso); 
      $this->db->order_by('valoracion', 'desc');
      $consulta = $this->db->get('hipotesis');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->titulo;
      }
      return $datos;
   }

   public function devolver_valoracionesparaparetto($idcaso)
   {

      $this->db->select('valoracion');
      $this->db->where('id_caso', $idcaso); 
      $this->db->order_by('valoracion', 'desc');
      $consulta = $this->db->get('hipotesis');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->valoracion;
      }
      return $datos;
   }

   public function devolver_nombresparametrosgen($idcaso,$numeroproceso)
   {

      $consulta = $this->db->get_where('fabricacion_listaprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        if($row->es_general == 1) $datos[] = $row->param_nombre;
      }

      return $datos;
   }

   public function devolver_nombresparametrosesp($idcaso,$numeroproceso)
   {

      $consulta = $this->db->get_where('fabricacion_listaprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        if($row->es_general == 0) $datos[] = $row->param_nombre;
      }

      return $datos;
   }

   public function devolver_idprocesopornombre($nombreproceso)
   {
      $consulta = $this->db->get_where('procesosfab_generales',array('nombre'=>$nombreproceso));
      $row = $consulta->row(1);
      $id = $row->id;
      return $id;
   }

   public function devolver_idprocesopoidcasoynumeroproceso($idcaso,$numeroproceso)
   {
      $consulta = $this->db->get_where('fabricacion_listaprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      $row = $consulta->row(1);
      $id = $row->proceso;
      return $id;
   }

   public function devolver_idsubprocesopoidcasoynumeroproceso($idcaso,$numeroproceso)
   {
      $consulta = $this->db->get_where('fabricacion_listaprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      $row = $consulta->row(1);
      $id = $row->subtipo;
      return $id;
   }

   public function devolver_valoresparametrosgen($idcaso,$numeroproceso)
   {

      $consulta = $this->db->get_where('fabricacion_listaprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        if($row->es_general == 1) $datos[] = $row->param_valor;
      }

      return $datos;
   }

   public function devolver_valoresparametrosesp($idcaso,$numeroproceso)
   {

      $consulta = $this->db->get_where('fabricacion_listaprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      
      $datos = array(); 

      foreach ($consulta->result() as $row)
      {
        if($row->es_general == 0) $datos[] = $row->param_valor;
      }

      return $datos;
   }

   public function devolver_nompreprecargadoporid($idprecargado)
   {
      $consulta = $this->db->get_where('precargados',array('id'=>$idprecargado));
      $row = $consulta->row(1);
      $nombre = $row->nombre_atributo;
      return $nombre;
   }

   public function devolver_discusionporidcaso($idcaso)
   {
      $consulta = $this->db->get_where('discusion',array('id_caso'=>$idcaso));
      $row = $consulta->row(1);
      $disc = $row->discusion;
      return $disc;
   }

   public function devolver_conclusionporidcaso($idcaso)
   {
      $consulta = $this->db->get_where('conclusionusuario',array('id_caso'=>$idcaso));
      $row = $consulta->row(1);
      $conc = $row->conclusion;
      return $conc;
   }


   public function editartituloydescripcion($idcaso)
   {
      $data = array(
               'titulo'=>$this->input->post('titulo',TRUE),
               'descripcion'=>$this->input->post('descripcion',TRUE),
            );

      $this->db->where('id', $idcaso);
      $this->db->update('casos', $data); 
   }

   public function devolver_titulocasoparaedicion($idcaso)
   {
     $consulta = $this->db->get_where('casos',array(
                                                         'id'=>$idcaso,
                                                       ));
      $row = $consulta->row(1);
      $tit = $row->titulo;
      return $tit;
   }

   public function devolver_descripcioncasoparaedicion($idcaso)
   {
     $consulta = $this->db->get_where('casos',array(
                                                         'id'=>$idcaso,
                                                       ));
      $row = $consulta->row(1);
      $desc = $row->descripcion;
      return $desc;
   }

   public function devolver_nombresensayos($idcaso)
   {

      $this->db->select('nombre');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('ensayos');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->nombre;
      }
      return $datos;
   }

   public function devolver_descripcionesensayos($idcaso)
   {

      $this->db->select('descripcion');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('ensayos');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->descripcion;
      }
      return $datos;
   }

   public function devolver_descripcionmacro($idcaso)
   {

      $this->db->select('descripcion');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('macrografia');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->descripcion;
      }
      return $datos;
   }

   public function devolver_tipofracturamacro($idcaso)
   {

      $this->db->select('tipo_fractura');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('macrografia');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->tipo_fractura;
      }
      return $datos;
   }

   public function devolver_descripcionmicro($idcaso)
   {

      $this->db->select('descripcion');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('micrografia');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->descripcion;
      }
      return $datos;
   }

   public function devolver_titulohipotesis($idcaso)
   {

      $this->db->select('titulo');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('hipotesis');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->titulo;
      }
      return $datos;
   }

   public function devolver_descripcionhipotesis($idcaso)
   {

      $this->db->select('descripcion');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('hipotesis');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->descripcion;
      }
      return $datos;
   }

   public function devolver_discusionparaedicion($idcaso)
   {
      $consulta = $this->db->get_where('discusion',array(
                                                         'id_caso'=>$idcaso,
                                                       ));
      $row = $consulta->row(1);
      $disc = $row->discusion;
      return $disc;

   }

   public function devolver_conclusionparaedicion($idcaso)
   {
      $consulta = $this->db->get_where('conclusionusuario',array(
                                                         'id_caso'=>$idcaso,
                                                       ));
      $row = $consulta->row(1);
      $conc = $row->conclusion;
      return $conc;

   }

   public function devolver_numerosensayos($idcaso)
   {

      $this->db->select('numero_ensayo');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('ensayos');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->numero_ensayo;
      }
      return $datos;
   }

   public function editarintroduccion($idcaso)
   {
      $data = array(
               'fallo_multiplesoc'=>$this->input->post('fallo',TRUE),
               'ttrabajo_tiempo'=>$this->input->post('ttrabajo',TRUE),
               'ttrabajo_cantidad'=>$this->input->post('ctrabajo',TRUE),
               'vutil_tiempo'=>$this->input->post('tvidautil',TRUE),
               'vutil_cantidad'=>$this->input->post('cvidautil',TRUE),
               'fase_ciclovida'=>$this->input->post('faseciclo',TRUE),
            );

      $this->db->where('id_caso', $idcaso);
      $this->db->update('pieza', $data); 
   }

   public function editarcomponente1($idcaso)
   {
      $data = array(
               'fallo_multiplesoc'=>$this->input->post('fallo',TRUE),
               'ttrabajo_tiempo'=>$this->input->post('ttrabajo',TRUE),
               'ttrabajo_cantidad'=>$this->input->post('ctrabajo',TRUE),
               'vutil_tiempo'=>$this->input->post('tvidautil',TRUE),
               'vutil_cantidad'=>$this->input->post('cvidautil',TRUE),
               'fase_ciclovida'=>$this->input->post('faseciclo',TRUE),
               'nombregen'=>$this->input->post('nombregen',TRUE),
               'codinterno'=>$this->input->post('codigoint',TRUE),
               'cantidadfalladas'=>$this->input->post('cantpiezas',TRUE),
               'usopieza'=>$this->input->post('usopieza',TRUE),
               'montadabien'=>$this->input->post('siguiendonorma',TRUE),
            );

      $this->db->where('id_caso', $idcaso);
      $this->db->update('pieza', $data); 
   }

   public function editarcomponente2($idcaso)
   {
      $data = array(
               'fallo_multiplesoc'=>$this->input->post('fallo',TRUE),
               'ttrabajo_tiempo'=>$this->input->post('ttrabajo',TRUE),
               'ttrabajo_cantidad'=>$this->input->post('ctrabajo',TRUE),
               'vutil_tiempo'=>$this->input->post('tvidautil',TRUE),
               'vutil_cantidad'=>$this->input->post('cvidautil',TRUE),
               'fase_ciclovida'=>$this->input->post('faseciclo',TRUE),
               'nombregen'=>$this->input->post('nombregen',TRUE),
               'codinterno'=>$this->input->post('codigoint',TRUE),
               'cantidadfalladas'=>$this->input->post('cantpiezas',TRUE),
               'usopieza'=>$this->input->post('usopieza',TRUE),
               'montadabien'=>$this->input->post('siguiendonorma',TRUE),
               'material'=>$this->input->post('material',TRUE),
               'submaterial'=>$this->input->post('submat',TRUE),
               'especifico'=>$this->input->post('matesp',TRUE),
               'descdetallada'=>$this->input->post('descdetallada',TRUE),
               'tipocargas'=>$this->input->post('tipocargas',TRUE),
               'umedidacargas'=>$this->input->post('umedida',TRUE),
               'cantcargas'=>$this->input->post('cantidad',TRUE),
               'tiposujeciones'=>$this->input->post('tiposujeciones',TRUE),
               'condtermicas'=>$this->input->post('condtermicas',TRUE),
               'utempcondtermicas'=>$this->input->post('utemp',TRUE),
               'cantidadtermica'=>$this->input->post('cantidadtermica',TRUE),
               'tipopresiones'=>$this->input->post('tipopresiones',TRUE),
               'distribpresiones'=>$this->input->post('distrib',TRUE),
               'umedidapres'=>$this->input->post('umedidapres',TRUE),
               'valpresion'=>$this->input->post('valpresion',TRUE),
               'veloctrab'=>$this->input->post('veloctrab',TRUE),
               'trayectoria'=>$this->input->post('trayectoria',TRUE),
               'unidadveloc'=>$this->input->post('unidadveloc',TRUE),
               'valveloc'=>$this->input->post('valveloc',TRUE),
               'elemsusp'=>$this->input->post('elemsusp',TRUE),
               'valsusp'=>$this->input->post('valsusp',TRUE),
               'modifcond'=>$this->input->post('modifcond',TRUE),
               'modificaciones'=>$this->input->post('modificaciones',TRUE),
            );

      $this->db->where('id_caso', $idcaso);
      $this->db->update('pieza', $data); 
   }

   public function editardiscusion($idcaso)
   {
      $data = array(
               'discusion'=>$this->input->post('comentariogeneral',TRUE),
            );

      $this->db->where('id_caso', $idcaso);
      $this->db->update('discusion', $data); 
   }

   public function editarconclusionusuario($idcaso)
   {
      $data = array(
               'conclusion'=>$this->input->post('conclusiongeneral',TRUE),
            );

      $this->db->where('id_caso', $idcaso);
      $this->db->update('conclusionusuario', $data); 
   }

   public function cambiarestadocaso($idcaso,$estado)
   {
      $data = array(
               'estado'=>$estado,
            );

      $this->db->where('id', $idcaso);
      $this->db->update('casos', $data); 
   }


   public function devolver_idcasosesp($idesp,$año,$mes,$paso)
   {

      $this->db->select('id');
      $this->db->select('fecha_alta');
      $this->db->where('id_asignado', $idesp); 
      $this->db->where('MONTH(fecha_alta)', $mes);
      $this->db->where('paso >=', $paso);
      $this->db->order_by('fecha_alta', 'asc');
      $consulta = $this->db->get('casos');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->id;
      }
      return $datos;
   }


   public function devolver_idcasosespsinfecha($idesp,$paso)
   {

      $this->db->select('id');
      $this->db->where('id_asignado', $idesp); 
      $this->db->where('paso >=', $paso);
      $consulta = $this->db->get('casos');
      
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->id;
      }
      return $datos;
   }

   public function devolver_tipofracturaporidcaso($idcaso)
   {
      $consulta = $this->db->get_where('macrografia',array(
                                                         'id_caso'=>$idcaso,
                                                       ));
      $row = $consulta->row(1);
      $tipofrac = $row->tipo_fractura;
      return $tipofrac;

   }

    public function devolver_siesfinalizado($idcaso)
   {
      $consulta = $this->db->get_where('casos',array(
                                                         'id'=>$idcaso,
                                                       ));
      $row = $consulta->row(1);
      $estado = $row->estado;
      return $estado;

   }

   public function devolver_faseciclovida($idpieza)
   {
      $consulta = $this->db->get_where('pieza',array(
                                                         'id'=>$idpieza,
                                                       ));
      $row = $consulta->row(1);
      $faseciclo = $row->fase_ciclovida;
      return $faseciclo;

   }

   public function devolver_modificaciones($idpieza)
   {
      $consulta = $this->db->get_where('pieza',array(
                                                         'id'=>$idpieza,
                                                       ));
      $row = $consulta->row(1);
      $mod = $row->modificaciones;
      return $mod;

   }

}
?>