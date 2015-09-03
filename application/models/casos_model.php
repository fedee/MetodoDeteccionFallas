<?php
class Casos_model extends CI_Model{

   function __construct(){
      parent::__construct();
   }

   public function devolver_titulocaso()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>'0'));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->titulo;
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

   public function devolver_idcaso()
   {
      $consulta = $this->db->get_where('casos',array('id_asignado'=>'0'));
      $datos = array(); 
      foreach ($consulta->result() as $row)
      {
        $datos[] = $row->id;
      }
      return $datos;
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
      $var = $this->session->userdata('id');
      $this->db->insert('casos',array(
                                          'titulo'=>$this->input->post('titulo',TRUE),
                                          'descripcion'=>$this->input->post('descripcion',TRUE),
                                          'id_usuario'=>$var,
                                          'id_asignado'=>'0',
                                          'estado' => '0', //0= incompleto, 1=no aplicable, 2=finalizado
                                          'paso' => '0' //el numero del paso en el que estoy. por defecto, no he hecho ninguno. 
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

}
?>