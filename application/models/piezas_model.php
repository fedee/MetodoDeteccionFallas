<?php
class Piezas_model extends CI_Model{

   function __construct(){
      parent::__construct();
   }

   public function guardainfo_casointroduccion($idcaso)
   {
      $this->db->insert('pieza',array(
                                          'fallo_multiplesoc'=>$this->input->post('fallo',TRUE),
                                          'ttrabajo_tiempo'=>$this->input->post('ttrabajo',TRUE),
                                          'ttrabajo_cantidad'=>$this->input->post('ctrabajo',TRUE),
                                          'vutil_tiempo'=>$this->input->post('tvidautil',TRUE),
                                          'vutil_cantidad' =>$this->input->post('cvidautil',TRUE),
                                          'fase_ciclovida'=>$this->input->post('faseciclo',TRUE),
                                          'id_caso'=>$idcaso,
                                          ));

   }

   public function guardainfo_casocomponente1($idcaso)
   {
      $data = array(
               'nombregen'=>$this->input->post('nombregen',TRUE),
               'codinterno'=>$this->input->post('codigoint',TRUE),
               'cantidadfalladas'=>$this->input->post('cantpiezas',TRUE),
               'usopieza'=>$this->input->post('usopieza',TRUE),
               'montadabien'=>$this->input->post('siguiendonorma',TRUE),
            );

      $this->db->where('id_caso', $idcaso);
      $this->db->update('pieza', $data); 

   }

  //Guardos IDs de los selectores, cuando vaya a hacer una view tipo la de edición tengo que fijarme a que nombre corresponde dicho ID del
  //selector en particular.

   public function guardainfo_casocomponente2($idcaso)
   {
      $data = array(
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

   public function devolver_idpiezaporidcaso($idcaso)
   {
      $consulta = $this->db->get_where('pieza',array('id_caso'=>$idcaso));
      $row = $consulta->row(1);
      $idpieza = $row->id;
      return $idpieza;
   }

   function actualizarimagenes($parapiezasmodel)
    {
        $data = array(
            'queimagen' => $parapiezasmodel['queimagen'],
            'urlimagen' => $parapiezasmodel['filename'],
            'id_pieza' => $parapiezasmodel['idpieza']
        );
        
        $this->db->insert('imagenes', $data);
    }

    function guardarimgparetto($tituloimg,$idcaso)
    {
        $data = array(
            'queimagen' => "10",
            'urlimagen' => $tituloimg,
            'id_pieza' => $this->devolver_idpiezaporidcaso($idcaso)
        );
        
        $this->db->insert('imagenes', $data);
    }


    public function guardainfo_casofabricacion($idcaso)
   {
      $numeroatr = count($_POST);
      $tags = array_keys($_POST);
      $valores = array_values($_POST);

      $cantidad_procesos = $this->devolver_cantidadprocesos($idcaso);

      $this->guardarinfo_proveedores($idcaso,$cantidad_procesos);

      $proceso = $valores[0];
      $subtipo = $valores[1];

      for($i = 7; $i<($numeroatr-1); $i++)
      {
          if($valores[$i] != "")
          {
              $consulta = $this->db->get_where('atributos',array('atributo'=>$tags[$i]));
              $row = $consulta->row(1);
              $espadre = $row->parent_id;

              if($espadre != 0){
                $espadre = 0;
              }
              else
              {
                $espadre = 1;
              }


              $this->db->insert('fabricacion_listaprocesos',array(
                                              'id_caso'=>$idcaso,
                                              'numero_proceso'=>($cantidad_procesos),
                                              'proceso'=>$proceso,
                                              'subtipo'=>$subtipo,
                                              'param_nombre'=>$tags[$i],
                                              'param_valor' => $valores[$i],
                                              'es_general' => $espadre 
                                              ));
          }

      }

      /*$numeroatr = count($_POST);
      $tags = array_keys($_POST);
      $valores = array_values($_POST);

      for($i = 0; $i<($numeroatr); $i++)
      {
          echo $tags[$i];
          echo ": ";
          echo $valores[$i];
          //echo ", es Padre: ";
          //echo $espadre;
          echo "</br>";
      }*/

       

      //Para cuando haga el panel de edición, puedo fijarme el nombre de los atributos en "atributos" para relacionar las tablas con
      //la de precargados, y de ahi precargar los selectores que hagan falta via comparación de IDs. Revisar los nombres porque puede 
      //llegar a haber conflictos con nombres iguales de atributos. Si todos son distintos va a andar perfecto.

   }


    public function guardarinfo_proveedores($idcaso,$numproceso)
   {
      
      $this->db->insert('proveedorprocesos',array(
                        'id_caso'=>$idcaso,
                        'numero_proceso'=>$numproceso,
                        'empresa'=> $this->input->post('empresaproveedor',TRUE),
                        'responsable'=> $this->input->post('responsableproveedor',TRUE),
                        'correoelectronico'=> $this->input->post('correoproveedor',TRUE),
                        'telefono' => $this->input->post('telefonoproveedor',TRUE),
                        'direccion' => $this->input->post('direccionproveedor',TRUE),
                       ));

   }

   public function devolver_cantidadprocesos($idcaso)
   {
      $this->db->distinct();
      $this->db->select('numero_proceso');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('fabricacion_listaprocesos');
      $cantidad = 0;

      foreach ($consulta->result() as $row)
      {
        $cantidad = $cantidad+1;
      }

      return ($cantidad+1);
   }


   public function guardainfo_ensayos($idcaso,$cantidadimagenes)
   {

      $this->db->insert('ensayos',array(
                                          'id_caso'=>$idcaso,
                                          'numero_ensayo'=>$this->devolver_cantidadensayos($idcaso),
                                          'nombre'=>$this->input->post('nombreensayo',TRUE),
                                          'descripcion'=>$this->input->post('descripcionensayo',TRUE),
                                          'cant_imagenes' =>$cantidadimagenes,
                                          ));

   }

   public function devolver_cantidadensayos($idcaso)
   {
      $this->db->distinct();
      $this->db->select('numero_ensayo');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('ensayos');
      $cantidad = 0;

      foreach ($consulta->result() as $row)
      {
        $cantidad = $cantidad+1;
      }

      return ($cantidad+1);
   }

   public function guardainfo_macrografia($idcaso,$iddescripcion)
   {
      $this->db->insert('macrografia',array(
                                          'id_caso'=>$idcaso,
                                          'id_descripcion'=>$iddescripcion,
                                          'descripcion'=>$this->input->post('descripcionmacro'.$iddescripcion,TRUE),
                                          'tipo_fractura'=>$this->input->post('fractura',TRUE),
                                          ));

   }

   public function guardainfo_micrografia($idcaso,$iddescripcion)
   {
      $this->db->insert('micrografia',array(
                                          'id_caso'=>$idcaso,
                                          'id_descripcion'=>$iddescripcion,
                                          'descripcion'=>$this->input->post('descripcionmicro'.$iddescripcion,TRUE),
                                          ));

   }

   public function guardainfo_discusion($idcaso)
   {
      $this->db->insert('discusion',array(
                                          'id_caso'=>$idcaso,
                                          'discusion'=>$this->input->post('comentariogeneral',TRUE),
                                          ));

   }

   public function guardainfo_hipotesis($idcaso,$cantidadimagenes)
   {

      $this->db->insert('hipotesis',array(
                                          'id_caso'=>$idcaso,
                                          'numero_hipotesis'=>$this->devolver_cantidadhipotesis($idcaso),
                                          'titulo'=>$this->input->post('titulohipo',TRUE),
                                          'descripcion'=>$this->input->post('descripcionhipo',TRUE),
                                          'valoracion'=>$this->input->post('valoracionhipo',TRUE),
                                          'cant_imagenes' =>$cantidadimagenes,
                                          ));

   }

   public function devolver_cantidadhipotesis($idcaso)
   {
      $this->db->distinct();
      $this->db->select('numero_hipotesis');
      $this->db->where('id_caso', $idcaso); 
      $consulta = $this->db->get('hipotesis');
      $cantidad = 0;

      foreach ($consulta->result() as $row)
      {
        $cantidad = $cantidad+1;
      }

      return ($cantidad+1);
   }

   public function devolver_cantidadhipotesisparetto($idcaso)
   {
      $this->db->like('id_caso', $idcaso);
      $this->db->from('hipotesis');
      $cantidad = $this->db->count_all_results();
      return $cantidad;
   }

   public function guardainfo_conclusion($idcaso)
   {
      $this->db->insert('conclusionusuario',array(
                                          'id_caso'=>$idcaso,
                                          'conclusion'=>$this->input->post('conclusiongeneral',TRUE),
                                          ));

   }

   public function devolver_todosobrelapieza($idcaso)
   {
      $consulta = $this->db->get_where('pieza',array('id_caso'=>$idcaso));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos['fallo_multiplesoc'] = $row->fallo_multiplesoc;
        $datos['ttrabajo_tiempo'] = $row->ttrabajo_tiempo;
        $datos['ttrabajocant'] = $row->ttrabajo_cantidad;
        $datos['vutil_tiempo'] = $row->vutil_tiempo;
        $datos['vutil_cantidad'] = $row->vutil_cantidad;
        $datos['fase_ciclovida'] = $row->fase_ciclovida;
        $datos['nombregen'] = $row->nombregen;
        $datos['codinterno'] = $row->codinterno;
        $datos['cantidadfalladas'] = $row->cantidadfalladas;
        $datos['usopieza'] = $row->usopieza;
        $datos['montadabien'] = $row->montadabien;
        $datos['material'] = $row->material;
        $datos['submaterial'] = $row->submaterial;
        $datos['especifico'] = $row->especifico;
        $datos['descdetallada'] = $row->descdetallada;
        $datos['tipocargas'] = $row->tipocargas;
        $datos['umedidacargas'] = $row->umedidacargas;
        $datos['cantcargas'] = $row->cantcargas;
        $datos['tiposujeciones'] = $row->tiposujeciones;
        $datos['condtermicas'] = $row->condtermicas;
        $datos['utempcondtermicas'] = $row->utempcondtermicas;
        $datos['cantidadtermica'] = $row->cantidadtermica;
        $datos['tipopresiones'] = $row->tipopresiones;
        $datos['distribpresiones'] = $row->distribpresiones;
        $datos['umedidapres'] = $row->umedidapres;
        $datos['valpresion'] = $row->valpresion;
        $datos['veloctrab'] = $row->veloctrab;
        $datos['trayectoria'] = $row->trayectoria;
        $datos['unidadveloc'] = $row->unidadveloc;
        $datos['valveloc'] = $row->valveloc;
        $datos['elemsusp'] = $row->elemsusp;
        $datos['valsusp'] = $row->valsusp;
        $datos['modifcond'] = $row->modifcond;
        $datos['modificaciones'] = $row->modificaciones;
      }
      return $datos;
   }

   public function devolver_todaslasurlimagenespieza($idpieza)
   {
      $consulta = $this->db->get_where('imagenes',array(
                                                         'id_pieza'=>$idpieza,
                                                       ));
   if ($consulta->num_rows() > 0) {
      $consulta = $consulta->result_array();
      return $consulta;
   }      
   }

   public function devolver_todosobreelproveedor($idcaso,$numeroproceso)
   {
      $consulta = $this->db->get_where('proveedorprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos['empresa'] = $row->empresa;
        $datos['responsable'] = $row->responsable;
        $datos['correoelectronico'] = $row->correoelectronico;
        $datos['telefono'] = $row->telefono;
        $datos['direccion'] = $row->direccion;
        
      }
      return $datos;  
   }


   public function devolver_cantidadprocesosgenerales($idcaso,$numeroproceso)
   {
      $consulta = $this->db->get_where('fabricacion_listaprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      $cantidad = 0;

      foreach ($consulta->result() as $row)
      {
        if($row->es_general == 1) $cantidad = $cantidad + 1;
      }

      return ($cantidad);
   }

   public function devolver_cantidadprocesosespecificos($idcaso,$numeroproceso)
   {
      $consulta = $this->db->get_where('fabricacion_listaprocesos',array(
                                                         'id_caso'=>$idcaso,
                                                         'numero_proceso'=>$numeroproceso,
                                                       ));
      $cantidad = 0;

      foreach ($consulta->result() as $row)
      {
        if($row->es_general == 0) $cantidad = $cantidad + 1;
      }

      return ($cantidad);
   }


   public function devolver_datosatributo($atributo,$idproceso)
   {
      $consulta = $this->db->get_where('atributos',array(
                                                         'atributo'=>$atributo,
                                                         'id_proceso'=>$idproceso,
                                                       ));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos['leyenda'] = $row->leyenda;
        $datos['tipo_campo'] = $row->tipo_campo;
        $datos['id'] = $row->id;
        
      }
      return $datos;  
   }

}
?>