<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
 
class GenerarGraficos
{
    public function __construct() // or any other method
    {
        require_once 'C:\xampp\htdocs\codeigniter2\application\libraries\pChart\pData.class'; //manipula el array de los datos
        require_once 'C:\xampp\htdocs\codeigniter2\application\libraries\pChart\pData.class'; //maneja la cache
        require_once 'C:\xampp\htdocs\codeigniter2\application\libraries\pChart\pData.class'; //maneja los graficos
    }
    function pData(){
        return new pData();    
    }
    function pImage($n,$i,$data=NULL,$trans=FALSE){
        return new pImage($n,$i,$data,$trans);
    }
} 
?>