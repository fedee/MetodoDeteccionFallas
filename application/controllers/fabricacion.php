<?php

class Fabricacion extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->model('fabricacion_model');
   }

   public function index() 
   {
      $this->load->helper('url');
   }


   public function getsubtipos()
   {
      $id_proceso = (int)$this->input->post('id_proceso');

      $subtipos = $this->fabricacion_model->devolver_subtiposporidproceso($id_proceso);
      echo "<option value='0'>Elegir</option>";
      foreach ($subtipos as $key => $subt) {
         $valor = $subt['id'];
         $texto = $subt['nombre'];         
         echo "<option value='$valor'>$texto</option>";
      }
   }


   public function getparametrosgenerales()
   {

         $idproceso = (int)$this->input->post('id_proceso');

         if($idproceso == -1)
         {
            echo"";
         }
         else
         {

            $cantidadcampos = $this->fabricacion_model->devolver_cantidadcamposgeneral($idproceso);

           $atributos = $this->fabricacion_model->devolver_todoslosatributos($idproceso);

           echo "<li class='list-group-item'>
                    <div class='row'>
                       <div class='col-lg-12'>
                          <h4 align='center'><strong>DATOS DEL PROVEEDOR</strong></h4>
                       </div>
                    </div>
                  </li>
                  <li class='list-group-item'>
                    <div class='row'>
                      <div class='col-lg-2'>
                          <h5><strong>Empresa:</strong></h5>
                      </div>
                      <div class='col-lg-4'>
                          <input type='text' name='empresaproveedor' class='form-control' placeholder=''>
                      </div>
                      <div class='col-lg-2'>
                          <h5><strong>Responsable:</strong></h5>
                      </div>
                      <div class='col-lg-4'>
                          <input type='text' name='responsableproveedor' class='form-control' placeholder=''>
                      </div>
                    </div>
                </li>
                <li class='list-group-item'>
                    <div class='row'>
                      <div class='col-lg-2'>
                          <h5><strong>Correo electrónico:</strong></h5>
                      </div>
                      <div class='col-lg-2'>
                          <input type='text' name='correoproveedor' class='form-control' placeholder=''>
                      </div>
                      <div class='col-lg-2'>
                          <h5><strong>Teléfono:</strong></h5>
                      </div>
                      <div class='col-lg-2'>
                          <input type='text' name='telefonoproveedor' class='form-control' placeholder=''>
                      </div>
                      <div class='col-lg-2'>
                          <h5><strong>Dirección:</strong></h5>
                      </div>
                      <div class='col-lg-2'>
                          <input type='text' name='direccionproveedor' class='form-control' placeholder=''>
                      </div>
                    </div>
                 </li>";

           echo "<li class='list-group-item'>
                    <div class='row'>
                       <div class='col-lg-12'>
                          <h4 align='center'><strong>PARÁMETROS GENERALES DE PROCESO</strong></h4>
                       </div>
                    </div>
                 </li>";

           $cantidaddeli = (float)$cantidadcampos/3;
           $cantidaddelientero = intval($cantidadcampos/3);
           if($cantidaddeli != $cantidaddelientero) $cantidaddeli = $cantidaddelientero+1;

           $var = 0;
           $j = 0;
           for($i=0 ; $i<$cantidaddeli; $i++)
           {  
              echo "<li class='list-group-item'>
                       <div class='row'>";

              for($j; $j<3; $j++)
                       {
                       echo"<div class='col-lg-2'>";
                       echo "<h5><strong>"; echo $atributos[$var]['leyenda']; echo"</strong></h5>
                       </div>
                       <div class='col-lg-2'>";
                       switch ($atributos[$var]['tipo_campo']) {
                                 case 0:
                                     echo "<input type='text' name= '"; echo $atributos[$var]['atributo']; echo "' class='form-control''>";
                                     break;
                                 case 1:
                                     echo "<label class='radio-inline'>";
                                     echo "<input type='radio' name= '"; echo $atributos[$var]['atributo']; echo "'value='1'> Si";
                                     echo "</label>";
                                     echo "<label class='radio-inline'>";
                                     echo "<input type='radio' name= '"; echo $atributos[$var]['atributo']; echo "'value='0'> No";
                                     echo "</label>";
                                     break;
                                 case 2:
                                     $aprecargar = $this->fabricacion_model->devolver_atributosaprecargar($atributos[$var]['id']);
                                     $cantidadprecargados = $this->fabricacion_model->devolver_cantidadprecargados($atributos[$var]['id']);
                                     echo "<select name='"; echo $atributos[$var]['atributo']; echo"' class='form-control'>";
                                     for ($k=0; $k<$cantidadprecargados; $k++)
                                     {   
                                         echo "<option value="; echo $k; echo ">"; echo $this->fabricacion_model->devolver_precargadoporid($aprecargar[$k]['opcion']); echo"</option>";
                                     }
                                     echo "</select>";
                                     break;
                             }
                       echo "</div>";
                       $var = $var +1;
                       $cantidadcampos = $cantidadcampos - 1;
                       if($cantidadcampos == 0) $j = 3;
                       }

                       if($cantidadcampos>3){$j=0;}
                       else {$j = 3 - $cantidadcampos;}

               echo " </div>
                 </li>";

            }

         }

         

       

   }



   public function getparametrosespecificos()
   {
         $idsubtipo = (int)$this->input->post('id_subtipo');

         if($idsubtipo == 0)
         {
            echo"";
         }

         else
         {

          $cantidadcampos = $this->fabricacion_model->devolver_cantidadcampossubtipo($idsubtipo);

         $atributos = $this->fabricacion_model->devolver_atributosespecificos($idsubtipo);


         echo "<li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <h4 align='center'><strong>PARÁMETROS ESPECÍFICOS DE PROCESO</strong></h4>
                     </div>
                  </div>
               </li>";

         $cantidaddeli = (float)$cantidadcampos/3;
         $cantidaddelientero = intval($cantidadcampos/3);
         if($cantidaddeli != $cantidaddelientero) $cantidaddeli = $cantidaddelientero+1;

         $var = 0;
         $j = 0;
         for($i=0 ; $i<$cantidaddeli; $i++)
         {  
            echo "<li class='list-group-item'>
                     <div class='row'>";

            for($j; $j<3; $j++)
                     {
                     echo"<div class='col-lg-2'>";
                     echo "<h5><strong>"; echo $atributos[$var]['leyenda']; echo"</strong></h5>
                     </div>
                     <div class='col-lg-2'>";
                     switch ($atributos[$var]['tipo_campo']) {
                               case 0:
                                   echo "<input type='text' name= '"; echo $atributos[$var]['atributo']; echo "' class='form-control''>";
                                   break;
                               case 1:
                                   echo "<label class='radio-inline'>";
                                   echo "<input type='radio' name='"; echo $atributos[$var]['atributo']; echo "' value='1'> Si";
                                   echo "</label>";
                                   echo "<label class='radio-inline'>";
                                   echo "<input type='radio' name='"; echo $atributos[$var]['atributo']; echo "'value='0'> No";
                                   echo "</label>";
                                   break;
                               case 2:
                                   $aprecargar = $this->fabricacion_model->devolver_atributosaprecargar($atributos[$var]['id']);
                                   $cantidadprecargados = $this->fabricacion_model->devolver_cantidadprecargados($atributos[$var]['id']);
                                   echo "<select name='"; echo $atributos[$var]['atributo']; echo"' class='form-control'>";
                                   for ($k=0; $k<$cantidadprecargados; $k++)
                                   {
                                       echo "<option value="; echo $k; echo ">"; echo $this->fabricacion_model->devolver_precargadoporid($aprecargar[$k]['opcion']); echo"</option>";
                                   }
                                   echo "</select>";
                                   break;
                           }
                     echo "</div>";
                     $var = $var +1;
                     $cantidadcampos = $cantidadcampos - 1;
                     if($cantidadcampos == 0) $j = 3;
                     }

                     if($cantidadcampos>3){$j=0;}
                     else {$j = 3 - $cantidadcampos;}

             echo " </div>
               </li>";

          }

    }

         }

         

}

?>