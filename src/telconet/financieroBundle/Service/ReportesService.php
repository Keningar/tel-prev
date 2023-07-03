<?php

namespace telconet\financieroBundle\Service;

use telconet\schemaBundle\Entity\AdmiFormaPago;
use Symfony\Component\HttpKernel\KernelEvents;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Fill;

class ReportesService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom   = $container->get('doctrine.orm.telconet_entity_manager');        
        $this->emfinan = $container->get('doctrine.orm.telconet_financiero_entity_manager');
    }
    
    /**
     * Documentacion de funcion obtenerFormasPagoParaReporteCierreCaja
     * Obtiene formas de pago depositables y si la empresa es Telconet agrega la forma de pago "Tarjeta de credito"
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 2016-07-14
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 2016-08-18   Se agregan todas las formas de pago tipo retención para la generación del reporte de cierre de caja.
     *  
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 2017-09-28   Se agregan las formas de pago con código 'DGMA' y 'TGMA'para la generación del reporte de cierre de caja.
     * 
     * @author Luis Lindao <llindao@telconet.ec>
     * @version 1.3 2019-01-02   Se elimina las formas de pagos fijas y se parametriza en la base para presentar en combo.
     * 
     * @param string $strPrefijoEmpresa Es el prefijo de la empresa
     * @return Array $arrFormasPago - retorna arreglo con informacion las formas de pago
     */    
    public function obtenerFormasPagoParaReporteCierreCaja($strPrefijoEmpresa)
    {   
        $arrFormasPago        = array();
        $strFormasPago        = "";
        $arrayParametros        = array();
        $arrayParametros['strEstadoFp']        = 'Activo';
        $arrayParametros['strPrefijoEmpresa']  = $strPrefijoEmpresa; 
        $arrayParametros['strNombreParametro'] = 'FORMA_PAGO_CIERRE_CAJA';
        $arrayParametros['strEstadoParametro'] = 'Activo';

        $arrayFormaPagoDepositable =  $this->emfinan->getRepository('schemaBundle:AdmiFormaPago')
                                               ->getFormasPagoParametrizadas($arrayParametros);
        
        foreach ($arrayFormaPagoDepositable as $fpago)
        {
                 
            $arrFormasPago[] = 
                array(
                    'id'          => $fpago['id'],
                    'descripcion' => $fpago['descripcionFormaPago']
                );  
            $strFormasPago .= $fpago['descripcionFormaPago'];
        }
        
        $arrFormasPago['arrFormasPago']=$arrFormasPago;
        $arrFormasPago['strFormasPago']=$strFormasPago;
        return $arrFormasPago;
    }

    /**
     * Documentación para el método 'getFormatoValorCelda'.
     * 
     * Método que devuelve un arreglo con el formato que tendrán las celdas según el arreglo $arrayTipo recibido.
     * $arrayTipo puede tener: 'N'  Formato numérico con punto decimal y coma para miles.
     *                         'T'  Formato tipo texto.
     * 
     * @param Array $arrayTipo Arreglo con los tipos de formatos de cada celda.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 22-09-2016
     */
    private function getFormatoValorCelda($arrayTipo)
    {
        $arrayFormato = array();
        
        foreach($arrayTipo as $strTipo)
        {
            if($strTipo === 'T')
            {
                $arrayFormato[] = PHPExcel_Style_NumberFormat::FORMAT_TEXT;
            }
            else if($strTipo === 'N')
            {
                $arrayFormato[] = PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
            }
        }
        
        return $arrayFormato;
    }
    
    /**
     * Documentación para el método 'getAlineamiento'.
     * 
     * Método que devuelve un arreglo con el formato de alinineamiento que tendrán las celdas según el arreglo $arrayAlignment recibido.
     * $arrayAlignment puede tener: 'L'  Indica alineación a la Izquierda.
     *                              'R'  Indica alineación a la Derecha.
     * 
     * @param Array $arrayAlineamiento Arreglo con los tipos de alineación de la celdas.
     * 
     * @return Array listado con estilos de alineamiento de las celdas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 22-09-2016
     */
    private function getAlineamiento($arrayAlignment)
    {
        $arrayAlineamiento = array();

        foreach($arrayAlignment as $strAlign)
        {
            if($strAlign === 'L')
            {
                $arrayAlineamiento[] = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT));
            }
            else if($strAlign === 'R')
            {
                $arrayAlineamiento[] = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
            }
        }
        
        return $arrayAlineamiento;
    }
    
    /**
     * Documentación para el método 'getEstiloSumario'.
     * 
     * Método que devuelve un arreglo con el estilo que llevarán las celdas del registro sumario.
     * 
     * @param Array  $arrayColumnas Arreglo con las columnas en las que aplica es estilo del sumario.
     * @param String $strFontColor  Código hexadecimal del color para la fuente del sumario.
     * @param String $strFillColor  Código hexadecimal del color para el fondo de las celdas del sumario.
     * 
     * @return Array Listado con los estilos de las celdas del sumario.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 22-09-2016
     */
    private function getEstiloSumario($arrayColumnas, $strFontColor, $strFillColor)
    {
        $arrayEstilo = array();

        foreach($arrayColumnas as $strAlign)
        {
            $arrayEstilo[] = array('font' => array('bold'  => false,
                                                   'color' => array('rgb' => $strFontColor),
                                                   'size'  => 10),
                                   'fill' => array('type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                   'color' => array('rgb' => $strFillColor)));
        }
        
        return $arrayEstilo;
    }
    
    /**
     * Documentación para el método 'aplicarEstiloFormatoRegistro'.
     * 
     * Método dinámico para formatear un conjunto de celdas por fila dentro del reporte.
     * 
     * @param Array    $arrayParametros['COLUMNAS']      Listado de letras que identifican a la columna de la celda.
     * @param Integer  $arrayParametros['INDICE']        Indicador de la fila a procesar.
     * @param Boolean  $arrayParametros['SUMARIO']       Indica si procesa o no el sumarizado para las columnas tipo numérico.
     * @param Array    $arrayParametros['ESTILO']        Listado con las propiedades de Formato/Estilo de cada celda.
     * @param Array    $arrayParametros['ALINEAMIENTO']  Listado con los indicadores L y R (izquiera/derecha) para alinear el contenido de la celda.
     * @param Array    $arrayParametros['FORMATO_VALOR'] Listado de tipo de contendido de la celda T y N.
     * @param Integer  $arrayParametros['INDENT']        Indicador del tamaño de la indentación para todas las celdas.
     * @param Array    $arrayParametros['COLS_ANCHO']    Listado con el tamaño del ancho de cada columna a procesar.
     * @param Integer  $arrayParametros['SUMARIO_INI']   Indicador del inicio del rango a sumarizar en cada columna.
     * @param PHPExcel $objPHPExcel Arreglo con los formatos de 
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 22-09-2016
     */
    public function aplicarEstiloFormatoRegistro($objPHPExcel, $arrayParametros)
    {
        if(isset($arrayParametros['COLUMNAS']) && isset($arrayParametros['INDICE']))
        {
            $i                 = 0;
            $intIndice         = $arrayParametros['INDICE'];
            $strFontColor      = 'FFFFFF';
            $strFillColor      = '888888';
            $arrayEstilo       = array();
            $arrayAlineamiento = array();
            $arrayFormato      = array();
            
            if(isset($arrayParametros['SUMARIO']) && $arrayParametros['SUMARIO'])
            {
                $arrayEstilo = $this->getEstiloSumario($arrayParametros['COLUMNAS'], $strFontColor, $strFillColor);
            }
            else if(isset($arrayParametros['ESTILO']))
            {
                $arrayEstilo = $arrayParametros['ESTILO'];
            }
            
            if(isset($arrayParametros['ALINEAMIENTO']))
            {
                $arrayAlineamiento = $this->getAlineamiento($arrayParametros['ALINEAMIENTO']);
            }
            
            if(isset($arrayParametros['FORMATO_VALOR']))
            {
                $arrayFormato = $this->getFormatoValorCelda($arrayParametros['FORMATO_VALOR']);
            }
            
            foreach($arrayParametros['COLUMNAS'] as $strColumna)
            {
                $strCelda = $strColumna . $intIndice;
                
                if(isset($arrayFormato[$i]))
                {
                    $objPHPExcel->getActiveSheet()->getStyle($strCelda)->getNumberFormat()->setFormatCode($arrayFormato[$i]);
                }
                
                if(isset($arrayEstilo[$i]) || isset($arrayAlineamiento[$i]))
                {
                    // Se crea un único objeto array de estilo a aplicar
                    $arrayStyle[] = array_merge(isset($arrayEstilo[$i]) ? $arrayEstilo[$i] : array(),
                                                isset($arrayAlineamiento[$i]) ? $arrayAlineamiento[$i] : array());
                    $objPHPExcel->getActiveSheet()->getStyle($strCelda)->applyFromArray($arrayStyle[$i]);
                }
                
                if(isset($arrayParametros['INDENT']))
                {
                    $objPHPExcel->getActiveSheet()->getStyle($strCelda)->getAlignment()->setIndent($arrayParametros['INDENT']);
                }

                if(isset($arrayParametros['COLS_ANCHO']) && isset($arrayParametros['COLS_ANCHO'][$i]))
                {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($strColumna)->setWidth($arrayParametros['COLS_ANCHO'][$i]);
                }
                
                if(isset($arrayParametros['SUMARIO']) && isset($arrayParametros['SUMARIO_INI']) && isset($arrayParametros['FORMATO_VALOR']) && 
                   isset($arrayParametros['FORMATO_VALOR'][$i]) && $arrayParametros['FORMATO_VALOR'][$i] === 'N')
                {
                    $intIndiceIni = $arrayParametros['SUMARIO_INI'];
                    $intIndiceFin = $intIndice - 1;
                    $strFormula   = "=SUM($strColumna$intIndiceIni:$strColumna$intIndiceFin)";
                    $objPHPExcel->getActiveSheet()->setCellValue($strCelda, $strFormula);
                }

                $i++;
            }
        }
    }
    
}
