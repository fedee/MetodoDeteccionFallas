<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ReportePDF extends CI_Controller {
 
    function __construct()
    {
        parent::__construct();
        $this->load->library("pdf");
        $this->load->model('casos_model');
        $this->load->model('piezas_model');
        $this->load->model('material_model');
        $this->load->model('fabricacion_model');
        $this->load->helper('url');
    }
 
    public function crearpdf($idcaso,$esusuariocomun) {

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Federico Sarmiento');
    $pdf->SetTitle('Reporte de caso');
    $pdf->SetSubject('Reporte CAFAP');
    $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
 
    //Nombre Usuario
    $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
    $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
 
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
 
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
 
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
 
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
 
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
 
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }   
 
    // ---------------------------------------------------------    
 
    // set default font subsetting mode
    $pdf->setFontSubsetting(true);   
 
    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 10, '', true);   
 
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage(); 
 
    //Título y descripción del caso
    $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);
    //<br/>
    $tituloydescripcionhtml = '<h1 align="center">Caso: '.$tituloydesccaso['titulo'].'</h1><br/>
             <h3>Descripción del caso</h3>
             <span style="text-align:justify;">'.$tituloydesccaso['descripcion'].'</span>';
            
    $pdf->writeHTML($tituloydescripcionhtml, true, false, true, false, ''); 

    //Todo lo necesario para el bloque de introducción y el bloque de componente
    $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

    //Bloque de introducción
    if($todosobrelapieza['fallo_multiplesoc']==0) $todosobrelapieza['fallo_multiplesoc'] = 'No';
    if($todosobrelapieza['fallo_multiplesoc']==1) $todosobrelapieza['fallo_multiplesoc'] = 'Si';
    if($todosobrelapieza['ttrabajo_tiempo']==0) $todosobrelapieza['ttrabajo_tiempo'] = 'años';
    if($todosobrelapieza['ttrabajo_tiempo']==1) $todosobrelapieza['ttrabajo_tiempo'] = 'meses';
    if($todosobrelapieza['ttrabajo_tiempo']==2) $todosobrelapieza['ttrabajo_tiempo'] = 'días';
    if($todosobrelapieza['ttrabajo_tiempo']==3) $todosobrelapieza['ttrabajo_tiempo'] = 'horas';
    if($todosobrelapieza['vutil_tiempo']==0) $todosobrelapieza['vutil_tiempo'] = 'años';
    if($todosobrelapieza['vutil_tiempo']==1) $todosobrelapieza['vutil_tiempo'] = 'meses';
    if($todosobrelapieza['vutil_tiempo']==2) $todosobrelapieza['vutil_tiempo'] = 'dias';
    if($todosobrelapieza['vutil_tiempo']==3) $todosobrelapieza['vutil_tiempo'] = 'horas';
    if($todosobrelapieza['fase_ciclovida']==0) $todosobrelapieza['fase_ciclovida'] = 'Mantenimiento';
    if($todosobrelapieza['fase_ciclovida']==1) $todosobrelapieza['fase_ciclovida'] = 'Servicio';
    if($todosobrelapieza['fase_ciclovida']==2) $todosobrelapieza['fase_ciclovida'] = 'Periodo de prueba';
    if($todosobrelapieza['fase_ciclovida']==3) $todosobrelapieza['fase_ciclovida'] = 'Prototipo';


    $introhtml = '<br/><h3>BLOQUE 1: Introducción al caso</h3>
            <ol>                
                <li><i>¿El mismo modelo ha fallado en múltipes ocasiones? </i><b>'.$todosobrelapieza['fallo_multiplesoc'].'.</b></li>
                <li><i>Tiempo de trabajo que estuvo sometida la pieza antes de la rotura:</i>
                       <b>'.$todosobrelapieza['ttrabajocant'].' '.$todosobrelapieza['ttrabajo_tiempo'].'.</b></li>
                <li><i>Vida útil de la pieza (según fabricante), o vida esperada:</i>
                <b>'.$todosobrelapieza['vutil_cantidad'].' '.$todosobrelapieza['vutil_tiempo'].'.</li>
                <li><i>Faso del ciclo de vida en donde se produjo la falla: </i><b>'.$todosobrelapieza['fase_ciclovida'].'.</b></li>   
            </ol>';

    $pdf->writeHTML($introhtml, true, false, true, false, '');

    //Bloque de descripción del componente
    if($todosobrelapieza['montadabien']==0) $todosobrelapieza['montadabien'] = 'No';
    if($todosobrelapieza['montadabien']==1) $todosobrelapieza['montadabien'] = 'Si';
    if($todosobrelapieza['tipocargas']==0) $todosobrelapieza['tipocargas'] = 'Fuerza';
    if($todosobrelapieza['tipocargas']==1) $todosobrelapieza['tipocargas'] = 'Presión';
    if($todosobrelapieza['tipocargas']==2) $todosobrelapieza['tipocargas'] = 'Gravedad';
    if($todosobrelapieza['tipocargas']==3) $todosobrelapieza['tipocargas'] = 'Torsión';
    if($todosobrelapieza['tipocargas']==4) $todosobrelapieza['tipocargas'] = 'Fricción';
    if($todosobrelapieza['tipocargas']==5) $todosobrelapieza['tipocargas'] = 'Salto térmico';
    if($todosobrelapieza['umedidacargas']==0) $todosobrelapieza['umedidacargas'] = 'Newton';
    if($todosobrelapieza['umedidacargas']==1) $todosobrelapieza['umedidacargas'] = 'Kilogramos';
    if($todosobrelapieza['umedidacargas']==2) $todosobrelapieza['umedidacargas'] = 'Libras';
    if($todosobrelapieza['tiposujeciones']==0) $todosobrelapieza['tiposujeciones'] = 'Fuerza';
    if($todosobrelapieza['tiposujeciones']==1) $todosobrelapieza['tiposujeciones'] = 'Presión';
    if($todosobrelapieza['tiposujeciones']==2) $todosobrelapieza['tiposujeciones'] = 'Gravedad';
    if($todosobrelapieza['tiposujeciones']==3) $todosobrelapieza['tiposujeciones'] = 'Torsión';
    if($todosobrelapieza['tiposujeciones']==4) $todosobrelapieza['tiposujeciones'] = 'Fricción';
    if($todosobrelapieza['tiposujeciones']==5) $todosobrelapieza['tiposujeciones'] = 'Salto térmico';
    if($todosobrelapieza['condtermicas']==0) $todosobrelapieza['condtermicas'] = 'Temperatura ambiental';
    if($todosobrelapieza['condtermicas']==1) $todosobrelapieza['condtermicas'] = 'Temperatura de contacto';
    if($todosobrelapieza['condtermicas']==2) $todosobrelapieza['condtermicas'] = 'Salto térmico';
    if($todosobrelapieza['utempcondtermicas']==0) $todosobrelapieza['utempcondtermicas'] = 'Celcius';
    if($todosobrelapieza['utempcondtermicas']==1) $todosobrelapieza['utempcondtermicas'] = 'Fahrenheit';
    if($todosobrelapieza['utempcondtermicas']==2) $todosobrelapieza['utempcondtermicas'] = 'Kelvin';
    if($todosobrelapieza['utempcondtermicas']==3) $todosobrelapieza['utempcondtermicas'] = 'Rankine';
    if($todosobrelapieza['tipopresiones']==0) $todosobrelapieza['tipopresiones'] = 'Unidireccional';
    if($todosobrelapieza['tipopresiones']==1) $todosobrelapieza['tipopresiones'] = 'Bidireccional';
    if($todosobrelapieza['tipopresiones']==2) $todosobrelapieza['tipopresiones'] = 'Tridireccional';
    if($todosobrelapieza['distribpresiones']==0) $todosobrelapieza['distribpresiones'] = 'Puntual';
    if($todosobrelapieza['distribpresiones']==1) $todosobrelapieza['distribpresiones'] = 'Uniforme';
    if($todosobrelapieza['distribpresiones']==2) $todosobrelapieza['distribpresiones'] = 'Parcial';
    if($todosobrelapieza['distribpresiones']==3) $todosobrelapieza['distribpresiones'] = 'Hidrostática';
    if($todosobrelapieza['umedidapres']==0) $todosobrelapieza['umedidapres'] = 'Kg/cm2';
    if($todosobrelapieza['umedidapres']==1) $todosobrelapieza['umedidapres'] = 'BAR';
    if($todosobrelapieza['umedidapres']==2) $todosobrelapieza['umedidapres'] = 'PSI';
    if($todosobrelapieza['umedidapres']==3) $todosobrelapieza['umedidapres'] = 'Lb/pulgada2';
    if($todosobrelapieza['veloctrab']==0) $todosobrelapieza['veloctrab'] = 'Lineal';
    if($todosobrelapieza['veloctrab']==1) $todosobrelapieza['veloctrab'] = 'Rotativa';
    if($todosobrelapieza['veloctrab']==2) $todosobrelapieza['veloctrab'] = 'Otras';
    if($todosobrelapieza['trayectoria']==0) $todosobrelapieza['trayectoria'] = 'Continua';
    if($todosobrelapieza['trayectoria']==1) $todosobrelapieza['trayectoria'] = 'Por tramos';
    if($todosobrelapieza['trayectoria']==2) $todosobrelapieza['trayectoria'] = 'Alternada';
    if($todosobrelapieza['unidadveloc']==0) $todosobrelapieza['unidadveloc'] = 'M/seg';
    if($todosobrelapieza['unidadveloc']==1) $todosobrelapieza['unidadveloc'] = 'Km/hr';
    if($todosobrelapieza['unidadveloc']==2) $todosobrelapieza['unidadveloc'] = 'Millas/hr';
    if($todosobrelapieza['unidadveloc']==3) $todosobrelapieza['unidadveloc'] = 'Pie/seg';
    if($todosobrelapieza['elemsusp']==0) $todosobrelapieza['elemsusp'] = 'Ninguno';
    if($todosobrelapieza['elemsusp']==1) $todosobrelapieza['elemsusp'] = 'Plomo';
    if($todosobrelapieza['elemsusp']==2) $todosobrelapieza['elemsusp'] = 'Azufre';
    if($todosobrelapieza['elemsusp']==3) $todosobrelapieza['elemsusp'] = 'Carbono';
    if($todosobrelapieza['modifcond']==0) $todosobrelapieza['modifcond'] = 'No';
    if($todosobrelapieza['modifcond']==1) $todosobrelapieza['modifcond'] = 'Si';
    if($todosobrelapieza['modificaciones']==0) $todosobrelapieza['modificaciones'] = 'Ninguna';
    if($todosobrelapieza['modificaciones']==1) $todosobrelapieza['modificaciones'] = 'Cambio de temperatura de trabajo';
    if($todosobrelapieza['modificaciones']==2) $todosobrelapieza['modificaciones'] = 'Cambio de fuerzas';
    if($todosobrelapieza['modificaciones']==3) $todosobrelapieza['modificaciones'] = 'Cambio de velocidades';
    if($todosobrelapieza['modificaciones']==4) $todosobrelapieza['modificaciones'] = 'Cambio de sujeciones';
    if($todosobrelapieza['modificaciones']==5) $todosobrelapieza['modificaciones'] = 'Cambio de lubricación';
    if($todosobrelapieza['modificaciones']==6) $todosobrelapieza['modificaciones'] = 'Cambio de ambiente';

    $nombrematerial = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['material'],'material');
    $nombresubmat = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['submaterial'],'submaterial');
    $nombrematespec = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['especifico'],'material_especifico');

    $propiedadesmatesp = $this->material_model->devolver_propmatesp($todosobrelapieza['especifico']);
    foreach ($propiedadesmatesp as $key => $prop) {
         $dens = $prop['densidad'];
         $modelas = $prop['modulo_elastico'];
         $eporc = $prop['elongacion_porc'];
         $tenac = $prop['tenacidad'];
         $cpoisson = $prop['coef_poisson']; 
      }


    $desccomponentehtml = '<br/><h3>BLOQUE 2: Descripción del componente</h3>
            <ol>                
                <li><i>Nombre genérico de la pieza o el conjunto que presenta la falla: </i><b>'.$todosobrelapieza['nombregen'].'.</b></li>
                <li><i>Nombre o código interno de la pieza o el conjunto: </i><b>'.$todosobrelapieza['codinterno'].'.</b></li>
                <li><i>Cantidad de piezas que presentan la falla: </i><b>'.$todosobrelapieza['cantidadfalladas'].'.</b></li>
                <li><i>Uso de la pieza (uso específico del componente, o forma de uso habitual): </i><b>'.$todosobrelapieza['usopieza'].'.</b></li>
                <li><i>¿La pieza fue montada y utilizada siguiendo las especificaciones técnicas recomendadas por el fabricante 
                       o normas relativas al componente? </i><b>'.$todosobrelapieza['montadabien'].'.</b></li>   
                <li><i>Condiciones de trabajo de la pieza: </i><b>'.$todosobrelapieza['descdetallada'].'.</b></li>
                <li><i>Tipo de carga: </i><b>'.$todosobrelapieza['tipocargas'].', con un valor de '.$todosobrelapieza['cantcargas'].'
                     '.$todosobrelapieza['umedidacargas'].'.</b></li>
                <li><i>Tipo de sujeciones: </i><b>'.$todosobrelapieza['tiposujeciones'].'.</b></li>
                <li><i>Condición térmica: </i><b>'.$todosobrelapieza['condtermicas'].', con un valor de '.$todosobrelapieza['cantidadtermica'].'
                     '.$todosobrelapieza['utempcondtermicas'].'.</b></li>
                <li><i>Presión: </i><b>'.$todosobrelapieza['tipopresiones'].', distribución '.$todosobrelapieza['distribpresiones'].'
                     y un valor de '.$todosobrelapieza['valpresion'].' '.$todosobrelapieza['umedidapres'].'.</b></li>
                <li><i>Velocidad de trabajo: </i><b>'.$todosobrelapieza['veloctrab'].', trayectoria '.$todosobrelapieza['trayectoria'].'
                     y un valor de '.$todosobrelapieza['valveloc'].' '.$todosobrelapieza['unidadveloc'].'.</b></li>
                <li><i>Elementos en suspensión: </i><b>'.$todosobrelapieza['elemsusp'].', con un valor de '.$todosobrelapieza['valsusp'].
                     '.</b></li>
                <li><i>¿Hubieron modificaciones en las condiciones de trabajo? </i><b>'.$todosobrelapieza['modifcond'].'.</b></li>
                <li><i>¿Qué modificaciones hubieron? </i><b>'.$todosobrelapieza['modificaciones'].'.</b></li>
                <li><i>Material: </i><b>'.$nombrematerial.'.</b></li>
                <li><i>Submaterial: </i><b>'.$nombresubmat.'.</b></li>
                <li><i>Material específico: </i><b>'.$nombrematespec.'.</b></li><br/>
            </ol>
             <table border="1" cellpadding="2" cellspacing="2" align="center">
             <tr nobr="true">
              <th colspan="5"><b>Propiedades del material específico '.$nombrematespec.'</b></th>
             </tr>
             <tr nobr="true">
              <td><i>Densidad</i></td>
              <td><i>Módulo elástico</i></td>
              <td><i>Elongación porcentual</i></td>
              <td><i>Tenacidad</i></td>
              <td><i>Coeficiente de Poisson</i></td>
             </tr>
             <tr nobr="true">
              <td>'.$dens.'</td>
              <td>'.$modelas.'</td>
              <td>'.$eporc.'</td>
              <td>'.$tenac.'</td>
              <td>'.$cpoisson.'</td>
             </tr>
            </table>';

    $pdf->writeHTML($desccomponentehtml, true, false, true, false, '');

    $pdf->AddPage();

    //Las imágenes subidas. Hago select de todas las que hay (asumo que al menos subió una.. y luego, si existe, lleno el html y mando al pdf).

    $imagenestitulohtml = '<h3>Imágenes del componente</h3>';

    $pdf->writeHTML($imagenestitulohtml, true, false, true, false, '');

    $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
    $datosimagenes = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);

    foreach ($datosimagenes as $key => $dat) {

         if($dat['queimagen']==1)
         {
            $imagenplano = 'Imagen del plano de la pieza.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }
         if($dat['queimagen']==2)
         {
            $imagenplano = 'Imagen de la pieza en buen estado.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }
         if($dat['queimagen']==3)
         {
            $imagenplano = 'Imagen del esquema de montaje y funcionamiento de la pieza.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }
         if($dat['queimagen']==8)
         {
            $imagenplano = 'Máquina o mecanismo sobre el cual se encuentra montada la pieza.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }
         if($dat['queimagen']==9)
         {
            $imagenplano = 'Esquema de montaje y funcionamiento de la pieza.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }


      }

    $pdf->AddPage();

    //Procesos de fabricación

    $procesostitulohtml = '<h3>BLOQUE 3: Procesos de fabricacón aplicados a la pieza</h3><br/>';

    $pdf->writeHTML($procesostitulohtml, true, false, true, false, '');

    $cantidadprocesos = $this->piezas_model->devolver_cantidadprocesosparareporte($idcaso);
    $cantidadprocesos = $cantidadprocesos - 1;
    $nombresprocesos = $this->casos_model->devolver_procesos($idcaso);
    $nombressubtipos = $this->casos_model->devolver_subtipos($idcaso);
    $numerosprocesos = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);


    for($i=0; $i<$cantidadprocesos; $i++)
    {
        $datosproveedor = $this->piezas_model->devolver_todosobreelproveedor($idcaso,$numerosprocesos[$i]);

        $proveedorhtml = '<h3>Proceso '.($i+1).': '.$nombresprocesos[$i].'. Subtipo: '.$nombressubtipos[$i].'</h3>
             <table border="1" cellpadding="2" cellspacing="2" align="center">
                 <tr nobr="true">
                     <th colspan="5"><b>Datos del proveedor</b></th>
                 </tr>
                 <tr nobr="true">
                     <td><i>Empresa</i></td>
                     <td><i>Responsable</i></td>
                     <td><i>Correo electrónico</i></td>
                     <td><i>Teléfono</i></td>
                     <td><i>Dirección</i></td>
                 </tr>
                 <tr nobr="true">
                     <td>'.$datosproveedor['empresa'].'</td>
                     <td>'.$datosproveedor['responsable'].'</td>
                     <td>'.$datosproveedor['correoelectronico'].'</td>
                     <td>'.$datosproveedor['telefono'].'</td>
                     <td>'.$datosproveedor['direccion'].'</td>
                 </tr>
             </table><br/>';

        $pdf->writeHTML($proveedorhtml, true, false, true, false, '');

        //Parametros generales
        $cantidadparamsgenerales = $this->piezas_model->devolver_cantidadprocesosgenerales($idcaso,$numerosprocesos[$i]);

        $cantidadderows = (float)$cantidadparamsgenerales/5;
        $cantidadderowsentero = intval($cantidadparamsgenerales/5);
        if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

        $paramgeneraleshtml1 = '
              <table border="1" cellpadding="2" cellspacing="2" align="center">
                 <tr nobr="true">
                     <th colspan="5"><b>Parámetros generales del proceso. Cantidad: '.$cantidadparamsgenerales.'</b></th>
                 </tr>';

        $paramgeneraleshtml2 = '';

        //Devuelvo todos los nombres de los parametros generales para mostrar en el proceso, y voy a necesitar el id del proceso en
        //cuestion para consultar en la tabla atributos.
        $nombresparametrosgen = $this->casos_model->devolver_nombresparametrosgen($idcaso,$numerosprocesos[$i]);
        $idprocesoinvolucrado = $this->casos_model->devolver_idprocesopornombre($nombresprocesos[$i]);
        $valoresparametrosgen = $this->casos_model->devolver_valoresparametrosgen($idcaso,$numerosprocesos[$i]);

        $numerocolumnas = 5;
        $atributosrestantes = $cantidadparamsgenerales;
        $indiceatributoamostrar = 0;

        for($j=0; $j<$cantidadderows; $j++)
        {
            $paramgeneraleshtml2 = $paramgeneraleshtml2.'<tr nobr="true">';

            for($k=0; $k<$numerocolumnas;$k++)
            {
                $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosgen[$indiceatributoamostrar],$idprocesoinvolucrado);

                $paramgeneraleshtml2 = $paramgeneraleshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                if($datosatributo['tipo_campo'] == "1")
                {
                    if($valoresparametrosgen[$indiceatributoamostrar]== "0") $valoresparametrosgen[$indiceatributoamostrar] = "No";
                    if($valoresparametrosgen[$indiceatributoamostrar]== "1") $valoresparametrosgen[$indiceatributoamostrar] = "Si";   
                }

                if($datosatributo['tipo_campo'] == "2")
                {
                    $valoresparametrosgen[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosgen[$indiceatributoamostrar]);
                }               

                $paramgeneraleshtml2 = $paramgeneraleshtml2.$valoresparametrosgen[$indiceatributoamostrar];


                $paramgeneraleshtml2 = $paramgeneraleshtml2.'</b></i></td>';

                if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                $indiceatributoamostrar = $indiceatributoamostrar + 1;
            }
            
            $paramgeneraleshtml2 = $paramgeneraleshtml2.'</tr>';

            $atributosrestantes = $atributosrestantes-5;
            
        }

        $paramgeneraleshtml2 = $paramgeneraleshtml2.'</table><br/>';

        $pdf->writeHTML($paramgeneraleshtml1.$paramgeneraleshtml2, true, false, true, false, '');


        //Parametros especificos

        if($nombressubtipos[$i] != "Ninguno")
        {
            $cantidadparamsespecificos = $this->piezas_model->devolver_cantidadprocesosespecificos($idcaso,$numerosprocesos[$i]);

            $cantidadderows = (float)$cantidadparamsespecificos/5;
            $cantidadderowsentero = intval($cantidadparamsespecificos/5);
            if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

            $paramespecificoshtml1 = '
                  <table border="1" cellpadding="2" cellspacing="2" align="center">
                     <tr nobr="true">
                         <th colspan="5"><b>Parámetros específicos del proceso. Cantidad: '.$cantidadparamsespecificos.'</b></th>
                     </tr>';

            $paramespecificoshtml2 = '';

            //Devuelvo todos los nombres de los parametros especificos para mostrar en el proceso, y voy a necesitar el id del proceso en
            //cuestion para consultar en la tabla atributos.
            $nombresparametrosesp = $this->casos_model->devolver_nombresparametrosesp($idcaso,$numerosprocesos[$i]);
            $idprocesoinvolucrado = $this->casos_model->devolver_idprocesopornombre($nombresprocesos[$i]);
            $valoresparametrosesp = $this->casos_model->devolver_valoresparametrosesp($idcaso,$numerosprocesos[$i]);

            $numerocolumnas = 5;
            $atributosrestantes = $cantidadparamsespecificos;
            $indiceatributoamostrar = 0;

            for($j=0; $j<$cantidadderows; $j++)
            {
                $paramespecificoshtml2 = $paramespecificoshtml2.'<tr nobr="true">';

                for($k=0; $k<$numerocolumnas;$k++)
                {
                    $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosesp[$indiceatributoamostrar],$idprocesoinvolucrado);

                    $paramespecificoshtml2 = $paramespecificoshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                    //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                    if($datosatributo['tipo_campo'] == "1")
                    {
                        if($valoresparametrosesp[$indiceatributoamostrar]== "0") $valoresparametrosesp[$indiceatributoamostrar] = "No";
                        if($valoresparametrosesp[$indiceatributoamostrar]== "1") $valoresparametrosesp[$indiceatributoamostrar] = "Si";   
                    }

                    if($datosatributo['tipo_campo'] == "2")
                    {
                        $valoresparametrosesp[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosesp[$indiceatributoamostrar]);
                    }               

                    $paramespecificoshtml2 = $paramespecificoshtml2.$valoresparametrosesp[$indiceatributoamostrar];


                    $paramespecificoshtml2 = $paramespecificoshtml2.'</b></i></td>';

                    if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                    $indiceatributoamostrar = $indiceatributoamostrar + 1;
                }
                
                $paramespecificoshtml2 = $paramespecificoshtml2.'</tr>';

                $atributosrestantes = $atributosrestantes-5;
                
            }

            $paramespecificoshtml2 = $paramespecificoshtml2.'</table><br/>';

            $pdf->writeHTML($paramespecificoshtml1.$paramespecificoshtml2, true, false, true, false, '');

        }

        $pdf->AddPage();

    }

    //Ensayos

    $cantidadensayos = ($this->piezas_model->devolver_cantidadensayos($idcaso))-1;
    $todosobreensayos = $this->piezas_model->devolver_todosobreensayos($idcaso);

    $cantidadimagenesdeensayo = $this->piezas_model->devolver_cantidadimagenesdeensayos($idpieza);
    $todosobreimgensayos = $this->piezas_model->devolver_todosobreimgdeensayos($idpieza);

    $titulobloqueensayoshtml = '<h3>BLOQUE 4: Ensayos</h3><br/>';
    $pdf->writeHTML($titulobloqueensayoshtml, true, false, true, false, '');

    $inicio = 0;
    $final = 0;

    for($i=0;$i<$cantidadensayos;$i++)
    {
        $ensayoshtml = '';
        $ensayoshtml = '<h3>Ensayo número '.($i+1).': '.$todosobreensayos[$i]['nombre'].'</h3>
             <span style="text-align:justify;">'.$todosobreensayos[$i]['descripcion'].'</span>';

        if($todosobreensayos[$i]['cant_imagenes']!=0)
        {
            $final = $final + $todosobreensayos[$i]['cant_imagenes'];

            $ensayoshtml = $ensayoshtml.'<br/><br/><b>Imágenes del ensayo.</b>';
            $pdf->writeHTML($ensayoshtml, true, false, true, false, '');

            for($inicio;$inicio<$final;$inicio++)
            {
                $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgensayos[$inicio]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');

            }
            
            $inicio = 0;
            $inicio = $inicio + $final;  

        }
        else $pdf->writeHTML($ensayoshtml, true, false, true, false, '');

    }

    $pdf->AddPage();


    //Macrografía

    $titulobloquemacrografiahtml = '<h3>BLOQUE 5: Macrografía</h3><br/><b>Imágenes de la macrografía.';
    $pdf->writeHTML($titulobloquemacrografiahtml, true, false, true, false, '');

    $cantidadmacrografias = $this->piezas_model->devolver_cantidaddescmacrografia($idcaso);
    $todosobremacrografia = $this->piezas_model->devolver_todosobremacrografia($idcaso);
    $todosobreimgmacrografia = $this->piezas_model->devolver_todosobreimgmacrografia($idpieza);

    if($todosobremacrografia[0]['tipo_fractura']==0) $todosobremacrografia[0]['tipo_fractura'] = 'Frágil';
    if($todosobremacrografia[0]['tipo_fractura']==1) $todosobremacrografia[0]['tipo_fractura'] = 'Dúctil';
    if($todosobremacrografia[0]['tipo_fractura']==2) $todosobremacrografia[0]['tipo_fractura'] = 'Mixta';

    $fracturapredom = 'Fractura predominante: '.$todosobremacrografia[0]['tipo_fractura'].'</b><br/>';
    $pdf->writeHTML($fracturapredom, true, false, true, false, '');

    $macrografiahtml = '';

    for($i=0; $i<$cantidadmacrografias;$i++)
    {
        $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgmacrografia[$i]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

        $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/>';

        $pdf->writeHTML($espacio, true, false, true, false, '');

        $macrografiahtml = '<b>Descripción: </b>'.$todosobremacrografia[$i]['descripcion'].'<br/>';

        $pdf->writeHTML($macrografiahtml, true, false, true, false, '');
    }


    $pdf->AddPage();


    //Micrografía

    $titulobloquemicrografiahtml = '<h3>BLOQUE 6: Micrografía</h3><br/><b>Imágenes de la micrografía.</b><br/>';
    $pdf->writeHTML($titulobloquemicrografiahtml, true, false, true, false, '');

    $cantidadmicrografias = $this->piezas_model->devolver_cantidaddescmicrografia($idcaso);
    $todosobremicrografia = $this->piezas_model->devolver_todosobremicrografia($idcaso);
    $todosobreimgmicrografia = $this->piezas_model->devolver_todosobreimgmicrografia($idpieza);

    $micrografiahtml = '';

    for($i=0; $i<$cantidadmicrografias;$i++)
    {
        $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgmicrografia[$i]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

        $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/>';

        $pdf->writeHTML($espacio, true, false, true, false, '');

        $micrografiahtml = '<b>Descripción: </b>'.$todosobremicrografia[$i]['descripcion'].'<br/>';

        $pdf->writeHTML($micrografiahtml, true, false, true, false, '');
    }

    $pdf->AddPage();


    //Discusión

    $discusion = $this->casos_model->devolver_discusionporidcaso($idcaso);

    $discusionhtml = '<h3>BLOQUE 7: Discusión</h3>
    <span style="text-align:justify;">'.$discusion.'</span><br/><br/><br/>';

    $pdf->writeHTML($discusionhtml, true, false, true, false, '');


    //Hipotesis

    $cantidadhipotesis = ($this->piezas_model->devolver_cantidadhipotesis($idcaso))-1;
    $todosobrehipotesis = $this->piezas_model->devolver_todosobrehipotesis($idcaso);

    $cantidadimagenesdehipotesis = $this->piezas_model->devolver_cantidadimagenesdehipotesis($idpieza);
    $todosobreimghipotesis = $this->piezas_model->devolver_todosobreimgdehipotesis($idpieza);

    $titulobloquehipotesishtml = '<h3>BLOQUE 8: Hipótesis del usuario</h3><br/>';
    $pdf->writeHTML($titulobloquehipotesishtml, true, false, true, false, '');

    $inicio = 0;
    $final = 0;

    for($i=0;$i<$cantidadhipotesis;$i++)
    {
        $hipotesishtml = '';
        $hipotesishtml = '<h3>Hipótesis número '.($i+1).': '.$todosobrehipotesis[$i]['titulo'].'. Valoración: '.$todosobrehipotesis[$i]['valoracion'].'</h3>
             <span style="text-align:justify;">'.$todosobrehipotesis[$i]['descripcion'].'</span>';

        if($todosobrehipotesis[$i]['cant_imagenes']!=0)
        {
            $final = $final + $todosobrehipotesis[$i]['cant_imagenes'];

            $hipotesishtml = $hipotesishtml.'<br/><br/><b>Imágenes de la hipótesis.</b>';
            $pdf->writeHTML($hipotesishtml, true, false, true, false, '');

            for($inicio;$inicio<$final;$inicio++)
            {
                $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimghipotesis[$inicio]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');

            }
            
            $inicio = 0;
            $inicio = $inicio + $final;  

        }
        else $pdf->writeHTML($hipotesishtml, true, false, true, false, '');

    }

    $pdf->AddPage();

    //Diagrama de Pareto

    $urldepareto = $this->piezas_model->devolver_urldeparetoparareporte($idpieza);

    $paretohtml = '<h3>Diagrama de Pareto para las hipótesis del caso</h3><br/><br/>';

    $pdf->writeHTML($paretohtml, true, false, true, false, '');

    $pdf->Image('http://localhost/cafap/uploads/'.$urldepareto, '', '', 180, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

    $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/><br/><br/><br/>';

    $pdf->writeHTML($espacio, true, false, true, false, '');


    //Conclusiones generales

    $conclusion = $this->casos_model->devolver_conclusionporidcaso($idcaso);

    $conclusionhtml = '<h3>BLOQUE 9: Conclusión del usuario</h3>
    <span style="text-align:justify;">'.$conclusion.'</span>';

    $pdf->writeHTML($conclusionhtml, true, false, true, false, '');


    $espacio = '<br/><br/>';

    $pdf->writeHTML($espacio, true, false, true, false, '');

    //Sugerencias del software

    //Procesos y subprocesos de fabricación (50):
    $idsprocesoscasoactual = $this->casos_model->devolver_procesosparasugerenciafallo($idcaso);
    $idssubprocesoscasoactual = $this->casos_model->devolver_subprocesosparasugerenciafallo($idcaso);
    //Tipo de fractura (20):
    $tipofracturacasoactual = $this->casos_model->devolver_tipofracturaporidcaso($idcaso);
    //Fase del ciclo de vida donde falló la pieza (9):
    $idpiezacasoactual = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
    $faseciclovidacasoactual = $this->casos_model->devolver_faseciclovida($idpiezacasoactual);
    //Material (7), Submaterial (11) y Material Específico (15):
    $materialcasoactual = $this->casos_model->devolver_materialparasugerencia($idpiezacasoactual);
    $submaterialcasoactual = $this->casos_model->devolver_submaterialparasugerencia($idpiezacasoactual);
    $materialespcasoactual = $this->casos_model->devolver_materialespparasugerencia($idpiezacasoactual);
    //Elementos en suspensión (3):
    $elemsuspcasoactual = $this->casos_model->devolver_elemsuspparasugerencia($idpiezacasoactual);
    //Modificaciones en condiciones de trabajo (3):
    $modifcondcasoactual = $this->casos_model->devolver_modifcondtrabparasugerencia($idpiezacasoactual);


    $totalafinidad = 0;

    $casosafines = array(
                  "id" => "",
                  "afinidad" => "",
                  );

    $casosfinalizados = $this->casos_model->devolver_finalizadosconconclusion();

    for($k = 0; $k <count($casosfinalizados); $k++)
    {
        //Procesos y subprocesos de fabricación (50):
        $idsprocesoscasoacomparar = $this->casos_model->devolver_procesosparasugerenciafallo($casosfinalizados[$k]);
        $idssubprocesoscasoacomparar = $this->casos_model->devolver_subprocesosparasugerenciafallo($casosfinalizados[$k]);
        //Tipo de fractura (20):
        $tipofracturacasoacomparar = $this->casos_model->devolver_tipofracturaporidcaso($casosfinalizados[$k]);
        //Fase del ciclo de vida donde falló la pieza (9):
        $idpiezacasoacomparar = $this->piezas_model->devolver_idpiezaporidcaso($casosfinalizados[$k]);
        $faseciclovidacasoacomparar = $this->casos_model->devolver_faseciclovida($idpiezacasoacomparar);
        //Material (7), Submaterial (11) y Material Específico (15):
        $materialcasoacomparar = $this->casos_model->devolver_materialparasugerencia($idpiezacasoacomparar);
        $submaterialcasoacomparar = $this->casos_model->devolver_submaterialparasugerencia($idpiezacasoacomparar);
        $materialespcasoacomparar = $this->casos_model->devolver_materialespparasugerencia($idpiezacasoacomparar);
        //Elementos en suspensión (3):
        $elemsuspcasoacomparar = $this->casos_model->devolver_elemsuspparasugerencia($idpiezacasoacomparar);
        //Modificaciones en condiciones de trabajo (3):
        $modifcondcasoacomparar = $this->casos_model->devolver_modifcondtrabparasugerencia($idpiezacasoacomparar);


        $afinidadentreprocesos = 0;

        for($i = 0; $i <count($idsprocesoscasoactual); $i++)
        {
            for($j = 0; $j <count($idsprocesoscasoacomparar); $j++)
            {
                if($idsprocesoscasoactual[$i]==$idsprocesoscasoacomparar[$j])
                {
                    if($idssubprocesoscasoactual[$i]==$idssubprocesoscasoacomparar[$j])
                    {
                        $afinidadentreprocesos++;
                        $idsprocesoscasoacomparar[$i] = -1;
                        $idssubprocesoscasoacomparar[$i] = -1;
                    }          
                } 
            }
        }

        $cantidadprocesoscasoactual = count($idsprocesoscasoactual);
        $porcentajeafinidadprocesos = ($afinidadentreprocesos*50)/$cantidadprocesoscasoactual;

        $totalafinidad = $porcentajeafinidadprocesos;

        if($tipofracturacasoactual == $tipofracturacasoacomparar) $totalafinidad += 20;
        if($faseciclovidacasoactual == $faseciclovidacasoacomparar) $totalafinidad += 9;

        $puntajematerial = 0;

        if($materialcasoactual == $materialcasoacomparar) $puntajematerial = 7;
        if($submaterialcasoactual == $submaterialcasoacomparar) $puntajematerial = 11;
        if($materialespcasoactual == $materialespcasoacomparar) $puntajematerial = 15;

        $totalafinidad += $puntajematerial;

        if($elemsuspcasoactual = $elemsuspcasoacomparar) $totalafinidad += 3;
        if($modifcondcasoactual = $modifcondcasoacomparar) $totalafinidad += 3;

        $casosafines['id'][$k] = $casosfinalizados[$k];
        $casosafines['afinidad'][$k] = $totalafinidad;
    //fin for
    }

    $this->casos_model->insertar_afinidades($casosafines);

    $afinidadesordenadas['idcasos'] = $this->casos_model->devolver_idsafinidadesord();
    $afinidadesordenadas['afinidades'] = $this->casos_model->devolver_afinidadesord();

    $this->casos_model->eliminar_afinidades();

    //Fin del paso 1 de obtener las afinidades ordenadas, ahora obtenemos el bloque más relevante junto con conclusión general.

    for($i = 0; $i <count($afinidadesordenadas['idcasos']); $i++)
    {
      $conclusiongeneral[$i] = $this->casos_model->devolver_conclusiongeneralparasugerencia($afinidadesordenadas['idcasos'][$i]);;
      $bloquehipotesismasrelevante[$i] = $this->casos_model->devolver_bloquehipotesismasrelevante($afinidadesordenadas['idcasos'][$i]);
      $concluhipotesismasrelevante[$i] = $this->casos_model->devolver_concluhipotesismasrelevante($afinidadesordenadas['idcasos'][$i],$bloquehipotesismasrelevante[$i]);;

      if ($i==10) $i = count($afinidadesordenadas['idcasos']);
    }

    $sugerenciashtml = '<h3>SUGERENCIAS DEL SOFTWARE</h3><br/>';

    $pdf->writeHTML($sugerenciashtml, true, false, true, false, '');

    $cuerposugerenciashtml = '';

    for($i = 0; $i <count($afinidadesordenadas['idcasos']); $i++) 
    {
        $cuerposugerenciashtml = $cuerposugerenciashtml.'<span style="text-align:justify;"><b>SUGERENCIA NÚMERO '.($i+1).'. Basada en un 
                                 caso con un '.$afinidadesordenadas['afinidades'][$i].'% de afinidad.</b><br/><br/>
                                 Bloque más influyente para este caso: ';
        $bloque = '';
        if($bloquehipotesismasrelevante[$i]=="conclusionbloque1") $bloque = 'BLOQUE 1 (INTRODUCCIÓN AL CASO)';
        if($bloquehipotesismasrelevante[$i]=="conclusionbloque2") $bloque = 'BLOQUE 2 (DESCRIPCIÓN DEL COMPONENTE)';
        if($bloquehipotesismasrelevante[$i]=="conclusionbloque3") $bloque = 'BLOQUE 3 (PROCESOS DE FABRICACIÓN)';
        if($bloquehipotesismasrelevante[$i]=="conclusionbloque4") $bloque = 'BLOQUE 4 (ENSAYOS)';
        if($bloquehipotesismasrelevante[$i]=="conclusionbloque5") $bloque = 'BLOQUE 5 (MACROGRAFÍA)';
        if($bloquehipotesismasrelevante[$i]=="conclusionbloque6") $bloque = 'BLOQUE 6 (MICROGRAFÍA))';
        if($bloquehipotesismasrelevante[$i]=="conclusionbloque8") $bloque = 'BLOQUE 8 (HIPÓTESIS DEL USUARIO)';


        $cuerposugerenciashtml = $cuerposugerenciashtml.$bloque.'. <br/><br/>
                                 Motivo de su influencia: '.$concluhipotesismasrelevante[$i].'<br/><br/>
                                 Conclusión del experto que analizó este caso (y posible motivo de la falla del suyo: '.$conclusiongeneral[$i].'
                                 <br/><br/></span>';
    }

    $pdf->writeHTML($cuerposugerenciashtml, true, false, true, false, '');
   

    //Conclusión del especialista

    $estadodelcaso = $this->casos_model->devolver_estadodelcaso($idcaso);

    if($estadodelcaso == 3)
    {
        $conclusionesp = $this->casos_model->devolver_conclusionespporidcaso($idcaso);

        $conclusionesphtml = '<br/><br/><br/><h3>CONCLUSÓN FINAL DEL ESPECIALISTA</h3>
        <span style="text-align:justify;">'.$conclusionesp.'</span>';

        $pdf->writeHTML($conclusionesphtml, true, false, true, false, '');
    }

    $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>';

    $pdf->writeHTML($espacio, true, false, true, false, '');

    //Agradecimiento

    $agradecimiento = '<h2 align="center">¡Muchas gracias por utilizar CAFAP!</h2>';

    $pdf->writeHTML($agradecimiento, true, false, true, false, '');
    

    
 
    // ---------------------------------------------------------    
 
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
     $pdf->Output('Caso de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');    

     if($esusuariocomun == 1); 
 
    //============================================================+
    // END OF FILE
    //============================================================+
    }




    public function verprocesodesdeedicion($idcaso,$numeroproceso) {

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Federico Sarmiento');
    $pdf->SetTitle('Reporte de caso');
    $pdf->SetSubject('Reporte CAFAP');
    $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
 
    //Nombre Usuario
    $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
    $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
 
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
 
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
 
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
 
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
 
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
 
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }   
 
    // ---------------------------------------------------------    
 
    // set default font subsetting mode
    $pdf->setFontSubsetting(true);   
 
    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 10, '', true);   
 
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage(); 
 
    //Título y descripción del caso
    $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);

    //Todo lo necesario para el bloque de introducción y el bloque de componente
    $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

    $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

    $datosproveedor = $this->piezas_model->devolver_todosobreelproveedor($idcaso,$numeroproceso);

    $idprocesoinv = $this->casos_model->devolver_idprocesopoidcasoynumeroproceso($idcaso,$numeroproceso);
    $idsubprocesoinv = $this->casos_model->devolver_idsubprocesopoidcasoynumeroproceso($idcaso,$numeroproceso);
    $nombresubprocesoinv = $this->casos_model->devolver_nombresubtipoporidparaedicion($idsubprocesoinv);
    $nombreprocesoinv = $this->casos_model->devolver_nombreprocesoporid($idprocesoinv);

    $proveedorhtml = '<h3>Proceso '.($numeroproceso).' aplicado a la pieza: '.$nombreprocesoinv.'. Subtipo: '.$nombresubprocesoinv.'</h3>
         <table border="1" cellpadding="2" cellspacing="2" align="center">
             <tr nobr="true">
                 <th colspan="5"><b>Datos del proveedor</b></th>
             </tr>
             <tr nobr="true">
                 <td><i>Empresa</i></td>
                 <td><i>Responsable</i></td>
                 <td><i>Correo electrónico</i></td>
                 <td><i>Teléfono</i></td>
                 <td><i>Dirección</i></td>
             </tr>
             <tr nobr="true">
                 <td>'.$datosproveedor['empresa'].'</td>
                 <td>'.$datosproveedor['responsable'].'</td>
                 <td>'.$datosproveedor['correoelectronico'].'</td>
                 <td>'.$datosproveedor['telefono'].'</td>
                 <td>'.$datosproveedor['direccion'].'</td>
             </tr>
         </table><br/>';

    $pdf->writeHTML($proveedorhtml, true, false, true, false, '');

    //Parametros generales
    $cantidadparamsgenerales = $this->piezas_model->devolver_cantidadprocesosgenerales($idcaso,$numeroproceso);

    $cantidadderows = (float)$cantidadparamsgenerales/5;
    $cantidadderowsentero = intval($cantidadparamsgenerales/5);
    if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

    $paramgeneraleshtml1 = '
          <table border="1" cellpadding="2" cellspacing="2" align="center">
             <tr nobr="true">
                 <th colspan="5"><b>Parámetros generales del proceso. Cantidad: '.$cantidadparamsgenerales.'</b></th>
             </tr>';

    $paramgeneraleshtml2 = '';

    //Devuelvo todos los nombres de los parametros generales para mostrar en el proceso, y voy a necesitar el id del proceso en
    //cuestion para consultar en la tabla atributos.
    $nombresparametrosgen = $this->casos_model->devolver_nombresparametrosgen($idcaso,$numeroproceso);
    $valoresparametrosgen = $this->casos_model->devolver_valoresparametrosgen($idcaso,$numeroproceso);

    $numerocolumnas = 5;
    $atributosrestantes = $cantidadparamsgenerales;
    $indiceatributoamostrar = 0;

    for($j=0; $j<$cantidadderows; $j++)
    {
        $paramgeneraleshtml2 = $paramgeneraleshtml2.'<tr nobr="true">';

        for($k=0; $k<$numerocolumnas;$k++)
        {
            $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosgen[$indiceatributoamostrar],$idprocesoinv);

            $paramgeneraleshtml2 = $paramgeneraleshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

            //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
            if($datosatributo['tipo_campo'] == "1")
            {
                if($valoresparametrosgen[$indiceatributoamostrar]== "0") $valoresparametrosgen[$indiceatributoamostrar] = "No";
                if($valoresparametrosgen[$indiceatributoamostrar]== "1") $valoresparametrosgen[$indiceatributoamostrar] = "Si";   
            }

            if($datosatributo['tipo_campo'] == "2")
            {
                $valoresparametrosgen[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosgen[$indiceatributoamostrar]);
            }               

            $paramgeneraleshtml2 = $paramgeneraleshtml2.$valoresparametrosgen[$indiceatributoamostrar];


            $paramgeneraleshtml2 = $paramgeneraleshtml2.'</b></i></td>';

            if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

            $indiceatributoamostrar = $indiceatributoamostrar + 1;
        }
        
        $paramgeneraleshtml2 = $paramgeneraleshtml2.'</tr>';

        $atributosrestantes = $atributosrestantes-5;
        
    }

    $paramgeneraleshtml2 = $paramgeneraleshtml2.'</table><br/>';

    $pdf->writeHTML($paramgeneraleshtml1.$paramgeneraleshtml2, true, false, true, false, '');


    //Parametros especificos

    if($nombresubprocesoinv != "Ninguno")
    {
        $cantidadparamsespecificos = $this->piezas_model->devolver_cantidadprocesosespecificos($idcaso,$numeroproceso);

        $cantidadderows = (float)$cantidadparamsespecificos/5;
        $cantidadderowsentero = intval($cantidadparamsespecificos/5);
        if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

        $paramespecificoshtml1 = '
              <table border="1" cellpadding="2" cellspacing="2" align="center">
                 <tr nobr="true">
                     <th colspan="5"><b>Parámetros específicos del proceso. Cantidad: '.$cantidadparamsespecificos.'</b></th>
                 </tr>';

        $paramespecificoshtml2 = '';

        //Devuelvo todos los nombres de los parametros especificos para mostrar en el proceso, y voy a necesitar el id del proceso en
        //cuestion para consultar en la tabla atributos.
        $nombresparametrosesp = $this->casos_model->devolver_nombresparametrosesp($idcaso,$numeroproceso);
        $valoresparametrosesp = $this->casos_model->devolver_valoresparametrosesp($idcaso,$numeroproceso);

        $numerocolumnas = 5;
        $atributosrestantes = $cantidadparamsespecificos;
        $indiceatributoamostrar = 0;

        for($j=0; $j<$cantidadderows; $j++)
        {
            $paramespecificoshtml2 = $paramespecificoshtml2.'<tr nobr="true">';

            for($k=0; $k<$numerocolumnas;$k++)
            {
                $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosesp[$indiceatributoamostrar],$idprocesoinv);

                $paramespecificoshtml2 = $paramespecificoshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                if($datosatributo['tipo_campo'] == "1")
                {
                    if($valoresparametrosesp[$indiceatributoamostrar]== "0") $valoresparametrosesp[$indiceatributoamostrar] = "No";
                    if($valoresparametrosesp[$indiceatributoamostrar]== "1") $valoresparametrosesp[$indiceatributoamostrar] = "Si";   
                }

                if($datosatributo['tipo_campo'] == "2")
                {
                    $valoresparametrosesp[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosesp[$indiceatributoamostrar]);
                }               

                $paramespecificoshtml2 = $paramespecificoshtml2.$valoresparametrosesp[$indiceatributoamostrar];


                $paramespecificoshtml2 = $paramespecificoshtml2.'</b></i></td>';

                if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                $indiceatributoamostrar = $indiceatributoamostrar + 1;
            }
            
            $paramespecificoshtml2 = $paramespecificoshtml2.'</tr>';

            $atributosrestantes = $atributosrestantes-5;
            
        }

        $paramespecificoshtml2 = $paramespecificoshtml2.'</table><br/><br/>';

        $pdf->writeHTML($paramespecificoshtml1.$paramespecificoshtml2, true, false, true, false, '');

    }

 
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
     $pdf->Output('Proceso de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');    

     //cambiar para volver al inicio
 
    //============================================================+
    // END OF FILE
    //============================================================+
    }


    public function verensayosdesdeedicion($idcaso) {

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Federico Sarmiento');
    $pdf->SetTitle('Reporte de caso');
    $pdf->SetSubject('Reporte CAFAP');
    $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
 
    //Nombre Usuario
    $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
    $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
 
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
 
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
 
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
 
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
 
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
 
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }   
 
    // ---------------------------------------------------------    
 
    // set default font subsetting mode
    $pdf->setFontSubsetting(true);   
 
    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 10, '', true);   
 
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage(); 
 
    //Título y descripción del caso
    $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);

    //Todo lo necesario para el bloque de introducción y el bloque de componente
    $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

    $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

    //Ensayos

    $cantidadensayos = ($this->piezas_model->devolver_cantidadensayos($idcaso))-1;
    $todosobreensayos = $this->piezas_model->devolver_todosobreensayos($idcaso);

    $cantidadimagenesdeensayo = $this->piezas_model->devolver_cantidadimagenesdeensayos($idpieza);
    $todosobreimgensayos = $this->piezas_model->devolver_todosobreimgdeensayos($idpieza);

    $titulobloqueensayoshtml = '<h3>BLOQUE 4: Ensayos</h3><br/>';
    $pdf->writeHTML($titulobloqueensayoshtml, true, false, true, false, '');

    $inicio = 0;
    $final = 0;

    for($i=0;$i<$cantidadensayos;$i++)
    {
        $ensayoshtml = '';
        $ensayoshtml = '<h3>Ensayo número '.($i+1).': '.$todosobreensayos[$i]['nombre'].'</h3>
             <span style="text-align:justify;">'.$todosobreensayos[$i]['descripcion'].'</span>';

        if($todosobreensayos[$i]['cant_imagenes']!=0)
        {
            $final = $final + $todosobreensayos[$i]['cant_imagenes'];

            $ensayoshtml = $ensayoshtml.'<br/><br/><b>Imágenes del ensayo.</b>';
            $pdf->writeHTML($ensayoshtml, true, false, true, false, '');

            for($inicio;$inicio<$final;$inicio++)
            {
                $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgensayos[$inicio]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');

            }
            
            $inicio = 0;
            $inicio = $inicio + $final;  

        }
        else $pdf->writeHTML($ensayoshtml, true, false, true, false, '');

    }

 
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
     $pdf->Output('Ensayos de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');    

     //cambiar para volver al inicio
 
    //============================================================+
    // END OF FILE
    //============================================================+
    }



    public function vermacrografiadesdeedicion($idcaso) {

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Federico Sarmiento');
    $pdf->SetTitle('Reporte de caso');
    $pdf->SetSubject('Reporte CAFAP');
    $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
 
    //Nombre Usuario
    $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
    $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
 
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
 
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
 
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
 
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
 
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
 
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }   
 
    // ---------------------------------------------------------    
 
    // set default font subsetting mode
    $pdf->setFontSubsetting(true);   
 
    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 10, '', true);   
 
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage(); 
 
    //Título y descripción del caso
    $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);

    //Todo lo necesario para el bloque de introducción y el bloque de componente
    $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

    $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

    $titulobloquemacrografiahtml = '<h3>Macrografía</h3><br/><b>Imágenes de la macrografía.';
    $pdf->writeHTML($titulobloquemacrografiahtml, true, false, true, false, '');

    $cantidadmacrografias = $this->piezas_model->devolver_cantidaddescmacrografia($idcaso);
    $todosobremacrografia = $this->piezas_model->devolver_todosobremacrografia($idcaso);
    $todosobreimgmacrografia = $this->piezas_model->devolver_todosobreimgmacrografia($idpieza);

    if($todosobremacrografia[0]['tipo_fractura']==0) $todosobremacrografia[0]['tipo_fractura'] = 'Frágil';
    if($todosobremacrografia[0]['tipo_fractura']==1) $todosobremacrografia[0]['tipo_fractura'] = 'Dúctil';
    if($todosobremacrografia[0]['tipo_fractura']==2) $todosobremacrografia[0]['tipo_fractura'] = 'Mixta';

    $fracturapredom = 'Fractura predominante: '.$todosobremacrografia[0]['tipo_fractura'].'</b><br/>';
    $pdf->writeHTML($fracturapredom, true, false, true, false, '');

    $macrografiahtml = '';

    for($i=0; $i<$cantidadmacrografias;$i++)
    {
        $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgmacrografia[$i]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

        $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/>';

        $pdf->writeHTML($espacio, true, false, true, false, '');

        $macrografiahtml = '<b>Descripción: </b>'.$todosobremacrografia[$i]['descripcion'].'<br/>';

        $pdf->writeHTML($macrografiahtml, true, false, true, false, '');
    }


 
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
     $pdf->Output('Macrografia de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');    

     //cambiar para volver al inicio
 
    //============================================================+
    // END OF FILE
    //============================================================+
    }

    public function vermicrografiadesdeedicion($idcaso) {

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Federico Sarmiento');
    $pdf->SetTitle('Reporte de caso');
    $pdf->SetSubject('Reporte CAFAP');
    $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
 
    //Nombre Usuario
    $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
    $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
 
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
 
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
 
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
 
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
 
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
 
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }   
 
    // ---------------------------------------------------------    
 
    // set default font subsetting mode
    $pdf->setFontSubsetting(true);   
 
    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 10, '', true);   
 
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage(); 
 
    //Título y descripción del caso
    $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);

    //Todo lo necesario para el bloque de introducción y el bloque de componente
    $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

    $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

    $titulobloquemicrografiahtml = '<h3>BLOQUE 6: Micrografía</h3><br/><b>Imágenes de la micrografía.</b><br/>';
    $pdf->writeHTML($titulobloquemicrografiahtml, true, false, true, false, '');

    $cantidadmicrografias = $this->piezas_model->devolver_cantidaddescmicrografia($idcaso);
    $todosobremicrografia = $this->piezas_model->devolver_todosobremicrografia($idcaso);
    $todosobreimgmicrografia = $this->piezas_model->devolver_todosobreimgmicrografia($idpieza);

    $micrografiahtml = '';

    for($i=0; $i<$cantidadmicrografias;$i++)
    {
        $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgmicrografia[$i]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

        $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/>';

        $pdf->writeHTML($espacio, true, false, true, false, '');

        $micrografiahtml = '<b>Descripción: </b>'.$todosobremicrografia[$i]['descripcion'].'<br/>';

        $pdf->writeHTML($micrografiahtml, true, false, true, false, '');
    }


 
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
     $pdf->Output('Micrografia de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');    

     //cambiar para volver al inicio
 
    //============================================================+
    // END OF FILE
    //============================================================+
    }


    public function verhipotesisdesdeedicion($idcaso) {

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Federico Sarmiento');
    $pdf->SetTitle('Reporte de caso');
    $pdf->SetSubject('Reporte CAFAP');
    $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
 
    //Nombre Usuario
    $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
    $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
 
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
 
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
 
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
 
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
 
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
 
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }   
 
    // ---------------------------------------------------------    
 
    // set default font subsetting mode
    $pdf->setFontSubsetting(true);   
 
    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 10, '', true);   
 
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage(); 
 
    //Título y descripción del caso
    $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);

    //Todo lo necesario para el bloque de introducción y el bloque de componente
    $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

    $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);

    $cantidadhipotesis = ($this->piezas_model->devolver_cantidadhipotesis($idcaso))-1;
    $todosobrehipotesis = $this->piezas_model->devolver_todosobrehipotesis($idcaso);

    $cantidadimagenesdehipotesis = $this->piezas_model->devolver_cantidadimagenesdehipotesis($idpieza);
    $todosobreimghipotesis = $this->piezas_model->devolver_todosobreimgdehipotesis($idpieza);

    $titulobloquehipotesishtml = '<h3>Hipótesis del usuario</h3><br/>';
    $pdf->writeHTML($titulobloquehipotesishtml, true, false, true, false, '');

    $inicio = 0;
    $final = 0;

    for($i=0;$i<$cantidadhipotesis;$i++)
    {
        $hipotesishtml = '';
        $hipotesishtml = '<h3>Hipótesis número '.($i+1).': '.$todosobrehipotesis[$i]['titulo'].'. Valoración: '.$todosobrehipotesis[$i]['valoracion'].'</h3>
             <span style="text-align:justify;">'.$todosobrehipotesis[$i]['descripcion'].'</span>';

        if($todosobrehipotesis[$i]['cant_imagenes']!=0)
        {
            $final = $final + $todosobrehipotesis[$i]['cant_imagenes'];

            $hipotesishtml = $hipotesishtml.'<br/><br/><b>Imágenes de la hipótesis.</b>';
            $pdf->writeHTML($hipotesishtml, true, false, true, false, '');

            for($inicio;$inicio<$final;$inicio++)
            {
                $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimghipotesis[$inicio]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');

            }
            
            $inicio = 0;
            $inicio = $inicio + $final;  

        }
        else $pdf->writeHTML($hipotesishtml, true, false, true, false, '');

    }

    $pdf->AddPage();
    
    //Diagrama de Pareto

    $urldepareto = $this->piezas_model->devolver_urldeparetoparareporte($idpieza);

    $paretohtml = '<h3>Diagrama de Pareto para las hipótesis del caso</h3><br/><br/>';

    $pdf->writeHTML($paretohtml, true, false, true, false, '');

    $pdf->Image('http://localhost/cafap/uploads/'.$urldepareto, '', '', 180, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

 
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
     $pdf->Output('Hipotesis de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');    

     //cambiar para volver al inicio
 
    //============================================================+
    // END OF FILE
    //============================================================+
    }



    public function verdatosespecialista($idcaso,$paso) {

    // create new PDF document

    if($paso == 0 || $paso == 1 || $paso == 2)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Federico Sarmiento');
        $pdf->SetTitle('Reporte de caso');
        $pdf->SetSubject('Reporte CAFAP');
        $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
     
        //Nombre Usuario
        $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
        $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
     
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
     
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
     
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
     
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
     
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
     
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }   
     
        // ---------------------------------------------------------    
     
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);   
     
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 10, '', true);   
     
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage(); 
     
        //Título y descripción del caso
        $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);
        //<br/>
        $tituloydescripcionhtml = '<h1 align="center">Caso: '.$tituloydesccaso['titulo'].'</h1><br/>
                 <h3>Descripción del caso</h3>
                 <span style="text-align:justify;">'.$tituloydesccaso['descripcion'].'</span>';
                
        $pdf->writeHTML($tituloydescripcionhtml, true, false, true, false, ''); 

        
        $pdf->Output('Caso de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');

    }


    if($paso == 3 || $paso == 4 || $paso == 5)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Federico Sarmiento');
        $pdf->SetTitle('Reporte de caso');
        $pdf->SetSubject('Reporte CAFAP');
        $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
     
        //Nombre Usuario
        $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
        $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
     
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
     
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
     
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
     
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
     
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
     
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }   
     
        // ---------------------------------------------------------    
     
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);   
     
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 10, '', true);   
     
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage(); 
     
        //Título y descripción del caso
        $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);
        //<br/>
        $tituloydescripcionhtml = '<h1 align="center">Caso: '.$tituloydesccaso['titulo'].'</h1><br/>
                 <h3>Descripción del caso</h3>
                 <span style="text-align:justify;">'.$tituloydesccaso['descripcion'].'</span>';
                
        $pdf->writeHTML($tituloydescripcionhtml, true, false, true, false, ''); 

        //Todo lo necesario para el bloque de introducción y el bloque de componente
        $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

        //Bloque de introducción
        if($todosobrelapieza['fallo_multiplesoc']==0) $todosobrelapieza['fallo_multiplesoc'] = 'No';
        if($todosobrelapieza['fallo_multiplesoc']==1) $todosobrelapieza['fallo_multiplesoc'] = 'Si';
        if($todosobrelapieza['ttrabajo_tiempo']==0) $todosobrelapieza['ttrabajo_tiempo'] = 'años';
        if($todosobrelapieza['ttrabajo_tiempo']==1) $todosobrelapieza['ttrabajo_tiempo'] = 'meses';
        if($todosobrelapieza['ttrabajo_tiempo']==2) $todosobrelapieza['ttrabajo_tiempo'] = 'días';
        if($todosobrelapieza['ttrabajo_tiempo']==3) $todosobrelapieza['ttrabajo_tiempo'] = 'horas';
        if($todosobrelapieza['vutil_tiempo']==0) $todosobrelapieza['vutil_tiempo'] = 'años';
        if($todosobrelapieza['vutil_tiempo']==1) $todosobrelapieza['vutil_tiempo'] = 'meses';
        if($todosobrelapieza['vutil_tiempo']==2) $todosobrelapieza['vutil_tiempo'] = 'dias';
        if($todosobrelapieza['vutil_tiempo']==3) $todosobrelapieza['vutil_tiempo'] = 'horas';
        if($todosobrelapieza['fase_ciclovida']==0) $todosobrelapieza['fase_ciclovida'] = 'Mantenimiento';
        if($todosobrelapieza['fase_ciclovida']==1) $todosobrelapieza['fase_ciclovida'] = 'Servicio';
        if($todosobrelapieza['fase_ciclovida']==2) $todosobrelapieza['fase_ciclovida'] = 'Periodo de prueba';
        if($todosobrelapieza['fase_ciclovida']==3) $todosobrelapieza['fase_ciclovida'] = 'Prototipo';


        $introhtml = '<br/><h3>BLOQUE 1: Introducción al caso</h3>
                <ol>                
                    <li><i>¿El mismo modelo ha fallado en múltipes ocasiones? </i><b>'.$todosobrelapieza['fallo_multiplesoc'].'.</b></li>
                    <li><i>Tiempo de trabajo que estuvo sometida la pieza antes de la rotura:</i>
                           <b>'.$todosobrelapieza['ttrabajocant'].' '.$todosobrelapieza['ttrabajo_tiempo'].'.</b></li>
                    <li><i>Vida útil de la pieza (según fabricante), o vida esperada:</i>
                    <b>'.$todosobrelapieza['vutil_cantidad'].' '.$todosobrelapieza['vutil_tiempo'].'.</li>
                    <li><i>Faso del ciclo de vida en donde se produjo la falla: </i><b>'.$todosobrelapieza['fase_ciclovida'].'.</b></li>   
                </ol>';

        $pdf->writeHTML($introhtml, true, false, true, false, '');

        //Bloque de descripción del componente
        if($todosobrelapieza['montadabien']==0) $todosobrelapieza['montadabien'] = 'No';
        if($todosobrelapieza['montadabien']==1) $todosobrelapieza['montadabien'] = 'Si';
        if($todosobrelapieza['tipocargas']==0) $todosobrelapieza['tipocargas'] = 'Fuerza';
        if($todosobrelapieza['tipocargas']==1) $todosobrelapieza['tipocargas'] = 'Presión';
        if($todosobrelapieza['tipocargas']==2) $todosobrelapieza['tipocargas'] = 'Gravedad';
        if($todosobrelapieza['tipocargas']==3) $todosobrelapieza['tipocargas'] = 'Torsión';
        if($todosobrelapieza['tipocargas']==4) $todosobrelapieza['tipocargas'] = 'Fricción';
        if($todosobrelapieza['tipocargas']==5) $todosobrelapieza['tipocargas'] = 'Salto térmico';
        if($todosobrelapieza['umedidacargas']==0) $todosobrelapieza['umedidacargas'] = 'Newton';
        if($todosobrelapieza['umedidacargas']==1) $todosobrelapieza['umedidacargas'] = 'Kilogramos';
        if($todosobrelapieza['umedidacargas']==2) $todosobrelapieza['umedidacargas'] = 'Libras';
        if($todosobrelapieza['tiposujeciones']==0) $todosobrelapieza['tiposujeciones'] = 'Fuerza';
        if($todosobrelapieza['tiposujeciones']==1) $todosobrelapieza['tiposujeciones'] = 'Presión';
        if($todosobrelapieza['tiposujeciones']==2) $todosobrelapieza['tiposujeciones'] = 'Gravedad';
        if($todosobrelapieza['tiposujeciones']==3) $todosobrelapieza['tiposujeciones'] = 'Torsión';
        if($todosobrelapieza['tiposujeciones']==4) $todosobrelapieza['tiposujeciones'] = 'Fricción';
        if($todosobrelapieza['tiposujeciones']==5) $todosobrelapieza['tiposujeciones'] = 'Salto térmico';
        if($todosobrelapieza['condtermicas']==0) $todosobrelapieza['condtermicas'] = 'Temperatura ambiental';
        if($todosobrelapieza['condtermicas']==1) $todosobrelapieza['condtermicas'] = 'Temperatura de contacto';
        if($todosobrelapieza['condtermicas']==2) $todosobrelapieza['condtermicas'] = 'Salto térmico';
        if($todosobrelapieza['utempcondtermicas']==0) $todosobrelapieza['utempcondtermicas'] = 'Celcius';
        if($todosobrelapieza['utempcondtermicas']==1) $todosobrelapieza['utempcondtermicas'] = 'Fahrenheit';
        if($todosobrelapieza['utempcondtermicas']==2) $todosobrelapieza['utempcondtermicas'] = 'Kelvin';
        if($todosobrelapieza['utempcondtermicas']==3) $todosobrelapieza['utempcondtermicas'] = 'Rankine';
        if($todosobrelapieza['tipopresiones']==0) $todosobrelapieza['tipopresiones'] = 'Unidireccional';
        if($todosobrelapieza['tipopresiones']==1) $todosobrelapieza['tipopresiones'] = 'Bidireccional';
        if($todosobrelapieza['tipopresiones']==2) $todosobrelapieza['tipopresiones'] = 'Tridireccional';
        if($todosobrelapieza['distribpresiones']==0) $todosobrelapieza['distribpresiones'] = 'Puntual';
        if($todosobrelapieza['distribpresiones']==1) $todosobrelapieza['distribpresiones'] = 'Uniforme';
        if($todosobrelapieza['distribpresiones']==2) $todosobrelapieza['distribpresiones'] = 'Parcial';
        if($todosobrelapieza['distribpresiones']==3) $todosobrelapieza['distribpresiones'] = 'Hidrostática';
        if($todosobrelapieza['umedidapres']==0) $todosobrelapieza['umedidapres'] = 'Kg/cm2';
        if($todosobrelapieza['umedidapres']==1) $todosobrelapieza['umedidapres'] = 'BAR';
        if($todosobrelapieza['umedidapres']==2) $todosobrelapieza['umedidapres'] = 'PSI';
        if($todosobrelapieza['umedidapres']==3) $todosobrelapieza['umedidapres'] = 'Lb/pulgada2';
        if($todosobrelapieza['veloctrab']==0) $todosobrelapieza['veloctrab'] = 'Lineal';
        if($todosobrelapieza['veloctrab']==1) $todosobrelapieza['veloctrab'] = 'Rotativa';
        if($todosobrelapieza['veloctrab']==2) $todosobrelapieza['veloctrab'] = 'Otras';
        if($todosobrelapieza['trayectoria']==0) $todosobrelapieza['trayectoria'] = 'Continua';
        if($todosobrelapieza['trayectoria']==1) $todosobrelapieza['trayectoria'] = 'Por tramos';
        if($todosobrelapieza['trayectoria']==2) $todosobrelapieza['trayectoria'] = 'Alternada';
        if($todosobrelapieza['unidadveloc']==0) $todosobrelapieza['unidadveloc'] = 'M/seg';
        if($todosobrelapieza['unidadveloc']==1) $todosobrelapieza['unidadveloc'] = 'Km/hr';
        if($todosobrelapieza['unidadveloc']==2) $todosobrelapieza['unidadveloc'] = 'Millas/hr';
        if($todosobrelapieza['unidadveloc']==3) $todosobrelapieza['unidadveloc'] = 'Pie/seg';
        if($todosobrelapieza['elemsusp']==0) $todosobrelapieza['elemsusp'] = 'Ninguno';
        if($todosobrelapieza['elemsusp']==1) $todosobrelapieza['elemsusp'] = 'Plomo';
        if($todosobrelapieza['elemsusp']==2) $todosobrelapieza['elemsusp'] = 'Azufre';
        if($todosobrelapieza['elemsusp']==3) $todosobrelapieza['elemsusp'] = 'Carbono';
        if($todosobrelapieza['modifcond']==0) $todosobrelapieza['modifcond'] = 'No';
        if($todosobrelapieza['modifcond']==1) $todosobrelapieza['modifcond'] = 'Si';
        if($todosobrelapieza['modificaciones']==0) $todosobrelapieza['modificaciones'] = 'Ninguna';
        if($todosobrelapieza['modificaciones']==1) $todosobrelapieza['modificaciones'] = 'Cambio de temperatura de trabajo';
        if($todosobrelapieza['modificaciones']==2) $todosobrelapieza['modificaciones'] = 'Cambio de fuerzas';
        if($todosobrelapieza['modificaciones']==3) $todosobrelapieza['modificaciones'] = 'Cambio de velocidades';
        if($todosobrelapieza['modificaciones']==4) $todosobrelapieza['modificaciones'] = 'Cambio de sujeciones';
        if($todosobrelapieza['modificaciones']==5) $todosobrelapieza['modificaciones'] = 'Cambio de lubricación';
        if($todosobrelapieza['modificaciones']==6) $todosobrelapieza['modificaciones'] = 'Cambio de ambiente';

        $nombrematerial = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['material'],'material');
        $nombresubmat = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['submaterial'],'submaterial');
        $nombrematespec = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['especifico'],'material_especifico');

        $propiedadesmatesp = $this->material_model->devolver_propmatesp($todosobrelapieza['especifico']);
        foreach ($propiedadesmatesp as $key => $prop) {
             $dens = $prop['densidad'];
             $modelas = $prop['modulo_elastico'];
             $eporc = $prop['elongacion_porc'];
             $tenac = $prop['tenacidad'];
             $cpoisson = $prop['coef_poisson']; 
          }


        $desccomponentehtml = '<br/><h3>BLOQUE 2: Descripción del componente</h3>
                <ol>                
                    <li><i>Nombre genérico de la pieza o el conjunto que presenta la falla: </i><b>'.$todosobrelapieza['nombregen'].'.</b></li>
                    <li><i>Nombre o código interno de la pieza o el conjunto: </i><b>'.$todosobrelapieza['codinterno'].'.</b></li>
                    <li><i>Cantidad de piezas que presentan la falla: </i><b>'.$todosobrelapieza['cantidadfalladas'].'.</b></li>
                    <li><i>Uso de la pieza (uso específico del componente, o forma de uso habitual): </i><b>'.$todosobrelapieza['usopieza'].'.</b></li>
                    <li><i>¿La pieza fue montada y utilizada siguiendo las especificaciones técnicas recomendadas por el fabricante 
                           o normas relativas al componente? </i><b>'.$todosobrelapieza['montadabien'].'.</b></li>   
                    <li><i>Condiciones de trabajo de la pieza: </i><b>'.$todosobrelapieza['descdetallada'].'.</b></li>
                    <li><i>Tipo de carga: </i><b>'.$todosobrelapieza['tipocargas'].', con un valor de '.$todosobrelapieza['cantcargas'].'
                         '.$todosobrelapieza['umedidacargas'].'.</b></li>
                    <li><i>Tipo de sujeciones: </i><b>'.$todosobrelapieza['tiposujeciones'].'.</b></li>
                    <li><i>Condición térmica: </i><b>'.$todosobrelapieza['condtermicas'].', con un valor de '.$todosobrelapieza['cantidadtermica'].'
                         '.$todosobrelapieza['utempcondtermicas'].'.</b></li>
                    <li><i>Presión: </i><b>'.$todosobrelapieza['tipopresiones'].', distribución '.$todosobrelapieza['distribpresiones'].'
                         y un valor de '.$todosobrelapieza['valpresion'].' '.$todosobrelapieza['umedidapres'].'.</b></li>
                    <li><i>Velocidad de trabajo: </i><b>'.$todosobrelapieza['veloctrab'].', trayectoria '.$todosobrelapieza['trayectoria'].'
                         y un valor de '.$todosobrelapieza['valveloc'].' '.$todosobrelapieza['unidadveloc'].'.</b></li>
                    <li><i>Elementos en suspensión: </i><b>'.$todosobrelapieza['elemsusp'].', con un valor de '.$todosobrelapieza['valsusp'].
                         '.</b></li>
                    <li><i>¿Hubieron modificaciones en las condiciones de trabajo? </i><b>'.$todosobrelapieza['modifcond'].'.</b></li>
                    <li><i>¿Qué modificaciones hubieron? </i><b>'.$todosobrelapieza['modificaciones'].'.</b></li>
                    <li><i>Material: </i><b>'.$nombrematerial.'.</b></li>
                    <li><i>Submaterial: </i><b>'.$nombresubmat.'.</b></li>
                    <li><i>Material específico: </i><b>'.$nombrematespec.'.</b></li><br/>
                </ol>
                 <table border="1" cellpadding="2" cellspacing="2" align="center">
                 <tr nobr="true">
                  <th colspan="5"><b>Propiedades del material específico '.$nombrematespec.'</b></th>
                 </tr>
                 <tr nobr="true">
                  <td><i>Densidad</i></td>
                  <td><i>Módulo elástico</i></td>
                  <td><i>Elongación porcentual</i></td>
                  <td><i>Tenacidad</i></td>
                  <td><i>Coeficiente de Poisson</i></td>
                 </tr>
                 <tr nobr="true">
                  <td>'.$dens.'</td>
                  <td>'.$modelas.'</td>
                  <td>'.$eporc.'</td>
                  <td>'.$tenac.'</td>
                  <td>'.$cpoisson.'</td>
                 </tr>
                </table>';

        $pdf->writeHTML($desccomponentehtml, true, false, true, false, '');

        $pdf->AddPage();

        //Las imágenes subidas. Hago select de todas las que hay (asumo que al menos subió una.. y luego, si existe, lleno el html y mando al pdf).

        $imagenestitulohtml = '<h3>Imágenes del componente</h3>';

        $pdf->writeHTML($imagenestitulohtml, true, false, true, false, '');

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $datosimagenes = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);

        foreach ($datosimagenes as $key => $dat) {

             if($dat['queimagen']==1)
             {
                $imagenplano = 'Imagen del plano de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==2)
             {
                $imagenplano = 'Imagen de la pieza en buen estado.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==3)
             {
                $imagenplano = 'Imagen del esquema de montaje y funcionamiento de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==8)
             {
                $imagenplano = 'Máquina o mecanismo sobre el cual se encuentra montada la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==9)
             {
                $imagenplano = 'Esquema de montaje y funcionamiento de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }


          }

          $pdf->Output('Caso de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');

    }


    if($paso == 6 || $paso == 7 || $paso == 8 || $paso == 9)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Federico Sarmiento');
        $pdf->SetTitle('Reporte de caso');
        $pdf->SetSubject('Reporte CAFAP');
        $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
     
        //Nombre Usuario
        $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
        $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
     
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
     
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
     
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
     
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
     
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
     
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }   
     
        // ---------------------------------------------------------    
     
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);   
     
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 10, '', true);   
     
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage(); 
     
        //Título y descripción del caso
        $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);
        //<br/>
        $tituloydescripcionhtml = '<h1 align="center">Caso: '.$tituloydesccaso['titulo'].'</h1><br/>
                 <h3>Descripción del caso</h3>
                 <span style="text-align:justify;">'.$tituloydesccaso['descripcion'].'</span>';
                
        $pdf->writeHTML($tituloydescripcionhtml, true, false, true, false, ''); 

        //Todo lo necesario para el bloque de introducción y el bloque de componente
        $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

        //Bloque de introducción
        if($todosobrelapieza['fallo_multiplesoc']==0) $todosobrelapieza['fallo_multiplesoc'] = 'No';
        if($todosobrelapieza['fallo_multiplesoc']==1) $todosobrelapieza['fallo_multiplesoc'] = 'Si';
        if($todosobrelapieza['ttrabajo_tiempo']==0) $todosobrelapieza['ttrabajo_tiempo'] = 'años';
        if($todosobrelapieza['ttrabajo_tiempo']==1) $todosobrelapieza['ttrabajo_tiempo'] = 'meses';
        if($todosobrelapieza['ttrabajo_tiempo']==2) $todosobrelapieza['ttrabajo_tiempo'] = 'días';
        if($todosobrelapieza['ttrabajo_tiempo']==3) $todosobrelapieza['ttrabajo_tiempo'] = 'horas';
        if($todosobrelapieza['vutil_tiempo']==0) $todosobrelapieza['vutil_tiempo'] = 'años';
        if($todosobrelapieza['vutil_tiempo']==1) $todosobrelapieza['vutil_tiempo'] = 'meses';
        if($todosobrelapieza['vutil_tiempo']==2) $todosobrelapieza['vutil_tiempo'] = 'dias';
        if($todosobrelapieza['vutil_tiempo']==3) $todosobrelapieza['vutil_tiempo'] = 'horas';
        if($todosobrelapieza['fase_ciclovida']==0) $todosobrelapieza['fase_ciclovida'] = 'Mantenimiento';
        if($todosobrelapieza['fase_ciclovida']==1) $todosobrelapieza['fase_ciclovida'] = 'Servicio';
        if($todosobrelapieza['fase_ciclovida']==2) $todosobrelapieza['fase_ciclovida'] = 'Periodo de prueba';
        if($todosobrelapieza['fase_ciclovida']==3) $todosobrelapieza['fase_ciclovida'] = 'Prototipo';


        $introhtml = '<br/><h3>BLOQUE 1: Introducción al caso</h3>
                <ol>                
                    <li><i>¿El mismo modelo ha fallado en múltipes ocasiones? </i><b>'.$todosobrelapieza['fallo_multiplesoc'].'.</b></li>
                    <li><i>Tiempo de trabajo que estuvo sometida la pieza antes de la rotura:</i>
                           <b>'.$todosobrelapieza['ttrabajocant'].' '.$todosobrelapieza['ttrabajo_tiempo'].'.</b></li>
                    <li><i>Vida útil de la pieza (según fabricante), o vida esperada:</i>
                    <b>'.$todosobrelapieza['vutil_cantidad'].' '.$todosobrelapieza['vutil_tiempo'].'.</li>
                    <li><i>Faso del ciclo de vida en donde se produjo la falla: </i><b>'.$todosobrelapieza['fase_ciclovida'].'.</b></li>   
                </ol>';

        $pdf->writeHTML($introhtml, true, false, true, false, '');

        //Bloque de descripción del componente
        if($todosobrelapieza['montadabien']==0) $todosobrelapieza['montadabien'] = 'No';
        if($todosobrelapieza['montadabien']==1) $todosobrelapieza['montadabien'] = 'Si';
        if($todosobrelapieza['tipocargas']==0) $todosobrelapieza['tipocargas'] = 'Fuerza';
        if($todosobrelapieza['tipocargas']==1) $todosobrelapieza['tipocargas'] = 'Presión';
        if($todosobrelapieza['tipocargas']==2) $todosobrelapieza['tipocargas'] = 'Gravedad';
        if($todosobrelapieza['tipocargas']==3) $todosobrelapieza['tipocargas'] = 'Torsión';
        if($todosobrelapieza['tipocargas']==4) $todosobrelapieza['tipocargas'] = 'Fricción';
        if($todosobrelapieza['tipocargas']==5) $todosobrelapieza['tipocargas'] = 'Salto térmico';
        if($todosobrelapieza['umedidacargas']==0) $todosobrelapieza['umedidacargas'] = 'Newton';
        if($todosobrelapieza['umedidacargas']==1) $todosobrelapieza['umedidacargas'] = 'Kilogramos';
        if($todosobrelapieza['umedidacargas']==2) $todosobrelapieza['umedidacargas'] = 'Libras';
        if($todosobrelapieza['tiposujeciones']==0) $todosobrelapieza['tiposujeciones'] = 'Fuerza';
        if($todosobrelapieza['tiposujeciones']==1) $todosobrelapieza['tiposujeciones'] = 'Presión';
        if($todosobrelapieza['tiposujeciones']==2) $todosobrelapieza['tiposujeciones'] = 'Gravedad';
        if($todosobrelapieza['tiposujeciones']==3) $todosobrelapieza['tiposujeciones'] = 'Torsión';
        if($todosobrelapieza['tiposujeciones']==4) $todosobrelapieza['tiposujeciones'] = 'Fricción';
        if($todosobrelapieza['tiposujeciones']==5) $todosobrelapieza['tiposujeciones'] = 'Salto térmico';
        if($todosobrelapieza['condtermicas']==0) $todosobrelapieza['condtermicas'] = 'Temperatura ambiental';
        if($todosobrelapieza['condtermicas']==1) $todosobrelapieza['condtermicas'] = 'Temperatura de contacto';
        if($todosobrelapieza['condtermicas']==2) $todosobrelapieza['condtermicas'] = 'Salto térmico';
        if($todosobrelapieza['utempcondtermicas']==0) $todosobrelapieza['utempcondtermicas'] = 'Celcius';
        if($todosobrelapieza['utempcondtermicas']==1) $todosobrelapieza['utempcondtermicas'] = 'Fahrenheit';
        if($todosobrelapieza['utempcondtermicas']==2) $todosobrelapieza['utempcondtermicas'] = 'Kelvin';
        if($todosobrelapieza['utempcondtermicas']==3) $todosobrelapieza['utempcondtermicas'] = 'Rankine';
        if($todosobrelapieza['tipopresiones']==0) $todosobrelapieza['tipopresiones'] = 'Unidireccional';
        if($todosobrelapieza['tipopresiones']==1) $todosobrelapieza['tipopresiones'] = 'Bidireccional';
        if($todosobrelapieza['tipopresiones']==2) $todosobrelapieza['tipopresiones'] = 'Tridireccional';
        if($todosobrelapieza['distribpresiones']==0) $todosobrelapieza['distribpresiones'] = 'Puntual';
        if($todosobrelapieza['distribpresiones']==1) $todosobrelapieza['distribpresiones'] = 'Uniforme';
        if($todosobrelapieza['distribpresiones']==2) $todosobrelapieza['distribpresiones'] = 'Parcial';
        if($todosobrelapieza['distribpresiones']==3) $todosobrelapieza['distribpresiones'] = 'Hidrostática';
        if($todosobrelapieza['umedidapres']==0) $todosobrelapieza['umedidapres'] = 'Kg/cm2';
        if($todosobrelapieza['umedidapres']==1) $todosobrelapieza['umedidapres'] = 'BAR';
        if($todosobrelapieza['umedidapres']==2) $todosobrelapieza['umedidapres'] = 'PSI';
        if($todosobrelapieza['umedidapres']==3) $todosobrelapieza['umedidapres'] = 'Lb/pulgada2';
        if($todosobrelapieza['veloctrab']==0) $todosobrelapieza['veloctrab'] = 'Lineal';
        if($todosobrelapieza['veloctrab']==1) $todosobrelapieza['veloctrab'] = 'Rotativa';
        if($todosobrelapieza['veloctrab']==2) $todosobrelapieza['veloctrab'] = 'Otras';
        if($todosobrelapieza['trayectoria']==0) $todosobrelapieza['trayectoria'] = 'Continua';
        if($todosobrelapieza['trayectoria']==1) $todosobrelapieza['trayectoria'] = 'Por tramos';
        if($todosobrelapieza['trayectoria']==2) $todosobrelapieza['trayectoria'] = 'Alternada';
        if($todosobrelapieza['unidadveloc']==0) $todosobrelapieza['unidadveloc'] = 'M/seg';
        if($todosobrelapieza['unidadveloc']==1) $todosobrelapieza['unidadveloc'] = 'Km/hr';
        if($todosobrelapieza['unidadveloc']==2) $todosobrelapieza['unidadveloc'] = 'Millas/hr';
        if($todosobrelapieza['unidadveloc']==3) $todosobrelapieza['unidadveloc'] = 'Pie/seg';
        if($todosobrelapieza['elemsusp']==0) $todosobrelapieza['elemsusp'] = 'Ninguno';
        if($todosobrelapieza['elemsusp']==1) $todosobrelapieza['elemsusp'] = 'Plomo';
        if($todosobrelapieza['elemsusp']==2) $todosobrelapieza['elemsusp'] = 'Azufre';
        if($todosobrelapieza['elemsusp']==3) $todosobrelapieza['elemsusp'] = 'Carbono';
        if($todosobrelapieza['modifcond']==0) $todosobrelapieza['modifcond'] = 'No';
        if($todosobrelapieza['modifcond']==1) $todosobrelapieza['modifcond'] = 'Si';
        if($todosobrelapieza['modificaciones']==0) $todosobrelapieza['modificaciones'] = 'Ninguna';
        if($todosobrelapieza['modificaciones']==1) $todosobrelapieza['modificaciones'] = 'Cambio de temperatura de trabajo';
        if($todosobrelapieza['modificaciones']==2) $todosobrelapieza['modificaciones'] = 'Cambio de fuerzas';
        if($todosobrelapieza['modificaciones']==3) $todosobrelapieza['modificaciones'] = 'Cambio de velocidades';
        if($todosobrelapieza['modificaciones']==4) $todosobrelapieza['modificaciones'] = 'Cambio de sujeciones';
        if($todosobrelapieza['modificaciones']==5) $todosobrelapieza['modificaciones'] = 'Cambio de lubricación';
        if($todosobrelapieza['modificaciones']==6) $todosobrelapieza['modificaciones'] = 'Cambio de ambiente';

        $nombrematerial = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['material'],'material');
        $nombresubmat = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['submaterial'],'submaterial');
        $nombrematespec = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['especifico'],'material_especifico');

        $propiedadesmatesp = $this->material_model->devolver_propmatesp($todosobrelapieza['especifico']);
        foreach ($propiedadesmatesp as $key => $prop) {
             $dens = $prop['densidad'];
             $modelas = $prop['modulo_elastico'];
             $eporc = $prop['elongacion_porc'];
             $tenac = $prop['tenacidad'];
             $cpoisson = $prop['coef_poisson']; 
          }


        $desccomponentehtml = '<br/><h3>BLOQUE 2: Descripción del componente</h3>
                <ol>                
                    <li><i>Nombre genérico de la pieza o el conjunto que presenta la falla: </i><b>'.$todosobrelapieza['nombregen'].'.</b></li>
                    <li><i>Nombre o código interno de la pieza o el conjunto: </i><b>'.$todosobrelapieza['codinterno'].'.</b></li>
                    <li><i>Cantidad de piezas que presentan la falla: </i><b>'.$todosobrelapieza['cantidadfalladas'].'.</b></li>
                    <li><i>Uso de la pieza (uso específico del componente, o forma de uso habitual): </i><b>'.$todosobrelapieza['usopieza'].'.</b></li>
                    <li><i>¿La pieza fue montada y utilizada siguiendo las especificaciones técnicas recomendadas por el fabricante 
                           o normas relativas al componente? </i><b>'.$todosobrelapieza['montadabien'].'.</b></li>   
                    <li><i>Condiciones de trabajo de la pieza: </i><b>'.$todosobrelapieza['descdetallada'].'.</b></li>
                    <li><i>Tipo de carga: </i><b>'.$todosobrelapieza['tipocargas'].', con un valor de '.$todosobrelapieza['cantcargas'].'
                         '.$todosobrelapieza['umedidacargas'].'.</b></li>
                    <li><i>Tipo de sujeciones: </i><b>'.$todosobrelapieza['tiposujeciones'].'.</b></li>
                    <li><i>Condición térmica: </i><b>'.$todosobrelapieza['condtermicas'].', con un valor de '.$todosobrelapieza['cantidadtermica'].'
                         '.$todosobrelapieza['utempcondtermicas'].'.</b></li>
                    <li><i>Presión: </i><b>'.$todosobrelapieza['tipopresiones'].', distribución '.$todosobrelapieza['distribpresiones'].'
                         y un valor de '.$todosobrelapieza['valpresion'].' '.$todosobrelapieza['umedidapres'].'.</b></li>
                    <li><i>Velocidad de trabajo: </i><b>'.$todosobrelapieza['veloctrab'].', trayectoria '.$todosobrelapieza['trayectoria'].'
                         y un valor de '.$todosobrelapieza['valveloc'].' '.$todosobrelapieza['unidadveloc'].'.</b></li>
                    <li><i>Elementos en suspensión: </i><b>'.$todosobrelapieza['elemsusp'].', con un valor de '.$todosobrelapieza['valsusp'].
                         '.</b></li>
                    <li><i>¿Hubieron modificaciones en las condiciones de trabajo? </i><b>'.$todosobrelapieza['modifcond'].'.</b></li>
                    <li><i>¿Qué modificaciones hubieron? </i><b>'.$todosobrelapieza['modificaciones'].'.</b></li>
                    <li><i>Material: </i><b>'.$nombrematerial.'.</b></li>
                    <li><i>Submaterial: </i><b>'.$nombresubmat.'.</b></li>
                    <li><i>Material específico: </i><b>'.$nombrematespec.'.</b></li><br/>
                </ol>
                 <table border="1" cellpadding="2" cellspacing="2" align="center">
                 <tr nobr="true">
                  <th colspan="5"><b>Propiedades del material específico '.$nombrematespec.'</b></th>
                 </tr>
                 <tr nobr="true">
                  <td><i>Densidad</i></td>
                  <td><i>Módulo elástico</i></td>
                  <td><i>Elongación porcentual</i></td>
                  <td><i>Tenacidad</i></td>
                  <td><i>Coeficiente de Poisson</i></td>
                 </tr>
                 <tr nobr="true">
                  <td>'.$dens.'</td>
                  <td>'.$modelas.'</td>
                  <td>'.$eporc.'</td>
                  <td>'.$tenac.'</td>
                  <td>'.$cpoisson.'</td>
                 </tr>
                </table>';

        $pdf->writeHTML($desccomponentehtml, true, false, true, false, '');

        $pdf->AddPage();

        //Las imágenes subidas. Hago select de todas las que hay (asumo que al menos subió una.. y luego, si existe, lleno el html y mando al pdf).

        $imagenestitulohtml = '<h3>Imágenes del componente</h3>';

        $pdf->writeHTML($imagenestitulohtml, true, false, true, false, '');

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $datosimagenes = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);

        foreach ($datosimagenes as $key => $dat) {

             if($dat['queimagen']==1)
             {
                $imagenplano = 'Imagen del plano de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==2)
             {
                $imagenplano = 'Imagen de la pieza en buen estado.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==3)
             {
                $imagenplano = 'Imagen del esquema de montaje y funcionamiento de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==8)
             {
                $imagenplano = 'Máquina o mecanismo sobre el cual se encuentra montada la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==9)
             {
                $imagenplano = 'Esquema de montaje y funcionamiento de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }


          }

          $pdf->AddPage();

         //Procesos de fabricación

        $procesostitulohtml = '<h3>BLOQUE 3: Procesos de fabricacón aplicados a la pieza</h3><br/>';

        $pdf->writeHTML($procesostitulohtml, true, false, true, false, '');

        $cantidadprocesos = $this->piezas_model->devolver_cantidadprocesosparareporte($idcaso);
        $cantidadprocesos = $cantidadprocesos - 1;
        $nombresprocesos = $this->casos_model->devolver_procesos($idcaso);
        $nombressubtipos = $this->casos_model->devolver_subtipos($idcaso);
        $numerosprocesos = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);


        for($i=0; $i<$cantidadprocesos; $i++)
        {
            $datosproveedor = $this->piezas_model->devolver_todosobreelproveedor($idcaso,$numerosprocesos[$i]);

            $proveedorhtml = '<h3>Proceso '.($i+1).': '.$nombresprocesos[$i].'. Subtipo: '.$nombressubtipos[$i].'</h3>
                 <table border="1" cellpadding="2" cellspacing="2" align="center">
                     <tr nobr="true">
                         <th colspan="5"><b>Datos del proveedor</b></th>
                     </tr>
                     <tr nobr="true">
                         <td><i>Empresa</i></td>
                         <td><i>Responsable</i></td>
                         <td><i>Correo electrónico</i></td>
                         <td><i>Teléfono</i></td>
                         <td><i>Dirección</i></td>
                     </tr>
                     <tr nobr="true">
                         <td>'.$datosproveedor['empresa'].'</td>
                         <td>'.$datosproveedor['responsable'].'</td>
                         <td>'.$datosproveedor['correoelectronico'].'</td>
                         <td>'.$datosproveedor['telefono'].'</td>
                         <td>'.$datosproveedor['direccion'].'</td>
                     </tr>
                 </table><br/>';

            $pdf->writeHTML($proveedorhtml, true, false, true, false, '');

            //Parametros generales
            $cantidadparamsgenerales = $this->piezas_model->devolver_cantidadprocesosgenerales($idcaso,$numerosprocesos[$i]);

            $cantidadderows = (float)$cantidadparamsgenerales/5;
            $cantidadderowsentero = intval($cantidadparamsgenerales/5);
            if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

            $paramgeneraleshtml1 = '
                  <table border="1" cellpadding="2" cellspacing="2" align="center">
                     <tr nobr="true">
                         <th colspan="5"><b>Parámetros generales del proceso. Cantidad: '.$cantidadparamsgenerales.'</b></th>
                     </tr>';

            $paramgeneraleshtml2 = '';

            //Devuelvo todos los nombres de los parametros generales para mostrar en el proceso, y voy a necesitar el id del proceso en
            //cuestion para consultar en la tabla atributos.
            $nombresparametrosgen = $this->casos_model->devolver_nombresparametrosgen($idcaso,$numerosprocesos[$i]);
            $idprocesoinvolucrado = $this->casos_model->devolver_idprocesopornombre($nombresprocesos[$i]);
            $valoresparametrosgen = $this->casos_model->devolver_valoresparametrosgen($idcaso,$numerosprocesos[$i]);

            $numerocolumnas = 5;
            $atributosrestantes = $cantidadparamsgenerales;
            $indiceatributoamostrar = 0;

            for($j=0; $j<$cantidadderows; $j++)
            {
                $paramgeneraleshtml2 = $paramgeneraleshtml2.'<tr nobr="true">';

                for($k=0; $k<$numerocolumnas;$k++)
                {
                    $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosgen[$indiceatributoamostrar],$idprocesoinvolucrado);

                    $paramgeneraleshtml2 = $paramgeneraleshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                    //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                    if($datosatributo['tipo_campo'] == "1")
                    {
                        if($valoresparametrosgen[$indiceatributoamostrar]== "0") $valoresparametrosgen[$indiceatributoamostrar] = "No";
                        if($valoresparametrosgen[$indiceatributoamostrar]== "1") $valoresparametrosgen[$indiceatributoamostrar] = "Si";   
                    }

                    if($datosatributo['tipo_campo'] == "2")
                    {
                        $valoresparametrosgen[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosgen[$indiceatributoamostrar]);
                    }               

                    $paramgeneraleshtml2 = $paramgeneraleshtml2.$valoresparametrosgen[$indiceatributoamostrar];


                    $paramgeneraleshtml2 = $paramgeneraleshtml2.'</b></i></td>';

                    if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                    $indiceatributoamostrar = $indiceatributoamostrar + 1;
                }
                
                $paramgeneraleshtml2 = $paramgeneraleshtml2.'</tr>';

                $atributosrestantes = $atributosrestantes-5;
                
            }

            $paramgeneraleshtml2 = $paramgeneraleshtml2.'</table><br/>';

            $pdf->writeHTML($paramgeneraleshtml1.$paramgeneraleshtml2, true, false, true, false, '');


            //Parametros especificos

            if($nombressubtipos[$i] != "Ninguno")
            {
                $cantidadparamsespecificos = $this->piezas_model->devolver_cantidadprocesosespecificos($idcaso,$numerosprocesos[$i]);

                $cantidadderows = (float)$cantidadparamsespecificos/5;
                $cantidadderowsentero = intval($cantidadparamsespecificos/5);
                if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

                $paramespecificoshtml1 = '
                      <table border="1" cellpadding="2" cellspacing="2" align="center">
                         <tr nobr="true">
                             <th colspan="5"><b>Parámetros específicos del proceso. Cantidad: '.$cantidadparamsespecificos.'</b></th>
                         </tr>';

                $paramespecificoshtml2 = '';

                //Devuelvo todos los nombres de los parametros especificos para mostrar en el proceso, y voy a necesitar el id del proceso en
                //cuestion para consultar en la tabla atributos.
                $nombresparametrosesp = $this->casos_model->devolver_nombresparametrosesp($idcaso,$numerosprocesos[$i]);
                $idprocesoinvolucrado = $this->casos_model->devolver_idprocesopornombre($nombresprocesos[$i]);
                $valoresparametrosesp = $this->casos_model->devolver_valoresparametrosesp($idcaso,$numerosprocesos[$i]);

                $numerocolumnas = 5;
                $atributosrestantes = $cantidadparamsespecificos;
                $indiceatributoamostrar = 0;

                for($j=0; $j<$cantidadderows; $j++)
                {
                    $paramespecificoshtml2 = $paramespecificoshtml2.'<tr nobr="true">';

                    for($k=0; $k<$numerocolumnas;$k++)
                    {
                        $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosesp[$indiceatributoamostrar],$idprocesoinvolucrado);

                        $paramespecificoshtml2 = $paramespecificoshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                        //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                        if($datosatributo['tipo_campo'] == "1")
                        {
                            if($valoresparametrosesp[$indiceatributoamostrar]== "0") $valoresparametrosesp[$indiceatributoamostrar] = "No";
                            if($valoresparametrosesp[$indiceatributoamostrar]== "1") $valoresparametrosesp[$indiceatributoamostrar] = "Si";   
                        }

                        if($datosatributo['tipo_campo'] == "2")
                        {
                            $valoresparametrosesp[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosesp[$indiceatributoamostrar]);
                        }               

                        $paramespecificoshtml2 = $paramespecificoshtml2.$valoresparametrosesp[$indiceatributoamostrar];


                        $paramespecificoshtml2 = $paramespecificoshtml2.'</b></i></td>';

                        if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                        $indiceatributoamostrar = $indiceatributoamostrar + 1;
                    }
                    
                    $paramespecificoshtml2 = $paramespecificoshtml2.'</tr>';

                    $atributosrestantes = $atributosrestantes-5;
                    
                }

                $paramespecificoshtml2 = $paramespecificoshtml2.'</table><br/>';

                $pdf->writeHTML($paramespecificoshtml1.$paramespecificoshtml2, true, false, true, false, '');

            }

            

        }

          $pdf->Output('Caso de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');

    }


    if($paso == 10 || $paso == 11 || $paso == 12 || $paso == 13)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Federico Sarmiento');
        $pdf->SetTitle('Reporte de caso');
        $pdf->SetSubject('Reporte CAFAP');
        $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
     
        //Nombre Usuario
        $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
        $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
     
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
     
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
     
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
     
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
     
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
     
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }   
     
        // ---------------------------------------------------------    
     
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);   
     
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 10, '', true);   
     
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage(); 
     
        //Título y descripción del caso
        $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);
        //<br/>
        $tituloydescripcionhtml = '<h1 align="center">Caso: '.$tituloydesccaso['titulo'].'</h1><br/>
                 <h3>Descripción del caso</h3>
                 <span style="text-align:justify;">'.$tituloydesccaso['descripcion'].'</span>';
                
        $pdf->writeHTML($tituloydescripcionhtml, true, false, true, false, ''); 

        //Todo lo necesario para el bloque de introducción y el bloque de componente
        $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

        //Bloque de introducción
        if($todosobrelapieza['fallo_multiplesoc']==0) $todosobrelapieza['fallo_multiplesoc'] = 'No';
        if($todosobrelapieza['fallo_multiplesoc']==1) $todosobrelapieza['fallo_multiplesoc'] = 'Si';
        if($todosobrelapieza['ttrabajo_tiempo']==0) $todosobrelapieza['ttrabajo_tiempo'] = 'años';
        if($todosobrelapieza['ttrabajo_tiempo']==1) $todosobrelapieza['ttrabajo_tiempo'] = 'meses';
        if($todosobrelapieza['ttrabajo_tiempo']==2) $todosobrelapieza['ttrabajo_tiempo'] = 'días';
        if($todosobrelapieza['ttrabajo_tiempo']==3) $todosobrelapieza['ttrabajo_tiempo'] = 'horas';
        if($todosobrelapieza['vutil_tiempo']==0) $todosobrelapieza['vutil_tiempo'] = 'años';
        if($todosobrelapieza['vutil_tiempo']==1) $todosobrelapieza['vutil_tiempo'] = 'meses';
        if($todosobrelapieza['vutil_tiempo']==2) $todosobrelapieza['vutil_tiempo'] = 'dias';
        if($todosobrelapieza['vutil_tiempo']==3) $todosobrelapieza['vutil_tiempo'] = 'horas';
        if($todosobrelapieza['fase_ciclovida']==0) $todosobrelapieza['fase_ciclovida'] = 'Mantenimiento';
        if($todosobrelapieza['fase_ciclovida']==1) $todosobrelapieza['fase_ciclovida'] = 'Servicio';
        if($todosobrelapieza['fase_ciclovida']==2) $todosobrelapieza['fase_ciclovida'] = 'Periodo de prueba';
        if($todosobrelapieza['fase_ciclovida']==3) $todosobrelapieza['fase_ciclovida'] = 'Prototipo';


        $introhtml = '<br/><h3>BLOQUE 1: Introducción al caso</h3>
                <ol>                
                    <li><i>¿El mismo modelo ha fallado en múltipes ocasiones? </i><b>'.$todosobrelapieza['fallo_multiplesoc'].'.</b></li>
                    <li><i>Tiempo de trabajo que estuvo sometida la pieza antes de la rotura:</i>
                           <b>'.$todosobrelapieza['ttrabajocant'].' '.$todosobrelapieza['ttrabajo_tiempo'].'.</b></li>
                    <li><i>Vida útil de la pieza (según fabricante), o vida esperada:</i>
                    <b>'.$todosobrelapieza['vutil_cantidad'].' '.$todosobrelapieza['vutil_tiempo'].'.</li>
                    <li><i>Faso del ciclo de vida en donde se produjo la falla: </i><b>'.$todosobrelapieza['fase_ciclovida'].'.</b></li>   
                </ol>';

        $pdf->writeHTML($introhtml, true, false, true, false, '');

        //Bloque de descripción del componente
        if($todosobrelapieza['montadabien']==0) $todosobrelapieza['montadabien'] = 'No';
        if($todosobrelapieza['montadabien']==1) $todosobrelapieza['montadabien'] = 'Si';
        if($todosobrelapieza['tipocargas']==0) $todosobrelapieza['tipocargas'] = 'Fuerza';
        if($todosobrelapieza['tipocargas']==1) $todosobrelapieza['tipocargas'] = 'Presión';
        if($todosobrelapieza['tipocargas']==2) $todosobrelapieza['tipocargas'] = 'Gravedad';
        if($todosobrelapieza['tipocargas']==3) $todosobrelapieza['tipocargas'] = 'Torsión';
        if($todosobrelapieza['tipocargas']==4) $todosobrelapieza['tipocargas'] = 'Fricción';
        if($todosobrelapieza['tipocargas']==5) $todosobrelapieza['tipocargas'] = 'Salto térmico';
        if($todosobrelapieza['umedidacargas']==0) $todosobrelapieza['umedidacargas'] = 'Newton';
        if($todosobrelapieza['umedidacargas']==1) $todosobrelapieza['umedidacargas'] = 'Kilogramos';
        if($todosobrelapieza['umedidacargas']==2) $todosobrelapieza['umedidacargas'] = 'Libras';
        if($todosobrelapieza['tiposujeciones']==0) $todosobrelapieza['tiposujeciones'] = 'Fuerza';
        if($todosobrelapieza['tiposujeciones']==1) $todosobrelapieza['tiposujeciones'] = 'Presión';
        if($todosobrelapieza['tiposujeciones']==2) $todosobrelapieza['tiposujeciones'] = 'Gravedad';
        if($todosobrelapieza['tiposujeciones']==3) $todosobrelapieza['tiposujeciones'] = 'Torsión';
        if($todosobrelapieza['tiposujeciones']==4) $todosobrelapieza['tiposujeciones'] = 'Fricción';
        if($todosobrelapieza['tiposujeciones']==5) $todosobrelapieza['tiposujeciones'] = 'Salto térmico';
        if($todosobrelapieza['condtermicas']==0) $todosobrelapieza['condtermicas'] = 'Temperatura ambiental';
        if($todosobrelapieza['condtermicas']==1) $todosobrelapieza['condtermicas'] = 'Temperatura de contacto';
        if($todosobrelapieza['condtermicas']==2) $todosobrelapieza['condtermicas'] = 'Salto térmico';
        if($todosobrelapieza['utempcondtermicas']==0) $todosobrelapieza['utempcondtermicas'] = 'Celcius';
        if($todosobrelapieza['utempcondtermicas']==1) $todosobrelapieza['utempcondtermicas'] = 'Fahrenheit';
        if($todosobrelapieza['utempcondtermicas']==2) $todosobrelapieza['utempcondtermicas'] = 'Kelvin';
        if($todosobrelapieza['utempcondtermicas']==3) $todosobrelapieza['utempcondtermicas'] = 'Rankine';
        if($todosobrelapieza['tipopresiones']==0) $todosobrelapieza['tipopresiones'] = 'Unidireccional';
        if($todosobrelapieza['tipopresiones']==1) $todosobrelapieza['tipopresiones'] = 'Bidireccional';
        if($todosobrelapieza['tipopresiones']==2) $todosobrelapieza['tipopresiones'] = 'Tridireccional';
        if($todosobrelapieza['distribpresiones']==0) $todosobrelapieza['distribpresiones'] = 'Puntual';
        if($todosobrelapieza['distribpresiones']==1) $todosobrelapieza['distribpresiones'] = 'Uniforme';
        if($todosobrelapieza['distribpresiones']==2) $todosobrelapieza['distribpresiones'] = 'Parcial';
        if($todosobrelapieza['distribpresiones']==3) $todosobrelapieza['distribpresiones'] = 'Hidrostática';
        if($todosobrelapieza['umedidapres']==0) $todosobrelapieza['umedidapres'] = 'Kg/cm2';
        if($todosobrelapieza['umedidapres']==1) $todosobrelapieza['umedidapres'] = 'BAR';
        if($todosobrelapieza['umedidapres']==2) $todosobrelapieza['umedidapres'] = 'PSI';
        if($todosobrelapieza['umedidapres']==3) $todosobrelapieza['umedidapres'] = 'Lb/pulgada2';
        if($todosobrelapieza['veloctrab']==0) $todosobrelapieza['veloctrab'] = 'Lineal';
        if($todosobrelapieza['veloctrab']==1) $todosobrelapieza['veloctrab'] = 'Rotativa';
        if($todosobrelapieza['veloctrab']==2) $todosobrelapieza['veloctrab'] = 'Otras';
        if($todosobrelapieza['trayectoria']==0) $todosobrelapieza['trayectoria'] = 'Continua';
        if($todosobrelapieza['trayectoria']==1) $todosobrelapieza['trayectoria'] = 'Por tramos';
        if($todosobrelapieza['trayectoria']==2) $todosobrelapieza['trayectoria'] = 'Alternada';
        if($todosobrelapieza['unidadveloc']==0) $todosobrelapieza['unidadveloc'] = 'M/seg';
        if($todosobrelapieza['unidadveloc']==1) $todosobrelapieza['unidadveloc'] = 'Km/hr';
        if($todosobrelapieza['unidadveloc']==2) $todosobrelapieza['unidadveloc'] = 'Millas/hr';
        if($todosobrelapieza['unidadveloc']==3) $todosobrelapieza['unidadveloc'] = 'Pie/seg';
        if($todosobrelapieza['elemsusp']==0) $todosobrelapieza['elemsusp'] = 'Ninguno';
        if($todosobrelapieza['elemsusp']==1) $todosobrelapieza['elemsusp'] = 'Plomo';
        if($todosobrelapieza['elemsusp']==2) $todosobrelapieza['elemsusp'] = 'Azufre';
        if($todosobrelapieza['elemsusp']==3) $todosobrelapieza['elemsusp'] = 'Carbono';
        if($todosobrelapieza['modifcond']==0) $todosobrelapieza['modifcond'] = 'No';
        if($todosobrelapieza['modifcond']==1) $todosobrelapieza['modifcond'] = 'Si';
        if($todosobrelapieza['modificaciones']==0) $todosobrelapieza['modificaciones'] = 'Ninguna';
        if($todosobrelapieza['modificaciones']==1) $todosobrelapieza['modificaciones'] = 'Cambio de temperatura de trabajo';
        if($todosobrelapieza['modificaciones']==2) $todosobrelapieza['modificaciones'] = 'Cambio de fuerzas';
        if($todosobrelapieza['modificaciones']==3) $todosobrelapieza['modificaciones'] = 'Cambio de velocidades';
        if($todosobrelapieza['modificaciones']==4) $todosobrelapieza['modificaciones'] = 'Cambio de sujeciones';
        if($todosobrelapieza['modificaciones']==5) $todosobrelapieza['modificaciones'] = 'Cambio de lubricación';
        if($todosobrelapieza['modificaciones']==6) $todosobrelapieza['modificaciones'] = 'Cambio de ambiente';

        $nombrematerial = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['material'],'material');
        $nombresubmat = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['submaterial'],'submaterial');
        $nombrematespec = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['especifico'],'material_especifico');

        $propiedadesmatesp = $this->material_model->devolver_propmatesp($todosobrelapieza['especifico']);
        foreach ($propiedadesmatesp as $key => $prop) {
             $dens = $prop['densidad'];
             $modelas = $prop['modulo_elastico'];
             $eporc = $prop['elongacion_porc'];
             $tenac = $prop['tenacidad'];
             $cpoisson = $prop['coef_poisson']; 
          }


        $desccomponentehtml = '<br/><h3>BLOQUE 2: Descripción del componente</h3>
                <ol>                
                    <li><i>Nombre genérico de la pieza o el conjunto que presenta la falla: </i><b>'.$todosobrelapieza['nombregen'].'.</b></li>
                    <li><i>Nombre o código interno de la pieza o el conjunto: </i><b>'.$todosobrelapieza['codinterno'].'.</b></li>
                    <li><i>Cantidad de piezas que presentan la falla: </i><b>'.$todosobrelapieza['cantidadfalladas'].'.</b></li>
                    <li><i>Uso de la pieza (uso específico del componente, o forma de uso habitual): </i><b>'.$todosobrelapieza['usopieza'].'.</b></li>
                    <li><i>¿La pieza fue montada y utilizada siguiendo las especificaciones técnicas recomendadas por el fabricante 
                           o normas relativas al componente? </i><b>'.$todosobrelapieza['montadabien'].'.</b></li>   
                    <li><i>Condiciones de trabajo de la pieza: </i><b>'.$todosobrelapieza['descdetallada'].'.</b></li>
                    <li><i>Tipo de carga: </i><b>'.$todosobrelapieza['tipocargas'].', con un valor de '.$todosobrelapieza['cantcargas'].'
                         '.$todosobrelapieza['umedidacargas'].'.</b></li>
                    <li><i>Tipo de sujeciones: </i><b>'.$todosobrelapieza['tiposujeciones'].'.</b></li>
                    <li><i>Condición térmica: </i><b>'.$todosobrelapieza['condtermicas'].', con un valor de '.$todosobrelapieza['cantidadtermica'].'
                         '.$todosobrelapieza['utempcondtermicas'].'.</b></li>
                    <li><i>Presión: </i><b>'.$todosobrelapieza['tipopresiones'].', distribución '.$todosobrelapieza['distribpresiones'].'
                         y un valor de '.$todosobrelapieza['valpresion'].' '.$todosobrelapieza['umedidapres'].'.</b></li>
                    <li><i>Velocidad de trabajo: </i><b>'.$todosobrelapieza['veloctrab'].', trayectoria '.$todosobrelapieza['trayectoria'].'
                         y un valor de '.$todosobrelapieza['valveloc'].' '.$todosobrelapieza['unidadveloc'].'.</b></li>
                    <li><i>Elementos en suspensión: </i><b>'.$todosobrelapieza['elemsusp'].', con un valor de '.$todosobrelapieza['valsusp'].
                         '.</b></li>
                    <li><i>¿Hubieron modificaciones en las condiciones de trabajo? </i><b>'.$todosobrelapieza['modifcond'].'.</b></li>
                    <li><i>¿Qué modificaciones hubieron? </i><b>'.$todosobrelapieza['modificaciones'].'.</b></li>
                    <li><i>Material: </i><b>'.$nombrematerial.'.</b></li>
                    <li><i>Submaterial: </i><b>'.$nombresubmat.'.</b></li>
                    <li><i>Material específico: </i><b>'.$nombrematespec.'.</b></li><br/>
                </ol>
                 <table border="1" cellpadding="2" cellspacing="2" align="center">
                 <tr nobr="true">
                  <th colspan="5"><b>Propiedades del material específico '.$nombrematespec.'</b></th>
                 </tr>
                 <tr nobr="true">
                  <td><i>Densidad</i></td>
                  <td><i>Módulo elástico</i></td>
                  <td><i>Elongación porcentual</i></td>
                  <td><i>Tenacidad</i></td>
                  <td><i>Coeficiente de Poisson</i></td>
                 </tr>
                 <tr nobr="true">
                  <td>'.$dens.'</td>
                  <td>'.$modelas.'</td>
                  <td>'.$eporc.'</td>
                  <td>'.$tenac.'</td>
                  <td>'.$cpoisson.'</td>
                 </tr>
                </table>';

        $pdf->writeHTML($desccomponentehtml, true, false, true, false, '');

        $pdf->AddPage();

        //Las imágenes subidas. Hago select de todas las que hay (asumo que al menos subió una.. y luego, si existe, lleno el html y mando al pdf).

        $imagenestitulohtml = '<h3>Imágenes del componente</h3>';

        $pdf->writeHTML($imagenestitulohtml, true, false, true, false, '');

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $datosimagenes = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);

        foreach ($datosimagenes as $key => $dat) {

             if($dat['queimagen']==1)
             {
                $imagenplano = 'Imagen del plano de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==2)
             {
                $imagenplano = 'Imagen de la pieza en buen estado.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==3)
             {
                $imagenplano = 'Imagen del esquema de montaje y funcionamiento de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==8)
             {
                $imagenplano = 'Máquina o mecanismo sobre el cual se encuentra montada la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==9)
             {
                $imagenplano = 'Esquema de montaje y funcionamiento de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }


          }

          $pdf->AddPage();

         //Procesos de fabricación

        $procesostitulohtml = '<h3>BLOQUE 3: Procesos de fabricacón aplicados a la pieza</h3><br/>';

        $pdf->writeHTML($procesostitulohtml, true, false, true, false, '');

        $cantidadprocesos = $this->piezas_model->devolver_cantidadprocesosparareporte($idcaso);
        $cantidadprocesos = $cantidadprocesos - 1;
        $nombresprocesos = $this->casos_model->devolver_procesos($idcaso);
        $nombressubtipos = $this->casos_model->devolver_subtipos($idcaso);
        $numerosprocesos = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);


        for($i=0; $i<$cantidadprocesos; $i++)
        {
            $datosproveedor = $this->piezas_model->devolver_todosobreelproveedor($idcaso,$numerosprocesos[$i]);

            $proveedorhtml = '<h3>Proceso '.($i+1).': '.$nombresprocesos[$i].'. Subtipo: '.$nombressubtipos[$i].'</h3>
                 <table border="1" cellpadding="2" cellspacing="2" align="center">
                     <tr nobr="true">
                         <th colspan="5"><b>Datos del proveedor</b></th>
                     </tr>
                     <tr nobr="true">
                         <td><i>Empresa</i></td>
                         <td><i>Responsable</i></td>
                         <td><i>Correo electrónico</i></td>
                         <td><i>Teléfono</i></td>
                         <td><i>Dirección</i></td>
                     </tr>
                     <tr nobr="true">
                         <td>'.$datosproveedor['empresa'].'</td>
                         <td>'.$datosproveedor['responsable'].'</td>
                         <td>'.$datosproveedor['correoelectronico'].'</td>
                         <td>'.$datosproveedor['telefono'].'</td>
                         <td>'.$datosproveedor['direccion'].'</td>
                     </tr>
                 </table><br/>';

            $pdf->writeHTML($proveedorhtml, true, false, true, false, '');

            //Parametros generales
            $cantidadparamsgenerales = $this->piezas_model->devolver_cantidadprocesosgenerales($idcaso,$numerosprocesos[$i]);

            $cantidadderows = (float)$cantidadparamsgenerales/5;
            $cantidadderowsentero = intval($cantidadparamsgenerales/5);
            if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

            $paramgeneraleshtml1 = '
                  <table border="1" cellpadding="2" cellspacing="2" align="center">
                     <tr nobr="true">
                         <th colspan="5"><b>Parámetros generales del proceso. Cantidad: '.$cantidadparamsgenerales.'</b></th>
                     </tr>';

            $paramgeneraleshtml2 = '';

            //Devuelvo todos los nombres de los parametros generales para mostrar en el proceso, y voy a necesitar el id del proceso en
            //cuestion para consultar en la tabla atributos.
            $nombresparametrosgen = $this->casos_model->devolver_nombresparametrosgen($idcaso,$numerosprocesos[$i]);
            $idprocesoinvolucrado = $this->casos_model->devolver_idprocesopornombre($nombresprocesos[$i]);
            $valoresparametrosgen = $this->casos_model->devolver_valoresparametrosgen($idcaso,$numerosprocesos[$i]);

            $numerocolumnas = 5;
            $atributosrestantes = $cantidadparamsgenerales;
            $indiceatributoamostrar = 0;

            for($j=0; $j<$cantidadderows; $j++)
            {
                $paramgeneraleshtml2 = $paramgeneraleshtml2.'<tr nobr="true">';

                for($k=0; $k<$numerocolumnas;$k++)
                {
                    $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosgen[$indiceatributoamostrar],$idprocesoinvolucrado);

                    $paramgeneraleshtml2 = $paramgeneraleshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                    //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                    if($datosatributo['tipo_campo'] == "1")
                    {
                        if($valoresparametrosgen[$indiceatributoamostrar]== "0") $valoresparametrosgen[$indiceatributoamostrar] = "No";
                        if($valoresparametrosgen[$indiceatributoamostrar]== "1") $valoresparametrosgen[$indiceatributoamostrar] = "Si";   
                    }

                    if($datosatributo['tipo_campo'] == "2")
                    {
                        $valoresparametrosgen[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosgen[$indiceatributoamostrar]);
                    }               

                    $paramgeneraleshtml2 = $paramgeneraleshtml2.$valoresparametrosgen[$indiceatributoamostrar];


                    $paramgeneraleshtml2 = $paramgeneraleshtml2.'</b></i></td>';

                    if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                    $indiceatributoamostrar = $indiceatributoamostrar + 1;
                }
                
                $paramgeneraleshtml2 = $paramgeneraleshtml2.'</tr>';

                $atributosrestantes = $atributosrestantes-5;
                
            }

            $paramgeneraleshtml2 = $paramgeneraleshtml2.'</table><br/>';

            $pdf->writeHTML($paramgeneraleshtml1.$paramgeneraleshtml2, true, false, true, false, '');


            //Parametros especificos

            if($nombressubtipos[$i] != "Ninguno")
            {
                $cantidadparamsespecificos = $this->piezas_model->devolver_cantidadprocesosespecificos($idcaso,$numerosprocesos[$i]);

                $cantidadderows = (float)$cantidadparamsespecificos/5;
                $cantidadderowsentero = intval($cantidadparamsespecificos/5);
                if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

                $paramespecificoshtml1 = '
                      <table border="1" cellpadding="2" cellspacing="2" align="center">
                         <tr nobr="true">
                             <th colspan="5"><b>Parámetros específicos del proceso. Cantidad: '.$cantidadparamsespecificos.'</b></th>
                         </tr>';

                $paramespecificoshtml2 = '';

                //Devuelvo todos los nombres de los parametros especificos para mostrar en el proceso, y voy a necesitar el id del proceso en
                //cuestion para consultar en la tabla atributos.
                $nombresparametrosesp = $this->casos_model->devolver_nombresparametrosesp($idcaso,$numerosprocesos[$i]);
                $idprocesoinvolucrado = $this->casos_model->devolver_idprocesopornombre($nombresprocesos[$i]);
                $valoresparametrosesp = $this->casos_model->devolver_valoresparametrosesp($idcaso,$numerosprocesos[$i]);

                $numerocolumnas = 5;
                $atributosrestantes = $cantidadparamsespecificos;
                $indiceatributoamostrar = 0;

                for($j=0; $j<$cantidadderows; $j++)
                {
                    $paramespecificoshtml2 = $paramespecificoshtml2.'<tr nobr="true">';

                    for($k=0; $k<$numerocolumnas;$k++)
                    {
                        $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosesp[$indiceatributoamostrar],$idprocesoinvolucrado);

                        $paramespecificoshtml2 = $paramespecificoshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                        //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                        if($datosatributo['tipo_campo'] == "1")
                        {
                            if($valoresparametrosesp[$indiceatributoamostrar]== "0") $valoresparametrosesp[$indiceatributoamostrar] = "No";
                            if($valoresparametrosesp[$indiceatributoamostrar]== "1") $valoresparametrosesp[$indiceatributoamostrar] = "Si";   
                        }

                        if($datosatributo['tipo_campo'] == "2")
                        {
                            $valoresparametrosesp[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosesp[$indiceatributoamostrar]);
                        }               

                        $paramespecificoshtml2 = $paramespecificoshtml2.$valoresparametrosesp[$indiceatributoamostrar];


                        $paramespecificoshtml2 = $paramespecificoshtml2.'</b></i></td>';

                        if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                        $indiceatributoamostrar = $indiceatributoamostrar + 1;
                    }
                    
                    $paramespecificoshtml2 = $paramespecificoshtml2.'</tr>';

                    $atributosrestantes = $atributosrestantes-5;
                    
                }

                $paramespecificoshtml2 = $paramespecificoshtml2.'</table><br/>';

                $pdf->writeHTML($paramespecificoshtml1.$paramespecificoshtml2, true, false, true, false, '');

            }

            

        }

        $pdf->AddPage();

        //Ensayos

        $cantidadensayos = ($this->piezas_model->devolver_cantidadensayos($idcaso))-1;
        $todosobreensayos = $this->piezas_model->devolver_todosobreensayos($idcaso);

        $cantidadimagenesdeensayo = $this->piezas_model->devolver_cantidadimagenesdeensayos($idpieza);
        $todosobreimgensayos = $this->piezas_model->devolver_todosobreimgdeensayos($idpieza);

        $titulobloqueensayoshtml = '<h3>BLOQUE 4: Ensayos</h3><br/>';
        $pdf->writeHTML($titulobloqueensayoshtml, true, false, true, false, '');

        $inicio = 0;
        $final = 0;

        for($i=0;$i<$cantidadensayos;$i++)
        {
            $ensayoshtml = '';
            $ensayoshtml = '<h3>Ensayo número '.($i+1).': '.$todosobreensayos[$i]['nombre'].'</h3>
                 <span style="text-align:justify;">'.$todosobreensayos[$i]['descripcion'].'</span>';

            if($todosobreensayos[$i]['cant_imagenes']!=0)
            {
                $final = $final + $todosobreensayos[$i]['cant_imagenes'];

                $ensayoshtml = $ensayoshtml.'<br/><br/><b>Imágenes del ensayo.</b>';
                $pdf->writeHTML($ensayoshtml, true, false, true, false, '');

                for($inicio;$inicio<$final;$inicio++)
                {
                    $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgensayos[$inicio]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                    $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                    <br/><br/><br/>';

                    $pdf->writeHTML($espacio, true, false, true, false, '');

                }
                
                $inicio = 0;
                $inicio = $inicio + $final;  

            }
            else $pdf->writeHTML($ensayoshtml, true, false, true, false, '');

        }

        $pdf->AddPage();


        //Macrografía

        $titulobloquemacrografiahtml = '<h3>BLOQUE 5: Macrografía</h3><br/><b>Imágenes de la macrografía.';
        $pdf->writeHTML($titulobloquemacrografiahtml, true, false, true, false, '');

        $cantidadmacrografias = $this->piezas_model->devolver_cantidaddescmacrografia($idcaso);
        $todosobremacrografia = $this->piezas_model->devolver_todosobremacrografia($idcaso);
        $todosobreimgmacrografia = $this->piezas_model->devolver_todosobreimgmacrografia($idpieza);

        if($todosobremacrografia[0]['tipo_fractura']==0) $todosobremacrografia[0]['tipo_fractura'] = 'Frágil';
        if($todosobremacrografia[0]['tipo_fractura']==1) $todosobremacrografia[0]['tipo_fractura'] = 'Dúctil';
        if($todosobremacrografia[0]['tipo_fractura']==2) $todosobremacrografia[0]['tipo_fractura'] = 'Mixta';

        $fracturapredom = 'Fractura predominante: '.$todosobremacrografia[0]['tipo_fractura'].'</b><br/>';
        $pdf->writeHTML($fracturapredom, true, false, true, false, '');

        $macrografiahtml = '';

        for($i=0; $i<$cantidadmacrografias;$i++)
        {
            $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgmacrografia[$i]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                    <br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');

            $macrografiahtml = '<b>Descripción: </b>'.$todosobremacrografia[$i]['descripcion'].'<br/>';

            $pdf->writeHTML($macrografiahtml, true, false, true, false, '');
        }


        $pdf->AddPage();


        //Micrografía

        $titulobloquemicrografiahtml = '<h3>BLOQUE 6: Micrografía</h3><br/><b>Imágenes de la micrografía.</b><br/>';
        $pdf->writeHTML($titulobloquemicrografiahtml, true, false, true, false, '');

        $cantidadmicrografias = $this->piezas_model->devolver_cantidaddescmicrografia($idcaso);
        $todosobremicrografia = $this->piezas_model->devolver_todosobremicrografia($idcaso);
        $todosobreimgmicrografia = $this->piezas_model->devolver_todosobreimgmicrografia($idpieza);

        $micrografiahtml = '';

        for($i=0; $i<$cantidadmicrografias;$i++)
        {
            $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgmicrografia[$i]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                    <br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');

            $micrografiahtml = '<b>Descripción: </b>'.$todosobremicrografia[$i]['descripcion'].'<br/>';

            $pdf->writeHTML($micrografiahtml, true, false, true, false, '');
        }

        $pdf->AddPage();


        //Discusión

        $discusion = $this->casos_model->devolver_discusionporidcaso($idcaso);

        $discusionhtml = '<h3>BLOQUE 7: Discusión</h3>
        <span style="text-align:justify;">'.$discusion.'</span><br/><br/><br/>';

        $pdf->writeHTML($discusionhtml, true, false, true, false, '');

        $pdf->Output('Caso de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');

    }



    if($paso == 14)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    
 
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Federico Sarmiento');
        $pdf->SetTitle('Reporte de caso');
        $pdf->SetSubject('Reporte CAFAP');
        $pdf->SetKeywords('PDF, CAFAP, UCC, ingeniería');   
     
        //Nombre Usuario
        $datosusuario = $this->casos_model->devolver_nombreusuarioporidcaso($idcaso);
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.', PDF_HEADER_STRING, array(0,0,0), array(0,0,0));
        $pdf->setFooterData(array(0,64,0), array(0,0,0)); 
     
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
     
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
     
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);    
     
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
     
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  
     
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }   
     
        // ---------------------------------------------------------    
     
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);   
     
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 10, '', true);   
     
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage(); 
     
        //Título y descripción del caso
        $tituloydesccaso = $this->casos_model->devolver_tituloydescporidcaso($idcaso);
        //<br/>
        $tituloydescripcionhtml = '<h1 align="center">Caso: '.$tituloydesccaso['titulo'].'</h1><br/>
                 <h3>Descripción del caso</h3>
                 <span style="text-align:justify;">'.$tituloydesccaso['descripcion'].'</span>';
                
        $pdf->writeHTML($tituloydescripcionhtml, true, false, true, false, ''); 

        //Todo lo necesario para el bloque de introducción y el bloque de componente
        $todosobrelapieza = $this->piezas_model->devolver_todosobrelapieza($idcaso);

        //Bloque de introducción
        if($todosobrelapieza['fallo_multiplesoc']==0) $todosobrelapieza['fallo_multiplesoc'] = 'No';
        if($todosobrelapieza['fallo_multiplesoc']==1) $todosobrelapieza['fallo_multiplesoc'] = 'Si';
        if($todosobrelapieza['ttrabajo_tiempo']==0) $todosobrelapieza['ttrabajo_tiempo'] = 'años';
        if($todosobrelapieza['ttrabajo_tiempo']==1) $todosobrelapieza['ttrabajo_tiempo'] = 'meses';
        if($todosobrelapieza['ttrabajo_tiempo']==2) $todosobrelapieza['ttrabajo_tiempo'] = 'días';
        if($todosobrelapieza['ttrabajo_tiempo']==3) $todosobrelapieza['ttrabajo_tiempo'] = 'horas';
        if($todosobrelapieza['vutil_tiempo']==0) $todosobrelapieza['vutil_tiempo'] = 'años';
        if($todosobrelapieza['vutil_tiempo']==1) $todosobrelapieza['vutil_tiempo'] = 'meses';
        if($todosobrelapieza['vutil_tiempo']==2) $todosobrelapieza['vutil_tiempo'] = 'dias';
        if($todosobrelapieza['vutil_tiempo']==3) $todosobrelapieza['vutil_tiempo'] = 'horas';
        if($todosobrelapieza['fase_ciclovida']==0) $todosobrelapieza['fase_ciclovida'] = 'Mantenimiento';
        if($todosobrelapieza['fase_ciclovida']==1) $todosobrelapieza['fase_ciclovida'] = 'Servicio';
        if($todosobrelapieza['fase_ciclovida']==2) $todosobrelapieza['fase_ciclovida'] = 'Periodo de prueba';
        if($todosobrelapieza['fase_ciclovida']==3) $todosobrelapieza['fase_ciclovida'] = 'Prototipo';


        $introhtml = '<br/><h3>BLOQUE 1: Introducción al caso</h3>
                <ol>                
                    <li><i>¿El mismo modelo ha fallado en múltipes ocasiones? </i><b>'.$todosobrelapieza['fallo_multiplesoc'].'.</b></li>
                    <li><i>Tiempo de trabajo que estuvo sometida la pieza antes de la rotura:</i>
                           <b>'.$todosobrelapieza['ttrabajocant'].' '.$todosobrelapieza['ttrabajo_tiempo'].'.</b></li>
                    <li><i>Vida útil de la pieza (según fabricante), o vida esperada:</i>
                    <b>'.$todosobrelapieza['vutil_cantidad'].' '.$todosobrelapieza['vutil_tiempo'].'.</li>
                    <li><i>Faso del ciclo de vida en donde se produjo la falla: </i><b>'.$todosobrelapieza['fase_ciclovida'].'.</b></li>   
                </ol>';

        $pdf->writeHTML($introhtml, true, false, true, false, '');

        //Bloque de descripción del componente
        if($todosobrelapieza['montadabien']==0) $todosobrelapieza['montadabien'] = 'No';
        if($todosobrelapieza['montadabien']==1) $todosobrelapieza['montadabien'] = 'Si';
        if($todosobrelapieza['tipocargas']==0) $todosobrelapieza['tipocargas'] = 'Fuerza';
        if($todosobrelapieza['tipocargas']==1) $todosobrelapieza['tipocargas'] = 'Presión';
        if($todosobrelapieza['tipocargas']==2) $todosobrelapieza['tipocargas'] = 'Gravedad';
        if($todosobrelapieza['tipocargas']==3) $todosobrelapieza['tipocargas'] = 'Torsión';
        if($todosobrelapieza['tipocargas']==4) $todosobrelapieza['tipocargas'] = 'Fricción';
        if($todosobrelapieza['tipocargas']==5) $todosobrelapieza['tipocargas'] = 'Salto térmico';
        if($todosobrelapieza['umedidacargas']==0) $todosobrelapieza['umedidacargas'] = 'Newton';
        if($todosobrelapieza['umedidacargas']==1) $todosobrelapieza['umedidacargas'] = 'Kilogramos';
        if($todosobrelapieza['umedidacargas']==2) $todosobrelapieza['umedidacargas'] = 'Libras';
        if($todosobrelapieza['tiposujeciones']==0) $todosobrelapieza['tiposujeciones'] = 'Fuerza';
        if($todosobrelapieza['tiposujeciones']==1) $todosobrelapieza['tiposujeciones'] = 'Presión';
        if($todosobrelapieza['tiposujeciones']==2) $todosobrelapieza['tiposujeciones'] = 'Gravedad';
        if($todosobrelapieza['tiposujeciones']==3) $todosobrelapieza['tiposujeciones'] = 'Torsión';
        if($todosobrelapieza['tiposujeciones']==4) $todosobrelapieza['tiposujeciones'] = 'Fricción';
        if($todosobrelapieza['tiposujeciones']==5) $todosobrelapieza['tiposujeciones'] = 'Salto térmico';
        if($todosobrelapieza['condtermicas']==0) $todosobrelapieza['condtermicas'] = 'Temperatura ambiental';
        if($todosobrelapieza['condtermicas']==1) $todosobrelapieza['condtermicas'] = 'Temperatura de contacto';
        if($todosobrelapieza['condtermicas']==2) $todosobrelapieza['condtermicas'] = 'Salto térmico';
        if($todosobrelapieza['utempcondtermicas']==0) $todosobrelapieza['utempcondtermicas'] = 'Celcius';
        if($todosobrelapieza['utempcondtermicas']==1) $todosobrelapieza['utempcondtermicas'] = 'Fahrenheit';
        if($todosobrelapieza['utempcondtermicas']==2) $todosobrelapieza['utempcondtermicas'] = 'Kelvin';
        if($todosobrelapieza['utempcondtermicas']==3) $todosobrelapieza['utempcondtermicas'] = 'Rankine';
        if($todosobrelapieza['tipopresiones']==0) $todosobrelapieza['tipopresiones'] = 'Unidireccional';
        if($todosobrelapieza['tipopresiones']==1) $todosobrelapieza['tipopresiones'] = 'Bidireccional';
        if($todosobrelapieza['tipopresiones']==2) $todosobrelapieza['tipopresiones'] = 'Tridireccional';
        if($todosobrelapieza['distribpresiones']==0) $todosobrelapieza['distribpresiones'] = 'Puntual';
        if($todosobrelapieza['distribpresiones']==1) $todosobrelapieza['distribpresiones'] = 'Uniforme';
        if($todosobrelapieza['distribpresiones']==2) $todosobrelapieza['distribpresiones'] = 'Parcial';
        if($todosobrelapieza['distribpresiones']==3) $todosobrelapieza['distribpresiones'] = 'Hidrostática';
        if($todosobrelapieza['umedidapres']==0) $todosobrelapieza['umedidapres'] = 'Kg/cm2';
        if($todosobrelapieza['umedidapres']==1) $todosobrelapieza['umedidapres'] = 'BAR';
        if($todosobrelapieza['umedidapres']==2) $todosobrelapieza['umedidapres'] = 'PSI';
        if($todosobrelapieza['umedidapres']==3) $todosobrelapieza['umedidapres'] = 'Lb/pulgada2';
        if($todosobrelapieza['veloctrab']==0) $todosobrelapieza['veloctrab'] = 'Lineal';
        if($todosobrelapieza['veloctrab']==1) $todosobrelapieza['veloctrab'] = 'Rotativa';
        if($todosobrelapieza['veloctrab']==2) $todosobrelapieza['veloctrab'] = 'Otras';
        if($todosobrelapieza['trayectoria']==0) $todosobrelapieza['trayectoria'] = 'Continua';
        if($todosobrelapieza['trayectoria']==1) $todosobrelapieza['trayectoria'] = 'Por tramos';
        if($todosobrelapieza['trayectoria']==2) $todosobrelapieza['trayectoria'] = 'Alternada';
        if($todosobrelapieza['unidadveloc']==0) $todosobrelapieza['unidadveloc'] = 'M/seg';
        if($todosobrelapieza['unidadveloc']==1) $todosobrelapieza['unidadveloc'] = 'Km/hr';
        if($todosobrelapieza['unidadveloc']==2) $todosobrelapieza['unidadveloc'] = 'Millas/hr';
        if($todosobrelapieza['unidadveloc']==3) $todosobrelapieza['unidadveloc'] = 'Pie/seg';
        if($todosobrelapieza['elemsusp']==0) $todosobrelapieza['elemsusp'] = 'Ninguno';
        if($todosobrelapieza['elemsusp']==1) $todosobrelapieza['elemsusp'] = 'Plomo';
        if($todosobrelapieza['elemsusp']==2) $todosobrelapieza['elemsusp'] = 'Azufre';
        if($todosobrelapieza['elemsusp']==3) $todosobrelapieza['elemsusp'] = 'Carbono';
        if($todosobrelapieza['modifcond']==0) $todosobrelapieza['modifcond'] = 'No';
        if($todosobrelapieza['modifcond']==1) $todosobrelapieza['modifcond'] = 'Si';
        if($todosobrelapieza['modificaciones']==0) $todosobrelapieza['modificaciones'] = 'Ninguna';
        if($todosobrelapieza['modificaciones']==1) $todosobrelapieza['modificaciones'] = 'Cambio de temperatura de trabajo';
        if($todosobrelapieza['modificaciones']==2) $todosobrelapieza['modificaciones'] = 'Cambio de fuerzas';
        if($todosobrelapieza['modificaciones']==3) $todosobrelapieza['modificaciones'] = 'Cambio de velocidades';
        if($todosobrelapieza['modificaciones']==4) $todosobrelapieza['modificaciones'] = 'Cambio de sujeciones';
        if($todosobrelapieza['modificaciones']==5) $todosobrelapieza['modificaciones'] = 'Cambio de lubricación';
        if($todosobrelapieza['modificaciones']==6) $todosobrelapieza['modificaciones'] = 'Cambio de ambiente';

        $nombrematerial = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['material'],'material');
        $nombresubmat = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['submaterial'],'submaterial');
        $nombrematespec = $this->material_model->devolver_nombrematsubesp($todosobrelapieza['especifico'],'material_especifico');

        $propiedadesmatesp = $this->material_model->devolver_propmatesp($todosobrelapieza['especifico']);
        foreach ($propiedadesmatesp as $key => $prop) {
             $dens = $prop['densidad'];
             $modelas = $prop['modulo_elastico'];
             $eporc = $prop['elongacion_porc'];
             $tenac = $prop['tenacidad'];
             $cpoisson = $prop['coef_poisson']; 
          }


        $desccomponentehtml = '<br/><h3>BLOQUE 2: Descripción del componente</h3>
                <ol>                
                    <li><i>Nombre genérico de la pieza o el conjunto que presenta la falla: </i><b>'.$todosobrelapieza['nombregen'].'.</b></li>
                    <li><i>Nombre o código interno de la pieza o el conjunto: </i><b>'.$todosobrelapieza['codinterno'].'.</b></li>
                    <li><i>Cantidad de piezas que presentan la falla: </i><b>'.$todosobrelapieza['cantidadfalladas'].'.</b></li>
                    <li><i>Uso de la pieza (uso específico del componente, o forma de uso habitual): </i><b>'.$todosobrelapieza['usopieza'].'.</b></li>
                    <li><i>¿La pieza fue montada y utilizada siguiendo las especificaciones técnicas recomendadas por el fabricante 
                           o normas relativas al componente? </i><b>'.$todosobrelapieza['montadabien'].'.</b></li>   
                    <li><i>Condiciones de trabajo de la pieza: </i><b>'.$todosobrelapieza['descdetallada'].'.</b></li>
                    <li><i>Tipo de carga: </i><b>'.$todosobrelapieza['tipocargas'].', con un valor de '.$todosobrelapieza['cantcargas'].'
                         '.$todosobrelapieza['umedidacargas'].'.</b></li>
                    <li><i>Tipo de sujeciones: </i><b>'.$todosobrelapieza['tiposujeciones'].'.</b></li>
                    <li><i>Condición térmica: </i><b>'.$todosobrelapieza['condtermicas'].', con un valor de '.$todosobrelapieza['cantidadtermica'].'
                         '.$todosobrelapieza['utempcondtermicas'].'.</b></li>
                    <li><i>Presión: </i><b>'.$todosobrelapieza['tipopresiones'].', distribución '.$todosobrelapieza['distribpresiones'].'
                         y un valor de '.$todosobrelapieza['valpresion'].' '.$todosobrelapieza['umedidapres'].'.</b></li>
                    <li><i>Velocidad de trabajo: </i><b>'.$todosobrelapieza['veloctrab'].', trayectoria '.$todosobrelapieza['trayectoria'].'
                         y un valor de '.$todosobrelapieza['valveloc'].' '.$todosobrelapieza['unidadveloc'].'.</b></li>
                    <li><i>Elementos en suspensión: </i><b>'.$todosobrelapieza['elemsusp'].', con un valor de '.$todosobrelapieza['valsusp'].
                         '.</b></li>
                    <li><i>¿Hubieron modificaciones en las condiciones de trabajo? </i><b>'.$todosobrelapieza['modifcond'].'.</b></li>
                    <li><i>¿Qué modificaciones hubieron? </i><b>'.$todosobrelapieza['modificaciones'].'.</b></li>
                    <li><i>Material: </i><b>'.$nombrematerial.'.</b></li>
                    <li><i>Submaterial: </i><b>'.$nombresubmat.'.</b></li>
                    <li><i>Material específico: </i><b>'.$nombrematespec.'.</b></li><br/>
                </ol>
                 <table border="1" cellpadding="2" cellspacing="2" align="center">
                 <tr nobr="true">
                  <th colspan="5"><b>Propiedades del material específico '.$nombrematespec.'</b></th>
                 </tr>
                 <tr nobr="true">
                  <td><i>Densidad</i></td>
                  <td><i>Módulo elástico</i></td>
                  <td><i>Elongación porcentual</i></td>
                  <td><i>Tenacidad</i></td>
                  <td><i>Coeficiente de Poisson</i></td>
                 </tr>
                 <tr nobr="true">
                  <td>'.$dens.'</td>
                  <td>'.$modelas.'</td>
                  <td>'.$eporc.'</td>
                  <td>'.$tenac.'</td>
                  <td>'.$cpoisson.'</td>
                 </tr>
                </table>';

        $pdf->writeHTML($desccomponentehtml, true, false, true, false, '');

        $pdf->AddPage();

        //Las imágenes subidas. Hago select de todas las que hay (asumo que al menos subió una.. y luego, si existe, lleno el html y mando al pdf).

        $imagenestitulohtml = '<h3>Imágenes del componente</h3>';

        $pdf->writeHTML($imagenestitulohtml, true, false, true, false, '');

        $idpieza = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $datosimagenes = $this->piezas_model->devolver_todaslasurlimagenespieza($idpieza);

        foreach ($datosimagenes as $key => $dat) {

             if($dat['queimagen']==1)
             {
                $imagenplano = 'Imagen del plano de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==2)
             {
                $imagenplano = 'Imagen de la pieza en buen estado.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==3)
             {
                $imagenplano = 'Imagen del esquema de montaje y funcionamiento de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==8)
             {
                $imagenplano = 'Máquina o mecanismo sobre el cual se encuentra montada la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }
             if($dat['queimagen']==9)
             {
                $imagenplano = 'Esquema de montaje y funcionamiento de la pieza.';

                $pdf->writeHTML($imagenplano, true, false, true, false, '');

                $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 110, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            <br/><br/><br/><br/><br/>';

                $pdf->writeHTML($espacio, true, false, true, false, '');
             }


          }

          $pdf->AddPage();

         //Procesos de fabricación

        $procesostitulohtml = '<h3>BLOQUE 3: Procesos de fabricacón aplicados a la pieza</h3><br/>';

        $pdf->writeHTML($procesostitulohtml, true, false, true, false, '');

        $cantidadprocesos = $this->piezas_model->devolver_cantidadprocesosparareporte($idcaso);
        $cantidadprocesos = $cantidadprocesos - 1;
        $nombresprocesos = $this->casos_model->devolver_procesos($idcaso);
        $nombressubtipos = $this->casos_model->devolver_subtipos($idcaso);
        $numerosprocesos = $this->casos_model->devolver_numeroprocesoparatabla($idcaso);


        for($i=0; $i<$cantidadprocesos; $i++)
        {
            $datosproveedor = $this->piezas_model->devolver_todosobreelproveedor($idcaso,$numerosprocesos[$i]);

            $proveedorhtml = '<h3>Proceso '.($i+1).': '.$nombresprocesos[$i].'. Subtipo: '.$nombressubtipos[$i].'</h3>
                 <table border="1" cellpadding="2" cellspacing="2" align="center">
                     <tr nobr="true">
                         <th colspan="5"><b>Datos del proveedor</b></th>
                     </tr>
                     <tr nobr="true">
                         <td><i>Empresa</i></td>
                         <td><i>Responsable</i></td>
                         <td><i>Correo electrónico</i></td>
                         <td><i>Teléfono</i></td>
                         <td><i>Dirección</i></td>
                     </tr>
                     <tr nobr="true">
                         <td>'.$datosproveedor['empresa'].'</td>
                         <td>'.$datosproveedor['responsable'].'</td>
                         <td>'.$datosproveedor['correoelectronico'].'</td>
                         <td>'.$datosproveedor['telefono'].'</td>
                         <td>'.$datosproveedor['direccion'].'</td>
                     </tr>
                 </table><br/>';

            $pdf->writeHTML($proveedorhtml, true, false, true, false, '');

            //Parametros generales
            $cantidadparamsgenerales = $this->piezas_model->devolver_cantidadprocesosgenerales($idcaso,$numerosprocesos[$i]);

            $cantidadderows = (float)$cantidadparamsgenerales/5;
            $cantidadderowsentero = intval($cantidadparamsgenerales/5);
            if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

            $paramgeneraleshtml1 = '
                  <table border="1" cellpadding="2" cellspacing="2" align="center">
                     <tr nobr="true">
                         <th colspan="5"><b>Parámetros generales del proceso. Cantidad: '.$cantidadparamsgenerales.'</b></th>
                     </tr>';

            $paramgeneraleshtml2 = '';

            //Devuelvo todos los nombres de los parametros generales para mostrar en el proceso, y voy a necesitar el id del proceso en
            //cuestion para consultar en la tabla atributos.
            $nombresparametrosgen = $this->casos_model->devolver_nombresparametrosgen($idcaso,$numerosprocesos[$i]);
            $idprocesoinvolucrado = $this->casos_model->devolver_idprocesopornombre($nombresprocesos[$i]);
            $valoresparametrosgen = $this->casos_model->devolver_valoresparametrosgen($idcaso,$numerosprocesos[$i]);

            $numerocolumnas = 5;
            $atributosrestantes = $cantidadparamsgenerales;
            $indiceatributoamostrar = 0;

            for($j=0; $j<$cantidadderows; $j++)
            {
                $paramgeneraleshtml2 = $paramgeneraleshtml2.'<tr nobr="true">';

                for($k=0; $k<$numerocolumnas;$k++)
                {
                    $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosgen[$indiceatributoamostrar],$idprocesoinvolucrado);

                    $paramgeneraleshtml2 = $paramgeneraleshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                    //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                    if($datosatributo['tipo_campo'] == "1")
                    {
                        if($valoresparametrosgen[$indiceatributoamostrar]== "0") $valoresparametrosgen[$indiceatributoamostrar] = "No";
                        if($valoresparametrosgen[$indiceatributoamostrar]== "1") $valoresparametrosgen[$indiceatributoamostrar] = "Si";   
                    }

                    if($datosatributo['tipo_campo'] == "2")
                    {
                        $valoresparametrosgen[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosgen[$indiceatributoamostrar]);
                    }               

                    $paramgeneraleshtml2 = $paramgeneraleshtml2.$valoresparametrosgen[$indiceatributoamostrar];


                    $paramgeneraleshtml2 = $paramgeneraleshtml2.'</b></i></td>';

                    if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                    $indiceatributoamostrar = $indiceatributoamostrar + 1;
                }
                
                $paramgeneraleshtml2 = $paramgeneraleshtml2.'</tr>';

                $atributosrestantes = $atributosrestantes-5;
                
            }

            $paramgeneraleshtml2 = $paramgeneraleshtml2.'</table><br/>';

            $pdf->writeHTML($paramgeneraleshtml1.$paramgeneraleshtml2, true, false, true, false, '');


            //Parametros especificos

            if($nombressubtipos[$i] != "Ninguno")
            {
                $cantidadparamsespecificos = $this->piezas_model->devolver_cantidadprocesosespecificos($idcaso,$numerosprocesos[$i]);

                $cantidadderows = (float)$cantidadparamsespecificos/5;
                $cantidadderowsentero = intval($cantidadparamsespecificos/5);
                if($cantidadderows != $cantidadderowsentero) $cantidadderows = $cantidadderowsentero+1;

                $paramespecificoshtml1 = '
                      <table border="1" cellpadding="2" cellspacing="2" align="center">
                         <tr nobr="true">
                             <th colspan="5"><b>Parámetros específicos del proceso. Cantidad: '.$cantidadparamsespecificos.'</b></th>
                         </tr>';

                $paramespecificoshtml2 = '';

                //Devuelvo todos los nombres de los parametros especificos para mostrar en el proceso, y voy a necesitar el id del proceso en
                //cuestion para consultar en la tabla atributos.
                $nombresparametrosesp = $this->casos_model->devolver_nombresparametrosesp($idcaso,$numerosprocesos[$i]);
                $idprocesoinvolucrado = $this->casos_model->devolver_idprocesopornombre($nombresprocesos[$i]);
                $valoresparametrosesp = $this->casos_model->devolver_valoresparametrosesp($idcaso,$numerosprocesos[$i]);

                $numerocolumnas = 5;
                $atributosrestantes = $cantidadparamsespecificos;
                $indiceatributoamostrar = 0;

                for($j=0; $j<$cantidadderows; $j++)
                {
                    $paramespecificoshtml2 = $paramespecificoshtml2.'<tr nobr="true">';

                    for($k=0; $k<$numerocolumnas;$k++)
                    {
                        $datosatributo = $this->piezas_model->devolver_datosatributo($nombresparametrosesp[$indiceatributoamostrar],$idprocesoinvolucrado);

                        $paramespecificoshtml2 = $paramespecificoshtml2.'<td><i>'.$datosatributo['leyenda'].'</i><br/><i><b>';

                        //en esta parte va simplemente la variable que voy a mostrar de valor concatenada al html anterior
                        if($datosatributo['tipo_campo'] == "1")
                        {
                            if($valoresparametrosesp[$indiceatributoamostrar]== "0") $valoresparametrosesp[$indiceatributoamostrar] = "No";
                            if($valoresparametrosesp[$indiceatributoamostrar]== "1") $valoresparametrosesp[$indiceatributoamostrar] = "Si";   
                        }

                        if($datosatributo['tipo_campo'] == "2")
                        {
                            $valoresparametrosesp[$indiceatributoamostrar] = $this->casos_model->devolver_nompreprecargadoporid($valoresparametrosesp[$indiceatributoamostrar]);
                        }               

                        $paramespecificoshtml2 = $paramespecificoshtml2.$valoresparametrosesp[$indiceatributoamostrar];


                        $paramespecificoshtml2 = $paramespecificoshtml2.'</b></i></td>';

                        if($atributosrestantes<5)$numerocolumnas = $atributosrestantes;

                        $indiceatributoamostrar = $indiceatributoamostrar + 1;
                    }
                    
                    $paramespecificoshtml2 = $paramespecificoshtml2.'</tr>';

                    $atributosrestantes = $atributosrestantes-5;
                    
                }

                $paramespecificoshtml2 = $paramespecificoshtml2.'</table><br/>';

                $pdf->writeHTML($paramespecificoshtml1.$paramespecificoshtml2, true, false, true, false, '');

            }

            

        }

        $pdf->AddPage();

        //Ensayos

        $cantidadensayos = ($this->piezas_model->devolver_cantidadensayos($idcaso))-1;
        $todosobreensayos = $this->piezas_model->devolver_todosobreensayos($idcaso);

        $cantidadimagenesdeensayo = $this->piezas_model->devolver_cantidadimagenesdeensayos($idpieza);
        $todosobreimgensayos = $this->piezas_model->devolver_todosobreimgdeensayos($idpieza);

        $titulobloqueensayoshtml = '<h3>BLOQUE 4: Ensayos</h3><br/>';
        $pdf->writeHTML($titulobloqueensayoshtml, true, false, true, false, '');

        $inicio = 0;
        $final = 0;

        for($i=0;$i<$cantidadensayos;$i++)
        {
            $ensayoshtml = '';
            $ensayoshtml = '<h3>Ensayo número '.($i+1).': '.$todosobreensayos[$i]['nombre'].'</h3>
                 <span style="text-align:justify;">'.$todosobreensayos[$i]['descripcion'].'</span>';

            if($todosobreensayos[$i]['cant_imagenes']!=0)
            {
                $final = $final + $todosobreensayos[$i]['cant_imagenes'];

                $ensayoshtml = $ensayoshtml.'<br/><br/><b>Imágenes del ensayo.</b>';
                $pdf->writeHTML($ensayoshtml, true, false, true, false, '');

                for($inicio;$inicio<$final;$inicio++)
                {
                    $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgensayos[$inicio]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                    $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                    <br/><br/><br/>';

                    $pdf->writeHTML($espacio, true, false, true, false, '');

                }
                
                $inicio = 0;
                $inicio = $inicio + $final;  

            }
            else $pdf->writeHTML($ensayoshtml, true, false, true, false, '');

        }

        $pdf->AddPage();


        //Macrografía

        $titulobloquemacrografiahtml = '<h3>BLOQUE 5: Macrografía</h3><br/><b>Imágenes de la macrografía.';
        $pdf->writeHTML($titulobloquemacrografiahtml, true, false, true, false, '');

        $cantidadmacrografias = $this->piezas_model->devolver_cantidaddescmacrografia($idcaso);
        $todosobremacrografia = $this->piezas_model->devolver_todosobremacrografia($idcaso);
        $todosobreimgmacrografia = $this->piezas_model->devolver_todosobreimgmacrografia($idpieza);

        if($todosobremacrografia[0]['tipo_fractura']==0) $todosobremacrografia[0]['tipo_fractura'] = 'Frágil';
        if($todosobremacrografia[0]['tipo_fractura']==1) $todosobremacrografia[0]['tipo_fractura'] = 'Dúctil';
        if($todosobremacrografia[0]['tipo_fractura']==2) $todosobremacrografia[0]['tipo_fractura'] = 'Mixta';

        $fracturapredom = 'Fractura predominante: '.$todosobremacrografia[0]['tipo_fractura'].'</b><br/>';
        $pdf->writeHTML($fracturapredom, true, false, true, false, '');

        $macrografiahtml = '';

        for($i=0; $i<$cantidadmacrografias;$i++)
        {
            $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgmacrografia[$i]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                    <br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');

            $macrografiahtml = '<b>Descripción: </b>'.$todosobremacrografia[$i]['descripcion'].'<br/>';

            $pdf->writeHTML($macrografiahtml, true, false, true, false, '');
        }


        $pdf->AddPage();


        //Micrografía

        $titulobloquemicrografiahtml = '<h3>BLOQUE 6: Micrografía</h3><br/><b>Imágenes de la micrografía.</b><br/>';
        $pdf->writeHTML($titulobloquemicrografiahtml, true, false, true, false, '');

        $cantidadmicrografias = $this->piezas_model->devolver_cantidaddescmicrografia($idcaso);
        $todosobremicrografia = $this->piezas_model->devolver_todosobremicrografia($idcaso);
        $todosobreimgmicrografia = $this->piezas_model->devolver_todosobreimgmicrografia($idpieza);

        $micrografiahtml = '';

        for($i=0; $i<$cantidadmicrografias;$i++)
        {
            $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimgmicrografia[$i]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                    <br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');

            $micrografiahtml = '<b>Descripción: </b>'.$todosobremicrografia[$i]['descripcion'].'<br/>';

            $pdf->writeHTML($micrografiahtml, true, false, true, false, '');
        }

        $pdf->AddPage();


        //Discusión

        $discusion = $this->casos_model->devolver_discusionporidcaso($idcaso);

        $discusionhtml = '<h3>BLOQUE 7: Discusión</h3>
        <span style="text-align:justify;">'.$discusion.'</span><br/><br/><br/>';

        $pdf->writeHTML($discusionhtml, true, false, true, false, '');

        //Hipotesis

        $cantidadhipotesis = ($this->piezas_model->devolver_cantidadhipotesis($idcaso))-1;
        $todosobrehipotesis = $this->piezas_model->devolver_todosobrehipotesis($idcaso);

        $cantidadimagenesdehipotesis = $this->piezas_model->devolver_cantidadimagenesdehipotesis($idpieza);
        $todosobreimghipotesis = $this->piezas_model->devolver_todosobreimgdehipotesis($idpieza);

        $titulobloquehipotesishtml = '<h3>BLOQUE 8: Hipótesis del usuario</h3><br/>';
        $pdf->writeHTML($titulobloquehipotesishtml, true, false, true, false, '');

        $inicio = 0;
        $final = 0;

        for($i=0;$i<$cantidadhipotesis;$i++)
        {
            $hipotesishtml = '';
            $hipotesishtml = '<h3>Hipótesis número '.($i+1).': '.$todosobrehipotesis[$i]['titulo'].'. Valoración: '.$todosobrehipotesis[$i]['valoracion'].'</h3>
                 <span style="text-align:justify;">'.$todosobrehipotesis[$i]['descripcion'].'</span>';

            if($todosobrehipotesis[$i]['cant_imagenes']!=0)
            {
                $final = $final + $todosobrehipotesis[$i]['cant_imagenes'];

                $hipotesishtml = $hipotesishtml.'<br/><br/><b>Imágenes de la hipótesis.</b>';
                $pdf->writeHTML($hipotesishtml, true, false, true, false, '');

                for($inicio;$inicio<$final;$inicio++)
                {
                    $pdf->Image('http://localhost/cafap/uploads/'.$todosobreimghipotesis[$inicio]['urlimagen'], '', '', 155, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

                    $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                    <br/><br/><br/>';

                    $pdf->writeHTML($espacio, true, false, true, false, '');

                }
                
                $inicio = 0;
                $inicio = $inicio + $final;  

            }
            else $pdf->writeHTML($hipotesishtml, true, false, true, false, '');

        }

        $pdf->AddPage();

        //Diagrama de Pareto

        $urldepareto = $this->piezas_model->devolver_urldeparetoparareporte($idpieza);

        $paretohtml = '<h3>Diagrama de Pareto para las hipótesis del caso</h3><br/><br/>';

        $pdf->writeHTML($paretohtml, true, false, true, false, '');

        $pdf->Image('http://localhost/cafap/uploads/'.$urldepareto, '', '', 180, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

        $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                    <br/><br/><br/><br/><br/>';

        $pdf->writeHTML($espacio, true, false, true, false, '');


        //Conclusiones generales

        $conclusion = $this->casos_model->devolver_conclusionporidcaso($idcaso);

        $conclusionhtml = '<h3>BLOQUE 9: Conclusión del usuario</h3>
        <span style="text-align:justify;">'.$conclusion.'</span>';

        $pdf->writeHTML($conclusionhtml, true, false, true, false, '');


        $espacio = '<br/><br/>';

        $pdf->writeHTML($espacio, true, false, true, false, '');


        //Sugerencias del software

        //Procesos y subprocesos de fabricación (50):
        $idsprocesoscasoactual = $this->casos_model->devolver_procesosparasugerenciafallo($idcaso);
        $idssubprocesoscasoactual = $this->casos_model->devolver_subprocesosparasugerenciafallo($idcaso);
        //Tipo de fractura (20):
        $tipofracturacasoactual = $this->casos_model->devolver_tipofracturaporidcaso($idcaso);
        //Fase del ciclo de vida donde falló la pieza (9):
        $idpiezacasoactual = $this->piezas_model->devolver_idpiezaporidcaso($idcaso);
        $faseciclovidacasoactual = $this->casos_model->devolver_faseciclovida($idpiezacasoactual);
        //Material (7), Submaterial (11) y Material Específico (15):
        $materialcasoactual = $this->casos_model->devolver_materialparasugerencia($idpiezacasoactual);
        $submaterialcasoactual = $this->casos_model->devolver_submaterialparasugerencia($idpiezacasoactual);
        $materialespcasoactual = $this->casos_model->devolver_materialespparasugerencia($idpiezacasoactual);
        //Elementos en suspensión (3):
        $elemsuspcasoactual = $this->casos_model->devolver_elemsuspparasugerencia($idpiezacasoactual);
        //Modificaciones en condiciones de trabajo (3):
        $modifcondcasoactual = $this->casos_model->devolver_modifcondtrabparasugerencia($idpiezacasoactual);


        $totalafinidad = 0;

        $casosafines = array(
                      "id" => "",
                      "afinidad" => "",
                      );

        $casosfinalizados = $this->casos_model->devolver_finalizadosconconclusion();

        for($k = 0; $k <count($casosfinalizados); $k++)
        {
            //Procesos y subprocesos de fabricación (50):
            $idsprocesoscasoacomparar = $this->casos_model->devolver_procesosparasugerenciafallo($casosfinalizados[$k]);
            $idssubprocesoscasoacomparar = $this->casos_model->devolver_subprocesosparasugerenciafallo($casosfinalizados[$k]);
            //Tipo de fractura (20):
            $tipofracturacasoacomparar = $this->casos_model->devolver_tipofracturaporidcaso($casosfinalizados[$k]);
            //Fase del ciclo de vida donde falló la pieza (9):
            $idpiezacasoacomparar = $this->piezas_model->devolver_idpiezaporidcaso($casosfinalizados[$k]);
            $faseciclovidacasoacomparar = $this->casos_model->devolver_faseciclovida($idpiezacasoacomparar);
            //Material (7), Submaterial (11) y Material Específico (15):
            $materialcasoacomparar = $this->casos_model->devolver_materialparasugerencia($idpiezacasoacomparar);
            $submaterialcasoacomparar = $this->casos_model->devolver_submaterialparasugerencia($idpiezacasoacomparar);
            $materialespcasoacomparar = $this->casos_model->devolver_materialespparasugerencia($idpiezacasoacomparar);
            //Elementos en suspensión (3):
            $elemsuspcasoacomparar = $this->casos_model->devolver_elemsuspparasugerencia($idpiezacasoacomparar);
            //Modificaciones en condiciones de trabajo (3):
            $modifcondcasoacomparar = $this->casos_model->devolver_modifcondtrabparasugerencia($idpiezacasoacomparar);


            $afinidadentreprocesos = 0;

            for($i = 0; $i <count($idsprocesoscasoactual); $i++)
            {
                for($j = 0; $j <count($idsprocesoscasoacomparar); $j++)
                {
                    if($idsprocesoscasoactual[$i]==$idsprocesoscasoacomparar[$j])
                    {
                        if($idssubprocesoscasoactual[$i]==$idssubprocesoscasoacomparar[$j])
                        {
                            $afinidadentreprocesos++;
                            $idsprocesoscasoacomparar[$i] = -1;
                            $idssubprocesoscasoacomparar[$i] = -1;
                        }          
                    } 
                }
            }

            $cantidadprocesoscasoactual = count($idsprocesoscasoactual);
            $porcentajeafinidadprocesos = ($afinidadentreprocesos*50)/$cantidadprocesoscasoactual;

            $totalafinidad = $porcentajeafinidadprocesos;

            if($tipofracturacasoactual == $tipofracturacasoacomparar) $totalafinidad += 20;
            if($faseciclovidacasoactual == $faseciclovidacasoacomparar) $totalafinidad += 9;

            $puntajematerial = 0;

            if($materialcasoactual == $materialcasoacomparar) $puntajematerial = 7;
            if($submaterialcasoactual == $submaterialcasoacomparar) $puntajematerial = 11;
            if($materialespcasoactual == $materialespcasoacomparar) $puntajematerial = 15;

            $totalafinidad += $puntajematerial;

            if($elemsuspcasoactual = $elemsuspcasoacomparar) $totalafinidad += 3;
            if($modifcondcasoactual = $modifcondcasoacomparar) $totalafinidad += 3;

            $casosafines['id'][$k] = $casosfinalizados[$k];
            $casosafines['afinidad'][$k] = $totalafinidad;
        //fin for
        }

        $this->casos_model->insertar_afinidades($casosafines);

        $afinidadesordenadas['idcasos'] = $this->casos_model->devolver_idsafinidadesord();
        $afinidadesordenadas['afinidades'] = $this->casos_model->devolver_afinidadesord();

        $this->casos_model->eliminar_afinidades();

        //Fin del paso 1 de obtener las afinidades ordenadas, ahora obtenemos el bloque más relevante junto con conclusión general.

        for($i = 0; $i <count($afinidadesordenadas['idcasos']); $i++)
        {
          $conclusiongeneral[$i] = $this->casos_model->devolver_conclusiongeneralparasugerencia($afinidadesordenadas['idcasos'][$i]);;
          $bloquehipotesismasrelevante[$i] = $this->casos_model->devolver_bloquehipotesismasrelevante($afinidadesordenadas['idcasos'][$i]);
          $concluhipotesismasrelevante[$i] = $this->casos_model->devolver_concluhipotesismasrelevante($afinidadesordenadas['idcasos'][$i],$bloquehipotesismasrelevante[$i]);;

          if ($i==10) $i = count($afinidadesordenadas['idcasos']);
        }

        $sugerenciashtml = '<h3>SUGERENCIAS DEL SOFTWARE</h3><br/>';

        $pdf->writeHTML($sugerenciashtml, true, false, true, false, '');

        $cuerposugerenciashtml = '';

        for($i = 0; $i <count($afinidadesordenadas['idcasos']); $i++) 
        {
            $cuerposugerenciashtml = $cuerposugerenciashtml.'<span style="text-align:justify;"><b>SUGERENCIA NÚMERO '.($i+1).'. Basada en un 
                                     caso con un '.$afinidadesordenadas['afinidades'][$i].'% de afinidad.</b><br/><br/>
                                     Bloque más influyente para este caso: ';
            $bloque = '';
            if($bloquehipotesismasrelevante[$i]=="conclusionbloque1") $bloque = 'BLOQUE 1 (INTRODUCCIÓN AL CASO)';
            if($bloquehipotesismasrelevante[$i]=="conclusionbloque2") $bloque = 'BLOQUE 2 (DESCRIPCIÓN DEL COMPONENTE)';
            if($bloquehipotesismasrelevante[$i]=="conclusionbloque3") $bloque = 'BLOQUE 3 (PROCESOS DE FABRICACIÓN)';
            if($bloquehipotesismasrelevante[$i]=="conclusionbloque4") $bloque = 'BLOQUE 4 (ENSAYOS)';
            if($bloquehipotesismasrelevante[$i]=="conclusionbloque5") $bloque = 'BLOQUE 5 (MACROGRAFÍA)';
            if($bloquehipotesismasrelevante[$i]=="conclusionbloque6") $bloque = 'BLOQUE 6 (MICROGRAFÍA))';
            if($bloquehipotesismasrelevante[$i]=="conclusionbloque8") $bloque = 'BLOQUE 8 (HIPÓTESIS DEL USUARIO)';


            $cuerposugerenciashtml = $cuerposugerenciashtml.$bloque.'. <br/><br/>
                                     Motivo de su influencia: '.$concluhipotesismasrelevante[$i].'<br/><br/>
                                     Conclusión del experto que analizó este caso (y posible motivo de la falla del suyo: '.$conclusiongeneral[$i].'
                                     <br/><br/></span>';
        }

        $pdf->writeHTML($cuerposugerenciashtml, true, false, true, false, '');
   

        //Conclusión del especialista

        $estadodelcaso = $this->casos_model->devolver_estadodelcaso($idcaso);

        if($estadodelcaso == 3)
        {
            $conclusionesp = $this->casos_model->devolver_conclusionespporidcaso($idcaso);

            $conclusionesphtml = '<h3>CONCLUSÓN FINAL DEL ESPECIALISTA</h3>
            <span style="text-align:justify;">'.$conclusionesp.'</span>';

            $pdf->writeHTML($conclusionesphtml, true, false, true, false, '');
        }

        $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>';

        $pdf->writeHTML($espacio, true, false, true, false, '');

        //Agradecimiento

        $agradecimiento = '<h2 align="center">¡Muchas gracias por utilizar CAFAP!</h2>';

        $pdf->writeHTML($agradecimiento, true, false, true, false, '');
        

        
     
        // ---------------------------------------------------------    
     
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
         $pdf->Output('Caso de '.$datosusuario['nombre'].' '.$datosusuario['apellido'].'.pdf', 'D');    

         if($esusuariocomun == 1); 
     
        //============================================================+
        // END OF FILE
        //============================================================+

    } //fin if

    } //fin metodo

} //fin clase
 
?>