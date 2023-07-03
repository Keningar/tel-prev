<?php

/**
 * Controlador utilizado para las transacciones en la pantalla de los casos de ECUCERT
 * 
 * @author Nestor Naula         <nnaulal@telconet.ec>
 * @version 1.0 09-02-2019
 *
 */
namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoIncidenciaDet;
use telconet\schemaBundle\Entity\InfoIncidenciaDetHist;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Form\AdmiPlantillaType;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

class CasoEcucertController extends controller 
{  

    /**
     * indexAction - Función que llama la pantalla para visualizar los casos de ECUCERT
     *
     * @Secure(roles="ROLE_434-1")
     * 
     * @return render
     *
     * @author Nestor Naula   <nnaulal@telconet.ec>
     * @version 1.0 09-02-2019
     * 
     * @author Nestor Naula   <nnaulal@telconet.ec>
     * @version 1.1 10-08-2019 -  Se le agrega la validación del rol (ROLE_434-1) para la visualización del módulo de ECUCERT.
     * @since 1.0
     * 
     */
    public function indexAction() 
    {        
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        
        $emSeguridad    = $this->getDoctrine()->getManager("telconet_seguridad");    
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("197", "1");
        
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$objSession->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());   

        return $this->render('soporteBundle:casoEcucert:index.html.twig', array(
                                                                                'item'  => $entityItemMenu
                                                                            )
                            );        
                 
    }
    
    /**
     * Buscar la información de los casos de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 07-03-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 11-08-2019 - Se agrega filtro para la busqueda del login del cliente
     * @since 1.0
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.2 08-04-2020 - Se agrega el ipController, puerto, tipo de usuario
     * y número de tarea para el filtro
     * @since 1.1
     * 
     */
    public function buscarCasosEcucertAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $serviceUtil        = $this->get('schema.Util');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strUsr             = $objRequest->getSession()->get('user');
        $strUsrIp           = $objRequest->getClientIp();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $strBandera1        = "";
        $strBandera2        = "";
        $strBandera3        = "";
       
        $arrayResultado     = array();
        $strFeEmisionDesde  = $objRequest->get('feEmisionDesde');
        $strFeEmisionHasta  = $objRequest->get('feEmisionHasta');
        $strNumeroCaso      = $objRequest->get('numeroCaso');
        $strEstadoInci      = $objRequest->get('estadoInci');
        $strNoTicket        = $objRequest->get('noTicket');
        $strIpAddressFil    = $objRequest->get('ipAddressFil');
        $strSubEstadoInci   = $objRequest->get('subEstadoInci'); 
        $strPrioridadInci   = $objRequest->get('prioridadInci'); 
        $strEstadoGestion   = $objRequest->get('estadoGestion'); 
        $strNotificaInci    = $objRequest->get('notificaInci');  
        $strCategoria       = $objRequest->get('categoria'); 
        $strCanton          = $objRequest->get('canton');
        $strStart           = $objRequest->get('start');
        $strLimit           = $objRequest->get('limit');
        $strTipoEvento      = $objRequest->get('tipoEvento');
        $intIdLogin         = $objRequest->get('login');
        
        $strIpControllerFil   =  $objRequest->get('ipControllerFil');
        $strNumeroTareaFil    =  $objRequest->get('numeroTareaFil');
        $strPuertoControl     =  $objRequest->get('puertoControl');
        $strIipoCliente       =  $objRequest->get('tipoCliente');

        $strPrimerEstGest   = "";
        $strSegundoEstGest  = "";
        $strTercerEstGest   = "";
        
        
        $strEstado                  = "Activo";
        $strDescripcionEstadoInc    = "PARAMETROS QUE NO VERIFICAN VULNERABILIDAD";
        $strDescripcionEstadoGest   = "PARAMETROS PARA ESTADO INCIDENCIA";
        $arrayParamEcucert          = array(
                                        'nombreParametro' => "PARAMETROS_ECUCERT",
                                        'estado'          => $strEstado
                                    );
        
        $entityParametroCab         = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneByNombreParametro($arrayParamEcucert);
             
        $intIdParametrosECU = 0;
        if( isset($entityParametroCab) && !empty($entityParametroCab) )
        {
            $intIdParametrosECU = $entityParametroCab->getId();
        }

        $arrayParametrosDet  = array( 
                                    'estado'      => $strEstado, 
                                    'parametroId' => $intIdParametrosECU,
                                    'descripcion' => $strDescripcionEstadoInc
                                );

        $objParametroDetEstadoInc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosDet);

        if( isset($objParametroDetEstadoInc) && !empty($objParametroDetEstadoInc) )
        {
            $strCategoriaVulnerable      = $objParametroDetEstadoInc->getValor1() ? $objParametroDetEstadoInc->getValor1() : '';
        }

        $arrayParametrosDetGestion  = array( 
                                        'estado'      => $strEstado, 
                                        'parametroId' => $intIdParametrosECU,
                                        'descripcion' => $strDescripcionEstadoGest
        );

        $objParametroDetEstadoGest = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosDetGestion);

        if( isset($objParametroDetEstadoGest) && !empty($objParametroDetEstadoGest) )
        {
            $strPrimerEstGest      = $objParametroDetEstadoGest->getValor2() ? $objParametroDetEstadoGest->getValor2() : '';
            $strSegundoEstGest     = $objParametroDetEstadoGest->getValor3() ? $objParametroDetEstadoGest->getValor3() : '';
            $strTercerEstGest      = $objParametroDetEstadoGest->getValor5() ? $objParametroDetEstadoGest->getValor5() : '';
        }

        if( !empty($strNoTicket) || 
            !empty($strTipoEvento) || 
            !empty($strFeEmisionDesde) || 
            !empty($strFeEmisionHasta) || 
            !empty($strNumeroCaso))
        {
            $strBandera1 = "INGRESO";
        }

        if( !empty($strEstadoInci) || 
            !empty($strNoTicket) || 
            !empty($strIpAddressFil) || 
            !empty($strSubEstadoInci)  || 
            !empty($strPrioridadInci) )
        {
            $strBandera2 = "INGRESO";
        }

        if(!empty($strEstadoGestion)  || 
            !empty($strNotificaInci) || 
            !empty($strCategoria) || 
            !empty($strCanton) || 
            !empty($strTipoEvento) )
        {
            $strBandera3 = "INGRESO";
        }

        if(!empty($strIpControllerFil)  || 
            !empty($strNumeroTareaFil) || 
            !empty($strPuertoControl) || 
            !empty($strIipoCliente)  )
        {
            $strBandera4 = "INGRESO";
        }

        try
        {
            if( !empty($strBandera1) || 
                !empty($strBandera2 )||
                !empty($strBandera3 )||
                !empty($strBandera4 )||
                !empty($intIdLogin)
            )
            {
                if(!empty($intIdLogin))
                {
                    $entityInfoPunto    = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                      ->findOneById($intIdLogin);
                    $strLogin           = $entityInfoPunto->getLogin();
                }

                $arrayParametros    = array('intIdEmpresa'      => $intIdEmpresa,
                                            'strFeEmisionDesde' => $strFeEmisionDesde,
                                            'strFeEmisionHasta' => $strFeEmisionHasta,
                                            'strNumeroCaso'     => $strNumeroCaso,
                                            'strEstadoInci'     => $strEstadoInci,
                                            'strNoTicket'       => $strNoTicket,
                                            'strIpAddressFil'   => $strIpAddressFil,
                                            'strSubEstadoInci'  => $strSubEstadoInci, 
                                            'strPrioridadInci'  => $strPrioridadInci,
                                            'strEstadoGestion'  => $strEstadoGestion, 
                                            'strNotificaInci'   => $strNotificaInci,
                                            'strCategoria'      => $strCategoria, 
                                            'strCanton'         => $strCanton,
                                            'strTipoEvento'     => $strTipoEvento,
                                            'strLogin'          => $strLogin,
                                            'strIpControllerFil'=> $strIpControllerFil, 
                                            'strNumeroTareaFil' => $strNumeroTareaFil,
                                            'strPuertoControl'  => $strPuertoControl,
                                            'strIipoCliente'    => $strIipoCliente
                );
        
                
                $entityInfoInicidencia = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')
                                                    ->getTodosLosRegistrosPorEmpresa($arrayParametros);

                foreach ($entityInfoInicidencia as $arrayIncidencia)
                {
                    if(strtoupper($strCategoriaVulnerable) == strtoupper($arrayIncidencia['categoria']) )
                    {
                        $arrayIncidencia['esconder'] = 1;
                    }
                    else
                    {
                        $arrayIncidencia['esconder'] = 0;
                    }
                    if($strPrimerEstGest == $arrayIncidencia['estadoIncEcucert'])
                    {
                        $arrayIncidencia['siguienteEstadoGestion'] = $strSegundoEstGest;
                    }
                    else
                    {
                        $arrayIncidencia['siguienteEstadoGestion'] = $strTercerEstGest;
                    }
                    
                    array_push($arrayResultado,$arrayIncidencia);
                }
                $arrayRepuestaLimitante = array_slice($arrayResultado, $strStart, $strLimit);
            }
            $arrayResultadoRegistro = array('total'        =>count($entityInfoInicidencia),
                                            'encontrados'  =>$arrayRepuestaLimitante );
        }
        catch(\Exception $ex)
        { 
            $strResultado = $ex->getMessage();
            $arrayResultadoRegistro = array('error'        =>"Error al procesar búsqueda. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarCasosEcucert', 
                                        $strResultado,
                                        $strUsr,
                                        $strUsrIp); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;         
    }
    
    /**
     * Verificar si aun existe vulnerabilidad en los casos de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 11-03-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 04-05-2020 - Se agrega nuevos parámetros para validar vulnerabilidad de la IP
     *                           agregando el número de ticket, puerto y el id de la incidencia
     * @since 1.0
     * 
     */
    public function verificarVulnerabilidadEcucertAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');

        $strWsAppName       = 'APP.CERT';
        $strWsServiceCert   = 'Authentication';
        $strWsGatewayCert   = 'Authentication';
        $strWsMethodCert    = 'Authentication';
        $strEstado          = "";
        $intIncidenciaDetId = $objRequest->get('idDetalleIncidencia');
        $strCategoria       = $objRequest->get('categoria');
        $strSubcategoria    = $objRequest->get('subcategoria');
        $intDetalleId       = $objRequest->get('intDetalleId');
        $strIpAddress       = $objRequest->get('ip');
        $strPuerto          = $objRequest->get('puerto');
        $strNoTicket        = $objRequest->get('ticketNo');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $intIdEmpresa       = $objRequest->getSession()->get('idEmpresa');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        
        $objAccesoCert      = $this->get('tecnico.EcucertService');
        
        try
        {            
            //Generación Token de seguridad        
            $arrayJsonToken = array(
                'user'    => $strUsrMod,
                'gateway' => $strWsGatewayCert,
                'service' => $strWsServiceCert,
                'method'  => $strWsMethodCert,
                'source'  => array(
                    'name'         => $strWsAppName,
                    'originID'     => $strIpMod,
                    'tipoOriginID' => 'IP'
                )
            );
                
            $arrayToken = $objAccesoCert->generateToken($arrayJsonToken);
            $strToken   = $arrayToken['token'];
            $strStatus  = $arrayToken['status'];

            if(empty($strStatus) || $strStatus != "200")
            {
                $strEstado = "No se pudo generar Token. Reintente";
            }
            else
            {
                $arrayParamVul      = array('op'    => "validate",
                                            'token' => $strToken,
                                            'data'  =>  array(  'categoria'     => $strCategoria,
                                                                'subcategoria'  => $strSubcategoria,
                                                                'ip_address'    => $strIpAddress,
                                                                'id_registro'   => $intIncidenciaDetId,
                                                                'puerto'        => $strPuerto,
                                                                'numero_ticket' => $strNoTicket)
                                            );

                $arrayRespuestaVul  = $objAccesoCert->estadoVulnerabilidadCert($arrayParamVul);

                if(isset($arrayRespuestaVul) && !empty($arrayRespuestaVul))
                {
                    if($arrayRespuestaVul['strStatus']  !== "ERROR" 
                        && isset($arrayRespuestaVul['strEstado']) 
                        && !empty($arrayRespuestaVul['strEstado']))
                    {
                        $arrayParametros    = array('intIncidenciaDetId' => $intIncidenciaDetId,
                                                    'strEstado'          => $arrayRespuestaVul['strEstado'],
                                                    'strUsrMod'          => $strUsrMod,
                                                    'strIpMod'           => $strIpMod);

                        $objSoporteService  = $this->get('soporte.SoporteService');
                        $arrayRespuesta = $objSoporteService->modificarEstadoDetalleIncEcucert($arrayParametros);

                        $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
                        $objInfoTareaSeguimiento->setDetalleId($intDetalleId);
                        $objInfoTareaSeguimiento->setObservacion('Botón Validar: '.$arrayRespuestaVul['strEstado']);
                        $objInfoTareaSeguimiento->setUsrCreacion($strUsrMod);
                        $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                        $objInfoTareaSeguimiento->setEmpresaCod($intIdEmpresa);
                        $objInfoTareaSeguimiento->setEstadoTarea("");
                        $objInfoTareaSeguimiento->setInterno("");
                        $objInfoTareaSeguimiento->setDepartamentoId("");
                        $objInfoTareaSeguimiento->setPersonaEmpresaRolId("");
                        $emSoporte->persist($objInfoTareaSeguimiento);
                        $emSoporte->flush();
 
                        if($arrayRespuesta['strStatus'] == "ERROR")
                        {
                            $strEstado = $arrayRespuesta['strMensaje'];
                        }
                        else
                        {
                            $strEstado = $arrayRespuestaVul['strEstado'];
                        }
                    }
                    else
                    {
                        $strEstado = $arrayRespuestaVul['strMensaje'];
                    }
                }
                else
                {
                    $strEstado = "ERROR NO SE PUDO OBTENER RESPUESTA";
                }
            }
        }
        catch(\Exception $ex)
        {
            $strEstado = "Error al comunicarse con CERT. Reintente";
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.verificarVulnerabilidadEcucert',  
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $arrayResultadoRegistro = array('estado'    =>  $strEstado );
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;         
    }
    
    /**
     * Buscar la información del estado de las incidencias de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 07-03-2019
     * 
     */
    public function buscarEstadoIncidenciaAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arrayEstadosIncidencia = $emSoporte->getRepository('schemaBundle:InfoIncidenciaDet')
                                               ->getTodosEstadosIncidencia();

            $arrayResultadoRegistro = array('total'        =>count($arrayEstadosIncidencia),
                                            'encontrados'  =>$arrayEstadosIncidencia );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar estados. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarEstadoIncidencia', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        
        return $objJsonRespuesta;         
    }
    
    /**
     * Buscar la información de las categoraias de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 09-07-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 23-05-2021 - Se cambia la tabla donde se obtiene la categoría
     * @since 1.0
     * 
     */
    public function buscarCategoriaIncidenciaAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $strNombreParam     = 'PLANTILLAS DE NOTIFICACIONES';
        $strDescripParam    = 'CODIGO DE PLANTILLA ECUCERT';
        $strEstado          = 'Activo';

        try
        {
            $arrayParametros    = array('strNombreParam'  => $strNombreParam,
                                        'strDescripParam' => $strDescripParam,
                                        'strEstado'       => $strEstado
                                        );

            $arrayCategoria = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')
                                               ->getTodasCategoriasIncidencia($arrayParametros);

            $arrayResultadoRegistro = array('total'        =>count($arrayCategoria),
                                            'encontrados'  =>$arrayCategoria );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'  =>"Error al obtener la categoría. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarCategoriaIncidencia', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }

    /**
     * Obtner las categorias y subCategorias de ECUCERT.
     * 
     * @Secure(roles="ROLE_434-8097")
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 19-05-2021
     * 
     */
    public function obtenerCategoriaAction()
    {
        ini_set('max_execution_time', 3000000); 
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest                 = $this->getRequest();
        $serviceUtil                = $this->get('schema.Util');
        $strUsrMod                  = $objRequest->getSession()->get('user');
        $intCodEmpresa              = $objRequest->getSession()->get('idEmpresa');
        $strIpMod                   = $objRequest->getClientIp();
        $strTipoEvento              = strtoupper($objRequest->get('tipoEvento'));
        $strCategoria               = strtoupper($objRequest->get('categoria'));
        $strMostrarTodas            = strtoupper($objRequest->get('mostrarTodas'));
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $emComunicacion             = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emSoporte                  = $this->getDoctrine()->getManager("telconet_soporte");
        $strEstado                  = "Activo";
        $strDescripcionCategoria    = "CODIGO DE PLANTILLA ECUCERT";
        $strNombreParam             = "PLANTILLAS DE NOTIFICACIONES";
        $arrayCategConPlantilla     = array();
        $arrayCategSinPlantilla     = array();

        try
        {
            $arrayParamEcucert          = array(
                                            'nombreParametro' => $strNombreParam,
                                            'estado'          => $strEstado
                                        );
            
            $entityParametroCab         = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneByNombreParametro($arrayParamEcucert);
                
            $intIdParametrosECU = 0;
            if( isset($entityParametroCab) && !empty($entityParametroCab) )
            {
                $intIdParametrosECU = $entityParametroCab->getId();
            }

            $arrayParametrosDet  = array( 
                                        'estado'      => $strEstado, 
                                        'parametroId' => $intIdParametrosECU,
                                        'descripcion' => $strDescripcionCategoria,
                                        'empresaCod'  => $intCodEmpresa
                                    );

            if(!empty($strTipoEvento))
            {
                $arrayParametrosDet['valor3'] = $strTipoEvento;
            }
            if(!empty($strCategoria))
            {
                $arrayParametrosDet['valor1'] = $strCategoria;
            }

            if(!empty($strMostrarTodas)  || !empty($strTipoEvento))
            {
                $arrayParametrosCat  = array('strNombreParam'  => $strNombreParam,
                                            'strDescripParam'  => $strDescripcionCategoria,
                                            'strEstado'        => $strEstado,
                                            'strTipoEvento'    => $strTipoEvento,
                                            'intCodEmpresa'    => $intCodEmpresa
                                            );

                $arrayCategoria = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')
                                            ->getTodasCategoriasIncidencia($arrayParametrosCat);
            }
            else
            {
                $objParametroDetEstadoInc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->findBy($arrayParametrosDet);
            
                foreach($objParametroDetEstadoInc as $objCategoria)
                {
                    $intIdPlantilla = 0;
                    $strCodigoPlantilla = $objCategoria->getValor4();
                    if(!empty($strCodigoPlantilla))
                    {
                        $objPlantilla = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                     ->findOneBy(array('estado'  => array('Activo','Modificado'),
                                                                        'codigo'  => $strCodigoPlantilla),
                                                                array('codigo' => 'ASC'));

                        if(is_object($objPlantilla))
                        {
                            $intIdPlantilla = $objPlantilla->getId();
                        }
                    }

                    if($intIdPlantilla != 0)
                    {
                        array_push(
                            $arrayCategConPlantilla,  array('idCategoria'     => $objCategoria->getId(),
                                                            'categoria'       => ucwords(strtolower($objCategoria->getValor1())),
                                                            'subCategoria'    => ucwords(strtolower($objCategoria->getValor2())),
                                                            'tipoEvento'      => ucwords(strtolower($objCategoria->getValor3())),
                                                            'codigoPlantilla' => $strCodigoPlantilla,
                                                            'idPlantilla'     => $intIdPlantilla)
                        );
                    }
                    else
                    {
                        array_push(
                            $arrayCategSinPlantilla,  array('idCategoria'     => $objCategoria->getId(),
                                                            'categoria'       => ucwords(strtolower($objCategoria->getValor1())),
                                                            'subCategoria'    => ucwords(strtolower($objCategoria->getValor2())),
                                                            'tipoEvento'      => ucwords(strtolower($objCategoria->getValor3())),
                                                            'codigoPlantilla' => $strCodigoPlantilla,
                                                            'idPlantilla'     => $intIdPlantilla)
                        );
                    }
                }

                $arrayCategoria = array_merge($arrayCategSinPlantilla, $arrayCategConPlantilla);
            }
            
            $arrayResultadoRegistro = array('total'        =>count($arrayCategoria),
                                            'encontrados'  =>$arrayCategoria );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'  =>"Error al obtener la categoría. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.obtenerCategoria', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }


    /**
     * Subir archivo CSV de ECUCERT.
     * 
     * @Secure(roles="ROLE_434-8117")
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 19-05-2021
     * 
     * @author Carlos Julio Pérez Quizhpe <cjperez@telconet.ec>
     * @version 1.1 25-06-2021
     * Se valida que la quinta columna del archivo CSV se mapee 
     * al nodo "bandCGNAT". Si el campo está vacío, bandCGNAT="N",
     * caso contrario bandCGNAT="S".
     * 
     * @author Carlos Julio Pérez Quizhpe <cjperez@telconet.ec>
     * @version 1.2 28-06-2021
     * Validación de los campos ip, fecha, hora y puerto en el 
     * procesamiento del archivo CSV. El quinto campo bandCGNAT
     * es opcional.
     * 
     * Este es un ejemplo del contenido del archivo CSV:
     * 
     * 181.199.116.82,2021-06-25,16:03:00,8080,S
     * 186.4.212.6,2021-06-28,16:45:22,9090
     * 
     */
    public function subirArchivoCSVAction()
    {
        ini_set('max_execution_time', 3000000); 
        
        $objJsonRespuesta    = new JsonResponse();
        $objSoporteService   = $this->get('soporte.SoporteService');
        $emSoporte           = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial         = $this->getDoctrine()->getManager("telconet");        
        $objRequest          = $this->getRequest();
        $serviceUtil         = $this->get('schema.Util');
        $objAccesoCert       = $this->get('tecnico.EcucertService');

        $strWsAppName        = 'APP.CERT';
        $strWsServiceCert    = 'Authentication';
        $strWsGatewayCert    = 'Authentication';
        $strWsMethodCert     = 'Authentication';

        $strUsrMod           = $objRequest->getSession()->get('user');
        $strIpMod            = $objRequest->getClientIp();
        $arrayArchivos       = $objRequest->files->get('archivos');
        $strSubCategoria     = $objRequest->get('subCategoriaCSV');
        $strTipoEvento       = $objRequest->get('tipoEventoCSV');
        $strCategoria        = $objRequest->get('categoriaCSV');
        $intIdEmpresa        = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa   = $objRequest->getSession()->get('prefijoEmpresa');
        $objInfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($intIdEmpresa);
        $strNombreEmpresa    = $objInfoEmpresaGrupo ? $objInfoEmpresaGrupo->getNombreEmpresa() : "";

        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $emComunicacion             = $this->getDoctrine()->getManager("telconet_comunicacion");
        $intCodEmpresa              = $objRequest->getSession()->get('idEmpresa');
        $strDescripcionCategoria    = "CODIGO DE PLANTILLA ECUCERT";
        $strNombreParam             = "PLANTILLAS DE NOTIFICACIONES";
        $strEstado                  = "Activo";
        $strCodigoPlantilla         = "";

        $arrayParamEcucert  = array(
                                    'nombreParametro' => $strNombreParam,
                                    'estado'          => $strEstado
                                );

        try
        {
            $entityParametroCab   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                              ->findOneByNombreParametro($arrayParamEcucert);

            $intIdParametrosECU = 0;
            if( isset($entityParametroCab) && !empty($entityParametroCab) )
            {
                $intIdParametrosECU = $entityParametroCab->getId();
            }

            if(empty($strSubCategoria))
            {
                $strSubCatParam = null;
            }
            else
            {
                $strSubCatParam = strtoupper($strSubCategoria);
            }

            $arrayParametrosDet  = array( 
                                        'estado'      => $strEstado, 
                                        'parametroId' => $intIdParametrosECU,
                                        'descripcion' => $strDescripcionCategoria,
                                        'empresaCod'  => $intCodEmpresa,
                                        'valor1'      => strtoupper($strCategoria),
                                        'valor2'      => $strSubCatParam,
                                        'valor3'      => strtoupper($strTipoEvento)
                                    );

            $objParametroDetEstadoInc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneBy($arrayParametrosDet);
                                                  
            if(is_object($objParametroDetEstadoInc))
            {
                $strCodigoPlantilla = $objParametroDetEstadoInc->getValor4();
            }
            else
            {
                throw new \Exception('No se encontró código de plantilla');
            }

            $objPlantilla = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                            ->findOneBy(array('estado'  => array('Activo','Modificado'),
                                                              'codigo'  => $strCodigoPlantilla),
                                                        array('codigo' => 'ASC'));

            if(!is_object($objPlantilla))
            {
                throw new \Exception('No se encontró la plantilla');
            }

            if ($arrayArchivos == "" || $arrayArchivos == null)
            {
                throw new \Exception('No existen archivos para cargar, favor revisar nuevamente!');
            }

            foreach($arrayArchivos as $objArchivo)
            {
                if ($objArchivo == null || $objArchivo == "")
                {
                    throw new \Exception('No existen archivos para cargar, favor revisar nuevamente!');
                }

                $strNameFile             = $objArchivo->getClientOriginalName();
                $arrayPartsNombreArchivo = explode('.', $strNameFile);
                $strExtArchivo           = $arrayPartsNombreArchivo[1];

                if($strExtArchivo == 'csv')
                {
                    try
                    {            
                        //Generación Token de seguridad        
                        /* */
                        $arrayJsonToken = array(
                            'user'    => $strUsrMod,
                            'gateway' => $strWsGatewayCert,
                            'service' => $strWsServiceCert,
                            'method'  => $strWsMethodCert,
                            'source'  => array(
                                'name'         => $strWsAppName,
                                'originID'     => $strIpMod,
                                'tipoOriginID' => 'IP'
                            )
                        );
                            
                        $arrayToken = $objAccesoCert->generateToken($arrayJsonToken);
                        $strToken   = $arrayToken['token'];
                        $strStatus  = $arrayToken['status'];
                        
                        if(empty($strStatus) || $strStatus != "200")
                        {
                            $strEstado = "No se pudo generar Token. Reintente";
                            throw new \Exception($strEstado);
                        }
                        else
                        {

                            $strFechaActual = date('Y-m-d h:m:s');
                            // Detalle
                            $arrayIps = array();
                            $intFila = 0;
                            $strValorCampoIncorrecto = "";
                            if (($objGestor = fopen($objArchivo, "r")) !== false) 
                            {
                                $intValidacionCampo = 0;
                                while (($arrayCampos = fgetcsv($objGestor, 1000, ",")) !== false) 
                                {
                                    $strIpAddress = $arrayCampos[0];
                                    $strFecha     = $arrayCampos[1];
                                    $strHora      = $arrayCampos[2];
                                    $strPuerto    = $arrayCampos[3];

                                    // Validaciones
                                    $strValid = filter_var($strIpAddress, FILTER_VALIDATE_IP);
                                    if (empty($strValid))
                                    {
                                        $strValorCampoIncorrecto = $strIpAddress;
                                        $intValidacionCampo = 1;
                                        break;
                                    }
 
                                    $strFormato = "Y-m-d";
                                    $objFecha = $strFecha ? \DateTime::createFromFormat($strFormato, $strFecha) : null;
                                    if (! $objFecha)
                                    {
                                        $strValorCampoIncorrecto = $strFecha;
                                        $intValidacionCampo = 2;
                                        break;
                                    }

                                    $strFormato = "H:i:s";
                                    $objHora = $strHora ? \DateTime::createFromFormat($strFormato, $strHora) : null;
                                    if (! $objHora)
                                    {
                                        $strValorCampoIncorrecto = $strHora;
                                        $intValidacionCampo = 3;
                                        break;
                                    }
                                    
                                    $strPuertoRegEx = "/^([0-9]{1,4}|[1-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5])$/";
                                    if(!preg_match($strPuertoRegEx, $strPuerto))
                                    {
                                        $strValorCampoIncorrecto = $strPuerto;
                                        $intValidacionCampo = 4;
                                        break;
                                    }

                                    $strBandCGNAT = empty($arrayCampos[4]) ? "N": "S";
                                     
                                    $arrayIps[] = array (
                                        "feTicketDetail" => $strFechaActual,
                                        "bandCGNAT"      => $strBandCGNAT,
                                        "status"         => "Vulnerable",
                                        "ipDestino"      => "",
                                        "empresa"        => $strPrefijoEmpresa,
                                        "ipAddress"      => $strIpAddress,
                                        "feIncidencia"   => $strFecha . " " . $strHora,
                                        "tipoIp"         => "Cliente",
                                        "puerto"         => $strPuerto
                                    );
                                    $intFila++; 
                                }
                                fclose($objGestor);
                            }

                            if ($intValidacionCampo == 0)
                            {
                                $intSecuenciaTicket = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')->getSecuenciaTicket();
                                $strNoTicket = "INTERNO #" . $intSecuenciaTicket;
                                $strSubject  = "[ECUCERT #" .  $strNoTicket . 
                                            "] PRIORIDAD Baja Notificacion de " . $strTipoEvento . " " . $strCategoria . " " .
                                            $strNombreEmpresa . " " . date('Y-m-d');
                                // Cabecera
                                $arrayData                    = array(
                                    "ips"                     => $arrayIps,
                                    "feTicket"                => $strFechaActual,
                                    "subject"                 => $strSubject,
                                    "bandRDA"                 => "S",
                                    "nombreCategoria"         => strtoupper($strCategoria),
                                    "tituloCategoria"         => strtoupper($strCategoria),
                                    "count"                   => $intFila,
                                    "noTicket"                => $strNoTicket,
                                    "prioridadInfraestructura" => "Baja",
                                    "tipoEvento"              => $strTipoEvento,
                                    "categoria"               => $strCategoria,
                                    "subCategoria"            => $strSubCategoria,
                                    "prioridad"               => "Baja",
                                    "bandCPE"                 => "N"
                                );   

                                $arraySource = array(                                    
                                    "originID"     => $strIpMod,
                                    "name"         => $strWsAppName,
                                    "tipoOriginID" => "IP"
                                );

                                $arrayJsonEcucert = array(
                                    "data"   => $arrayData,
                                    "user"   => $strUsrMod,
                                    "token"  => $strToken,
                                    "source" => $arraySource,
                                    "op"     => "putIngresarIncidencia"
                                );

                                $arrayParametros = array('arrayJsonEcucert'  => $arrayJsonEcucert,
                                                'strUsrCreacion'    => $strUsrMod,
                                                'strNumeroRegis'    => $intFila,
                                                'strIpCreacion'     => $strIpMod);


                                // Ejecutar el proceso
                                $objSoporteService->guardarJsonIncidenciaEcucert($arrayParametros);
                            }
                            else
                            {
                                $intFila++; // Para mostrar la línea del error del campo en el archivo CSV.
                                $strMensaje = "";
                                switch ($intValidacionCampo) 
                                {
                                    case 1:
                                        $strMensaje = "IP válido";
                                        break;
                                    case 2:
                                        $strMensaje = "Fecha válida (aaaa-mm-dd)";
                                        break;
                                    case 3:
                                        $strMensaje = "Hora válida (hh:mm:ss)";
                                        break;
                                    case 4:
                                        $strMensaje = "Puerto válido (0 a 65535)";
                                        break;
                                    default:
                                        break;
                                }
                                $strEstado = "El dato [$strValorCampoIncorrecto] no corresponde a un valor de $strMensaje. Fila: $intFila";
                                throw new \Exception($strEstado);
                            }
                        }
                    }
                    catch(\Exception $ex)
                    {
                        $strEstado = "Error procesando el archivo. Reintente. ";
                        throw new \Exception($strEstado . " " . $ex->getMessage());
                    }
                }
                else
                {
                    throw new \Exception('Solo se puede subir archivo en formato csv');
                }
            }
            $arrayResultadoRegistro = array('success'       => "true",
                                            'respuesta'     => "Proceso realizado con éxito. Su ticket generado fue: ".$strNoTicket,
                                            'mensaje'       => "OK" );
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('success'       => "false",
                                            'respuesta'     => $ex->getMessage(),
                                            'mensaje'       => $ex->getMessage() );
            
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.subirArchivoCSV', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }

    /**
     * Redireccionar a la Plantillas de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 19-05-2021
     * 
     */
    public function redireccionarPlantillaAction()
    {
        ini_set('max_execution_time', 3000000); 
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrCre          = $objRequest->getSession()->get('user');
        $strIpCre           = $objRequest->getClientIp();
        $intIdPlantilla     = $objRequest->get('idPlantilla');
        $strCodigoPlantilla = $objRequest->get('codigoPlantilla');

        try
        {
            if($intIdPlantilla != 0)
            {
                $strUrl = $this->generateUrl('admiplantilla_edit', 
                        array('id'        => $intIdPlantilla,
                              'banderEcu' => 1
                        )
                );
            }
            else
            {
                $strUrl = $this->generateUrl('admiplantilla_new', 
                        array('codigo' =>$strCodigoPlantilla)
                );
            }
            
            $arrayResultadoRegistro = array('success'       => "true",
                                            'respuesta'     => "Proceso realizado con éxito",
                                            'mensaje'       => "OK",
                                            'strUrl'        => $strUrl
                                         );
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('success'       => "false",
                                            'respuesta'     => $ex->getMessage(),
                                            'mensaje'       => $ex->getMessage() );
            
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.redireccionarPlantillaAction', 
                                        $ex->getMessage(),
                                        $strUsrCre,
                                        $strIpCre); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }

    /**
     * Guardar categoria y subCategoria de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 19-05-2021
     * 
     */
    public function guardarCategoriaAction()
    {
        ini_set('max_execution_time', 3000000); 
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest                 = $this->getRequest();
        $serviceUtil                = $this->get('schema.Util');
        $strUsrCreacion             = $objRequest->getSession()->get('user');
        $intCodEmpresa              = $objRequest->getSession()->get('idEmpresa');
        $strIpCreacion              = $objRequest->getClientIp();
        $strCategoria               = strtoupper($objRequest->get('categoria'));
        $strSubCategoria            = strtoupper($objRequest->get('subcategoria'));
        $strTipoEvento              = strtoupper($objRequest->get('tipoEvento'));
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $emComercial                = $this->getDoctrine()->getManager("telconet");
        $strEstado                  = "Activo";
        $strDescripcionCategoria    = "CODIGO DE PLANTILLA ECUCERT";
        $strNombreParametroDet      = 'CODIGO DE PLANTILLA ECUCERT';
        $strSubCatePlantilla        = "";
        $strCodigoPlantilla         = "";

        $arrayParamEcucert  = array(
            'nombreParametro' => "PLANTILLAS DE NOTIFICACIONES",
            'estado'          => $strEstado
        );
   
        try
        {
            $objEmpresaGrupo   = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                                             ->findOneById($intCodEmpresa);

            if(is_object($objEmpresaGrupo))
            {
                $strPrefijoEmpr    = $objEmpresaGrupo->getPrefijo();
            }
            else
            {
                throw new \Exception('No existe la empresa.');
            }

            $entityParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneByNombreParametro($arrayParamEcucert);

            $intIdParametrosECU = 0;
            if( isset($entityParametroCab) && !empty($entityParametroCab) )
            {
                $intIdParametrosECU = $entityParametroCab->getId();
            }

            $arrayParametrosDet  = array( 
                        'estado'      => $strEstado, 
                        'parametroId' => $intIdParametrosECU,
                        'descripcion' => $strDescripcionCategoria,
                        'empresaCod'  => $intCodEmpresa
                    );

            $objParametroDetEstadoInc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy($arrayParametrosDet);
            
            foreach($objParametroDetEstadoInc as $objCategoria)
            {
                $strCategoriaParam    = $objCategoria->getValor1();
                $strSubCategoriaParam = $objCategoria->getValor2();

                if($strCategoria == $strCategoriaParam && $strSubCategoria == $strSubCategoriaParam)
                {
                    throw new \Exception('Ya existe la Categoria con la SubCategoria.');
                }   
                
                if($strCategoria == $strCategoriaParam)
                {
                    $strCodigoPlantilla = $objCategoria->getValor4();
                }
            }

            if(empty($strCodigoPlantilla))
            {
                if(strlen($strSubCategoria)>0)
                {
                    $strSubCatePlantilla = substr($strSubCategoria, 0, strlen($strSubCategoria)-1);
                }
                $strCodigoPlantilla  = substr($strPrefijoEmpr.$strCategoria.$strSubCatePlantilla,0,14);
            }

            if(empty($strCodigoPlantilla))
            {
                throw new \Exception('No se genero la código para la plantilla.');
            }  

            $objAdmiParametroDet = new AdmiParametroDet();
            $objAdmiParametroDet->setDescripcion($strNombreParametroDet);
            $objAdmiParametroDet->setEstado($strEstado);
            $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
            $objAdmiParametroDet->setIpCreacion($strIpCreacion);
            $objAdmiParametroDet->setParametroId($entityParametroCab);
            $objAdmiParametroDet->setUsrCreacion($strUsrCreacion);
            $objAdmiParametroDet->setValor1($strCategoria);
            $objAdmiParametroDet->setValor2($strSubCategoria);
            $objAdmiParametroDet->setValor3($strTipoEvento);
            $objAdmiParametroDet->setValor4($strCodigoPlantilla);
            $objAdmiParametroDet->setEmpresaCod($intCodEmpresa);

            $emGeneral->persist($objAdmiParametroDet);
            $emGeneral->flush();

            $arrayResultadoRegistro = array('success'       => "true",
                                            'respuesta'     => "Proceso realizado con éxito",
                                            'mensaje'       => "OK" );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('success'       => "false",
                                            'respuesta'     => $ex->getMessage(),
                                            'mensaje'       => $ex->getMessage() );
            
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.guardarCategoriaAction', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }

    /**
     * Remover categoria y subCategoria de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 19-05-2021
     * 
     */
    public function removerCategoriaAction()
    {
        ini_set('max_execution_time', 3000000); 
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest                 = $this->getRequest();
        $serviceUtil                = $this->get('schema.Util');
        $strUsrCreacion             = $objRequest->getSession()->get('user');
        $strIpCreacion              = $objRequest->getClientIp();
        $intIdCategoriaParam        = strtoupper($objRequest->get('idCategoriaParam'));
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $emComunicacion             = $this->getDoctrine()->getManager("telconet_comunicacion");
        $strEstado                  = "Eliminado";
   
        try
        {
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneById($intIdCategoriaParam);
            
            if(is_object($objAdmiParametroDet))
            {
                $objAdmiParametroDet->setEstado($strEstado);
                $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                $objAdmiParametroDet->setUsrUltMod($strUsrCreacion);

                $objPlantilla = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                   ->findOneBy(array('estado'  => array('Activo','Modificado'),
                                                                     'codigo'  => $objAdmiParametroDet->getValor4()));

                if(is_object($objPlantilla))
                {
                    $objPlantilla->setEstado('Eliminado');
                    $emComunicacion->persist($objPlantilla);
                    $emComunicacion->flush(); 
                }

                $emGeneral->persist($objAdmiParametroDet);
                $emGeneral->flush();              
            }
            else
            {
                throw new \Exception('No existe el registro de la Categoria.');
            }

            $arrayResultadoRegistro = array('success'       => "true",
                                            'respuesta'     => "Proceso realizado con éxito",
                                            'mensaje'       => "OK" );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('success'       => "false",
                                            'respuesta'     => $ex->getMessage(),
                                            'mensaje'       => $ex->getMessage() );
            
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.removerCategoriaAction', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }

    /**
     * Buscar combo de ocpiones para los seguimientos de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 22-08-2019
     * 
     */
    public function buscarOpcionesSeguimientosAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arraySeguimientos = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')
                                               ->getTodosSeguimientosECUCERT();

            $arrayResultadoRegistro = array('total'        =>count($arraySeguimientos),
                                            'encontrados'  =>$arraySeguimientos );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'  =>"Error al obtener los seguimientos de ECUCERT. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarOpcionesSeguimientos', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }
    
    /**
     * Buscar la información de las Jurisdicciones existente en Telcos.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 09-07-2019
     * 
     */
    public function buscarCantonAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $strEstado          = "Activo";
        $arrayCantones      = array();
        $arrayParametros    = array( 'estado'      => $strEstado );
        $strRegistroAnt     = "";

        try
        {
            $arrayCanton  = $emGeneral->getRepository('schemaBundle:AdmiCanton')->findBy($arrayParametros,array('jurisdiccion' => 'ASC'));
            
            foreach($arrayCanton as $objCanton)
            {
                $arrayCanton["nombre"]  = $objCanton->getJurisdiccion();
                if(strpos($strRegistroAnt, $objCanton->getJurisdiccion()) === false &&  $objCanton->getJurisdiccion() !== null)
                {
                    $arrayCantones[]        = $arrayCanton; 
                }
                $strRegistroAnt         = $objCanton->getJurisdiccion();
            }

            $arrayResultadoRegistro = array('total'        =>count($arrayCantones),
                                            'encontrados'  =>$arrayCantones );
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al obtener el cantón con su jurisdicción. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarCanton', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }
    
    /**
     * Buscar la información de los sub estados de las incidencias de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 07-03-2019
     * 
     */
    public function buscarSubEstadoIncidenciaAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arraySubEstados = $emSoporte->getRepository('schemaBundle:InfoIncidenciaDet')
                                               ->getTodosSubEstadosIncidencia();

            $arrayResultadoRegistro = array('total'        =>count($arraySubEstados),
                                            'encontrados'  =>$arraySubEstados );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar sub estados. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarSubEstadoIncidencia', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }

/**
     * Buscar la información de los tipos de clientes de las incidencias de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 08-04-2020
     * 
     */
    public function buscarTipoClienteAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arrayTipoCliente = $emSoporte->getRepository('schemaBundle:InfoIncidenciaDet')
                                          ->getTodosTipoCliente();

            $arrayResultadoRegistro = array('total'        =>count($arrayTipoCliente),
                                            'encontrados'  =>$arrayTipoCliente );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar el tipo de cliente. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarTipoCliente', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }
    
    /**
     * Buscar la información de los tipos de Evento de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 10-07-2019
     * 
     */
    public function buscarTipoEventoAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arrayTipoEventos = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')
                                               ->getTiposdeEvento();

            $arrayResultadoRegistro = array('total'        =>count($arrayTipoEventos),
                                            'encontrados'  =>$arrayTipoEventos );

        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar tipo de evento. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarTipoEvento', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }
    
    /**
     * Buscar los logines existentes de los puntos.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 07-08-2019
     * 
     */
    public function buscarLoginAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $strQueryNombre     = $objRequest->query->get('query') ? $objRequest->query->get('query') : "";
        $strNombre          = ($strQueryNombre != '' ? $strQueryNombre : $objRequest->query->get('nombre'));  
        $emComercial        = $this->getDoctrine()->getManager("telconet");

        try
        {            
            if( $strNombre )
            {
                $arrayResultadoRegistro = json_decode($emComercial
                                                            ->getRepository('schemaBundle:InfoPunto')
                                                            ->generarJsonClientes($strNombre));
            }
                
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar el login. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarLogin', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }
    
    /**
     * Buscar la información de la prioridad de las incidencias de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 07-03-2019
     * 
     */
    public function buscarPrioridadIncidenciaAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arrayPrioridades = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')
                                               ->getTodasPrioridadesIncidencia();

            $arrayResultadoRegistro = array('total'        =>count($arrayPrioridades),
                                            'encontrados'  =>$arrayPrioridades );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar prioridad. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarPrioridadIncidencia', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 
        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }
    
    /**
     * Buscar la información del estado de gestión de las incidencias de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 28-04-2019
     * 
     */
    public function buscarEstadoGestionAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arrayEstadosGestion = $emSoporte->getRepository('schemaBundle:InfoIncidenciaDet')
                                               ->getTodosEstadoGestionIncidencia();

            $arrayResultadoRegistro = array('total'        =>count($arrayEstadosGestion),
                                            'encontrados'  =>$arrayEstadosGestion );

        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar estado de gestión. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarEstadoGestion', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 


        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;          
    }
    
    /**
     * Buscar la información del estado de notificación de las incidencias de ECUCERT.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 07-03-2019
     * 
     */
    public function buscarEstadoNotificacionAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $strUsrMod          = $objRequest->getSession()->get('user');
        $strIpMod           = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arrayEstadoNotificacion = $emSoporte->getRepository('schemaBundle:InfoIncidenciaDet')
                                               ->getTodosEstadosNotifIncidencia();

            $arrayResultadoRegistro = array('total'        =>count($arrayEstadoNotificacion),
                                            'encontrados'  =>$arrayEstadoNotificacion );
      
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar estado de notificación. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.buscarEstadoNotificacion', 
                                        $ex->getMessage(),
                                        $strUsrMod,
                                        $strIpMod); 


        }
        $objJsonRespuesta->setData($arrayResultadoRegistro);
    
        return $objJsonRespuesta;         
    }
   
    /**
     * Función que cierra el caso de la tarea.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 11-03-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 26-08-2019 - Se realiza la validación para que cierre la tarea aunque no tenga un caso asociado 
     * @since 1.0
     * 
     */
    public function cerrarCasosEcucertAction()
    {
        ini_set('max_execution_time', 3000000);

        $objJsonRespuesta = new JsonResponse();
        
        $objRequest             = $this->getRequest();
        $serviceUtil            = $this->get('schema.Util');
        $emGeneral              = $this->getDoctrine()->getManager("telconet_general");
        
        $strFechaActual            = new \DateTime("now");
        $intCodEmpresa             = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa         = $objRequest->getSession()->get('prefijoEmpresa');
        $intIdCaso                 = $objRequest->get('id_caso');
        $intDetalleId              = $objRequest->get('id_detalle');
        $strFechaCierre            = date_format($strFechaActual, 'Y/m/d');
        $strHoraCierre             = date_format($strFechaActual, 'H:i:s');
        $strTituloFinalHipotesis   = $objRequest->get('tituloFinalHipotesis');
        $strTipoAfectacion         = "SINAFECTACION";
        $strVersionFinal           = $objRequest->get('versionFinal');
        $intTareaId                = $objRequest->get('tarea_id');
        $strFechaSol               = $objRequest->get('fechaSol');
        $strHoraSol                = $objRequest->get('horaSol');
        $strUsrCreacion            = $objRequest->getSession()->get('user');
        $strIpCreacion             = $objRequest->getClientIp();
        $strEmpleado               = $objRequest->getSession()->get('empleado');
        $strIdEmpleado             = $objRequest->getSession()->get('id_empleado');
        $strIdDepartamento         = $objRequest->getSession()->get('idDepartamento');
        $intIncidenciaDetId        = $objRequest->get('IncidenciaDetId');
        $strMensaje                = "No se pudo procesar";

        $objCreacionTarea          = new \DateTime($strFechaSol." ".$strHoraSol);
        $strDiferenciaFechas       = $strFechaActual->diff($objCreacionTarea);
        $strTiempoTotalSolucion    = (($strDiferenciaFechas->days *24) * 60) + ($strDiferenciaFechas->i);
      
        $strEstado                  = "Activo";
        $strDescripcionEstadoInc    = "PARAMETROS PARA ESTADO INCIDENCIA";
        $arrayParamEcucert          = array(
                                        'nombreParametro' => "PARAMETROS_ECUCERT",
                                        'estado'          => $strEstado
                                    );
        
        $arrayParametrosCaso = array(
                                    'idEmpresa'             => $intCodEmpresa,
                                    'prefijoEmpresa'        => $strPrefijoEmpresa,
                                    'idCaso'                => $intIdCaso,
                                    'fechaCierre'           => $strFechaCierre,
                                    'horaCierre'            => $strHoraCierre,
                                    'tituloFinalHipotesis'  => $strTituloFinalHipotesis,
                                    'versionFinalHipotesis' => $strVersionFinal,
                                    'tiempoTotalCaso'       => $strTiempoTotalSolucion,
                                    'usrCreacion'           => $strUsrCreacion,
                                    'ipCreacion'            => $strIpCreacion,
                                    'idDepartamento'        => $strIdDepartamento,
                                    'idEmpleado'            => $strIdEmpleado,
                                    'empleado'              => $strEmpleado,
                                    'tipo_afectacion'       => $strTipoAfectacion,
                                );
        
        $arrayParametrosTarea = array(
                                    'idEmpresa'             => $intCodEmpresa,
                                    'prefijoEmpresa'        => $strPrefijoEmpresa,
                                    'idCaso'                => $intIdCaso,
                                    'idDetalle'             => $intDetalleId,
                                    'tarea'                 => $intTareaId,
                                    'tiempoTotal'           => $strTiempoTotalSolucion,
                                    'fechaCierre'           => $strFechaCierre,
                                    'horaCierre'            => $strHoraCierre,
                                    'fechaEjecucion'        => $strFechaSol,
                                    'horaEjecucion'         => $strHoraSol,
                                    'esSolucion'            => 'S',
                                    'fechaApertura'         => "",
                                    'horaApertura'          => "",
                                    'jsonMateriales'        => "",
                                    'idAsignado'            => null,
                                    'observacion'           => "Tarea finalizada por proceso ECUCERT",
                                    'empleado'              => $strEmpleado,
                                    'usrCreacion'           => $strUsrCreacion,
                                    'ipCreacion'            => $strIpCreacion,
                                    'strEnviaDepartamento'  => "N"
                        );
        
        $serviceSoporte         = $this->get('soporte.SoporteService');
        
        try
        {
            $entityParametroCab         = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneByNombreParametro($arrayParamEcucert);
             
            $intIdParametrosECU = 0;
            if( isset($entityParametroCab) && !empty($entityParametroCab) )
            {
                $intIdParametrosECU = $entityParametroCab->getId();
            }

            $arrayParametrosDet  = array( 
                                        'estado'      => $strEstado, 
                                        'parametroId' => $intIdParametrosECU,
                                        'descripcion' => $strDescripcionEstadoInc
                                    );

            $objParametroDetEstadoInc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosDet);

            if( isset($objParametroDetEstadoInc) && !empty($objParametroDetEstadoInc) )
            {
                $strEstadoAtentido      = $objParametroDetEstadoInc->getValor5() ? $objParametroDetEstadoInc->getValor5() : '';
            }

            $arrayParametrosGest    = array('intIncidenciaDetId' => $intIncidenciaDetId,
                                            'strEstado'          => $strEstadoAtentido,
                                            'strUsrMod'          => $strUsrCreacion,
                                            'strIpMod'           => $strIpCreacion);
            
            if(isset($intDetalleId) && !empty($intDetalleId))   
            {
                $arrayRespuestaTarea    = $serviceSoporte->finalizarTarea($arrayParametrosTarea);
                $strStatusTarea         = $arrayRespuestaTarea['status'];
                $strMensaje             = " La tarea fue ".$arrayRespuestaTarea['mensaje'];
                $serviceSoporte->modificarEstadoGestIncEcucert($arrayParametrosGest);
            }     
     
            if(isset($intIdCaso) && !empty($intIdCaso))
            { 
                $arrayRespuestaCaso = $serviceSoporte->cerrarCaso($arrayParametrosCaso);
                $strStatusCaso      = $arrayRespuestaCaso['status'];
                $strMensaje         = $arrayRespuestaCaso['mensaje'];    
                $serviceSoporte->modificarEstadoGestIncEcucert($arrayParametrosGest);          
            }

            if($strStatusTarea == "OK" && $strStatusCaso == "OK")
            {
                $strMensaje = " El Caso y la Tarea fueron ".$arrayRespuestaTarea['mensaje']+"s ";
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = "Error al procesar finalización del caso. Por favor reintente";
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.cerrarCasosEcucert', 
                                        $ex->getMessage(),
                                        $strUsrCreacion,
                                        $strIpCreacion); 
        }
        $arrayResultadoRegistro = array('estado'    =>  $strMensaje );
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;        
    }
    
    
    /**
     * Función que contiene la información que se envia de la Ip reportada por ECUCERT
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 14-03-2019
     * 
     */
    public function contenerIpAction()
    {
        ini_set('max_execution_time', 3000000);

        $objJsonRespuesta = new JsonResponse();
        
        $serviceUtil        = $this->get('schema.Util');
        $objRequest         = $this->getRequest();
        $strEstado          = "";
        $strWsAppName       = 'APP.CERT';
        $strWsServiceCert   = 'Authentication';
        $strWsGatewayCert   = 'Authentication';
        $strWsMethodCert    = 'Authentication';
        $strUsrCreacion     = $objRequest->getSession()->get('user');
        $strIpCreacion      = $objRequest->getClientIp();
        $strCategoria       = $objRequest->get('categoria');
        $strSubcategoria    = $objRequest->get('subcategoria');
        $strIpAddress       = $objRequest->get('ip');
        $objAccesoCert      = $this->get('tecnico.EcucertService');
        
        try
        {
            //Generación Token de seguridad        
            $arrayJsonToken = array(
                'user'    => $strUsrCreacion,
                'gateway' => $strWsGatewayCert,
                'service' => $strWsServiceCert,
                'method'  => $strWsMethodCert,
                'source'  => array(
                    'name'         => $strWsAppName,
                    'originID'     => $strIpCreacion,
                    'tipoOriginID' => 'IP'
                )
            );
                
            $arrayToken = $objAccesoCert->generateToken($arrayJsonToken);
            $strToken   = $arrayToken['token'];
            $strStatus  = $arrayToken['status'];

            if(empty($strStatus) || $strStatus != "200")
            {
                $strEstado = "No se pudo generar Token. Reintente";
            }
            else
            {
                $arrayParamBlock    = array('op'    => "block",
                                            'token' => $strToken,
                                            'data'  =>  array(  'categoria'     => $strCategoria,
                                                                'subcategoria'  => $strSubcategoria,
                                                                'ip_address'    => $strIpAddress)
                                            );

                $arrayRespuestaBlock  = $objAccesoCert->contencionVulnerabilidadCert($arrayParamBlock);

                if(isset($arrayRespuestaBlock) && !empty($arrayRespuestaBlock))
                {
                    if($arrayRespuestaBlock['strStatus']  !== "ERROR" 
                        && isset($arrayRespuestaBlock['strEstado']) 
                        && !empty($arrayRespuestaBlock['strEstado']))
                    {

                        $strEstado = $arrayRespuestaBlock['strEstado'];
                    }
                    else
                    {
                        $strEstado = $arrayRespuestaBlock['strStatus'];
                    }
                }
                else
                {
                    $strEstado = "ERROR NO SE PUDO OBTENER RESPUESTA";
                }
            }
        }
        catch(\Exception $ex)
        {
            $strEstado = "Error al comunicarse con servidor de CERT. Por favor reintente";
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.contenerIp', 
                                        $ex->getMessage(),
                                        $strUsrCreacion,
                                        $strIpCreacion); 
        } 
        $arrayResultadoRegistro = array('estado'    =>  $strEstado );
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;           
    }
    
    /**
     * Función que devuelve los correos que fueron notificados al cliente.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 20-03-2019
     * 
     */
    public function verNotificacionesAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $objJsonRespuesta = new JsonResponse();
        
        $serviceUtil        = $this->get('schema.Util');
        $objRequest         = $this->getRequest();
        $strUsrCreacion     = $objRequest->getSession()->get('user');
        $strIpCreacion      = $objRequest->getClientIp();

        $intIncidenciaDetId = $objRequest->get('idDetalleIncidencia');

        $arrayParametros    = array ('intIncidenciaDetId' => $intIncidenciaDetId);
  
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        
        try
        {
            $arrayCorreos       = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')
                                                ->getCorreosClientesNotifInc($arrayParametros);

            $arrayResultadoRegistro = array('total'        =>count($arrayCorreos),
                                            'encontrados'  =>$arrayCorreos );
        }
        catch(\Exception $ex)
        {
            $arrayResultadoRegistro = array('error'        =>"Error al procesar notificaciones. Por favor reintente" );
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.verNotificaciones', 
                                        $ex->getMessage(),
                                        $strUsrCreacion,
                                        $strIpCreacion); 


        } 
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        
        return $objJsonRespuesta;         
    }
    
    /**
     * Función que envía la notificación al cliente
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 21-03-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 11-08-2019 - Se agrega el nombre del cliente y la fecha del incidente para el reenvío de correo
     * @since 1.0 
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.2 17-08-2019 - Se corrige el valor que valida el correo de quien envia la notificación a cliente
     * @since 1.1
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.3 16-04-2020 - Se agrega el parámetro de correos adicionales para enviar la notificación
     * @since 1.2
     * 
     */
    public function enviarCorreoClienteAction()
    {
        ini_set('max_execution_time', 3000000);

        $objJsonRespuesta   = new JsonResponse();
        
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $serviceSoporte     = $this->get('soporte.SoporteService');

        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->getRequest();
        $intCodEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $strUsrCreacion         = $objRequest->getSession()->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $intIncidenciaDetId     = $objRequest->get('idDetalleIncidencia');
        $intIdPunto             = $objRequest->get('idPunto');
        $strLogin               = $objRequest->get('loginCliente');
        $strIdcaso              = $objRequest->get('casoId');  
        $intIdPerEmpRol         = $objRequest->get('personaEmpresaRolId');
        $strSubCategoria        = $objRequest->get('subCategoria');
        $strCategoria           = $objRequest->get('categoria');
        $strTipoEvento          = $objRequest->get('tipoEvento');
        
        $strIp                  = $objRequest->get('ip');
        $strPuerto              = $objRequest->get('puerto');
        $strIpDestino           = $objRequest->get('ipDestino');
        $strTicket              = $objRequest->get('ticket');
        $strJsonCorreos         = $objRequest->get('jsonCorreos');
        $intIdDetalle           = $objRequest->get('idDetalle');
        $strEstadoGestion       = $objRequest->get('estadoGestion');
        $strLoginAdicional      = $objRequest->get('loginAdicional');
        
        $strTipoCaso                = "";
        $strEstado                  = "Activo";
        $strCodPlantilla            = "";
        $strDescripcionPlantilla    = "CODIGO DE PLANTILLA ECUCERT";
        $strDescripcion             = "PARAMETROS PARA CREAR TAREA";
        $strDescripcionCorreo       = "PARAMETROS PARA ENVIAR CORREO";
        $strDescripcionEstadoInc    = "PARAMETROS PARA ESTADO INCIDENCIA";
        $arrayParamPlantilla        = array(
                                    'nombreParametro' => "PLANTILLAS DE NOTIFICACIONES",
                                    'estado'          => $strEstado
                                    );

        try
        {
            $arrayCorreos             = json_decode($strJsonCorreos,true);   
            $entityInfoIncidenciaDet  = $emSoporte->getRepository('schemaBundle:InfoIncidenciaDet')
                                                  ->findOneById($intIncidenciaDetId);
            $strFechaIncidencia       = $entityInfoIncidenciaDet->getFeIncidencia();

            if(!empty($strLoginAdicional))
            {
                $strLogin  = $strLoginAdicional;
                $entityInfoIncidenciaDet->setLoginAdicional($strLoginAdicional);
                $emSoporte->persist($entityInfoIncidenciaDet);
                $emSoporte->flush();
            }

            if(!empty($intIdPerEmpRol))
            {
                $entityInfoPersonaEmpRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->findOneById($intIdPerEmpRol);

                $entityInfoPersona        = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->findOneById($entityInfoPersonaEmpRol->getPersonaId());

                $strNombreCliente         = "// ".$entityInfoPersona->getRazonSocial();
                if(empty($strNombreCliente))
                {
                    $strNombreCliente = "// ".$entityInfoPersona->getNombres()." ".$entityInfoPersona->getApellidos();
                }
            }

            $arrayParamEcucert          = array(
                                            'nombreParametro' => "PARAMETROS_ECUCERT",
                                            'estado'          => $strEstado
                                        );

            $entityParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneByNombreParametro($arrayParamEcucert);
            
            $entityParamPlantillaCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneByNombreParametro($arrayParamPlantilla);

            $intIdParametroCargo = 0;
            if( isset($entityParametroCab) && !empty($entityParametroCab) )
            {
                $intIdParametroCargo = $entityParametroCab->getId();
            }
            
            $intIdParamPlantilla = 0;
            if( isset($entityParamPlantillaCab) && !empty($entityParamPlantillaCab) )
            {
                $intIdParamPlantilla = $entityParamPlantillaCab->getId();
            }  

            if(!empty($strSubCategoria))
            {
                $arrayParamPlantillaDet  = array( 
                                            'estado'      => $strEstado, 
                                            'parametroId' => $intIdParamPlantilla,
                                            'descripcion' => $strDescripcionPlantilla,
                                            'valor1'      => strtoupper($strCategoria),
                                            'valor2'      => strtoupper($strSubCategoria),
                                            'valor3'      => strtoupper($strTipoEvento),
                                            'empresaCod'  => $intCodEmpresa
                                        );
            }
            else
            {
                $arrayParamPlantillaDet  = array( 
                                            'estado'      => $strEstado, 
                                            'parametroId' => $intIdParamPlantilla,
                                            'descripcion' => $strDescripcionPlantilla,
                                            'valor1'      => strtoupper($strCategoria),
                                            'valor2'      => null,
                                            'valor3'      => strtoupper($strTipoEvento),
                                            'empresaCod'  => $intCodEmpresa
                                        );
            }
            
            $objParamPlantillaDetEcu = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy($arrayParamPlantillaDet);

            if( isset($objParamPlantillaDetEcu) && !empty($objParamPlantillaDetEcu) )
            {
                $strCodPlantilla   = $objParamPlantillaDetEcu->getValor4() ? $objParamPlantillaDetEcu->getValor4() : '';
            }

            if(!empty($strCodPlantilla))
            {

                $arrayParametrosDet  = array( 
                                            'estado'      => $strEstado, 
                                            'parametroId' => $intIdParametroCargo,
                                            'descripcion' => $strDescripcion
                                        );

                $objParametroDetEcu = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosDet);

                if( isset($objParametroDetEcu) && !empty($objParametroDetEcu) )
                {
                    $strNombreProceso   = $objParametroDetEcu->getValor4() ? $objParametroDetEcu->getValor4() : '';
                }

                $arrayParametrosDet['descripcion']  = $strDescripcionCorreo;
                $objParametroDetCorreo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosDet);

                if( isset($objParametroDetCorreo) && !empty($objParametroDetCorreo) )
                {
                    $strDescFormContact = $objParametroDetCorreo->getValor1() ? $objParametroDetCorreo->getValor1() : '';
                    $strContacto        = $objParametroDetCorreo->getValor2() ? $objParametroDetCorreo->getValor2() : '';
                    $strAsunto          = $strNombreProceso." ".$objParametroDetCorreo->getValor4() ? $objParametroDetCorreo->getValor4() : '';
                }

                $arrayParametrosDet['descripcion']  = $strDescripcionEstadoInc;
                $objParametroDetEstadoInc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosDet);

                if( isset($objParametroDetEstadoInc) && !empty($objParametroDetEstadoInc) )
                {
                    $strEstadoAnalisis      = $objParametroDetEstadoInc->getValor3() ? $objParametroDetEstadoInc->getValor3() : '';
                    $strEstadoNotif         = $objParametroDetEstadoInc->getValor4() ? $objParametroDetEstadoInc->getValor4() : '';
                    $strEstadoNotifFallo    = "No ".$strEstadoNotif;
                }

                $objInfoCaso                = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($strIdcaso);
                if(isset($objInfoCaso) && !empty($objInfoCaso))
                {
                    $strTipoCaso    = $objInfoCaso->getTipoCasoId()->getNombreTipoCaso();
                    $strAsunto      = $strAsunto." ".$strTipoEvento.":".str_replace("_","",$strCategoria).
                                        " // Caso:  ".$objInfoCaso->getNumeroCaso()." ".$strNombreCliente.
                                        " // Login:  ".$strLogin;
                }
                else
                {
                    $strAsunto      = $strAsunto." ".$strTipoEvento.":".$strCategoria.
                                        " ".$strNombreCliente." // Login:  ". $strLogin;
                }

                $arrayParametrosEnvio   = array('idPersonaEmpresaRol'         => $intIdPerEmpRol,
                                                'intIdPunto'                  => $intIdPunto,
                                                'estado'                      => $strEstado,                                        
                                                'strDescFormContact'          => $strDescFormContact,
                                                'strContacto'                 => $strContacto,
                                                'strEstadoNotificacionIn'     => $strEstadoNotifFallo,
                                                'strEstadoNotificacionEn'     => $strEstadoNotif,
                                                'asunto'                      => $strAsunto,
                                                'codPlantilla'                => $strCodPlantilla,
                                                'idCaso'                      => $strIdcaso,
                                                'empresa'                     => $intCodEmpresa,
                                                'idEmpresa'                   => $intCodEmpresa,
                                                'strLoginAfectado'            => $strLogin,
                                                'tipoCaso'                    => $strTipoCaso,
                                                'caso'                        => $objInfoCaso,
                                                'strUsrCreacion'              => $strUsrCreacion,
                                                'strIpCreacion'               => $strIpCreacion,
                                                'intIncidenciaDetId'          => $intIncidenciaDetId,
                                                'login'                       => $strLogin,
                                                'ip'                          => $strIp,
                                                'puerto'                      => $strPuerto,
                                                'ipDestino'                   => $strIpDestino,
                                                'ticket'                      => $strTicket,
                                                'nombreCliente'               => $strNombreCliente,
                                                'timestamp'                   => $strFechaIncidencia,
                                                'arrayCorreos'                => $arrayCorreos,
                                                'intIdDetalle'                => $intIdDetalle
                                            );

                $arrayRespuestaNotif  = $serviceSoporte->enviarPlantillaECUCERT($arrayParametrosEnvio);

                $arrayParametrosGest    = array('intIncidenciaDetId' => $intIncidenciaDetId,
                                                'strEstado'          => $strEstadoAnalisis,
                                                'strUsrMod'          => $strUsrCreacion,
                                                'strIpMod'           => $strIpCreacion);

                if($strEstadoGestion != "Atendido")
                {
                    $serviceSoporte->modificarEstadoGestIncEcucert($arrayParametrosGest);
                }

                $strRespuestaFinal = $arrayRespuestaNotif['strMensaje'];
            }
            else
            {
                $strRespuestaFinal = "ERROR: No se encontró la plantilla para ".$strCategoria.". Por favor comunicarse con CERT";
            }
        }
        catch(\Exception $ex)
        {
            $strRespuestaFinal = "ERROR: No se pudo enviar el correo. Por favor reintente";
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.enviarCorreoCliente', 
                                        $ex->getMessage(),
                                        $strUsrCreacion,
                                        $strIpCreacion); 


        }
        $arrayResultadoRegistro = array('estado'    =>  $strRespuestaFinal );
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;          
    }
   

    /**
     * Función que cambia de estado de Gestión de la IP asociado al ticket.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 11-08-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 20-02-2020 -  Se agrega un seguimiento al cambio de estado de gestión
     * @since 1.0
     * 
     */
    public function cambiarEstadoGestionAction()
    {
        ini_set('max_execution_time', 3000000);

        $objJsonRespuesta   = new JsonResponse();
        $serviceUtil        = $this->get('schema.Util');
        
        $objRequest                 = $this->getRequest(); 
        $strUsrCreacion             = $objRequest->getSession()->get('user');
        $strIpCreacion              = $objRequest->getClientIp();
        $emSoporte                  = $this->getDoctrine()->getManager("telconet_soporte");
        $intDetalleIncidenciaId     = $objRequest->get('DetalleIncidenciaId') ?: '';
        $strEstadoGestion           = $objRequest->get('estado') ?: 'Atendido';
        $intIdEmpresa               = $objRequest->getSession()->get('idEmpresa');
        $intIdDetalle               = $objRequest->get('idDetalle') ?: '';

        try
        {      
            $emSoporte->getConnection()->beginTransaction(); 
            $entityInfoIncidenciaDet = $emSoporte->getRepository('schemaBundle:InfoIncidenciaDet')
                                                 ->findOneById($intDetalleIncidenciaId);           
            $entityInfoIncidenciaDet->setEstadoGestion($strEstadoGestion);
            $emSoporte->persist($entityInfoIncidenciaDet);
            $emSoporte->flush();

            $entityInfoIncidenciaDetHist = new  InfoIncidenciaDetHist();
            $entityInfoIncidenciaDetHist->setDetalleIncidenciaId($intDetalleIncidenciaId);
            $entityInfoIncidenciaDetHist->setEstado($strEstadoGestion);
            $entityInfoIncidenciaDetHist->setUsrCreacion($strUsrCreacion);
            $entityInfoIncidenciaDetHist->setFeCreacion(new \DateTime('now'));
            $entityInfoIncidenciaDetHist->setIpCreacion($strIpCreacion);
            $emSoporte->persist($entityInfoIncidenciaDetHist);
            $emSoporte->flush();

            if(isset($intIdDetalle) && !empty($intIdDetalle))
            {
                $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
                $objInfoTareaSeguimiento->setDetalleId($intIdDetalle);
                $objInfoTareaSeguimiento->setObservacion('Cambio de estado de gestión a: '.$strEstadoGestion);
                $objInfoTareaSeguimiento->setUsrCreacion($strUsrCreacion);
                $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                $objInfoTareaSeguimiento->setEmpresaCod($intIdEmpresa);
                $objInfoTareaSeguimiento->setEstadoTarea("");
                $objInfoTareaSeguimiento->setInterno("");
                $objInfoTareaSeguimiento->setDepartamentoId("");
                $objInfoTareaSeguimiento->setPersonaEmpresaRolId("");
                $emSoporte->persist($objInfoTareaSeguimiento);
                $emSoporte->flush();
            }

            $emSoporte->commit();
            $emSoporte->flush();

            $strRespuesta = "Se actualizó el registro correctamente";
        }
        catch(\Exception $ex)
        {
            $strRespuesta = "Error: No se pudo cambiar de estado de Gestión. Por favor reintente";
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.cambiarEstadoGestionAction', 
                                        $ex->getMessage(),
                                        $strUsrCreacion,
                                        $strIpCreacion); 
        }
        $arrayResultadoRegistro = array('estado'    =>  $strRespuesta );
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;          
    }

    /**
     * Función que cambia de estado de Notificación de la IP asociado al ticket.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 22-08-2019
     * 
     */
    public function cambiarEstadoNotificacionAction()
    {
        ini_set('max_execution_time', 3000000);

        $objJsonRespuesta   = new JsonResponse();
        $serviceUtil        = $this->get('schema.Util');
        
        $objRequest                 = $this->getRequest(); 
        $strUsrCreacion             = $objRequest->getSession()->get('user');
        $strIpCreacion              = $objRequest->getClientIp();
        $intDetalleIncidenciaId     = $objRequest->get('DetalleIncidenciaId') ?: '';
        $strJsonCorreos             = $objRequest->get('jsonCorreos');
        $serviceSoporte             = $this->get('soporte.SoporteService');
        $strEstadoNotificacionEn    = 'Notificado';  

        $arrayParametrosNotificacion = array(
                                            'intIncidenciaDetId'   => $intDetalleIncidenciaId,
                                            'strCorreo'            => null,
                                            'strEstado'            => $strEstadoNotificacionEn,
                                            'strUsrCreacion'       => $strUsrCreacion,
                                            'strIpCreacion'        => $strIpCreacion);

        try
        {   
            $arrayCorreos           = json_decode($strJsonCorreos,true);       
            foreach($arrayCorreos as $arrayContacto)
            {   
                $arrayParametrosNotificacion['strCorreo']       = $arrayContacto['correo'];
                $arrayParametrosNotificacion['strEstado']       = $strEstadoNotificacionEn;
                $arrayParametrosNotificacion['strTipoContacto'] = $arrayContacto['tipoUsuario'];
                
                $arrayRespuestaNotif  = $serviceSoporte->guardarNotificacionIncidenciaEcucert($arrayParametrosNotificacion);
            }
            if($arrayRespuestaNotif["strMensaje"] == "Enviado")
            {
                $strRespuesta = "Se actualizó el registro correctamente";
            }
            else
            {
                $strRespuesta = $arrayRespuestaNotif["strMensaje"];
            }
           
        }
        catch(\Exception $ex)
        {
            $strRespuesta = "Error: No se pudo cambiar de estado de Notificación. Por favor reintente";
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.cambiarEstadoNotificacionAction', 
                                        $ex->getMessage(),
                                        $strUsrCreacion,
                                        $strIpCreacion); 
        }
        $arrayResultadoRegistro = array('estado'    =>  $strRespuesta );
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;          
    }

    /**
     * Función que genera reporte de las incidencias.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 20-03-2019
     * 
     */
    public function generarReporteAction()
    {
        ini_set('max_execution_time', 3000000);

        $objJsonRespuesta   = new JsonResponse();
        $serviceUtil        = $this->get('schema.Util');
        
        $objRequest         = $this->getRequest(); 
        $serviceSoporte     = $this->get('soporte.SoporteService');
        $strUsrCreacion     = $objRequest->getSession()->get('user');
        $strIpCreacion      = $objRequest->getClientIp();
        $strFechaInicio     = $objRequest->get('fechaInicio') ?: '';
        $strFechaFin        = $objRequest->get('fechaFin') ?: '';
        $strNoTicket        = $objRequest->get('noTicket') ?: '';
        $strFeEmisionDesde  = "";
        $strFeEmisionHasta  = "";
        
        if(isset($strFechaInicio) && !empty($strFechaInicio) && isset($strFechaFin) && !empty($strFechaFin))
        {
            $strFeEmisionDesde  = date("d/m/y", strtotime($strFechaInicio)); 
            $strFeEmisionHasta  = date("d/m/y", strtotime($strFechaFin)); 
        }
        
        $arrayParametros    = array ('strIpCreacion'    => $strIpCreacion,
                                     'strFechaInicio'   => $strFeEmisionDesde,
                                     'strFechaFin'      => $strFeEmisionHasta,
                                     'strNoTicket'      => $strNoTicket,
                                     'strUsrCreacion'   => $strUsrCreacion);

        try
        {       
            $arrayRespuestoReporte  = $serviceSoporte->generarReporteEcucert($arrayParametros);
            $strRespuesta           = $arrayRespuestoReporte['strMensaje'];
        }
        catch(\Exception $ex)
        {
            $strRespuesta = "Error: No se pudo generar reporte. Por favor reintente";
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.generarReporte', 
                                        $ex->getMessage(),
                                        $strUsrCreacion,
                                        $strIpCreacion); 
        }
        $arrayResultadoRegistro = array('estado'    =>  $strRespuesta );
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;          
    }
    
    /**
     * Función que busca a que cliente pertenence al IP para reprocesarlo y notificarlo.
     *
     * @param array $arrayParametros 
     * [
     *       $strIp                  - IP de la incidencia
     *       $intDetalleIncidencia   - Id del detalla de la incidencia  
     *       $strNoTicket            - Número de ticket
     *       $strSubCategoria        - Sub Categoria de la incidencia
     *       $strCategoria           - Categoría de la incidencia
     *       $strTipoEvento          - Tipo de Evento de la incidencia
     *       $strFechaIncidencia     - Fecha que se cometió la incidencia
     *       $strEstadoIncidencia    - Estado de la incidencia
     * ]
     * 
     * @return string
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 25-04-2019
     * 
     */
    public function reprocesarIPAction()
    {
        ini_set('max_execution_time', 3000000);

        $objJsonRespuesta   = new JsonResponse();
        $serviceUtil        = $this->get('schema.Util');
        
        $objRequest         = $this->getRequest(); 
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $objEcucertService  = $this->get('tecnico.EcucertService');
        $strUsrCreacion     = $objRequest->getSession()->get('user');
        $strIpCreacion      = $objRequest->getClientIp();
        $strIpAddresss      = $objRequest->get('ipAddress') ?: '';
        $intIdDetalleInc    = $objRequest->get('idDetalleIncidencia') ?: '';
        $strNoTicket        = $objRequest->get('noTicket') ?: '';
        $strSubCategoria    = $objRequest->get('subCategoria') ?: '';
        $strCategoria       = $objRequest->get('categoria') ?: '';
        $strTipoEvento      = $objRequest->get('tipoEvento') ?: '';
        $strFechaIncidencia = $objRequest->get('feIncidencia') ?: '';
        $strEstadoIncidencia= $objRequest->get('estadoIncidencia') ?: '';
        
        try
        {
            $entityInfoInciDet          = $emSoporte->getRepository('schemaBundle:InfoIncidenciaDet')
                                                    ->findOneById($intIdDetalleInc);
            
            if(isset($entityInfoInciDet) && !empty($entityInfoInciDet))
            {
                $strIpDestino   = $entityInfoInciDet->getIpDestino();
                $strPuerto      = $entityInfoInciDet->getPuerto();
            }

            $arrayParametrosGest    = array('strIpAddresss'         => $strIpAddresss,
                                            'intIdIncidencia'       => $intIdDetalleInc,
                                            'strFechaIncidencia'    => $strFechaIncidencia,
                                            'strPuerto'             => $strPuerto,
                                            'strNoTicket'           => $strNoTicket,
                                            'strCategoria'          => $strCategoria,
                                            'strSubCategoria'       => $strSubCategoria,
                                            'strTipoEvento'         => $strTipoEvento,
                                            'strIpDestino'          => $strIpDestino,
                                            'strUserMod'            => $strUsrCreacion,
                                            'strIpMod'              => $strIpCreacion,
                                            'strEstadoIncidencia'   => $strEstadoIncidencia);
            
            $arrayRespuesto = $objEcucertService->reprocesarIncidenciaEcucert($arrayParametrosGest);
            $strResultado   = $arrayRespuesto['strMensaje'];
        }
        catch(\Exception $ex)
        {
            $strResultado = "Error: No se pudo reprocesar el cliente. Por favor reintente";
            $serviceUtil->insertError(  'Telcos', 
                                        'CasoEcucertController.reprocesarIP', 
                                        $ex->getMessage(),
                                        $strUsrCreacion,
                                        $strIpCreacion); 
        }
        $arrayResultadoRegistro = array('estado'    =>  $strResultado );
        $objJsonRespuesta->setData($arrayResultadoRegistro);
        return $objJsonRespuesta;  

    }
    
}
