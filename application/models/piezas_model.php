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
               'maquinamontada'=>$this->input->post('maqomec',TRUE),
               'espmontaje'=>$this->input->post('especmontaje',TRUE),
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


    public function guardainfo_casofabricacion($idcaso)
   {
      $numeroatr = count($_POST);
      $tags = array_keys($_POST);
      $valores = array_values($_POST);

      $cantidad_procesos = $this->devolver_cantidadprocesos($idcaso);

      $proceso = $valores[0];
      $subtipo = $valores[1];

      for($i = 2; $i<($numeroatr-1); $i++)
      {
          if($valores[$i] != "")
          {
              $consulta = $this->db->get_where('atributos',array('atributo'=>$tags[$i]));
              $row = $consulta->row(1);
              $espadre = $row->parent_id;

              if($espadre != 0){
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

      /*echo $tags[$i];
        echo ": ";
        echo $valores[$i];
        echo ", es Padre: ";
        echo $espadre;
        echo "</br>";*/

      //Para cuando haga el panel de edición, puedo fijarme el nombre de los atributos en "atributos" para relacionar las tablas con
      //la de precargados, y de ahi precargar los selectores que hagan falta via comparación de IDs. Revisar los nombres porque puede 
      //llegar a haber conflictos con nombres iguales de atributos. Si todos son distintos va a andar perfecto.

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

}
?>