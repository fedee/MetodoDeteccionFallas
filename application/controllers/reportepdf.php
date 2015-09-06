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
    }
 
    public function crearpdf($idcaso) {

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

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 112, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }
         if($dat['queimagen']==2)
         {
            $imagenplano = 'Imagen de la pieza en buen estado.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 112, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }
         if($dat['queimagen']==3)
         {
            $imagenplano = 'Imagen del esquema de montaje y funcionamiento de la pieza.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 112, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }
         if($dat['queimagen']==8)
         {
            $imagenplano = 'Máquina o mecanismo sobre el cual se encuentra montada la pieza.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 112, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }
         if($dat['queimagen']==9)
         {
            $imagenplano = 'Esquema de montaje y funcionamiento de la pieza.';

            $pdf->writeHTML($imagenplano, true, false, true, false, '');

            $pdf->Image('http://localhost/cafap/uploads/'.$dat['urlimagen'], '', '', 175, 112, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

            $espacio = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                        <br/><br/><br/><br/><br/><br/>';

            $pdf->writeHTML($espacio, true, false, true, false, '');
         }


      }

    $pdf->AddPage();

    $procesosfab = '<h3>BLOQUE 3: Procesos de fabricacón</h3>
            <ol>                
                <li><i>¿El mismo modelo ha fallado en múltipes ocasiones? </i><b>'.$todosobrelapieza['fallo_multiplesoc'].'.</b></li>
                <li><i>Tiempo de trabajo que estuvo sometida la pieza antes de la rotura:</i>
                       <b>'.$todosobrelapieza['ttrabajocant'].' '.$todosobrelapieza['ttrabajo_tiempo'].'.</b></li>
                <li><i>Vida útil de la pieza (según fabricante), o vida esperada:</i>
                <b>'.$todosobrelapieza['vutil_cantidad'].' '.$todosobrelapieza['vutil_tiempo'].'.</li>
                <li><i>Faso del ciclo de vida en donde se produjo la falla: </i><b>'.$todosobrelapieza['fase_ciclovida'].'.</b></li>   
            </ol>';

    $pdf->writeHTML($procesosfab, true, false, true, false, '');


    
 
    // ---------------------------------------------------------    
 
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
     $pdf->Output('example_001.pdf', 'I');    
 
    //============================================================+
    // END OF FILE
    //============================================================+
    }
}
 
/* End of file c_test.php */
/* Location: ./application/controllers/c_test.php */
?>