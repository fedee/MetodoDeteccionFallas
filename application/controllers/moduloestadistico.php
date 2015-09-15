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

        $datosbarchart = $this->devolver_datosbarchart($idespecialista);
        $datosareachart = $this->devolver_datosareachart($idespecialista);
        $datosdonutchart = $this->devolver_datosdonutchart($idespecialista);
        $datoslinechart = $this->devolver_datoslinechart($idespecialista);

          $datosparagraficos = array(
             'datosbarchart' => $datosbarchart,
             'datosareachart' => $datosareachart,
             'datosdonutchart' => $datosdonutchart,
             'datoslinechart' => $datoslinechart,
            );

        $this->load->view('estadisticasusuarioesp.html',$datosparagraficos);

        } 


        public function devolver_datosbarchart($idespecialista)
          {
            $idcasosdelespecialista = $this->casos_model->devolver_idcasosespsinfecha($idespecialista,3);
            
            $cantidadninguna = 0;
            $cantidadtemp = 0;
            $cantidadfuerzas = 0;
            $cantidadveloc = 0;
            $cantidadsujec = 0;
            $cantidadlub = 0;
            $cantidadambiente = 0;

            $idpieza = 0;

            for($i = 0; $i <count($idcasosdelespecialista); $i++)
            {
                $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcasosdelespecialista[$i]);

                $modificaciones = $this->casos_model->devolver_modificaciones($idpieza);

                if($modificaciones == 0) $cantidadninguna ++;
                if($modificaciones == 1) $cantidadtemp ++;
                if($modificaciones == 2) $cantidadfuerzas ++;
                if($modificaciones == 3) $cantidadveloc ++;
                if($modificaciones == 4) $cantidadsujec ++;
                if($modificaciones == 5) $cantidadlub ++;
                if($modificaciones == 6) $cantidadambiente ++;
               
            }

            if($cantidadninguna == 0) $cantidadninguna = 0;
            if($cantidadtemp == 0) $cantidadtemp = 0;
            if($cantidadfuerzas == 0) $cantidadfuerzas = 0;
            if($cantidadveloc == 0) $cantidadveloc = 0;
            if($cantidadsujec == 0) $cantidadsujec = 0;
            if($cantidadlub == 0) $cantidadlub = 0;
            if($cantidadambiente == 0) $cantidadambiente = 0;

            $jsonentero = "[{ cambios: 'Ninguna', casos: ".$cantidadninguna." }, { cambios: 'Temperatura', casos: ".$cantidadtemp."
                            }, { cambios: 'Fuerzas', casos: ".$cantidadfuerzas." }, { cambios: 'Velocidades', casos: ".$cantidadveloc." }, 
                            { cambios: 'Sujeciones', casos: ".$cantidadsujec." }, { cambios: 'Lubricación', casos: ".$cantidadlub." },
                            { cambios: 'Ambiente', casos: ".$cantidadambiente." }]";

            return $jsonentero;
            
          }


        public function devolver_datosdonutchart($idespecialista)
          {

            $idcasosdelespecialista = $this->casos_model->devolver_idcasosespsinfecha($idespecialista,1);
            
            $cantidadmantenimiento = 0;
            $cantidadservicio = 0;
            $cantidadperprueba = 0;
            $cantidadprototipo = 0;
            $idpieza = 0;

            for($i = 0; $i <count($idcasosdelespecialista); $i++)
            {
                $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcasosdelespecialista[$i]);

                $faseciclovida = $this->casos_model->devolver_faseciclovida($idpieza);

                if($faseciclovida == 0) $cantidadmantenimiento ++;
                if($faseciclovida == 1) $cantidadservicio ++;
                if($faseciclovida == 2) $cantidadperprueba ++;
                if($faseciclovida == 3) $cantidadprototipo ++;
               
            }

            if($cantidadmantenimiento == 0) $cantidadmantenimiento = 0;
            if($cantidadservicio == 0) $cantidadservicio = 0;
            if($cantidadperprueba == 0) $cantidadperprueba = 0;
            if($cantidadprototipo == 0) $cantidadprototipo = 0;

            $jsonentero = "[{ label: 'Mantenimiento', value: ".$cantidadmantenimiento."}, { label: 'Servicio de la pieza',
                              value: ".$cantidadservicio." }, { label: 'Periodo de prueba', value: ".$cantidadperprueba." },{
                              label: 'Probando prototipo', value: ".$cantidadprototipo." }]";

            return $jsonentero;
          }

        public function devolver_datosareachart($idespecialista)
          {

            // [{ mes: '2015-1', fragil: 2, ductil: 1, mixta: 1 }, { mes: '2015-2', fragil: 1, ductil: null, mixta: 1 }, ]

            $fechadehoy = date('d-m-Y');
            $centrodeljson = "";
            $mesesarestar = -5;

            for($j=0; $j<6; $j++)
            {
                $haceseismeses = strtotime ( "".$mesesarestar." month", strtotime ($fechadehoy) ) ;
                $haceseismeses = date ( 'Y-m-j' , $haceseismeses );
                $añohaceseismeses =  date ( 'Y' , strtotime($haceseismeses) );
                $meshaceseismeses = date ( 'm' , strtotime($haceseismeses) );

                $idcasosdelespecialista = $this->casos_model->devolver_idcasosesp($idespecialista,$añohaceseismeses,$meshaceseismeses,8);

                $cantidadfragil = 0;
                $cantidadductil = 0;
                $cantidadmixta = 0;
                $tipofractura = 0;

                for($i = 0; $i <count($idcasosdelespecialista); $i++)
                {
                    $tipofractura = $this->casos_model->devolver_tipofracturaporidcaso($idcasosdelespecialista[$i]);
                    if($tipofractura == 0) $cantidadfragil = $cantidadfragil + 1;
                    if($tipofractura == 1) $cantidadductil = $cantidadductil + 1;
                    if($tipofractura == 2) $cantidadmixta = $cantidadmixta + 1;

                }

                if($cantidadfragil == 0) $cantidadfragil = "null";
                if($cantidadductil == 0) $cantidadductil = "null";
                if($cantidadmixta == 0) $cantidadmixta = "null";

                $centrodeljson = $centrodeljson."{ mes: '".$añohaceseismeses."-".$meshaceseismeses."', fragil: ".$cantidadfragil.", 
                                 ductil: ".$cantidadductil.", mixta: ".$cantidadmixta."},";

                $mesesarestar = $mesesarestar + 1;
            }

            $jsonentero = "[ ".$centrodeljson." ]";


            return $jsonentero;

          }


          public function devolver_datoslinechart($idespecialista)
          {

            $fechadehoy = date('d-m-Y');
            $centrodeljson = "";
            $mesesarestar = -5;

            for($j=0; $j<6; $j++)
            {
                $haceseismeses = strtotime ( "".$mesesarestar." month", strtotime ($fechadehoy) ) ;
                $haceseismeses = date ( 'Y-m-j' , $haceseismeses );
                $añohaceseismeses =  date ( 'Y' , strtotime($haceseismeses) );
                $meshaceseismeses = date ( 'm' , strtotime($haceseismeses) );

                $idcasosdelespecialista = $this->casos_model->devolver_idcasosesp($idespecialista,$añohaceseismeses,$meshaceseismeses,14);
                
                $cantidadfinalizados = 0;

                for($i = 0; $i <count($idcasosdelespecialista); $i++)
                {
                    $esfinalizado = $this->casos_model->devolver_siesfinalizado($idcasosdelespecialista[$i]);
                    if($esfinalizado == 2) $cantidadfinalizados = $cantidadfinalizados + 1;

                }

                if($cantidadfinalizados == 0) $cantidadfinalizados = "null";

                $centrodeljson = $centrodeljson."{ mes: '".$añohaceseismeses."-".$meshaceseismeses."', casos: ".$cantidadfinalizados."},";

                $mesesarestar = $mesesarestar + 1;
            }

            $jsonentero = "[ ".$centrodeljson." ]";

            return $jsonentero;
          }

    }
 

?>