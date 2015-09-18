<style>

   #paginacion{
      background-color: #808080;
      padding: 4px;
      margin: auto;
      width: 550px;
      text-align: center;
   }

   #paginacion a{
      color: #222;
      text-decoration: none;
      padding: 4px;
   }

   #paginacion a:hover{
      background-color: #333333;
      color: #C0C0C0;
   }

   .actual{
      color:#FFFFFF;
      padding: 4px;
   }


</style>

<?php

class Mensajes extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->library('pagination');
      $this->load->model('mensajes_model');
   }

   public function index() 
   {
      $this->load->helper('url');
   }

   public function iramensajes()
   {
      $this->load->helper('url');

      $config['base_url'] = site_url().'/mensajes/iramensajes';
      $config['total_rows'] = $this->mensajes_model->num_mensajes();
      $config['per_page'] = 4;
      $config['num_links'] = 5;
      $config['first_link'] = 'Primero';
      $config['last_link'] = 'Ultimo';
      $config['next_link'] = 'Siguiente';
      $config['prev_link'] = 'Anterior';

      $config['cur_tag_open'] = '<b class = "actual">';
      $config['cur_tag_close'] = '</b>';

      $config['full_tag_open'] = '<div id="paginacion">';
      $config['full_tag_close'] = '</div>';

      $this->pagination->initialize($config);

      $data = array('mensajes'=> $this->mensajes_model->get_mensajes($config['per_page']),
                    'paginacion' => $this->pagination->create_links());

      $this->load->view('mensajesview.html',$data);
   }


   public function vermsjenparticular($idmensaje)
   {
      $this->load->helper('url');

      $data = array('infomsj'=> $this->mensajes_model->devolver_todosobremsj($idmensaje),
                    'todaslasrtas' => $this->mensajes_model->devolver_respuestasamsj($idmensaje),
                   );

      $this->load->view('mensajeenparticular.html',$data);
   }

   public function nueva_respuesta($idpadre,$idparaquien)
   {
      $this->load->helper('url');

      if($this->input->post('submit_responder'))
      {

         $this->mensajes_model->insertar_respuesta($idpadre,$idparaquien);

         $data = array('infomsj'=> $this->mensajes_model->devolver_todosobremsj($idpadre),
                    'todaslasrtas' => $this->mensajes_model->devolver_respuestasamsj($idpadre),
                   );

         $this->load->view('mensajeenparticular.html',$data);

      }

      if($this->input->post('submit_volveramensajes'))
      {
         redirect(site_url().'/mensajes/iramensajes');
      }
   }

   public function enviarmensaje_ausuariocomun($idcaso)
   {
      $this->load->helper('url');

      $data = array('idcaso'=> $idcaso,
                   );

      $this->load->view('nuevomsjausuariocomun.html',$data);
   }

   public function nuevo_mensaje($idcaso)
   {
      $this->load->helper('url');

      if($this->input->post('submit_enviarmensaje'))
      {

         $this->mensajes_model->insertar_nuevomensaje($idcaso);

         redirect(site_url().'/usuarioespecialista/ver_casos');

      }

      if($this->input->post('submit_volveracasosasignados'))
      {
         redirect(site_url().'/usuarioespecialista/ver_casos');
      }
   }

  
}

?>