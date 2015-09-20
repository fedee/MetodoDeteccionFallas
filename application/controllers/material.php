<?php

class Material extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->model('material_model');
   }

   public function index() 
   {
      $this->load->helper('url');
   }


   public function getSubMateriales()
   {
      $id_material = (int)$this->input->post('id_material');

      $submateriales = $this->material_model->devolver_submaterialesporidmaterial($id_material);
      echo "<option value= '0'>Elegir</option>";
      foreach ($submateriales as $key => $submaterial) {
         $valor = $submaterial['id'];
         $texto = $submaterial['nombre'];         
         echo "<option value='$valor'>$texto</option>";
      }
   }

    public function getMaterialesEspecificos()
   {
      $id_submaterial = (int)$this->input->post('id_submaterial');

      $materialesesp = $this->material_model->devolver_matespporidsubmaterial($id_submaterial);
      echo "<option value='0'>Elegir</option>";
      foreach ($materialesesp as $key => $matesp) {
         $valor = $matesp['id'];
         $texto = $matesp['nombre'];         
         echo "<option value='$valor'>$texto</option>";
      }
   }

   public function getMaterialEspPropiedades()
   {
      $id_matespec = (int)$this->input->post('id_matesp');

      $propiedades = $this->material_model->devolver_propmatesp($id_matespec);
      foreach ($propiedades as $key => $prop) {
         $dens = $prop['densidad'];
         $modelas = $prop['modulo_elastico'];
         $eporc = $prop['elongacion_porc'];
         $tenac = $prop['tenacidad'];
         $cpoisson = $prop['coef_poisson'];     
         echo "<thead>
                  <tr>
                     <th>Densidad</th>
                     <th>Límite elástico</th>
                     <th>Elongación porcentual</th>
                     <th>Tenacidad</th>
                     <th>Coeficiente de Poisson</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>$dens</td>
                     <td>$modelas</td>
                     <td>$eporc</td>
                     <td>$tenac</td>
                     <td>$cpoisson</td>
                  </tr>
               </tbody>";

      }
   }

}

?>