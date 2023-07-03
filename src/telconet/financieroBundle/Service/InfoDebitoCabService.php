<?php

namespace telconet\financieroBundle\Service;

use \PHPExcel;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Cell;
use \PHPExcel_IOFactory;
use \PHPExcel_Settings;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;

use telconet\schemaBundle\Entity\AdmiFormaPago;
use telconet\schemaBundle\Entity\InfoDebitoCab;
use telconet\schemaBundle\Entity\InfoDebitoGeneral;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\schemaBundle\DependencyInjection\BatchTransaction;
use Symfony\Component\Console\Output\NullOutput;

class InfoDebitoCabService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;

    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emfinan   = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emGeneral = $container->get('doctrine.orm.telconet_general_entity_manager');
    }
    
    
    /**
     * Reemplaza cadena de caracteres que no se deben visualizar en los nombres de los débitos
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 29-08-2016
     * 
     * @param string $strCadenaAReemplazar  Cadena a reemplazar 
     * @param string $strNombreParametro    Nombre del parámetro a buscar que contiene el listado de palabras con el caracter a reemplazar
     * @param int    $intIdEmpresa          Id de la empresa a la que pertenece el parametro a buscar
     * @return string $strCadenaReemplazada
     */
    public function reemplazarPalabrasEnCadena($strCadenaAReemplazar, $strNombreParametro, $intIdEmpresa)
    {
        $strCadenaReemplazada  = $strCadenaAReemplazar;
        $arrayCadenasBuscar    = array();
        $arrayCadenasReemplazo = array();
        $intIdParametroCargo   = 0;
        $objParametroCab       = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                 ->findOneBy( array('nombreParametro' => $strNombreParametro, 
                                                                    'estado'          => 'Activo') );

        if( !empty($objParametroCab) )
        {
            $intIdParametroCargo = $objParametroCab->getId();
        }
        
        $arrayCriteriosBusqueda = array('estado' => 'Activo', 'parametroId' => $intIdParametroCargo);
        
        if( !empty($intIdEmpresa) )
        {
            $arrayCriteriosBusqueda['intEmpresaCod'] = $intIdEmpresa;
        }//( !empty($intIdEmpresa) )

        $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getParametrosByCriterios($arrayCriteriosBusqueda);

        if( !empty($arrayParametrosDet['registros']) )
        {
            foreach($arrayParametrosDet['registros'] as $arrayDato)
            {
                $arrayCadenasReemplazo[] = $arrayDato['valor1'];
                $arrayCadenasBuscar[]    = $arrayDato['descripcion'];
            }//foreach($arrayResultados['registros'] as $arrayDato)
        }//( !empty($arrayResultados['registros']) )


        if( !empty($arrayCadenasReemplazo) && !empty($arrayCadenasBuscar) )
        {
            $strCadenaReemplazada = str_replace( $arrayCadenasBuscar, $arrayCadenasReemplazo, strtoupper($strCadenaReemplazada) );
        }
        
        return $strCadenaReemplazada;
    }
    
    
    /**
     * Anula debitoGeneral y las cabeceras del debito
     * @param type array $arr_debitos_cab
     * @param type String $usuarioUltMod (Ultimo usuario que modifico el debitoCab)
     * @param type String $feUltMod (Ultima vez que se modifico el debitoCab)
     * @since 15/08/2014
     * @return array
     */
    public function anularCabecerasDebito($arrayDebitoGeneral,$usuarioUltMod,$fechaUltMod)
    {
        $this->emfinan->getConnection()->beginTransaction();        
        try
        {
            $msg = "Se Anulo Debito Correctamente";
            $success = true;             
            for($indiceGen=0;$indiceGen<count($arrayDebitoGeneral)-1;$indiceGen++)
            {
                $arrDebitosCab = $this->emfinan->getRepository('schemaBundle:InfoDebitoCab')->findByDebitoGeneralId($arrayDebitoGeneral[$indiceGen]);
                if(count($arrDebitosCab)>0)
                {    
                    //RECORRE ARREGLO DE CABECERAS DE DEBITOS ENVIADO POR PARAMETRO
                    foreach($arrDebitosCab as $infoDebitoCab)
                    {
                        $arrayDebitosProcesados=$this->emfinan->getRepository('schemaBundle:InfoDebitoCab')
                            ->findCountDebitosPorDebitoCabIdPorEstado($infoDebitoCab->getId(),'Procesado');
                        $arrayDebitosRechazados=$this->emfinan->getRepository('schemaBundle:InfoDebitoCab')
                            ->findCountDebitosPorDebitoCabIdPorEstado($infoDebitoCab->getId(),'Rechazado');
                        //SE VALIDA QUE NO TENGA DEBITOS PROCESADOS NI RECHAZADOS PARA PODERLO ANULAR
                        if($arrayDebitosProcesados[0]['total']==0 && $arrayDebitosRechazados[0]['total']==0)
                        {
                            //$arrayDebitoGeneralId[]=$infoDebitoCab->getDebitoGeneralId()->getId();
                            $infoDebitoCab->setEstado('Anulado');
                            $infoDebitoCab->setUsrUltMod($usuarioUltMod);
                            $infoDebitoCab->setFeUltMod($fechaUltMod);
                            $this->emfinan->persist($infoDebitoCab);
                            $this->emfinan->flush();  
                        }
                        else
                        {
                            $msg = "No se puede anular porque hay bancos que ya tienen debitos procesados o rechazados.";
                            $success = false;                         
                        }
                    }
                }
                else
                {
                    $msg = "No se puede anular porque se encontraron inconsistencias, ".
                        "una o mas cabeceras del debito no fueron encontrados.";
                    $success = false;                     
                }
                if($success)
                {
                    //SE VALIDA SI NO TIENE CABECERAS QUE ESTEN PENDIENTES O PROCESADOS
                    $objInfoDebitosCab = $this->emfinan->getRepository('schemaBundle:InfoDebitoCab')
                        ->findDebitosPorDebitoGeneralIdPorEstado($arrayDebitoGeneral[$indiceGen],array('Pendiente','Procesado'));
                    //SI NO TIENE PENDIENTES O PROCESADOS, PROCEDE A INACTIVAR AL DEBITO GENERAL
                    if(!$objInfoDebitosCab)
                    {
                        $objInfoDebitoGeneral=$this->emfinan->getRepository('schemaBundle:InfoDebitoGeneral')->find($arrayDebitoGeneral[$indiceGen]);
                        $objInfoDebitoGeneral->setEstado('Inactivo');
                        $this->emfinan->persist($objInfoDebitoGeneral);
                        $this->emfinan->flush();                   
                        $this->emfinan->getConnection()->commit();                         
                    }
                    else
                    {
                        $this->emfinan->getConnection()->rollback();
                        $this->emfinan->getConnection()->close();                        
                        $msg = "No se puede anular porque hay cabeceras de debitos que estan pendientes o procesadas.";
                        $success = false;                         
                    }                                          
                }    
            }
            return array('success' => $success, 'msg' => $msg);            
        }
        catch(\Exception $e){
            $this->emfinan->getConnection()->rollback();
            $this->emfinan->getConnection()->close();
            $success=false;
            $msg= $e->getMessage();            
            return array('success' => $success, 'msg' => $msg);            
        }
    }
    
    /**
     * Reabre las cabeceras del debito
     * @param type array $arr_debitos_cab
     * @param type String $usuarioUltMod (Ultimo usuario que modifico el debitoCab)
     * @param type String $feUltMod (Ultima vez que se modifico el debitoCab)
     * @since 15/08/2014
     * @return array
     */
    public function reabreCabecerasDebito($arrayDebitoGeneral,$usuarioUltMod,$fechaUltMod)
    {
        $this->emfinan->getConnection()->beginTransaction();        
        try
        {
            $msg = "Se Reabrio Debitos Correctamente";
            $success = true;   
            for($indiceGen=0;$indiceGen<count($arrayDebitoGeneral)-1;$indiceGen++)
            {
                $arrayDebitosCab = $this->emfinan->getRepository('schemaBundle:InfoDebitoCab')->findBy(
                    array('debitoGeneralId'=>$arrayDebitoGeneral[$indiceGen]));
                foreach($arrayDebitosCab as $objDebitoCab)
                {     
                    //SE VALIDA SI LA CABECERA ESTA EN ESTADO PROCESADO
                    if ($objDebitoCab->getEstado()=='Procesado')  
                    {                   
                        $objDebitoCab->setEstado('Pendiente');
                        $objDebitoCab->setProcesado('N');
                        $objDebitoCab->setUsrUltMod($usuarioUltMod);
                        $objDebitoCab->setFeUltMod($fechaUltMod);
                        $this->emfinan->persist($objDebitoCab);
                        $this->emfinan->flush();
                    }
                    else
                    {
                        $msg = "Una o mas cabeceras del debito no fueron encontrados.";
                        $success = false; 
                    }
                }
            }            
            $this->emfinan->getConnection()->commit();           
            return array('success' => $success, 'msg' => $msg);            
        }
        catch(\Exception $e){
            $this->emfinan->getConnection()->rollback();
            $this->emfinan->getConnection()->close();
            $success=false;
            $msg= $e->getMessage();            
            return array('success' => $success, 'msg' => $msg);            
        }
    }    

    /**
     * Documentación para procesaCabecerasDebitoGeneral
     * Procesa las cabeceras
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 13-09-2017
     * @return array
     */
    public function procesaCabecerasDebitoGeneral($arrayParametros)
    {
        $intDebitoGeneralId = $arrayParametros["intDebitoGeneralId"];
        $strUsrUltMod       = $arrayParametros["strUsrUltMod"];
        $strFechaUltMod     = $arrayParametros["strFechaUltMod"];
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            $intEstado       = 1;
            $arrayDebitosCab = $this->emfinan->getRepository('schemaBundle:InfoDebitoCab')->findBy(
                    array('debitoGeneralId' => $intDebitoGeneralId));
            foreach ($arrayDebitosCab as $objDebitoCab)
            {
                $objDebitoCab->setEstado('Procesado');
                $objDebitoCab->setProcesado('S');
                $objDebitoCab->setUsrUltMod($strUsrUltMod);
                $objDebitoCab->setFeUltMod($strFechaUltMod);
                $this->emfinan->persist($objDebitoCab);
                $this->emfinan->flush();
            }
            $this->emfinan->getConnection()->commit();
            return array('intEstado' => $intEstado, 'strMensaje' => '');
        }
        catch (\Exception $objException)
        {
            $this->emfinan->getConnection()->rollback();
            $this->emfinan->getConnection()->close();
            $intEstado  = 0;
            $strMensaje = $objException->getMessage();
            return array('intEstado' => $intEstado, 'strMensaje' => $strMensaje);
        }
    }
    
    
    /*
     * Documentación para función findTipoFiltroEscenarioDebito
     * 
     * Obtiene el Tipo de Escenario y Filtro según corresponda el débito.
     *
     * @author: Hector Lozano<hlozano@telconet.ec>
     * @version: 1.0 11-06-2020 
     * @param type $arrayParametros['intIdDebitoGeneral' : Id del Débito
     *                              'intIdEmpresa'       : Id de Empresa 
     *                             ]
     * @return array
     */
    public function findTipoFiltroEscenarioDebito($arrayParametros)
    {
        try
        {
            $arrayInfoDebitoCab = $this->emfinan->getRepository('schemaBundle:InfoDebitoCab')->findTipoFiltroEscenarioDebito($arrayParametros);
            
            return $arrayInfoDebitoCab;
            
        } 
        catch (Exception $ex) 
        {
            
            return null;
          
        }
    
    }
    
    /*
     * Documentación para función obtenerEscenariosPorEstado
     * 
     * Obtiene los escenarios según los estados para la generación de los débitos.
     *
     * @author: Hector Lozano<hlozano@telconet.ec>
     * @version: 1.0 13-05-2020 
     * @param type $arrayParametros['nombreParametro' : Nombre del parámetro cab
     *                              'estado'          : Estado del parámetro 
     *                             ]
     * @return array
     */
    public function obtenerEscenariosPorEstado($arrayParametros)
    {
        $objParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                           ->findOneBy(array("nombreParametro" => $arrayParametros['nombreParametro'],
                                                             "estado"          => $arrayParametros['estado']));
        
        if (is_object($objParametroCab))
        { 
            $arrayParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findBy(array("parametroId" => $objParametroCab,
                                                                "estado"      => "Activo",
                                                                "empresaCod"  => $arrayParametros['idEmpresa']));
            
            if ($arrayParametroDet)
            {
                foreach ($arrayParametroDet as $objParametroDet)
                {
                 $arrayEscenarios[] = array('intIdEscenario'     => $objParametroDet->getId(),
                                            'strNombreEscenario' => $objParametroDet->getValor2(),
                                            'strValorEscenario'  => $objParametroDet->getValor1());
                }
            }

        }
        
        return $arrayEscenarios;
    }
    
    
    /*
     * Documentación para función obtenerMontosEscenario2
     * 
     * Obtiene los montos a debitar del escenario 2, para la generación de los débitos.
     *
     * @author: Hector Lozano<hlozano@telconet.ec>
     * @version: 1.0 13-05-2020 
     * @param type $arrayParametros[
     *                              'nombreParametro' : Nombre del parámetro cab
     *                              'estado'          : Estado del parámetro
     *                             ]
     * @return array
     */
    public function obtenerMontosEscenario2($arrayParametros)
    {
        $objParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                           ->findOneBy(array("nombreParametro" => $arrayParametros['nombreParametro'],
                                                             "estado"          => $arrayParametros['estado']));
        
        if (is_object($objParametroCab))
        { 
            $arrayParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findBy(array("parametroId" => $objParametroCab,
                                                                "estado"      => "Activo"));
            
            if ($arrayParametroDet)
            {
                foreach ($arrayParametroDet as $objParametroDet)
                {
                 $arrayMontosEscenario2[] = array('intIdMonto' => $objParametroDet->getId(), 'strValorMonto' => $objParametroDet->getValor1());
                }
            }

        }
        
        return $arrayMontosEscenario2;
    }
    
    /*
     * Documentación para función obtenerCuotasEscenario3
     * 
     * Obtiene el número de cuotas de NDI a debitar del escenario 3, para la generación de los débitos.
     *
     * @author: Hector Lozano<hlozano@telconet.ec>
     * @version: 1.0 17-06-2020 
     * @param type $arrayParametros[
     *                              'nombreParametro' : Nombre del parámetro cab
     *                              'estado'          : Estado del parámetro
     *                             ]
     * @return array
     */
    public function obtenerCuotasEscenario3($arrayParametros)
    {
        $objParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                           ->findOneBy(array("nombreParametro" => $arrayParametros['nombreParametro'],
                                                             "estado"          => $arrayParametros['estado']));
        
        if (is_object($objParametroCab))
        { 
            $arrayParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findBy(array("parametroId" => $objParametroCab,
                                                                "estado"      => "Activo"));
            
            if ($arrayParametroDet)
            {
                foreach ($arrayParametroDet as $objParametroDet)
                {
                 $arrayCuotasEscenario3[] = array('intIdCuota' => $objParametroDet->getId(), 'strValorCuota' => $objParametroDet->getValor1());
                }
            }

        }
        
        return $arrayCuotasEscenario3;
    }
    

}
