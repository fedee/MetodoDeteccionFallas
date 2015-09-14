<?php 

    if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class ModuloEstadistico extends CI_Controller {
     
        function __construct()
        {
            parent::__construct();
            $this->load->model('usuarios_model');
            $this->load->model('casos_model');
            $this->load->model('piezas_model');
            $this->load->model('material_model');
            $this->load->model('fabricacion_model');
        }
     
        function todoparagenerargraficos($idespecialista){

        $this->load->helper('url');

        $datosbarchart = $this->devolver_datosbarchart();
        $datosareachart = $this->devolver_datosareachart();
        $datosdonutchart = $this->devolver_datosdonutchart();
        $datoslinechart = $this->devolver_datoslinechart();

          $datosparagraficos = array(
             'datosbarchart' => $datosbarchart,
             'datosareachart' => $datosareachart,
             'datosdonutchart' => $datosdonutchart,
             'datoslinechart' => $datoslinechart,
            );

        $this->load->view('estadisticasusuarioesp.html',$datosparagraficos);

        } 


        public function devolver_datosbarchart()
          {

            return "[{
                      cambios: 'Ninguna',
                      casos: 13
                    }, 
                    {
                        cambios: 'Temperatura',
                        casos: 7
                    }, 
                    {
                        cambios: 'Fuerzas',
                        casos: 15
                    }, 
                    {
                        cambios: 'Velocidades',
                        casos: 3
                    }, 
                    {
                        cambios: 'Sujeciones',
                        casos: 8
                    }, 
                    {
                        cambios: 'Lubricación',
                        casos: 9
                    },
                    {
                        cambios: 'Ambiente',
                        casos: 12
                    }]";
          }

        public function devolver_datosdonutchart()
          {

            return "[{
                    label: 'Mantenimiento',
                    value: 13
                    }, {
                        label: 'Servicio de la pieza',
                        value: 50
                    }, {
                        label: 'Periodo de prueba',
                        value: 10
                    },{
                        label: 'Probando prototipo',
                        value: 23
                    }]";
          }

        public function devolver_datosareachart()
          {

            return "[{
                    mes: '2015-1',
                    fragil: 2,
                    ductil: 1,
                    mixta: 1
                }, {
                    mes: '2015-2',
                    fragil: 1,
                    ductil: null,
                    mixta: 1
                }, {
                    mes: '2015-3',
                    fragil: 1,
                    ductil: 2,
                    mixta: 3
                }, {
                    mes: '2015-4',
                    fragil: 2,
                    ductil: 5,
                    mixta: 3
                }, {
                    mes: '2015-5',
                    fragil: null,
                    ductil: 1,
                    mixta: 3
                }, {
                    mes: '2015-6',
                    fragil: 5,
                    ductil: 1,
                    mixta: 4
                }]";

          }


          public function devolver_datoslinechart()
          {

            return "[{
                        mes: '2015-1',
                        casos: 2
                    }, {
                        mes: '2015-2',
                        casos: 4
                    }, {
                        mes: '2015-3',
                        casos: 3
                    }, {
                        mes: '2015-4',
                        casos: 5
                    }, {
                        mes: '2015-5',
                        casos: 4
                    }, {
                        mes: '2015-6',
                        casos: 8
                    },]";
          }

    }
 

?>