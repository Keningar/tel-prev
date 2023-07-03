<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Form\InfoElementoFuenteType;

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
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Alignment;

/**
 * InfoElementoTransporte controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la administración de Fuentes de la empresa
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 18-01-2016
 */
class InfoElementoFuenteController extends Controller
{
    const TIPO_ELEMENTO      = 'FUENTE';
    const ESTADO_ACTIVO      = 'Activo';
    const ESTADO_ELIMINADO   = 'Eliminado';
    const ESTADO_CONNECTED   = 'connected';
    const ESTADO_NOTCONNECT  = 'not connect';
    
    
    /**
     * @Secure(roles="ROLE_329-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redireccion a la pantalla principal de la administracion de fuentes de la empresa
     * @return render.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-01-2016
     */
    public function indexAction()
    {
        if (true === $this->get('security.context')->isGranted('ROLE_329-6'))
        {
            $rolesPermitidos[] = 'ROLE_329-6'; //Ver la información de la fuente
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_329-4'))
        {
            $rolesPermitidos[] = 'ROLE_329-4'; //Editar una fuente
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_329-8'))
        {
            $rolesPermitidos[] = 'ROLE_329-8'; //Eliminar fuente
        }
        
        return $this->render('tecnicoBundle:InfoElementoFuente:index.html.twig', array('rolesPermitidos' => $rolesPermitidos));
    }
    
    
    /**
     * @Secure(roles="ROLE_329-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra el listado de todos las fuentes creadas.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 18-01-2016
     */
    public function gridAction()
    {
        $jsonResponse           = new JsonResponse();
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombre              = $objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : "";
        $intModelo              = $objRequest->query->get('modelo') ? $objRequest->query->get('modelo') : "";
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
                                    'strCategoriaElemento' => 'fuente',
                                    'criterios'            => array( 'nombre'         => $strNombre, 
                                                                     'tipoElemento'   => $arrayTiposElemento,
                                                                     'modeloElemento' => $arrayModelosElemento )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);
        
        $jsonResponse->setData( $arrayResultados );
        
        return $jsonResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_329-2")
     * 
     * Documentación para el método 'newAction'.
     *
     * Muestra usado para mostrar el formulario vacío para crear una fuente.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 18-01-2016
     */
    public function newAction()
    {
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTipoElemento        = self::TIPO_ELEMENTO;
        $arrayModelosElementos  = array();
        $arrayTmpParametros     = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array($strTipoElemento) );
        $arrayTmpResultados     = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                    ->getModeloElementosByCriterios( $arrayTmpParametros );
        $objInfoElemento        = new InfoElemento();
        $form                   = $this->createForm(new InfoElementoFuenteType(), $objInfoElemento);
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
        }

        return $this->render( 'tecnicoBundle:InfoElementoFuente:new.html.twig', array(
                                                                                        'modelosElemento' => $arrayModelosElementos,
                                                                                        'strTipoElemento' => $strTipoElemento,
                                                                                        'entity'          => $objInfoElemento,
                                                                                        'form'            => $form->createView()
                                                                                      ) 
                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_329-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Guarda una fuente.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 18-01-2016
     */
    public function createAction()
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdEmpresaSession  = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strPrefijoEmpresa    = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strUserSession       = $objSession->get('user');
        $strIpUserSession     = $objRequest->getClientIp();
        $datetimeActual       = new \DateTime('now');
        $arrayPostFuente      = $objRequest->get('infoElementoFuente');
        $strNombreElemento    = $arrayPostFuente['nombreElemento'] ? $arrayPostFuente['nombreElemento'] : '';
        $strObservacion       = $arrayPostFuente['observacion'] ? $arrayPostFuente['observacion'] : '';
        $intIdModeloElemento  = $arrayPostFuente['modeloElementoId'] ? $arrayPostFuente['modeloElementoId'] : 0;
        $intIdPuerto          = $objRequest->request->get('intIdPuerto') ? $objRequest->request->get('intIdPuerto') : '';
        $strFechaInstalacion  = $objRequest->request->get('dateFechaInstalacion') ? $objRequest->request->get('dateFechaInstalacion') : '';
        
        if( $strFechaInstalacion )
        {
            $datetimeFechaInstalacion = new \DateTime($strFechaInstalacion);
            $strFechaInstalacion      = $datetimeFechaInstalacion->format('d-m-Y');
        }

        $objInfoElemento = new InfoElemento();
        
        $emInfraestructura->getConnection()->beginTransaction();	
        
        try
        {
            /*
             * Bloque que guarda el InfoElemento
             */
            $intIdEmpresa      = $intIdEmpresaSession;
            $objModeloElemento = null;
            
            if( $intIdModeloElemento )
            {
                $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($intIdModeloElemento);
            }
            
                    
            $objInfoElemento->setModeloElementoId($objModeloElemento);
            $objInfoElemento->setNombreElemento(trim($strNombreElemento));
            $objInfoElemento->setDescripcionElemento("DISPOSITIVO FUENTE DE ".$strPrefijoEmpresa);
            $objInfoElemento->setObservacion(trim($strObservacion));
            $objInfoElemento->setEstado(self::ESTADO_ACTIVO);
            $objInfoElemento->setFeCreacion($datetimeActual);
            $objInfoElemento->setUsrCreacion($strUserSession);
            $objInfoElemento->setIpCreacion($strIpUserSession);
            $emInfraestructura->persist($objInfoElemento);
            $emInfraestructura->flush();
            
            $intIdElemento = $objInfoElemento->getId();
            /*
             * Fin del Bloque que guarda el InfoElemento
             */
            
            
            /*
             * Bloque que guarda los detalles del InfoElemento
             */
            $arrayInfoDetalles = array( 'FECHA_INSTALACION' => $strFechaInstalacion );
            
            foreach($arrayInfoDetalles as $strKey => $strValue)
            {
                if( $strValue )
                {
                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                    $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                    $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                    $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                    $objInfoDetalleElemento->setElementoId($intIdElemento);
                    $objInfoDetalleElemento->setDetalleNombre($strKey);
                    $objInfoDetalleElemento->setDetalleValor($strValue);
                    $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                }//( $strValue )
            }//foreach($arrayInfoDetalles as $strKey => $strValue)
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
            $objInfoHistorialElemento->setObservacion($strObservacion);
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
             * Bloque que guarda la InfoEnlace entre el Switch y la Fuente
             */
            $strNombreInterfazElemento = 'Eth0'; 

            $objInfoInterfaceElemento = new InfoInterfaceElemento();
            $objInfoInterfaceElemento->setElementoId($objInfoElemento);
            $objInfoInterfaceElemento->setEstado(self::ESTADO_NOTCONNECT);
            $objInfoInterfaceElemento->setUsrCreacion($strUserSession);
            $objInfoInterfaceElemento->setFeCreacion($datetimeActual);
            $objInfoInterfaceElemento->setIpCreacion($strIpUserSession);
            $objInfoInterfaceElemento->setNombreInterfaceElemento($strNombreInterfazElemento);
            $objInfoInterfaceElemento->setDescripcionInterfaceElemento('INTERFACE FUENTE: '.$strNombreInterfazElemento);
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
            /*
             * Fin Bloque que guarda la InfoEnlace entre el Switch y la Fuente
             */
            
            
            /*
             * Bloque que guarda la InfoElementoUbicacion del Elemento FUENTE
             */
            if( $objInterfaceSwitch )
            {
                $objInfoRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                             ->findOneBy( array( 'estado'      => self::ESTADO_ACTIVO,
                                                                                 'elementoIdB' => $objInterfaceSwitch->getElementoId()->getId() ) );
                if( $objInfoRelacionElemento )
                {
                    $objInfoElementoNodo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                             ->findOneBy( array( 'estado' => self::ESTADO_ACTIVO,
                                                                                 'id'     => $objInfoRelacionElemento->getElementoIdA() ) );

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
                    throw new \Exception("No se encontro conexión del Switch con el Nodo en estado Activo");
                }//( $objInfoRelacionElemento ) 
            }
            else
            {
                throw new \Exception("No se encontro interfaz del Switch estado Activo");
            }//( $objInterfaceSwitch )
            /*
             * Fin Bloque que guarda la InfoElementoUbicacion del Elemento FUENTE
             */
            
            

            $emInfraestructura->getConnection()->commit();
            $emInfraestructura->getConnection()->close();

            return $this->redirect($this->generateUrl('elementofuente_show', array('id' => $intIdElemento)));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }//try
        
        
        return $this->redirect($this->generateUrl('elementofuente_new'));
    }
    
    
    /**
     * @Secure(roles="ROLE_329-6")
     * 
     * Documentación para el método 'showAction'.
     *
     * Muestra la información de una Fuente existente.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 18-01-2016
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
        
        $arrayInfoDetalles = array( 'FECHA_INSTALACION' => '' );
        
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

        return $this->render('tecnicoBundle:InfoElementoFuente:show.html.twig', array(
                                                                                        'elemento' => $objInfoElemento,
                                                                                        'detalles' => $arrayInfoDetalles,
                                                                                        'empresa'  => $strNombreEmpresa,
                                                                                        'switch'   => $strNombreSwitch,
                                                                                        'puerto'   => $strPuertoSwitch,
                                                                                     )
                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_329-4")
     * 
     * Documentación para el método 'editAction'.
     *
     * Muestra la información de una fuente a la cual se le va a actualizar la información.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 20-01-2016
     */
    public function editAction($id)
    {
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTipoElemento   = self::TIPO_ELEMENTO;
        $objInfoElemento   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objInfoElemento )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );
        
        $arrayInfoDetalles = array( 'FECHA_INSTALACION' => '' );
        
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
        
        
        $form = $this->createForm(new InfoElementoFuenteType(), $objInfoElemento);
        
        return $this->render('tecnicoBundle:InfoElementoFuente:edit.html.twig', array(
                                                                                        'entity'          => $objInfoElemento,
                                                                                        'detalles'        => $arrayInfoDetalles,
                                                                                        'modelosElemento' => $arrayModelosElementos,
                                                                                        'form'            => $form->createView(),
                                                                                        'switch'          => $intIdSwitch,
                                                                                        'puerto'          => $intIdPuertoSwitch
                                                                                     )
                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_329-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información de una Fuente.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 20-01-2016
     */
    public function updateAction($id)
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdEmpresaSession  = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strUserSession       = $objSession->get('user');
        $strIpUserSession     = $objRequest->getClientIp();
        $datetimeActual       = new \DateTime('now');
        $arrayPostFuente      = $objRequest->get('infoElementoFuente');
        $strNombreElemento    = $arrayPostFuente['nombreElemento'] ? $arrayPostFuente['nombreElemento'] : '';
        $strObservacion       = $arrayPostFuente['observacion'] ? $arrayPostFuente['observacion'] : '';
        $intIdModeloElemento  = $arrayPostFuente['modeloElementoId'] ? $arrayPostFuente['modeloElementoId'] : 0;
        $intIdPuerto          = $objRequest->request->get('intIdPuerto') ? $objRequest->request->get('intIdPuerto') : 0;
        $strFechaInstalacion  = $objRequest->request->get('dateFechaInstalacion') ? $objRequest->request->get('dateFechaInstalacion') : '';
        $objInfoElemento      = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( $strFechaInstalacion )
        {
            $datetimeFechaInstalacion = new \DateTime($strFechaInstalacion);
            $strFechaInstalacion      = $datetimeFechaInstalacion->format('d-m-Y');
        }
        
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
            $objModeloElemento = null;
            if( $intIdModeloElemento )
            {
                $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($intIdModeloElemento);
            }
            
            $intIdEmpresa = $intIdEmpresaSession;
             
            $objInfoElemento->setModeloElementoId($objModeloElemento);
            $objInfoElemento->setNombreElemento(trim($strNombreElemento));
            $objInfoElemento->setObservacion(trim($strObservacion));
            $emInfraestructura->persist($objInfoElemento);
            $emInfraestructura->flush();
            
            $intIdElemento = $objInfoElemento->getId();
            /*
             * Fin del Bloque que actualiza el InfoElemento
             */
            
            
            /*
             * Bloque que actualiza los detalles del InfoElemento
             */
            $arrayInfoDetalles = array( 'FECHA_INSTALACION' => $strFechaInstalacion );
            
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
            $objInfoHistorialElemento->setObservacion($strObservacion);
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
             * Bloque que actualiza la InfoEnlace entre el Switch y la Fuente
             */
            $objInterfaceFuente = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findOneBy( array( 'elementoId'              => $objInfoElemento, 
                                                                        'estado'                  => self::ESTADO_CONNECTED,
                                                                        'nombreInterfaceElemento' => 'Eth0' ) );
            
            if( !$objInterfaceFuente )
            {
                $strNombreInterfazElemento = 'Eth0'; 

                $objInfoInterfaceElemento = new InfoInterfaceElemento();
                $objInfoInterfaceElemento->setElementoId($objInfoElemento);
                $objInfoInterfaceElemento->setEstado(self::ESTADO_NOTCONNECT);
                $objInfoInterfaceElemento->setUsrCreacion($strUserSession);
                $objInfoInterfaceElemento->setFeCreacion($datetimeActual);
                $objInfoInterfaceElemento->setIpCreacion($strIpUserSession);
                $objInfoInterfaceElemento->setNombreInterfaceElemento($strNombreInterfazElemento);
                $objInfoInterfaceElemento->setDescripcionInterfaceElemento('INTERFACE FUENTE: '.$strNombreInterfazElemento);
                $emInfraestructura->persist($objInfoInterfaceElemento);
                $emInfraestructura->flush();
            }//( !$objInterfaceFuente )
            
                
            $objAdmiTipoMedio   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                    ->findOneBy( array('estado' => self::ESTADO_ACTIVO, 'nombreTipoMedio' => 'Cobre') );

            $objInterfaceSwitchNew = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                       ->findOneById( $intIdPuerto );

            $objInfoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                               ->findOneBy( array( 'interfaceElementoFinId' => $objInfoInterfaceElemento,
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
            
            
            $objInfoEnlace->setInterfaceElementoFinId($objInfoInterfaceElemento);
            $objInfoEnlace->setInterfaceElementoIniId($objInterfaceSwitchNew);
            
            $emInfraestructura->persist($objInfoEnlace);
            $emInfraestructura->flush();
            
            $objInfoInterfaceElemento->setEstado(self::ESTADO_CONNECTED);
            $emInfraestructura->persist($objInfoInterfaceElemento);
            $emInfraestructura->flush();
            
            $objInterfaceSwitchNew->setEstado(self::ESTADO_CONNECTED);
            $emInfraestructura->persist($objInterfaceSwitchNew);
            $emInfraestructura->flush();
            /*
             * Fin Bloque que guarda la InfoEnlace entre el Switch y la Fuente
             */
            
            
            /*
             * Bloque que guarda la InfoElementoUbicacion del Elemento FUENTE
             */
            if( $objInterfaceSwitchNew )
            {
                $objInfoRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                             ->findOneBy( array( 'estado'      => self::ESTADO_ACTIVO,
                                                                                 'elementoIdB' => $objInterfaceSwitchNew->getElementoId()->getId() ) );
                if( $objInfoRelacionElemento )
                {
                    $objInfoElementoNodo = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                             ->findOneBy( array( 'estado' => self::ESTADO_ACTIVO,
                                                                                 'id'     => $objInfoRelacionElemento->getElementoIdA() ) );

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
                    throw new \Exception("No se encontro conexión del Switch con el Nodo en estado Activo");
                }//( $objInfoRelacionElemento ) 
            }
            else
            {
                throw new \Exception("No se encontro interfaz del Switch estado Activo");
            }//( $objInterfaceSwitch )
            /*
             * Fin Bloque que guarda la InfoElementoUbicacion del Elemento FUENTE
             */
            
            
            $emInfraestructura->getConnection()->commit();
            $emInfraestructura->getConnection()->close();

            return $this->redirect($this->generateUrl('elementofuente_show', array('id' => $intIdElemento)));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }//try
        

        return $this->redirect($this->generateUrl('elementofuente_edit', array('id' => $intIdElemento)));
    }
    
    
    /**
     * @Secure(roles="ROLE_329-8")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     *
     * Elimina la información de una Fuente.
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 20-01-2016
     */
    public function deleteAjaxAction()
    {
        $response            = new Response();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdEmpresaSession = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strElementos        = $objRequest->request->get('fuente') ? $objRequest->request->get('fuente') : '';
        $boolError           = false;
        $strMensaje          = 'No se encontró fuente en estado activo';
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
                    $strMensaje = 'No se encontró fuente en estado activo';
                }

                if( !$boolError )
                {
                    $intIdElemento = $objInfoElemento->getId();
                    
                    /*
                     * Bloque que elimina el InfoElemento
                     */
                    $strObservacionHistorial = 'Se elimina la información de la fuente';

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
                     * Bloque que elimina la InfoEnlace entre el Switch y la fuente
                     */
                    $arrayInterfacesFuente     = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                   ->findBy( array( 'elementoId' => $objInfoElemento ) );

                    if( $arrayInterfacesFuente )
                    {
                        foreach( $arrayInterfacesFuente as $objInterfaceFuente )
                        {
                            $objInterfaceFuente->setEstado(self::ESTADO_ELIMINADO);
                            $emInfraestructura->persist($objInterfaceFuente);
                            $emInfraestructura->flush();

                            $arrayInfoEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                 ->findBy( array( 'interfaceElementoFinId' => $objInterfaceFuente,
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

                                        $objInterfaceSwitch = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                ->findOneBy( array( 'estado' => self::ESTADO_CONNECTED,
                                                                                                    'id'     => $objInfoEnlace->getInterfaceElementoIniId() ) );

                                        $objInterfaceSwitch->setEstado(self::ESTADO_NOTCONNECT);
                                        $emInfraestructura->persist($objInterfaceSwitch);
                                        $emInfraestructura->flush();
                                    }//( $objInfoEnlace )
                                }//foreach( $arrayInfoEnlace as $objInfoEnlace )
                            }//( $arrayInfoEnlace )
                        }//foreach( $arrayInterfacesFuente as $objInterfaceFuente )
                    }//( $arrayInterfacesFuente )
                    /*
                     * Fin Bloque que elimina la InfoEnlace entre el Switch y la Fuente
                     */
                    
                    
                    $objInfoEmpresaElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                     ->findOneBy( array( 'elementoId' => $objInfoElemento,
                                                                                         'empresaCod' => $intIdEmpresaSession ) );

                    if( $objInfoEmpresaElementoUbica )
                    {
                        $emInfraestructura->remove($objInfoEmpresaElementoUbica);
                        $emInfraestructura->flush();
                    }
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
     * @version 1.0 18-01-2016
     */
    public function verificarDataAction()
    {
        $response               = new Response();
        $objRequest             = $this->get('request');
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $intIdPuerto            = $objRequest->request->get('puerto') ? $objRequest->request->get('puerto') : 0;
        $strNombreElemento      = $objRequest->request->get('nombre') ? $objRequest->request->get('nombre') : 0;
        $intIdElementoGuardado  = $objRequest->request->get('idFuente') ? $objRequest->request->get('idFuente') : 0;
        $strMensaje             = 'OK';
        $boolError              = false;
        
        
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


        if( !$boolError )
        {
            $arrayElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->findBy( array('estado' => self::ESTADO_ACTIVO, 'nombreElemento' => trim($strNombreElemento)) );

            if($arrayElementos)
            {
                foreach($arrayElementos as $objElemento)
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
                }//foreach($arrayElementos as $objElemento)
            }//($arrayElementos)
        }//( !$boolError )
        
        $response->setContent( $strMensaje );
        
        return $response;
    }  
    
    
    /**
     * @Secure(roles="ROLE_329-3457")
     * 
     * Documentación para el método 'gridMonitoreoAction'.
     *
     * Muestra el listado de todos las fuentes creadas para monitorear.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 03-02-2016
     */
    public function gridMonitoreoAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombre              = $objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : "";
        $intModelo              = $objRequest->query->get('modelo') ? $objRequest->query->get('modelo') : "";
        $intStart               = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit               = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $serviceInfoElemento    = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento     = array( self::TIPO_ELEMENTO );
        $arrayModelosElemento   = $intModelo ? array( $intModelo ) : array(); 
        $arrayDispositivos      = array();
        
        $arrayParametros = array(
                                    'intStart'             => $intStart,
                                    'intLimit'             => $intLimit,
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => 'fuente',
                                    'criterios'            => array( 'nombre'         => $strNombre, 
                                                                     'tipoElemento'   => $arrayTiposElemento,
                                                                     'modeloElemento' => $arrayModelosElemento )
                                );
        
        $arrayDispositivos = $serviceInfoElemento->getDispositivosMonitoreoFuentes($arrayParametros);
        
        return $this->render('tecnicoBundle:InfoElementoFuente:monitoreo.html.twig', array('dispositivos' => $arrayDispositivos));
    }
    
    
    /**
     * Documentación para el método 'buscarDispositivosAction'.
     *
     * Muestra el listado de todos los dispositivos para monitorear.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 04-02-2016
     */
    public function buscarDispositivosAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombreDispositivo   = $objRequest->request->get('dispositivo') ? $objRequest->request->get('dispositivo') : "";
        $serviceInfoElemento    = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento     = array( self::TIPO_ELEMENTO );
        $arrayDispositivos      = array();
        
        $arrayParametros = array(
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => 'fuente',
                                    'criterios'            => array( 'nombreDispositivo' => $strNombreDispositivo, 
                                                                     'tipoElemento'      => $arrayTiposElemento, )
                                );
        
        $arrayDispositivos = $serviceInfoElemento->getDispositivosMonitoreoFuentes($arrayParametros);
        
        return $this->render('tecnicoBundle:InfoElementoFuente:gridMonitoreo.html.twig', array('dispositivos' => $arrayDispositivos));
    }
    
    
    /**
     * Documentación para el método 'exportarReporteMonitoreoFuentesAction'.
     *
     * Retorna la información consolidada sobre los dispositivos enlazados a las fuentes.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 04-02-2016
     */
    public function exportarReporteMonitoreoFuentesAction()
    {
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);
        
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strUsuarioSession      = $objSession->get('empleado') ? ucwords(strtolower($objSession->get('empleado'))) : '';
        $strNombreDispositivo   = $objRequest->query->get('dispositivo') ? $objRequest->query->get('dispositivo') : "";
        $serviceInfoElemento    = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento     = array( self::TIPO_ELEMENTO );
        $arrayDispositivos      = array();
        
        $arrayParametros = array(
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => 'fuente',
                                    'criterios'            => array( 'nombreDispositivo' => $strNombreDispositivo, 
                                                                     'tipoElemento'      => $arrayTiposElemento, )
                                );
        
        
        
        $arrayDispositivos = $serviceInfoElemento->getDispositivosMonitoreoFuentes($arrayParametros);

        $objPHPExcel   = new PHPExcel();
        $cacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objPHPExcel = $objReader->load(__DIR__ . "/../Resources/templatesExcel/templateFuentesMonitoreo.xls");

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($strUsuarioSession);
        $objPHPExcel->getProperties()->setTitle("Tabla de Monitoreo de Fuentes");
        $objPHPExcel->getProperties()->setSubject("Tabla de Monitoreo de Fuentes");
        $objPHPExcel->getProperties()->setDescription("Tabla de Monitoreo de Fuentes.");
        $objPHPExcel->getProperties()->setKeywords("Fuentes");
        $objPHPExcel->getProperties()->setCategory("Monitoreo");
        
        $objPHPExcel->getActiveSheet()->setCellValue('B3', $strUsuarioSession);

        $objPHPExcel->getActiveSheet()->setCellValue('B4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8', ''.$strNombreDispositivo);

        $i = 13;
        $j = 1;
        
        $styleAlignCenter           = array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
        $styleBackgroundColorRed    = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FF0000') );
        $styleBackgroundColorYellow = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFF00') );
        $styleBackgroundColorGreen  = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '008000') );
        
        foreach($arrayDispositivos as $arrayDispositivo)
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $arrayDispositivo['switch']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $arrayDispositivo['puerto']);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $arrayDispositivo['estado']);
            
            if( $arrayDispositivo['estado'] == 'connected' )
            {
                $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorGreen
                                                                                          ) 
                                                                                 );
            }
            elseif( $arrayDispositivo['estado'] == 'notconnected' )
            {
                $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorRed
                                                                                          ) 
                                                                                 );
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorYellow
                                                                                          ) 
                                                                                 );
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
        header('Content-Disposition: attachment;filename="Tabla_de_Fuentes_Monitoreo_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        
        exit;
    }    
}
