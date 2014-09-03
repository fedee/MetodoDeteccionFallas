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
      echo "<option value=<?php echo '0'; ?>Elegir</option>";
      foreach ($subtipos as $key => $subt) {
         $valor = $subt['id'];
         $texto = $subt['nombre'];         
         echo "<option value='$valor'>$texto</option>";
      }
   }

   public function getparametrosgenerales()
   {
      $idproceso = (int)$this->input->post('id_proceso');
      if($idproceso == 0) $nombreproceso = "";
      else {
      $nombreproceso = $this->fabricacion_model->devolver_nombreprocesoporid($idproceso);}

      $cantidadcampos = $this->fabricacion_model->devolver_cantidadcampos($idproceso);

      $atributos = $this->fabricacion_model->devolver_todoslosatributos($idproceso);

      if($nombreproceso == 'Fundición') //vamos con parametros generales.
      {
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
                        if($atributos[$var]['tipo_campo'] == 0)
                        {
                           echo "<input type='text' name="; echo $atributos[$var]['atributo']; echo " class='form-control''>";
                        }
                        if($atributos[$var]['tipo_campo'] == 1)
                        {
                           echo "<label class='radio-inline'>";
                           echo "<input type='radio' name="; echo $atributos[$var]['atributo']; echo " value=<?php echo '1'; ?> Si";
                           echo "</label>";
                           echo "<label class='radio-inline'>";
                           echo "<input type='radio' name="; echo $atributos[$var]['atributo']; echo "value=<?php echo '0'; ?> No";
                           echo "</label>";
                        }
                     echo "</div>";
                     $var = $var +1;
                     $cantidadcampos = $cantidadcampos - 1;
                     }

                     if($cantidadcampos>3){$j=0;}
                     else {$j = 3 - $cantidadcampos;}

             echo " </div>
               </li>";

         }

      }

      if($nombreproceso == 'Fundición2') //vamos con parametros generales.
      {
         echo "<li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <h4 align='center'><strong>PARÁMETROS GENERALES DE PROCESO</strong></h4>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Temp. de colada:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tempcolada' class='form-control' placeholder='Temperatura.''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>T. de desmoldeo:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tdesmoldeo' class='form-control' placeholder='Tiempo.''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Mat. del modelo:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='matmoldeo' class='form-control' placeholder='Material.''>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Temperatura precalentamiento molde:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tempprecal' class='form-control' placeholder='Temperatura.''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Temperatura de desmoldeo:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tempdesmoldeo' class='form-control' placeholder='Temperatura''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Presión de fundición:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='presfundic' class='form-control' placeholder='Pf''>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Temperatura de vaciado de metal líquido:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tempvac' class='form-control' placeholder='Temperatura.''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Temp. inicial del molde:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tempinic' class='form-control' placeholder='Ti''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Temp. final del molde:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tempfinal' class='form-control' placeholder='Tf''>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Tiempo total de vaciado:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tvaciado' class='form-control' placeholder='Tv''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Tiempo de enfriamiento:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tenf' class='form-control' placeholder='Te''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>¿Posee el molde zonas Refrigeradas o Calefaccionadas?</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <label class='radio-inline'>
                           <input type='radio' name='posee' id='posee' value=<?php echo '1'; ?> Si
                        </label>
                        <label class='radio-inline'>
                           <input type='radio' name='posee' id='posee' value=<?php echo '0'; ?> No
                        </label>
                     </div>
                  </div>
               </li>";
      }
      
   }



   public function getparametrosespecificos()
   {
      $idsubtipo = (int)$this->input->post('id_subtipo');
      if($idsubtipo == 0) $nombresubtipo = "";
      else {
      $nombresubtipo = $this->fabricacion_model->devolver_nombresubtipoporid($idsubtipo);}

      if($nombresubtipo == 'A presión en matriz') //vamos con parametros específicos.
      {
         echo "<li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <h4 align='center'><strong>PARÁMETROS ESPECÍFICOS: FUNDICIÓN A PRESIÓN EN MATRIZ</strong></h4>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Tipo desmoldante:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tipodesmold' class='form-control' placeholder='Tipo.''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Sistema de inyección:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='sistiny' class='form-control' placeholder='Sistema.''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Mat. de Noyos:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='matnoyos' class='form-control' placeholder='Material''>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Tiempo de ciclo por pieza:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tciclo' class='form-control' placeholder='Tiempo.''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Tipo de refrigeración del molde:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tiporef' class='form-control' placeholder='Tipo''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Temperatura del molde al inyectar:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tiny' class='form-control' placeholder='Temperatura''>
                     </div>
                  </div>
               </li>";
      }

      if($nombresubtipo == 'Centrífuga') //vamos con parametros específicos.
      {
         echo "<li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <h4 align='center'><strong>PARÁMETROS ESPECÍFICOS: FUNDICIÓN CENTRÍFUGA</strong></h4>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Velocidad de rotación:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='velrot' class='form-control' placeholder='R.P.M.''>
                     </div>
                  </div>
               </li>";
      }

      if($nombresubtipo == 'Molde arena') //vamos con parametros específicos.
      {
         echo "<li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <h4 align='center'><strong>PARÁMETROS ESPECÍFICOS: FUNDICIÓN A MOLDE DE ARENA</strong></h4>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Porcentaje de humedad del molde:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='porchum' class='form-control' placeholder='%''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Permeabilidad:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='perm' class='form-control' placeholder='Permeabilidad''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Dureza en seco del molde:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='dursec' class='form-control' placeholder='Dureza''>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Material de Noyos:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='maynoy' class='form-control' placeholder='Material''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Tipo de arena empleada:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tipoar' class='form-control' placeholder='Tipo''>
                     </div>
                     <div class='col-lg-2'>
                        <h5><strong>Colapsabilidad:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='colaps' class='form-control' placeholder='Colapsabilidad''>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Resistencia:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='resist' class='form-control' placeholder='Resistencia''>
                     </div>
                  </div>
               </li>";
      }

      if($nombresubtipo == 'Molde permanente') //vamos con parametros específicos.
      {
         echo "<li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <h4 align='center'><strong>PARÁMETROS ESPECÍFICOS: FUNDICIÓN POR MOLDE PERMANENTE</strong></h4>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-2'>
                        <h5><strong>Tipo de desmoldante:</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <input type='text' name='tipodes' class='form-control' placeholder='Tipo''>
                     </div>
                  </div>
               </li>";
      }

      if($nombresubtipo == 'Moldeo evaporativo') //vamos con parametros específicos.
      {
         echo "<li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <h4 align='center'><strong>PARÁMETROS ESPECÍFICOS: FUNDICIÓN POR MOLDEO EVAPORATIVO</strong></h4>
                     </div>
                  </div>
               </li>
               <li class='list-group-item'>
                  <div class='row'>
                     <div class='col-lg-6'>
                        <h5><strong>¿Hubo eliminación de modelo previo a la colada?</strong></h5>
                     </div>
                     <div class='col-lg-2'>
                        <label class='radio-inline'>
                           <input type='radio' name='elim' id='posee' value=<?php echo '1'; ?> Si
                        </label>
                        <label class='radio-inline'>
                           <input type='radio' name='elim' id='posee' value=<?php echo '0'; ?> No
                        </label>
                     </div>
                  </div>
               </li>";
      }
      
   }


}

?>