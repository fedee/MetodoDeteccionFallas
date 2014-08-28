<?php

class Paginacion extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->library('pagination');
      $this->load->model('noticias_model');
   }

   public function index() 
   {
      $this->load->helper('url');

      $config['base_url'] = site_url().'/paginacion/index/';
      $config['total_rows'] = $this->noticias_model->num_noticias();
      $config['per_page'] = 2;
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

      $data = array('noticias'=> $this->noticias_model->get_noticias($config['per_page']),
                    'paginacion' => $this->pagination->create_links());
      $this->load->view('paginacion_view',$data);
   }

  
}

?>