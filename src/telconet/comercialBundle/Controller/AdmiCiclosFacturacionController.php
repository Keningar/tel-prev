<?php

namespace telconet\comercialBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\ReturnResponse;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use telconet\schemaBundle\Entity\AdmiCiclo;
use telconet\schemaBundle\Entity\AdmiCicloHistorial;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;

/**
 * AdmiCiclosFacturacionController controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Administración de Ciclos de Facturación
 *
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 22-08-2017
 */
class AdmiCiclosFacturacionController extends Controller
{
   
   /**   
    * @Secure(roles="ROLE_395-1")
    * 
    * Documentación para el método 'indexAction'.        
    * Método que redirecciona a página de administración de Ciclos de Facturación
    * @return render 
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 24-08-2017
    */
    public function indexAction()
    {       
        return $this->render('comercialBundle:AdmiCiclosFacturacion:index.html.twig');
    }  
    
   /**
    * @Secure(roles="ROLE_395-1")
    * 
    * Documentación para el método 'gridListadoCiclosFacturacionAction'
    * Metodo que obtiene los ciclos de Facturacion ADMI_CICLO      
    * @return JsonResponse 
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 28-08-2017       
    */
    public function gridListadoCiclosFacturacionAction()
    {
        $emFinanciero                     = $this->getDoctrine()->getManager("telconet_financiero");
        $arrayParametros                  = array();
        $objRequest                       = $this->getRequest();
        $objSession                       = $objRequest->getSession();
        $serviceUtil                      = $this->get('schema.Util');
        $strIpClient                      = $objRequest->getClientIp();
        $strUsrSesion                     = $objSession->get('user');        
        $arrayParametros['strPrefijo']    = $objSession->get('prefijoEmpresa');
        $arrayParametros['strCodEmpresa'] = $objSession->get('idEmpresa');        
        $arrayParametros['intStart']      = $objRequest->get('start');
        $arrayParametros['intLimit']      = $objRequest->get('limit');                  
        $arrayParametros['serviceRouter'] = $this->container->get('router');  
        $boolEliminacionPermitida         = false;
        

        //MODULO eliminacionCiclosFacturacion - delete
        if(true === $this->get('security.context')->isGranted('ROLE_397-8'))
        {
            $boolEliminacionPermitida = true;
        }
        $arrayParametros['boolEliminacionPermitida'] = $boolEliminacionPermitida;  
        
        $arrayListadoCiclosFacturacion = array();
        $objJsonResponse               = new JsonResponse($arrayListadoCiclosFacturacion);
             
        try
        {        
            $arrayListadoCiclosFacturacion  = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                                           ->getListadoCiclosFacturacion($arrayParametros);
            $objJsonResponse->setData($arrayListadoCiclosFacturacion);
              
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCiclosFacturacionController.gridListadoCiclosFacturacionAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }  
                                
        return $objJsonResponse;                              
    }  

/**
    * @Secure(roles="ROLE_395-1")
    *
    * Documentación para el método 'gridListadoCiclosClientesAction'
    * Metodo para obtener el JSON de Detalle de Clientes por ciclo
    * @return JsonResponse
    *
    * @author Jorge Guerrero <jguerrerop@telconet.ec>
    * @version 1.0 19-10-2017
    */
    public function gridListadoCiclosClientesAction()
    {
        $emFinanciero                     = $this->getDoctrine()->getManager("telconet_financiero");
        $arrayParametros                  = array();
        $objRequest                       = $this->getRequest();
        $objSession                       = $objRequest->getSession();
        $serviceUtil                      = $this->get('schema.Util');
        $strIpClient                      = $objRequest->getClientIp();
        $strUsrSesion                     = $objSession->get('user');
        $objPeticion                      = $this->get('request');
        $arrayParametros['intIdCiclo']    = $objPeticion->get('intIdCiclo');
        $arrayParametros['strPrefijo']    = $objSession->get('prefijoEmpresa');
        $arrayParametros['boolConsultaPermitida'] = false;

        if(true === $this->get('security.context')->isGranted('ROLE_397-8'))
        {
            $arrayParametros['boolConsultaPermitida'] = true;
        }

        $arrayListadoCiclosClientes = array();
        $objJsonResponse               = new JsonResponse($arrayListadoCiclosClientes);

        try
        {
            $arrayListadoCiclosClientes  = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                                        ->getGridDetClientes($arrayParametros);

            $arrayResultClientes= array();
            $booleanBand = true;
            foreach($arrayListadoCiclosClientes as $arrayCiclosCliente)
            {
                if (count($arrayResultClientes) > 0)
                {
                    $intCount = 0;
                    foreach ($arrayResultClientes as $arrayColumna)
                    {
                        if ($arrayColumna['strEstados'] == $arrayCiclosCliente['strEstado'])
                        {
                            $booleanBand = false;
                            if ($arrayCiclosCliente['strDescripcionTipoRol'] == 'Cliente')
                            {
                                $arrayResultClientes[$intCount]['intClientes'] = $arrayCiclosCliente['intCantidad'];
                                $arrayResultClientes[$intCount]['intTotal'] = $arrayResultClientes[$intCount]['intTotal']
                                                                            + $arrayCiclosCliente['intCantidad'];
                            }
                            else
                            {
                                $arrayResultClientes[$intCount]['intPreClientes'] = $arrayCiclosCliente['intCantidad'];
                                $arrayResultClientes[$intCount]['intTotal'] = $arrayResultClientes[$intCount]['intTotal']
                                                                            + $arrayCiclosCliente['intCantidad'];
                            }
                        }
                        $intCount=$intCount+1;
                    }

                    if ($booleanBand)
                    {
                        if ($arrayCiclosCliente['strDescripcionTipoRol'] == 'Cliente')
                        {
                            $arrayResultClientes[]=array('strEstados' => $arrayCiclosCliente['strEstado'],
                                                       'intPreClientes' => 0,
                                                       'intClientes' => $arrayCiclosCliente['intCantidad'],
                                                       'intTotal' => $arrayCiclosCliente['intCantidad']);
                        }
                        else
                        {
                            $arrayResultClientes[]=array('strEstados' => $arrayCiclosCliente['strEstado'],
                                                       'intPreClientes' => $arrayCiclosCliente['intCantidad'],
                                                       'intClientes' => 0,
                                                       'intTotal' => $arrayCiclosCliente['intCantidad']);
                        }
                    }

                    $booleanBand=true;
                }
                else
                {
                    if ($arrayCiclosCliente['strDescripcionTipoRol'] == 'Cliente')
                    {
                        $arrayResultClientes[]=array('strEstados' => $arrayCiclosCliente['strEstado'],
                                                   'intPreClientes' => 0,
                                                   'intClientes' => $arrayCiclosCliente['intCantidad'],
                                                   'intTotal' => $arrayCiclosCliente['intCantidad']);
                    }
                    else
                    {
                        $arrayResultClientes[]=array('strEstados' => $arrayCiclosCliente['strEstado'],
                                                   'intPreClientes' => $arrayCiclosCliente['intCantidad'],
                                                   'intClientes' => 0,
                                                   'intTotal' => $arrayCiclosCliente['intCantidad']);
                    }
                }
            }

            $arrayResultGrid['intTotales']= count($arrayResultClientes);
            $arrayResultGrid['arrayEncontrados'] = $arrayResultClientes;

            $objJsonResponse->setData($arrayResultGrid);
        }
        catch (\Exception $e)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCiclosFacturacionController.gridListadoCiclosClientesAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
        }

        return $objJsonResponse;
    }

   /**
    * @Secure(roles="ROLE_395-1")
    * 
    * Documentación para el método 'gridListadoCiclosFacturacionHistAction'
    * Metodo que obtiene Historico de los ciclos de Facturacion ADMI_CICLO_HISTORIAL     
    * @return JsonResponse 
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 31-08-2017       
    */
    public function gridListadoCiclosFacturacionHistAction()
    {
        $emFinanciero                     = $this->getDoctrine()->getManager("telconet_financiero");
        $arrayParametros                  = array();
        $objRequest                       = $this->getRequest();
        $objSession                       = $objRequest->getSession();
        $serviceUtil                      = $this->get('schema.Util');
        $strIpClient                      = $objRequest->getClientIp();
        $strUsrSesion                     = $objSession->get('user');        
        $arrayParametros['strCodEmpresa'] = $objSession->get('idEmpresa');        
        $arrayParametros['intStart']      = $objRequest->get('start');
        $arrayParametros['intLimit']      = $objRequest->get('limit');                          
                
        $arrayListadoCiclosFacturacionHist = array();
        $objJsonResponse                   = new JsonResponse($arrayListadoCiclosFacturacionHist);
             
        try
        {        
            $arrayListadoCiclosFacturacionHist  = $emFinanciero->getRepository('schemaBundle:AdmiCicloHistorial')
                                                               ->getListadoCiclosFacturacionHist($arrayParametros);
            $objJsonResponse->setData($arrayListadoCiclosFacturacionHist);
              
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCiclosFacturacionController.gridListadoCiclosFacturacionHistAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }  
                                
        return $objJsonResponse;                              
    }  
   /**          
    * Documentación para el método 'validaCiclosFacturacionAjaxAction'
    * 
    * Metodo para Validar si el ciclo de Inicio y Ciclo de Fin de Facturacion son Correctos, 
    * Se Valida que el ciclo ingresado no exista.
    * Se valida un maximo de 30 o 31 dias
    * Se debe considarar el mes de Julio por tener 31 dias y porque su mes consecutivo Agosto tambien posee 31 dias
    * y el año del Sysdate para calcular la diferencia en dias entre el ciclo de inicio y el ciclo Fin
    * @return JsonResponse      
    * 
    * @param  string  $strTxCicloInicio  
    * @param  string  $strTxCicloFin  
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 31-08-2017       
    */
    public function validaCiclosFacturacionAjaxAction()
    {
        $objResponse           = new JsonResponse();
        $emFinanciero          = $this->getDoctrine()->getManager('telconet_financiero');        
        $objPeticion           = $this->get('request');
        $objSession            = $objPeticion->getSession();
        $strTxCicloInicio      = $objPeticion->get('strTxCicloInicio');
        $strTxCicloFin         = $objPeticion->get('strTxCicloFin');           
        $strPermiteIngreso     = "NO";
        $strMesInicio          = '07'; //Mes sobre el cual se Validara es Julio        
        $strAnio               = date('Y'); //Año del Sysdate
        $strTxCicloInicio      = !empty($strTxCicloInicio)?$strTxCicloInicio:"";
        $strTxCicloFin         = !empty($strTxCicloFin)?$strTxCicloFin:"";
        $intNumDiasEntreCiclos = 0;

        //Valido si Ciclo ya Existe
        $arrayParametros                     = array();               
        $arrayParametros['strCodEmpresa']    = $objSession->get('idEmpresa'); 
        $arrayParametros['strTxCicloInicio'] = str_pad($strTxCicloInicio, 2, '0',STR_PAD_LEFT); 
        $arrayParametros['strTxCicloFin']    = str_pad($strTxCicloFin,2,'0',STR_PAD_LEFT); 
        $arrayParametros['arrayEstado']      = array('Activo','Inactivo');         
        $boolExisteCicloFacturacion          = false;
        
        $boolExisteCicloFacturacion          = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                                            ->isCicloFacturacionExistente($arrayParametros);
         
        if(!$boolExisteCicloFacturacion)
        {
            //Valido que el ciclo ingresado posea 30-31 dias
            $intDiferenciaDias = $strTxCicloFin - $strTxCicloInicio;
            if($intDiferenciaDias > 0)
            {
                $strMesFin = $strMesInicio;
            }
            else
            {
                $strMesFin = $strMesInicio + 1;
            }

            $intFeCicloInicio      = mktime(0, 0, 0, $strMesInicio, $strTxCicloInicio, $strAnio);
            $intFeCicloFin         = mktime(0, 0, 0, $strMesFin, $strTxCicloFin, $strAnio);
            $intDiferencia         = $intFeCicloFin - $intFeCicloInicio;
            $intNumDiasEntreCiclos = $intDiferencia / (60 * 60 * 24);
            
            if($intNumDiasEntreCiclos == 30 || $intNumDiasEntreCiclos == 31)
            {
                $strPermiteIngreso = 'SI';
            }
        }
        
        $objResponse->setData( array('strPermiteIngreso'          => $strPermiteIngreso,
                                     'boolExisteCicloFacturacion' => $boolExisteCicloFacturacion,
                                     'intNumDiasEntreCiclos'      => $intNumDiasEntreCiclos) );
        
        return $objResponse;
    }
     /**    
    * @Secure(roles="ROLE_395-1")
    * 
    * Documentación para el método 'guardarCicloFacturacionAjaxAction'.
    * 
    * Funcion para guardar Ciclo de Facturacion
    * Consideraciones:
    * Con la creacion de un Nuevo Ciclo Activo, se deberán Inactivar los ciclos existentes que se encuentren en estado Activo.
    * Se deberá crear Historial de Creacion de Ciclo Nuevo e Historial de Inactivacion de ciclo.   
    * Se Autogenera el nombre del Ciclo en base al ciclo de inicio y al ciclo fin.
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 01-09-2017
    *
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.1 06-12-2017 - Se agrega el campo CODIGO al insertar el CICLO.
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.2 14-06-2022 - Se agrega el ingreso el detalle de parametro que marca a un ciclo de facturacion como ciclo especial.
    *                           Se agrega el ingreso de los detalles de parametros correspondiente a los grupos para procesamiento de 
    *                           Promociones para el ciclo de facturacion nuevo en base a la plantilla existente para ciclo1. 
    *
    * @param  string  $strTxDescripcionCiclo  
    * @param  string  $strTxCicloInicio  
    * @param  string  $strTxCicloFin  
    * 
    * @return $objJsonResponse
    */
    public function guardarCicloFacturacionAjaxAction()
    {     
        $objJsonResponse       = new JsonResponse();
        //Obtiene parametros enviados desde el ajax
        $objRequest            = $this->get('request');
        $strTxDescripcionCiclo = $objRequest->get('strTxDescripcionCiclo');        
        $strTxCicloInicio      = $objRequest->get('strTxCicloInicio');
        $strTxCicloFin         = $objRequest->get('strTxCicloFin');
        $strTxCicloEspecial    = $objRequest->get('strTxCicloEspecial');
        $strNombreParametro    = 'PROM_PARAMETROS';
        $strDescripcion        = 'PROM_CICLOS_FACTURACION';   
        $strCicloPlantilla     = 'CICLO1';
        $strIpClient           = $objRequest->getClientIp();
        $objSession            = $objRequest->getSession();
        $strUsrCreacion        = $objSession->get('user');
        $strCodEmpresa         = $objSession->get('idEmpresa');  
        $serviceUtil           = $this->get('schema.Util');  
        $emFinanciero          = $this->getDoctrine()->getManager('telconet_financiero');
        $emGeneral             = $this->getDoctrine()->getManager("telconet_general");
        
        $strMesInicio          = '07'; //Mes sobre el cual se guardara ciclo es Julio
        $strAnio               = date('Y'); //Año del Sysdate
        $intDiferenciaDias     = ($strTxCicloFin - $strTxCicloInicio)+1;
        
        $strFeCicloInicio      = date_create($strAnio. '-' . $strMesInicio . '-' .$strTxCicloInicio );
        if($intDiferenciaDias == 30 || $intDiferenciaDias == 31)
        {                                    
            $strFeCicloFin    = date_create($strAnio. '-' . $strMesInicio . '-' .$strTxCicloFin);                  
        }
        else
        {                                 
            $strFeCicloFin = ($strAnio. "-" . $strMesInicio . "-" . $strTxCicloFin);
            $strFeCicloFin = date("Y-m-d", strtotime("+1 months $strFeCicloFin"));            
            $strFeCicloFin = date_create($strFeCicloFin);
        }       
        $emFinanciero->getConnection()->beginTransaction();
        $emGeneral->getConnection()->beginTransaction();
        try
        {   
            $arrayParametros                  = array();
            $arrayParametros['strCodEmpresa'] = $strCodEmpresa;       
            $arrayParametros['intStart']      = $objRequest->get('start');
            $arrayParametros['intLimit']      = $objRequest->get('limit'); 
            $arrayResultado                   = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                                             ->getResultadoCiclosFacturacion($arrayParametros);            
            $intTotal                         = !empty($arrayResultado['intTotal'])?$arrayResultado['intTotal']:0;      
            //SE AGREGA EL CÓDIGO DEL CICLO
            $strCodigo                        = 'CICLO' . strval($intTotal + 1);
            $strNumRomano                     = $this->to_roman($intTotal+1);
            $strNombreCiclo                   = 'Ciclo ('.$strNumRomano.') - '. $strTxCicloInicio. ' al '. $strTxCicloFin;
               
            //Inactivo Ciclos de Facturacion que se encuentren en estado Activo
             $objAdmiCicloInactivo = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                                  ->findBy(array("empresaCod"    => $strCodEmpresa,
                                                                 "estado"        => "Activo"));   
             foreach($objAdmiCicloInactivo as $objAdmiCicloInactivo)
             {                 
                 //Inserto Historial por Inactivacion de Ciclos que se encontraban Activos            
                 $objAdmiCicloHistorial = new AdmiCicloHistorial();                 
                 $objAdmiCicloHistorial->setCicloId($objAdmiCicloInactivo);
                 $objAdmiCicloHistorial->setNombreCiclo($objAdmiCicloInactivo->getNombreCiclo());
                 $objAdmiCicloHistorial->setFeInicio($objAdmiCicloInactivo->getFeInicio());
                 $objAdmiCicloHistorial->setFeFin($objAdmiCicloInactivo->getFeFin());
                 $objAdmiCicloHistorial->setEstado('Inactivo');
                 $objAdmiCicloHistorial->setObservacion('Se actualiza Ciclo de Facturacion: <br>' .
                                'Estado Anterior: ' . $objAdmiCicloInactivo->getEstado() . '<br>' .
                                'Estado Nuevo: ' . $objAdmiCicloHistorial->getEstado().
                                '<br>');
                 $objAdmiCicloHistorial->setFeCreacion(new \DateTime('now'));
                 $objAdmiCicloHistorial->setUsrCreacion($strUsrCreacion);
                 $objAdmiCicloHistorial->setIpCreacion($strIpClient);
                 $objAdmiCicloHistorial->setEmpresaCod($objAdmiCicloInactivo->getEmpresaCod());                 
                 $emFinanciero->persist($objAdmiCicloHistorial);
                 
                 //Actualizo a Inactivo el estado del ciclo
                 $objAdmiCicloInactivo->setEstado('Inactivo');
                 $emFinanciero->persist($objAdmiCicloInactivo);
                 
             }                        
            //Inserto Nuevo Ciclo de Facturacion en estado Activo            
            $objAdmiCiclo = new AdmiCiclo();
            $objAdmiCiclo->setNombreCiclo($strNombreCiclo);
            $objAdmiCiclo->setFeInicio($strFeCicloInicio);
            $objAdmiCiclo->setFeFin($strFeCicloFin);
            $objAdmiCiclo->setObservacion($strTxDescripcionCiclo);
            $objAdmiCiclo->setFeCreacion(new \DateTime('now'));
            $objAdmiCiclo->setUsrCreacion($strUsrCreacion);
            $objAdmiCiclo->setIpCreacion($strIpClient);
            $objAdmiCiclo->setEmpresaCod($strCodEmpresa);
            $objAdmiCiclo->setEstado('Activo');
            $objAdmiCiclo->setCodigo($strCodigo);
            $emFinanciero->persist($objAdmiCiclo);
            
            //Inserto Historial por creacion de nuevo Ciclo
            $objNuevoCicloHistorial = new AdmiCicloHistorial();                 
            $objNuevoCicloHistorial->setCicloId($objAdmiCiclo);
            $objNuevoCicloHistorial->setNombreCiclo($objAdmiCiclo->getNombreCiclo());
            $objNuevoCicloHistorial->setFeInicio($objAdmiCiclo->getFeInicio());
            $objNuevoCicloHistorial->setFeFin($objAdmiCiclo->getFeFin());
            $objNuevoCicloHistorial->setEstado($objAdmiCiclo->getEstado());
            $objNuevoCicloHistorial->setObservacion('Se crea nuevo Ciclo de Facturación');
            $objNuevoCicloHistorial->setFeCreacion(new \DateTime('now'));
            $objNuevoCicloHistorial->setUsrCreacion($strUsrCreacion);
            $objNuevoCicloHistorial->setIpCreacion($strIpClient);
            $objNuevoCicloHistorial->setEmpresaCod($objAdmiCiclo->getEmpresaCod());                 
            $emFinanciero->persist($objNuevoCicloHistorial);
                                         
            //Ingreso el detalle de parametro que marca a un ciclo de facturacion como ciclo especial.
            if ($strTxCicloEspecial =='S')
            {
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                 ->findOneBy(array('nombreParametro' => $strNombreParametro,
                                                                   'estado'          => 'Activo'));
                if(is_object($objAdmiParametroCab))
                {
                    $objAdmiParametroDet = new AdmiParametroDet();
                    $objAdmiParametroDet->setDescripcion($strDescripcion);
                    $objAdmiParametroDet->setEstado('Activo');
                    $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                    $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                    $objAdmiParametroDet->setIpCreacion($strIpClient);
                    $objAdmiParametroDet->setIpUltMod($strIpClient);
                    $objAdmiParametroDet->setParametroId($objAdmiParametroCab);
                    $objAdmiParametroDet->setUsrCreacion($strUsrCreacion);
                    $objAdmiParametroDet->setUsrUltMod($strUsrCreacion);
                    $objAdmiParametroDet->setValor1($strCodigo);
                    $objAdmiParametroDet->setEmpresaCod($strCodEmpresa);
                    $emGeneral->persist($objAdmiParametroDet);            
                }
            }
            //Obtengo e inserto los detalles de parametros correspondiente a los grupos para procesamiento de Promociones para el ciclo de
            //facturacion nuevo en base a la plantilla existente para ciclo1
            $objAdmiParamPromocionCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                  ->findOneBy(array('nombreParametro' => 'MAPEO DE PROMOCIONES MENSUAL',
                                                                    'estado'          => 'Activo'
                                                             ));
            if(is_object($objAdmiParamPromocionCab))
            {               
                $objAdmiCicloPlantilla = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                                      ->findOneBy(array("empresaCod" => $strCodEmpresa,
                                                                        "codigo"     => $strCicloPlantilla)); 
                if(is_object($objAdmiCicloPlantilla))
                {                                    
                    $arrayAdmiParamPromocionDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('MAPEO DE PROMOCIONES MENSUAL',
                                                                  'COMERCIAL','MAPEO PROMOCIONES','',
                                                                  '', '', '', '', $objAdmiCicloPlantilla->getId(), $strCodEmpresa);
            
                    foreach($arrayAdmiParamPromocionDet as $arrayParamDet)
                    {                         
                        $objAdmiParametroDetPromo = new AdmiParametroDet();
                        $objAdmiParametroDetPromo->setParametroId($objAdmiParamPromocionCab);
                        $objAdmiParametroDetPromo->setDescripcion(str_replace($strCicloPlantilla, $strCodigo, $arrayParamDet["descripcion"]));
                        $objAdmiParametroDetPromo->setEstado('Activo');
                        $objAdmiParametroDetPromo->setFeCreacion(new \DateTime('now'));
                        $objAdmiParametroDetPromo->setFeUltMod(new \DateTime('now'));
                        $objAdmiParametroDetPromo->setIpCreacion($strIpClient);
                        $objAdmiParametroDetPromo->setIpUltMod($strIpClient);                    
                        $objAdmiParametroDetPromo->setUsrCreacion($strUsrCreacion);
                        $objAdmiParametroDetPromo->setUsrUltMod($strUsrCreacion);
                        $objAdmiParametroDetPromo->setValor1($arrayParamDet["valor1"]);
                        $objAdmiParametroDetPromo->setValor2($arrayParamDet["valor2"]);
                        $objAdmiParametroDetPromo->setValor3($arrayParamDet["valor3"]);
                        $objAdmiParametroDetPromo->setValor4($arrayParamDet["valor4"]);
                        $objAdmiParametroDetPromo->setValor5($objAdmiCiclo->getId());
                        $objAdmiParametroDetPromo->setValor6($arrayParamDet["valor6"]);
                        $objAdmiParametroDetPromo->setValor7($arrayParamDet["valor7"]);
                        $objAdmiParametroDetPromo->setObservacion($arrayParamDet["observacion"]);
                        $objAdmiParametroDetPromo->setEmpresaCod($strCodEmpresa);
                        $emGeneral->persist($objAdmiParametroDetPromo);                                    
                    }
                }
            }
            $emFinanciero->flush(); 
            $emGeneral->flush();           
            $emFinanciero->getConnection()->commit();    
            $emGeneral->getConnection()->commit(); 
            
            $objJsonResponse->setData('OK');  
        } 
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCiclosFacturacionController.guardarCicloFacturacionAjaxAction',
                                      $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpClient
                                     );               
           $objJsonResponse->setData('Error');  
           if ($emFinanciero->getConnection()->isTransactionActive())
           {
               $emFinanciero->getConnection()->rollback();
           }                            
           $emFinanciero->getConnection()->close();  
           
           if ($emGeneral->getConnection()->isTransactionActive())
           {
               $emGeneral->getConnection()->rollback();
           }                            
           $emGeneral->getConnection()->close();   
        }                                                  
        return $objJsonResponse;
    }
  
    /**             
    * Documentación para el método 'to_roman'.
    * 
    * Descripcion: Metodo encargado de convertir un numero entero en Romano
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 04-09-2017       
    * 
    * @param integer $intNum     
    * return string
    */
    public function to_roman($intNum)
    {
        if($intNum < 0 || $intNum > 9999)
        {
            return $intNum;
        }
        $arrayOnes = array(1 => "I",
                           2 => "II",
                           3 => "III",
                           4 => "IV", 
                           5 => "V", 
                           6 => "VI",
                           7 => "VII",
                           8 => "VIII", 
                           9 => "IX");
        $arrayTens = array(1 => "X",
                           2 => "XX",
                           3 => "XXX", 
                           4 => "XL",
                           5 => "L",
                           6 => "LX",
                           7 => "LXX",
                           8 => "LXXX",
                           9 => "XC");
        $arrayHund = array(1 => "C",
                           2 => "CC", 
                           3 => "CCC",
                           4 => "CD",
                           5 => "D",
                           6 => "DC",
                           7 => "DCC", 
                           8 => "DCCC",
                           9 => "CM");
        $arrayThou = array(1 => "M",
                           2 => "MM", 
                           3 => "MMM", 
                           4 => "MMMM", 
                           5 => "MMMMM", 
                           6 => "MMMMMM", 
                           7 => "MMMMMMM",
                           8 => "MMMMMMMM",
                           9 => "MMMMMMMMM");
        $intOnes      = $intNum % 10;
        $intTens      = ($intNum - $intOnes) % 100;
        $intHundreds  = ($intNum - $intTens - $intOnes) % 1000;
        $intThou      = ($intNum - $intHundreds - $intTens - $intOnes) % 10000;
        $intTens      = $intTens / 10;
        $intHundreds  = $intHundreds / 100;
        $intThou      = $intThou / 1000;
        $strRomanNum  = '';
        if($intThou)
        {
            $strRomanNum .= $arrayThou[$intThou];
        }
        if($intHundreds)
        {
            $strRomanNum .= $arrayHund[$intHundreds];
        }
        if($intTens)
        {
            $strRomanNum .= $arrayTens[$intTens];
        }
        if($intOnes)
        {
            $strRomanNum .= $arrayOnes[$intOnes];
        }
        return $strRomanNum;
    }

     /**          
    * Documentación para el método 'validaCicloAEliminarAjaxAction'
    * 
    * Metodo para Validar si un ciclo puede ser Eliminado, solo se podrá eliminar un Ciclo que no se encuentre asignado a ningun Cliente.
    * @return JsonResponse      
    * 
    * @param  string  $intIdCiclo      
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 05-09-2017       
    */
    public function validaCicloAEliminarAjaxAction()
    {
        $objResponse                   = new JsonResponse();        
        $strPermiteEliminar            = "SI";
        $emFinanciero                  = $this->getDoctrine()->getManager("telconet_financiero");   
        
        $arrayParametros               = array();
        $objRequest                    = $this->getRequest();
        $objSession                    = $objRequest->getSession();
        $intIdCiclo                    = $objRequest->get('intIdCiclo');
        $arrayParametros['strPrefijo'] = $objSession->get('prefijoEmpresa'); 
        $arrayParametros['intIdCiclo'] = $intIdCiclo; 
        $intCantidadClientesEnCiclo    = 0;
        
        $intCantidadClientesEnCiclo = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                                   ->getTotalClientesAsignadosACicloFact($arrayParametros);
        if($intCantidadClientesEnCiclo > 0)
        {
            $strPermiteEliminar  = "NO";
        }
        $objResponse->setData(array('strPermiteEliminar'         => $strPermiteEliminar,
                                    'intCantidadClientesEnCiclo' => $intCantidadClientesEnCiclo));
        
        return $objResponse;
    }
    
     /**          
    * Documentación para el método 'mensajesValidacionAjaxAction'
    * 
    * Metodo para mensajes utilizados para la activacion del ciclo bajo demanda.
    * @return JsonResponse      
    *     
    * 
    * @author Ivan Romero <icromero@telconet.ec>
    * @version 1.0 16-03-2022       
    */
    public function mensajesValidacionAjaxAction()
    {
        $emComercial                      = $this->getDoctrine()->getManager();
        $arrayParametros                  = array();
        $objRequest                       = $this->getRequest();
        $objSession                       = $objRequest->getSession();
        $strUsrSesion                     = $objSession->get('user');        
        $strCodEmpresa                    = $objSession->get('idEmpresa');        
        
        $strParametroCabActivacionCiclo   = 'ACTIVACION_CICLOS_FACTURACION';
        $strParametroEstado               = 'Activo';
        $strModulo                        = 'COMERCIAL';
        $strDescripcionParametroMensajes  = 'MENSAJES_DEL_PROCESO';

        //obtener mensaje de error parametrizado
        $objParametrosCabActivacionCiclo = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
        ->findOneBy(array('nombreParametro'  => $strParametroCabActivacionCiclo,
                            'estado'           => $strParametroEstado,
                            'modulo'           => $strModulo));

        $objParametrosDetCancelacion = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
        ->findOneBy(array('parametroId'  => $objParametrosCabActivacionCiclo->getId(),
                            'estado'       => $strParametroEstado,
                            'empresaCod'       => $strCodEmpresa,
                            'descripcion'       => $strDescripcionParametroMensajes));

        $arrayParametros['strMensajeConfirmarActivacion'] = $objParametrosDetCancelacion->getValor1();
        $arrayParametros['strMensajeConfirmacionActivacion'] = $objParametrosDetCancelacion->getValor2();
        $arrayParametros['strMensajeErrorActivacion'] = $objParametrosDetCancelacion->getValor3();

        
        try
        {        
            $objJsonResponse               = new JsonResponse();
            $objJsonResponse->setData(array('strMensajeConfirmarActivacion'         => $arrayParametros['strMensajeConfirmarActivacion'],
                                            'strMensajeConfirmacionActivacion' => $arrayParametros['strMensajeConfirmacionActivacion'],
                                            'strMensajeErrorActivacion' => $arrayParametros['strMensajeErrorActivacion']));
              
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCiclosFacturacionController.mensajesValidacionAjaxAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }  
                                
        return $objJsonResponse; 
    }
    /**     
    * @Secure(roles="ROLE_397-8")
    * 
    * Documentación para el método 'eliminarCiclosFacturacionAjaxAction'.
    * 
    * Descripcion: Metodo encargado de eliminar Ciclo de Facturacion, solo se podrán eliminar ciclos en estado Inactivo
    * y que no se encuentren atados a ningun Cliente
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 01-09-2017       
    * 
    * @param integer $intIdCiclo        
    * @return $objJsonResponse   
    */
    public function eliminarCiclosFacturacionAjaxAction($intIdCiclo)
    {           
        $objJsonResponse  = new JsonResponse();
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();  
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $objRequest->getClientIp();
        $strUsrSesion     = $objSession->get('user');           
        $intIdCiclo       = $objRequest->get('intIdCiclo');
        $emFinanciero     = $this->getDoctrine()->getManager("telconet_financiero");
        $objAdmiCiclo     = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')->find($intIdCiclo); 
        
        if ( !is_object($objAdmiCiclo) ) 
        {
            throw $this->createNotFoundException('No se encontró Ciclo de Facturación a Eliminar');
        }                                      
        try
        {
            // Elimino Ciclo e inserto Historial de Eliminacion           
            $objAdmiCicloHistorial = new AdmiCicloHistorial();
            $objAdmiCicloHistorial->setCicloId($objAdmiCiclo);
            $objAdmiCicloHistorial->setNombreCiclo($objAdmiCiclo->getNombreCiclo());
            $objAdmiCicloHistorial->setFeInicio($objAdmiCiclo->getFeInicio());
            $objAdmiCicloHistorial->setFeFin($objAdmiCiclo->getFeFin());
            $objAdmiCicloHistorial->setEstado('Eliminado');
            $objAdmiCicloHistorial->setObservacion('Se actualiza Ciclo de Facturacion: <br>' .
                'Estado Anterior: ' . $objAdmiCiclo->getEstado() . '<br>' .
                'Estado Nuevo: ' . $objAdmiCicloHistorial->getEstado() .
                '<br>');
            $objAdmiCicloHistorial->setFeCreacion(new \DateTime('now'));
            $objAdmiCicloHistorial->setUsrCreacion($strUsrSesion);
            $objAdmiCicloHistorial->setIpCreacion($strIpClient);
            $objAdmiCicloHistorial->setEmpresaCod($objAdmiCiclo->getEmpresaCod());
            $emFinanciero->persist($objAdmiCicloHistorial);

            //Actualizo a Eliminado el estado del ciclo
            $objAdmiCiclo->setEstado('Eliminado');
            $emFinanciero->persist($objAdmiCiclo);
            $emFinanciero->flush(); 
            $emFinanciero->getConnection()->commit();           
            $objJsonResponse->setData('OK');  
        }
        catch (\Exception $e)
        {   
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCiclosFacturacionController.eliminarCicloFacturacionAjaxAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );            
           $objJsonResponse->setData('Error');  
           if ($emFinanciero->getConnection()->isTransactionActive())
           {
               $emFinanciero->getConnection()->rollback();
           }                            
           $emFinanciero->getConnection()->close();            
        }  
        return $objJsonResponse;       
    }    



    /**     
    * @Secure(roles="ROLE_395-1")
    * 
    * Documentación para el método 'activarCiclosFacturacionAjaxAction'.
    * 
    * Descripcion: Metodo encargado de activar Ciclo de Facturacion, solo se podrán activar ciclos en estado Inactivo
    * 
    * @author Ivan Romero <icromero@telconet.ec>
    * @version 1.0 15-03-2022       
    * 
    * @param integer $intIdCiclo        
    * @return $objJsonResponse   
    */
    public function activarCiclosFacturacionAjaxAction($intIdCiclo)
    {           
        $objJsonResponse  = new JsonResponse();
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();  
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $objRequest->getClientIp();
        $strUsrSesion     = $objSession->get('user');           
        $intIdCiclo       = $objRequest->get('intIdCiclo');
        $emFinanciero     = $this->getDoctrine()->getManager("telconet_financiero");
        $objAdmiCiclo     = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')->find($intIdCiclo); 
        $strEstadoActivo  = "Activo";
        $strCodEmpresa    = $objSession->get('idEmpresa'); 
    
        $emFinanciero->getConnection()->beginTransaction();
        try
        {   
            //Inactivo Ciclos de Facturacion que se encuentren en estado Activo
             $objAdmiCicloInactivo = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                                  ->findBy(array("empresaCod"    => $strCodEmpresa,
                                                                 "estado"        => $strEstadoActivo));   
             foreach($objAdmiCicloInactivo as $objAdmiCicloInactivo)
             {                 
                 //Inserto Historial por Inactivacion de Ciclos que se encontraban Activos            
                 $objAdmiCicloHistorial = new AdmiCicloHistorial();                 
                 $objAdmiCicloHistorial->setCicloId($objAdmiCicloInactivo);
                 $objAdmiCicloHistorial->setNombreCiclo($objAdmiCicloInactivo->getNombreCiclo());
                 $objAdmiCicloHistorial->setFeInicio($objAdmiCicloInactivo->getFeInicio());
                 $objAdmiCicloHistorial->setFeFin($objAdmiCicloInactivo->getFeFin());
                 $objAdmiCicloHistorial->setEstado('Inactivo');
                 $objAdmiCicloHistorial->setObservacion('Se actualiza Ciclo de Facturacion: <br>' .
                                'Estado Anterior: ' . $objAdmiCicloInactivo->getEstado() . '<br>' .
                                'Estado Nuevo: ' . $objAdmiCicloHistorial->getEstado().
                                '<br>');
                 $objAdmiCicloHistorial->setFeCreacion(new \DateTime('now'));
                 $objAdmiCicloHistorial->setUsrCreacion($strUsrSesion);
                 $objAdmiCicloHistorial->setIpCreacion($strIpClient);
                 $objAdmiCicloHistorial->setEmpresaCod($objAdmiCicloInactivo->getEmpresaCod());                 
                 $emFinanciero->persist($objAdmiCicloHistorial);
                 
                 //Actualizo a Inactivo el estado del ciclo
                 $objAdmiCicloInactivo->setEstado('Inactivo');
                 $emFinanciero->persist($objAdmiCicloInactivo);
                 
             }                        
            //Cambio Ciclo de Facturacion de estado eliminado a un estado Activo 
            $objAdmiCiclo->setEstado('Activo');
            $emFinanciero->persist($objAdmiCiclo);
            
            //Inserto Historial por activacion de ciclo
            $objNuevoCicloHistorial = new AdmiCicloHistorial();                 
            $objNuevoCicloHistorial->setCicloId($objAdmiCiclo);
            $objNuevoCicloHistorial->setNombreCiclo($objAdmiCiclo->getNombreCiclo());
            $objNuevoCicloHistorial->setFeInicio($objAdmiCiclo->getFeInicio());
            $objNuevoCicloHistorial->setFeFin($objAdmiCiclo->getFeFin());
            $objNuevoCicloHistorial->setEstado($objAdmiCiclo->getEstado());
            $objNuevoCicloHistorial->setObservacion('Se activa Ciclo de Facturación');
            $objNuevoCicloHistorial->setFeCreacion(new \DateTime('now'));
            $objNuevoCicloHistorial->setUsrCreacion($strUsrSesion);
            $objNuevoCicloHistorial->setIpCreacion($strIpClient);
            $objNuevoCicloHistorial->setEmpresaCod($objAdmiCiclo->getEmpresaCod());                 
            $emFinanciero->persist($objNuevoCicloHistorial);
                 
            $emFinanciero->flush(); 
            $emFinanciero->getConnection()->commit();           
            $objJsonResponse->setData('OK');  
        } 
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCiclosFacturacionController.activarCiclosFacturacionAjaxAction',
                                      $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpClient
                                     );               
           $objJsonResponse->setData('Error');  
           if ($emFinanciero->getConnection()->isTransactionActive())
           {
               $emFinanciero->getConnection()->rollback();
           }                            
           $emFinanciero->getConnection()->close();            
        }                                                  
        return $objJsonResponse;       
    }
}
