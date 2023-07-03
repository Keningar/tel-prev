<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoSnmpElemento;
use telconet\schemaBundle\Form\InfoElementoUpsType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Fill;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;

/**
 * InfoElementoUpsController controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la administración de UPS de la empresa
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 13-01-2016
 */
class InfoElementoUpsController extends Controller
{
    const TIPO_ELEMENTO      = 'UPS';
    const ESTADO_ACTIVO      = 'Activo';
    const ESTADO_ELIMINADO   = 'Eliminado';
    const ESTADO_CONNECTED   = 'connected';
    const ESTADO_NOTCONNECT  = 'not connect';
    const VALOR_INICIAL_BUSQUEDA = 0;
    const VALOR_LIMITE_BUSQUEDA = 100;
    
    
    /**
     * @Secure(roles="ROLE_326-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redireccion a la pantalla principal de la administracion de ups de la empresa
     * @return render.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-01-2016
     */
    public function indexAction()
    {
        if (true === $this->get('security.context')->isGranted('ROLE_326-6'))
        {
            $rolesPermitidos[] = 'ROLE_326-6'; //Ver la información del UPS
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_326-4'))
        {
            $rolesPermitidos[] = 'ROLE_326-4'; //Editar un UPS
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_326-8'))
        {
            $rolesPermitidos[] = 'ROLE_326-8'; //Eliminar UPS
        }
        
        return $this->render('tecnicoBundle:InfoElementoUps:index.html.twig', array('rolesPermitidos' => $rolesPermitidos));
    }
    
    
    /**
     * @Secure(roles="ROLE_326-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra el listado de todos los UPS creados.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-01-2016
     */
    public function gridAction()
    {
        $jsonResponse           = new JsonResponse();
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombre              = $objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : "";
        $intModelo              = $objRequest->query->get('modelo') ? $objRequest->query->get('modelo') : "";
        $strIp                  = $objRequest->query->get('ip') ? $objRequest->query->get('ip') : "";
        $strFeInstalacion       = $objRequest->query->get('feInstalacion') ? $objRequest->query->get('feInstalacion') : "";
        $arrayTmpFeInstalacion  = explode('T', $strFeInstalacion);
        $arrayFeInstalacion     = $arrayTmpFeInstalacion[0] ? explode('-', $arrayTmpFeInstalacion[0]) : array();
        $strFeInstalacion       = $arrayFeInstalacion ? $arrayFeInstalacion[2].'-'.$arrayFeInstalacion[1].'-'.$arrayFeInstalacion[0] : '';
        $intStart               = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit               = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $serviceInfoElemento    = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento     = array( self::TIPO_ELEMENTO );
        $arrayModelosElemento   = $intModelo ? array( $intModelo ) : array(); 
        
        $arrayParametros = array(
                                    'intStart'             => $intStart,
                                    'intLimit'             => $intLimit,
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => strtolower(self::TIPO_ELEMENTO),
                                    'criterios'            => array( 'nombre'                => $strNombre, 
                                                                     'tipoElemento'          => $arrayTiposElemento,
                                                                     'modeloElemento'        => $arrayModelosElemento,
                                                                     'feInstalacionBaterias' => $strFeInstalacion,
                                                                     'ipElemento'            => $strIp
                                                                   )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);
        
        $jsonResponse->setData( $arrayResultados );
        
        return $jsonResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_326-2")
     * 
     * Documentación para el método 'newAction'.
     *
     * Muestra usado para mostrar el formulario vacío para crear un UPS.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-01-2016
     */
    public function newAction()
    {
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTipoElemento        = self::TIPO_ELEMENTO;
        $intIdElementoBateria   = 0;
        $arrayModelosElementos  = array();
        $arrayTmpParametros     = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array($strTipoElemento) );
        $arrayTmpResultados     = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                    ->getModeloElementosByCriterios( $arrayTmpParametros );
        $objInfoElemento        = new InfoElemento();
        $form                   = $this->createForm(new InfoElementoUpsType(), $objInfoElemento);
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
        }
        
        $objElementoBateria = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento('BATERIA');
        
        if( $objElementoBateria )
        {
            $intIdElementoBateria = $objElementoBateria->getId();
        }

        return $this->render( 'tecnicoBundle:InfoElementoUps:new.html.twig', array(
                                                                                    'modelosElemento'   => $arrayModelosElementos,
                                                                                    'strTipoElemento'   => $strTipoElemento,
                                                                                    'entity'            => $objInfoElemento,
                                                                                    'form'              => $form->createView(),
                                                                                    'idBateriaElemento' => $intIdElementoBateria
                                                                                  ) 
                            );
    }
    
    
    /**
     * Documentación para el método 'getParametrosDetAction'.
     *
     * Función usada para retornar los detalles de los parametros de acuerdo al parametro cabecera
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-01-2016
     */
    public function getParametrosDetAction($strNombreParametroCab)
    {
        $response           = new Response();
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $intIdParametroCab  = 0;
        $jsonResultado      = null;
        
        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                     ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'nombreParametro' => $strNombreParametroCab) );
        
        if( $objParametroCab )
        {
            $intIdParametroCab = $objParametroCab->getId();
            
            $arrayParametros = array( 'estado' => self::ESTADO_ACTIVO, 'parametroId' => $intIdParametroCab );
            $jsonResultado   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getJSONParametrosByCriterios( $arrayParametros );
        }//($objParametroCab)
        
        $response->setContent($jsonResultado);
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'getSwitchesAction'
     * 
     * Función que retorna los switches existentes en el sistema.
     * 
     * @return response $objResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-01-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 18-02-2017 - Se quita enviar el parámetro 'nombreElemento' puesto que ya se envía el 'tipoElemento' perteneciente al 'SWITCH'
     */
    public function getSwitchesAction()
    {
        $objResponse       = new Response();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $intIdEmpresa      = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdNodo         = $objRequest->query->get('nodo') ? $objRequest->query->get('nodo') : 0;
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        if( $intIdEmpresa != 0 )
        {
            $intIdTipoElemento   = 0;
            $objAdmiTipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                     ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'nombreTipoElemento' => 'SWITCH') );
            
            if( $objAdmiTipoElemento )
            {
                $intIdTipoElemento = $objAdmiTipoElemento->getId();
            }
            
            $arrayParametros = array( 'empresa'            => $intIdEmpresa,
                                      'estado'             => self::ESTADO_ACTIVO,
                                      'intIdElementoPadre' => $intIdNodo,
                                      'tipoElemento'       => $intIdTipoElemento );
            
            $jsonRespuesta = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->generarJsonElementosPorParametros( $arrayParametros );
            
            $objResponse->setContent($jsonRespuesta);
        }//if( $intIdEmpresa != 0 )
        
        return $objResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_326-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Guarda un UPS.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     */
    public function createAction()
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdEmpresaSession  = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strPrefijoEmpresa    = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strUserSession       = $objSession->get('user');
        $strIpUserSession     = $objRequest->getClientIp();
        $datetimeActual       = new \DateTime('now');
        $intIdNodo            = $objRequest->request->get('intIdNodo') ? $objRequest->request->get('intIdNodo') : 0;
        $intIdClase           = $objRequest->request->get('intIdClase') ? $objRequest->request->get('intIdClase') : 0;
        $intIdPuerto          = $objRequest->request->get('intIdPuerto') ? $objRequest->request->get('intIdPuerto') : 0;
        $intIdSnmp            = $objRequest->request->get('intIdSnmp') ? $objRequest->request->get('intIdSnmp') : 0;
        $arrayPostUps         = $objRequest->get('infoElementoUps');
        $strNombreElemento    = $arrayPostUps['nombreElemento'] ? $arrayPostUps['nombreElemento'] : '';
        $strIp                = $arrayPostUps['ipElemento'] ? $arrayPostUps['ipElemento'] : '';
        $intIdModeloElemento  = $objRequest->request->get('modeloElementoId') ? $objRequest->request->get('modeloElementoId') : 0;
        $strTAcceso           = $objRequest->request->get('tAcceso') ? $objRequest->request->get('tAcceso') : '';
        $strIac               = $objRequest->request->get('iac') ? $objRequest->request->get('iac') : '';
        $strNumSerie          = $objRequest->request->get('numSerie') ? $objRequest->request->get('numSerie') : '';
        $strGenerador         = $objRequest->request->get('generador') ? $objRequest->request->get('generador') : '';
        $strObservacion       = $arrayPostUps['observacion'] ? $arrayPostUps['observacion'] : '';
        $jsonBaterias         = $objRequest->request->get('baterias') ? json_decode($objRequest->request->get('baterias')) : null;

        $objInfoElemento = new InfoElemento();
        $form            = $this->createForm(new InfoElementoUpsType(), $objInfoElemento);
        
        $emInfraestructura->getConnection()->beginTransaction();	
        
        try
        {
            /*
             * Bloque que guarda el InfoElemento
             */
            $objInfoEmpresaElementoUbicaNodo = null;
            $objModeloElemento               = null;
            
            if( $intIdModeloElemento )
            {
                $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($intIdModeloElemento);
            }
            
            $intIdEmpresa = $intIdEmpresaSession;
            
            $strObservacionHistorial = 'Se crea el nuevo UPS';
                    
            $objInfoElemento->setModeloElementoId($objModeloElemento);
            $objInfoElemento->setSerieFisica(trim($strNumSerie));
            $objInfoElemento->setNombreElemento(trim($strNombreElemento));
            $objInfoElemento->setDescripcionElemento("DISPOSITIVO UPS DE ".$strPrefijoEmpresa);
            $objInfoElemento->setObservacion(trim($strObservacion));
            $objInfoElemento->setEstado(self::ESTADO_ACTIVO);
            $objInfoElemento->setFeCreacion($datetimeActual);
            $objInfoElemento->setUsrCreacion($strUserSession);
            $objInfoElemento->setIpCreacion($strIpUserSession);
            $emInfraestructura->persist($objInfoElemento);
            $emInfraestructura->flush();
            /*
             * Fin del Bloque que guarda el InfoElemento
             */
            
            
            /*
             * Bloque que guarda los detalles del InfoElemento
             */
            $strClase        = 'NO TIENE CLASE';
            $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'id' => $intIdClase) );
        
            if( $objParametroDet )
            {
                $strClase = $objParametroDet->getDescripcion();
            }
            
            $intIdElemento     = $objInfoElemento->getId();
            $arrayInfoDetalles = array( 'CLASE'         => $strClase, 
                                        'TIEMPO_ACCESO' => $strTAcceso, 
                                        'IAC'           => $strIac, 
                                        'GENERADOR'     => $strGenerador );
            
            foreach($arrayInfoDetalles as $strKey => $strValue)
            {
                if( $strValue )
                {
                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setElementoId($intIdElemento);
                    $objInfoDetalleElemento->setDetalleNombre($strKey);
                    $objInfoDetalleElemento->setDetalleValor(trim($strValue));
                    $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                    $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                    $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                    $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                    $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                }
            }
            /*
             * Fin del Bloque que guarda los detalles del InfoElemento
             */
            
            
            /*
             * Bloque que guarda la relación del InfoElemento con la empresa en sessión del usuario
             */
            $objInfoEmpresaElemento = new InfoEmpresaElemento();
            $objInfoEmpresaElemento->setElementoId($objInfoElemento);
            $objInfoEmpresaElemento->setEmpresaCod($intIdEmpresa);
            $objInfoEmpresaElemento->setObservacion($strObservacion);
            $objInfoEmpresaElemento->setFeCreacion($datetimeActual);
            $objInfoEmpresaElemento->setUsrCreacion($strUserSession);
            $objInfoEmpresaElemento->setIpCreacion($strIpUserSession);
            $objInfoEmpresaElemento->setEstado(self::ESTADO_ACTIVO);
            $emInfraestructura->persist($objInfoEmpresaElemento);
            $emInfraestructura->flush();
            /*
             * Fin del Bloque que guarda la relación del InfoElemento con la empresa en sessión del usuario
             */
            
            
            /*
             * Bloque que guarda el historial del InfoElemento
             */
            $objInfoHistorialElemento = new InfoHistorialElemento();
            $objInfoHistorialElemento->setElementoId($objInfoElemento);
            $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
            $objInfoHistorialElemento->setFeCreacion($datetimeActual);
            $objInfoHistorialElemento->setUsrCreacion($strUserSession);
            $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
            $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
            $emInfraestructura->persist($objInfoHistorialElemento);
            $emInfraestructura->flush();
            /*
             * Fin del Bloque que guarda el historial del InfoElemento
             */
            
            
            /*
             * Bloque que guarda la InfoRelacionElemento entre el Nodo y el Elemento UPS
             */
            $objRelacionElemento = new InfoRelacionElemento();
            $objRelacionElemento->setElementoIdA($intIdNodo);
            $objRelacionElemento->setElementoIdB($intIdElemento);
            $objRelacionElemento->setTipoRelacion("CONTIENE");
            $objRelacionElemento->setObservacion("Nodo contiene UPS");
            $objRelacionElemento->setEstado(self::ESTADO_ACTIVO);
            $objRelacionElemento->setUsrCreacion($strUserSession);
            $objRelacionElemento->setFeCreacion($datetimeActual);
            $objRelacionElemento->setIpCreacion($strIpUserSession);
            $emInfraestructura->persist($objRelacionElemento);
            $emInfraestructura->flush();
            /*
             * Fin Bloque que guarda la InfoRelacionElemento entre el Nodo y el Elemento UPS
             */
            
            
            /*
             * Bloque que guarda la InfoEnlace entre el Switch y el UPS
             */
            if( $intIdPuerto )
            {
                $strNombreInterfazElemento = 'Eth0'; 

                $objInfoInterfaceElemento = new InfoInterfaceElemento();
                $objInfoInterfaceElemento->setElementoId($objInfoElemento);
                $objInfoInterfaceElemento->setEstado(self::ESTADO_NOTCONNECT);
                $objInfoInterfaceElemento->setUsrCreacion($strUserSession);
                $objInfoInterfaceElemento->setFeCreacion($datetimeActual);
                $objInfoInterfaceElemento->setIpCreacion($strIpUserSession);
                $objInfoInterfaceElemento->setNombreInterfaceElemento($strNombreInterfazElemento);
                $objInfoInterfaceElemento->setDescripcionInterfaceElemento('INTERFACE UPS: '.$strNombreInterfazElemento);
                $emInfraestructura->persist($objInfoInterfaceElemento);
                $emInfraestructura->flush();

                $objAdmiTipoMedio   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                        ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'nombreTipoMedio' => 'Cobre') );

                $objInterfaceSwitch = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->findOneBy( array('estado' => self::ESTADO_NOTCONNECT, 'id' => $intIdPuerto) );

                $objInfoEnlace = new InfoEnlace();
                $objInfoEnlace->setInterfaceElementoFinId($objInfoInterfaceElemento);
                $objInfoEnlace->setInterfaceElementoIniId($objInterfaceSwitch);
                $objInfoEnlace->setTipoEnlace('PRINCIPAL');
                $objInfoEnlace->setEstado(self::ESTADO_ACTIVO);
                $objInfoEnlace->setUsrCreacion($strUserSession);
                $objInfoEnlace->setFeCreacion($datetimeActual);
                $objInfoEnlace->setIpCreacion($strIpUserSession);
                $objInfoEnlace->setTipoMedioId($objAdmiTipoMedio);
                $emInfraestructura->persist($objInfoEnlace);
                $emInfraestructura->flush();

                $objInfoInterfaceElemento->setEstado(self::ESTADO_CONNECTED);
                $emInfraestructura->persist($objInfoInterfaceElemento);
                $emInfraestructura->flush();

                $objInterfaceSwitch->setEstado(self::ESTADO_CONNECTED);
                $emInfraestructura->persist($objInterfaceSwitch);
                $emInfraestructura->flush();
            }//( $intIdPuerto )
            /*
             * Fin Bloque que guarda la InfoEnlace entre el Switch y el UPS
             */
            
            
            /*
             * Bloque que guarda la InfoIP del Elemento UPS
             */
            $objInfoIp = new InfoIp();
            $objInfoIp->setElementoId($intIdElemento);
            $objInfoIp->setIp($strIp);
            $objInfoIp->setVersionIp('IPV4');
            $objInfoIp->setTipoIp('FIJA');
            $objInfoIp->setEstado(self::ESTADO_ACTIVO);
            $objInfoIp->setUsrCreacion($strUserSession);
            $objInfoIp->setFeCreacion($datetimeActual);
            $objInfoIp->setIpCreacion($strIpUserSession);
            $emInfraestructura->persist($objInfoIp);
            $emInfraestructura->flush();
            /*
             * Fin Bloque que guarda la InfoIP del Elemento UPS
             */
            
            
            /*
             * Bloque que guarda la InfoSnmpElemento del Elemento UPS
             */
            $objAdmiSnmp = $emInfraestructura->getRepository('schemaBundle:AdmiSnmp')->findOneBy( array('estado' => self::ESTADO_ACTIVO, 
                                                                                                        'id'     => $intIdSnmp) );
            
            $objInfoSnmpElemento = new InfoSnmpElemento();
            $objInfoSnmpElemento->setSnmpId($objAdmiSnmp);
            $objInfoSnmpElemento->setElementoId($objInfoElemento);
            $objInfoSnmpElemento->setUsrCreacion($strUserSession);
            $objInfoSnmpElemento->setFeCreacion($datetimeActual);
            $objInfoSnmpElemento->setIpCreacion($strIpUserSession);
            $emInfraestructura->persist($objInfoSnmpElemento);
            $emInfraestructura->flush();
            /*
             * Fin Bloque que guarda la InfoSnmpElemento del Elemento UPS
             */
            
            
            /*
             * Bloque que guarda la InfoElementoUbicacion del Elemento UPS
             */
            if( $intIdNodo )
            {
                $objInfoElementoNodo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                         ->findOneBy( array( 'estado' => self::ESTADO_ACTIVO,
                                                                             'id'     => $intIdNodo ) );

                if( $objInfoElementoNodo )
                {
                    $objInfoEmpresaElementoUbicaNodo = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                         ->findOneBy( array( 'elementoId' => $objInfoElementoNodo,
                                                                                             'empresaCod' => $intIdEmpresa ) );

                    if( $objInfoEmpresaElementoUbicaNodo )
                    {
                        $objInfoEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                        $objInfoEmpresaElementoUbica->setEmpresaCod($intIdEmpresa);
                        $objInfoEmpresaElementoUbica->setElementoId($objInfoElemento);
                        $objInfoEmpresaElementoUbica->setUbicacionId($objInfoEmpresaElementoUbicaNodo->getUbicacionId());
                        $objInfoEmpresaElementoUbica->setUsrCreacion($strUserSession);
                        $objInfoEmpresaElementoUbica->setFeCreacion($datetimeActual);
                        $objInfoEmpresaElementoUbica->setIpCreacion($strIpUserSession);
                        $emInfraestructura->persist($objInfoEmpresaElementoUbica);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        throw new \Exception("No se encontro el Ubicación del Nodo");
                    }//( $objInfoEmpresaElementoUbicaNodo )
                }
                else
                {
                    throw new \Exception("No se encontro el Nodo en estado Activo");
                }//( $objInfoElementoNodo )
            }
            else
            {
                throw new \Exception("No se encontro el Nodo en estado Activo");
            }//( $intIdNodo )
            /*
             * Fin Bloque que guarda la InfoIP del Elemento UPS
             */
            
            
            /*
             * Bloque que guarda las baterias y su relación con el UPS
             */
            $objAdmiTipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento('BATERIA');
            $arrayBaterias       = $jsonBaterias->baterias ? $jsonBaterias->baterias : array();
            $intTotal            = $jsonBaterias->total ? $jsonBaterias->total : 0;
            $i                   = 0;

            if( $intTotal > 0 )
            {
                foreach($arrayBaterias as $objBateria)
                {
                    $i++;

                    $strModeloElementoBateria   = $objBateria->strModeloElemento;
                    $strSerieFisica             = $objBateria->strSerieFisica;
                    $strTipoBateria             = $objBateria->strTIPO_BATERIA;
                    $strAmperaje                = $objBateria->strAMPERAJE;
                    $strFechaInstalacionBateria = $objBateria->strFECHA_INSTALACION;

                    if( $strFechaInstalacionBateria )
                    {
                        $datetimeFechaInstalacion   = new \DateTime($strFechaInstalacionBateria);
                        $strFechaInstalacionBateria = $datetimeFechaInstalacion->format('d-m-Y');
                    }

                    $strNombreElementoBateria   = "Bateria ".$strNombreElemento." ".$i;
                    $objModeloElementoBateria   = null;

                    if( $strModeloElementoBateria )
                    {
                        $objModeloElementoBateria = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                                      ->findOneBy( array( 'estado'               => self::ESTADO_ACTIVO,
                                                                                          'nombreModeloElemento' => $strModeloElementoBateria,
                                                                                          'tipoElementoId'       => $objAdmiTipoElemento ) );
                    }

                    $objInfoElementoBateria = new InfoElemento();
                    $objInfoElementoBateria->setModeloElementoId($objModeloElementoBateria);
                    $objInfoElementoBateria->setSerieFisica($strSerieFisica);
                    $objInfoElementoBateria->setNombreElemento($strNombreElementoBateria);
                    $objInfoElementoBateria->setDescripcionElemento($strNombreElementoBateria);
                    $objInfoElementoBateria->setEstado(self::ESTADO_ACTIVO);
                    $objInfoElementoBateria->setFeCreacion($datetimeActual);
                    $objInfoElementoBateria->setUsrCreacion($strUserSession);
                    $objInfoElementoBateria->setIpCreacion($strIpUserSession);
                    $emInfraestructura->persist($objInfoElementoBateria);
                    $emInfraestructura->flush();


                    $intIdElementoBateria     = $objInfoElementoBateria->getId();
                    $arrayInfoDetallesBateria = array(  'AMPERAJE'          => $strAmperaje, 
                                                        'TIPO_BATERIA'      => $strTipoBateria, 
                                                        'FECHA_INSTALACION' => $strFechaInstalacionBateria );

                    foreach($arrayInfoDetallesBateria as $strKey => $strValue)
                    {
                        if( $strValue )
                        {
                            $objInfoDetalleElementoBateria = new InfoDetalleElemento();
                            $objInfoDetalleElementoBateria->setElementoId($intIdElementoBateria);
                            $objInfoDetalleElementoBateria->setDetalleNombre($strKey);
                            $objInfoDetalleElementoBateria->setDetalleValor(trim($strValue));
                            $objInfoDetalleElementoBateria->setDetalleDescripcion($strKey);
                            $objInfoDetalleElementoBateria->setFeCreacion($datetimeActual);
                            $objInfoDetalleElementoBateria->setUsrCreacion($strUserSession);
                            $objInfoDetalleElementoBateria->setIpCreacion($strIpUserSession);
                            $objInfoDetalleElementoBateria->setEstado(self::ESTADO_ACTIVO);
                            $emInfraestructura->persist($objInfoDetalleElementoBateria);
                            $emInfraestructura->flush();
                        }//( $strValue )
                    }//foreach($arrayInfoDetallesBateria as $strKey => $strValue)

                    $strObservacion = 'Se crea bateria';

                    $objInfoEmpresaElementoBateria = new InfoEmpresaElemento();
                    $objInfoEmpresaElementoBateria->setElementoId($objInfoElementoBateria);
                    $objInfoEmpresaElementoBateria->setEmpresaCod($intIdEmpresa);
                    $objInfoEmpresaElementoBateria->setObservacion($strObservacion);
                    $objInfoEmpresaElementoBateria->setFeCreacion($datetimeActual);
                    $objInfoEmpresaElementoBateria->setUsrCreacion($strUserSession);
                    $objInfoEmpresaElementoBateria->setIpCreacion($strIpUserSession);
                    $objInfoEmpresaElementoBateria->setEstado(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoEmpresaElementoBateria);
                    $emInfraestructura->flush();


                    $objInfoHistorialElementoBateria = new InfoHistorialElemento();
                    $objInfoHistorialElementoBateria->setElementoId($objInfoElementoBateria);
                    $objInfoHistorialElementoBateria->setObservacion($strObservacion);
                    $objInfoHistorialElementoBateria->setFeCreacion($datetimeActual);
                    $objInfoHistorialElementoBateria->setUsrCreacion($strUserSession);
                    $objInfoHistorialElementoBateria->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElementoBateria->setEstadoElemento(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoHistorialElementoBateria);
                    $emInfraestructura->flush();


                    $objRelacionElementoBateria = new InfoRelacionElemento();
                    $objRelacionElementoBateria->setElementoIdA($intIdElemento);
                    $objRelacionElementoBateria->setElementoIdB($intIdElementoBateria);
                    $objRelacionElementoBateria->setTipoRelacion("CONTIENE");
                    $objRelacionElementoBateria->setObservacion("UPS contiene BATERIA");
                    $objRelacionElementoBateria->setEstado(self::ESTADO_ACTIVO);
                    $objRelacionElementoBateria->setUsrCreacion($strUserSession);
                    $objRelacionElementoBateria->setFeCreacion($datetimeActual);
                    $objRelacionElementoBateria->setIpCreacion($strIpUserSession);
                    $emInfraestructura->persist($objRelacionElementoBateria);
                    $emInfraestructura->flush();
                    
                    
                    if( $objInfoEmpresaElementoUbicaNodo )
                    {
                        $objInfoEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                        $objInfoEmpresaElementoUbica->setEmpresaCod($intIdEmpresa);
                        $objInfoEmpresaElementoUbica->setElementoId($objInfoElementoBateria);
                        $objInfoEmpresaElementoUbica->setUbicacionId($objInfoEmpresaElementoUbicaNodo->getUbicacionId());
                        $objInfoEmpresaElementoUbica->setUsrCreacion($strUserSession);
                        $objInfoEmpresaElementoUbica->setFeCreacion($datetimeActual);
                        $objInfoEmpresaElementoUbica->setIpCreacion($strIpUserSession);
                        $emInfraestructura->persist($objInfoEmpresaElementoUbica);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        throw new \Exception("No se encontro el Ubicación del Nodo");
                    }//( $objInfoEmpresaElementoUbicaNodo )
                }//foreach($arrayBaterias as $objBateria)
            }//( $intTotal > 0 )
            /*
             * Fin Bloque que guarda las baterias y su relación con el UPS
             */
            

            $emInfraestructura->getConnection()->commit();
            $emInfraestructura->getConnection()->close();

            return $this->redirect($this->generateUrl('elementoups_show', array('id' => $intIdElemento)));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }//try
        
        
        $arrayModelosElementos = array();
        $arrayTmpParametros    = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array(self::TIPO_ELEMENTO) );
        $arrayTmpResultados    = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                   ->getModeloElementosByCriterios( $arrayTmpParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
        }

        return $this->render( 'tecnicoBundle:InfoElementoUps:new.html.twig', array(
                                                                                    'modelosElemento' => $arrayModelosElementos,
                                                                                    'strTipoElemento' => self::TIPO_ELEMENTO,
                                                                                    'entity'          => $objInfoElemento,
                                                                                    'form'            => $form->createView()
                                                                                  ) 
                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_326-6")
     * 
     * Documentación para el método 'showAction'.
     *
     * Muestra la información de un UPS existente.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-01-2016
     */
    public function showAction($id)
    {
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objInfoElemento )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );
        
        $arrayInfoDetalles = array( 'CLASE' => '', 'TIEMPO_ACCESO' => '', 'IAC' => '', 'GENERADOR'  => '', 'HABILITADO' => '' );
        
        if( $objDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $arrayInfoDetalles[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        $strNombreEmpresa = '';
        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                              ->findOneBy( array ('elementoId' => $objInfoElemento, 'estado' => self::ESTADO_ACTIVO) );

        if( $objInfoEmpresa )
        {
            $strCodEmpresa = $objInfoEmpresa->getEmpresaCod();
            
            $objEmpresa = null;
            if( $strCodEmpresa )
            {
                $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($strCodEmpresa);
            }
            
            if( $objEmpresa )
            {
                $strNombreEmpresa = $objEmpresa->getNombreEmpresa();
            }
        }
        
        
        /*
         * Bloque que obtiene el Nombre del Nodo
         */
        $strNombreNodo       = '';
        $objRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                 ->findOneBy( array ('elementoIdB' => $id, 'estado' => self::ESTADO_ACTIVO) );
        
        if( $objRelacionElemento )
        {
            $intIdNodo           = $objRelacionElemento->getElementoIdA();
            $objInfoElementoNodo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($intIdNodo);
            
            if( $objInfoElementoNodo )
            {
                $strNombreNodo = $objInfoElementoNodo->getNombreElemento();
            }
        }
        /*
         * Fin Bloque que obtiene el Nombre del Nodo
         */
        
        
        /*
         * Bloque que obtiene el Nombre del Switch y el Puerto
         */
        $strNombreSwitch = '';
        $strPuertoSwitch = '';
        $objAdmiTipoMedio   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'nombreTipoMedio' => 'Cobre') );
        $objInterfaceUps    = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->findOneBy( array('estado' => self::ESTADO_CONNECTED, 'elementoId' => $objInfoElemento) );
        $objInfoEnlace      = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                ->findOneBy( array( 'estado'                 => self::ESTADO_ACTIVO, 
                                                                    'interfaceElementoFinId' => $objInterfaceUps,
                                                                    'tipoMedioId'            => $objAdmiTipoMedio ) );
        
        if($objInfoEnlace)
        {
            $objInterfaceSwitch = $objInfoEnlace->getInterfaceElementoIniId();
            $strNombreSwitch    = $objInterfaceSwitch->getElementoId()->getNombreElemento();
            $strPuertoSwitch    = $objInterfaceSwitch->getNombreInterfaceElemento();
        }
        /*
         * Fin Bloque que obtiene el Nombre del Switch y el Puerto
         */
        
        
        /*
         * Bloque que obtiene la ip guardada con el UPS
         */
        $strIpUps = '';
        $objInfoIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                                       ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'elementoId' => $id) );
        
        if($objInfoIp)
        {
            $strIpUps = $objInfoIp->getIp();
        }
        /*
         * Fin Bloque que obtiene la ip guardada con el UPS
         */
        
        
        /*
         * Bloque que obtiene el snmp guardado con el UPS
         */
        $strSnmp             = '';
        $objInfoSnmpElemento = $emInfraestructura->getRepository('schemaBundle:InfoSnmpElemento')
                                                 ->findOneBy(array('elementoId' => $objInfoElemento));
        
        if($objInfoSnmpElemento)
        {
            $strSnmp = $objInfoSnmpElemento->getSnmpId()->getSnmpCommunity();
        }
        /*
         * Fin Bloque que obtiene el snmp guardado con el UPS
         */
        
        
        $objElementoBateria = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento('BATERIA');
        
        if( $objElementoBateria )
        {
            $intIdElementoBateria = $objElementoBateria->getId();
        }

        return $this->render('tecnicoBundle:InfoElementoUps:show.html.twig', array(
                                                                                      'elemento'  => $objInfoElemento,
                                                                                      'detalles'  => $arrayInfoDetalles,
                                                                                      'empresa'   => $strNombreEmpresa,
                                                                                      'nodo'      => $strNombreNodo,
                                                                                      'switch'    => $strNombreSwitch,
                                                                                      'puerto'    => $strPuertoSwitch,
                                                                                      'ip'        => $strIpUps,
                                                                                      'bateriaId' => $intIdElementoBateria,
                                                                                      'strSnmp'   => $strSnmp
                                                                                   )
                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_326-4")
     * 
     * Documentación para el método 'editAction'.
     *
     * Muestra la información de un UPS al cual se le va a actualizar la información.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-01-2016
     */
    public function editAction($id)
    {
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTipoElemento      = self::TIPO_ELEMENTO;
        $objInfoElemento      = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        $intIdMarca           = 0;
        $intIdModelo          = 0;
        $intIdElementoBateria = 0;
        
        if( !$objInfoElemento )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );
        
        $arrayInfoDetalles = array( 'CLASE' => '', 'TIEMPO_ACCESO' => '', 'IAC' => '', 'GENERADOR'  => '' );
        
        if( $objDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $arrayInfoDetalles[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        $arrayModelosElementos = array();
        
        $arrayTmpParametros = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array($strTipoElemento) );
        $arrayTmpResultados = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->getModeloElementosByCriterios( $arrayTmpParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
        }
        
        
        /*
         * Bloque que obtiene el Nombre del Nodo
         */
        $intIdNodo           = 0;
        $objRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                 ->findOneBy( array ('elementoIdB' => $id, 'estado' => self::ESTADO_ACTIVO) );
        
        if( $objRelacionElemento )
        {
            $intIdNodo = $objRelacionElemento->getElementoIdA();
        }
        /*
         * Fin Bloque que obtiene el Nombre del Nodo
         */
        
        
        /*
         * Bloque que obtiene el Nombre del Switch y el Puerto
         */
        $intIdSwitch       = 0;
        $intIdPuertoSwitch = 0;
        $objAdmiTipoMedio  = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                               ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'nombreTipoMedio' => 'Cobre') );
        $objInterfaceUps   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                               ->findOneBy( array('estado' => self::ESTADO_CONNECTED, 'elementoId' => $objInfoElemento) );
        $objInfoEnlace     = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                               ->findOneBy( array( 'estado'                 => self::ESTADO_ACTIVO, 
                                                                   'interfaceElementoFinId' => $objInterfaceUps,
                                                                   'tipoMedioId'            => $objAdmiTipoMedio ) );
        
        if($objInfoEnlace)
        {
            $objInterfaceSwitch = $objInfoEnlace->getInterfaceElementoIniId();
            $intIdSwitch        = $objInterfaceSwitch->getElementoId()->getId();
            $intIdPuertoSwitch  = $objInterfaceSwitch->getId();
        }
        /*
         * Fin Bloque que obtiene el Nombre del Switch y el Puerto
         */
        
        
        /*
         * Bloque que obtiene la ip guardada con el UPS
         */
        $strIpUps = '';
        $objInfoIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                                       ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'elementoId' => $id) );
        
        if($objInfoIp)
        {
            $strIpUps = $objInfoIp->getIp();
            
            $objInfoElemento->setIpElemento($strIpUps);
        }
        /*
         * Fin Bloque que obtiene la ip guardada con el UPS
         */
        
        
        /*
         * Bloque que obtiene el snmp guardado con el UPS
         */
        $intIdSnmp           = 0;
        $objInfoSnmpElemento = $emInfraestructura->getRepository('schemaBundle:InfoSnmpElemento')
                                                 ->findOneBy(array('elementoId' => $objInfoElemento));
        
        if($objInfoSnmpElemento)
        {
            $intIdSnmp = $objInfoSnmpElemento->getSnmpId()->getId();
        }
        /*
         * Fin Bloque que obtiene el snmp guardado con el UPS
         */
        
        
        $objModeloElemento = $objInfoElemento->getModeloElementoId();
        
        if( $objModeloElemento )
        {
            $intIdMarca  = $objModeloElemento->getMarcaElementoId()->getId();
            $intIdModelo = $objModeloElemento->getId();
        }
        
        
        $objElementoBateria = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento('BATERIA');
        
        if( $objElementoBateria )
        {
            $intIdElementoBateria = $objElementoBateria->getId();
        }
        
        
        $form = $this->createForm(new InfoElementoUpsType(), $objInfoElemento);
        
        return $this->render('tecnicoBundle:InfoElementoUps:edit.html.twig', array(
                                                                                      'entity'          => $objInfoElemento,
                                                                                      'detalles'        => $arrayInfoDetalles,
                                                                                      'modelosElemento' => $arrayModelosElementos,
                                                                                      'form'            => $form->createView(),
                                                                                      'nodo'            => $intIdNodo,
                                                                                      'switch'          => $intIdSwitch,
                                                                                      'puerto'          => $intIdPuertoSwitch,
                                                                                      'ip'              => $strIpUps,
                                                                                      'marca'           => $intIdMarca,
                                                                                      'modelo'          => $intIdModelo,
                                                                                      'bateriaId'       => $intIdElementoBateria,
                                                                                      'snmp'            => $intIdSnmp
                                                                                   )
                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_326-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información de un UPS.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-01-2016
     */
    public function updateAction($id)
    {
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdEmpresaSession   = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strUserSession        = $objSession->get('user');
        $strIpUserSession      = $objRequest->getClientIp();
        $datetimeActual        = new \DateTime('now');
        $intIdNodo             = $objRequest->request->get('intIdNodo') ? $objRequest->request->get('intIdNodo') : 0;
        $intIdClase            = $objRequest->request->get('intIdClase') ? $objRequest->request->get('intIdClase') : 0;
        $intIdPuerto           = $objRequest->request->get('intIdPuerto') ? $objRequest->request->get('intIdPuerto') : 0;
        $intIdSnmp            = $objRequest->request->get('intIdSnmp') ? $objRequest->request->get('intIdSnmp') : 0;
        $arrayPostUps          = $objRequest->get('infoElementoUps');
        $strNombreElemento     = $arrayPostUps['nombreElemento'] ? $arrayPostUps['nombreElemento'] : '';
        $strIp                 = $arrayPostUps['ipElemento'] ? $arrayPostUps['ipElemento'] : '';
        $intIdModeloElemento   = $objRequest->request->get('modeloElementoId') ? $objRequest->request->get('modeloElementoId') : 0;
        $strTAcceso            = $objRequest->request->get('tAcceso') ? $objRequest->request->get('tAcceso') : '';
        $strIac                = $objRequest->request->get('iac') ? $objRequest->request->get('iac') : '';
        $strNumSerie           = $objRequest->request->get('numSerie') ? $objRequest->request->get('numSerie') : '';
        $strGenerador          = $objRequest->request->get('generador') ? $objRequest->request->get('generador') : '';
        $strObservacion        = $arrayPostUps['observacion'] ? $arrayPostUps['observacion'] : '';
        $objInfoElemento       = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        $jsonBaterias          = $objRequest->request->get('baterias') ? json_decode($objRequest->request->get('baterias')) : null;
        $arrayOpcionesEditadas = array();

        if( !$objInfoElemento )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $emInfraestructura->getConnection()->beginTransaction();	
        
        try
        {
            /*
             * Bloque que actualiza el InfoElemento
             */
            $objModeloElemento               = null;
            $objInfoEmpresaElementoUbicaNodo = null;
            
            if( $intIdModeloElemento )
            {
                $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($intIdModeloElemento);
            }
            
            $intIdEmpresa = $intIdEmpresaSession;
            
            $strObservacionHistorial = 'Se actualiza información del UPS';
                    
            $objInfoElemento->setModeloElementoId($objModeloElemento);
            $objInfoElemento->setSerieFisica(trim($strNumSerie));
            $objInfoElemento->setNombreElemento(trim($strNombreElemento));
            $objInfoElemento->setObservacion(trim($strObservacion));
            $emInfraestructura->persist($objInfoElemento);
            $emInfraestructura->flush();
            /*
             * Fin del Bloque que actualiza el InfoElemento
             */
            
            
            /*
             * Bloque que actualiza los detalles del InfoElemento
             */
            $strClase        = 'NO TIENE CLASE';
            $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'id' => $intIdClase) );
        
            if( $objParametroDet )
            {
                $strClase = $objParametroDet->getDescripcion();
            }
            
            $intIdElemento     = $objInfoElemento->getId();
            $arrayInfoDetalles = array( 'CLASE'         => $strClase, 
                                        'TIEMPO_ACCESO' => $strTAcceso, 
                                        'IAC'           => $strIac, 
                                        'GENERADOR'     => $strGenerador );
            
            foreach($arrayInfoDetalles as $strKey => $strValue)
            {
                if( $strValue )
                {
                    $objInfoDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy( array( 'elementoId'    => $id, 
                                                                                    'estado'        => self::ESTADO_ACTIVO, 
                                                                                    'detalleNombre' => $strKey ) );
                    
                    if( !$objInfoDetalleElemento )
                    {
                        $objInfoDetalleElemento = new InfoDetalleElemento();
                        $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                        $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                        $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                        $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                    }
                    elseif( trim($objInfoDetalleElemento->getDetalleValor()) != trim($strValue) )
                    {
                        $objInfoDetalleElemento->setEstado(self::ESTADO_ELIMINADO);
                        $emInfraestructura->persist($objInfoDetalleElemento);
                        $emInfraestructura->flush();
                        
                        $objInfoDetalleElemento = new InfoDetalleElemento();
                        $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                        $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                        $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                        $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                    }
                    
                    $objInfoDetalleElemento->setElementoId($intIdElemento);
                    $objInfoDetalleElemento->setDetalleNombre($strKey);
                    $objInfoDetalleElemento->setDetalleValor(trim($strValue));
                    $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                    
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                }//( $strValue )
            }//foreach($arrayInfoDetalles as $strKey => $strValue)
            /*
             * Fin del Bloque que actualiza los detalles del InfoElemento
             */
            
            
            /*
             * Bloque que actualiza la relación del InfoElemento con la empresa en sessión del usuario
             */
            $objInfoEmpresaElemento = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                        ->findOneBy( array( 'elementoId' => $objInfoElemento, 
                                                                            'estado'     => self::ESTADO_ACTIVO ) );
                    
            if( !$objInfoEmpresaElemento )
            {
                $objInfoEmpresaElemento = new InfoEmpresaElemento();
                $objInfoEmpresaElemento->setFeCreacion($datetimeActual);
                $objInfoEmpresaElemento->setUsrCreacion($strUserSession);
                $objInfoEmpresaElemento->setIpCreacion($strIpUserSession);
                $objInfoEmpresaElemento->setEstado(self::ESTADO_ACTIVO);
            }
            
            $objInfoEmpresaElemento->setElementoId($objInfoElemento);
            $objInfoEmpresaElemento->setEmpresaCod($intIdEmpresa);
            $objInfoEmpresaElemento->setObservacion($strObservacion);
            $emInfraestructura->persist($objInfoEmpresaElemento);
            $emInfraestructura->flush();
            /*
             * Fin del Bloque que actualiza la relación del InfoElemento con la empresa en sessión del usuario
             */
            
            
            /*
             * Bloque que guarda el historial del InfoElemento
             */
            $objInfoHistorialElemento = new InfoHistorialElemento();
            $objInfoHistorialElemento->setElementoId($objInfoElemento);
            $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
            $objInfoHistorialElemento->setFeCreacion($datetimeActual);
            $objInfoHistorialElemento->setUsrCreacion($strUserSession);
            $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
            $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
            $emInfraestructura->persist($objInfoHistorialElemento);
            $emInfraestructura->flush();
            /*
             * Fin del Bloque que guarda el historial del InfoElemento
             */
            
            
            /*
             * Bloque que actualiza la InfoRelacionElemento entre el Nodo y el Elemento UPS
             */
            $objRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                     ->findOneBy( array( 'elementoIdB' => $intIdElemento, 
                                                                         'estado'      => self::ESTADO_ACTIVO ) );
                    
            if( !$objRelacionElemento )
            {
                $objRelacionElemento = new InfoRelacionElemento();
                $objRelacionElemento->setEstado(self::ESTADO_ACTIVO);
                $objRelacionElemento->setUsrCreacion($strUserSession);
                $objRelacionElemento->setFeCreacion($datetimeActual);
                $objRelacionElemento->setIpCreacion($strIpUserSession);
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion("Nodo contiene UPS");
            }
            
            $objRelacionElemento->setElementoIdA($intIdNodo);
            $objRelacionElemento->setElementoIdB($intIdElemento);
            $emInfraestructura->persist($objRelacionElemento);
            $emInfraestructura->flush();
            /*
             * Fin Bloque que actualiza la InfoRelacionElemento entre el Nodo y el Elemento UPS
             */
            
            
            /*
             * Bloque que actualiza la InfoEnlace entre el Switch y el UPS
             */
            if( $intIdPuerto )
            {
                $objInterfaceUps = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findOneBy( array( 'elementoId'              => $objInfoElemento, 
                                                                         'estado'                  => self::ESTADO_CONNECTED,
                                                                         'nombreInterfaceElemento' => 'Eth0' ) );

                if( !$objInterfaceUps )
                {
                    $strNombreInterfazElemento = 'Eth0'; 

                    $objInterfaceUps = new InfoInterfaceElemento();
                    $objInterfaceUps->setElementoId($objInfoElemento);
                    $objInterfaceUps->setEstado(self::ESTADO_NOTCONNECT);
                    $objInterfaceUps->setUsrCreacion($strUserSession);
                    $objInterfaceUps->setFeCreacion($datetimeActual);
                    $objInterfaceUps->setIpCreacion($strIpUserSession);
                    $objInterfaceUps->setNombreInterfaceElemento($strNombreInterfazElemento);
                    $objInterfaceUps->setDescripcionInterfaceElemento('INTERFACE UPS: '.$strNombreInterfazElemento);
                    $emInfraestructura->persist($objInterfaceUps);
                    $emInfraestructura->flush();
                }//( !$objInterfaceUps )


                $objAdmiTipoMedio   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                        ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'nombreTipoMedio' => 'Cobre') );

                $objInterfaceSwitchNew = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                           ->findOneById( $intIdPuerto );

                $objInfoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                   ->findOneBy( array( 'interfaceElementoFinId' => $objInterfaceUps,
                                                                       'estado'                 => self::ESTADO_ACTIVO ) );

                if( !$objInfoEnlace )
                {
                    $objInfoEnlace = new InfoEnlace();
                    $objInfoEnlace->setTipoEnlace('PRINCIPAL');
                    $objInfoEnlace->setEstado(self::ESTADO_ACTIVO);
                    $objInfoEnlace->setUsrCreacion($strUserSession);
                    $objInfoEnlace->setFeCreacion($datetimeActual);
                    $objInfoEnlace->setIpCreacion($strIpUserSession);
                    $objInfoEnlace->setTipoMedioId($objAdmiTipoMedio);
                }
                else
                {
                    $objInterfaceSwitchOld = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                               ->findOneBy( array( 'estado' => self::ESTADO_CONNECTED,
                                                                                   'id'     => $objInfoEnlace->getInterfaceElementoIniId() ) );

                    $objInterfaceSwitchOld->setEstado(self::ESTADO_NOTCONNECT);
                    $emInfraestructura->persist($objInterfaceSwitchOld);
                    $emInfraestructura->flush();
                }//( !$objInfoEnlace )



                $objInfoEnlace->setInterfaceElementoFinId($objInterfaceUps);
                $objInfoEnlace->setInterfaceElementoIniId($objInterfaceSwitchNew);

                $emInfraestructura->persist($objInfoEnlace);
                $emInfraestructura->flush();

                $objInterfaceUps->setEstado(self::ESTADO_CONNECTED);
                $emInfraestructura->persist($objInterfaceUps);
                $emInfraestructura->flush();

                $objInterfaceSwitchNew->setEstado(self::ESTADO_CONNECTED);
                $emInfraestructura->persist($objInterfaceSwitchNew);
                $emInfraestructura->flush();
            }//( $intIdPuerto )
            /*
             * Fin Bloque que actualiza la InfoEnlace entre el Switch y el UPS
             */
            
            
            /*
             * Bloque que actualiza la InfoIP del Elemento UPS
             */
            $objInfoIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                                           ->findOneBy( array( 'elementoId' => $intIdElemento, 
                                                               'estado'     => self::ESTADO_ACTIVO ) );

            if( !$objInfoIp )
            {
                $objInfoIp = new InfoIp();
                $objInfoIp->setElementoId($intIdElemento);
                $objInfoIp->setVersionIp('IPV4');
                $objInfoIp->setTipoIp('FIJA');
                $objInfoIp->setEstado(self::ESTADO_ACTIVO);
                $objInfoIp->setUsrCreacion($strUserSession);
                $objInfoIp->setFeCreacion($datetimeActual);
                $objInfoIp->setIpCreacion($strIpUserSession);
            }//( !$objInfoIp )
            
            $objInfoIp->setIp($strIp);
            
            $emInfraestructura->persist($objInfoIp);
            $emInfraestructura->flush();
            /*
             * Fin Bloque que actualiza la InfoIP del Elemento UPS
             */
            
            
            /*
             * Bloque que actualiza la InfoSnmpElemento del Elemento UPS
             */
            $objAdmiSnmp = $emInfraestructura->getRepository('schemaBundle:AdmiSnmp')->findOneBy( array('estado' => self::ESTADO_ACTIVO, 
                                                                                                            'id'     => $intIdSnmp) );
            
            $objInfoSnmpElemento = $emInfraestructura->getRepository('schemaBundle:InfoSnmpElemento')
                                                     ->findOneBy(array('elementoId' => $objInfoElemento));

            if(!$objInfoSnmpElemento)
            {
                $objInfoSnmpElemento = new InfoSnmpElemento();
                $objInfoSnmpElemento->setElementoId($objInfoElemento);
                $objInfoSnmpElemento->setUsrCreacion($strUserSession);
                $objInfoSnmpElemento->setFeCreacion($datetimeActual);
                $objInfoSnmpElemento->setIpCreacion($strIpUserSession);
            }
            
            $objInfoSnmpElemento->setSnmpId($objAdmiSnmp);
            $emInfraestructura->persist($objInfoSnmpElemento);
            $emInfraestructura->flush();
            /*
             * Fin Bloque que actualiza la InfoSnmpElemento del Elemento UPS
             */
            
            
            /*
             * Bloque que actualiza la InfoElementoUbicacion del Elemento UPS
             */
            if( $intIdNodo )
            {
                $objInfoElementoNodo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                         ->findOneBy( array( 'estado' => self::ESTADO_ACTIVO,
                                                                             'id'     => $intIdNodo ) );

                if( $objInfoElementoNodo )
                {
                    $objInfoEmpresaElementoUbicaNodo = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                         ->findOneBy( array( 'elementoId' => $objInfoElementoNodo,
                                                                                             'empresaCod' => $intIdEmpresa ) );

                    if( $objInfoEmpresaElementoUbicaNodo )
                    {
                        $objInfoEmpresaElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                         ->findOneBy( array( 'elementoId' => $objInfoElemento,
                                                                                             'empresaCod' => $intIdEmpresa ) );
                        
                        if( !$objInfoEmpresaElementoUbica )
                        {
                            $objInfoEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                            
                            $objInfoEmpresaElementoUbica->setUsrCreacion($strUserSession);
                            $objInfoEmpresaElementoUbica->setFeCreacion($datetimeActual);
                            $objInfoEmpresaElementoUbica->setIpCreacion($strIpUserSession);
                            $objInfoEmpresaElementoUbica->setEmpresaCod($intIdEmpresa);
                            $objInfoEmpresaElementoUbica->setElementoId($objInfoElemento);
                        }
                        
                        $objInfoEmpresaElementoUbica->setUbicacionId($objInfoEmpresaElementoUbicaNodo->getUbicacionId());
                        $emInfraestructura->persist($objInfoEmpresaElementoUbica);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        throw new \Exception("No se encontro el Ubicación del Nodo");
                    }//( $objInfoEmpresaElementoUbicaNodo )
                }
                else
                {
                    throw new \Exception("No se encontro el Nodo en estado Activo");
                }//( $objInfoElementoNodo )
            }
            else
            {
                throw new \Exception("No se encontro el Nodo en estado Activo");
            }//( $intIdNodo )
            /*
             * Fin Bloque que actualiza la InfoElementoUbicacion del Elemento UPS
             */
            
            
            /*
             * Bloque que actualiza las baterias y su relación con el UPS
             */
            $objAdmiTipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento('BATERIA');
            $arrayBaterias       = $jsonBaterias ? $jsonBaterias->baterias : array();
            $intTotal            = $jsonBaterias ? $jsonBaterias->total : 0;
            $i                   = 0;

            if( $intTotal > 0 )
            {
                foreach($arrayBaterias as $objBateria)
                {
                    $i++;

                    $intIdElementoBateria       = $objBateria->intIdElemento;
                    $strModeloElementoBateria   = $objBateria->strModeloElemento;
                    $strSerieFisica             = $objBateria->strSerieFisica;
                    $strTipoBateria             = $objBateria->strTIPO_BATERIA;
                    $strAmperaje                = $objBateria->strAMPERAJE;
                    $strFechaInstalacionBateria = $objBateria->strFECHA_INSTALACION;

                    if( $strFechaInstalacionBateria )
                    {
                        $datetimeFechaInstalacion   = new \DateTime($strFechaInstalacionBateria);
                        $strFechaInstalacionBateria = $datetimeFechaInstalacion->format('d-m-Y');
                    }

                    $strNombreElementoBateria   = "Bateria ".$strNombreElemento." ".$i;
                    $objModeloElementoBateria   = null;

                    if( $strModeloElementoBateria )
                    {
                        $objModeloElementoBateria = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                                      ->findOneBy( array( 'estado'               => self::ESTADO_ACTIVO,
                                                                                          'nombreModeloElemento' => $strModeloElementoBateria,
                                                                                          'tipoElementoId'       => $objAdmiTipoElemento ) );
                    }
                    
                    $objInfoElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($intIdElementoBateria);

                    if( !$objInfoElementoBateria )
                    {
                        $objInfoElementoBateria = new InfoElemento();
                        $objInfoElementoBateria->setEstado(self::ESTADO_ACTIVO);
                        $objInfoElementoBateria->setFeCreacion($datetimeActual);
                        $objInfoElementoBateria->setUsrCreacion($strUserSession);
                        $objInfoElementoBateria->setIpCreacion($strIpUserSession);
                    }
                    
                    $objInfoElementoBateria->setModeloElementoId($objModeloElementoBateria);
                    $objInfoElementoBateria->setSerieFisica($strSerieFisica);
                    $objInfoElementoBateria->setNombreElemento($strNombreElementoBateria);
                    $objInfoElementoBateria->setDescripcionElemento($strNombreElementoBateria);
                    $emInfraestructura->persist($objInfoElementoBateria);
                    $emInfraestructura->flush();


                    $intIdElementoBateria     = $objInfoElementoBateria->getId();
                    $arrayOpcionesEditadas[]  = $intIdElementoBateria;
                    $arrayInfoDetallesBateria = array(  'AMPERAJE'          => $strAmperaje, 
                                                        'TIPO_BATERIA'      => $strTipoBateria, 
                                                        'FECHA_INSTALACION' => $strFechaInstalacionBateria );

                    foreach($arrayInfoDetallesBateria as $strKey => $strValue)
                    {
                        if( $strValue )
                        {
                            $objInfoDetalleElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                               ->findOneBy( array( 'elementoId'    => $intIdElementoBateria, 
                                                                                                   'estado'        => self::ESTADO_ACTIVO, 
                                                                                                   'detalleNombre' => $strKey ) );
                    
                            if( !$objInfoDetalleElementoBateria )
                            {
                                $objInfoDetalleElementoBateria = new InfoDetalleElemento();
                                $objInfoDetalleElementoBateria->setFeCreacion($datetimeActual);
                                $objInfoDetalleElementoBateria->setUsrCreacion($strUserSession);
                                $objInfoDetalleElementoBateria->setIpCreacion($strIpUserSession);
                                $objInfoDetalleElementoBateria->setEstado(self::ESTADO_ACTIVO);
                            }
                            elseif( trim($objInfoDetalleElementoBateria->getDetalleValor()) != trim($strValue) )
                            {
                                $objInfoDetalleElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objInfoDetalleElementoBateria);
                                $emInfraestructura->flush();

                                $objInfoDetalleElementoBateria = new InfoDetalleElemento();
                                $objInfoDetalleElementoBateria->setFeCreacion($datetimeActual);
                                $objInfoDetalleElementoBateria->setUsrCreacion($strUserSession);
                                $objInfoDetalleElementoBateria->setIpCreacion($strIpUserSession);
                                $objInfoDetalleElementoBateria->setEstado(self::ESTADO_ACTIVO);
                            }
                            
                            $objInfoDetalleElementoBateria->setElementoId($intIdElementoBateria);
                            $objInfoDetalleElementoBateria->setDetalleNombre($strKey);
                            $objInfoDetalleElementoBateria->setDetalleValor(trim($strValue));
                            $objInfoDetalleElementoBateria->setDetalleDescripcion(trim($strKey));
                            $emInfraestructura->persist($objInfoDetalleElementoBateria);
                            $emInfraestructura->flush();
                        }//( $strValue )
                    }//foreach($arrayInfoDetallesBateria as $strKey => $strValue)

                    $strObservacion = 'Se edita la bateria';
                    
                    $objInfoEmpresaElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                                       ->findOneBy( array( 'elementoId' => $objInfoElementoBateria,
                                                                                           'estado'     => self::ESTADO_ACTIVO ));

                    if( !$objInfoEmpresaElementoBateria )
                    {
                        $objInfoEmpresaElementoBateria = new InfoEmpresaElemento();
                        $objInfoEmpresaElementoBateria->setFeCreacion($datetimeActual);
                        $objInfoEmpresaElementoBateria->setUsrCreacion($strUserSession);
                        $objInfoEmpresaElementoBateria->setIpCreacion($strIpUserSession);
                        $objInfoEmpresaElementoBateria->setEstado(self::ESTADO_ACTIVO);
                        $objInfoEmpresaElementoBateria->setObservacion($strObservacion);
                        $objInfoEmpresaElementoBateria->setElementoId($objInfoElementoBateria);
                    }

                    $objInfoEmpresaElementoBateria->setEmpresaCod($intIdEmpresa);
                    $emInfraestructura->persist($objInfoEmpresaElementoBateria);
                    $emInfraestructura->flush();


                    $objInfoHistorialElementoBateria = new InfoHistorialElemento();
                    $objInfoHistorialElementoBateria->setElementoId($objInfoElementoBateria);
                    $objInfoHistorialElementoBateria->setObservacion($strObservacion);
                    $objInfoHistorialElementoBateria->setFeCreacion($datetimeActual);
                    $objInfoHistorialElementoBateria->setUsrCreacion($strUserSession);
                    $objInfoHistorialElementoBateria->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElementoBateria->setEstadoElemento(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoHistorialElementoBateria);
                    $emInfraestructura->flush();


                    
                    $objRelacionElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                    ->findOneBy( array( 'elementoIdA' => $intIdElemento,
                                                                                        'elementoIdB' => $intIdElementoBateria,
                                                                                        'estado'      => self::ESTADO_ACTIVO ));

                    if( !$objRelacionElementoBateria )
                    {
                        $objRelacionElementoBateria = new InfoRelacionElemento();
                        $objRelacionElementoBateria->setEstado(self::ESTADO_ACTIVO);
                        $objRelacionElementoBateria->setUsrCreacion($strUserSession);
                        $objRelacionElementoBateria->setFeCreacion($datetimeActual);
                        $objRelacionElementoBateria->setIpCreacion($strIpUserSession);
                        $objRelacionElementoBateria->setElementoIdA($intIdElemento);
                        $objRelacionElementoBateria->setElementoIdB($intIdElementoBateria);
                        $objRelacionElementoBateria->setTipoRelacion("CONTIENE");
                        $objRelacionElementoBateria->setObservacion("UPS contiene BATERIA");
                        $emInfraestructura->persist($objRelacionElementoBateria);
                        $emInfraestructura->flush();
                    }//( !$objRelacionElementoBateria )
                    
                    
                    /*
                     * Bloque que actualiza las baterias y su ubicacion con el Nodo
                     */
                    if( $objInfoEmpresaElementoUbicaNodo )
                    {
                        $objInfoEmpresaElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                         ->findOneBy( array( 'elementoId' => $objInfoElementoBateria,
                                                                                             'empresaCod' => $intIdEmpresa ) );

                        if( !$objInfoEmpresaElementoUbica )
                        {
                            $objInfoEmpresaElementoUbica = new InfoEmpresaElementoUbica();

                            $objInfoEmpresaElementoUbica->setUsrCreacion($strUserSession);
                            $objInfoEmpresaElementoUbica->setFeCreacion($datetimeActual);
                            $objInfoEmpresaElementoUbica->setIpCreacion($strIpUserSession);
                            $objInfoEmpresaElementoUbica->setEmpresaCod($intIdEmpresa);
                            $objInfoEmpresaElementoUbica->setElementoId($objInfoElementoBateria);
                        }

                        $objInfoEmpresaElementoUbica->setUbicacionId($objInfoEmpresaElementoUbicaNodo->getUbicacionId());
                        $emInfraestructura->persist($objInfoEmpresaElementoUbica);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        throw new \Exception("No se encontro el Ubicación del Nodo");
                    }//( $objInfoEmpresaElementoUbicaNodo )
                    /*
                     * Fin Bloque que actualiza las baterias y su ubicacion con el Nodo
                     */
                }//foreach($arrayBaterias as $objBateria)
                
                
                /*
                 * Bloque cambia de estado a eliminado las opciones que el usuario eliminó
                 */
                $arrayRelacionElementoBaterias = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                   ->findBy( array( 'elementoIdA' => $intIdElemento,
                                                                                    'estado'      => self::ESTADO_ACTIVO ));
                
                if( $arrayRelacionElementoBaterias )
                {
                    foreach( $arrayRelacionElementoBaterias as $objTmpRelacionElementoBateria )
                    {
                        $intTmpIdElementoBateria = $objTmpRelacionElementoBateria->getElementoIdB();
                        
                        if( !in_array($intTmpIdElementoBateria, $arrayOpcionesEditadas) )
                        {
                            $objTmpRelacionElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                            $emInfraestructura->persist($objTmpRelacionElementoBateria);
                            $emInfraestructura->flush();
                            
                            $objTmpInfoElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                           ->findOneById($intTmpIdElementoBateria);
                            
                            if( $objTmpInfoElementoBateria )
                            {
                                $objTmpInfoElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objTmpInfoElementoBateria);
                                $emInfraestructura->flush();
                            }
                            
                            $arrayInfoDetalleElementoBaterias = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                  ->findBy( array( 'elementoId'    => $intTmpIdElementoBateria, 
                                                                                                   'estado'        => self::ESTADO_ACTIVO ) );
                            
                            foreach( $arrayInfoDetalleElementoBaterias as $objTmpInfoDetalleElemento )
                            {
                                $objTmpInfoDetalleElemento->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objTmpInfoDetalleElemento);
                                $emInfraestructura->flush();
                            }//foreach( $arrayInfoDetalleElementoBaterias as $objTmpInfoDetalleElemento )
                            
                            
                            $objTmpInfoEmpresaElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                                                  ->findOneBy( array( 'elementoId' => $objTmpInfoElementoBateria,
                                                                                                      'estado'     => self::ESTADO_ACTIVO ));

                            if( $objTmpInfoEmpresaElementoBateria )
                            {
                                $objTmpInfoEmpresaElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objTmpInfoEmpresaElementoBateria);
                                $emInfraestructura->flush();
                            }
                            
                            $objRelacionElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                            ->findOneBy( array( 'elementoIdA' => $intIdElemento,
                                                                                                'elementoIdB' => $intTmpIdElementoBateria,
                                                                                                'estado'      => self::ESTADO_ACTIVO ));

                            if( $objRelacionElementoBateria )
                            {
                                $objRelacionElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objRelacionElementoBateria);
                                $emInfraestructura->flush();
                            }//( !$objRelacionElementoBateria )

                            
                            $objTmpInfoHistorialElementoBateria = new InfoHistorialElemento();
                            $objTmpInfoHistorialElementoBateria->setElementoId($objTmpInfoElementoBateria);
                            $objTmpInfoHistorialElementoBateria->setObservacion('Se elimina la Bateria');
                            $objTmpInfoHistorialElementoBateria->setFeCreacion($datetimeActual);
                            $objTmpInfoHistorialElementoBateria->setUsrCreacion($strUserSession);
                            $objTmpInfoHistorialElementoBateria->setIpCreacion($strIpUserSession);
                            $objTmpInfoHistorialElementoBateria->setEstadoElemento(self::ESTADO_ELIMINADO);
                            $emInfraestructura->persist($objTmpInfoHistorialElementoBateria);
                            $emInfraestructura->flush();
                        }//( !in_array($objTmpRelacionElementoBateria->getElementoIdB(), $arrayOpcionesEditadas) )
                    }//( $arrayRelacionElementoBaterias as $objTmpRelacionElementoBateria )
                }//( $arrayRelacionElementoBaterias )
                /*
                 * Fin Bloque cambia de estado a eliminado las opciones que el usuario eliminó
                 */
            }//( $intTotal > 0 )
            /*
             * Fin Bloque que guarda las baterias y su relación con el UPS
             */
            
            
            $emInfraestructura->getConnection()->commit();
            $emInfraestructura->getConnection()->close();

            return $this->redirect($this->generateUrl('elementoups_show', array('id' => $intIdElemento)));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }//try
        

        return $this->redirect($this->generateUrl('elementoups_edit', array('id' => $intIdElemento)));
    }
    
    
    /**
     * @Secure(roles="ROLE_326-8")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     *
     * Elimina la información de un UPS.
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-01-2016
     */
    public function deleteAjaxAction()
    {
        $response            = new Response();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdEmpresaSession = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strElementos        = $objRequest->request->get('ups') ? $objRequest->request->get('ups') : '';
        $boolError           = false;
        $strMensaje          = 'No se encontró UPS en estado activo';
        $strUserSession      = $objSession->get('user');
        $strIpUserSession    = $objRequest->getClientIp();
        $datetimeActual      = new \DateTime('now');
        
        $arrayElementos = array();
        if( $strElementos )
        {
            $arrayElementos = explode('|', $strElementos);
        }
            
        
        $emInfraestructura->getConnection()->beginTransaction();	
        
        try
        {
            foreach( $arrayElementos as $intIdElemento )
            {
                $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($intIdElemento);

                if( !$objInfoElemento )
                {
                    $boolError  = true;
                    $strMensaje = 'No se encontró ups en estado activo';
                }

                if( !$boolError )
                {
                    $intIdElemento = $objInfoElemento->getId();
                    
                    /*
                     * Bloque que elimina el InfoElemento
                     */
                    $strObservacionHistorial = 'Se elimina la información del UPS';

                    $objInfoElemento->setEstado(self::ESTADO_ELIMINADO);
                    $emInfraestructura->persist($objInfoElemento);
                    $emInfraestructura->flush();
                    /*
                     * Fin del Bloque que elimina el InfoElemento
                     */


                    /*
                     * Bloque que elimina los detalles del InfoElemento
                     */
                    $arrayInfoDetallesElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                   ->findBy( array( 'elementoId' => $intIdElemento, 
                                                                                    'estado'     => self::ESTADO_ACTIVO ) );

                    if( $arrayInfoDetallesElemento )
                    {
                        foreach($arrayInfoDetallesElemento as $objInfoDetalleElemento)
                        {
                            $objInfoDetalleElemento->setEstado(self::ESTADO_ELIMINADO);

                            $emInfraestructura->persist($objInfoDetalleElemento);
                            $emInfraestructura->flush();
                        }//foreach($arrayInfoDetallesElemento as $objInfoDetalleElemento)
                    }//( $arrayInfoDetallesElemento )
                    /*
                     * Fin del Bloque que elimina los detalles del InfoElemento
                     */


                    /*
                     * Bloque que elimina la relación del InfoElemento con la empresa en sessión del usuario
                     */
                    $objInfoEmpresaElemento = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                                ->findOneBy( array( 'elementoId' => $objInfoElemento, 
                                                                                    'estado'     => self::ESTADO_ACTIVO ) );

                    if( $objInfoEmpresaElemento )
                    {
                        $objInfoEmpresaElemento->setEstado(self::ESTADO_ELIMINADO);
                        
                        $emInfraestructura->persist($objInfoEmpresaElemento);
                        $emInfraestructura->flush();
                    }
                    /*
                     * Fin del Bloque que elimina la relación del InfoElemento con la empresa en sessión del usuario
                     */


                    /*
                     * Bloque que guarda el historial del InfoElemento
                     */
                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objInfoElemento);
                    $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
                    $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                    $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                    $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ELIMINADO);
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush();
                    /*
                     * Fin del Bloque que guarda el historial del InfoElemento
                     */


                    /*
                     * Bloque que elimina la InfoRelacionElemento entre el Nodo y el Elemento UPS
                     */
                    $objRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                             ->findOneBy( array( 'elementoIdB' => $intIdElemento, 
                                                                                 'estado'      => self::ESTADO_ACTIVO ) );

                    if( $objRelacionElemento )
                    {
                        $objRelacionElemento->setEstado(self::ESTADO_ELIMINADO);
                        $emInfraestructura->persist($objRelacionElemento);
                        $emInfraestructura->flush();
                    }
                    /*
                     * Fin Bloque que elimina la InfoRelacionElemento entre el Nodo y el Elemento UPS
                     */


                    /*
                     * Bloque que elimina la InfoEnlace entre el Switch y el UPS
                     */
                    $objInterfaceUpsInicial = null;
                    $arrayInterfacesUps     = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->findBy( array( 'elementoId' => $objInfoElemento ) );

                    if( $arrayInterfacesUps )
                    {
                        foreach( $arrayInterfacesUps as $objInterfaceUps )
                        {
                            $objInterfaceUps->setEstado(self::ESTADO_ELIMINADO);
                            $emInfraestructura->persist($objInterfaceUps);
                            $emInfraestructura->flush();
                            
                            $arrayInfoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                 ->findOneBy( array( 'interfaceElementoFinId' => $objInterfaceUps,
                                                                                     'estado'                 => self::ESTADO_ACTIVO ) );
                            
                            if( $arrayInfoEnlace )
                            {
                                foreach( $arrayInfoEnlace as $objInfoEnlace )
                                {
                                    if( $objInfoEnlace )
                                    {
                                        $objInfoEnlace->setEstado(self::ESTADO_ELIMINADO);

                                        $emInfraestructura->persist($objInfoEnlace);
                                        $emInfraestructura->flush();
                                        
                                        $arrayTmpParametros = array( 'estado' => self::ESTADO_CONNECTED,
                                                                     'id'     => $objInfoEnlace->getInterfaceElementoIniId() );
                                        
                                        $objInterfaceSwitch = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                ->findOneBy( $arrayTmpParametros );

                                        $objInterfaceSwitch->setEstado(self::ESTADO_NOTCONNECT);
                                        $emInfraestructura->persist($objInterfaceSwitch);
                                        $emInfraestructura->flush();
                                    }//( $objInfoEnlace )
                                }//foreach( $arrayInfoEnlace as $objInfoEnlace )
                            }//( $arrayInfoEnlace )
                        }//foreach( $arrayInterfacesUps as $objInterfaceUps )
                    }//( $arrayInterfacesUps ) 
                    /*
                     * Fin Bloque que elimina la InfoEnlace entre el Switch y el UPS
                     */
                    
                    
                    /*
                     * Bloque que elimina la InfoEmpresaElementoUbica del UPS
                     */
                    $objInfoEmpresaElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                     ->findOneBy( array( 'elementoId' => $objInfoElemento,
                                                                                         'empresaCod' => $intIdEmpresaSession ) );

                    if( $objInfoEmpresaElementoUbica )
                    {
                        $emInfraestructura->remove($objInfoEmpresaElementoUbica);
                        $emInfraestructura->flush();
                    }
                    /*
                     * Fin Bloque que elimina la InfoEmpresaElementoUbica del UPS
                     */


                    /*
                     * Bloque que elimina la InfoIP del Elemento UPS
                     */
                    $objInfoIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                   ->findOneBy( array( 'elementoId' => $intIdElemento, 
                                                                       'estado'     => self::ESTADO_ACTIVO ) );

                    if( $objInfoIp )
                    {
                        $objInfoIp->setEstado(self::ESTADO_ELIMINADO);
                        
                        $emInfraestructura->persist($objInfoIp);
                        $emInfraestructura->flush();
                    }//( $objInfoIp )
                    /*
                     * Fin Bloque que elimina la InfoIP del Elemento UPS
                     */
                    
                    
                    /*
                     * Bloque que elimina la InfoSnmpElemento del Elemento UPS
                     */
                    $objInfoSnmpElemento = $emInfraestructura->getRepository('schemaBundle:InfoSnmpElemento')
                                                             ->findOneBy(array('elementoId' => $objInfoElemento));

                    if($objInfoSnmpElemento)
                    {
                        $emInfraestructura->remove($objInfoSnmpElemento);
                        $emInfraestructura->flush();
                    }
                    /*
                     * Fin Bloque que elimina la InfoSnmpElemento del Elemento UPS
                     */
                    
                    
                    /*
                     * Bloque cambia de estado a eliminado las baterias asociadas al UPS que el usuario eliminó
                     */
                    $arrayRelacionElementoBaterias = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                       ->findBy( array( 'elementoIdA' => $intIdElemento,
                                                                                        'estado'      => self::ESTADO_ACTIVO ));

                    if( $arrayRelacionElementoBaterias )
                    {
                        foreach( $arrayRelacionElementoBaterias as $objTmpRelacionElementoBateria )
                        {
                            $intTmpIdElementoBateria = $objTmpRelacionElementoBateria->getElementoIdB();

                            $objTmpRelacionElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                            $emInfraestructura->persist($objTmpRelacionElementoBateria);
                            $emInfraestructura->flush();

                            $objTmpInfoElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                           ->findOneById($intTmpIdElementoBateria);

                            if( $objTmpInfoElementoBateria )
                            {
                                $objTmpInfoElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objTmpInfoElementoBateria);
                                $emInfraestructura->flush();
                            }

                            $arrayInfoDetalleElementoBaterias = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                  ->findBy( array( 'elementoId'    => $intTmpIdElementoBateria, 
                                                                                                   'estado'        => self::ESTADO_ACTIVO ) );

                            foreach( $arrayInfoDetalleElementoBaterias as $objTmpInfoDetalleElemento )
                            {
                                $objTmpInfoDetalleElemento->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objTmpInfoDetalleElemento);
                                $emInfraestructura->flush();
                            }//foreach( $arrayInfoDetalleElementoBaterias as $objTmpInfoDetalleElemento )


                            $objTmpInfoEmpresaElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                                                  ->findOneBy( array( 'elementoId' => $objTmpInfoElementoBateria,
                                                                                                      'estado'     => self::ESTADO_ACTIVO ));

                            if( $objTmpInfoEmpresaElementoBateria )
                            {
                                $objTmpInfoEmpresaElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objTmpInfoEmpresaElementoBateria);
                                $emInfraestructura->flush();
                            }
                            
                            
                            $objRelacionElementoBateria = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                            ->findOneBy( array( 'elementoIdA' => $intIdElemento,
                                                                                                'elementoIdB' => $intTmpIdElementoBateria,
                                                                                                'estado'      => self::ESTADO_ACTIVO ));

                            if( $objRelacionElementoBateria )
                            {
                                $objRelacionElementoBateria->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objRelacionElementoBateria);
                                $emInfraestructura->flush();
                            }//( !$objRelacionElementoBateria )
                            

                            $objTmpInfoHistorialElementoBateria = new InfoHistorialElemento();
                            $objTmpInfoHistorialElementoBateria->setElementoId($objTmpInfoElementoBateria);
                            $objTmpInfoHistorialElementoBateria->setObservacion('Se elimina la Bateria');
                            $objTmpInfoHistorialElementoBateria->setFeCreacion($datetimeActual);
                            $objTmpInfoHistorialElementoBateria->setUsrCreacion($strUserSession);
                            $objTmpInfoHistorialElementoBateria->setIpCreacion($strIpUserSession);
                            $objTmpInfoHistorialElementoBateria->setEstadoElemento(self::ESTADO_ELIMINADO);
                            $emInfraestructura->persist($objTmpInfoHistorialElementoBateria);
                            $emInfraestructura->flush();
                        }//( $arrayRelacionElementoBaterias as $objTmpRelacionElementoBateria )
                    }//( $arrayRelacionElementoBaterias )
                    /*
                     * Fin Bloque cambia de estado a eliminado las baterias asociadas al UPS que el usuario eliminó
                     */
                }
            }
            
            $emInfraestructura->getConnection()->commit();
            $emInfraestructura->getConnection()->close();
            
            $strMensaje = 'OK';
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            
            $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';

            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }//try
        
        $response->setContent( $strMensaje );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'verificarDataAction'.
     *
     * Retorna un string con 'OK' si no existe la data guardada en base de datos
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-01-2016
     */
    public function verificarDataAction()
    {
        $response               = new Response();
        $objRequest             = $this->get('request');
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdPuerto            = $objRequest->request->get('puerto') ? $objRequest->request->get('puerto') : 0;
        $strIp                  = $objRequest->request->get('ip') ? $objRequest->request->get('ip') : '';
        $strNombreElemento      = $objRequest->request->get('nombre') ? $objRequest->request->get('nombre') : 0;
        $intIdElementoGuardado  = $objRequest->request->get('idUps') ? $objRequest->request->get('idUps') : 0;
        $strMensaje             = 'OK';
        $boolError              = false;
        
        $objInfoIpRepetida = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                                               ->findOneBy( array( "ip" => $strIp, "estado" => self::ESTADO_ACTIVO ) );
        
        if($objInfoIpRepetida)
        {
            if( $intIdElementoGuardado )
            {
                if( $intIdElementoGuardado != $objInfoIpRepetida->getElementoId() )
                {
                    $boolError  = true;
                    $strMensaje = "La Ip ingresada ya existe en otro Elemento, favor revisar!";
                }
            }
            else
            {
                $boolError  = true;
                $strMensaje = "La Ip ingresada ya existe en otro Elemento, favor revisar!";
            }//( $intIdElementoGuardado )
        }//($objInfoIpRepetida)
        
        if( !$boolError )
        {
            if( $intIdPuerto )
            {
                $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->findOneBy( array( 'id' => $intIdPuerto ) );

                if( $objInterfaceElemento )
                {
                    $objEnlaceFinal = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                        ->findOneBy( array( "interfaceElementoFinId" => $objInterfaceElemento, 
                                                                            "estado"                 => self::ESTADO_ACTIVO ) );

                    if( $objEnlaceFinal )
                    {
                        if( $intIdElementoGuardado )
                        {
                            if( $intIdElementoGuardado != $objEnlaceFinal->getElementoId()->getId() )
                            {
                                $boolError  = true;
                                $strMensaje = "El puerto ingresado ya existe enlazado a un elemento, favor revisar!";
                            }
                        }
                        else
                        {
                            $boolError  = true;
                            $strMensaje = "El puerto ingresado ya existe enlazado a un elemento, favor revisar!";
                        }//( $intIdElementoGuardado )
                    }//( $objEnlaceInicial || $objEnlaceFinal )
                }//( $objInterfaceElemento )
            }//( $intIdPuerto )
            
            
            if( !$boolError )
            {
                $arrayElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->findBy( array('estado' => self::ESTADO_ACTIVO, 'nombreElemento' => $strNombreElemento) );

                if($arrayElementos)
                {
                    foreach( $arrayElementos as $objElemento )
                    {
                        if( $objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() == self::TIPO_ELEMENTO )
                        {
                            if( $intIdElementoGuardado )
                            {
                                if( $intIdElementoGuardado != $objElemento->getId() )
                                {
                                    $strMensaje = "Ya existe un elemento con dicho nombre";
                                    break;
                                }
                            }
                            else
                            {
                                $strMensaje = "Ya existe un elemento con dicho nombre";
                                break;
                            }//( $intIdElementoGuardado )
                        }//( $objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() == self::TIPO_ELEMENTO )
                    }//foreach( $arrayElementos as $objElemento )
                }//($objElemento)
            }//( !$boolError )
        }//( !$boolError )
        
        $response->setContent( $strMensaje );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'gridBateriasAction'.
     *
     * Muestra el listado de todas las baterias creadas para un elemento específico.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 26-01-2016
     */
    public function gridBateriasAction()
    {
        $jsonResponse           = new JsonResponse();
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intElementoUps         = $objRequest->query->get('ups') ? $objRequest->query->get('ups') : 0;
        $intStart               = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit               = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $serviceInfoElemento    = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento     = array( 'BATERIA' );
        
        $arrayParametros = array(
                                    'intStart'             => $intStart,
                                    'intLimit'             => $intLimit,
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => strtolower(self::TIPO_ELEMENTO),
                                    'criterios'            => array( 'tipoElemento'     => $arrayTiposElemento,
                                                                     'relacionElemento' => $intElementoUps )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);
        
        $jsonResponse->setData( $arrayResultados );
        
        return $jsonResponse;
    }
    

    /**
     * Documentación para el método 'exportarReporteMonitoreoAction'.
     *
     * Retorna la información de los UPS en formato excel que se consultaron por los filtros elegidos por el usuario.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 28-03-2016
     */
    public function exportarInformacionUpsAction()
    {
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);
        
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strUsuarioSession      = $objSession->get('empleado') ? ucwords(strtolower($objSession->get('empleado'))) : '';
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombre              = $objRequest->request->get('strNombreUps') ? $objRequest->request->get('strNombreUps') : "";
        $intModelo              = $objRequest->request->get('intIdModeloUps') ? $objRequest->request->get('intIdModeloUps') : "";
        $strModelo              = $objRequest->request->get('strModeloUps') ? $objRequest->request->get('strModeloUps') : "";
        $strIp                  = $objRequest->request->get('strIpUps') ? $objRequest->request->get('strIpUps') : "";
        $strFeInstalacion       = $objRequest->request->get('strFechaInstalacion') ? $objRequest->request->get('strFechaInstalacion') : "";
        $strFeInstalacion       = str_replace('/', '-', $strFeInstalacion);
        $arrayTmpFeInstalacion  = explode('-', $strFeInstalacion);
        $strFeInstalacion       = $strFeInstalacion ? ($arrayTmpFeInstalacion[1].'-'.$arrayTmpFeInstalacion[0].'-'.$arrayTmpFeInstalacion[2]) : '';
        $intStart               = 0;
        $intLimit               = 0;
        $serviceInfoElemento    = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento     = array( self::TIPO_ELEMENTO );
        $arrayModelosElemento   = $intModelo ? array( $intModelo ) : array(); 
        
        $arrayParametros = array(
                                    'intStart'             => $intStart,
                                    'intLimit'             => $intLimit,
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => strtolower(self::TIPO_ELEMENTO),
                                    'criterios'            => array( 'nombre'                => $strNombre, 
                                                                     'tipoElemento'          => $arrayTiposElemento,
                                                                     'modeloElemento'        => $arrayModelosElemento,
                                                                     'feInstalacionBaterias' => $strFeInstalacion,
                                                                     'ipElemento'            => $strIp
                                                                   )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);

        $objPHPExcel   = new PHPExcel();
        $cacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objPHPExcel = $objReader->load(__DIR__ . "/../Resources/templatesExcel/templateUpsInformacion.xls");

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($strUsuarioSession);
        $objPHPExcel->getProperties()->setTitle("Tabla de UPS");
        $objPHPExcel->getProperties()->setSubject("Tabla de UPS");
        $objPHPExcel->getProperties()->setDescription("Información de UPS");
        $objPHPExcel->getProperties()->setKeywords("UPS");
        $objPHPExcel->getProperties()->setCategory("Informacion");
        
        $objPHPExcel->getActiveSheet()->setCellValue('B3', $strUsuarioSession);

        $objPHPExcel->getActiveSheet()->setCellValue('B4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8', ''.($strNombre ? $strNombre : 'Todos'));
        $objPHPExcel->getActiveSheet()->setCellValue('B9', ''.($strIp ? $strIp : 'Todas'));
        $objPHPExcel->getActiveSheet()->setCellValue('E8', ''.($strModelo ? $strModelo : 'Todas'));
        $objPHPExcel->getActiveSheet()->setCellValue('E9', ''.($strFeInstalacion ? $strFeInstalacion : 'Todas'));

        $i = 14;
        $j = 1;
        
        $styleAlignCenterCenter = array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                         'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER  );
        
        $styleAlignLeftCenter = array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                       'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER );
        
        foreach($arrayResultados['encontrados'] as $arrayElemento)
        {
            /*
             * Bloque que obtiene el Nombre del Nodo
             */
            $strNombreNodo       = '';
            $objRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                     ->findOneBy( array ( 'elementoIdB' => $arrayElemento['intIdElemento'], 
                                                                          'estado'      => self::ESTADO_ACTIVO) );

            if( $objRelacionElemento )
            {
                $intIdNodo           = $objRelacionElemento->getElementoIdA();
                $objInfoElementoNodo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($intIdNodo);

                if( $objInfoElementoNodo )
                {
                    $strNombreNodo = $objInfoElementoNodo->getNombreElemento();
                }
            }
            /*
             * Fin Bloque que obtiene el Nombre del Nodo
             */
            
            
            /*
             * Bloque que obtiene la ip guardada con el UPS
             */
            $strIpUps = '';
            $objInfoIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                                           ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'elementoId' => $arrayElemento['intIdElemento']) );

            if($objInfoIp)
            {
                $strIpUps = $objInfoIp->getIp();
            }
            /*
             * Fin Bloque que obtiene la ip guardada con el UPS
             */
            
            
            /*
             * Bloque que retorna la información de las baterías de un UPS
             */
            $arrayParametros = array(
                                        'intStart'             => $intStart,
                                        'intLimit'             => $intLimit,
                                        'intEmpresa'           => $intIdEmpresaSession,
                                        'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                        'strCategoriaElemento' => 'ups',
                                        'criterios'            => array( 'tipoElemento'     => array( 'BATERIA' ),
                                                                         'relacionElemento' => $arrayElemento['intIdElemento'] )
                                    );

            $arrayBaterias = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);

            
            $k = $i;
            
            foreach($arrayBaterias['encontrados'] as $arrayBateria)
            {
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$k, $arrayBateria['strTIPO_BATERIA']);
                $objPHPExcel->getActiveSheet()->getStyle('E'.$k)->applyFromArray( array('alignment' => $styleAlignCenterCenter) );
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$k, $arrayBateria['strAMPERAJE']);
                $objPHPExcel->getActiveSheet()->getStyle('F'.$k)->applyFromArray( array('alignment' => $styleAlignCenterCenter) );
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$k, $arrayBateria['strSerieFisica']);
                $objPHPExcel->getActiveSheet()->getStyle('G'.$k)->applyFromArray( array('alignment' => $styleAlignCenterCenter) );
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$k, $arrayBateria['strModeloElemento']);
                $objPHPExcel->getActiveSheet()->getStyle('H'.$k)->applyFromArray( array('alignment' => $styleAlignCenterCenter) );
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$k, $arrayBateria['strFECHA_INSTALACION']);
                $objPHPExcel->getActiveSheet()->getStyle('I'.$k)->applyFromArray( array('alignment' => $styleAlignCenterCenter) );
            
                $k++;
            }
        
        
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $strNombreNodo);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray( array('alignment' => $styleAlignLeftCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $arrayElemento['strNombreElemento']);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( array('alignment' => $styleAlignLeftCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $strIpUps);
            $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray( array('alignment' => $styleAlignCenterCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $arrayElemento['strModeloElemento']);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray( array('alignment' => $styleAlignLeftCenter) );
            
            
            if( $arrayBaterias['total'] > 0 )
            {
                $intContadorTemporal = $i + $arrayBaterias['total'];
                $intContadorTemporal--;
            }
            else
            {
                $intContadorTemporal = $i;
            }
            
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$intContadorTemporal);
            $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.$intContadorTemporal);
            $objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.$intContadorTemporal);
            $objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':D'.$intContadorTemporal);
            
            $i = $intContadorTemporal;
            
            $j++;
            $i++;
        }
        
        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Tabla_de_UPS_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        
        exit;
    }
    
    
    /**
     * @Secure(roles="ROLE_326-3457")
     * 
     * Documentación para el método 'gridMonitoreoAction'.
     *
     * Muestra el listado de todos los UPS creados para monitorear.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-02-2016
     */
    public function gridMonitoreoAction()
    {
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte               = $this->getDoctrine()->getManager("telconet_soporte");
        $intIdEstadosMonitoreo   = 0;
        $intIdSeveridadMonitoreo = 0;
        $intIdTarea              = 0;
        $strNombreTarea          = 'MANTENIMIENTO';
        $intIdEmpresaSession     = $objSession->get('idEmpresa');
        $strEmpresaSession       = $objSession->get('empresa');
        
        /*
         * Bloque que busca el id del parametro 'ESTADOS_MONITOREO_UPS'
         */
        $objGeneralEstados = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                       ->findOneBy( array( 'nombreParametro' => 'ESTADOS_MONITOREO_UPS', 'estado' => 'Activo' ) );
        
        if( $objGeneralEstados )
        {
            $intIdEstadosMonitoreo = $objGeneralEstados->getId();
        }
        /*
         * Fin Bloque que busca el id del parametro 'ESTADOS_MONITOREO_UPS'
         */
        
        
        /*
         * Bloque que busca el id del parametro 'SEVERIDAD_MONITOREO_UPS'
         */
        $objGeneralSeveridad = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy( array( 'nombreParametro' => 'SEVERIDAD_MONITOREO_UPS', 'estado' => 'Activo' ) );
        
        if( $objGeneralSeveridad )
        {
            $intIdSeveridadMonitoreo = $objGeneralSeveridad->getId();
        }
        /*
         * Fin Bloque que busca el id del parametro 'SEVERIDAD_MONITOREO_UPS'
         */
        
        
        /*
         * Bloque que busca el id de la tarea
         */
        $objAdmiProceso = $emSoporte->getRepository('schemaBundle:AdmiProceso')
                                    ->findOneBy(array('estado' => self::ESTADO_ACTIVO, 'nombreProceso' => 'INCIDENCIAS DE ELEMENTOS'));
        
        if( $objAdmiProceso )
        {
            $objAdmiTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                      ->findOneBy( array( 'estado'      => self::ESTADO_ACTIVO, 
                                                          'nombreTarea' => $strNombreTarea, 
                                                          'procesoId'   => $objAdmiProceso ) );
            
            if( $objAdmiTarea )
            {
                $intIdTarea = $objAdmiTarea->getId();
            }
        }
        /*
         * Fin Bloque que busca el id de la tarea
         */
        
        if (true === $this->get('security.context')->isGranted('ROLE_326-6'))
        {
            $rolesPermitidos[] = 'ROLE_326-3817';//Asignar Tarea en el monitoreo de UPS
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_326-4'))
        {
            $rolesPermitidos[] = 'ROLE_326-3577';//Exportar a Excel Reporte de Monitoreo
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_326-8'))
        {
            $rolesPermitidos[] = 'ROLE_326-1147';//Ver Seguimiento de Tarea en el monitoreo de UPS
        }
        
        return $this->render('tecnicoBundle:InfoElementoUps:monitoreo.html.twig', array( 'intIdEstadosMonitoreo'   => $intIdEstadosMonitoreo,
                                                                                         'intIdSeveridadMonitoreo' => $intIdSeveridadMonitoreo,
                                                                                         'intIdEmpresaSession'     => $intIdEmpresaSession,
                                                                                         'strEmpresaSession'       => $strEmpresaSession,
                                                                                         'intIdTarea'              => $intIdTarea,
                                                                                         'strNombreTarea'          => $strNombreTarea,
                                                                                         'rolesPermitidos'         => $rolesPermitidos,
                                                                                         'intValorInicial'         => self::VALOR_INICIAL_BUSQUEDA,
                                                                                         'intValorLimite'          => self::VALOR_LIMITE_BUSQUEDA ));
    }
    
    
    /**
     * Documentación para el método 'buscarDispositivosAction'.
     *
     * Muestra el listado de todos los UPS para monitorear.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-02-2016
     */
    public function buscarDispositivosAction()
    {
        $objJsonResponse     = new JsonResponse();
        $objRequest          = $this->get('request');

        $strDraw             = $objRequest->request->get('draw') ? $objRequest->request->get('draw') : "1";
        $intLength           = $objRequest->request->get('length') ? $objRequest->request->get('length') : self::VALOR_LIMITE_BUSQUEDA;
        $intStart            = $objRequest->request->get('start') ? $objRequest->request->get('start') : self::VALOR_INICIAL_BUSQUEDA;
        $strNombreNodo       = $objRequest->request->get('strNombreNodo') ? $objRequest->request->get('strNombreNodo') : "";
        $strIpsUps           = $objRequest->request->get('strIpsUps') ? $objRequest->request->get('strIpsUps') : "";
        $strMarca            = $objRequest->request->get('strMarca') ? $objRequest->request->get('strMarca') : "";
        $strRegion           = $objRequest->request->get('strRegion') ? $objRequest->request->get('strRegion') : "";
        $strProvincia        = $objRequest->request->get('strProvincia') ? $objRequest->request->get('strProvincia') : "";
        $strCiudad           = $objRequest->request->get('strCiudad') ? $objRequest->request->get('strCiudad') : "";
        $strEstado           = $objRequest->request->get('strEstado') ? $objRequest->request->get('strEstado') : "Activo";
        $arrayEstados        = $strEstado ? explode(',', $strEstado) : array();
        $strSeveridad        = $objRequest->request->get('strSeveridad') ? $objRequest->request->get('strSeveridad') : "";
        $arraySeveridad      = $strSeveridad ? explode(',', $strSeveridad) : array();
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');
        
        $arrayParametros = array( 'intInicio' => $intStart,
                                  'intLimite' => $intLength,
                                  'criterios' => array( 'strNombreNodo'  => $strNombreNodo,
                                                        'strIpsUps'      => $strIpsUps,
                                                        'strMarca'       => $strMarca,
                                                        'strRegion'      => $strRegion,
                                                        'strProvincia'   => $strProvincia,
                                                        'strCiudad'      => $strCiudad,
                                                        'arrayEstado'    => $arrayEstados,
                                                        'arraySeveridad' => $arraySeveridad ) );

        $arrayResultados = $serviceInfoElemento->getAlertasMonitoreoUPS( $arrayParametros );
        
        $arrayDataJson = array( "draw"            => $strDraw, 
                                "recordsTotal"    => $arrayResultados['total'], 
                                "recordsFiltered" => $arrayResultados['total'], 
                                "data"            => $arrayResultados['registros'],
                                "intInicio"       => $intStart,
                                "intLimite"       => $intLength );
        
        $objJsonResponse->setData($arrayDataJson);
        
        return $objJsonResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_326-3577")
     * 
     * Documentación para el método 'exportarReporteMonitoreoAction'.
     *
     * Retorna la información consolidada sobre los dispositivos enlazados a los UPS.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-02-2016
     */
    public function exportarReporteMonitoreoAction()
    {
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);
        
        $objRequest          = $this->get('request');        
        $objSession          = $objRequest->getSession();
        $strUsuarioSession   = $objSession->get('empleado') ? ucwords(strtolower($objSession->get('empleado'))) : '';
        $strNombreNodo       = $objRequest->request->get('strNombreNodo') ? $objRequest->request->get('strNombreNodo') : "";
        $strIpsUps           = $objRequest->request->get('strIpsUps') ? $objRequest->request->get('strIpsUps') : "";
        $strMarca            = $objRequest->request->get('strMarca') ? $objRequest->request->get('strMarca') : "";
        $strRegion           = $objRequest->request->get('strRegion') ? $objRequest->request->get('strRegion') : "";
        $strProvincia        = $objRequest->request->get('strProvincia') ? $objRequest->request->get('strProvincia') : "";
        $strCiudad           = $objRequest->request->get('strCiudad') ? $objRequest->request->get('strCiudad') : "";
        $strEstado           = $objRequest->request->get('strEstado') ? $objRequest->request->get('strEstado') : "";
        $arrayEstados        = $strEstado ? explode(',', $strEstado) : array();
        $strSeveridad        = $objRequest->request->get('strSeveridad') ? $objRequest->request->get('strSeveridad') : "";
        $arraySeveridad      = $strSeveridad ? explode(',', $strSeveridad) : array();
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');
        
        $arrayParametros = array( 'criterios' => array( 'strNombreNodo' => $strNombreNodo,
                                                        'strIpsUps'     => $strIpsUps,
                                                        'strMarca'      => $strMarca,
                                                        'strRegion'     => $strRegion,
                                                        'strProvincia'  => $strProvincia,
                                                        'strCiudad'     => $strCiudad,
                                                        'arrayEstado'   => $arrayEstados,
                                                        'arraySeveridad' => $arraySeveridad ) );
        $arrayResultados   = $serviceInfoElemento->getAlertasMonitoreoUPS( $arrayParametros );
        $arrayDispositivos = ( !empty($arrayResultados['registros']) ) ? $arrayResultados['registros'] : array();

        $objPHPExcel   = new PHPExcel();
        $cacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objPHPExcel = $objReader->load(__DIR__ . "/../Resources/templatesExcel/templateUpsMonitoreo.xls");

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($strUsuarioSession);
        $objPHPExcel->getProperties()->setTitle("Tabla de Monitoreo de UPS");
        $objPHPExcel->getProperties()->setSubject("Tabla de Monitoreo de UPS");
        $objPHPExcel->getProperties()->setDescription("Monitoreo de UPS");
        $objPHPExcel->getProperties()->setKeywords("UPS");
        $objPHPExcel->getProperties()->setCategory("Monitoreo");
        
        $objPHPExcel->getActiveSheet()->setCellValue('B3', $strUsuarioSession);

        $objPHPExcel->getActiveSheet()->setCellValue('B4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8', ''.($strNombreNodo ? $strNombreNodo : 'Todos'));
        $objPHPExcel->getActiveSheet()->setCellValue('B9', ''.($strMarca ? $strMarca : 'Todas'));
        $objPHPExcel->getActiveSheet()->setCellValue('B10', ''.($strProvincia ? $strProvincia : 'Todas'));
        $objPHPExcel->getActiveSheet()->setCellValue('B11', ''.($strEstado ? $strEstado : 'Todos'));
        $objPHPExcel->getActiveSheet()->setCellValue('E8', ''.($strIpsUps ? $strIpsUps : 'Todas'));
        $objPHPExcel->getActiveSheet()->setCellValue('E9', ''.($strRegion ? $strRegion : 'Todas'));
        $objPHPExcel->getActiveSheet()->setCellValue('E10', ''.($strCiudad ? $strCiudad : 'Todas'));
        $objPHPExcel->getActiveSheet()->setCellValue('E11', ''.($strSeveridad ? $strSeveridad : 'Todos'));

        $i = 16;
        $j = 1;
        
        $styleAlignCenter                = array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
        $styleBackgroundColorAlta        = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FF6666') );
        $styleBackgroundColorDesastre    = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'dd4b39') );
        $styleBackgroundColorMedia       = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FF6633') );
        $styleBackgroundColorInformativo = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCFFFF') );
        $styleBackgroundColorPrecaucion  = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFF99') );
        
        foreach($arrayDispositivos as $arrayDispositivo)
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $arrayDispositivo['nombreNodo']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $arrayDispositivo['nombreUps']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $arrayDispositivo['ipUps']);
            $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $arrayDispositivo['tipo']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $arrayDispositivo['ciudad']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $arrayDispositivo['direccion']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $arrayDispositivo['severidad']);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $arrayDispositivo['descripcionAlerta']);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $arrayDispositivo['fechaModificacion']);
            $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            
            if( $arrayDispositivo['severidad'] == "Alta")
            {
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorAlta) );
                $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorAlta) );
            }
            else if( $arrayDispositivo['severidad'] == "Desastre")
            {
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorDesastre) );
                $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorDesastre) );
            }
            else if( $arrayDispositivo['severidad'] == "Media")
            {
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorMedia) );
                $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorMedia) );
            }
            else if( $arrayDispositivo['severidad'] == "Informativo")
            {
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorInformativo) );
                $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorInformativo) );
            }
            else if( $arrayDispositivo['severidad'] == "Precaucion")
            {
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorPrecaucion) );
                $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray( array('alignment' => $styleAlignCenter,
                                                                                        'fill'      => $styleBackgroundColorPrecaucion) );
            }
            
            $j++;
            $i++;
        }
        
        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Tabla_de_UPS_Monitoreo_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        
        exit;
    }
    
    
    /**
     * @Secure(roles="ROLE_326-3577")
     * 
     * Documentación para el método 'asignarTareaAction'.
     *
     * Método que asigna tarea a un empleado.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-02-2016
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 09-08-2017 - En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                           se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 26-12-2017 - En el asunto y cuerpo del correo se agrega el nombre del proceso al que pertenece la tarea asignada
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     *
     */
    public function asignarTareaAction()
    {
        $objJsonResponse         = new JsonResponse();
        $boolError               = false;
        $strMensajeRespuesta     = "OK";
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strUserSession          = $objSession->get('user');
        $strIpUserSession        = $objRequest->getClientIp();
        $datetimeActual          = new \DateTime('now');
        $emComercial             = $this->getDoctrine()->getManager('telconet');
        $emSoporte               = $this->getDoctrine()->getManager("telconet_soporte");
        $intIdEmpresaSession     = $objSession->get('idEmpresa');
        $intIdDepartamento       = $objSession->get('idDepartamento');
        $intIdAlerta             = $objRequest->request->get('intIdAlerta') ? $objRequest->request->get('intIdAlerta') : 0;
        $intIdElemento           = $objRequest->request->get('intIdElemento') ? $objRequest->request->get('intIdElemento') : 0;
        $intIdTarea              = $objRequest->request->get('intIdTarea') ? $objRequest->request->get('intIdTarea') : 0;
        $strObservacion          = $objRequest->request->get('strObservacion') ? $objRequest->request->get('strObservacion') : '';  
        $intDepartamentoAsignado = $objRequest->request->get('intDepartamentoAsignado') ? $objRequest->request->get('intDepartamentoAsignado') : 0;  
        $strEmpleadoAsignado     = $objRequest->request->get('strEmpleadoAsignado') ? $objRequest->request->get('strEmpleadoAsignado') : '';
        $strFechaEjecucion       = $objRequest->request->get('strFechaEjecucion') ? $objRequest->request->get('strFechaEjecucion') : '';
        $arrayFechaEjecucion     = $strFechaEjecucion ? explode('T', $strFechaEjecucion) : array();
        $strFechaEjecucion       = $arrayFechaEjecucion ? $arrayFechaEjecucion[0] : '';
        $strHoraEjecucion        = $objRequest->request->get('strHoraEjecucion') ? $objRequest->request->get('strHoraEjecucion') : '';
        $arrayHoraEjecucion      = $strHoraEjecucion ? explode('T', $strHoraEjecucion) : array();
        $strHoraEjecucion        = $arrayHoraEjecucion ? $arrayHoraEjecucion[1] : '';
        $arrayHoraEjecucion      = $strHoraEjecucion ? explode(':', $strHoraEjecucion) : array();
        $strHoraEjecucion        = $arrayHoraEjecucion ? $arrayHoraEjecucion[0].':'.$arrayHoraEjecucion[1] : '';
        $serviceSoporte       = $this->get('soporte.SoporteService');
        $arrayParametrosHist  = array();
        $strNombreProceso     = "";
        
        $arrayParametrosHist["strCodEmpresa"]           = $intIdEmpresaSession;
        $arrayParametrosHist["strUsrCreacion"]          = $strUserSession;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $strIpUserSession;
        
        $arrayEmpleadoAsignado          = $strEmpleadoAsignado ? explode("@@",$strEmpleadoAsignado) : array();                     
        $intIdPersonaAsignado           = $arrayEmpleadoAsignado ? $arrayEmpleadoAsignado[0] : 0;                     
        $intIdPersonaEmpresaRolAsignado = $arrayEmpleadoAsignado ? $arrayEmpleadoAsignado[1] : 0;
        $strNombreDepartamentoAsignado  = $objRequest->request->get('strNombreDepartamentoAsignado') 
                                          ? $objRequest->request->get('strNombreDepartamentoAsignado') : '';  
        $strNombreEmpleadoAsignado      = $objRequest->request->get('strNombreEmpleadoAsignado') 
                                          ? $objRequest->request->get('strNombreEmpleadoAsignado') : '';
        

        $emComercial->getConnection()->beginTransaction();
        $emSoporte->getConnection()->beginTransaction();
            
        try
        {
            $objAdmiTarea            = $emSoporte->getRepository('schemaBundle:AdmiTarea')->findOneById( $intIdTarea );
            $datetimeFechaSolicitada = date_create(date('Y-m-d H:i', strtotime($strFechaEjecucion.' '.$strHoraEjecucion)));
            
            
            /*
             * Bloque Crear Solicitud
             * 
             * Este bloque crea una solicitud por mantenimiento de equipo UPS
             */
            $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                            ->findOneByDescripcionSolicitud( 'SOLICITUD MANTENIMIENTO MONITOREO ELEMENTO' );

            $strObservacionSolicitud = "Se crea solicitud por Mantenimiento Monitoreo Elemento UPS";
            
            $objDetalleSolicitud = new InfoDetalleSolicitud();
            $objDetalleSolicitud->setEstado("Pendiente");
            $objDetalleSolicitud->setFeCreacion($datetimeActual);
            $objDetalleSolicitud->setUsrCreacion($strUserSession);
            $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
            $objDetalleSolicitud->setObservacion($strObservacionSolicitud);
            $objDetalleSolicitud->setElementoId($intIdElemento);
            $emComercial->persist($objDetalleSolicitud);
            $emComercial->flush();                                    
                                    
            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneByDescripcionCaracteristica( "SOLICITUD_MONITOREO_ELEMENTO" );

            $objDetalleSolCarac = new InfoDetalleSolCaract();
            $objDetalleSolCarac->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolCarac->setEstado("Activo");
            $objDetalleSolCarac->setFeCreacion($datetimeActual);
            $objDetalleSolCarac->setUsrCreacion($strUserSession);
            $objDetalleSolCarac->setValor($intIdAlerta);
            $objDetalleSolCarac->setCaracteristicaId($objAdmiCaracteristica);
            $emComercial->persist($objDetalleSolCarac);
            $emComercial->flush();
            
            $objDetalleSolHist = new InfoDetalleSolHist();
            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHist->setEstado("Pendiente");
            $objDetalleSolHist->setFeCreacion($datetimeActual);
            $objDetalleSolHist->setIpCreacion($strIpUserSession);
            $objDetalleSolHist->setUsrCreacion($strUserSession);
            $objDetalleSolHist->setObservacion($strObservacionSolicitud);
            $emComercial->persist($objDetalleSolHist);
            $emComercial->flush();
            /*
             * Fin Bloque Crear Solicitud
             */
            
		
            /*
             * Bloque Crear Tarea
             * 
             * Este bloque crea una tarea a la cual estara enlazada la solicitud de mantenimiento.
             */
            $objInfoDetalle = new InfoDetalle();
            $objInfoDetalle->setTareaId($objAdmiTarea);
            $objInfoDetalle->setObservacion($strObservacion);
            $objInfoDetalle->setPesoPresupuestado(0);
            $objInfoDetalle->setValorPresupuestado(0);
            $objInfoDetalle->setFeSolicitada($datetimeFechaSolicitada);
            $objInfoDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
            $objInfoDetalle->setFeCreacion($datetimeActual);
            $objInfoDetalle->setUsrCreacion($strUserSession);
            $objInfoDetalle->setIpCreacion($strIpUserSession);
            $emSoporte->persist($objInfoDetalle);
            $emSoporte->flush();						

            $objInfoDetalleAsignacion = new InfoDetalleAsignacion();
            $objInfoDetalleAsignacion->setDetalleId($objInfoDetalle);
            $objInfoDetalleAsignacion->setMotivo($strObservacion);	
            $objInfoDetalleAsignacion->setAsignadoId($intDepartamentoAsignado);
            $objInfoDetalleAsignacion->setAsignadoNombre($strNombreDepartamentoAsignado);
            $objInfoDetalleAsignacion->setRefAsignadoId($intIdPersonaAsignado);
            $objInfoDetalleAsignacion->setRefAsignadoNombre($strNombreEmpleadoAsignado); 
            $objInfoDetalleAsignacion->setPersonaEmpresaRolId($intIdPersonaEmpresaRolAsignado);
            $objInfoDetalleAsignacion->setTipoAsignado("EMPLEADO");
            $objInfoDetalleAsignacion->setIpCreacion($strIpUserSession);
            $objInfoDetalleAsignacion->setFeCreacion($datetimeActual);
            $objInfoDetalleAsignacion->setUsrCreacion($strUserSession);
            $emSoporte->persist($objInfoDetalleAsignacion);
            $emSoporte->flush();            		    		    
            
            //Se ingresa el historial de la tarea
            if(is_object($objInfoDetalle))
            {
                $arrayParametrosHist["intDetalleId"] = $objInfoDetalle->getId();            
            }
                        
            $arrayParametrosHist["strObservacion"]  = "Tarea Asignada - Modulo de Monitoreo de Elemento UPS";                
            $arrayParametrosHist["strEstadoActual"] = "Asignada";
            $arrayParametrosHist["strAccion"]       = "Asignada";

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);             
            
            //Se ingresa el seguimiento de la tarea        
            $arrayParametrosHist["strObservacion"] = "Tarea fue asignada a ".$strNombreEmpleadoAsignado;
            $arrayParametrosHist["strOpcion"]      = "Seguimiento";

            $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);               
            /*
             * Fin Bloque Crear Tarea
             */
            
            
            /***************************************************************************************************/
            //		ENVIO MAIL DE NOTIFICACION POR CREACION DE TAREA
            /***************************************************************************************************/	
            $intCanton = 0; 
            $arrayTo   = array();            
            
            $objAdmiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                ->findOneBy( array( 'descripcionFormaContacto' => 'Correo Electronico',
                                                                    'estado'                   => self::ESTADO_ACTIVO ) );

            $objInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                       ->findOneBy( array( 'personaId'       => $intIdPersonaAsignado,
                                                                           'formaContactoId' => $objAdmiFormaContacto,
                                                                           'estado'          => self::ESTADO_ACTIVO ));					  

            if($objInfoPersonaFormaContacto)	
            {
                $arrayTo[] = $objInfoPersonaFormaContacto->getValor();//Correo Persona Asignada	
            }
            

            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRolAsignado);											

            if($objInfoPersonaEmpresaRol)
            {		  
                $objInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                   ->find( $objInfoPersonaEmpresaRol->getOficinaId()->getId() );
                $intCanton           = $objInfoOficinaGrupo->getCantonId();
            }

            if(is_object($objInfoDetalle))
            {
                $objAdmiTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($objInfoDetalle->getTareaId());
            }

            if(is_object($objAdmiTarea))
            {
                $strNombreProceso = $objAdmiTarea->getProcesoId()->getNombreProceso();
            }

            $strAsunto    = "Nueva Tarea, INCIDENCIA DE ELEMENTO - MANTENIMIENTO | PROCESO: ".$strNombreProceso;
            $strActividad = "TAREAACT";

            $arrayParametros = array(
                                        'nombreProceso'     => $strNombreProceso,
                                        'asignacion'        => $objInfoDetalleAsignacion,
                                        'nombreTarea'       => "MANTENIMIENTO",
                                        'empleadoLogeado'   => $objSession->get('empleado'),
                                        'empresa'           => $objSession->get('prefijoEmpresa')
                                    );					  					  		  
            
            $envioPlantilla = $this->get('soporte.EnvioPlantilla');     
            $envioPlantilla->generarEnvioPlantilla( $strAsunto, $arrayTo , $strActividad, $arrayParametros , $intIdEmpresaSession , $intCanton, 
                                                    $intDepartamentoAsignado );
            /********************************************************************************************************/		
            
            $emComercial->getConnection()->commit();                
            $emSoporte->getConnection()->commit();

            //Proceso que graba tarea en INFO_TAREA
            if(is_object($objInfoDetalle))
            {
                $arrayParametrosInfoTarea['intDetalleId']   = $objInfoDetalle->getId();
                $arrayParametrosInfoTarea['strUsrCreacion'] = $strUserSession;
                $objServiceSoporte                          = $this->get('soporte.SoporteService');
                $objServiceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
            }
        } 
        catch (Exception $e) 
        {
            error_log($e->getMessage());
            
            $boolError           = false;
            $strMensajeRespuesta = "Hubo un problema al asignar la tarea, por favor vuelva a intentarlo";
                
            $emComercial->getConnection()->rollback();
            $emSoporte->getConnection()->rollback();
        }

        $emComercial->getConnection()->close();
        $emSoporte->getConnection()->close();
        
        $objJsonResponse->setData( array('boolError' => $boolError, 'strMensaje' => $strMensajeRespuesta) );
        
        return $objJsonResponse;
    }
}
