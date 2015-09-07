<?php 

    if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class GraficosParetto extends CI_Controller {
     
        function __construct()
        {
            parent::__construct();
            $this->load->library("generargraficos");
            require_once 'C:\xampp\htdocs\cafap\application\libraries\pChart\pData.class'; //manipula el array de los datos
            require_once 'C:\xampp\htdocs\cafap\application\libraries\pChart\pCache.class'; //maneja la cache
            require_once 'C:\xampp\htdocs\cafap\application\libraries\pChart\pChart.class'; //maneja los graficos
            $this->load->model('usuarios_model');
            $this->load->model('casos_model');
            $this->load->model('piezas_model');
            $this->load->model('material_model');
            $this->load->model('fabricacion_model');
        }
     
        function construir_paretto($idcaso){

        $this->load->helper('url');

        $datosparagrafico = $this->devolver_datosparaparetto($idcaso);

          $datosparetto = array(
             'titulo' => $this->casos_model->devolver_tituloporid($idcaso),
             'id' => $idcaso,
             'datosgrafico' => $datosparagrafico,
            );


        //Generacion de grafico que se guarda, independiente del que se muestra por frontend

         $datosimggrafico = $this->devolver_datosparaguardarimg($idcaso);

         $DataSet = new pData;  

         for($i=0 ; $i<$datosimggrafico['cantidadcasos']; $i++)
         {
            $num=$i+1;
            $DataSet->AddPoint(array($datosimggrafico['valoraciones'][$i]),"Serie".$num);
         }

         $DataSet->AddAllSeries();  
         $DataSet->SetAbsciseLabelSerie();  

         for($i=0 ; $i<$datosimggrafico['cantidadcasos']; $i++)
         {
            $num=$i+1;
            $DataSet->SetSerieName("".$datosimggrafico['titulos'][$i]."","Serie".$num);
         }
           
         $font_folder = 'C:\xampp\htdocs\cafap\application\libraries\Fonts';
          
         // Initialise the graph  
         $Test = new pChart(1000,530);  
         $Test->setFontProperties($font_folder."/tahoma.ttf",10); 
         $Test->setGraphArea(50,30,980,500);  
         $Test->drawFilledRoundedRectangle(7,7,1093,623,5,240,240,240);  
         $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);  
         $Test->drawGraphArea(255,255,255,TRUE);  

         //esto de acá abajo lo hago como "fix" para la lib esta porque aguanta hasta 8 conjuntos de datos, tengo que setear colores
         //nuevos para que aguante más. Con esto de abajo aguantaría 20 y algo, de ser necesario se cargan más con un for tal vez.
         $Test->setColorPalette(8,155,100,0);  
         $Test->setColorPalette(100,0,255,0);  
         $Test->setColorPalette(10,0,0,255);
         $Test->setColorPalette(9,255,0,0);  
         $Test->setColorPalette(10,0,255,0);  
         $Test->setColorPalette(11,0,0,255);
         $Test->setColorPalette(12,255,0,0);  
         $Test->setColorPalette(13,0,255,0);  
         $Test->setColorPalette(14,0,0,255);
         $Test->setColorPalette(15,255,0,0);  
         $Test->setColorPalette(16,0,255,0);  
         $Test->setColorPalette(17,0,0,255);
         $Test->setColorPalette(18,255,0,0);  
         $Test->setColorPalette(19,0,255,0);  
         $Test->setColorPalette(20,0,0,255);
         $Test->setColorPalette(21,255,0,0);  
         $Test->setColorPalette(22,0,255,0);  
         $Test->setColorPalette(23,0,0,255);

         $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);     
         $Test->drawGrid(4,TRUE,230,230,230,50);  
          
         // Draw the 0 line  
         $Test->setFontProperties($font_folder."/tahoma.ttf",12);   
         $Test->drawTreshold(0,143,55,72,TRUE,TRUE);  
          
         // Draw the bar graph  
         $Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);  
          
         // Finish the graph  
         $Test->setFontProperties($font_folder."/tahoma.ttf",12);  
         $Test->drawLegend(725,37,$DataSet->GetDataDescription(),255,255,255);  
         $Test->setFontProperties($font_folder."/tahoma.ttf",13);   
         $Test->drawTitle(50,22,"Diagrama de Pareto",50,50,50,985);  
         $Test->Render('C:\xampp\htdocs\cafap\uploads\paretto-'.$idcaso.'.png');

         $tituloimg = 'paretto-'.$idcaso.'.png';

         //guardo url y demas de la imagen para tenerla en la bd para el reporte en pdf.
         $this->piezas_model->guardarimgparetto($tituloimg,$idcaso);

         //Fin del gráfico que se guarda



         $this->casos_model->actualizarpaso($idcaso,'11');
         $this->load->view('paretto.html',$datosparetto);

        } 


        public function devolver_datosparaparetto($idcaso)
          {
            $cantidadcasos = $this->piezas_model->devolver_cantidadhipotesisparetto($idcaso);
            $tituloshipotesis = $this->casos_model->devolver_tituloparaparetto($idcaso);
            $valoracioneshipotesis = $this->casos_model->devolver_valoracionesparaparetto($idcaso);

            $iniciojson="[";
            $cuerpojson="";
            $finjson="";

            for($i=0 ; $i<$cantidadcasos-1; $i++)
            {
              $cuerpojson= $cuerpojson."{ hipotesis: '".$tituloshipotesis[$i]."', valoracion: ".$valoracioneshipotesis[$i]." },";
            } 

            $finjson="{ hipotesis: '".$tituloshipotesis[$cantidadcasos-1]."', valoracion: ".$valoracioneshipotesis[$cantidadcasos-1]." }  ]";

            $jsonentero = $iniciojson.$cuerpojson.$finjson;

            //estructura del JSON: [{l:'v', l:v},{l:'v', l:v},{l:'v', l:v}]

            return $jsonentero;

              /*"[{
                      device: 'probandoo',
                      geekbench: 136
                   }, 
                   {
                       device: 'Creo que es por esto',
                       geekbench: 137
                   }, 
                   {
                            device: 'iPhone 3GS',
                            geekbench: 275
                        }, {
                            device: 'iPhone 4',
                            geekbench: 380
                        }, {
                            device: 'iPhone 4S',
                            geekbench: 655
                        }, {
                            device: 'iPhone 5',
                            geekbench: 1571
                        }]"*/

          }


          public function devolver_datosparaguardarimg($idcaso)
          {
            $cantidadcasos = $this->piezas_model->devolver_cantidadhipotesisparetto($idcaso);
            $tituloshipotesis = $this->casos_model->devolver_tituloparaparetto($idcaso);
            $valoracioneshipotesis = $this->casos_model->devolver_valoracionesparaparetto($idcaso);

            $datos['cantidadcasos'] = $cantidadcasos;
            $datos['titulos'] =  $tituloshipotesis;
            $datos['valoraciones'] =  $valoracioneshipotesis;

            return $datos;

          }

    }
 

?>