<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\AdmiParroquia;
use telconet\schemaBundle\Form\InfoElementoTransporteType;
use telconet\schemaBundle\Form\InfoDocumentoType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * InfoElementoTransporte controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la administración de Vehículos de la empresa
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 05-11-2015
 */
class InfoElementoTransporteController extends Controller 
{
    const TIPO_ELEMENTO_VEHICULO               = 'VEHICULO';
    const TIPO_ELEMENTO_MOTO                  = 'MOTO';
    const TIPO_MEDIOS_TRANSPORTE               = 'MEDIOS_TRANSPORTE';
    const ESTADO_ACTIVO                        = 'Activo';
    const ESTADO_CANCELADO                    = 'Cancelado';
    const ESTADO_ELIMINADO                     = 'Eliminado';
    const DETALLE_ASOCIADO_ELEMENTO_CUADRILLA  = 'CUADRILLA';
    const TIPO_CONTRATO_VEHICULO               = 'VEHICULO';
    
    
    public function getRegionesVehiculosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $em             = $this->getDoctrine()->getManager();
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $idOficina      = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina     = $em->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);
        $strRegion      = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }
        
        $strNombreParametro="REGIONES_VEHICULOS";
        
        $objJson = $this->getDoctrine()->getManager("telconet_soporte")->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getJSONDetallesParametro($strNombreParametro,"",$strRegion);

        $respuesta->setContent($objJson);

        return $respuesta;
    } 
    
    public function getPlanesMantenimientoAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest = $this->get('request');
        $nombre = $objRequest->get('query');

        $session    = $objRequest->getSession();
        $codEmpresa = $session->get('idEmpresa');

        $parametros = array();
        $parametros["esPlanMantenimiento"]  = 'S';
        
        $start = $objRequest->get('start');
        $limit = $objRequest->get('limit');

        $objJson = $this->getDoctrine()->getManager("telconet_soporte")->getRepository('schemaBundle:AdmiProceso')
                                                                        ->generarJson($parametros, $nombre, "Activo", $start, $limit,$codEmpresa);

        $objResponse->setContent($objJson);

        return $objResponse;
    } 
    
    /**
     * @Secure(roles="ROLE_313-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redireccion a la pantalla principal de la administracion de medios de transporte de la empresa
     * @return render.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 05-11-2012
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 13-04-2016 - Se obtienen los roles permitidos de cada usuario
     */
    public function indexAction()
    {
        $rolesPermitidos = array();
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("313", "1");

        //MODULO 313 - TRANSPORTE/CREAR
        if(true === $this->get('security.context')->isGranted('ROLE_313-3'))
        {
            $rolesPermitidos[] = 'ROLE_313-3';
        }
        //MODULO 313 - TRANSPORTE/EDITAR
        if (true === $this->get('security.context')->isGranted('ROLE_313-4'))
        {
            $rolesPermitidos[] = 'ROLE_313-4';
        }
        //MODULO 313 - TRANSPORTE/CONSULTAR
        if (true === $this->get('security.context')->isGranted('ROLE_313-6'))
        {
            $rolesPermitidos[] = 'ROLE_313-6';
        }
        //MODULO 313 - TRANSPORTE/ELIMINAR
        if (true === $this->get('security.context')->isGranted('ROLE_313-8'))
        {
            $rolesPermitidos[] = 'ROLE_313-8';
        }
        
        //MODULO 313 - TRANSPORTE/SUBIR_ARCHIVOS
        if (true === $this->get('security.context')->isGranted('ROLE_313-3857'))
        {
            $rolesPermitidos[] = 'ROLE_313-3857';
        }
        
        //MODULO 313 - TRANSPORTE/VER_ARCHIVOS
        if (true === $this->get('security.context')->isGranted('ROLE_313-3858'))
        {
            $rolesPermitidos[] = 'ROLE_313-3858';
        }
        
        //MODULO 313 - TRANSPORTE/ELIMINAR_ARCHIVOS
        if (true === $this->get('security.context')->isGranted('ROLE_313-3859'))
        {
            $rolesPermitidos[] = 'ROLE_313-3859';
        }
        
        return $this->render('tecnicoBundle:InfoElementoTransporte:index.html.twig',array(
            'item'            => $entityItemMenu,
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    
    /**
     * @Secure(roles="ROLE_313-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra el listado de todos los medios de transporte creados.
     *
     * @return Response 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 05-11-2012
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 16-12-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 29-07-2016 Se agregan los filtros por disco y por region enla sesión
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 01-08-2016 Se modifica para que no se filtre por región aún ya que no se ha ingresado la información completa
     */
    public function gridAction()
    {
        $em                       = $this->getDoctrine()->getManager('telconet');
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $jsonResponse             = new JsonResponse();
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $intIdEmpresaSession      = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strPlaca                 = $objRequest->query->get('placa') ? $objRequest->query->get('placa') : "";
        $strChasis                = $objRequest->query->get('chasis') ? $objRequest->query->get('chasis') : "";
        $strMotor                 = $objRequest->query->get('motor') ? $objRequest->query->get('motor') : "";
        $strDisco                 = $objRequest->query->get('disco') ? $objRequest->query->get('disco') : "";
        $intModeloMedioTransporte = $objRequest->query->get('modeloMedioTransporte') ? $objRequest->query->get('modeloMedioTransporte') : "";
        $intStart                 = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit                 = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $serviceInfoElemento      = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento       = array( self::TIPO_ELEMENTO_VEHICULO );
        $arrayModelosElemento     = $intModeloMedioTransporte ? array( $intModeloMedioTransporte ) : array(); 
        $idOficina      = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina     = $em->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);

        $strRegion      = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }  
        
        $arrayParametros = array(
                                    'intStart'             => $intStart,
                                    'intLimit'             => $intLimit,
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => 'transporte',
                                    'criterios'            => array( 'nombre'           => $strPlaca,
                                                                     'tipoElemento'     => $arrayTiposElemento,
                                                                     'modeloElemento'   => $arrayModelosElemento,
                                                                     'detallesElemento' => array(
                                                                                            'chasis' => $strChasis,
                                                                                            'motor'  => $strMotor,
                                                                                            'disco'  => $strDisco
                                                                                           )
                                                                     )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);
        
        $jsonResponse->setData( $arrayResultados );
        
        return $jsonResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_313-2")
     * 
     * Documentación para el método 'newAction'.
     *
     * Muestra usado para mostrar el formulario vacío para crear un medio de transporte.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     */
    public function newAction()
    {
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTipoTransporte  = self::TIPO_ELEMENTO_VEHICULO;
        $arrayTmpParametros = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array($strTipoTransporte) );
        $arrayTmpResultados = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->getModeloElementosByCriterios( $arrayTmpParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
        }

        return $this->render( 'tecnicoBundle:InfoElementoTransporte:new.html.twig', array(
                                                                                            'modelosElemento'   => $arrayModelosElementos,
                                                                                            'strTipoTransporte' => $strTipoTransporte
                                                                                         ) 
                            );
    }
    
    
    /**
     * Documentación para el método 'getModelosMedioTransporteAction'.
     *
     * Función usada para retornar los modelos ingresados al tipo de transporte seleccionado por el usuario
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     */
    public function getModelosMedioTransporteAction()
    {
        $response              = new JsonResponse();
        $emInfraestructura     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest            = $this->get('request');
        $strTipoTransporte     = $objRequest->request->get('tipoTransporte') ? $objRequest->request->get('tipoTransporte') 
                                 : self::TIPO_ELEMENTO_VEHICULO;
        $intTotal              = 0;
        $arrayModelosElementos = array();
        $arrayTmpParametros    = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array($strTipoTransporte) );
        $arrayTmpResultados    = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                   ->getModeloElementosByCriterios( $arrayTmpParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
            
            foreach( $arrayModelosElementos as $objModeloElemento )
            {
                $item                      = array();
                $item['strIdentificacion'] = $objModeloElemento->getId();
                $item['strDescripcion']    = ucwords(strtolower($objModeloElemento->getNombreModeloElemento()));

                $arrayMediosTransporte[] = $item;

                $intTotal++;
            }//foreach($arrayResultados as $arrayTipoMedioTransporte)
        }//($arrayResultados)
        
        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayMediosTransporte) );
        
        return $response;
    }
    
    
    /**
     * @Secure(roles="ROLE_313-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Guarda un medio de transporte.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     * 
     * @version 1.1 16-12-2015 - Se guardan los campos para el chasis y el motor del transporte.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.2 07-01-2016 - Se guarda el tipo de vehículo: "EMPRESA" O "SUBCONTRATADO"
     *                           Si es un vehículo de la empresa, se procede a guardar los campos de la ficha técnica:
     *                              "PLAN_MANTENIMIENTO",  "ALERTA_KM" como detalles del elemento
     *                           Si el vehículo es subcontratado, se guardará la información del contrato asociado:
     *                              Se guarda como detalle del elemento el ID del contrato con la descripción "CONTRATO"
     *                              y se guarda un contrato con el contratista y la fecha de inicio y fin de contrato.
     * 
     * @author Modificado: Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.3 16-08-2016 - Se usa arreglo de parametros $arrayParametrosContrato para funcion $serviceInfoContrato->crearContrato
     * 
     * @author Modificado: Allan Suárez <arsuarez@telconet.ec>
     * @version 1.4 20-04-2020 - Se agrega información del GPS además de la filial de los vehículos
     * 
     */
    public function createAction()
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strCodEmpresaSession = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $intIdEmpresaSession  = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strUserSession       = $objSession->get('user');
        $strIpUserSession     = $objRequest->getClientIp();
        $datetimeActual       = new \DateTime('now');
        $strCodEmpresa        = $objRequest->request->get('empresa') ? $objRequest->request->get('empresa') : $strCodEmpresaSession;
        $strTipoTransporte    = $objRequest->request->get('tipoTransporte') ? $objRequest->request->get('tipoTransporte') : '';
        $intIdModeloElemento  = $objRequest->request->get('modeloElementoId') ? $objRequest->request->get('modeloElementoId') : 0;
        $strLetrasPlacas      = $objRequest->request->get('letraPlaca') ? $objRequest->request->get('letraPlaca') : '';
        $strNumerosPlacas     = $objRequest->request->get('numeroPlaca') ? $objRequest->request->get('numeroPlaca') : '';
        $strNombreElemento    = $strLetrasPlacas.'-'.$strNumerosPlacas;
        $strGps               = $objRequest->request->get('gps') ? $objRequest->request->get('gps') : '';
        $strImei              = $objRequest->request->get('imei') ? $objRequest->request->get('imei') : '';
        $strChip              = $objRequest->request->get('chip') ? $objRequest->request->get('chip') : '';
        $strDisco             = $objRequest->request->get('disco') ? $objRequest->request->get('disco') : '';
        $strAnio              = $objRequest->request->get('anio') ? $objRequest->request->get('anio') : '';
        $intFilial            = $objRequest->request->get('escogida_filial_value') ?
                                $objRequest->request->get('escogida_filial_value') : '';
        $strEsMonitoreado     = $objRequest->request->get('escogido_es_monitoreado') ?
                                $objRequest->request->get('escogido_es_monitoreado') : 'S';
        $strTipoVehiculo      = $objRequest->get('tipoVehiculo') ? $objRequest->get('tipoVehiculo') : '';
        

        $strChasis            = $objRequest->request->get('chasis') ? $objRequest->request->get('chasis') : '';
        $strMotor             = $objRequest->request->get('motor') ? $objRequest->request->get('motor') : '';

        
        
        $objMedioTransporte = new InfoElemento();
        
        
        $emInfraestructura->getConnection()->beginTransaction();	
        
        try
        {
            /*
             * Bloque que guarda el InfoElemento
             */
            $objModeloElemento = null;
            if( $intIdModeloElemento )
            {
                $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($intIdModeloElemento);
            }
            
            $objEmpresa = null;
            if( $strCodEmpresa )
            {
                $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strCodEmpresa);
            }
            
            $intIdEmpresa = 0;
            if( $objEmpresa )
            {
                $intIdEmpresa = $objEmpresa->getId();
            }
            else
            {
                $intIdEmpresa = $intIdEmpresaSession;
            }
            
            $strDescripcionElemento  = $strTipoTransporte.': '.$strNombreElemento;
            $strObservacionHistorial = '<b>Datos Nuevos<b><br>'; 
            $strObservacionHistorial .= 'Tipo: '.$strTipoTransporte.'<br>';
            $strObservacionHistorial .= 'Modelo: '.$objModeloElemento->getNombreModeloElemento().'<br>';
            $strObservacionHistorial .= 'Placa: '.$strNombreElemento.'<br>';

            $objMedioTransporte->setModeloElementoId($objModeloElemento);
            $objMedioTransporte->setNombreElemento($strNombreElemento);
            $objMedioTransporte->setDescripcionElemento($strDescripcionElemento);
            $objMedioTransporte->setEstado(self::ESTADO_ACTIVO);
            $objMedioTransporte->setFeCreacion($datetimeActual);
            $objMedioTransporte->setUsrCreacion($strUserSession);
            $objMedioTransporte->setIpCreacion($strIpUserSession);
            $emInfraestructura->persist($objMedioTransporte);
            $emInfraestructura->flush();
            /*
             * Fin del Bloque que guarda el InfoElemento
             */
            
            
            /*
             * Bloque que guarda los detalles del InfoElemento
             */
            $intIdElemento      = $objMedioTransporte->getId();
            $objElementoChipGps = null;

            //Se realiza guardado de información del GPS como un elemento de tipo CHIP
            $objTipoElementoChip = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                     ->findOneBy(array('nombreTipoElemento' => 'CHIP','estado' => 'Activo'));

            if(is_object($objTipoElementoChip)) 
            {
                $objModeloElementoChip = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                           ->findOneBy(array('tipoElementoId' => $objTipoElementoChip->getId(),
                                                                             'estado'         => 'Activo'));
                //Ingreso del chip como elemento
                if(is_object($objModeloElementoChip))                                                                             
                {
                    $objElementoChipGps = new InfoElemento(); 
                    $objElementoChipGps->setModeloElementoId($objModeloElementoChip);
                    $objElementoChipGps->setNombreElemento($strGps);
                    $objElementoChipGps->setDescripcionElemento('CHIP GPS');
                    $objElementoChipGps->setEstado(self::ESTADO_ACTIVO);
                    $objElementoChipGps->setFeCreacion($datetimeActual);
                    $objElementoChipGps->setUsrCreacion($strUserSession);
                    $objElementoChipGps->setIpCreacion($strIpUserSession);
                    $emInfraestructura->persist($objElementoChipGps);
                    $emInfraestructura->flush();

                    $arrayInfoDetalles = array( 'IMEI' => $strImei, 
                                                'CHIP' => $strChip
                                              );

                    foreach($arrayInfoDetalles as $strKey => $strValue)
                    {
                        if( $strValue )
                        {
                            $objInfoDetalleElemento = new InfoDetalleElemento();
                            $objInfoDetalleElemento->setElementoId($objElementoChipGps->getId());
                            $objInfoDetalleElemento->setDetalleNombre($strKey);
                            $objInfoDetalleElemento->setDetalleValor($strValue);
                            $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                            $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                            $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                            $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                            $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                            $emInfraestructura->persist($objInfoDetalleElemento);
                            $emInfraestructura->flush();
                            
                            $strObservacionHistorial .= $strKey.': '.$strValue.'<br>';
                        }
                    }

                    //Empresa e historial elemento para el chip
                    $objInfoEmpresaElemento = new InfoEmpresaElemento();
                    $objInfoEmpresaElemento->setElementoId($objElementoChipGps);
                    $objInfoEmpresaElemento->setEmpresaCod($intIdEmpresa);
                    $objInfoEmpresaElemento->setObservacion('CHIP GPS');
                    $objInfoEmpresaElemento->setFeCreacion($datetimeActual);
                    $objInfoEmpresaElemento->setUsrCreacion($strUserSession);
                    $objInfoEmpresaElemento->setIpCreacion($strIpUserSession);
                    $objInfoEmpresaElemento->setEstado(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoEmpresaElemento);
                    $emInfraestructura->flush();
                    
                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objElementoChipGps);
                    $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
                    $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                    $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                    $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush();
                }
            }                                                    

            if(!is_object($objElementoChipGps))
            {
                throw new NotFoundHttpException('No se pudo crear la información del Chip Gps'); 
            }            

            $arrayInfoDetalles = array( 'GPS'             => $objElementoChipGps->getId(), 
                                        'DISCO'           => $strDisco, 
                                        'ANIO'            => $strAnio, 
                                        'CHASIS'          => $strChasis, 
                                        'MOTOR'           => $strMotor ,
                                        'TIPO_VEHICULO'   => $strTipoVehiculo,
                                        'ES_MONITORIZADO' => $strEsMonitoreado
                                         );

            foreach($arrayInfoDetalles as $strKey => $strValue)
            {
                if( $strValue )
                {
                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setElementoId($intIdElemento);
                    $objInfoDetalleElemento->setDetalleNombre($strKey);
                    $objInfoDetalleElemento->setDetalleValor($strValue);
                    $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                    $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                    $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                    $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                    $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                    
                    $strObservacionHistorial .= $strKey.': '.$strValue.'<br>';
                }
            }
            /*
             * Fin del Bloque que guarda los detalles del InfoElemento
             */
            
            
            /*
             * Bloque que guarda la relación del InfoElemento con la empresa en sessión del usuario
             */
            $objInfoEmpresaElemento = new InfoEmpresaElemento();
            $objInfoEmpresaElemento->setElementoId($objMedioTransporte);
            $objInfoEmpresaElemento->setEmpresaCod($intIdEmpresa);
            $objInfoEmpresaElemento->setObservacion($strDescripcionElemento);
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
            $objInfoHistorialElemento->setElementoId($objMedioTransporte);
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
            * Bloque que guarda la ubicación por filial del InfoElemento
            */
            //Obtener la filial            
            $objInfoOficinaGrupo = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                               ->find($intFilial);

            if(!is_object($objInfoOficinaGrupo))                                               
            {
                throw new NotFoundHttpException('No se pudo obtener la información de Filial'); 
            }

            $objParroquia = $emInfraestructura->getRepository("schemaBundle:AdmiParroquia")
                                              ->findOneBy(array('cantonId' => $objInfoOficinaGrupo->getCantonId(),
                                                                'estado'   => 'Activo')); 
                                                                
            if(!is_object($objParroquia))                                               
            {
                throw new NotFoundHttpException('No se pudo obtener la información de la Parroquia'); 
            }                                                                
                                                          
            $objUbicacionElemento = new InfoUbicacion();
            $objUbicacionElemento->setLatitudUbicacion(0);
            $objUbicacionElemento->setLongitudUbicacion(0);
            $objUbicacionElemento->setDireccionUbicacion('Generico');
            $objUbicacionElemento->setAlturaSnm(0);
            $objUbicacionElemento->setParroquiaId($objParroquia);
            $objUbicacionElemento->setOficinaId($objInfoOficinaGrupo->getId());
            $objUbicacionElemento->setUsrCreacion($strUserSession);
            $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
            $objUbicacionElemento->setIpCreacion($strIpUserSession);
            $emInfraestructura->persist($objUbicacionElemento);
            $emInfraestructura->flush();

            //empresa elemento ubicacion
            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
            $objEmpresaElementoUbica->setEmpresaCod($objEmpresa->getId());
            $objEmpresaElementoUbica->setElementoId($objMedioTransporte);
            $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
            $objEmpresaElementoUbica->setUsrCreacion($strUserSession);
            $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $objEmpresaElementoUbica->setIpCreacion($strIpUserSession);
            $emInfraestructura->persist($objEmpresaElementoUbica); 
            $emInfraestructura->flush();                     
            /*
             * Guardar Ficha Tecnica
             */
            if($strTipoVehiculo=="EMPRESA")
            {
                $valueRegion             = $objRequest->request->get('escogida_region_value') ? 
                                           $objRequest->request->get('escogida_region_value') : '';
                $intIdPlanMantenimiento  = $objRequest->request->get('escogido_proceso_id') ? 
                                           $objRequest->request->get('escogido_proceso_id') : '';
                $intAlertaKM             = $objRequest->request->get('alertaKM') ? $objRequest->request->get('alertaKM') : '';
                $arrayDetallesFicha      = array(
                                                'PLAN_MANTENIMIENTO'    => $intIdPlanMantenimiento, 
                                                'ALERTA_KM'             => $intAlertaKM,
                                                'REGION'                => $valueRegion
                                                );

                $strDoc                   = 'FICHA TECNICA';
                $strObservacionHistorial  = '<b>Datos Nuevos de '.$strDoc.':<b><br>';

                foreach($arrayDetallesFicha as $strKey => $strValue)
                {
                    if( $strValue )
                    {
                        $objInfoDetalleElemento = new InfoDetalleElemento();
                        $objInfoDetalleElemento->setElementoId($intIdElemento);
                        $objInfoDetalleElemento->setDetalleNombre($strKey);
                        $objInfoDetalleElemento->setDetalleValor($strValue);
                        $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                        $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                        $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                        $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                        $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                        $emInfraestructura->persist($objInfoDetalleElemento);
                        $emInfraestructura->flush();

                        $strObservacionHistorial .= $strKey.': '.$strValue.'<br>';
                    }
                }


                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objMedioTransporte);
                $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
                $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                $emInfraestructura->persist($objInfoHistorialElemento);
                $emInfraestructura->flush();
                
            }//if($strTipoVehiculo=="EMPRESA")
            /*
             * Guardar Contrato
             */
            else if($strTipoVehiculo=="SUBCONTRATADO")
            {          
                $objTipoContrato        = $emComercial ->getRepository('schemaBundle:AdmiTipoContrato')
                                                        ->findOneBy(array(
                                                            'empresaCod'                => $intIdEmpresa,
                                                            'descripcionTipoContrato'   => self::TIPO_CONTRATO_VEHICULO
                                                            ));

                $intIdTipoContrato      = $objTipoContrato->getId() ? $objTipoContrato->getId() : 0;

                $intIdCliente           = $objRequest->get('infocontratoextratype_idcontratista');
                $intIdPersonaEmpresaRol = $objRequest->get('infocontratoextratype_personaEmpresaRolId');
                
                $formaPago              = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')
                                                    ->findOneByDescripcionFormaPago('EFECTIVO');
                $intIdFormaPago         = $formaPago->getId() ? $formaPago->getId(): 0;

                $clientIp               = $objRequest->getClientIp();
                $session                = $objRequest->getSession();
                $usrCreacion            = $session->get('user');
                $codEmpresa             = $session->get('idEmpresa');
                $prefijoEmpresa         = $session->get('prefijoEmpresa');
                $idOficina              = $session->get('idOficina');
                $strfechaInicioContrato = $objRequest->get('fecha_inicio_contrato')? $objRequest->get('fecha_inicio_contrato') :'' ;
                $strfechaFinContrato    = $objRequest->get('fecha_fin_contrato') ? $objRequest->get('fecha_fin_contrato') : '' ;
                
                $datetimeFechaInicioContrato = new \DateTime();
                if($strfechaInicioContrato!="")
                {
                    list($anioInicioContrato,$mesInicioContrato,$diaInicioContrato)=explode("-",$strfechaInicioContrato);
                    $datetimeFechaInicioContrato->setDate($anioInicioContrato,$mesInicioContrato,$diaInicioContrato);
                }
                $datetimeFechaFinContrato = new \DateTime();
                if($strfechaFinContrato!="")
                {
                    list($anioFinContrato,$mesFinContrato,$diaFinContrato)=explode("-",$strfechaFinContrato);
                    $datetimeFechaFinContrato->setDate($anioFinContrato,$mesFinContrato,$diaFinContrato);
                }

                $check                  = NULL;
                $clausula               = NULL;
                $datosContrato          = array(
                                            'codigoNumeracionVE'    => 'CONVE',
                                            'tipoContratoId'        => $intIdTipoContrato,
                                            'feInicioContrato'      => $datetimeFechaInicioContrato,
                                            'feFinContratoPost'     => $datetimeFechaFinContrato,
                                            'idcliente'             => $intIdCliente,
                                            'personaEmpresaRolId'   => $intIdPersonaEmpresaRol,
                                            'valorEstado'           => 'Activo',
                                            'formaPagoId'           => $intIdFormaPago,
                                            'datos_form_files'      => array(),
                                            'arrayTipoDocumentos'   => array(),
                                            'tipoCuentaId'          => '',
                                            'bancoTipoCuentaId'     => '',
                                            'numeroCtaTarjeta'      => '',
                                            'titularCuenta'         => '',  
                                            'valorAnticipo'         => '',
                                            'numeroContratoEmpPub'  => ''
                                        );
                try
                {
                    /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                    $serviceInfoContrato = $this->get('comercial.InfoContrato');
                    $arrayParametrosContrato                   = array();
                    $arrayParametrosContrato['codEmpresa']     = $codEmpresa;
                    $arrayParametrosContrato['prefijoEmpresa'] = $prefijoEmpresa; 
                    $arrayParametrosContrato['idOficina']      = $idOficina; 
                    $arrayParametrosContrato['usrCreacion']    = $usrCreacion; 
                    $arrayParametrosContrato['clientIp']       = $clientIp; 
                    $arrayParametrosContrato['datos_form']     = $datosContrato; 
                    $arrayParametrosContrato['check']          = $check; 
                    $arrayParametrosContrato['clausula']       = $clausula;
                    $entity = $serviceInfoContrato->crearContrato($arrayParametrosContrato);
                    
                    $intIdContrato            = $entity->getId();
                    $strDoc                   = 'CONTRATO';
                    $strObservacionHistorial  = '<b>Datos Nuevos de '.$strDoc.':<b><br>';
                    $objInfoDetalleElemento   = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setElementoId($intIdElemento);
                    $objInfoDetalleElemento->setDetalleNombre('CONTRATO');
                    $objInfoDetalleElemento->setDetalleValor($intIdContrato);
                    $objInfoDetalleElemento->setDetalleDescripcion('CONTRATO');
                    $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                    $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                    $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                    $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                    $strObservacionHistorial .= 'ID de Contrato: '.$intIdContrato.'<br>';

                    $strNombreContratista = "";
                    $obPersonaContrato=$entity->getPersonaEmpresaRolId()->getPersonaId();
                    if($obPersonaContrato->getNombres()!="" && $obPersonaContrato->getApellidos()!="")
                    {
                        $strNombreContratista.= $obPersonaContrato->getNombres()." ".$obPersonaContrato->getApellidos();

                    }
                    else
                    {
                        $strNombreContratista.= $obPersonaContrato->getRazonSocial();
                    }

                    $strObservacionHistorial .= 'Contratista: '.$strNombreContratista.'<br>';
                    $strObservacionHistorial .= 'Tipo de Contrato:'.self::TIPO_CONTRATO_VEHICULO.'<br>';
                    $strObservacionHistorial .= 'Fecha de Inicio de Contrato: '.date_format($entity->getFeAprobacion(),'d-m-Y').'<br>';
                    $strObservacionHistorial .= 'Fecha de Fin de Contrato: '.date_format($entity->getFeFinContrato(),'d-m-Y').'<br>';
                    $strObservacionHistorial .= 'Estado: '.$entity->getEstado().'<br>';

                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objMedioTransporte);
                    $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
                    $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                    $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                    $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush();

                }
                catch (\Exception $e)
                {   
                    error_log($e->getMessage());
                    $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
                    return $this->redirect($this->generateUrl('elementotransporte_show', array('id' => $intIdElemento)));
                }

            }//if($strTipoVehiculo=="SUBCONTRATADO")
            $emInfraestructura->getConnection()->commit();
            $emInfraestructura->getConnection()->close();
            return $this->redirect($this->generateUrl('elementotransporte_show', array('id' => $intIdElemento)));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }//try
        
        
        $arrayModelosElementos = array();
        
        $arrayTmpParametros = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => $strTipoTransporte );
        $arrayTmpResultados = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->getModeloElementosByCriterios( $arrayTmpParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
        }
        

        return $this->render( 'tecnicoBundle:InfoElementoTransporte:new.html.twig', array(
                                                                                            'modelosElemento'   => $arrayModelosElementos,
                                                                                            'strTipoTransporte' => $strTipoTransporte
                                                                                         ) 
                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_313-6")
     * 
     * Documentación para el método 'showAction'.
     *
     * Muestra la información de un medio de transporte.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.1 10-01-2016 - Se muestra el tipo de vehículo, y si tiene un tipo de vehículo asociado, se mostrará
     *                           la información de la ficha técnica o del contrato según corresponda. 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>                          
     * @version 1.2 16-12-2015 - Se pueden visualizar los campos chasis y motor.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 10-08-2016 - Se modifica para que el plan de mantenimiento no sea un campo obligatorio cuando el vehículo es de la empresa.
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 20-04-2020 - Se modifica para que devuelva la información completa de la camioneta
     *
     */
    public function showAction($id)
    {
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objMedioTransporte )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );
                
        $arrayDetalle = array('GPS' => '', 'DISCO' => '', 'ANIO' => '', 'CHASIS' => '', 'IMEI' => '', 'CHIP' => '',
                              'MOTOR' => '','TIPO_VEHICULO' => '','REGION'=>'','ES_MONITORIZADO'=>'', 'FILIAL' => '');
        
        if( $objDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $strValor = $objDetalle->getDetalleValor();

                //Si es GPS, obtenemos la informacion complementaria del elemento chip
                if($objDetalle->getDetalleNombre() == 'GPS')
                {
                    $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                     ->find($objDetalle->getDetalleValor());
                    if(is_object($objElemento))
                    {
                        $strValor = $objElemento->getNombreElemento();

                        $arrayDetallesGps = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                              ->findBy( array('elementoId' => $objElemento->getId(), 
                                                                              'estado'     => self::ESTADO_ACTIVO) );
                        foreach( $arrayDetallesGps as $objDetalleGps  )
                        {
                            $arrayDetalle[$objDetalleGps->getDetalleNombre()] = $objDetalleGps->getDetalleValor();
                        }                                                                              
                    }
                }
                $arrayDetalle[$objDetalle->getDetalleNombre()] = $strValor;
            }
        }

        //Obtener la filial del elemento
        $objEmpresaElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                     ->findOneBy(array('elementoId' => $id));
        if(is_object($objEmpresaElementoUbica))
        {
            $objUbicacion = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                              ->find($objEmpresaElementoUbica->getUbicacionId()->getId());
            if(is_object($objUbicacion) && !is_null($objUbicacion->getOficinaId()))
            {
                $objOficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($objUbicacion->getOficinaId());

                if(is_object($objOficina))
                {
                    $arrayDetalle['FILIAL'] = $objOficina->getNombreOficina();
                }
            }
        }
        
        $entityContrato             = array();
        $strNombrePlanMantenimiento = "N/A";
        if($arrayDetalle['TIPO_VEHICULO']=='EMPRESA')
        {
            if(isset($arrayDetalle['PLAN_MANTENIMIENTO']))
            {
                if($arrayDetalle['PLAN_MANTENIMIENTO'])
                {
                    $idPlanMantenimiento                    = $arrayDetalle['PLAN_MANTENIMIENTO'];
                    $entityPlanMantenimiento                = $emComercial->getRepository('schemaBundle:AdmiProceso')->find($idPlanMantenimiento);
                    if($entityPlanMantenimiento)
                    {
                        $strNombrePlanMantenimiento=$entityPlanMantenimiento->getNombreProceso();
                    }

                }
            }
        }
        else if($arrayDetalle['TIPO_VEHICULO']=='SUBCONTRATADO')
        {
            if($arrayDetalle['CONTRATO'])
            {
                $idContrato        = $arrayDetalle['CONTRATO'];
                $entityContrato    = $emComercial->getRepository('schemaBundle:InfoContrato')->find($idContrato);
            }      
        }
        
        
        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                              ->findOneBy( array ('elementoId' => $objMedioTransporte, 'estado' => self::ESTADO_ACTIVO) );
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

        return $this->render('tecnicoBundle:InfoElementoTransporte:show.html.twig', array(
                                                                                        'medioTransporte'           => $objMedioTransporte,
                                                                                        'detalles'                  => $arrayDetalle,
                                                                                        'empresa'                   => $strNombreEmpresa,
                                                                                        'contrato'                  => $entityContrato,
                                                                                        'nombrePlanMantenimiento'   => $strNombrePlanMantenimiento
                                                                                        )

                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_313-4")
     * 
     * Documentación para el método 'editAction'.
     *
     * Muestra la información de un medio de transporte al cual se le va a actualizar la información.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.1 16-12-2015 - Se pueden editar los campos para el motor y el chasis del transporte.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 11-01-2016 - Permite seleccionar el tipo de vehículo y lo presenta como un campo obligatorio
     *                           y según sea el caso se permitirá editar la información de la ficha técnica o del contrato. 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 10-08-2016 - Se modifica para que el plan de mantenimiento no sea un campo obligatorio cuando el vehículo es de la empresa.
     * 
     * @author Modificado: Allan Suárez <arsuarez@telconet.ec>
     * @version 1.4 22-04-2020 - Se modifica para que la pantalla de actualización muestre información complementaria del chip, imei y filial
     */
    public function editAction($id)
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objMedioTransporte )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles  = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                          ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );
        

        

        $arrayDetalle = array('GPS' => '', 'DISCO' => '', 'ANIO' => '', 'CHASIS' => '', 'IMEI' => '','CHIP' => '',
                            'MOTOR' => '','TIPO_VEHICULO'=>'','REGION'=>'', 'FILIAL' => '','ES_MONITORIZADO' => '',
                            'FILIAL_NOMBRE' => '' );
        

        if( $objDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $strValor = $objDetalle->getDetalleValor();
                //Si es GPS, obtenemos la informacion complementaria del elemento chip
                if($objDetalle->getDetalleNombre() == 'GPS')
                {
                    $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                     ->find($objDetalle->getDetalleValor());
                    if(is_object($objElemento))
                    {
                        $strValor = $objElemento->getNombreElemento();

                        $arrayDetalle[$objDetalle->getDetalleNombre()] = $strValor;

                        $arrayDetallesGps = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                              ->findBy( array('elementoId' => $objElemento->getId(), 
                                                                              'estado'     => self::ESTADO_ACTIVO) );
                        foreach( $arrayDetallesGps as $objDetalleGps  )
                        {
                            $arrayDetalle[$objDetalleGps->getDetalleNombre()] = $objDetalleGps->getDetalleValor();
                        }                                                                              
                    }
                }
                else
                {
                    $arrayDetalle[$objDetalle->getDetalleNombre()] = $strValor;
                }                
            }
        }

        //Obtener la filial del elemento
        $objEmpresaElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                     ->findOneBy(array('elementoId' => $id));
        if(is_object($objEmpresaElementoUbica))
        {
            $objUbicacion = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                              ->find($objEmpresaElementoUbica->getUbicacionId()->getId());
            if(is_object($objUbicacion) && !is_null($objUbicacion->getOficinaId()))
            {
                $objOficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($objUbicacion->getOficinaId());

                if(is_object($objOficina))
                {
                    $arrayDetalle['FILIAL']        = $objOficina->getId();
                    $arrayDetalle['FILIAL_NOMBRE'] = $objOficina->getNombreOficina();
                }
            }
        }
        
        $entityContrato             = array();
        $datosContrato              = array();
        $strNombrePlanMantenimiento = "N/A";
        $idPlanMantenimiento        = 0;
        if($arrayDetalle['TIPO_VEHICULO']=='EMPRESA')
        {
            if(isset($arrayDetalle['PLAN_MANTENIMIENTO']))
            {
                if($arrayDetalle['PLAN_MANTENIMIENTO'])
                {
                    $idPlanMantenimiento                    = $arrayDetalle['PLAN_MANTENIMIENTO'];
                    $entityPlanMantenimiento                = $emSoporte->getRepository('schemaBundle:AdmiProceso')->find($idPlanMantenimiento);
                    if($entityPlanMantenimiento)
                    {
                        $strNombrePlanMantenimiento             = $entityPlanMantenimiento->getNombreProceso();
                    }

                }
            }
        }
        else if($arrayDetalle['TIPO_VEHICULO']=='SUBCONTRATADO')
        {
            if($arrayDetalle['CONTRATO'])
            {
                $idContrato        = $arrayDetalle['CONTRATO'];
                $entityContrato    = $emComercial->getRepository('schemaBundle:InfoContrato')->find($idContrato);
                if($entityContrato)
                {
                    $objPersonaEmpresaRolContrato                       = $entityContrato->getPersonaEmpresaRolId();
                    $objPersonaContrato                                 = $objPersonaEmpresaRolContrato->getPersonaId();
                    $datosContrato['nombreContratista']                 = sprintf("%s",$objPersonaContrato);
                    $datosContrato['idPersonaEmpresaRolContratista']    = $objPersonaEmpresaRolContrato->getId();
                    $datosContrato['idPersonaContratista']              = $objPersonaContrato->getId();
                    $datosContrato['fechaInicio']                       = $entityContrato->getFeAprobacion();
                    $datosContrato['fechaFin']                          = $entityContrato->getFeFinContrato();
                }


            }
        }
        
        $arrayModelosElementos = array();
        
        $strNombreElemento   = $objMedioTransporte->getNombreElemento();
        $arrayNombreElemento = explode('-', $strNombreElemento);
        $strLetrasPlaca      = $arrayNombreElemento[0];
        $strNumerosPlaca     = $arrayNombreElemento[1];
        $strTipoTransporte   = $objMedioTransporte->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
        
        $arrayTmpParametros = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array($strTipoTransporte) );
        $arrayTmpResultados = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->getModeloElementosByCriterios( $arrayTmpParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
        }
        
        return $this->render('tecnicoBundle:InfoElementoTransporte:edit.html.twig', array(
                                                                                'medioTransporte'                   => $objMedioTransporte,
                                                                                'detalles'                          => $arrayDetalle,
                                                                                'modelosElemento'                   => $arrayModelosElementos,
                                                                                'letrasPlaca'                       => $strLetrasPlaca,
                                                                                'numerosPlaca'                      => $strNumerosPlaca,
                                                                                'strTipoTransporte'                 => $strTipoTransporte,
                                                                                'contrato'                          => $datosContrato,
                                                                                'region'                            => $arrayDetalle["REGION"],
                                                                                'idPlanMantenimiento'               => $idPlanMantenimiento,
                                                                                'nombrePlanMantenimiento'           => $strNombrePlanMantenimiento
                                                                                        )
                            );
    }
    
    
    /**
     * @Secure(roles="ROLE_313-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información de un medio de transporte.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 16-12-2015 - Se actualizan los valores para motor y chasis.
     *  
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 12-01-2016 - Se actualiza el tipo de vehículo: "EMPRESA" O "SUBCONTRATADO" con la respectiva ficha técnica o contrato
     * 
     * @author Modificado: Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.3 16-08-2016 - Se usa arreglo de parametros $arrayParametrosContrato para funcion $serviceInfoContrato->crearContrato
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 22-04-2020 - Se modifica para actualizar información en base a datos de IMEI, CHIP, FILIAL y se corrige lógica para detalles
     *
     */
    public function updateAction($id)
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $strCodEmpresaSession = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $intIdEmpresaSession  = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strUserSession       = $objSession->get('user');
        $strIpUserSession     = $objRequest->getClientIp();
        $datetimeActual       = new \DateTime('now');
        $strCodEmpresa        = $objRequest->request->get('empresa') ? $objRequest->request->get('empresa') : $strCodEmpresaSession;
        $strTipoTransporte    = $objRequest->request->get('tipoTransporte') ? $objRequest->request->get('tipoTransporte') : '';
        $intIdModeloElemento  = $objRequest->request->get('modeloElementoId') ? $objRequest->request->get('modeloElementoId') : 0;
        $strLetrasPlacas      = $objRequest->request->get('letraPlaca') ? $objRequest->request->get('letraPlaca') : '';
        $strNumerosPlacas     = $objRequest->request->get('numeroPlaca') ? $objRequest->request->get('numeroPlaca') : '';
        $strNombreElemento    = $strLetrasPlacas.'-'.$strNumerosPlacas;
        $strGps               = $objRequest->request->get('gps') ? $objRequest->request->get('gps') : '';
        $strImei              = $objRequest->request->get('imei') ? $objRequest->request->get('imei') : '';
        $strChip              = $objRequest->request->get('chip') ? $objRequest->request->get('chip') : '';
        $strDisco             = $objRequest->request->get('disco') ? $objRequest->request->get('disco') : '';
        $strAnio              = $objRequest->request->get('anio') ? $objRequest->request->get('anio') : '';
        $intFilial            = $objRequest->request->get('escogida_filial_value') ?
                                $objRequest->request->get('escogida_filial_value') : '';
        $strEsMonitoreado     = $objRequest->request->get('escogido_es_monitoreado') ?
                                $objRequest->request->get('escogido_es_monitoreado') : 'N';
        $strTipoVehiculo      = $objRequest->request->get('tipoVehiculo') ? $objRequest->request->get('tipoVehiculo') : '';
        

        $strChasis            = $objRequest->request->get('chasis') ? $objRequest->request->get('chasis') : '';
        $strMotor             = $objRequest->request->get('motor') ? $objRequest->request->get('motor') : '';
            

        $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objMedioTransporte )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $strNombreFilial = '';

        if(!empty($intFilial))
        {
            $objInfoOficinaGrupo = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                               ->find($intFilial);
            
            if(is_object($objInfoOficinaGrupo))                                               
            {
                $strNombreFilial = $objInfoOficinaGrupo->getNombreOficina();
            }
        }        

        $arrayInfoDetalleGps    = array('IMEI'  => $strImei, 'CHIP' => $strChip);
        
        $objDetalleTipoVehiculoAntiguo = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy( array(
                                                                                'elementoId'    => $id, 
                                                                                'estado'        => self::ESTADO_ACTIVO,
                                                                                'detalleNombre' => 'TIPO_VEHICULO'));
        $strTipoVehiculoAntiguo = '';
        $strDatosAntiguosTipoVehiculo='';
        if($objDetalleTipoVehiculoAntiguo)
        {

            $strDatosAntiguosTipoVehiculo   .= 'TIPO_VEHICULO: '.$objDetalleTipoVehiculoAntiguo->getDetalleValor().'<br/>';
            $strTipoVehiculoAntiguo          = $objDetalleTipoVehiculoAntiguo->getDetalleValor();

        }
        
        $objInfoEmpresaElemento = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                    ->findOneBy( array ('elementoId' => $objMedioTransporte, 'estado' => self::ESTADO_ACTIVO));
        
        
        $emInfraestructura->getConnection()->beginTransaction();	
        
        try
        {
            /*
             * Bloque que actualiza la información del InfoElemento seleccionado por el usuario
             */   
            $objModeloElemento = null;
            if( $intIdModeloElemento )
            {
                $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($intIdModeloElemento);
            }
            
            $objEmpresa = null;
            if( $strCodEmpresa )
            {
                $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strCodEmpresa);
            }
            
            $intIdEmpresa = 0;
            if( $objEmpresa )
            {
                $intIdEmpresa = $objEmpresa->getId();
            }
            else
            {
                $intIdEmpresa = $intIdEmpresaSession;
            }
            
            $strDescripcionElemento = $strTipoTransporte.': '.$strNombreElemento;
            $strDatosAntiguos       = '<b>Datos Anteriores<b><br/>';
            $strDatosAntiguos       .= 'Tipo: '.$objMedioTransporte->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento().'<br/>';
            $strDatosAntiguos       .= 'Modelo: '.$objMedioTransporte->getModeloElementoId()->getNombreModeloElemento().'<br>';
            $strDatosAntiguos       .= 'Placa: '.$objMedioTransporte->getNombreElemento().'<br/>';
            
            $strDatosNuevos         = '<b>Datos Nuevos<b><br/>';     
            $strDatosNuevos         .= 'Tipo: '.$strTipoTransporte.'<br/>';
            $strDatosNuevos         .= 'Modelo: '.$objModeloElemento->getNombreModeloElemento().'<br/>';
            $strDatosNuevos         .= 'Placa: '.$strNombreElemento.'<br/>';
               
            $objMedioTransporte->setModeloElementoId($objModeloElemento);
            $objMedioTransporte->setNombreElemento($strNombreElemento);
            $objMedioTransporte->setDescripcionElemento($strDescripcionElemento);
            $emInfraestructura->persist($objMedioTransporte);
            $emInfraestructura->flush();

            $objInfoHistorialElemento = new InfoHistorialElemento();
            $objInfoHistorialElemento->setElementoId($objMedioTransporte);
            $objInfoHistorialElemento->setObservacion($strDatosAntiguos.$strDatosNuevos);
            $objInfoHistorialElemento->setFeCreacion($datetimeActual);
            $objInfoHistorialElemento->setUsrCreacion($strUserSession);
            $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
            $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
            $emInfraestructura->persist($objInfoHistorialElemento);
            $emInfraestructura->flush(); 
            /*
             * Fin del Bloque que actualiza la información del InfoElemento seleccionado por el usuario
             */
            
            /*
             * Bloque que guarda los detalles del InfoElemento GPS
             */
            $intIdElemento      = $objMedioTransporte->getId();  
            $intIdElementoGps   = 0;
            $strDatosAntiguos   = '';
            $strDatosNuevos     = '';
            $objElementoChipGps = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->findOneBy(array('nombreElemento' => $strGps,
                                                                    'estado'         => 'Activo'));
            
            if(is_object($objElementoChipGps))
            {
                $intIdElementoGps = $objElementoChipGps->getId();

                $arrayDetallesGps = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findBy( array('elementoId' => $intIdElementoGps, 
                                                                      'estado'     => self::ESTADO_ACTIVO) );
                foreach($arrayDetallesGps as $objDetalle)
                {
                    foreach($arrayInfoDetalleGps as $strKey => $strValue)
                    {
                        if($strKey == $objDetalle->getDetalleNombre() && $strValue != $objDetalle->getDetalleValor())
                        {                                                            
                            $strDatosAntiguos .= $strKey.': '.$strValue.'<br/>';
                            $strDatosNuevos   .= $strKey.': '.$objDetalle->getDetalleValor().'<br/>';

                            $objInfoDetalleElemento = new InfoDetalleElemento();
                            $objInfoDetalleElemento->setElementoId($intIdElementoGps);
                            $objInfoDetalleElemento->setDetalleNombre($strKey);
                            $objInfoDetalleElemento->setDetalleValor($strValue);
                            $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                            $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                            $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                            $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                            $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                            $emInfraestructura->persist($objInfoDetalleElemento);
                            $emInfraestructura->flush();                                
                            
                            $objDetalle->setEstado(self::ESTADO_ELIMINADO);
                            $emInfraestructura->persist($objDetalle);
                            $emInfraestructura->flush();

                            $objInfoHistorialElemento = new InfoHistorialElemento();
                            $objInfoHistorialElemento->setElementoId($objElementoChipGps);
                            $objInfoHistorialElemento->setObservacion($strDatosAntiguos.$strDatosNuevos);
                            $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                            $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                            $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                            $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                            $emInfraestructura->persist($objInfoHistorialElemento);
                            $emInfraestructura->flush();                                                            
                        }
                    }
                }
                                                                                      
            }                                                                   
            else
            {                
                //Se realiza guardado de información del GPS como un elemento de tipo CHIP cuando este no existe
                $objTipoElementoChip = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                         ->findOneBy(array('nombreTipoElemento' => 'CHIP','estado' => 'Activo'));

                if(is_object($objTipoElementoChip)) 
                {
                    $objModeloElementoChip = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                               ->findOneBy(array('tipoElementoId' => $objTipoElementoChip->getId(),
                                                                                 'estado'         => 'Activo'));
                    //Ingreso del chip como elemento
                    if(is_object($objModeloElementoChip))                                                                             
                    {
                        $objElementoChipGps = new InfoElemento(); 
                        $objElementoChipGps->setModeloElementoId($objModeloElementoChip);
                        $objElementoChipGps->setNombreElemento($strGps);
                        $objElementoChipGps->setDescripcionElemento('CHIP GPS');
                        $objElementoChipGps->setEstado(self::ESTADO_ACTIVO);
                        $objElementoChipGps->setFeCreacion($datetimeActual);
                        $objElementoChipGps->setUsrCreacion($strUserSession);
                        $objElementoChipGps->setIpCreacion($strIpUserSession);
                        $emInfraestructura->persist($objElementoChipGps);
                        $emInfraestructura->flush();

                        $arrayInfoDetallesGps = array( 'IMEI' => $strImei, 
                                                       'CHIP' => $strChip
                                                     );

                        foreach($arrayInfoDetallesGps as $strKey => $strValue)
                        {
                            if( $strValue )
                            {
                                $objInfoDetalleElemento = new InfoDetalleElemento();
                                $objInfoDetalleElemento->setElementoId($objElementoChipGps->getId());
                                $objInfoDetalleElemento->setDetalleNombre($strKey);
                                $objInfoDetalleElemento->setDetalleValor($strValue);
                                $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                                $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                                $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                                $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                                $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                                $emInfraestructura->persist($objInfoDetalleElemento);
                                $emInfraestructura->flush();

                                $strObservacionHistorial .= $strKey.': '.$strValue.'<br>';
                            }
                        }

                        //Empresa e historial elemento para el chip
                        $objInfoEmpresaElemento = new InfoEmpresaElemento();
                        $objInfoEmpresaElemento->setElementoId($objElementoChipGps);
                        $objInfoEmpresaElemento->setEmpresaCod($intIdEmpresa);
                        $objInfoEmpresaElemento->setObservacion('CHIP GPS');
                        $objInfoEmpresaElemento->setFeCreacion($datetimeActual);
                        $objInfoEmpresaElemento->setUsrCreacion($strUserSession);
                        $objInfoEmpresaElemento->setIpCreacion($strIpUserSession);
                        $objInfoEmpresaElemento->setEstado(self::ESTADO_ACTIVO);
                        $emInfraestructura->persist($objInfoEmpresaElemento);
                        $emInfraestructura->flush();

                        $objInfoHistorialElemento = new InfoHistorialElemento();
                        $objInfoHistorialElemento->setElementoId($objElementoChipGps);
                        $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
                        $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                        $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                        $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                        $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                        $emInfraestructura->persist($objInfoHistorialElemento);
                        $emInfraestructura->flush();

                        $intIdElementoGps = $objElementoChipGps->getId();
                    }
                }                                                    

                if(!is_object($objElementoChipGps))
                {
                    throw new NotFoundHttpException('No se pudo crear la información del Chip Gps'); 
                }    
            }
          
            /*
             * Bloque que actualiza los detalles del InfoElemento seleccionado por el usuario
             */      
            
            $arrayInfoDetalles      = array('GPS'  => $intIdElementoGps, 'DISCO' => $strDisco,
                                            'ANIO' => $strAnio, 'CHASIS' => $strChasis, 
                                            'MOTOR' => $strMotor ,'TIPO_VEHICULO' => $strTipoVehiculo,
                                            'ES_MONITORIZADO' => $strEsMonitoreado                                        
                                        );

            $arrayObjDetalles       = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );

            $boolTieneCambios = false;
            $strDatosAntiguos = '';
            $strDatosNuevos   = '';

            //Se recorren los detalles para verificar si no hay nuevos                                                            
            foreach( $arrayObjDetalles as $objDetalle  )
            {
                //recorren los valores que se envian en la edición
                foreach($arrayInfoDetalles as $strKey => $strValue)
                {
                    //Si el key es el mismo se verifica
                    ////Si es diferente de acuerdo al tipo de detalle se crea uno nuevo y se da de baja al anterior
                    if($strKey == $objDetalle->getDetalleNombre() && $strValue != $objDetalle->getDetalleValor())
                    {                  
                        $boolTieneCambios = true;
                        $strDatosAntiguos .= $strKey.': '.$objDetalle->getDetalleValor().'<br/>';
                        $strDatosNuevos   .= $strKey.': '.$strValue.'<br/>';
                                                
                        $objInfoDetalleElemento = new InfoDetalleElemento();
                        $objInfoDetalleElemento->setElementoId($intIdElemento);
                        $objInfoDetalleElemento->setDetalleNombre($strKey);

                        if($strKey == 'GPS')
                        {
                            $objInfoDetalleElemento->setDetalleValor($intIdElementoGps);
                        }
                        else
                        {
                            $objInfoDetalleElemento->setDetalleValor($strValue);
                        }

                        $objInfoDetalleElemento->setDetalleValor($strValue);
                        $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                        $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                        $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                        $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                        $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                        $emInfraestructura->persist($objInfoDetalleElemento);
                        $emInfraestructura->flush();

                        $objDetalle->setEstado(self::ESTADO_ELIMINADO);
                        $emInfraestructura->persist($objDetalle);
                        $emInfraestructura->flush();                        
                    }
                }
            }

            if($boolTieneCambios)
            {
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objMedioTransporte);
                $objInfoHistorialElemento->setObservacion(
                                                        'DATOS ANTIGUOS: </br>'.$strDatosAntiguos.'</br>'.
                                                        'DATOS NUEVOS: </br>'.$strDatosNuevos.'</br>');
                $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                $emInfraestructura->persist($objInfoHistorialElemento);
                $emInfraestructura->flush();

                $boolTieneCambios = false;
            }
            
            /*
             * Bloque que actualiza la relación del InfoElemento con la empresa seleccionada por el usuario
             */
            if( !$objInfoEmpresaElemento )
            {
                $objInfoEmpresaElemento = new InfoEmpresaElemento();
                $objInfoEmpresaElemento->setFeCreacion($datetimeActual);
                $objInfoEmpresaElemento->setUsrCreacion($strUserSession);
                $objInfoEmpresaElemento->setIpCreacion($strIpUserSession);
                $objInfoEmpresaElemento->setEstado(self::ESTADO_ACTIVO);
            }//( !$objInfoEmpresaElemento )
            
            $objInfoEmpresaElemento->setElementoId($objMedioTransporte);
            $objInfoEmpresaElemento->setEmpresaCod($intIdEmpresa);
            $objInfoEmpresaElemento->setObservacion($strDescripcionElemento);
            
            $emInfraestructura->persist($objInfoEmpresaElemento);
            $emInfraestructura->flush();
            /*
             * Fin del Bloque que actualiza la relación del InfoElemento con la empresa seleccionada por el usuario
             */

            //Obtener la ubicacion del elemento
            $objEmpresaElementoUbica = $emInfraestructura->getRepository("schemaBundle:InfoEmpresaElementoUbica")
                                                         ->findOneByElementoId($intIdElemento);

            $objInfoUbicacion = null;

            if(is_object($objEmpresaElementoUbica))
            {
                $objInfoUbicacion = $objEmpresaElementoUbica->getUbicacionId();
            }                                                         

            //Se verifica cambio en la filial
            $objInfoOficinaGrupo = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                               ->find($intFilial);

            if(!is_object($objInfoOficinaGrupo))                                               
            {
                throw new NotFoundHttpException('No se pudo obtener la información de Filial'); 
            }

            $objParroquia = $emInfraestructura->getRepository("schemaBundle:AdmiParroquia")
                                              ->findOneBy(array('cantonId' => $objInfoOficinaGrupo->getCantonId(),
                                                                'estado'   => 'Activo')); 
                                                                
            if(!is_object($objParroquia))                                               
            {
                throw new NotFoundHttpException('No se pudo obtener la información de la Parroquia'); 
            }            
            
            if(is_object($objInfoUbicacion))
            {
                $objInfoUbicacion->setParroquiaId($objParroquia);
                $objInfoUbicacion->setOficinaId($objInfoOficinaGrupo->getId());
                $emInfraestructura->persist($objInfoUbicacion);
                $emInfraestructura->flush();
            }
            else
            {
                $objUbicacionElemento = new InfoUbicacion();
                $objUbicacionElemento->setLatitudUbicacion(0);
                $objUbicacionElemento->setLongitudUbicacion(0);
                $objUbicacionElemento->setDireccionUbicacion('Generico');
                $objUbicacionElemento->setAlturaSnm(0);
                $objUbicacionElemento->setParroquiaId($objParroquia);
                $objUbicacionElemento->setOficinaId($objInfoOficinaGrupo->getId());
                $objUbicacionElemento->setUsrCreacion($strUserSession);
                $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                $objUbicacionElemento->setIpCreacion($strIpUserSession);
                $emInfraestructura->persist($objUbicacionElemento);
                $emInfraestructura->flush();

                //empresa elemento ubicacion
                $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                $objEmpresaElementoUbica->setEmpresaCod($objEmpresa->getId());
                $objEmpresaElementoUbica->setElementoId($objMedioTransporte);
                $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                $objEmpresaElementoUbica->setUsrCreacion($strUserSession);
                $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                $objEmpresaElementoUbica->setIpCreacion($strIpUserSession);
                $emInfraestructura->persist($objEmpresaElementoUbica); 
                $emInfraestructura->flush();  
            }

            $arrayInfoDetallesFilial = array('FILIAL'       => $objInfoOficinaGrupo->getId(),
                                             'FILIAL_NOMBRE'=> $objInfoOficinaGrupo->getNombreOficina());
                                                                                                 
            if($strTipoVehiculo!="")
            {
                $strDatosNuevos       .= 'TIPO_VEHICULO: '.  $strTipoVehiculo .'<br/>';
            }            
            /*
             * Fin del Bloque que guarda el historial del InfoElemento
             */
            
            $strDatosAntiguosPorTipoVeh  = '';
            
            
            //Guardar Ficha Tecnica
            if($strTipoVehiculo=="EMPRESA")
            {
                /*Verificar si tenía anteriormente en el detalle TIPO_VEHICULO="EMPRESA"
                 * 
                 */
                //Si tenía TIPO_VEHICULO="EMPRESA" 
                    //actualizar los datos de los otros detalles 'PLAN_MANTENIMIENTO','ALERTA_KM','REGION'
                
                $valueRegion             = $objRequest->request->get('escogida_region_value') ? 
                                           $objRequest->request->get('escogida_region_value') : '';
                $intIdPlanMantenimiento  = $objRequest->request->get('escogido_proceso_id') ? 
                                           $objRequest->request->get('escogido_proceso_id') : '';
                $intAlertaKM             = $objRequest->request->get('alertaKM') ? $objRequest->request->get('alertaKM') : '';
                
                
                $strDoc                   = 'FICHA TECNICA';
                $strDatosNuevosFichaTecnica  = '<b>Datos Nuevos de '.$strDoc.':<b><br>';
                
                $arrayDetallesFichaTecnica = array(
                                                        'PLAN_MANTENIMIENTO'    => $intIdPlanMantenimiento,
                                                        'ALERTA_KM'             => $intAlertaKM,
                                                        'REGION'                => $valueRegion);

                //PROBADO
                /*
                 * Tipo de vehículo para actualizar "EMPRESA"
                 * Tipo de vehículo anterior "EMPRESA"
                 * Se actualizan los datos de los detalles 
                 * 'PLAN_MANTENIMIENTO','ALERTA_KM','REGION' que se encuentren con estado 'Activo'
                 * o se insertan(si es que no hubiera el detalle del elemento)
                 * 
                 */
                if($strTipoVehiculoAntiguo == "EMPRESA")
                {
                    $strDatosAntiguos = '';
                    $strDatosNuevos   = '';
                    $boolTieneCambios = false;
                    //Se recorren los detalles para verificar si no hay nuevos                                                            
                    foreach( $arrayObjDetalles as $objDetalle  )
                    {
                        //recorren los valores que se envian en la edición
                        foreach($arrayDetallesFichaTecnica as $strKey => $strValue)
                        {
                            //Si el key es el mismo se verifica si existen cambios
                            //Si es diferente de acuerdo al tipo de detalle se crea uno nuevo y se da de baja al anterior
                            if($strKey == $objDetalle->getDetalleNombre() && $strValue != $objDetalle->getDetalleValor())
                            {                           
                                $boolTieneCambios  = true;
                                $strDatosAntiguos .= $strKey.': '.$objDetalle->getDetalleValor().'<br/>';
                                $strDatosNuevos   .= $strKey.': '.$strValue.'<br/>';
                            
                                $objInfoDetalleElemento = new InfoDetalleElemento();
                                $objInfoDetalleElemento->setElementoId($intIdElemento);
                                $objInfoDetalleElemento->setDetalleNombre($strKey);
                                $objInfoDetalleElemento->setDetalleValor($strValue);                                    
                                $objInfoDetalleElemento->setDetalleDescripcion($strKey);
                                $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                                $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                                $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                                $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                                $emInfraestructura->persist($objInfoDetalleElemento);
                                $emInfraestructura->flush();

                                $objDetalle->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objDetalle);
                                $emInfraestructura->flush();                                
                            }
                        }
                    }     
                    
                    if($boolTieneCambios)
                    {
                        $objInfoHistorialElemento = new InfoHistorialElemento();
                        $objInfoHistorialElemento->setElementoId($objMedioTransporte);
                        $objInfoHistorialElemento->setObservacion('<b>FICHA TECNICA</b></br>'.
                                                                'DATOS ANTIGUOS: </br>'.$strDatosAntiguos.'</br>'.
                                                                'DATOS NUEVOS: </br>'.$strDatosNuevos.'</br>');
                        $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                        $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                        $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                        $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                        $emInfraestructura->persist($objInfoHistorialElemento);
                        $emInfraestructura->flush();

                        $boolTieneCambios = false;
                    }
                }//if($strTipoVehiculoAntiguo == "EMPRESA")
                else
                {
                    /*
                     * Tipo de vehículo para actualizar "EMPRESA"
                     * Tipo de vehículo anterior "SUBCONTRATADO"
                     * Se actualiza el detalle 'CONTRATO' y el contrato con el estado 'Eliminado'
                     */ 
                    if($strTipoVehiculoAntiguo == "SUBCONTRATADO")
                    {
                        $emComercial->getConnection()->beginTransaction();
                        try
                        {
                            $strDocAnterior = 'CONTRATO';
                            $strDatosAntiguosPorTipoVeh  = '<b>Datos Anteriores de '.$strDocAnterior.':<b><br>';
                            $objDetalleContrato = $emInfraestructura ->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy( array(
                                                                            'elementoId' => $id, 
                                                                            'estado' => self::ESTADO_ACTIVO, 
                                                                            'detalleNombre'=>'CONTRATO') );

                            //Existe Contrato
                            if($objDetalleContrato)
                            {
                                $strDatosAntiguosPorTipoVeh .= 'ID de Contrato: '.$objDetalleContrato->getDetalleValor().'<br/>';

                                $objDetalleContrato->setEstado(self::ESTADO_ELIMINADO);
                                $emInfraestructura->persist($objDetalleContrato);
                                $emInfraestructura->flush();

                                $idContrato  = $objDetalleContrato->getDetalleValor();
                                $objContrato = $emComercial->getRepository('schemaBundle:InfoContrato')->find($idContrato);

                                $strNombreContratista = "";
                                if($objContrato)
                                {
                                    $objPersonaContrato         = $objContrato->getPersonaEmpresaRolId()->getPersonaId();
                                    $strNombreContratista       = sprintf("%s",$objPersonaContrato);


                                    $strDatosAntiguosPorTipoVeh .= 'Contratista: '.$strNombreContratista.'<br>';
                                    $strDatosAntiguosPorTipoVeh .= 'Tipo de Contrato:'.self::TIPO_CONTRATO_VEHICULO.'<br>';
                                    $strDatosAntiguosPorTipoVeh .= 'Fecha de Inicio de Contrato: '
                                                                .date_format($objContrato->getFeAprobacion(),'d-m-Y').'<br>';
                                    $strDatosAntiguosPorTipoVeh .= 'Fecha de Fin de Contrato: '
                                                                .date_format($objContrato->getFeFinContrato(),'d-m-Y').'<br>';
                                    $strDatosAntiguosPorTipoVeh .= 'Estado: '.$objContrato->getEstado().'<br>';

                                    $objContrato->setEstado(self::ESTADO_ELIMINADO);
                                    $emComercial->persist($objContrato);
                                    $emComercial->flush();
                                    if ($emComercial->getConnection()->isTransactionActive())
                                    {
                                        $emComercial->getConnection()->commit();
                                    }
                                }//if($objContrato) 
                            }//if($objDetalleContrato)
                            $emComercial->getConnection()->close(); 
                        }
                        catch (\Exception $e)
                        {   
                            if ($emComercial->getConnection()->isTransactionActive())
                            {
                                $emComercial->getConnection()->rollback();
                            }                                   
                            $emComercial->getConnection()->close();
                            error_log($e->getMessage());
                            return $this->redirect($this->generateUrl('elementotransporte_show', array('id' => $intIdElemento)));
                        }

                    }//if($strTipoVehiculoAntiguo == "SUBCONTRATADO")

                    /*
                     * Tipo de vehículo para actualizar "EMPRESA"
                     * Tipo de vehículo anterior "SUBCONTRATADO" o no tenía
                     * Se Crean los nuevos detalles del elemento: 'PLAN_MANTENIMIENTO','ALERTA_KM','REGION'
                     */
                    foreach($arrayDetallesFichaTecnica as $strKeyFichaTecnica => $strValueFichaTecnica)
                    {
                        if( $strValueFichaTecnica )
                        {
                            $objInfoDetalleElemento = new InfoDetalleElemento();
                            $objInfoDetalleElemento->setElementoId($intIdElemento);
                            $objInfoDetalleElemento->setDetalleNombre($strKeyFichaTecnica);
                            $objInfoDetalleElemento->setDetalleValor($strValueFichaTecnica);
                            $objInfoDetalleElemento->setDetalleDescripcion($strKeyFichaTecnica);
                            $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                            $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                            $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                            $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                            $emInfraestructura->persist($objInfoDetalleElemento);
                            $emInfraestructura->flush();

                            $strDatosNuevosFichaTecnica .= $strKeyFichaTecnica.': '.$strValueFichaTecnica.'<br>';
                        }
                    }

                    $strObservacionHistorial = $strDatosAntiguosPorTipoVeh.$strDatosNuevosFichaTecnica;

                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objMedioTransporte);
                    $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
                    $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                    $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                    $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush(); 
                }//if($strTipoVehiculoAntiguo == "EMPRESA")
                    
            }//if($strTipoVehiculo == "EMPRESA")

            
            //Guardar Contrato
            else if($strTipoVehiculo=="SUBCONTRATADO")
            {
                
                $strDoc                   = 'CONTRATO';
                $strDatosNuevosContrato  = '<b>Datos Nuevos de '.$strDoc.':<b><br>';
                
                $idPersonaEmpresaRol = $objRequest->get('infocontratoextratype_personaEmpresaRolId') 
                                        ? $objRequest->get('infocontratoextratype_personaEmpresaRolId') : '';
                $strNombreContratista = $objRequest->get('infocontratoextratype_contratista') 
                                        ? $objRequest->get('infocontratoextratype_contratista') : '';
                
                $strfechaInicioContrato = $objRequest->get('fecha_inicio_contrato')? $objRequest->get('fecha_inicio_contrato') :'' ;
                $strfechaFinContrato    = $objRequest->get('fecha_fin_contrato') ? $objRequest->get('fecha_fin_contrato') : '' ;
                
                
                
                $datetimeFechaInicioContrato = new \DateTime();
                if($strfechaInicioContrato!="")
                {
                    list($anioInicioContrato,$mesInicioContrato,$diaInicioContrato)=explode("-",$strfechaInicioContrato);
                    $datetimeFechaInicioContrato->setDate($anioInicioContrato,$mesInicioContrato,$diaInicioContrato);
                }
                $datetimeFechaFinContrato = new \DateTime();
                if($strfechaFinContrato!="")
                {
                    list($anioFinContrato,$mesFinContrato,$diaFinContrato)=explode("-",$strfechaFinContrato);
                    $datetimeFechaFinContrato->setDate($anioFinContrato,$mesFinContrato,$diaFinContrato);
                }
                
                /*
                 * Tipo de vehículo para actualizar "SUBCONTRATADO"
                 * Tipo de vehículo anterior "SUBCONTRATADO"
                 * Se obtiene el detalle elemento 'CONTRATO' y luego se obtiene el objeto contrato para actualizarlo
                 */
                if($strTipoVehiculoAntiguo=="SUBCONTRATADO")
                {
                    $strDocAnterior             = 'CONTRATO';
                    $strDatosAntiguosPorTipoVeh = '<b>Datos Anteriores de '.$strDocAnterior.':<b><br>';
                    $objDetalleContrato         = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array(
                                                                                'elementoId' => $id, 
                                                                                'estado' => self::ESTADO_ACTIVO, 
                                                                                'detalleNombre'=>'CONTRATO') );

                    $emComercial->getConnection()->beginTransaction();
                    try
                    {
                        if($objDetalleContrato)
                        {

                            $idContrato  = $objDetalleContrato->getDetalleValor();
                            $objContrato = $emComercial->getRepository('schemaBundle:InfoContrato')->find($idContrato);
                            $strNombreContratistaAntiguo = "";
                            if($objContrato)
                            {
                                $objPersonaContrato             = $objContrato->getPersonaEmpresaRolId()->getPersonaId();
                                $strNombreContratistaAntiguo    = sprintf("%s",$objPersonaContrato);
                                
                                $strDatosAntiguosPorTipoVeh .= 'Contratista: '.$strNombreContratistaAntiguo.'<br>';
                                $strDatosAntiguosPorTipoVeh .= 'Tipo de Contrato:'.self::TIPO_CONTRATO_VEHICULO.'<br>';
                                $strDatosAntiguosPorTipoVeh .= 'Fecha de Inicio de Contrato: '
                                                                .date_format($objContrato->getFeAprobacion(),'d-m-Y').'<br>';
                                $strDatosAntiguosPorTipoVeh .= 'Fecha de Fin de Contrato: '
                                                                .date_format($objContrato->getFeFinContrato(),'d-m-Y').'<br>';
                                $strDatosAntiguosPorTipoVeh .= 'Estado: '.$objContrato->getEstado().'<br>';
                                if($idPersonaEmpresaRol!='')
                                {
                                    $personaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->find($idPersonaEmpresaRol);
                                    $objContrato->setPersonaEmpresaRolId($personaEmpresaRol);
                                }
                                
                                $objContrato->setFeAprobacion($datetimeFechaInicioContrato);
                                $objContrato->setFeFinContrato($datetimeFechaFinContrato);
                                $emComercial->persist($objContrato);
                                $emComercial->flush(); 
                                if ($emComercial->getConnection()->isTransactionActive())
                                {
                                    $emComercial->getConnection()->commit();
                                }

                                $strDatosNuevosContrato .= 'Contratista: '.$strNombreContratista.'<br>';
                                $strDatosNuevosContrato .= 'Tipo de Contrato:'.self::TIPO_CONTRATO_VEHICULO.'<br>';
                                $strDatosNuevosContrato .= 'Fecha de Inicio de Contrato: '
                                                        .date_format($objContrato->getFeAprobacion(),'d-m-Y').'<br>';
                                $strDatosNuevosContrato .= 'Fecha de Fin de Contrato: '
                                                            .date_format($objContrato->getFeFinContrato(),'d-m-Y').'<br>';
                                $strDatosNuevosContrato .= 'Estado: '.$objContrato->getEstado().'<br>';
                                
                                
                                $strObservacionHistorial=$strDatosAntiguosPorTipoVeh.$strDatosNuevosContrato;
                                $objInfoHistorialElemento = new InfoHistorialElemento();
                                $objInfoHistorialElemento->setElementoId($objMedioTransporte);
                                $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
                                $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                                $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                                $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                                $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                                $emInfraestructura->persist($objInfoHistorialElemento);
                                $emInfraestructura->flush();
                            }//if($objContrato)  
                        }//if($objDetalleContrato)
                        $emComercial->getConnection()->close();
                    }
                    catch (\Exception $e)
                    {   
                        if ($emComercial->getConnection()->isTransactionActive())
                        {
                            $emComercial->getConnection()->rollback();
                        }                                   
                        $emComercial->getConnection()->close();
                        error_log($e->getMessage());
                        return $this->redirect($this->generateUrl('elementotransporte_show', array('id' => $intIdElemento)));
                    }
                }
                else
                {
                    //PROBADO
                    /*
                     * Tipo de vehículo para actualizar "SUBCONTRATADO"
                     * Tipo de vehículo anterior "EMPRESA"
                     * Colocar el estado 'Eliminado' a los detalles'PLAN_MANTENIMIENTO' y 'ALERTA_KM' y 'REGION'
                     */
                    if($strTipoVehiculoAntiguo=="EMPRESA")
                    {
                        
                        $strDocAnterior             = 'FICHA TECNICA';
                        $strDatosAntiguosPorTipoVeh = '<b>Datos Anteriores de '.$strDocAnterior.':<b><br>';
                        $arrayDetallesFichaTecnica  = array(
                                                            'PLAN_MANTENIMIENTO'    => '', 
                                                            'ALERTA_KM'             => '',
                                                            'REGION'                =>'');
                        
                        
                        
                        foreach($arrayDetallesFichaTecnica as $strKeyFichaTecnica => $strValueFichaTecnica)
                        {
                            if( $objDetalles )
                            {
                                foreach( $objDetalles as $objDetalle  )
                                {
                                    if( $objDetalle->getDetalleNombre() == $strKeyFichaTecnica )
                                    {
                                        $strDatosAntiguosPorTipoVeh .= $strKeyFichaTecnica.': '.$objDetalle->getDetalleValor().'<br/>';
                                        
                                        $objDetalle->setEstado(self::ESTADO_ELIMINADO);
                                        $emInfraestructura->persist($objDetalle);
                                        $emInfraestructura->flush();
                                    }//if( $objDetalle->getDetalleNombre() == $strKeyFichaTecnica )
                                }//foreach( $objDetalles as $objDetalle  )
                            }//if($objDetalles)
                        }//foreach $arrayDetallesFichaTecnica
                    }//if($strTipoVehiculoAntiguo=="EMPRESA")
                    
                    
                    //$strTipoVehiculo="SUBCONTRATADO"
                    //si $strTipoVehiculoAntiguo="EMPRESA" || no tiene $strTipoVehiculoAntiguo
                        //crear contrato
                    
                    /*
                     * Tipo de vehículo para actualizar "SUBCONTRATADO"
                     * Tipo de vehículo anterior "EMPRESA" o no tiene tipo de vehículo
                     * Crear Contrato y crear detalle de elemento 'CONTRATO'
                     */
                    $objTipoContrato        = $emComercial ->getRepository('schemaBundle:AdmiTipoContrato')
                                                            ->findOneBy(array(
                                                                            'empresaCod'=>$intIdEmpresa,
                                                                            'descripcionTipoContrato'=>self::TIPO_CONTRATO_VEHICULO
                                                                ));
                    $intIdTipoContrato      = $objTipoContrato->getId() ? $objTipoContrato->getId() : 0;

                    $intIdCliente           = $objRequest->get('infocontratoextratype_idcontratista');
                    $intIdPersonaEmpresaRol = $objRequest->get('infocontratoextratype_personaEmpresaRolId');

                    $formaPago              = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')
                                                        ->findOneByDescripcionFormaPago('EFECTIVO');
                    $intIdFormaPago         = $formaPago->getId() ? $formaPago->getId(): 0;

                    $clientIp               = $objRequest->getClientIp();
                    $session                = $objRequest->getSession();
                    $usrCreacion            = $session->get('user');
                    $codEmpresa             = $session->get('idEmpresa');
                    $prefijoEmpresa         = $session->get('prefijoEmpresa');
                    $idOficina              = $session->get('idOficina');
                    $check                  = NULL;
                    $clausula               = NULL;

                    $datosContrato          = array(
                                                'codigoNumeracionVE'    => 'CONVE',
                                                'tipoContratoId'        => $intIdTipoContrato,
                                                'feInicioContrato'      => $datetimeFechaInicioContrato,
                                                'feFinContratoPost'     => $datetimeFechaFinContrato,
                                                'idcliente'             => $intIdCliente,
                                                'personaEmpresaRolId'   => $intIdPersonaEmpresaRol,
                                                'valorEstado'           => 'Activo',
                                                'formaPagoId'           => $intIdFormaPago,
                                                'datos_form_files'      => array(),
                                                'arrayTipoDocumentos'   => array(),
                                                'tipoCuentaId'          => '',
                                                'bancoTipoCuentaId'     => '',
                                                'numeroCtaTarjeta'      => '',
                                                'titularCuenta'         => '',  
                                                'valorAnticipo'         => '',
                                                'numeroContratoEmpPub'  => ''
                                            );
                    try
                    {
                        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                        $serviceInfoContrato = $this->get('comercial.InfoContrato');
                        $arrayParametrosContrato                   = array();
                        $arrayParametrosContrato['codEmpresa']     = $codEmpresa;
                        $arrayParametrosContrato['prefijoEmpresa'] = $prefijoEmpresa; 
                        $arrayParametrosContrato['idOficina']      = $idOficina; 
                        $arrayParametrosContrato['usrCreacion']    = $usrCreacion; 
                        $arrayParametrosContrato['clientIp']       = $clientIp; 
                        $arrayParametrosContrato['datos_form']     = $datosContrato; 
                        $arrayParametrosContrato['check']          = $check; 
                        $arrayParametrosContrato['clausula']       = $clausula;
                        $entity = $serviceInfoContrato->crearContrato($arrayParametrosContrato);
                        
                        $intIdContrato            = $entity->getId();
                        $objInfoDetalleElemento   = new InfoDetalleElemento();
                        $objInfoDetalleElemento->setElementoId($intIdElemento);
                        $objInfoDetalleElemento->setDetalleNombre('CONTRATO');
                        $objInfoDetalleElemento->setDetalleValor($intIdContrato);
                        $objInfoDetalleElemento->setDetalleDescripcion('CONTRATO');
                        $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                        $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                        $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                        $objInfoDetalleElemento->setEstado(self::ESTADO_ACTIVO);
                        $emInfraestructura->persist($objInfoDetalleElemento);
                        $emInfraestructura->flush();
                        $strDatosNuevosContrato .= 'ID de Contrato: '.$intIdContrato.'<br>';

                        
                        $objPersonaContrato     = $entity->getPersonaEmpresaRolId()->getPersonaId();
                        $strNombreContratista   = sprintf("%s",$objPersonaContrato);
                        
                        $strDatosNuevosContrato .= 'Contratista: '.$strNombreContratista.'<br>';
                        $strDatosNuevosContrato .= 'Tipo de Contrato:'.self::TIPO_CONTRATO_VEHICULO.'<br>';
                        $strDatosNuevosContrato .= 'Fecha de Inicio de Contrato: '.date_format($entity->getFeAprobacion(),'d-m-Y').'<br>';
                        $strDatosNuevosContrato .= 'Fecha de Fin de Contrato: '.date_format($entity->getFeFinContrato(),'d-m-Y').'<br>';
                        $strDatosNuevosContrato .= 'Estado: '.$entity->getEstado().'<br>';


                        $strObservacionHistorial=$strDatosAntiguosPorTipoVeh.$strDatosNuevosContrato;
                        $objInfoHistorialElemento = new InfoHistorialElemento();
                        $objInfoHistorialElemento->setElementoId($objMedioTransporte);
                        $objInfoHistorialElemento->setObservacion($strObservacionHistorial);
                        $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                        $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                        $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                        $objInfoHistorialElemento->setEstadoElemento(self::ESTADO_ACTIVO);
                        $emInfraestructura->persist($objInfoHistorialElemento);
                        $emInfraestructura->flush();
                    }
                    catch (\Exception $e)
                    {   
                        $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
                        return $this->redirect($this->generateUrl('elementotransporte_show', array('id' => $intIdElemento)));
                    }
                }
            }
            $emInfraestructura->getConnection()->commit();
            $emInfraestructura->getConnection()->close();
            return $this->redirect($this->generateUrl('elementotransporte_show', array('id' => $intIdElemento)));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }//try
        
        
        if( $arrayInfoDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $arrayInfoDetalles[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        $arrayModelosElementos = array();
        
        $arrayTmpParametros = array( 'estadoActivo' => self::ESTADO_ACTIVO, 'tipoElemento' => array($strTipoTransporte) );
        $arrayTmpResultados = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->getModeloElementosByCriterios( $arrayTmpParametros );
        
        if( $arrayTmpResultados )
        {
            $arrayModelosElementos = $arrayTmpResultados['registros'];
        }

        $arrayInfoDetalles = array_merge($arrayInfoDetalles,$arrayInfoDetallesGps,$arrayInfoDetallesFilial);

        return $this->render('tecnicoBundle:InfoElementoTransporte:edit.html.twig', array(
                                                                                            'medioTransporte'   => $objMedioTransporte,
                                                                                            'detalles'          => $arrayInfoDetalles,
                                                                                            'modelosElemento'   => $arrayModelosElementos,
                                                                                            'letrasPlaca'       => $strLetrasPlacas,
                                                                                            'numerosPlaca'      => $strNumerosPlacas,
                                                                                            'strTipoTransporte' => $strTipoTransporte
                                                                                         )
                            );
                
    }
    
    
    /**
     * @Secure(roles="ROLE_313-8")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     *
     * Elimina la información de un medio de transporte.
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     */
    public function deleteAjaxAction()
    {
        $response           = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strMedioTransporte = $objRequest->request->get('medioTransporte') ? $objRequest->request->get('medioTransporte') : '';
        $boolError          = false;
        $strMensaje         = 'No se encontró medio de transporte en estado activo';
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $datetimeActual     = new \DateTime('now');
        
        $arrayMediosTransporte = array();
        if( $strMedioTransporte )
        {
            $arrayMediosTransporte = explode('|', $strMedioTransporte);
        }
            
        
        $emInfraestructura->getConnection()->beginTransaction();	
        
        try
        {
            foreach( $arrayMediosTransporte as $intIdMedioTransporte )
            {
                $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($intIdMedioTransporte);

                if( !$objMedioTransporte )
                {
                    $boolError  = true;
                    $strMensaje = 'No se encontró medio de transporte en estado activo';
                }

                if( !$boolError )
                {
                    $objDetalles            = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findBy( array( 'elementoId' => $intIdMedioTransporte, 
                                                                                 'estado'     => self::ESTADO_ACTIVO ) );
                    $objInfoEmpresaElemento = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                                ->findOneBy( array ( 'elementoId' => $objMedioTransporte, 
                                                                                     'estado'     => self::ESTADO_ACTIVO) );
                    
                    /*
                     * Bloque que actualiza la información del InfoElemento seleccionado por el usuario
                     */
                    $objMedioTransporte->setEstado(self::ESTADO_ELIMINADO);
                    $emInfraestructura->persist($objMedioTransporte);
                    $emInfraestructura->flush();
                    /*
                     * Fin del Bloque que actualiza la información del InfoElemento seleccionado por el usuario
                     */
                    
                    
                    /*
                     * Bloque que actualiza los detalles del InfoElemento seleccionado por el usuario
                     */
                    if( $objDetalles )
                    {
                        foreach( $objDetalles as $objDetalle  )
                        {
                            $objDetalle->setEstado(self::ESTADO_ELIMINADO);
                            $emInfraestructura->persist($objDetalle);
                            $emInfraestructura->flush();
                        }
                    }
                    /*
                     * Fin del Bloque que actualiza los detalles del InfoElemento seleccionado por el usuario
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
                    $objInfoHistorialElemento->setElementoId($objMedioTransporte);
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
     * Documentación para el método 'verificarPlacaAction'.
     *
     * Retorna un string con 'OK' si no existe la placa en base de datos
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 13-11-2015
     */
    public function verificarPlacaAction()
    {
        $response             = new Response();
        $objRequest           = $this->get('request');
        $strPlaca             = $objRequest->request->get('placa') ? $objRequest->request->get('placa') : '';
        $strTipoTransporte    = $objRequest->request->get('tipoTransporte') ? $objRequest->request->get('tipoTransporte') : '';
        $strAccion            = $objRequest->request->get('accion') ? $objRequest->request->get('accion') : '';
        $intIdMedioTransporte = $objRequest->request->get('idMedioTransporte') ? $objRequest->request->get('idMedioTransporte') : 0;
        $serviceInfoElemento  = $this->get('tecnico.InfoElemento');
        $strMensaje           = 'OK';
        
        $arrayParametros = array(
                                    'strEstadoActivo'      => 'Activo',
                                    'strCategoriaElemento' => 'transporte',
                                    'criterios'            => array( 'tipoElemento' => array( $strTipoTransporte ),
                                                                     'nombre'       => $strPlaca )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);

        if( $arrayResultados['total'] > 0 && $strAccion == 'guardar' )
        {
            $strMensaje = 'El número de placa ingresado ya existe, por favor ingresar un número de placa diferente';
        }//if( $arrayResultados['total'] > 0 )
        elseif( $arrayResultados['total'] >= 1 && $strAccion == 'editar' )
        {
            foreach( $arrayResultados['encontrados'] as $arrayMedioTransporte )
            {
                $intIdElementoEncontrado = ( isset($arrayMedioTransporte['intIdElemento']) ) ? $arrayMedioTransporte['intIdElemento'] : 0;

                if( $intIdElementoEncontrado != $intIdMedioTransporte )
                {
                    $strMensaje = 'El número de placa ingresado ya existe, por favor ingresar un número de placa diferente';
                }//( $intIdElementoEncontrado != $intIdMedioTransporte )
            }//foreach( $arrayResultados['encontrados'] as $arrayMedioTransporte )                
        }//( $arrayResultados['total'] == 1 && $strAccion == 'editar' )
        
        $response->setContent( $strMensaje );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'verificarElementosAEliminarAction'.
     *
     * Verifica que los medios de transporte a eliminar no esten asignados a una cuadrilla.
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
        $strMedioTransporte = $objRequest->request->get('medioTransporte') ? $objRequest->request->get('medioTransporte') : '';
        $intContadorError   = 0;
        
        $arrayMediosTransporte = array();
        if( $strMedioTransporte )
        {
            $arrayMediosTransporte = explode('|', $strMedioTransporte);
        }	
        
        try
        {
            foreach( $arrayMediosTransporte as $intIdMedioTransporte )
            {
                $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->findOneBy( 
                                                                        array(
                                                                                'id'     => $intIdMedioTransporte,
                                                                                'estado' => self::ESTADO_ACTIVO,
                                                                             )
                                                                   );
                
                if( !$objMedioTransporte )
                {
                    $intContadorError++;
                    $strMensaje = '<b>'.$intIdMedioTransporte.'</b>: No se encontró medio de transporte en estado activo<br/>';
                }
                else
                {
                    $objDetalles = $emInfraestructura->getRepository( 'schemaBundle:InfoDetalleElemento')
                                                     ->findBy( 
                                                                array( 
                                                                        'elementoId'    => $intIdMedioTransporte, 
                                                                        'estado'        => self::ESTADO_ACTIVO,
                                                                        'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_CUADRILLA
                                                                     ) 
                                                             );
                    
                    if( $objDetalles )
                    {
                        $intContadorError++;
                        $strMensaje .= '<b>'.$objMedioTransporte->getNombreElemento().':</b> Asignado a Cuadrilla<br/>';
                    }//( !$objMedioTransporte )
                }//( !$boolError )
            }//foreach( $arrayMediosTransporte as $intIdMedioTransporte )
            
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
     * @Secure(roles="ROLE_313-3858")
     * 
     * Documentación para el método 'showDocumentosAction'.
     * 
     * Función en Ajax que lista los archivos digitales asociados al transporte.
     * 
     * @param integer $id
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-12-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 13-04-2016 - Se agregó parámetros de seguridad para esta funcionalidad
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 22-08-2016 - Se cambia la funcionalidad para que sólo se muestren los tipos de documentos visibles en elemento
     */      
    public function showDocumentosTransporteAction($id) 
    {

        $objRequest  = $this->getRequest();
        $start       = $objRequest->get('start', 0);
        $limit       = $objRequest->get('limit', 10);
        
        $objResponse    = new Response();
        $objResponse->headers->set('Content-type', 'text/json');	
        $emComunicacion   = $this->getDoctrine()->getManager('telconet_comunicacion');
        
        $arrayParametros = array(
                                    "idElemento"        => $id,
                                    "visibleEnElemento" => 'S',
                                    "container"         => $this->container,
                                    "intStart"          => $start,
                                    "intLimit"          => $limit
                            );
        
        $objJson = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->getJSONDocumentosTransporte($arrayParametros);

        $objResponse->setContent($objJson);

        return $objResponse;
        
    }

    /**
     * 
     * Documentación para el método 'descargarDocumentoAction'.
     * 
     * Método encargado de descargar los documentos a partir del id de la referencia enviada.
     * 
     * @param integer $id // id de InfoDocumento
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-12-2015
     */ 
    public function descargarDocumentoAction($id)
    {
        $em                = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objInfoDocumento  = $em->getRepository('schemaBundle:InfoDocumento')->find($id);               
        $path              = $objInfoDocumento->getUbicacionFisicaDocumento();        
        $path_telcos       = $this->container->getParameter('path_telcos');
        $content           = file_get_contents($path_telcos.$path);        
        $response          = new Response();
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$objInfoDocumento->getUbicacionLogicaDocumento());
        $response->setContent($content);
        return $response;       
    }
    
    
    
    
    /**
     * @Secure(roles="ROLE_313-3857")
     * 
     * Documentación para el método 'newArchivoDigitalAction'.
     * 
     * Función para el ingreso de Nuevos Archivos Digitales asociados al transporte.
     * 
     * @param interger $id 
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-12-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 13-04-2016 - Se agregó parámetros de seguridad para esta funcionalidad
     */
    public function newArchivoDigitalAction($id)
    {
              
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');

        $objMedioTransporte  = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($id);	    

        $arrayTipoDocumentos = array();
        $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                         ->findBy(array('estado'=>"Activo",'visibleEnElemento'=>'S'));                   
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
        }

        $form_documentos                           = $this->createForm(new InfoDocumentoType(array(
                                                                                              'validaFile'                 =>true,
                                                                                              'validaFechaPublicacionHasta'=>true,
                                                                                              'arrayTipoDocumentos'        =>$arrayTipoDocumentos)
                                                                                            ), new InfoDocumento());
        $arrayParametros                           = array('form_documentos' => $form_documentos->createView());   
        
        $arrayParametros['arrayTipoDocumentos']    = $arrayTipoDocumentos;
        $arrayParametros['objMedioTransporte']     = $objMedioTransporte;

        return $this->render('tecnicoBundle:InfoElementoTransporte:newArchivoDigital.html.twig',$arrayParametros);       
    }
    
    
    /**
     * @Secure(roles="ROLE_313-3859")
     * 
     * Documentación para el método 'eliminarDocumentoAjaxAction'.
     * 
     * Método encargado de eliminar individualmente o masivamente documentos a partir del o los ids de la referencia enviada.
     * 
     * @param integer $id 
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 13-04-2016 - Se agregó parámetros de seguridad para esta funcionalidad
     */
    public function eliminarDocumentoAjaxAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest  = $this->get('request');
        $parametro   = $objRequest->get('id');
        
        /*
         * tipo=1 Eliminar un documento por medio de su id
         * tipo=2 Eliminación Masiva de documentos
         * 
         */
        $tipo = $objRequest->get('tipo');
        
        if($parametro)
        {
            $arrayValor = explode("|",$parametro);
        }
        else
        {
            $parametro  = $objRequest->get('param');
           
            $arrayValor = explode("|",$parametro);
        } 

        $emComunicacion  = $this->getDoctrine()->getManager("telconet_comunicacion");
        $documentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findOneByDocumentoId($arrayValor[0]);
        $idElementoId=$documentoRelacion->getElementoId();
                 
        $strMensajeError = "";
        try
        {
            foreach($arrayValor as $id)
            {            
                $objInfoDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);                                              
                if( $objInfoDocumento )
                {                                  
                     /* @var $serviceInfoElemento \telconet\tecnicoBundle\Service\InfoElementoService */
                    $serviceInfoElemento = $this->get('tecnico.InfoElemento');
                    $entity              = $serviceInfoElemento->eliminarDocumento($id);
                 }
                 else
                 {
                     $strMensajeError.="No existe el documento con id ".$id." <br>";
                 }

            }

            if($tipo==1)
            {
                return $this->redirect($this->generateUrl('elementotransporte_show', array('id'=>$idElementoId))); 
            }
            else if($tipo==2)
            {
                return $objResponse->setContent('La eliminacion fue exitosa');
            }
   
        } 
        catch (Exception $e) 
        {
            if($tipo==1)
            {
                $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
                return $this->redirect($this->generateUrl('elementotransporte_show', array('id'=>$idElementoId))); 
            }
            else if($tipo==2)
            {
                return $objResponse->setContent($strMensajeError);
            }
        }                 
    } 
    

    /**
     * @Secure(roles="ROLE_313-3857")
     * 
     * Documentación para el método 'guardarArchivoDigitalAction'.
     * 
     * Método que Guarda Archivos Digitales agregados al transporte
     * 
     * @param request $request
     * @param integer $id // id de InfoElemento 
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 13-04-2016 - Se agregó parámetros de seguridad para esta funcionalidad
     */     
    public function guardarArchivoDigitalAction(Request $objRequest,$id)
    {       
        $intClientIp                = $objRequest->getClientIp();
        $session                    = $objRequest->getSession();
        $strUsrCreacion             = $session->get('user');                     
        $datos_form_files           = $objRequest->files->get('infodocumentotype');
        $datos_form_Doc             = $objRequest->get('infodocumentotype');    
        $arrayTipoDocumentos        = array ();
        $arrayFechasHastaDocumentos = array ();
        
        foreach ($datos_form_Doc as $key => $arrayAttr)
        {   
            if($key=='tipos')
            {
                foreach ( $arrayAttr as $key_tipo => $value)
                {                     
                    $arrayTipoDocumentos[$key_tipo]=$value;                
                }
            }
            else if($key=="fechasPublicacionHasta")
            {
                foreach ( $arrayAttr as $key_fecha => $value)
                {                     
                    if($arrayAttr[$key_fecha]['year'] && $arrayAttr[$key_fecha]['month'] && $arrayAttr[$key_fecha]['day'])
                    {
                        $fechaHasta=date_create($arrayAttr[$key_fecha]['year'].'-'.$arrayAttr[$key_fecha]['month'].'-'.$arrayAttr[$key_fecha]['day']);
                        $arrayFechasHastaDocumentos[$key_fecha]=$fechaHasta;
                    }
                }
            }
        }             
        
        $datos_form = array_merge(               
                                    $objRequest->get('infoelementotransporteextratype'),
                                    array('datos_form_files'           => $datos_form_files),
                                    array('arrayTipoDocumentos'        => $arrayTipoDocumentos),
                                    array('arrayFechasHastaDocumentos' => $arrayFechasHastaDocumentos)
                                 );
        try
        {
            /* @var $serviceInfoElemento \telconet\tecnicoBundle\Service\InfoElementoService */
            $serviceInfoElemento = $this->get('tecnico.InfoElemento');
            //retorna un objInfoDocumentoRelacion
            $entity              = $serviceInfoElemento->guardarArchivoDigital($id, $strUsrCreacion, $intClientIp, $datos_form);
            return $this->redirect($this->generateUrl('elementotransporte_newArchivoDigital', array('id' => $id)));
        }
        catch (\Exception $e)
        {   
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('elementotransporte_newArchivoDigital', array('id'=>$id)));
        }
    }
    
    
    
    /**
     * 
     * Documentación para el método 'validarDocumentosObligatoriosAjaxAction'.
     * 
     * Método que valida que los archivos obligatorios para el tipo de transporte se hayan subido.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-12-2015
     */    
    public function validarDocumentosObligatoriosAjaxAction()
    {
        $objRequest                = $this->get('request');
        $idMedioTransporte         = $objRequest->get('idMedioTransporte');
        $strDatosFormTipos         = $objRequest->get('strIdsTiposDocsASubir');
        $strDatosFormFechasHasta   = $objRequest->get('strFechasHastaDocsPorSubir');
        

        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');
        $emComunicacion   = $this->getDoctrine()->getManager("telconet_comunicacion");
        
        $arrayIdsTiposDocsActuales              = array();
        $arrayCodigoTipoDocumentosObligatorios  = array();
        $arrayTiposDocumentoBase                = array();
        $arrayFechasObligatorias                = array();
        
        $strMensaje     = '';
        
        $objIdsTiposDocsActuales = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                  ->getIdsTiposDocumentosArchivosSubidosByElemento($idMedioTransporte);
        
        if($objIdsTiposDocsActuales)
        {
            foreach ( $objIdsTiposDocsActuales as $objIdTipoDocActual)
            {   
                foreach ( $objIdTipoDocActual as $keyObjIdTipoDocActual=> $valObjIdTipoDocActual)
                {   
                    $arrayIdsTiposDocsActuales[] = $valObjIdTipoDocActual; 
                }
            }
        }
       
        $arrayIdsTiposDocumentosASubir=explode("/",$strDatosFormTipos);
        $arrayFechasHastaDocumentosASubir=explode("/",$strDatosFormFechasHasta);
   
        switch(true)
        {
            default:
                $arrayCodigoTipoDocumentosObligatorios=array('MAT');
            break;
        }
  
        if(!empty($arrayCodigoTipoDocumentosObligatorios))
        {
            $objTiposDocumentosObligatorios  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                         ->findByCodigosTipoDocumento($arrayCodigoTipoDocumentosObligatorios);
            if($objTiposDocumentosObligatorios)
            {
                foreach ( $objTiposDocumentosObligatorios as $objTipoDocumento )
                {   
                   $arrayIdsTipoDocumentosBase[]                       = $objTipoDocumento->getId(); 
                   $arrayTiposDocumentoBase[$objTipoDocumento->getId()]=array(
                                                                         'codigoTipoDocumento'     =>$objTipoDocumento->getCodigoTipoDocumento(),
                                                                         'descripcionTipoDocumento'=>$objTipoDocumento->getDescripcionTipoDocumento()
                                                                        );
                   
                   
                   if(in_array($objTipoDocumento->getId(),$arrayIdsTiposDocumentosASubir))
                   {
                       $arrayPosicionesTiposObligatorios = array_keys($arrayIdsTiposDocumentosASubir, $objTipoDocumento->getId());
                       
                       for($i=0;$i<count($arrayPosicionesTiposObligatorios);$i++)
                       {
                           $arrayFechasObligatorias[]=
                                               array(
                                                'posicion'                =>$arrayPosicionesTiposObligatorios[$i],
                                                'descripcionTipoDocumento'=>$objTipoDocumento->getDescripcionTipoDocumento(),
                                                'valorFechaDocumento'     =>$arrayFechasHastaDocumentosASubir[$arrayPosicionesTiposObligatorios[$i]]
                                               );
                       }
                       
                       
                   }

                }
            }
            
            
            $arrayIdsTiposDocumentosFinales=array_merge($arrayIdsTiposDocsActuales,$arrayIdsTiposDocumentosASubir);
        
            /* @var $objServicioInfoDocumento \telconet\comunicacionesBundle\Service\InfoDocumentoService */
            $objServicioInfoDocumento = $this->get('comunicaciones.InfoDocumento');
            $arrayValidacion          = $objServicioInfoDocumento->validacionesDocumentosObligatorios($arrayTiposDocumentoBase,
                                                                                                      $arrayIdsTiposDocumentosFinales,
                                                                                                      $arrayIdsTipoDocumentosBase); 
            
            if(!$arrayValidacion['boolOk'])    
            {
                $strMensaje .= $arrayValidacion['strMsg'];
            }
            
            $arrayValidacionFechasObligatorias=$objServicioInfoDocumento->validacionesFechasDocumentosObligatorios($arrayFechasObligatorias);
            
            if(!$arrayValidacionFechasObligatorias['boolOk']) 
            {
                if($strMensaje!="")
                {
                    $strMensaje .="<br>";
                }
                $strMensaje .= $arrayValidacionFechasObligatorias['strMsg'];
            }
            
        }
        
        $objResponse = new Response(json_encode(array('msg'=>$strMensaje)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse; 
    }
    
    
    /**
     * 
     * Documentación para el método 'getArchivosCaducadosAction'.
     * 
     * Muestra los documentos caducados de un empleado.
     * 
     * @param integer $id // id de elementoId
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 29-12-2015
     * 
     */ 
    public function getArchivosCaducadosAction($id)
    {
        $fechaActual                           = date("Y-m-d");
        list($anioActual,$mesActual,$diaActual)= explode('-', $fechaActual);
        $timestampActual                       = mktime('0','0','0',$mesActual,$diaActual,$anioActual);

        $response = new Response();
        $response->headers->set('Content-type', 'text/json');        
        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');		
        $emComunicacion   = $this->getDoctrine()->getManager('telconet_comunicacion');

        $objInfoDocumentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
        $objEntities               = $objInfoDocumentoRelacion->findBy(array('elementoId' => $id,'estado' => 'Activo'), array('id' => 'DESC'));


        $arrayResponse                   = array();
        $arrayResponse['docsCaducados']  = array();
        $arrayResponse['docsPorCaducar'] = array();

        $contDocsCaducados  = 0;
        $contDocsPorCaducar = 0;

        foreach ($objEntities as $entity) 
        {
            $arrayDocCaducado   = array();
            $arrayDocPorCaducar = array();

            $infoDocumento=$emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($entity->getDocumentoId());

            if($infoDocumento->getFechaPublicacionHasta())
            {
                $fechaPublicacionHasta=$infoDocumento->getFechaPublicacionHasta();

                $anioPublicacionHasta = strval(date_format($fechaPublicacionHasta, "Y"));
                $mesPublicacionHasta  = strval(date_format($fechaPublicacionHasta, "m"));
                $diaPublicacionHasta  = strval(date_format($fechaPublicacionHasta, "d"));


                $timestampPublicacionHasta = mktime('0','0','0',$mesPublicacionHasta,$diaPublicacionHasta,$anioPublicacionHasta);

                $diferenciaSegundos        = $timestampPublicacionHasta-$timestampActual;
                $diferenciaDias            = $diferenciaSegundos/(60*60*24);
                $absDiferenciaDias         = abs($diferenciaDias);
                $floorAbsDiferenciaDias    = floor($absDiferenciaDias);

                //Documentos ya Caducados
                if($diferenciaDias<0)
                {
                    $arrayDocCaducado['id']                       = $entity->getDocumentoId();
                    $arrayDocCaducado['ubicacionLogicaDocumento'] = $infoDocumento->getUbicacionLogicaDocumento();

                    $objTipoDocumentoGeneral                      = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                              ->find($infoDocumento->getTipoDocumentoGeneralId());                                                                                                                                    

                    if( $objTipoDocumentoGeneral != null )
                    {       
                        $urlVerDocumento                           = $this->generateUrl('personaempleado_descargarDocumento', 
                                                                                         array('id' => $entity->getDocumentoId()));                             

                        $arrayDocCaducado['tipoDocumentoGeneral'] = $objTipoDocumentoGeneral->getDescripcionTipoDocumento();

                    } 

                    $arrayDocCaducado['feCreacion']            = date_format($entity->getFeCreacion(), 'd-m-Y H:i:s');
                    $arrayDocCaducado['feCaducidad']           = date_format($infoDocumento->getFechaPublicacionHasta(), 'd-m-Y');
                    $arrayDocCaducado['usrCreacion']           = $entity->getUsrCreacion();     
                    $arrayDocCaducado['linkVerDocumento']      = $urlVerDocumento;
                    $arrayDocCaducado['estadoCaducidad']       = "Ya caducado"; 
                    $arrayResponse['docsCaducados'][]          = $arrayDocCaducado;
                    $contDocsCaducados++;
                }
                //Documentos que caducan en 2 semanas o menos 
                else if($floorAbsDiferenciaDias<=14)
                {
                    $arrayDocPorCaducar['ubicacionLogicaDocumento'] = $infoDocumento->getUbicacionLogicaDocumento();

                    $objTipoDocumentoGeneral                        = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                                ->find($infoDocumento->getTipoDocumentoGeneralId());                                                                                                                                    

                    if( $objTipoDocumentoGeneral != null )
                    {       
                        $urlVerDocumento                            = $this->generateUrl('personaempleado_descargarDocumento', 
                                                                                   array('id' => $entity->getDocumentoId()));                             

                        $arrayDocPorCaducar['tipoDocumentoGeneral'] = $objTipoDocumentoGeneral->getDescripcionTipoDocumento();

                    } 

                    $arrayDocPorCaducar['feCreacion']            = date_format($entity->getFeCreacion(), 'd-m-Y H:i:s');
                    $arrayDocPorCaducar['feCaducidad']           = date_format($infoDocumento->getFechaPublicacionHasta(), 'd-m-Y');
                    $arrayDocPorCaducar['usrCreacion']           = $entity->getUsrCreacion();     
                    $arrayDocPorCaducar['linkVerDocumento']      = $urlVerDocumento;
                    $strCantidadDias="";
                    if($diferenciaDias==0) 
                    {
                        $strCantidadDias="Hoy";
                    }
                    else if($diferenciaDias==1) 
                    {
                        $strCantidadDias="Mañana";
                    }
                    else 
                    {
                        $strCantidadDias="En ".$diferenciaDias." días";
                    }
                    $arrayDocPorCaducar['estadoCaducidad']       = $strCantidadDias; 
                    $arrayResponse['docsPorCaducar'][]           = $arrayDocPorCaducar;


                    $contDocsPorCaducar++;
                }  

            }   
        }
        $arrayResponse['totalDocsCaducados']  = $contDocsCaducados;
        $arrayResponse['totalDocsPorCaducar'] = $contDocsPorCaducar;
        $arrayResponse['total']               = $arrayResponse['totalDocsCaducados'] + $arrayResponse['totalDocsPorCaducar'];
        $arrayResponse['docs']                = array_merge($arrayResponse['docsCaducados'],$arrayResponse['docsPorCaducar']);

        $response->setContent(json_encode($arrayResponse));
        return $response;

    }
    
    
    
}
