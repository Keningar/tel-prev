<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Form\InfoElementoTabletType;

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
use \PHPExcel_Style_Border;

/**
 * InfoElementoTablet controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la administración de Tablets de la empresa
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 12-11-2015
 */
class InfoElementoTabletController extends Controller
{
    const TIPO_ELEMENTO_TABLET              = 'TABLET';
    const ESTADO_ACTIVO                     = 'Activo';
    const ESTADO_ELIMINADO                  = 'Eliminado';
    const DETALLE_ASOCIADO_ELEMENTO_LIDER   = 'LIDER';
    const DETALLE_RESPONSABLE_TABLET        = 'RESPONSABLE_TABLET';
    const DETALLE_MOTIVO_INACTIVAR_TABLET   = 'MOTIVO_INACTIVAR_TABLET';
    const ESTADOS_MONITOREO_TABLET          = 'ESTADOS_MONITOREO_TABLET';
    const VALOR_INICIAL_BUSQUEDA            = 0;
    const VALOR_LIMITE_BUSQUEDA             = 10;
    const ESTADO_MANTENIMIENTO_LIBRE        = 'MANTENIMIENTO_LIBRE';
    const ESTADO_CUADRILLAS_LIBRES          = 'CUADRILLAS_LIBRES';
    const ESTADO_EN_CAMPO                   = 'EN_CAMPO';
    const LIMITE_MINUTOS_MONITOREO          = 'LIMITE_MINUTOS_MONITOREO';
    const DEPARTAMENTO_FILTRO_POR_HORARIO   = 'DEPARTAMENTOS_MONITOREO_TABLETS_FILTRAR_POR_HORARIO';

    /**
     * @Secure(roles="ROLE_314-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redireccion a la pantalla principal de la administracion de tablets de la empresa
     * @return render.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-11-2012
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-11-2016 Se agrega el permiso para inactivar y reactivar una tablet
     */
    public function indexAction()
    {
        $arrayRolesPermitidos   = array();
        $em_seguridad           = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu         = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("314", "1");
        
        //MODULO 314 - tablet/reactivar
        if (true === $this->get('security.context')->isGranted('ROLE_314-4977'))
        {
            $arrayRolesPermitidos[] = 'ROLE_314-4977';
        }
        //MODULO 314 - tablet/inactivar
        if (true === $this->get('security.context')->isGranted('ROLE_314-4957'))
        {
            $arrayRolesPermitidos[] = 'ROLE_314-4957';
        }
        
        return $this->render('tecnicoBundle:InfoElementoTablet:index.html.twig',
                             array(
                                    'item'            => $entityItemMenu,
                                    'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }
    /**
     * @Secure(roles="ROLE_314-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra el listado de todas las tablets creados.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-11-2012
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-12-2016 Se agregan los filtros por estado
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.2 01-10-2020 Se agregan los filtros por Publish Id
     * 
     */
    public function gridAction()
    {
        $jsonResponse        = new JsonResponse();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdEmpresaSession = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strImei             = $objRequest->get('imei') ? $objRequest->get('imei') : "";
        $strSerieLogica      = $objRequest->get('serieLogica') ? $objRequest->get('serieLogica') : "";
        $strEstado           = $objRequest->get('strEstado') ? $objRequest->get('strEstado') : "";
        $intStart            = $objRequest->get('start') ? $objRequest->get('start') : 0;
        $intLimit            = $objRequest->get('limit') ? $objRequest->get('limit') : 0;
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento  = array( self::TIPO_ELEMENTO_TABLET );
        
        $arrayEstadoTablets  = array('Activo','Inactivo');
        if($strEstado!="")
        {
            $arrayEstadoTablets = array($strEstado);
        }
        
        $arrayParametros = array(
                                    'intStart'             => $intStart,
                                    'intLimit'             => $intLimit,
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => self::TIPO_ELEMENTO_TABLET,
                                    'arrayEstadosBusqueda' => $arrayEstadoTablets,
                                    'criterios'            => array( 'nombreIndex'     => $strImei,
                                                                     'tipoElemento'    => $arrayTiposElemento,
                                                                     'strPublishIndex' => $strSerieLogica )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);
        
        $jsonResponse->setData( $arrayResultados );
        
        return $jsonResponse;
    }
    
    /**
     * @Secure(roles="ROLE_314-4957")
     * 
     * Documentación para el método 'inactivarAction'.
     *
     * Función que inactiva una tablet de acuerdo al motivo seleccionado
     *
     * @return JsonResponse $objResponse
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-12-2016
     */ 
    public function inactivarAction()
    {
        $objResponse        = new JsonResponse();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objDatetimeActual  = new \DateTime('now');
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $strMensaje         = "";
        $serviceUtil        = $this->get('schema.Util');
        
        $intIdElementoTablet    = $objRequest->get('intIdElemento') ? $objRequest->get('intIdElemento') : 0;
        $intIdMotivoInactivar   = $objRequest->get('intIdMotivoInactivar') ? $objRequest->get('intIdMotivoInactivar') : 0;
        
        if($intIdElementoTablet && $intIdMotivoInactivar)
        {
            $objElementoTablet  = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoTablet);
            $objMotivoInactivar = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivoInactivar);
            
            if(is_object($objElementoTablet) && is_object($objMotivoInactivar))
            {
                $emInfraestructura->beginTransaction();
                try
                {
                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setElementoId($intIdElementoTablet);
                    $objInfoDetalleElemento->setDetalleNombre(self::DETALLE_MOTIVO_INACTIVAR_TABLET);
                    $objInfoDetalleElemento->setDetalleValor($intIdMotivoInactivar);
                    $objInfoDetalleElemento->setDetalleDescripcion(self::DETALLE_MOTIVO_INACTIVAR_TABLET);
                    $objInfoDetalleElemento->setFeCreacion($objDatetimeActual);
                    $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                    $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                    $objInfoDetalleElemento->setEstado('Activo');
                    $emInfraestructura->persist($objInfoDetalleElemento);

                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objElementoTablet);
                    $objInfoHistorialElemento->setObservacion('Se inactiva la tablet por : '.$objMotivoInactivar->getNombreMotivo());
                    $objInfoHistorialElemento->setFeCreacion($objDatetimeActual);
                    $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                    $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElemento->setEstadoElemento('Inactivo');
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush();

                    $objElementoTablet->setEstado('Inactivo');
                    $emInfraestructura->persist($objElementoTablet);
                    
                    $emInfraestructura->flush();
                    $emInfraestructura->commit();
                    $strMensaje .= 'OK';
                }
                catch (\Exception $e)
                {
                    $strMensaje .= 'Ha ocurrido un problema al inactivar la tablet. Por favor notificar a Sistemas!';
                    if ($emInfraestructura->getConnection()->isTransactionActive())
                    {
                        $emInfraestructura->getConnection()->rollback();
                    }
                    $emInfraestructura->getConnection()->close();

                    $serviceUtil->insertError(
                                                'Telcos+', 
                                                'InfoElementoTabletController->inactivarAction', 
                                                $e->getMessage(), 
                                                $strUserSession, 
                                                $strIpUserSession);
                }

            }
            else
            {
                $strMensaje .= 'No se ha obtenido la información de la tablet o el motivo de inactivación. Por favor notificar a Sistemas!';
            }
        }
        else
        {
            $strMensaje .= 'No se ha seleccionado la información de la tablet o el motivo de inactivación de manera correcta';
        }
        
        $objResponse->setContent( $strMensaje );
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_314-4977")
     * 
     * Documentación para el método 'reactivarAction'.
     *
     * Función que reactiva una tablet 
     *
     * @return JsonResponse $objResponse
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-12-2016
     */ 
    public function reactivarAction()
    {
        $objResponse        = new JsonResponse();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $datetimeActual     = new \DateTime('now');
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $strMensaje         = "";
        $serviceUtil        = $this->get('schema.Util');
        
        $intIdElementoTablet    = $objRequest->get('intIdElemento') ? $objRequest->get('intIdElemento') : 0;
        
        if($intIdElementoTablet)
        {
            $objElementoTablet  = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoTablet);
            
            if(is_object($objElementoTablet))
            {
                $emInfraestructura->beginTransaction();
                try
                {                    
                    $objDetalleMotivoAEliminar  = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 
                                                                                        'elementoId'    => $intIdElementoTablet,
                                                                                        'estado'        => 'Activo', 
                                                                                        'detalleNombre' => self::DETALLE_MOTIVO_INACTIVAR_TABLET) 
                                                                                );
                    if(is_object($objDetalleMotivoAEliminar))
                    {
                        $objDetalleMotivoAEliminar->setEstado('Eliminado');
                        $emInfraestructura->persist($objDetalleMotivoAEliminar);
                        $emInfraestructura->flush();
                    }
                    
                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objElementoTablet);
                    $objInfoHistorialElemento->setObservacion('Se reactiva la tablet');
                    $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                    $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                    $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElemento->setEstadoElemento('Activo');
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush();

                    $objElementoTablet->setEstado('Activo');
                    $emInfraestructura->persist($objElementoTablet);
                    
                    $emInfraestructura->flush();
                    $emInfraestructura->commit();
                    $strMensaje .= 'OK';
                }
                catch (\Exception $e)
                {
                    $strMensaje .= 'Ha ocurrido un problema al reactivar la tablet. Por favor notificar a Sistemas!';
                    if ($emInfraestructura->getConnection()->isTransactionActive())
                    {
                        $emInfraestructura->getConnection()->rollback();
                    }   
                    $emInfraestructura->getConnection()->close();
                    $serviceUtil->insertError(
                                                'Telcos+', 
                                                'InfoElementoTabletController->reactivarAction', 
                                                $e->getMessage(), 
                                                $strUserSession, 
                                                $strIpUserSession);
                }

            }
            else
            {
                $strMensaje .= 'No se ha obtenido la información de la tablet. Por favor notificar a Sistemas!';
            }
        }
        else
        {
            $strMensaje .= 'No se ha seleccionado la información de la tablet de manera correcta';
        }
        
        $objResponse->setContent( $strMensaje );
        return $objResponse;
    }
    
    /**
     * 
     * Documentación para el método 'getMotivosAction'.
     *
     * Función que obtiene los motivos asociados a la inactivación de una tablet
     *
     * @return JsonResponse $objResponse
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-12-2016
     */ 
    public function getMotivosAction()
    {
        $objResponse = new JsonResponse();
        $objRequest  = $this->get('request');
        $em          = $this->getDoctrine()->getManager('telconet');

        $strModulo    = $objRequest->get('strModulo');
        $strAccion    = $objRequest->get('strAccion');

        $arrayParametros    = array(
            "nombreModulo"  => $strModulo,
            "nombreAccion"  => $strAccion,
            "estados"       => array(
                                    "estadoActivo"    => "Activo",
                                    "estadoModificado"=> "Modificado"
                               )
        );

        $objJson    = $em->getRepository('schemaBundle:AdmiMotivo')->getJSONMotivosPorModuloYPorAccion( $arrayParametros );
        $objResponse->setContent($objJson);
        return $objResponse;
    }   
    
    
    /**
     * @Secure(roles="ROLE_314-2")
     * 
     * Documentación para el método 'newAction'.
     *
     * Muestra usado para mostrar el formulario vacío para crear una tablet.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-11-2015
     */
    public function newAction()
    {
        $objTablet = new InfoElemento();
        $form      = $this->createForm(new InfoElementoTabletType(), $objTablet);
        
        return $this->render( 'tecnicoBundle:InfoElementoTablet:new.html.twig', array('form'=> $form->createView()) );
    }
    
    
    /**
     * @Secure(roles="ROLE_314-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Guarda la información necesaria de una Tablet
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-12-2016 Se guarda la información del responsable de la tablet
     */
    public function createAction()
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial          = $this->getDoctrine()->getManager();
        $intIdEmpresaSession  = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strUserSession       = $objSession->get('user');
        $strIpUserSession     = $objRequest->getClientIp();
        $intIdPerResponsable  = $objRequest->get('intIdPerResponsable') ? $objRequest->get('intIdPerResponsable') : 0;
        $datetimeActual       = new \DateTime('now');
        
        $objTablet = new InfoElemento();
        $form      = $this->createForm(new InfoElementoTabletType(), $objTablet);
        
        $form->bind($objRequest);
        
        if( $form->isValid() )
        {
            $emInfraestructura->getConnection()->beginTransaction();
            
            try
            {
                /*
                 * Bloque que guarda la información del InfoElemento ingresado por el usuario
                 */
                $strNombreElemento       = $objTablet->getNombreElemento();
                $strModeloElemento       = $objTablet->getModeloElementoId()->getNombreModeloElemento();
                $strSerieFisica          = $objTablet->getSerieFisica();
                $strDescripcionElemento  = self::TIPO_ELEMENTO_TABLET.': '.$strNombreElemento;
                $strObservacionHistorial = '<b>Datos Nuevos<b><br>'; 
                $strObservacionHistorial .= 'Tipo: '.self::TIPO_ELEMENTO_TABLET.'<br>';
                $strObservacionHistorial .= 'Modelo: '.$strModeloElemento.'<br>';
                $strObservacionHistorial .= 'IMEI: '.$strNombreElemento.'<br>';
                $strObservacionHistorial .= 'Serie Fisica: '.$strSerieFisica.'<br>';
                
                $objTablet->setDescripcionElemento($strDescripcionElemento);
                $objTablet->setEstado(self::ESTADO_ACTIVO);
                $objTablet->setFeCreacion($datetimeActual);
                $objTablet->setUsrCreacion($strUserSession);
                $objTablet->setIpCreacion($strIpUserSession);
                
                $emInfraestructura->persist($objTablet);
                $emInfraestructura->flush();
                /*
                 * Fin del Bloque que guarda la información del InfoElemento ingresado por el usuario
                 */
                
                
                /*
                 * Bloque que guarda la relación del InfoElemento con la empresa del usuario en session
                 */
                $objInfoEmpresaElemento = new InfoEmpresaElemento();
                $objInfoEmpresaElemento->setFeCreacion($datetimeActual);
                $objInfoEmpresaElemento->setUsrCreacion($strUserSession);
                $objInfoEmpresaElemento->setIpCreacion($strIpUserSession);
                $objInfoEmpresaElemento->setEstado(self::ESTADO_ACTIVO);
                $objInfoEmpresaElemento->setElementoId($objTablet);
                $objInfoEmpresaElemento->setEmpresaCod($intIdEmpresaSession);
                $objInfoEmpresaElemento->setObservacion($strDescripcionElemento);

                $emInfraestructura->persist($objInfoEmpresaElemento);
                $emInfraestructura->flush();
                /*
                 * Fin del Bloque que guarda la relación del InfoElemento con la empresa del usuario en session
                 */
                
                
                /*
                 * Bloque que guarda la relación del InfoElemento con la persona responsable
                 */
                
                if($intIdPerResponsable)
                {
                    $strResponsable = "";
                    $objPerResponsable = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerResponsable);
                    if(is_object($objPerResponsable))
                    {
                        $objPersonaResponsable = $objPerResponsable->getPersonaId();
                        if(is_object($objPersonaResponsable))
                        {
                            $strResponsable = sprintf('%s', $objPersonaResponsable);
                        }
                    }
                    $strObservacionHistorial .= 'Responsable: '.$strResponsable.'<br>';

                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setElementoId($objTablet->getId());
                    $objInfoDetalleElemento->setDetalleNombre(self::DETALLE_RESPONSABLE_TABLET);
                    $objInfoDetalleElemento->setDetalleValor($intIdPerResponsable);
                    $objInfoDetalleElemento->setDetalleDescripcion(self::DETALLE_RESPONSABLE_TABLET);
                    $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                    $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                    $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                    $objInfoDetalleElemento->setEstado('Activo');
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                }
                
                /*
                 * Fin del Bloque que guarda la relación del InfoElemento con la persona responsable
                 */

                
                /*
                 * Bloque que guarda el historial del InfoElemento
                 */
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objTablet);
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
                
                $emInfraestructura->getConnection()->commit();
                
                return $this->redirect($this->generateUrl('elementotablet_show', array('id' => $objTablet->getId())));
            }
            catch (\Exception $e)
            {
                error_log($e->getMessage());

                $emInfraestructura->getConnection()->rollback();
            }//try
            
            $emInfraestructura->getConnection()->close();
        }//( $form->isValid() )
        
        return $this->render( 'tecnicoBundle:InfoElementoTablet:new.html.twig', array('form'=> $form->createView()) );
    }
    
    
    /**
     * @Secure(roles="ROLE_314-6")
     * 
     * Documentación para el método 'showAction'.
     *
     * Muestra la información de una Tablet.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-12-2016 Se obtiene la información de la persona responsable de la tablet
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-01-2017 Se obtiene la información de la región, cantón y departamento al que pertenece la persona responsable de la tablet
     */
    public function showAction($id)
    {
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $objTablet = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objTablet )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }

        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')->findOneByElementoId($objTablet);
        $strNombreEmpresa = '';

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
        
        $strResponsable                         = "";
        $strDepartamentoResponsableTablet       = "";
        $strRegionResponsableTablet             = "";
        $strCantonResponsableTablet             = "";
                            
        $objDetalleResponsable  = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array( 
                                                                        'elementoId'    => $id, 
                                                                        'estado'        => self::ESTADO_ACTIVO,
                                                                        'detalleNombre' => self::DETALLE_RESPONSABLE_TABLET
                                                                     ) 
                                                               );
        
        if(is_object($objDetalleResponsable))
        {
            $intIdPerResponsable = $objDetalleResponsable->getDetalleValor();
            if($intIdPerResponsable)
            {
                $objPerResponsable = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerResponsable);
                if(is_object($objPerResponsable))
                {
                    $objPersonaResponsable = $objPerResponsable->getPersonaId();
                    if(is_object($objPersonaResponsable))
                    {
                        $strResponsable = sprintf('%s', $objPersonaResponsable);
                    }
                    
                    $intIdDepartamentoPerResponsable    = $objPerResponsable->getDepartamentoId();
                    if($intIdDepartamentoPerResponsable)
                    {
                        $objDepartamentoPerResponsable = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                                     ->find($intIdDepartamentoPerResponsable);
                        if(is_object($objDepartamentoPerResponsable))
                        {
                            $strDepartamentoResponsableTablet  = sprintf('%s', $objDepartamentoPerResponsable);
                        }
                    }
                    
                    
                    $intIdOficinaPerResponsable         = $objPerResponsable->getOficinaId();
                    if($intIdOficinaPerResponsable)
                    {
                        $objOficinaPerResponsable   = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                                  ->find($intIdOficinaPerResponsable);
                        if(is_object($objOficinaPerResponsable))
                        {
                            $intIdCantonPerResponsable  = $objOficinaPerResponsable->getCantonId();
                            if($intIdCantonPerResponsable)
                            {
                                $objCantonPerResponsable   = $emComercial->getRepository('schemaBundle:AdmiCanton')
                                                                         ->find($intIdCantonPerResponsable);
                                if(is_object($objCantonPerResponsable))
                                {
                                    $strRegionResponsableTablet = $objCantonPerResponsable->getRegion();
                                    $strCantonResponsableTablet = sprintf('%s', $objCantonPerResponsable);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $this->render(   'tecnicoBundle:InfoElementoTablet:show.html.twig', 
                                array(  "tablet"                                    => $objTablet, 
                                        "empresa"                                   => $strNombreEmpresa,
                                        "strResponsable"                            => $strResponsable,
                                        "strDepartamentoResponsableTablet"          => $strDepartamentoResponsableTablet,
                                        "strRegionResponsableTablet"                => $strRegionResponsableTablet,
                                        "strCantonResponsableTablet"                => $strCantonResponsableTablet
                                    ));
    }
    
    
    /**
     * @Secure(roles="ROLE_314-4")
     * 
     * Documentación para el método 'editAction'.
     *
     * Muestra la información de una Tablet a la cual se le va a actualizar la información.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-11-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-12-2016 Se obtiene la información del responsable de la tablet
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-01-2017 Se obtiene la información de la región, cantón y departamento al que pertenece la persona responsable de la tablet
     */
    public function editAction($id)
    {
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial        = $this->getDoctrine()->getManager();
        
        $objTablet = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objTablet )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalleResponsableActual    = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy(array( 
                                                                                'elementoId'    => $id, 
                                                                                'estado'        => self::ESTADO_ACTIVO,
                                                                                'detalleNombre' => self::DETALLE_RESPONSABLE_TABLET
                                                                             ) 
                                                                       );
        $intIdPerResponsable                    = 0;
        $strResponsable                         = "";
        $strDepartamentoResponsableTablet       = "";
        $strRegionResponsableTablet             = "";
        $strCantonResponsableTablet             = "";
        if(is_object($objDetalleResponsableActual))
        {
            $intIdPerResponsable = $objDetalleResponsableActual->getDetalleValor();
            if($intIdPerResponsable)
            {
                $objPerResponsable = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerResponsable);
                
                if(is_object($objPerResponsable))
                {
                    $objPersonaResponsable = $objPerResponsable->getPersonaId();
                    if(is_object($objPersonaResponsable))
                    {
                        $strResponsable = sprintf('%s', $objPersonaResponsable);
                    }
                    
                    $intIdDepartamentoPerResponsable    = $objPerResponsable->getDepartamentoId();
                    if($intIdDepartamentoPerResponsable)
                    {
                        $objDepartamentoPerResponsable  = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                                      ->find($intIdDepartamentoPerResponsable);
                        if(is_object($objDepartamentoPerResponsable))
                        {
                            $strDepartamentoResponsableTablet  = sprintf('%s', $objDepartamentoPerResponsable);
                        }
                    }

                    $intIdOficinaPerResponsable         = $objPerResponsable->getOficinaId();
                    if($intIdOficinaPerResponsable)
                    {
                        $objOficinaPerResponsable   = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                                  ->find($intIdOficinaPerResponsable);
                        if(is_object($objOficinaPerResponsable))
                        {
                            $intIdCantonPerResponsable  = $objOficinaPerResponsable->getCantonId();
                            if($intIdCantonPerResponsable)
                            {
                                $objCantonPerResponsable   = $emComercial->getRepository('schemaBundle:AdmiCanton')
                                                                         ->find($intIdCantonPerResponsable);
                                if(is_object($objCantonPerResponsable))
                                {
                                    $strRegionResponsableTablet = $objCantonPerResponsable->getRegion();
                                    $strCantonResponsableTablet = sprintf('%s', $objCantonPerResponsable);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        
        $form = $this->createForm(new InfoElementoTabletType(), $objTablet);
        return $this->render('tecnicoBundle:InfoElementoTablet:edit.html.twig', 
                             array( 'form'                                      => $form->createView(), 
                                    'tablet'                                    => $objTablet,
                                    'intIdPerResponsable'                       => $intIdPerResponsable,
                                    'strResponsable'                            => $strResponsable,
                                    "strDepartamentoResponsableTablet"          => $strDepartamentoResponsableTablet,
                                    "strRegionResponsableTablet"                => $strRegionResponsableTablet,
                                    "strCantonResponsableTablet"                => $strCantonResponsableTablet
                                 ));
    }
    
    
    /**
     * @Secure(roles="ROLE_314-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información de una Tablet.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-11-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-12-2016 Se procedió a cambiar la lógica de los redirect. Además se guarda la información del responsable de la tablet.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 10-04-2018 Se agrega la funcionaliddad de notificar a Hal cuando a la misma persona se le cambia una tablet nueva
     *
     */
    public function updateAction($id)
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial       = $this->getDoctrine()->getManager();
        $strUserSession    = $objSession->get('user');
        $strIpUserSession  = $objRequest->getClientIp();
        $datetimeActual    = new \DateTime('now');
        $serviceSoporte    = $this->get('soporte.SoporteService');

        $objTablet = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);

        if( !$objTablet )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        $intIdPerResponsable     = $objRequest->get('intIdPerResponsable') ? $objRequest->get('intIdPerResponsable') : 0;
        if($intIdPerResponsable)
        {
            $objPerResponsable = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerResponsable);
            if(is_object($objPerResponsable))
            {
                $boolCrearDetalleResponsable    = false;
                $strResponsableActual           = "";
                $objDetalleResponsableActual    = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy(array( 
                                                                                        'elementoId'    => $id, 
                                                                                        'estado'        => self::ESTADO_ACTIVO,
                                                                                        'detalleNombre' => self::DETALLE_RESPONSABLE_TABLET
                                                                                     ) 
                                                                               );
                
                if(is_object($objDetalleResponsableActual))
                {
                    $intIdPerResponsableActual = $objDetalleResponsableActual->getDetalleValor();
                    if($intIdPerResponsableActual)
                    {
                        $objPerResponsableActual = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->find($intIdPerResponsableActual);
                        if(is_object($objPerResponsableActual))
                        {
                            $objPersonaResponsableActual = $objPerResponsableActual->getPersonaId();
                            if(is_object($objPersonaResponsableActual))
                            {
                                $strResponsableActual = sprintf('%s', $objPersonaResponsableActual);
                            }
                            
                        }
                    }
                }
                else
                {
                    $boolCrearDetalleResponsable = true;
                }

                $strNombreElementoActual = $objTablet->getNombreElemento();
                $strModeloElementoActual = $objTablet->getModeloElementoId()->getNombreModeloElemento();
                $strSerieFisicaActual    = $objTablet->getSerieFisica();
                $strTipoElementoActual   = $objTablet->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                $strDatosAntiguos        = '<b>Datos Anteriores<b><br/>';
                $strDatosAntiguos        .= 'Tipo: '.$strTipoElementoActual.'<br/>';
                $strDatosAntiguos        .= 'Modelo: '.$strModeloElementoActual.'<br>';
                $strDatosAntiguos        .= 'IMEI: '.$strNombreElementoActual.'<br>';
                $strDatosAntiguos        .= 'Serie Fisica: '.$strSerieFisicaActual.'<br>';
                $strDatosAntiguos        .= 'Responsable: '.$strResponsableActual.'<br>';
                $form = $this->createForm(new InfoElementoTabletType(), $objTablet);

                $form->bind($objRequest);

                if( $form->isValid() )
                {
                    $emInfraestructura->getConnection()->beginTransaction();

                    try
                    {
                        /*
                         * Bloque que actualiza la información del InfoElemento seleccionado por el usuario
                         */
                        $strNombreElementoNuevo       = $objTablet->getNombreElemento();
                        $strModeloElementoNuevo       = $objTablet->getModeloElementoId()->getNombreModeloElemento();
                        $strSerieFisicaNuevo          = $objTablet->getSerieFisica();
                        $strDescripcionElementoNuevo  = self::TIPO_ELEMENTO_TABLET.': '.$strNombreElementoNuevo;
                        $strDatosNuevos               = '<b>Datos Nuevos<b><br>'; 
                        $strDatosNuevos               .= 'Tipo: '.self::TIPO_ELEMENTO_TABLET.'<br>';
                        $strDatosNuevos               .= 'Modelo: '.$strModeloElementoNuevo.'<br>';
                        $strDatosNuevos               .= 'IMEI: '.$strNombreElementoNuevo.'<br>';
                        $strDatosNuevos               .= 'Serie Fisica: '.$strSerieFisicaNuevo.'<br>';
                        /*
                         * Fin del Bloque que actualiza la información del InfoElemento seleccionado por el usuario
                         */
                        
                        /*
                         * Bloque que actualiza el responsable asociado a una tablet
                         */
                        $strResponsableNuevo        = "";
                        $objPersonaResponsableNuevo = $objPerResponsable->getPersonaId();
                        if(is_object($objPersonaResponsableNuevo))
                        {
                            $strResponsableNuevo = sprintf('%s', $objPersonaResponsableNuevo);
                        }
                        $strDatosNuevos               .= 'Responsable: '.$strResponsableNuevo.'<br>';
                        
                        $objTablet->setDescripcionElemento($strDescripcionElementoNuevo);
                        $emInfraestructura->persist($objTablet);
                        $emInfraestructura->flush();
                        
                        if($boolCrearDetalleResponsable)
                        {
                            $objInfoDetalleElemento = new InfoDetalleElemento();
                            $objInfoDetalleElemento->setElementoId($id);
                            $objInfoDetalleElemento->setDetalleNombre(self::DETALLE_RESPONSABLE_TABLET);
                            $objInfoDetalleElemento->setDetalleValor($intIdPerResponsable);
                            $objInfoDetalleElemento->setDetalleDescripcion(self::DETALLE_RESPONSABLE_TABLET);
                            $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                            $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                            $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                            $objInfoDetalleElemento->setEstado('Activo');
                            $emInfraestructura->persist($objInfoDetalleElemento);
                            $emInfraestructura->flush();
                        }
                        else
                        {
                            $objDetalleResponsableActual->setDetalleValor($intIdPerResponsable);
                            $emInfraestructura->persist($objDetalleResponsableActual);
                            $emInfraestructura->flush();
                        }
                        /*
                         * Fin de Bloque que actualiza el responsable asociado a una tablet
                         */


                        /*
                         * Bloque que guarda el historial del InfoElemento
                         */
                        $strObservacionHistorial = $strDatosAntiguos.$strDatosNuevos;
                        $objInfoHistorialElemento = new InfoHistorialElemento();
                        $objInfoHistorialElemento->setElementoId($objTablet);
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

                        $emInfraestructura->getConnection()->commit();


                        /*===============================================================================
                        =========================== INICIO NOTIFICACIONES HAL ===========================
                        ================================================================================*/

                        if (($strNombreElementoActual != $strNombreElementoNuevo) &&
                            ($intIdPerResponsableActual === $intIdPerResponsable))
                        {
                            $serviceSoporte->notificacionesHal(
                                array ('strModulo' => 'cambiotabletcuadrilla',
                                       'strUser'   =>  $strUserSession,
                                       'strIp'     =>  $strIpUserSession,
                                       'arrayJson' => array ('idPersona'          => $objPerResponsable->getPersonaId()->getId(),
                                                             'imeiTabletAnterior' => $strNombreElementoActual,
                                                             'imeiTabletNueva'    => $strNombreElementoNuevo)));
                        }

                        /*============================================================================
                        =========================== FIN NOTIFICACIONES HAL ===========================
                        =============================================================================*/


                        return $this->redirect($this->generateUrl('elementotablet_show', array('id' => $objTablet->getId())));
                    }
                    catch (\Exception $e)
                    {
                        error_log($e->getMessage());

                        $emInfraestructura->getConnection()->rollback();
                    }//try

                    $emInfraestructura->getConnection()->close();
                }//( $form->isValid() )
                else
                {
                    $this->get('session')->getFlashBag()->add('notice', 'El formulario no es válido');
                    return $this->redirect($this->generateUrl('elementotablet_edit', array('id' => $objTablet->getId())));
                }
            }
            else
            {
                $this->get('session')->getFlashBag()->add('notice', 'No se ha encontrado un responsable de la Tablet');
                return $this->redirect($this->generateUrl('elementotablet_edit', array('id' => $objTablet->getId())));
            }
        }
        else
        {
            $this->get('session')->getFlashBag()->add('notice', 'No ha seleccionado un responsable de la Tablet');
            return $this->redirect($this->generateUrl('elementotablet_edit', array('id' => $objTablet->getId())));
        }
        
        return $this->redirect($this->generateUrl('elementotablet_show', array('id' => $id)));            
    }
    
    
    /**
     * @Secure(roles="ROLE_314-8")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     *
     * Elimina la información de una Tablet.
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-11-2015
     */
    public function deleteAjaxAction()
    {
        $response           = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTablet          = $objRequest->request->get('tablet') ? $objRequest->request->get('tablet') : '';
        $boolError          = false;
        $strMensaje         = 'No se encontró tablet en estado activo';
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $datetimeActual     = new \DateTime('now');
        $arrayTablets       = array();
        
        if( $strTablet )
        {
            $arrayTablets = explode('|', $strTablet);
        }
            
        
        $emInfraestructura->getConnection()->beginTransaction();	
        
        try
        {
            foreach( $arrayTablets as $intIdTablet )
            {
                $objTablet = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($intIdTablet);

                if( !$objTablet )
                {
                    $boolError = true;
                }

                if( !$boolError )
                {
                    $objInfoEmpresaElemento = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')->findOneByElementoId($objTablet);
                    
                    /*
                     * Bloque que actualiza la información del InfoElemento seleccionado por el usuario
                     */
                    $objTablet->setEstado(self::ESTADO_ELIMINADO);
                    $emInfraestructura->persist($objTablet);
                    $emInfraestructura->flush();
                    /*
                     * Fin del Bloque que actualiza la información del InfoElemento seleccionado por el usuario
                     */
                    
                    
                    /*
                     * Bloque que actualiza la relación del InfoElemento con la empresa seleccionada por el usuario
                     */
                    if( $objInfoEmpresaElemento )
                    {
                        $objInfoEmpresaElemento->setEstado(self::ESTADO_ELIMINADO);
                        $emInfraestructura->persist($objInfoEmpresaElemento);
                        $emInfraestructura->flush();
                    }//( !$objInfoEmpresaElemento )
                    /*
                     * Fin del Bloque que actualiza la relación del InfoElemento con la empresa seleccionada por el usuario
                     */
                    
                    
                    /*
                     * Bloque que guarda el historial del InfoElemento
                     */
                    $strObservacionHistorial = 'Se elimina el elemento';

                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objTablet);
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
                }
            }
            
            $emInfraestructura->getConnection()->commit();
            
            $strMensaje = 'OK';
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            
            $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';

            $emInfraestructura->getConnection()->rollback();
        }//try
        
        $emInfraestructura->getConnection()->close();
        
        $response->setContent( $strMensaje );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'verificarImeiAction'.
     *
     * Retorna un string con 'OK' si no existe el imei en base de datos
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-11-2015
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 28-09-2020
     * Se agrega campo Publish ID para la identificación única por dispositivo tablet 
     * ingresado.  
     * Se agregan validaciones.
     */
    public function verificarImeiAction()
    {
        $response            = new Response();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdEmpresaSession = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strImei             = $objRequest->request->get('imei') ? $objRequest->request->get('imei') : '';
        $strSerieLogica      = $objRequest->request->get('serieLogica') ? $objRequest->request->get('serieLogica') : '';
        $strAccion           = $objRequest->request->get('accion') ? $objRequest->request->get('accion') : '';
        $intIdTablet         = $objRequest->request->get('idTablet') ? $objRequest->request->get('idTablet') : 0;
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');
        $strMensaje          = 'OK';
        $strMensajeImei      = '';
        $arrayTiposElemento  = array( self::TIPO_ELEMENTO_TABLET );
        $strBooleanValidator = false;
        if(empty($strImei))
        {
            $strMensajeImei = "El Imei es un campo requerido";
            $response->setContent( $strMensajeImei );
            $strBooleanValidator = true;
        }
        if( empty($strSerieLogica))
        {
            $strMensajePublish = "El Publish ID es un campo requerido. </br>";
            $response->setContent( $strMensajePublish );
            $strBooleanValidator = true;
        }
        if(strlen($strImei) <= 14)
        {
            $strMsgImei = "El Imei debe tener un mínimo de 15 caracteres. </br>";
            $response->setContent( $strMsgImei );
            $strBooleanValidator = true;
        }
        if(strlen($strSerieLogica) <= 35)
        {
            $strMsgPublisId = "El Publish ID debe tener un mínimo de 36 caracteres. </br>";
            $response->setContent( $strMsgPublisId);
            $strBooleanValidator = true;
        }
        if($strBooleanValidator)
        {
            return $response;
        }
        $arrayParametros = array(
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => self::TIPO_ELEMENTO_TABLET,
                                    'criterios'            => array( 'nombre'   => $strImei, 'tipoElemento' => $arrayTiposElemento,
                                                                     'serieLogica' => $strSerieLogica )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);
       
        if( $arrayResultados['total'] > 0 && ($strAccion == 'guardar' ))
        {
            foreach( $arrayResultados['encontrados'] as $arrayTablet )
            {
                $intIdElementoEncontrado = ( isset($arrayTablet['strNombreElemento']) ) ? $arrayTablet['strNombreElemento'] : 0;
                $intSerieLogicaEncontrado  = ( isset($arrayTablet['strSerieLogica']) ) ? $arrayTablet['strSerieLogica'] : 0;
    
                if( $intIdElementoEncontrado == $strImei )
                {
                    $strMensaje = 'El número de IMEI ingresado ya existe, por favor ingresar un número de imei diferente';
                }
                else if ($intSerieLogicaEncontrado == $strSerieLogica)
                {
                    $strMensaje = 'El número de Publish Id ingresado ya existe, por favor ingresar un identificador diferente';
                }
            }
        }
        else if ( $arrayResultados['total'] >= 1 && $strAccion == 'editar' )
        {
            foreach( $arrayResultados['encontrados'] as $arrayTablet )
            {
                $intIdElementoEncontrado = ( isset($arrayTablet['intIdElemento']) ) ? $arrayTablet['intIdElemento'] : 0;

                if( $intIdElementoEncontrado != $intIdTablet )
                {
                    $intIdElementoEncontrado = ( isset($arrayTablet['strNombreElemento']) ) ? $arrayTablet['strNombreElemento'] : 0;
                    $intSerieLogicaEncontrado  = ( isset($arrayTablet['strSerieLogica']) ) ? $arrayTablet['strSerieLogica'] : 0;
                    
                    if( $intIdElementoEncontrado == $strImei )
                    {
                        $strMensaje = 'El número de IMEI ingresado ya existe, por favor ingresar un número de imei diferente';
                    }
                    else if ($intSerieLogicaEncontrado == $strSerieLogica)
                    {
                        $strMensaje = 'El número de Publish Id ingresado ya existe, por favor ingresar un identificador diferente';
                    }
                }//( $intIdElementoEncontrado != $intIdMedioTransporte )
            }
        }
        $response->setContent( $strMensaje );
        
        return $response;
    }

    /**
     * Documentación para el método 'verificarElementosAEliminarAction'.
     *
     * Verifica que las tablets a eliminar no esten asignadas a un empleado.
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 19-11-2015
     */
    public function verificarElementosAEliminarAction()
    {
        $response           = new Response();
        $objRequest         = $this->get('request');
        $strMensaje         = 'No se pueden eliminar los elementos seleccionados<br/>';
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTablet          = $objRequest->request->get('tablet') ? $objRequest->request->get('tablet') : '';
        $intContadorError   = 0;
        
        $arrayTablets = array();
        if( $strTablet )
        {
            $arrayTablets = explode('|', $strTablet);
        }	
        
        try
        {
            foreach( $arrayTablets as $intIdTablet )
            {
                $objTablet = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->findOneBy( array( 'id' => $intIdTablet, 'estado' => self::ESTADO_ACTIVO ) );
                
                if( !$objTablet )
                {
                    $intContadorError++;
                    $strMensaje = '<b>'.$intIdTablet.'</b>: No se encontró tablet en estado activo<br/>';
                }
                else
                {
                    $objDetalles = $emInfraestructura->getRepository( 'schemaBundle:InfoDetalleElemento')
                                                     ->findBy( array( 
                                                                        'elementoId'    => $intIdTablet, 
                                                                        'estado'        => self::ESTADO_ACTIVO,
                                                                        'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_LIDER
                                                                     ) 
                                                             );
                    
                    if( $objDetalles )
                    {
                        $intContadorError++;
                        $strMensaje .= '<b>'.$objTablet->getNombreElemento().':</b> Asignado a un Líder de Cuadrilla<br/>';
                    }//( $objDetalles )
                }//( !$objTablet )
            }//foreach( $arrayTablets as $intIdTablet )
            
            if( $intContadorError == 0 )
            {
                $strMensaje = 'OK';
            }
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
        }//try
        
        $response->setContent( $strMensaje );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'getModelosTabletAction'.
     *
     * Función usada para retornar los modelos ingresados al tipo de tablet seleccionado por el usuario
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 20-11-2015
     */
    public function getModelosTabletAction()
    {
        $response              = new JsonResponse();
        $emInfraestructura     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest            = $this->get('request');
        $strTipoElemento       = $objRequest->request->get('tipoElemento') ? $objRequest->request->get('tipoElemento') 
                                 : self::TIPO_ELEMENTO_TABLET;
        $intTotal              = 0;
        $arrayModelosElementos = array();
        $arrayTmpParametros    = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array($strTipoElemento) );
        $arrayTmpResultados    = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                   ->getModeloElementosByCriterios( $arrayTmpParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayTmpModelosElementos = $arrayTmpResultados['registros'];
            
            foreach( $arrayTmpModelosElementos as $objModeloElemento )
            {
                $item                      = array();
                $item['strIdentificacion'] = $objModeloElemento->getId();
                $item['strDescripcion']    = ucwords(strtolower($objModeloElemento->getNombreModeloElemento()));
                
                $arrayModelosElementos[] = $item;

                $intTotal++;
            }//foreach($arrayResultados as $arrayTipoMedioTransporte)
        }//($arrayResultados)
        
        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayModelosElementos) );
        
        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_314-3457")
     * 
     * Documentación para el método 'gridMonitoreoAction'.
     *
     * Muestra el listado de todas las tablets creados para monitorear.
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 02-12-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 28-12-2016 Se obtiene los datos de región, departamento y ciudad desde la sesión del usuario para cargar la búsqueda de
     *                         las tablets monitoreadas de acuerdo a estos parámetros por defecto
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-01-2017 Se consulta si se debe aplicar o no el filtro por horario de manera automática de acuerdo al departamento de la 
     *                         persona en sesión y se eliminan arreglo de roles que no se está utilizando
     */
    public function gridMonitoreoAction()
    {
        $objRequest                     = $this->get('request');
        $objSession                     = $objRequest->getSession();
        
        $emGeneral                      = $this->getDoctrine()->getManager('telconet_general');
        $emComercial                    = $this->getDoctrine()->getManager();
        
        $strCodEmpresa                  = $objSession->get('idEmpresa');
        $strPrefijoEmpresa              = $objSession->get('prefijoEmpresa');
        
        $intIdDepartamentoUsrSession    = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $intIdOficinaUsrSession         = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        
        $strRegionUsrSession            = '';
        $strCiudadUsrSession            = '';
        $strDepartamentoUsrSession      = $objSession->get('departamento') ? $objSession->get('departamento') : 0;
        if($intIdOficinaUsrSession)
        {
            $objOficinaUsrSession       = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaUsrSession);
            if(is_object($objOficinaUsrSession))
            {
                $intIdCantonUsrSession  = $objOficinaUsrSession->getCantonId();
                if($intIdCantonUsrSession)
                {
                    $objCantonUsrSession      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($intIdCantonUsrSession);
                    if(is_object($objCantonUsrSession))
                    {
                        $strRegionUsrSession    = $objCantonUsrSession->getRegion();
                        $strCiudadUsrSession    = $objCantonUsrSession->getNombreCanton();        
                    }
                }
                
            }
        }
        
        $strNombreDepartamentoBase              = "";
        $strFiltrarPorHorarioPorDepartamento    = "";
        if($intIdDepartamentoUsrSession)
        {
            $objDepartamentoSession = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($intIdDepartamentoUsrSession);
            if(is_object($objDepartamentoSession))
            {
                $strNombreDepartamentoBase                      = $objDepartamentoSession->getNombreDepartamento();
                
                $arrayRegistroDepartamentosFiltrarPorHorario    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne(   self::DEPARTAMENTO_FILTRO_POR_HORARIO,
                                                                                        '', 
                                                                                        '', 
                                                                                        '', 
                                                                                        $strNombreDepartamentoBase, 
                                                                                        '', 
                                                                                        '', 
                                                                                        '', 
                                                                                        '', 
                                                                                        $strCodEmpresa);

                if($arrayRegistroDepartamentosFiltrarPorHorario)
                {
                    $strFiltrarPorHorarioPorDepartamento = 'SI';
                }
            }
        }
                  
        return $this->render(   'tecnicoBundle:InfoElementoTablet:monitoreo.html.twig', 
                                array(
                                        'strRegionUsrSession'           => $strRegionUsrSession,
                                        'intIdCantonUsrSession'         => $intIdCantonUsrSession,
                                        'strCiudadUsrSession'           => $strCiudadUsrSession,
                                        'intIdDepartamentoUsrSession'   => $intIdDepartamentoUsrSession,
                                        'strDepartamentoUsrSession'     => $strDepartamentoUsrSession,
                                        'intIdEmpresaSession'           => $strCodEmpresa,
                                        'strPrefijoEmpresa'             => $strPrefijoEmpresa,
                                        'intValorInicial'               => self::VALOR_INICIAL_BUSQUEDA,
                                        'intValorLimite'                => self::VALOR_LIMITE_BUSQUEDA,
                                        'strFiltrarPorHorarioUsrSession'=> $strFiltrarPorHorarioPorDepartamento
                                ));
    }
    
    /**
     * Documentación para el método 'buscarTabletsAction'.
     *
     * Muestra el listado de todas las tablets para monitorear.
     *
     * @return JsonResponse 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 23-12-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 28-12-2016 Se agregan los filtros por región, ciudad y  departamento del usuario en sesión y se obtiene como parámetro
     *                         la cantidad de minutos que será utilizada para la comparación de fechas y horas en el monitoreo de tablets
     * 
     * @version 1.2 08-01-2016 Se envía como parámetro adicional strCuadrillaEstaLibre que verifica si una cuadrilla está o no libre
     * 
     * @version 1.3 09-01-2016 Se agrega el respectivo estado en el que se encuentra la tablet cuando se consulta por el reporte de Mantenimiento
     *                         y Libres, tal manera que no se traslape una tablet que pudiera estar en mantenimiento y libre al mismo tiempo, 
     *                         dándole prioridad a la tablet en mantenimiento para el conteo del monitoreo de tablets
     */
    public function buscarTabletsAction()
    {
        $emInfraestructura                      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral                              = $this->getDoctrine()->getManager('telconet_general');
        $objResponse                            = new JsonResponse();
        $objRequest                             = $this->getRequest();
        $objSession                             = $objRequest->getSession();
        $intIdPerSession                        = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intStart                               = $objRequest->get('start') ? $objRequest->request->get('start') : self::VALOR_INICIAL_BUSQUEDA;
        $intLength                              = $objRequest->get('length') ? $objRequest->request->get('length') : self::VALOR_LIMITE_BUSQUEDA;
        
        $strImeiTablet                          = $objRequest->get('strImeiTablet') ? $objRequest->get('strImeiTablet') : "";
        $strSerieLogicaTablet                   = $objRequest->get('strSerieLogicaTablet') ? $objRequest->get('strSerieLogicaTablet') : "";
        $strResponsableTablet                   = $objRequest->get('strResponsableTablet') ? $objRequest->get('strResponsableTablet') : "";
        $strDepartamentoPer                     = $objRequest->get('strDepartamentoPer') ? $objRequest->get('strDepartamentoPer') : "";
        $strCuadrillaPer                        = $objRequest->get('strCuadrillaPer') ? $objRequest->get('strCuadrillaPer') : "";
        
        $arrayResultadoEstadosMonitoreo         = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getResultadoDetallesParametro(self::ESTADOS_MONITOREO_TABLET,'','');
        
        $strTipoReporte                         = $objRequest->get('strTipoReporte') ? $objRequest->get('strTipoReporte') : "";
        
        $strDraw                                = $objRequest->request->get('draw') ? $objRequest->request->get('draw') : "1";
        
        /*Criterios de Búsqueda Avanzada*/          
        $strRegionPerBusqAvanzada               = $objRequest->get('strRegionPerBusqAvanzada') 
                                                  ? trim($objRequest->get('strRegionPerBusqAvanzada')) : "";

        $intIdCantonPerBusqAvanzada             = $objRequest->get('intIdCantonPerBusqAvanzada') 
                                                  ? $objRequest->get('intIdCantonPerBusqAvanzada') : 0;

        $intIdDepartamentoPerBusqAvanzada       = $objRequest->get('intIdDepartamentoPerBusqAvanzada') 
                                                  ? $objRequest->get('intIdDepartamentoPerBusqAvanzada') : 0;
            
        $intIdDepartamentoCuadrillaBusqAvanzada = $objRequest->get('intIdDepartamentoCuadrillaBusqAvanzada') 
                                                  ? $objRequest->get('intIdDepartamentoCuadrillaBusqAvanzada') : 0;
        $intIdZonaCuadrillaBusqAvanzada         = $objRequest->get('intIdZonaCuadrillaBusqAvanzada') 
                                                  ? $objRequest->get('intIdZonaCuadrillaBusqAvanzada') : 0;
        $intIdModeloBusqAvanzada                = $objRequest->get('intIdModeloBusqAvanzada') 
                                                  ? $objRequest->get('intIdModeloBusqAvanzada') : 0;
        $strEstadoMonitoreoBusqAvanzada         = $objRequest->get('strEstadoMonitoreoBusqAvanzada') 
                                                  ? $objRequest->get('strEstadoMonitoreoBusqAvanzada') : "";
        $strFiltrarMisCuadrillasBusqAvanzada    = $objRequest->get('strFiltrarMisCuadrillasBusqAvanzada') 
                                                  ? $objRequest->get('strFiltrarMisCuadrillasBusqAvanzada') : "NO";
        $strFiltrarPorHorarioBusqAvanzada       = $objRequest->get('strFiltrarPorHorarioBusqAvanzada') 
                                                  ? $objRequest->get('strFiltrarPorHorarioBusqAvanzada') : "NO";
        

        $strCodEmpresaSession                   = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $strMinutosMonitoreo                    = "";

        $arrayRegistroLimiteMinutosMonitoreo    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne(   self::LIMITE_MINUTOS_MONITOREO,
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        $strCodEmpresaSession);

        if($arrayRegistroLimiteMinutosMonitoreo)
        {
            $strMinutosMonitoreo = $arrayRegistroLimiteMinutosMonitoreo['valor1'];
        }

        $arrayEstadosTablet = array();
        if($strTipoReporte == self::ESTADO_MANTENIMIENTO_LIBRE)
        {
            $arrayEstadosTablet[] = 'Inactivo';
        }
        else if($strTipoReporte == self::ESTADO_EN_CAMPO)
        {
            $arrayEstadosTablet[] = 'Activo';
        }
        else if($strTipoReporte == self::ESTADO_MANTENIMIENTO_LIBRE)
        {
            $arrayEstadosTablet[] = 'Inactivo';
        }
        else if($strTipoReporte == self::ESTADO_CUADRILLAS_LIBRES)
        {
            $arrayEstadosTablet[] = 'Activo';
        }
        
        $arrayParametros = array( 
                                'strMinutosMonitoreo'                   => $strMinutosMonitoreo,
                                'draw'                                  => $strDraw,    
                                'strEstadoActivo'                       => 'Activo',
                                'strEstadoPrestadoCuadrilla'            => 'Prestado',
                                'strEstadoEsPrestamoCuadrilla'          => 'Es_Prestamo',
                                'strCuadrillaEstaLibre'                 => 'SI',
                                'strNombreTipoElemento'                 => self::TIPO_ELEMENTO_TABLET,
                                'strDetalleResponsable'                 => self::DETALLE_RESPONSABLE_TABLET,
                                'strDetalleMotivo'                      => self::DETALLE_MOTIVO_INACTIVAR_TABLET,
                                'arrayValuesMotivos'                    => array('MANTENIMIENTO'),
                                'strTipoReporte'                        => $strTipoReporte,
                                'arrayRegistrosEstadosMonitoreo'        => $arrayResultadoEstadosMonitoreo['registros'] ? 
                                                                           $arrayResultadoEstadosMonitoreo['registros'] : array(),
                                'arrayNotInEstadosZonas'                => array('Eliminado','Inactivo'),
                                'arrayCriterios'                        => array( 
                                                                                'arrayEstadosTablet'        => $arrayEstadosTablet,
                                                                                'strImeiTablet'             => $strImeiTablet,
                                                                                'strSerieLogicaTablet'      => $strSerieLogicaTablet,
                                                                                'strResponsableTablet'      => $strResponsableTablet,
                                                                                'strDepartamentoPer'        => $strDepartamentoPer,
                                                                                'strCuadrillaPer'           => $strCuadrillaPer
                                                                            ),
                                'arrayCriteriosBusquedaAvanzada'        => array(
                                                                                    'intIdDepartamentoCuadrillaBusqAvanzada'    => 
                                                                                    $intIdDepartamentoCuadrillaBusqAvanzada,
                                                                                    'intIdZonaCuadrillaBusqAvanzada'            => 
                                                                                    $intIdZonaCuadrillaBusqAvanzada,
                                                                                    'intIdModeloBusqAvanzada'                   => 
                                                                                    $intIdModeloBusqAvanzada,
                                                                                    'strEstadoMonitoreoBusqAvanzada'            => 
                                                                                    $strEstadoMonitoreoBusqAvanzada,
                                                                                    'strFiltrarMisCuadrillasBusqAvanzada'       => 
                                                                                    $strFiltrarMisCuadrillasBusqAvanzada,
                                                                                    'strFiltrarPorHorarioBusqAvanzada'          => 
                                                                                    $strFiltrarPorHorarioBusqAvanzada,
                                                                                    'intIdPerSession'                           => 
                                                                                    $intIdPerSession,
                                                                                    'strRegionPerBusqAvanzada'                  =>
                                                                                    $strRegionPerBusqAvanzada,
                                                                                    'intIdCantonPerBusqAvanzada'                =>
                                                                                    $intIdCantonPerBusqAvanzada,
                                                                                    'intIdDepartamentoPerBusqAvanzada'          =>
                                                                                    $intIdDepartamentoPerBusqAvanzada
                                                                                    
                                                                            )
                            );
        
        if($strTipoReporte!="TODAS_EN_CAMPO")
        {
            $arrayParametros['intInicio'] = $intStart;
            $arrayParametros['intLimite'] = $intLength;
        }
        else
        {
            /*Estados que se marcarán en el mapa: Con ubicación actualizada y desactualizada*/
            $arrayParametros['strFiltrarEstadosMonitoreoMapa'] = "SI";
        }

        $strJson    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getJSONTabletsMonitoreo( $arrayParametros );
        $objResponse->setContent($strJson);
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'generarReporteGeneralAction'.
     *
     * Genera el reporte estadístico del número de tablets categorizados por region, ciudad y departamento de acuerdo a los parámetros enviados.
     *
     * @return JsonResponse 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 23-02-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 27-12-2016 Se agregan los filtros por región, ciudad y  departamento del usuario en sesión y se obtiene como parámetro
     *                         la cantidad de minutos que será utilizada para la comparación de fechas y horas en el monitoreo de tablets
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 08-01-2017 Se envían parámetros adicionales referentes al estado de la cuadrilla cuando es prestada y al campo está libre 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 16-01-2017 Se elimina parámetro strBusqRegion, ya que actualmente no se envía nada en dicho parámetro y en su lugar se lo envía
     *                         como parámetro strRegionPerBusqAvanzada de la búsqueda avanzada.
     * 
     */
    public function generarReporteGeneralAction()
    {
        $emGeneral                                  = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura                          = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objResponse                                = new JsonResponse();
        $objRequest                                 = $this->getRequest();
        $objSession                                 = $objRequest->getSession();
        $strTipoReporteMonitoreo                    = $objRequest->get('strTipoReporteMonitoreo') ? $objRequest->get('strTipoReporteMonitoreo') 
                                                        : "RESUMEN_GENERAL";
        
        $arrayCriteriosPerBusquedaAvanzada          = array();
        $arrayCriteriosCuadrillaBusquedaAvanzada    = array();
        $arrayCriteriosTabletBusquedaAvanzada       = array();
        $strCodEmpresaSession                       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $strMinutosMonitoreo                        = "";
        
        $arrayRegistroLimiteMinutosMonitoreo        = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne(   self::LIMITE_MINUTOS_MONITOREO, 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            $strCodEmpresaSession );

        if($arrayRegistroLimiteMinutosMonitoreo)
        {
            $strMinutosMonitoreo = $arrayRegistroLimiteMinutosMonitoreo['valor1'];
        }
  
        /*
         * Criterios de búsqueda avanzada también deben ser consideradas para obtener el RESUMEN_GENERAL
         */
        if($strTipoReporteMonitoreo=="RESUMEN_GENERAL")
        {
            /*Criterios del usuario*/
            $strRegionPerBusqAvanzada               = $objRequest->get('strRegionPerBusqAvanzada') 
                                                        ? trim($objRequest->get('strRegionPerBusqAvanzada')) : "";
            
            $intIdCantonPerBusqAvanzada             = $objRequest->get('intIdCantonPerBusqAvanzada') 
                                                        ? $objRequest->get('intIdCantonPerBusqAvanzada') : 0;
            
            $intIdDepartamentoPerBusqAvanzada       = $objRequest->get('intIdDepartamentoPerBusqAvanzada') 
                                                        ? $objRequest->get('intIdDepartamentoPerBusqAvanzada') : 0;
            if($strRegionPerBusqAvanzada || $intIdCantonPerBusqAvanzada || $intIdDepartamentoPerBusqAvanzada)
            {
                $arrayCriteriosPerBusquedaAvanzada["strRegionPerBusqAvanzada"]          = $strRegionPerBusqAvanzada;
                $arrayCriteriosPerBusquedaAvanzada["intIdCantonPerBusqAvanzada"]        = $intIdCantonPerBusqAvanzada;
                $arrayCriteriosPerBusquedaAvanzada["intIdDepartamentoPerBusqAvanzada"]  = $intIdDepartamentoPerBusqAvanzada;
                
            }
            

            /*Criterios de la cuadrilla*/
            $intIdDepartamentoCuadrillaBusqAvanzada = $objRequest->get('intIdDepartamentoCuadrillaBusqAvanzada') 
                                                        ? $objRequest->get('intIdDepartamentoCuadrillaBusqAvanzada') : 0;
            
            $intIdZonaCuadrillaBusqAvanzada         = $objRequest->get('intIdZonaCuadrillaBusqAvanzada') 
                                                        ? $objRequest->get('intIdZonaCuadrillaBusqAvanzada') : 0;
            
            $strFiltrarMisCuadrillasBusqAvanzada    = $objRequest->get('strFiltrarMisCuadrillasBusqAvanzada') 
                                                        ? $objRequest->get('strFiltrarMisCuadrillasBusqAvanzada') : "NO";
            
            $intIdPerSession                        = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        
            
            $strFiltrarPorHorarioBusqAvanzada       = $objRequest->get('strFiltrarPorHorarioBusqAvanzada') 
                                                        ? $objRequest->get('strFiltrarPorHorarioBusqAvanzada') : "NO";
            
            
            if($intIdDepartamentoCuadrillaBusqAvanzada || $intIdZonaCuadrillaBusqAvanzada 
                || $strFiltrarMisCuadrillasBusqAvanzada!="NO" || $strFiltrarPorHorarioBusqAvanzada!="NO")
            {
                $arrayCriteriosCuadrillaBusquedaAvanzada["intIdDepartamentoCuadrillaBusqAvanzada"]  = $intIdDepartamentoCuadrillaBusqAvanzada;
                $arrayCriteriosCuadrillaBusquedaAvanzada["intIdZonaCuadrillaBusqAvanzada"]          = $intIdZonaCuadrillaBusqAvanzada;
                $arrayCriteriosCuadrillaBusquedaAvanzada["strFiltrarMisCuadrillasBusqAvanzada"]     = $strFiltrarMisCuadrillasBusqAvanzada;
                $arrayCriteriosCuadrillaBusquedaAvanzada["intIdPerSession"]                         = $intIdPerSession;
                $arrayCriteriosCuadrillaBusquedaAvanzada["strFiltrarPorHorarioBusqAvanzada"]        = $strFiltrarPorHorarioBusqAvanzada;
            }
            
            
            
            
            /*Criterios de tablet*/
            $intIdModeloBusqAvanzada = $objRequest->get('intIdModeloBusqAvanzada') ? $objRequest->get('intIdModeloBusqAvanzada') : 0;
            if($intIdModeloBusqAvanzada)
            {
                $arrayCriteriosTabletBusquedaAvanzada["intIdModeloBusqAvanzada"]  = $intIdModeloBusqAvanzada;
            }
            
        }

        $arrayParametros = array(
                                    'strMinutosMonitoreo'                       => $strMinutosMonitoreo,
                                    'strTipoReporteMonitoreo'                   => $strTipoReporteMonitoreo,
                                    'strEstadoActivo'                           => 'Activo',
                                    'strEstadoInactivo'                         => 'Inactivo',
                                    'strEstadoPrestadoCuadrilla'                => 'Prestado',
                                    'strCuadrillaEstaLibre'                     => 'SI',
                                    'strDetalleResponsable'                     => self::DETALLE_RESPONSABLE_TABLET,
                                    'strNombreTipoElemento'                     => self::TIPO_ELEMENTO_TABLET,
                                    'strDetalleMotivo'                          => self::DETALLE_MOTIVO_INACTIVAR_TABLET,
                                    'arrayMotivosEnTotalTablets'                => array('MANTENIMIENTO'),
                                    'arrayCriteriosCuadrillaBusquedaAvanzada'   => $arrayCriteriosCuadrillaBusquedaAvanzada,
                                    'arrayCriteriosPerBusquedaAvanzada'         => $arrayCriteriosPerBusquedaAvanzada,
                                    'arrayCriteriosTabletBusquedaAvanzada'      => $arrayCriteriosTabletBusquedaAvanzada
                            );
        
        $strJson    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getJSONReporteTabletsMonitoreo( $arrayParametros );
        $objResponse->setContent($strJson);
        return $objResponse;
    }
    
    
    /**
     * Documentación para el método 'getLideresCuadrillaAction'.
     *
     * Lista todas las zonas
     *
     * @return JsonResponse $objResponse
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     */
    public function getLideresCuadrillaAction()
    {
        $objResponse    = new JsonResponse();
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $objRequest     = $this->get('request');
        $strNombreZona  = $objRequest->query->get('query') ? $objRequest->query->get('query') : '';
        
        $objSession     = $objRequest->getSession();
        $idOficina      = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina     = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);

        $strRegion      = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }
        $arrayParametros = array(
                                    "nombre"    => $strNombreZona,
                                    "region"    => $strRegion,
                                    "estados"   => array(
                                                            'estadoEliminado'   =>'Eliminado',
                                                            'estadoInactivo'    =>'Inactivo'
                                                        )
                            );
        $strJson    = $emComercial->getRepository('schemaBundle:AdmiZona')->getJSONZonasByParametros($arrayParametros);   
        $objResponse->setContent($strJson);

        return $objResponse;
    }
    
    
    /**
     * Documentación para el método 'getZonasAction'.
     *
     * Lista todas las zonas
     *
     * @return JsonResponse $objResponse
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     */
    public function getZonasAction()
    {
        $objResponse    = new JsonResponse();
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $objRequest     = $this->get('request');
        $strNombreZona  = $objRequest->query->get('query') ? $objRequest->query->get('query') : '';
        
        $objSession     = $objRequest->getSession();
        $idOficina      = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina     = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);

        $strRegion      = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }
        $arrayParametros = array(
                                    "nombre"    => $strNombreZona,
                                    "region"    => $strRegion,
                                    "estados"   => array(
                                                            'estadoEliminado'   =>'Eliminado',
                                                            'estadoInactivo'    =>'Inactivo'
                                                        )
                            );
        $strJson    = $emComercial->getRepository('schemaBundle:AdmiZona')->getJSONZonasByParametros($arrayParametros);   
        $objResponse->setContent($strJson);

        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getDepartamentosAction'.
     *
     * Lista todos los departamentos de acuerdo a los parámetros enviados
     *
     * @return JsonResponse $objResponse
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 Se modifica la función para obtener únicamente los departamentos cuyos empleados tengan asignada tablets
     * 
     */
    public function getDepartamentosAction()
    {
        $objResponse        = new JsonResponse();
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $objRequest         = $this->get('request');
        $strNombreDep       = $objRequest->query->get('query') ? $objRequest->query->get('query') : '';
        
        $objSession         = $objRequest->getSession();
        $strCodEmpresa      = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        
        $strJson            = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->getJSONDepartamentosTablets(array(
                                                                                    "strTipoElemento"           => 'TABLET',
                                                                                    "strEstadoActivo"           => 'Activo',
                                                                                    "strCodEmpresa"             => $strCodEmpresa,
                                                                                    "strNombreDepartamento"     => $strNombreDep,
                                                                                    "strNombreDetResponsable"   => self::DETALLE_RESPONSABLE_TABLET
                                                ));

        $objResponse->setContent($strJson);

        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getCantonesAction'.
     *
     * Lista todas las ciudades de acuerdo a los parámetros enviados
     *
     * @return JsonResponse $objResponse
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-12-2016 
     */
    public function getCantonesAction()
    {
        $objResponse        = new JsonResponse();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objRequest         = $this->get('request');
        $strNombreCanton    = $objRequest->query->get('query') ? $objRequest->query->get('query') : "";
        $strRegion          = $objRequest->get('strRegion') ? $objRequest->get('strRegion') : "";
        
        $arrayParametros    = array("strRegion" => $strRegion );

        $strJson            = $emGeneral->getRepository('schemaBundle:AdmiCanton')
                                        ->generarJson($arrayParametros, $strNombreCanton,'Activo-Todos','','');

        $objResponse->setContent($strJson);

        return $objResponse;
    }
    
    
    /**
     * Documentación para el método 'getEstadosMonitoreoAction'.
     *
     * Lista todos los estados en los que se puede encontrar una tablet en el monitoreo
     *
     * @return JsonResponse $objResponse
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     */
    public function getEstadosMonitoreoAction()
    {
        $objResponse                = new JsonResponse();
        $emGeneral                  = $this->getDoctrine()->getManager("telconet");
        $strJson                    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getJSONDetallesParametroGeneral(self::ESTADOS_MONITOREO_TABLET,'','');

        $objResponse->setContent($strJson);

        return $objResponse;
    }
    
     /**
     * 
     * Documentación de la funcion 'getResponsablesAction'.
     * 
     * Método que retorna los empleados de cualquier departamento que aparecerán en el combo para asignar un responsable a la tablet
     * 
     * @return JsonResponse retorna el resultado de la operación
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-12-2016
     */
    public function getResponsablesAction()
    {
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $objResponse         = new JsonResponse();
        $intStart            = $objRequest->get("start") ? $objRequest->get("start") : 0;
        $intLimit            = $objRequest->get("limit") ? $objRequest->get("limit") : 0;
        $intIdEmpresa        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        
        $intIdPerResponsable = $objRequest->get("intIdPerResponsable") ? $objRequest->get("intIdPerResponsable") : 0;
        
        
        $strNombresApellidosResponsable = $objRequest->get("query") ? $objRequest->get("query") : '';

        $arrayParametros = array(
                                    'idEmpresa'                     => $intIdEmpresa,
                                    'intIdPerEmpresaRol'            => $intIdPerResponsable,
                                    'nombreApellidoPer'             => $strNombresApellidosResponsable,
                                    'start'                         => $intStart,
                                    'limit'                         => $intLimit,
                                    'estado'                        => 'Activo',
                                    'strDescripcionTipoRol'         => 'Empleado',
        );

        $strJson    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getJSONPersonaEmpresaRolPorCriterios($arrayParametros);
        $objResponse->setContent($strJson);
        return $objResponse;
    }
    
     /**
     * 
     * Documentación de la funcion 'getInfoPerResponsableAction'.
     * 
     * Método que retorna la información del departamento, oficina, cantón y región de la persona empresa rol enviada
     * 
     * @return JsonResponse retorna el resultado de la operación
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-12-2016
     */
    public function getInfoPerResponsableAction()
    {
        $emComercial                            = $this->getDoctrine()->getManager('telconet');
        $objRequest                             = $this->getRequest();
        $intIdPerResponsable                    = $objRequest->get("intIdPerResponsable") ? $objRequest->get("intIdPerResponsable") : 0;
        $objPerResponsable                      = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerResponsable);
        $strNombreDepartamentoResponsableTablet = "";
        $strRegionResponsableTablet             = "";
        $strCantonResponsableTablet             = "";
        $objResponse                            = new JsonResponse();
        
        if(is_object($objPerResponsable))
        {
            $intIdDepartamentoPerResponsable    = $objPerResponsable->getDepartamentoId();
            if($intIdDepartamentoPerResponsable)
            {
                $objDepartamentoPerResponsable  = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                              ->find($intIdDepartamentoPerResponsable);
                if(is_object($objDepartamentoPerResponsable))
                {
                    $strNombreDepartamentoResponsableTablet  = sprintf('%s', $objDepartamentoPerResponsable);
                }
            }
            $intIdOficinaPerResponsable         = $objPerResponsable->getOficinaId();
            if($intIdOficinaPerResponsable)
            {
                $objOficinaPerResponsable   = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                          ->find($intIdOficinaPerResponsable);
                if(is_object($objOficinaPerResponsable))
                {
                    $intIdCantonPerResponsable  = $objOficinaPerResponsable->getCantonId();
                    if($intIdCantonPerResponsable)
                    {
                        $objCantonPerResponsable   = $emComercial->getRepository('schemaBundle:AdmiCanton')
                                                                 ->find($intIdCantonPerResponsable);
                        if(is_object($objCantonPerResponsable))
                        {
                            $strRegionResponsableTablet = $objCantonPerResponsable->getRegion();
                            $strCantonResponsableTablet = sprintf('%s', $objCantonPerResponsable);
                        }
                    }
                }
            }   
        }
        
        $arrayPerEncontrados    = array("strDepartamentoPerResponsable" => $strNombreDepartamentoResponsableTablet,
                                        "strRegionPerResponsable"       => $strRegionResponsableTablet,
                                        "strCantonPerResponsable"       => $strCantonResponsableTablet);
        
        $objResponse->setData($arrayPerEncontrados);
        return $objResponse;
    }

    /**
     * Documentación para el método 'exportarResumenesMonitoreoAction'.
     *
     * Función que exporta el resumen solicitado, ya sea Nacional o Regional
     *
     * @return Response 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-01-2017
     */
    public function exportarResumenesMonitoreoAction()
    {
        ini_set('max_execution_time', 30);
        
        $emGeneral                                  = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura                          = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest                                 = $this->getRequest();
        $objSession                                 = $objRequest->getSession();
        
        $strCodEmpresaSession                       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $strUsuarioSession                          = $objSession->get('empleado') ? ucwords(strtolower($objSession->get('empleado'))) : '';
        $strTipoResumenMonitoreo                    = trim($objRequest->get('strTipoExportarResumenDetallado')) ? 
                                                      trim($objRequest->get('strTipoExportarResumenDetallado')) : "RESUMEN_NACIONAL";
        $strRegionResumenMonitoreo                  = trim($objRequest->get('strRegionExportarResumenDetallado')) ? 
                                                      trim($objRequest->get('strRegionExportarResumenDetallado')) : "";
        $strMinutosMonitoreo                        = "";
        
        
        $arrayRegistroLimiteMinutosMonitoreo        = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne(   self::LIMITE_MINUTOS_MONITOREO, 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            $strCodEmpresaSession );

        if( $arrayRegistroLimiteMinutosMonitoreo )
        {
            $strMinutosMonitoreo = $arrayRegistroLimiteMinutosMonitoreo['valor1'];
        }
        
        $arrayParametros = array(
                                    'strMinutosMonitoreo'                       => $strMinutosMonitoreo,
                                    'strTipoReporteMonitoreo'                   => $strTipoResumenMonitoreo,
                                    'strEstadoActivo'                           => 'Activo',
                                    'strEstadoInactivo'                         => 'Inactivo',
                                    'strEstadoPrestadoCuadrilla'                => 'Prestado',
                                    'strCuadrillaEstaLibre'                     => 'SI',
                                    'strDetalleResponsable'                     => self::DETALLE_RESPONSABLE_TABLET,
                                    'strNombreTipoElemento'                     => self::TIPO_ELEMENTO_TABLET,
                                    'strDetalleMotivo'                          => self::DETALLE_MOTIVO_INACTIVAR_TABLET,
                                    'arrayMotivosEnTotalTablets'                => array('MANTENIMIENTO'),
                                    'arrayCriteriosPerBusquedaAvanzada'         => array("strRegionPerBusqAvanzada" => $strRegionResumenMonitoreo)
                            );
        
        $arrayResultadosResumen = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->getResultadoReporteTabletsMonitoreo( $arrayParametros );
        
        $objPHPExcel        = new PHPExcel();
        $strCacheMethod     = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $arrayCacheSettings = array(' memoryCacheSize ' => '1024MB');
        
        PHPExcel_Settings::setCacheStorageMethod($strCacheMethod, $arrayCacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        
        $strDirTemplate     = "";
        $strNombreArchivo   = "";
        $strTituloResumen   = "";
        $strCriterioReporte = "";
        if($strTipoResumenMonitoreo=="RESUMEN_NACIONAL")
        {
            $strDirTemplate     ="/../Resources/templatesExcel/templateTabletResumen.xls";
            $strNombreArchivo   = "Monitoreo_Resumen_Nacional_";
            $strTituloResumen   = "RESUMEN NACIONAL - MONITOREO DE TABLETS";
            $strCriterioReporte = "DEPARTAMENTO";
            
        }
        else if($strTipoResumenMonitoreo=="RESUMEN_REGIONAL")
        {
            $strDirTemplate     ="/../Resources/templatesExcel/templateTabletResumen.xls";
            $strNombreArchivo   = "Monitoreo_Resumen_Regional_".$strRegionResumenMonitoreo."_";
            $strTituloResumen   = "RESUMEN REGIONAL ".$strRegionResumenMonitoreo." - MONITOREO DE TABLETS";
            $strCriterioReporte = "REGIÓN - CIUDAD - DEPARTAMENTO";
        }

        $objPHPExcel = $objReader->load(__DIR__ . $strDirTemplate);

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($strUsuarioSession);
        $objPHPExcel->getProperties()->setTitle("Monitoreo de Tablets");
        $objPHPExcel->getProperties()->setSubject("Monitoreo de Tablets");
        $objPHPExcel->getProperties()->setDescription("Información de Monitoreo de Tablets");
        $objPHPExcel->getProperties()->setKeywords("Tablet");
        $objPHPExcel->getProperties()->setCategory("Monitoreo");
        
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $strTituloResumen);
        $objPHPExcel->getActiveSheet()->setCellValue('B3', $strUsuarioSession);

        $objPHPExcel->getActiveSheet()->setCellValue('B4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('A7', $strCriterioReporte);
        
        $intIteradorFila = 9;
                
        $arrayStyleAlignRightCenter = array(    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER  );
        
        $arrayStyleAlignLeftCenter  = array(    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER );
        
        $arrayStyleFontTotal        = array(
                                                'bold'  => true,
                                                'color' => array('rgb' => '000000'),
                                                'size'  => 11
                                      );
        
        $arraySumsCategorias        = array(
                                            "intSumNumTabletsTotal"             => 0,
                                            "intSumNumTabletsMantLibre"         => 0,
                                            "intSumNumTabletsCuadrillasLibres"  => 0,
                                            "intSumNumTabletsEnCampo"           => 0,
                                            "intSumNumTabletsActualizadas"      => 0,
                                            "intSumNumTabletsDesactualizadas"   => 0,
                                            "intSumNumTabletsProblGPS"          => 0,
                                            "intSumNumTabletsNoMonitoreadas"    => 0,
                                      );
        
        foreach($arrayResultadosResumen['resultado'] as $arrayResultadoResumen)
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$intIteradorFila, $arrayResultadoResumen['strNombreFila']);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignLeftCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$intIteradorFila, $arrayResultadoResumen['intNumTabletsTotal']);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignRightCenter) );
            $arraySumsCategorias["intSumNumTabletsTotal"] = $arraySumsCategorias["intSumNumTabletsTotal"] 
                                                            + $arrayResultadoResumen['intNumTabletsTotal'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$intIteradorFila, $arrayResultadoResumen['intNumTabletsMantLibre']);
            $objPHPExcel->getActiveSheet()->getStyle('C'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignRightCenter) );
            $arraySumsCategorias["intSumNumTabletsMantLibre"] = $arraySumsCategorias["intSumNumTabletsMantLibre"] 
                                                                + $arrayResultadoResumen['intNumTabletsMantLibre'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$intIteradorFila, $arrayResultadoResumen['intNumTabletsCuadrillasLibres']);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignRightCenter) );
            $arraySumsCategorias["intSumNumTabletsCuadrillasLibres"] = $arraySumsCategorias["intSumNumTabletsCuadrillasLibres"] 
                                                                       + $arrayResultadoResumen['intNumTabletsCuadrillasLibres'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$intIteradorFila, $arrayResultadoResumen['intNumTabletsEnCampo']);
            $objPHPExcel->getActiveSheet()->getStyle('E'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignRightCenter) );
            $arraySumsCategorias["intSumNumTabletsEnCampo"] = $arraySumsCategorias["intSumNumTabletsEnCampo"] 
                                                              + $arrayResultadoResumen['intNumTabletsEnCampo'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$intIteradorFila, $arrayResultadoResumen['intNumTabletsActualizadas']);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignRightCenter) );
            $arraySumsCategorias["intSumNumTabletsActualizadas"] = $arraySumsCategorias["intSumNumTabletsActualizadas"] 
                                                                   + $arrayResultadoResumen['intNumTabletsActualizadas'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$intIteradorFila, $arrayResultadoResumen['intNumTabletsDesactualizadas']);
            $objPHPExcel->getActiveSheet()->getStyle('G'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignRightCenter) );
            $arraySumsCategorias["intSumNumTabletsDesactualizadas"] = $arraySumsCategorias["intSumNumTabletsDesactualizadas"] 
                                                                      + $arrayResultadoResumen['intNumTabletsDesactualizadas'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$intIteradorFila, $arrayResultadoResumen['intNumTabletsProblGPS']);
            $objPHPExcel->getActiveSheet()->getStyle('H'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignRightCenter) );
            $arraySumsCategorias["intSumNumTabletsProblGPS"] = $arraySumsCategorias["intSumNumTabletsProblGPS"] 
                                                               + $arrayResultadoResumen['intNumTabletsProblGPS'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$intIteradorFila, $arrayResultadoResumen['intNumTabletsNoMonitoreadas']);
            $objPHPExcel->getActiveSheet()->getStyle('I'.$intIteradorFila)->applyFromArray( array('alignment' => $arrayStyleAlignRightCenter) );
            $arraySumsCategorias["intSumNumTabletsNoMonitoreadas"] = $arraySumsCategorias["intSumNumTabletsNoMonitoreadas"] 
                                                                     + $arrayResultadoResumen['intNumTabletsNoMonitoreadas'];
            
            $intIteradorFila++;
        }
        
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$intIteradorFila, "TOTAL");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignLeftCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );
        
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$intIteradorFila, $arraySumsCategorias["intSumNumTabletsTotal"]);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignRightCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );

        $objPHPExcel->getActiveSheet()->setCellValue('C'.$intIteradorFila, $arraySumsCategorias["intSumNumTabletsMantLibre"]);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignRightCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );

        $objPHPExcel->getActiveSheet()->setCellValue('D'.$intIteradorFila, $arraySumsCategorias["intSumNumTabletsCuadrillasLibres"]);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignRightCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );

        $objPHPExcel->getActiveSheet()->setCellValue('E'.$intIteradorFila, $arraySumsCategorias["intSumNumTabletsEnCampo"]);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignRightCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );

        $objPHPExcel->getActiveSheet()->setCellValue('F'.$intIteradorFila, $arraySumsCategorias["intSumNumTabletsActualizadas"]);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignRightCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );

        $objPHPExcel->getActiveSheet()->setCellValue('G'.$intIteradorFila, $arraySumsCategorias["intSumNumTabletsDesactualizadas"]);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignRightCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );

        $objPHPExcel->getActiveSheet()->setCellValue('H'.$intIteradorFila, $arraySumsCategorias["intSumNumTabletsProblGPS"]);
        $objPHPExcel->getActiveSheet()->getStyle('H'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignRightCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );

        $objPHPExcel->getActiveSheet()->setCellValue('I'.$intIteradorFila, $arraySumsCategorias["intSumNumTabletsNoMonitoreadas"]);
        $objPHPExcel->getActiveSheet()->getStyle('I'.$intIteradorFila)->applyFromArray( array(  'alignment' => $arrayStyleAlignRightCenter,
                                                                                                'font'      => $arrayStyleFontTotal) );

        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->setTitle('Resumen Monitoreo Tablets');
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$strNombreArchivo.date('d_m_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}