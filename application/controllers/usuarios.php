<?php

class Usuarios extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->library('form_validation');
      $this->load->model('usuarios_model');
   }

   public function index() 
   {
      $this->load->helper('url');
      $this->load->view('login.html');
   }

   public function registroucomun()
   {
      $this->load->helper('url');
      $this->load->view('registrousuariocomun.html');
   }

    public function registrouesp()
   {
      $this->load->helper('url');
      $this->load->view('registrousuarioespecialista.html');
   }

   public function registrouc_very()
   {
      $this->load->helper('url');

      if($this->input->post('submit_reg'))
      {
         $this->form_validation->set_rules('nombre','Nombre','required');
         $this->form_validation->set_rules('apellido','Apellido','required');
         $this->form_validation->set_rules('domicilio','Domicilio','required');
         $this->form_validation->set_rules('telefono','Telefono','required');
         $this->form_validation->set_rules('correo','Correo','required|trim|valid_email|callback_very_correo');
         $this->form_validation->set_rules('usuario','Usuario','required|trim|callback_very_user');
         $this->form_validation->set_rules('pass','Contraseña','required|trim|min_length[6]');
         $this->form_validation->set_rules('pass2','Confirmar Contraseña','required|trim|matches[pass]');

         $this->form_validation->set_message('required','El campo %s es obligatorio.');
         $this->form_validation->set_message('very_user','El %s ya existe.');
         $this->form_validation->set_message('very_correo','El %s ya está registrado en el sitio.');
         $this->form_validation->set_message('valid_email','Debe ingresar un %s valido.');
         $this->form_validation->set_message('matches','Las contraseñas no coinciden.');
         $this->form_validation->set_message('min_length','La contraseña debe poseer al menos 6 caracteres.');
      

         if($this->form_validation->run() != FALSE)
         {
            $this->usuarios_model->add_usuariocomun();
            $data = array('mensaje'=>'Ahora puede iniciar sesión.');
            $this->load->view('login.html',$data);
         }
         else
         {
            $this->load->view('registrousuariocomun.html');
         }
      }

      else
      {
         redirect(site_url().'/usuarios/registroucomun');
      }
   }


   public function registroue_very()
   {
      $this->load->helper('url');

      if($this->input->post('submit_reg'))
      {
         $this->form_validation->set_rules('nombreempresa','Nombre Empresa','required');
         $this->form_validation->set_rules('respuso','Responsable de Uso','required');
         $this->form_validation->set_rules('domicilio','Domicilio','required');
         $this->form_validation->set_rules('telefono','Telefono','required');
         $this->form_validation->set_rules('correo','Correo','required|trim|valid_email|callback_very_correo');
         $this->form_validation->set_rules('usuario','Usuario','required|trim|callback_very_user');
         $this->form_validation->set_rules('pass','Contraseña','required|trim|min_length[6]');
         $this->form_validation->set_rules('pass2','Confirmar Contraseña','required|trim|matches[pass]');

         $this->form_validation->set_message('required','El campo %s es obligatorio.');
         $this->form_validation->set_message('very_user','El %s ya existe.');
         $this->form_validation->set_message('very_correo','El %s ya está registrado en el sitio.');
         $this->form_validation->set_message('valid_email','Debe ingresar un %s valido.');
         $this->form_validation->set_message('matches','Las contraseñas no coinciden.');
         $this->form_validation->set_message('min_length','La contraseña debe poseer al menos 6 caracteres.');
      

         if($this->form_validation->run() != FALSE)
         {
            $this->usuarios_model->add_usuarioespecialista();
            $data = array('mensaje'=>'El usuario queda pendiente a revisión del administrador para ser activado.');
            $this->load->view('login.html',$data);
         }
         else
         {
            $this->load->view('registrousuarioespecialista.html');
         }
      }

      else
      {
         redirect(site_url().'/usuarios/registrouesp');
      }
   }

   
   function very_user($user)
   {
      $variable = $this->usuarios_model->very($user,'usuario');
      if($variable == true)
      {
         return false;
      }
      else
      {
         return true;
      }
   }

   function very_correo($correo)
   {
      $variable = $this->usuarios_model->very($correo,'correo');
      if($variable == true)
      {
         return false;
      }
      else
      {
         return true;
      }
   }

   public function very_sesion()
   {
      $this->load->helper('url');
      if($this->input->post('submit'))
      {
         $variable = $this->usuarios_model->very_sesion($this->input->post('user'));

         if($variable == true)
         {
            $tipo = $this->usuarios_model->devolver_tipo($this->input->post('user'));
            $activo = $this->usuarios_model->devolver_activo($this->input->post('user')); 
            $id = $this->usuarios_model->devolver_id($this->input->post('user')); 

            $variables = array(
                              'usuario' => $this->input->post('user'),
                              'tipo' => $tipo,
                              'activo' => $activo,
                              'id' => $id,
                              );

            $this->session->set_userdata($variables);

            if($this->session->userdata('activo')==1)
            {
               if($this->session->userdata('tipo')==0)
               {
                  redirect(site_url().'/dashboardadmin');
               }
               if($this->session->userdata('tipo')==1)
               {
                  redirect(site_url().'/dashboardusuarioespecialista');
               }
               if($this->session->userdata('tipo')==2)
               {
                  redirect(site_url().'/dashboardusuariocomun');
               }
            }
            else
            {
               $data = array('mensaje' => 'El Usuario no ha sido activado aún.');
               $this->load->view('login.html',$data);
            }
            
         }
         else
         {
            $data = array('mensaje' => 'El Usuario/Contraseña son incorrectos.');
            $this->load->view('login.html',$data);
         }
      }
      else
      {
         redirect(site_url().'/usuarios/');
      }
   }
}

?>