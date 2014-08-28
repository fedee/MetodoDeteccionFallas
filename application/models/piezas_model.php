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

   public function devolver_idpiezaporidcaso($idcaso)
   {
      $consulta = $this->db->get_where('pieza',array('id_caso'=>$idcaso));
      $row = $consulta->row(1);
      $idpieza = $row->id;
      return $idpieza;
   }

   function actualizarimagenescomp1($parapiezasmodel)
    {
        $data = array(
            'queimagen' => $parapiezasmodel['queimagen'],
            'urlimagen' => $parapiezasmodel['filename'],
            'id_pieza' => $parapiezasmodel['idpieza']
        );
        
        $this->db->insert('imagenes', $data);
    }

}
?>