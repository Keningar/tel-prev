<?php

namespace telconet\tecnicoBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoHistorialElemento;

/**
 * Clase que sirve para ejecutar procesos masivos via ws hacia equipos de tn
 * 
 * @author Allan Suarez <arsuarez@telconet.ec>
 * @version 1.0 26-04-2016
 */
class ProcesosMasivosWSController extends BaseWSController
{
    private static $CORTAR_ACCION   = 311;

    private static $strReactivarAccion = 315;

    private static $CANCELAR_ACCION = 313;
    
    private static $CORTAR_MOTIVO   = "Falta de pago";
    
    private static $CANCELAR_MOTIVO = "Cancelación por Solicitud Masiva";
    /**
     * Funcion que sirve para procesar las opciones que vienen desde el virgo para procesos masivos
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 26-04-2016
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 23-05-2017 - Se agrega nuevo op, por nuevo flujo de Demos
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 26-06-2018 - Se agrega nuevo op, para opción de renovación de licencias Office 365.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 25-10-2019 - Se agrego nueva op, para cambio de ultima milla
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 12-05-2020 - Se agrego nueva op, para reactivación cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 27-05-2020 - Se modifica el nombre de la op: CortarCliente -> CortarClienteTN y ReactivarCliente -> ReactivarClienteTN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 22-06-2020 - Se agrego nueva op, para el control de la capacidad de la interface
     *
     * @param $request
     */
    public function procesarAction(Request $request)
    {
        $data           = json_decode($request->getContent(),true);
        $response       = null;        
        $objResponse    = new Response();
        $op             = $data['op'];
        
        if($op)
        {
            switch($op)
            {
                case 'CortarClienteTN':
                    $response = $this->putCortarServicio($data);
                    break;
                case 'CancelarCliente':                    
                    $response = $this->putCancelarServicio($data);
                    break;
                case 'CambioPlanMasivo':                    
                    $response = $this->putCambiarPlanServicio($data);
                    break;
                case 'Demos':                    
                    $response = $this->putEjecucionDemos($data);
                    break;                
                case 'ReactivarServiciosPuntos':
                    $response = $this->reactivarServiciosPuntos($data);
                    break;
                case 'RenovarLicenciaOffice365':
                    $response = $this->renovarLicenciaOffice365($data);
                    break;
                case 'CambioUltimaMilla':
                    $response = $this->realizarCambioUltimaMilla($data);
                    break;
                case 'ReactivarClienteTN':
                    $response = $this->putReactivarServicio($data);
                    break;
                case 'ControlBwMasivo':
                    $response = $this->putControlBwInterface($data);
                    break;
                default:
                    $response['status']  = "ERROR";
                    $response['mensaje'] = "Metodo ".$op." no valido/inexistente";
            }
        }
        if(isset($response))
        {           
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($response));
        }
        return $objResponse;
    }
    
    /**
     * Funcion que sirve para obtener la informacion tecnica completa por servicio enviado
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 08-09-2020 - Se envía el parámetro $servicePlanificar a la función generarArrayClientes
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 29-03-2019 - Se envía el parámetro $serviceCliente a la función generarArrayClientes
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 15-06-2016 - Se recibe la cadena de servicios validos a obtener informacion tecnica
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 27-04-2016
     *
     * @param  type $array [ $strServicios ]
     * @return Array $arrayServicios
     */
    private function getArrayServiciosConfigurar($strServicios)
    {
        $emComercial = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceTecnico  = $this->get('tecnico.InfoServicioTecnico');   
        $serviceCliente  = $this->get('comercial.Cliente');
        $servicePlanificar = $this->get('planificacion.planificar');
        
        //variables para conexion a la base
        $arrayOciCon['user_comercial']   = $this->container->getParameter('user_comercial');
        $arrayOciCon['passwd_comercial'] = $this->container->getParameter('passwd_comercial');
        $arrayOciCon['dsn']              = $this->container->getParameter('database_dsn');
              
        $arrayParam = array('servicios'      => trim($strServicios, ','),
                            'estado'         => 'Todos',
                            "ociCon"         => $arrayOciCon,
                            "serviceTecnico" => $serviceTecnico,
                            "serviceCliente" => $serviceCliente,
                            "planificarService" => $servicePlanificar,
                           );

        $arrayServicios = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")->generarArrayClientes($arrayParam);
        
        return $arrayServicios;
    }
    
    
     /**
     * putEjecucionDemos
     *
     * @author Richard Cabrera  <rcabrera@telconet.ec>
     * @version 1.0 16-05-2017
     * 
     * Funcion encargada de ejecutar los demos
     *
     * @param array $arrayParametros [strFechaInicio    => fecha de inicio del reporte de arcotel
     *                                strFechaFin       => fecha fin del reporte de arcotel
     *                                strPrefijoEmpresa => prefijo de la empresa
     *                                strUser           => usuario que ejecuta la funcion
     *                                usrCreacion         => ip desde donde se ejecuta la funcion ]
     *
     * @return array $arrayRespuesta
     *
     */    
    public function putEjecucionDemos($arrayParametros)
    {
        $serviceUtil    = $this->get('schema.Util');
        $strIpCliente   = "127.0.0.1";
        $arrayRespuesta = array();

        try
        {
            //Se ejecutan demos pendientes
            $arrayRespuesta = $this->putCambiarPlanServicio($arrayParametros);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'ProcesosMasivosWSController->putEjecucionDemos', 
                                      $e->getMessage(),
                                      $arrayParametros['usrCreacion'],
                                      $strIpCliente
                                     );            
           
            $arrayRespuesta = $this->getArrayException($e);                    
        }

        return $arrayRespuesta;
    }


    /**
     * Funcion que sirve para cortar los servicios
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 26-04-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 16-06-2016 - Se valida servicios existentes y formacion de respuesta bajo error para devolver a cliente de WS
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 15-04-2020 - Si la empresa del servicio es TN y se corta correctamente se ingresa el historial del servicio,
     *                           caso contrario si no se corta el servicio TN se envía un correo electrónico.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     *
     * @param array  $data
     * @return Array $response [ status , mensaje , servicio ]
     */
    private function putCortarServicio($data)
    {
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceUtil = $this->get('schema.Util');
        
        ini_set('max_execution_time', 400000);                      
        
        $arrayResultado = array();
        $arrayRespuesta = array();
        $strServicios   = "";
        
        try
        {                                                
            $serviceCortarServicio  = $this->get('tecnico.InfoCortarServicio');                        
            $serviceReconectarWifi  = $this->get('tecnico.InfoElementoWifi');

            //Se verifica previamente que servicio es existente
            foreach($data['data']['servicios'] as $servicio)
            {
                $objServicio  = $emComercial->getRepository("schemaBundle:InfoServicio")->find($servicio);
                
                //Se obtienen los servicios a ser cortados concatenados con "," para ser enviados al PACKAGE TECNICO
                if($objServicio)
                {
                    $strServicios = $strServicios . $servicio . ",";
                }
                else
                {
                    $arrayResultadoServicio['status']   = 404;
                    $arrayResultadoServicio['mensaje']  = "Servicio a configurar no existe";
                    $arrayResultadoServicio['servicio'] = intval($servicio);
                
                    $arrayRespuesta[] = $arrayResultadoServicio;
                }
            }
            
            if($strServicios != "")
            {
                //Se obtiene el motivo
                $emGeneral = $this->getDoctrine()->getManager("telconet_general");            
                $objMotivo = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findOneByNombreMotivo(self::$CORTAR_MOTIVO);
                $objTipoSolicitud   = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneBy(array(  "descripcionSolicitud" => "SOLICITUD CORTE MASIVO",
                                                            "estado"               => "Activo"));

                $arrayServicios = $this->getArrayServiciosConfigurar($strServicios);                                               
                      
                foreach($arrayServicios['resultado'] as $dataServicio)
                {                
                    if($dataServicio['estado'] != 'In-Corte')
                    {
                        if(is_object($objTipoSolicitud))
                        {
                            //obtengo el detalle de la solicitud
                            $objSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                            ->createQueryBuilder('p')
                                                            ->where('p.tipoSolicitudId =  :tipoSolicitudId')
                                                            ->andWhere("p.servicioId   =  :servicioId")
                                                            ->andWhere("p.estado       != :estado")
                                                            ->setParameter('tipoSolicitudId', $objTipoSolicitud->getId())
                                                            ->setParameter('servicioId',      $dataServicio['idServicio'])
                                                            ->setParameter('estado',          'Finalizada')
                                                            ->orderBy('p.id', 'DESC')
                                                            ->setMaxResults(1)
                                                            ->getQuery()
                                                            ->getOneOrNullResult();
                            if(is_object($objSolicitud))
                            {
                                $objMotivo = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->find($objSolicitud->getMotivoId());
                            }
                        }

                        $dataServicio['idMotivo']    =  $objMotivo->getId();
                        $dataServicio['idAccion']    =  self::$CORTAR_ACCION;
                        $dataServicio['idProducto']  =  $dataServicio['productoId'];
                        $dataServicio['usrCreacion'] =  $data['data']['usrCreacion'];
                        $dataServicio['ipCreacion']  =  $data['data']['ipCreacion'];

                        if( $dataServicio['descripcionProducto'] == 'L3MPLS' || $dataServicio['descripcionProducto'] == 'L3MPLS SDWAN' ||
                            $dataServicio['descripcionProducto'] == 'INTERNET' || $dataServicio['descripcionProducto'] == 'INTMPLS' ||
                            $dataServicio['descripcionProducto'] == 'TUNELIP' || $dataServicio['descripcionProducto'] == 'INTERNET SDWAN' )
                        {
                            $arrayResponse = $serviceCortarServicio->cortarServicioTN($dataServicio);

                            $arrayResultadoServicio['status']   = $arrayResponse[0]['status']=="OK" ? 200 : 500;
                            $arrayResultadoServicio['mensaje']  = $arrayResponse[0]['status']=="OK" ?
                                                                  "Se Corto el Servicio" : $arrayResponse[0]['mensaje'];
                        }
                        elseif( $dataServicio['descripcionProducto'] == 'INTERNET WIFI' )
                        {
                            $dataServicio['motivo']     = $objMotivo->getId();
                            $dataServicio['capacidad1'] = $dataServicio['capacidadUno'];
                            $dataServicio['capacidad2'] = $dataServicio['capacidadDos'];
                            $arrayResponse = $serviceReconectarWifi->cortarServicio($dataServicio);

                            $arrayResultadoServicio['status']   = $arrayResponse[0]['status']=="OK" ? 200 : 500;
                            $arrayResultadoServicio['mensaje']  = $arrayResponse[0]['status']=="OK" ?
                                                                  "Se Corto el Servicio" : $arrayResponse[0]['mensaje'];
                        }
                        elseif( $dataServicio['descripcionProducto'] == 'OTROS' )
                        {
                            $dataServicio['usrCreacion']   = $data['data']['usrCreacion'];
                            $dataServicio['clientIp']      = $data['data']['ipCreacion'];
                            $dataServicio['strCodEmpresa'] = $dataServicio['idEmpresa'];
                            $arrayResponse = $serviceCortarServicio->cortarServiciosOtros($dataServicio);

                            $arrayResultadoServicio['status']   = $arrayResponse['status']=="OK" ? 200 : 500;
                            $arrayResultadoServicio['mensaje']  = $arrayResponse['status']=="OK" ?
                                                                  "Se Corto el Servicio" : $arrayResponse['mensaje'];
                        }

                        $arrayResultadoServicio['servicio'] = $dataServicio['idServicio'];
                        if( $arrayResultadoServicio['status'] == 200 && $dataServicio['prefijoEmpresa'] == 'TN' &&
                            isset($objSolicitud) && is_object($objSolicitud) )
                        {
                            //obtengo el servicio
                            $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($dataServicio['idServicio']);
                            if(is_object($objInfoServicio))
                            {
                                //actualizo el id de la solicitud con el ultimo registro
                                $objSolicitud->setEstado("Finalizada");
                                $objSolicitud->setObservacion('Se finalizó la solicitud del corte del servicio (ProcesoMasivo).');
                                $emComercial->persist($objSolicitud);
                                $emComercial->flush();

                                //registro historial a la solicitud
                                $objDetalleSolHistorial = new InfoDetalleSolHist();
                                $objDetalleSolHistorial->setDetalleSolicitudId($objSolicitud);
                                $objDetalleSolHistorial->setIpCreacion($dataServicio['ipCreacion']);
                                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                                $objDetalleSolHistorial->setUsrCreacion($dataServicio['usrCreacion']);
                                $objDetalleSolHistorial->setEstado($objSolicitud->getEstado());
                                $objDetalleSolHistorial->setObservacion('Se finalizó la solicitud del corte '.
                                                                        'del servicio (ProcesoMasivo).');
                                $emComercial->persist($objDetalleSolHistorial);
                                $emComercial->flush();

                                //registro servicio historial
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objInfoServicio);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setUsrCreacion($dataServicio['usrCreacion']);
                                $objServicioHistorial->setIpCreacion($dataServicio['ipCreacion']);
                                $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                                $objServicioHistorial->setObservacion('Se finalizó la solicitud del corte del servicio (ProcesoMasivo).');
                                $emComercial->persist($objServicioHistorial);
                                $emComercial->flush();
                            }
                        }
                        elseif( $dataServicio['prefijoEmpresa'] == 'TN' && $arrayResultadoServicio['status'] == 500 )
                        {
                            try
                            {
                                //tipo de region del servicio
                                $strRegionServivio = '';
                                //arreglo de correos para enviar
                                $arrayToMail     = array();
                                //obtengo la empresa
                                $objEmpresa      = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo('TN');
                                //obtengo el servicio
                                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($dataServicio['idServicio']);
                                if( is_object($objInfoServicio) )
                                {
                                    //obtengo el punto del servicio
                                    $objInfoPunto = $objInfoServicio->getPuntoId();

                                    //registro servicio historial
                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objInfoServicio);
                                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorial->setUsrCreacion($dataServicio['usrCreacion']);
                                    $objServicioHistorial->setIpCreacion($dataServicio['ipCreacion']);
                                    $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                                    $objServicioHistorial->setObservacion($arrayResultadoServicio['mensaje']);
                                    $emComercial->persist($objServicioHistorial);
                                    $emComercial->flush();

                                    //obtengo el tipo de distrito del punto
                                    if(is_object($objInfoPunto))
                                    {
                                        //obtengo el sector
                                        $objSector = $objInfoPunto->getSectorId();
                                        if(is_object($objSector))
                                        {
                                            //obtengo la parroquia
                                            $objParroquia = $objSector->getParroquiaId();
                                            if(is_object($objParroquia))
                                            {
                                                //obtengo el canton
                                                $objCanton = $objParroquia->getCantonId();
                                                if(is_object($objCanton))
                                                {
                                                    $strRegionServivio = $objCanton->getRegion();
                                                }
                                            }
                                        }
                                    }

                                    //obtengo el mail del asistente o vendedor
                                    $strMailAsistente = '';
                                    $objInfoPersona   = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->findOneByLogin($objInfoServicio->getUsrCreacion());
                                    if(is_object($objInfoPersona) )
                                    {
                                        $arrayParametroMail                   = array();
                                        $arrayParametroMail['strLogin']       = $objInfoPersona->getLogin();
                                        $arrayParametroMail['intIdEmp']       = $objEmpresa->getId();
                                        $arrayParametroMail['objUtilService'] = $serviceUtil;
                                        $strMailAsistente = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->getMailNaf($arrayParametroMail);
                                    }
                                    if( is_object($objInfoPunto) && empty($strMailAsistente) )
                                    {
                                        $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->findOneByLogin($objInfoPunto->getUsrVendedor());
                                        if(is_object($objInfoPersona))
                                        {
                                            $arrayParametroMail                   = array();
                                            $arrayParametroMail['strLogin']       = $objInfoPersona->getLogin();
                                            $arrayParametroMail['intIdEmp']       = $objEmpresa->getId();
                                            $arrayParametroMail['objUtilService'] = $serviceUtil;
                                            $strMailAsistente = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                            ->getMailNaf($arrayParametroMail);
                                        }
                                    }
                                    if( !empty($strMailAsistente) )
                                    {
                                        $arrayToMail[] = $strMailAsistente;
                                    }
                                }

                                //obtengo el mail del usuario
                                if( isset($objSolicitud) && is_object($objSolicitud) )
                                {
                                    $objProcesoMasivoDet = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                ->findOneBySolicitudId($objSolicitud->getId());
                                    if(is_object($objProcesoMasivoDet))
                                    {
                                        $objInfoPersona   = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                            ->findOneByLogin($objProcesoMasivoDet->getUsrCreacion());
                                        if(is_object($objInfoPersona) )
                                        {
                                            $arrayParametroMail                   = array();
                                            $arrayParametroMail['strLogin']       = $objInfoPersona->getLogin();
                                            $arrayParametroMail['intIdEmp']       = $objEmpresa->getId();
                                            $arrayParametroMail['objUtilService'] = $serviceUtil;
                                            $strMailUsuario = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                            ->getMailNaf($arrayParametroMail);
                                            if( !empty($strMailUsuario) )
                                            {
                                                $arrayToMail[] = $strMailUsuario;
                                            }
                                        }
                                    }
                                }

                                //enviar notificación del error por correo electrónico
                                $objMailer      = $this->get('schema.Mailer');
                                $strTwigMail    = 'tecnicoBundle:InfoServicio:mailerErrorCortarClienteMasivo.html.twig';
                                $strAsuntoMail  = "Notificación Error: Cortar Servicio Masivo TN";
                                $strFromMail    = "notificaciones_telcos@telconet.ec";
                                $arrayDatosMail = array(
                                    'strLogin'     => $dataServicio['login'],
                                    'strLoginAux'  => $dataServicio['loginAux'],
                                    'strMensaje'   => $arrayResultadoServicio['mensaje'],
                                );

                                //obtengo los correos para el envío de la notificación
                                $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                            array(  'nombreParametro'   => 'CORREOS_RESPUESTA_CORTE_REACTIVAR_SERVICIO_MASIVO',
                                                                    'estado'            => 'Activo'));
                                if( !is_object($objAdmiParametroCab) )
                                {
                                    throw new \Exception('No se encontraron los correos para las notificaciones '.
                                                         'del corte masivo del servicio en el telcos.');
                                }

                                //verifico que no este vacia la region
                                if(!empty($strRegionServivio))
                                {
                                    $arrayParametrosCorreos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                            array(  "parametroId"   => $objAdmiParametroCab->getId(),
                                                                                    "valor1"        => "CORTE_MASIVO",
                                                                                    "valor3"        => $strRegionServivio,
                                                                                    "estado"        => "Activo"));
                                    foreach($arrayParametrosCorreos as $objParametroCorreo)
                                    {
                                        $arrayToMail[] = $objParametroCorreo->getValor2();
                                    }
                                }

                                //obtengo los correos principales
                                $arrayParametrosCorAll  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                        array(  "parametroId"   => $objAdmiParametroCab->getId(),
                                                                                "valor1"        => "CORTE_MASIVO",
                                                                                "valor3"        => 'PRINCIPAL',
                                                                                "estado"        => "Activo"));
                                foreach($arrayParametrosCorAll as $objParametroCorreo)
                                {
                                    $arrayToMail[] = $objParametroCorreo->getValor2();
                                }

                                $objMailer->sendTwig($strAsuntoMail,
                                                     $strFromMail,
                                                     $arrayToMail,
                                                     $strTwigMail,
                                                     $arrayDatosMail);
                            }
                            catch(\Exception $ex)
                            {
                                $serviceUtil->insertError('Telcos+',
                                                          'ProcesosMasivosWSController.putCortarServicio',
                                                           $ex->getMessage(),
                                                           $dataServicio['usrCreacion'],
                                                           $dataServicio['ipCreacion']);
                            }
                        }
                    }
                    else
                    {
                        $arrayResultadoServicio['status']   = 200;
                        $arrayResultadoServicio['mensaje']  = "Se Corto el Servicio";
                        $arrayResultadoServicio['servicio'] = $dataServicio['idServicio'];
                    }

                    $arrayRespuesta[] = $arrayResultadoServicio;
                }              
            }                  
            
            $arrayResultado['servicios'] = $arrayRespuesta;
        } 
        catch (\Exception $e) 
        {
            $arrayResultado = $this->getArrayException($e);
        }
                
        return $arrayResultado;
    }
    
    /**
     * Funcion que sirve para cancelar los servicios
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 26-04-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 16-06-2016 - Se valida servicios existentes y formacion de respuesta bajo error para devolver a cliente de WS
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 22-11-2016 - Se modifica para validar si un servicio es pseudope para ejecutar la funcion determinada para cada
     *                           escenario
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 10-02-2017 - Se modifica para validar si un servicio es pseudope para ejecutar la funcion determinada para cada
     *                           escenario ( se valida y se pregunta por bandera S o N de acuerdo lo venido en la informacion tecnica del servicio )
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 03-01-2018 - Se agrega validación para cancelación de servicios con producto INTERNET WIFI
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 26-03-2018 - Se realizan ajustes para controlar excepciones y provocar caidas del PMA java
     * @since 1.4
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 19-03-2019 - Se agrega nombre técnico TELCOHOME para realizar la cancelación de dichos servicios
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.7 16-12-2019 - Se agrega funcionalidad para que pueda ser cancelado de manera masiva el servicio "Wifi Alquiler Equipos".
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 08-09-2020 - Se agrega la opción para cancelar servicios con nombre técnico del producto 'TELEFONIA_NETVOICE'
     *
     * @param array  $data
     * @return Array $response [ status , mensaje , servicio ]
     */
    private function putCancelarServicio($data)
    {
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        ini_set('max_execution_time', 400000);        
        
        $arrayResultado = array();
        $arrayRespuesta = array();    
        $strServicios   = "";
        
        try
        {
            $serviceCancelarServicio  = $this->get('tecnico.InfoCancelarServicio');                                                
            $serviceElementoWifi      = $this->get('tecnico.InfoElementoWifi');            
            $serviceTelefonia         = $this->get('tecnico.InfoTelefonia');
            //Se verifica previamente que servicio es existente
            foreach($data['data']['servicios'] as $servicio)
            {
                $objServicio  = $emComercial->getRepository("schemaBundle:InfoServicio")->find($servicio);
                
                //Se obtienen los servicios a ser cortados concatenados con "," para ser enviados al PACKAGE TECNICO
                if($objServicio)
                {
                    $strServicios = $strServicios . $servicio . ",";
                }
                else
                {
                    $arrayResultadoServicio['status']   = 404;
                    $arrayResultadoServicio['mensaje']  = "Servicio a configurar no existe";
                    $arrayResultadoServicio['servicio'] = intval($servicio);
                
                    $arrayRespuesta[] = $arrayResultadoServicio;
                }
            }
            
            if($strServicios != "")
            {
                $emComercial    = $this->getDoctrine()->getManager("telconet");     
                //Se obtiene el motivo
                $emGeneral = $this->getDoctrine()->getManager("telconet_general");            
                $objMotivo = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findOneByNombreMotivo(self::$CANCELAR_MOTIVO);
                
                $arrayServicios = $this->getArrayServiciosConfigurar($strServicios);                                               
                      
                foreach($arrayServicios['resultado'] as $dataServicio)
                {                                
                    if($dataServicio['estado'] != 'Cancel')
                    {
                        $dataServicio['idMotivo']    =  $objMotivo->getId();
                        $dataServicio['idAccion']    =  self::$CANCELAR_ACCION;
                        $dataServicio['idProducto']  =  $dataServicio['productoId'];
                        $dataServicio['usrCreacion'] =  $data['data']['usrCreacion'];
                        $dataServicio['ipCreacion']  =  $data['data']['ipCreacion'];

                        //Si la bandera es pseudoPe existe y es SI se ejecuta flujo para PseudoPe
                        if(isset($dataServicio['esPseudoPe']) && $dataServicio['esPseudoPe']=='S')
                        {
                            try
                            {
                                $arrayResponse = $serviceCancelarServicio->cancelarServicioPseudoPe($dataServicio);
                            }
                            catch (\Exception $objEx) 
                            {
                                $arrayResponse   = array();
                                $arrayResponse[] = array('statusCode' => 500,
                                                         'status'     => 'ERROR',
                                                         'mensaje'    => strlen($objEx->getMessage())>4000?
                                                                         substr($objEx->getMessage(), -4000, 4000):$objEx->getMessage(),
                                                         'servicio'   => $dataServicio['idServicio']
                                                        );
                            }
                        }
                        else if(isset($dataServicio['descripcionProducto']) && $dataServicio['descripcionProducto'] === 'INTERNET WIFI')
                        {
                            $strRegionServicio                = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->getRegionPorServicio($dataServicio['idServicio']);
                            $arrayParamDetResponsableRetiro   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->getOne( 'RESPONSABLES_RETIRO_EQUIPO', 
                                                                                    '', 
                                                                                    '', 
                                                                                    '', 
                                                                                    $strRegionServicio ? $strRegionServicio: "R2", 
                                                                                    '', 
                                                                                    '', 
                                                                                    '');
                            $strLoginPersonaTarea       = $arrayParamDetResponsableRetiro['valor3'];
                            $strIdDepartamentoTarea     = $arrayParamDetResponsableRetiro['valor2'];
                            
                            $objPersonaTarea            = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                      ->findOneByLogin($strLoginPersonaTarea);
                            $strIdentifPersonaTarea     = $objPersonaTarea->getIdentificacionCliente();
                            $objPersonaEmpresaRolTarea  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                      ->findByIdentificacionTipoRolEmpresa( $strIdentifPersonaTarea, 
                                                                                                            'Empleado', 
                                                                                                            '10');
                            $dataServicio['intIdDepartamento']      = $strIdDepartamentoTarea;
                            $dataServicio['motivo']                 = $objMotivo->getId();
                            $dataServicio['idPersonaEmpresaRol']    = $objPersonaEmpresaRolTarea->getId();
                            try
                            {
                                $arrayResponse  = $serviceElementoWifi->cancelarServicio($dataServicio);
                            }
                            catch (\Exception $objEx) 
                            {
                                $arrayResponse   = array();
                                $arrayResponse[] = array('statusCode' => 500,
                                                         'status'     => 'ERROR',
                                                         'mensaje'    => strlen($objEx->getMessage())>4000?
                                                                         substr($objEx->getMessage(), -4000, 4000):$objEx->getMessage(),
                                                         'servicio'   => $dataServicio['idServicio']
                                                        );
                            }
                            
                            if(isset($arrayResponse[0]['status']) && $arrayResponse[0]['status'] === 'OK')
                            {
                                $arrayResponse[0]['statusCode'] = 200;
                            }
                            else
                            {
                                $arrayResponse[0]['statusCode'] = 500;
                            }
                        }
                        else if(isset($dataServicio['nombreProducto']) && $dataServicio['nombreProducto'] === 'WIFI Alquiler Equipos')
                        {
                            $strRegionServicio                = $emComercial->getRepository('schemaBundle:InfoServicio')
                                ->getRegionPorServicio($dataServicio['idServicio']);
                            $arrayParamDetResponsableRetiro   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                ->getOne( 'RESPONSABLES_RETIRO_EQUIPO',
                                    '',
                                    '',
                                    '',
                                    $strRegionServicio ? $strRegionServicio: "R2",
                                    '',
                                    '',
                                    '');
                            $strLoginPersonaTarea       = $arrayParamDetResponsableRetiro['valor3'];
                            $strIdDepartamentoTarea     = $arrayParamDetResponsableRetiro['valor2'];

                            $objPersonaTarea            = $emComercial->getRepository('schemaBundle:InfoPersona')
                                ->findOneByLogin($strLoginPersonaTarea);
                            $strIdentifPersonaTarea     = $objPersonaTarea->getIdentificacionCliente();
                            $objPersonaEmpresaRolTarea  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->findByIdentificacionTipoRolEmpresa( $strIdentifPersonaTarea,
                                    'Empleado',
                                    '10');
                            $dataServicio['intIdDepartamento']      = $strIdDepartamentoTarea;
                            $dataServicio['motivo']                 = $objMotivo->getId();
                            $dataServicio['idPersonaEmpresaRol']    = $objPersonaEmpresaRolTarea->getId();
                            try
                            {
                                $arrayResponse  = $serviceElementoWifi->cancelarWifiAlquilerEquipos($dataServicio);
                            }
                            catch (\Exception $objEx)
                            {
                                $arrayResponse   = array();
                                $arrayResponse[] = array('statusCode' => 500,
                                    'status'     => 'ERROR',
                                    'mensaje'    => strlen($objEx->getMessage())>4000?
                                        substr($objEx->getMessage(), -4000, 4000):$objEx->getMessage(),
                                    'servicio'   => $dataServicio['idServicio']
                                );
                            }

                            if(isset($arrayResponse[0]['status']) && $arrayResponse[0]['status'] === 'OK')
                            {
                                $arrayResponse[0]['statusCode'] = 200;
                            }
                            else
                            {
                                $arrayResponse[0]['statusCode'] = 500;
                            }
                        }
                        else if(isset($dataServicio['descripcionProducto'])
                            && ($dataServicio['descripcionProducto'] === 'INTERNET SMALL BUSINESS' 
                                ||  $dataServicio['descripcionProducto'] === 'TELCOHOME'))
                        {
                            $strRegionServicio                = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->getRegionPorServicio($dataServicio['idServicio']);
                            $arrayParamDetResponsableRetiro   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->getOne( 'RESPONSABLES_RETIRO_EQUIPO', 
                                                                                    '', 
                                                                                    '', 
                                                                                    '', 
                                                                                    $strRegionServicio ? $strRegionServicio: "R2", 
                                                                                    '', 
                                                                                    '', 
                                                                                    '');
                            $strLoginPersonaTarea       = $arrayParamDetResponsableRetiro['valor3'];
                            $strIdDepartamentoTarea     = $arrayParamDetResponsableRetiro['valor2'];
                            
                            $objPersonaTarea            = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                      ->findOneByLogin($strLoginPersonaTarea);
                            $strIdentifPersonaTarea     = $objPersonaTarea->getIdentificacionCliente();
                            $objPersonaEmpresaRolTarea  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                      ->findByIdentificacionTipoRolEmpresa( $strIdentifPersonaTarea, 
                                                                                                            'Empleado', 
                                                                                                            '10');
                            $arrayParametros = array(   'strOpcion'             => 'ProcesoMasivo',
                                                        'idProducto'            => $dataServicio['idProducto'],
                                                        'idServicio'            => $dataServicio['idServicio'],
                                                        'idAccion'              => $dataServicio['idAccion'],
                                                        'intIdDepartamento'     => $strIdDepartamentoTarea,
                                                        'login'                 => $dataServicio['login'],
                                                        'idEmpresa'             => $dataServicio['idEmpresa'],
                                                        'usrCreacion'           => $dataServicio['usrCreacion'],
                                                        'ipCreacion'            => $dataServicio['ipCreacion'],
                                                        'motivo'                => $objMotivo,
                                                        'idPersonaEmpresaRol'   => $objPersonaEmpresaRolTarea->getId()
                                                    );
                            try
                            {
                                $arrayResponse = $serviceCancelarServicio->cancelarServicioIsb($arrayParametros);
                            }
                            catch (\Exception $objEx) 
                            {
                                $arrayResponse = array();
                                $arrayResponse[] = array('statusCode' => 500,
                                                         'status'     => 'ERROR',
                                                         'mensaje'    => strlen($objEx->getMessage())>4000?
                                                                         substr($objEx->getMessage(), -4000, 4000):$objEx->getMessage(),
                                                         'servicio'   => $dataServicio['idServicio']
                                                        );
                            }
                        }
                        else if(isset($dataServicio['descripcionProducto']) && $dataServicio['descripcionProducto'] === 'TELEFONIA_NETVOICE')
                        {
                            $arrayResponse   = array();
                            $arrayParametros = array(
                                                    'intIdServicio'     => $dataServicio['idServicio'],
                                                    'strUser'           => $dataServicio['usrCreacion'],
                                                    'strIpClient'       => $dataServicio['ipCreacion'],
                                                    'strPrefijoEmpresa' => $dataServicio['prefijoEmpresa'],
                                                    'intCodEmpresa'     => $dataServicio['idEmpresa']
                                                );
                            try
                            {
                                $arrayResponse[] = $serviceTelefonia->cancelarLineas($arrayParametros);
                                if($arrayResponse[0]['status'] === "OK")
                                {
                                    $arrayResponse[0]['statusCode'] = 200;
                                }
                                else
                                {
                                    $arrayResponse[0]['statusCode'] = 500;
                                }
                                $arrayResponse[0]['servicio'] = $dataServicio['idServicio'];
                            }
                            catch (\Exception $objEx)
                            {
                                $arrayResponse[] = array(
                                                        'statusCode' => 500,
                                                        'status'     => 'ERROR',
                                                        'mensaje'    => substr($objEx->getMessage(), 0, 4000),
                                                        'servicio'   => $dataServicio['idServicio']
                                                    );
                            }
                        }
                        else
                        {
                            try
                            {
                                $arrayResponse = $serviceCancelarServicio->cancelarServicioTn($dataServicio);
                            }
                            catch (\Exception $objEx) 
                            {
                                $arrayResponse = array();
                                $arrayResponse[] = array('statusCode' => 500,
                                                         'status'     => 'ERROR',
                                                         'mensaje'    => strlen($objEx->getMessage())>4000?
                                                                         substr($objEx->getMessage(), -4000, 4000):$objEx->getMessage(),
                                                         'servicio'   => $dataServicio['idServicio']
                                                        );
                            }
                        }                        
                        $arrayResultadoServicio['status']   = $arrayResponse[0]['statusCode'];
                        $arrayResultadoServicio['mensaje']  = $arrayResponse[0]['status']=="OK"?
                                                              "Se Cancelo el Servicio":$arrayResponse[0]['mensaje'];
                        $arrayResultadoServicio['servicio'] = $dataServicio['idServicio'];
                    }
                    else
                    {
                        $arrayResultadoServicio['status']   = 200;
                        $arrayResultadoServicio['mensaje']  = "Se Cancelo el Servicio";
                        $arrayResultadoServicio['servicio'] = $dataServicio['idServicio'];
                    }

                    $arrayRespuesta[] = $arrayResultadoServicio;
                }              
            }                        
           
            $arrayResultado['servicios'] = $arrayRespuesta;
        } 
        catch (\Exception $e) 
        {
            $arrayResultado = $this->getArrayException($e);
        }
        
        return $arrayResultado;
    }        
    
     /**
     * Funcion que sirve para realizar cambio de plan a los servicios
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 04-05-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 16-06-2016 - Se valida servicios existentes y formacion de respuesta bajo error para devolver a cliente de WS
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 16-05-2017 - Se realizan ajustes para reutilizar la funcion para el proceso de Demos
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 26-03-2018 - Se realizan ajustes para controlar excepciones y provocar caidas del PMA java
     * @since 1.2
     *
     * @param array  $data
     * @return Array $response [ status , mensaje , servicio ]
     */
    private function putCambiarPlanServicio($data)
    {
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        ini_set('max_execution_time', 400000);        
        
        $arrayResultado = array();
        $arrayRespuesta = array();    
        $strServicios   = "";
        $strTipoProceso = "";
        
        try
        {
            $serviceCambiarPlanServicio  = $this->get('tecnico.InfoCambiarPlan'); 

            //Se verifica previamente que servicio es existente
            foreach($data['data']['servicios'] as $servicio)
            {
                $objServicio  = $emComercial->getRepository("schemaBundle:InfoServicio")->find($servicio);
                
                //Se obtienen los servicios a ser cortados concatenados con "," para ser enviados al PACKAGE TECNICO
                if($objServicio)
                {
                    $strServicios = $strServicios . $servicio . ",";
                }
                else
                {
                    $arrayResultadoServicio['status']   = 404;
                    $arrayResultadoServicio['mensaje']  = "Servicio a configurar no existe";
                    $arrayResultadoServicio['servicio'] = intval($servicio);
                
                    $arrayRespuesta[] = $arrayResultadoServicio;
                }
            }
                                   
            if($strServicios != "")
            {                                                
                $arrayServicios = $this->getArrayServiciosConfigurar($strServicios);                                               
                
                if($data["op"] === "Demos")
                {
                    $strTipoProceso = "Demos";
                }
                
                if($data["op"] === "CambioPlanMasivo")
                {
                    $strTipoProceso = "CambioPlan";
                }                

                foreach($arrayServicios['resultado'] as $dataServicio)
                {                                                
                    $dataServicio['idProducto']  =  $dataServicio['productoId'];
                    $dataServicio['usrCreacion'] =  $data['data']['usrCreacion'];
                    $dataServicio['ipCreacion']  =  $data['data']['ipCreacion'];
                    $dataServicio['tipoProceso'] =  $strTipoProceso;                        
                    
                    try
                    {
                        $arrayResponse = $serviceCambiarPlanServicio->cambioPlanTn($dataServicio);
                    }
                    catch (\Exception $objEx) 
                    {
                        $arrayResponse   = array();
                        $arrayResponse[] = array('statusCode' => 500,
                                                 'status'     => 'ERROR',
                                                 'mensaje'    => strlen($objEx->getMessage())>4000?
                                                                 substr($objEx->getMessage(), -4000, 4000):$objEx->getMessage(),
                                                 'servicio'   => $dataServicio['idServicio']
                                                );
                    }

                    $arrayResultadoServicio['status']   = $arrayResponse[0]['statusCode'];
                    $arrayResultadoServicio['mensaje']  = $arrayResultadoServicio['status']=="OK"?"Se realizo Cambio Plan":$arrayResponse[0]['mensaje'];
                    $arrayResultadoServicio['servicio'] = $dataServicio['idServicio'];

                    $arrayRespuesta[] = $arrayResultadoServicio;
                } 
            }
            
            $arrayResultado['servicios'] = $arrayRespuesta;
        } 
        catch (\Exception $e) 
        {            
            $arrayResultado = $this->getArrayException($e);
        }
        
        return $arrayResultado;
    }
    
    /**
     * Funcion que sirve para devolver errores por Excepcion en los metodos invocados
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 15-06-2016
     * 
     * @param type $e
     * @return array [ status , mensaje , servicio ]
     */
    private function getArrayException($e)
    {
        $arrayRespuesta = array();
        
        if($e->getMessage() == "NULL")
        {               
            $arrayResultadoServicio['mensaje']   = $this->mensaje['NULL'];
        }        
        else
        {             
            $arrayResultadoServicio['mensaje']   = $e->getMessage();
        }

        $arrayResultadoServicio['status']    = 500;
        $arrayResultadoServicio['servicio']  = 0;

        $arrayRespuesta[] = $arrayResultadoServicio;

        $arrayResultado['servicios'] = $arrayRespuesta;
        
        return $arrayResultado;
    }


    /**
    * Funcion que sirve el para reactivar los puntos que ya no cuentan con deudas pendientes
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.0 25-09-2017
    * @since 1.0
    * @param type $arrayData
    * @return string
    *
    * @author Héctor Lozano <hlozano@telconet.ec>
    * @version 1.1 18-10-2018
    * @since 1.1 - Se agregan registros en logs, para tener un historial de lo realizado en la función.
    */
    private function reactivarServiciosPuntos($arrayData)
    {
        $arrayRespuesta = array();
        $serviceUtil          = $this->get('schema.Util');
        $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');
        try
        {
            $arrayParams=array('puntos'          => $arrayData['data']['puntos'],
                               'prefijoEmpresa'  => $arrayData['data']['prefijoEmpresa'],
                               'empresaId'       => $arrayData['data']['empresaId'],
                               'oficinaId'       => $arrayData['data']['oficinaId'],
                               'usuarioCreacion' => $arrayData['data']['usuarioCreacion'],
                               'ip'              => $arrayData['data']['ip'],
                               'idPago'          => null,
                               'debitoId'        => $arrayData['data']['debitoId']
                              );
            
            /*Se Guardan los parámetros enviados */   
            $serviceProcesoMasivo->loggerProcesoMasivo('Función:reactivarServiciosPuntos-ProcesoMasivoWSController', 'Datos Envío:' .
                                                       ' |Puntos:' . json_encode($arrayData['data']['puntos']).
                                                       ' |Pref:'.$arrayData['data']['prefijoEmpresa']. ' |Emp:'.$arrayData['data']['empresaId']. 
                                                       ' |Ofic:'. $arrayData['data']['oficinaId']. ' |Usr:'.$arrayData['data']['usuarioCreacion'].
                                                       ' |Ip:'. $arrayData['data']['ip']. ' |DebId:'. $arrayData['data']['debitoId']);
            
            /* Reactivar los servicios */
            $arrayRespuesta['respuesta'] = $serviceProcesoMasivo->reactivarServiciosPuntos($arrayParams);
            
            /*Se almacena la respuesta obtenida */   
            $serviceProcesoMasivo->loggerProcesoMasivo('Función:reactivarServiciosPuntos-ProcesoMasivoWSController', 'Respuesta Obtenida: '. 
                                                       $arrayRespuesta['respuesta']. ' |Usr:'.$arrayData['data']['usuarioCreacion'].
                                                       ' |Ip:'. $arrayData['data']['ip'] . ' |DebId:'. $arrayData['data']['debitoId']);
            
            $serviceProcesoMasivo->loggerProcesoMasivo('-------------------------------------', '-------------------------------------');
        }
        catch(\Exception $ex)
        {
            $serviceProcesoMasivo->loggerProcesoMasivo('Error: TecnicoWSController.reactivarServiciosPuntos', 'Excepción: '.$ex->getMessage(). 
                                                       ' |Usr:'.$arrayData['data']['usuarioCreacion'] . ' |Ip:'. $arrayData['data']['ip'] . 
                                                       ' |DebId:'. $arrayData['data']['debitoId']);
            
            $serviceUtil->insertError('Telcos+',
                                      'TecnicoWSController.reactivarServiciosPuntos',
                                      'Error al reactivar los servicios. '.$ex->getMessage(),
                                       $arrayData['data']['usuarioCreacion'],
                                       $arrayData['data']['ip']);

            $arrayRespuesta['respuesta'] = "ERROR: ".$ex->getMessage();
        }
        return $arrayRespuesta;
    }
    
    /**
    * Funcion que sirve el para renovar la licencia de servicios con producto Office 365
    *
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 26-06-2018
    * @since 1.0
    * @param type $arrayData
    * @return string
    */
    private function renovarLicenciaOffice365($arrayData)
    {
        $arrayRespuesta            = array();
        $serviceUtil               = $this->get('schema.Util');
        $serviceLicenciasOffice365 = $this->get('tecnico.LicenciasOffice365');

        try
        {
            $arrayParametrosWs=array('strPrefijoEmpresa'  => $arrayData['data']['prefijoEmpresa'],
                                     'strEmpresaCod'      => $arrayData['data']['empresaId'],
                                     'strUsuarioCreacion' => $arrayData['data']['usuarioCreacion'],
                                     'strIp'              => $arrayData['data']['ip'],
                                     'intServicioId'      => $arrayData['data']['servicioId'],
                                     'strAccion'          => $arrayData['data']['accion']
                                    );

            $arrayRespuesta['respuesta'] = $serviceLicenciasOffice365->renovarLicenciaOffice365($arrayParametrosWs);
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+',
                                      'TecnicoWSController.renovarLicenciaOffice365',
                                      'Error al renovarLicenciaOffice365 . '.$ex->getMessage(),
                                       $arrayData['data']['usuarioCreacion'],
                                       $arrayData['data']['ip']);

            $arrayRespuesta['respuesta'] = "ERROR: ".$ex->getMessage();
        }
        return $arrayRespuesta;
    }    
    
    /**
    * Documentación para el método 'realizarCambioUltimaMilla'.
    *
    * Metodo que realiza el cambio de ultima milla de interfaces de elementos SWITCH por medio de proceso masivo
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.0 25-10-2019
    * 
    * @param Array $arrayData [
    *                              op,                  Nombre del metodo a realizar la operación   
    *                              data [
    *                                  servicios,       Arreglo de los id de los servicios
    *                                  usrCreacion,     Usuario de creacion, quien ejecuta la accion
    *                                  ipCreacion       Ip de quien ejecuta la accion
    *                              ]
    *                         ]
    * @return Array $arrayRespuesta [ status , mensaje ]
    */
    private function realizarCambioUltimaMilla($arrayData)
    {
        ini_set('max_execution_time', 400000);
        $serviceUtil                = $this->get('schema.Util');
        $serviceElemento            = $this->get('tecnico.InfoElemento'); 
        try
        {
            if( !isset($arrayData['data']['servicios']) || empty($arrayData['data']['servicios']) || 
                !is_array($arrayData['data']['servicios']) || count($arrayData['data']['servicios']) == 0 )
            {
                throw new \Exception("No hay servicios para cambio de ultima milla.");
            }
            $arrayParametrosWs      =   array(
                                            'servicios'         => $arrayData['data']['servicios'],
                                            'strUsrCreacion'    => $arrayData['data']['usrCreacion'],
                                            'strIpCreacion'     => $arrayData['data']['ipCreacion']
                                        );
            $arrayRespuesta         = $serviceElemento->realizarCambioUMProcesoMasivo($arrayParametrosWs);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos+',
                                      'ProcesosMasivosWSController.realizarCambioUltimaMilla',
                                       $e->getMessage(),
                                       $arrayData['data']['usrCreacion'],
                                       $arrayData['data']['ipCreacion']);
            $arrayRespuesta = $this->getArrayException($e);
        }
        return $arrayRespuesta;
    }

    /**
     * Funcion que sirve para reactivar los servicios
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 12-05-2020
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     *
     * @param array  $arrayData
     * @return Array $response [ status , mensaje , servicio ]
     */
    private function putReactivarServicio($arrayData)
    {
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceUtil = $this->get('schema.Util');

        ini_set('max_execution_time', 400000);

        $arrayResultado = array();
        $arrayRespuesta = array();
        $strServicios   = "";

        try
        {
            $serviceReconectarServicio = $this->get('tecnico.InfoReconectarServicio');
            $serviceReconectarWifi     = $this->get('tecnico.InfoElementoWifi');

            //Se verifica previamente que servicio es existente
            foreach($arrayData['data']['servicios'] as $intServicio)
            {
                $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicio);
                //Se obtienen los servicios a ser reactivados concatenados con "," para ser enviados al PACKAGE TECNICO
                if(is_object($objServicio))
                {
                    $strServicios = $strServicios . $intServicio . ",";
                }
                else
                {
                    $arrayResultadoServicio['status']   = 404;
                    $arrayResultadoServicio['mensaje']  = "Servicio a configurar no existe";
                    $arrayResultadoServicio['servicio'] = intval($intServicio);
                    $arrayRespuesta[] = $arrayResultadoServicio;
                }
            }

            if($strServicios != "")
            {
                $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
                $objTipoSolicitud   = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneBy(array(  "descripcionSolicitud" => "SOLICITUD REACTIVAR MASIVO",
                                                            "estado"               => "Activo"));

                $arrayServicios = $this->getArrayServiciosConfigurar($strServicios);

                foreach($arrayServicios['resultado'] as $arrayDataServicio)
                {
                    if($arrayDataServicio['estado'] != 'Activo')
                    {
                        if(is_object($objTipoSolicitud))
                        {
                            //obtengo el detalle de la solicitud
                            $objSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                            ->createQueryBuilder('p')
                                                            ->where('p.tipoSolicitudId =  :tipoSolicitudId')
                                                            ->andWhere("p.servicioId   =  :servicioId")
                                                            ->andWhere("p.estado       != :estado")
                                                            ->setParameter('tipoSolicitudId', $objTipoSolicitud->getId())
                                                            ->setParameter('servicioId',      $arrayDataServicio['idServicio'])
                                                            ->setParameter('estado',          'Finalizada')
                                                            ->orderBy('p.id', 'DESC')
                                                            ->setMaxResults(1)
                                                            ->getQuery()
                                                            ->getOneOrNullResult();
                        }

                        $arrayDataServicio['idAccion']    =  self::$strReactivarAccion;
                        $arrayDataServicio['idProducto']  =  $arrayDataServicio['productoId'];
                        $arrayDataServicio['usrCreacion'] =  $arrayData['data']['usrCreacion'];
                        $arrayDataServicio['ipCreacion']  =  $arrayData['data']['ipCreacion'];

                        if( $arrayDataServicio['descripcionProducto'] == 'L3MPLS' || $arrayDataServicio['descripcionProducto'] == 'L3MPLS SDWAN' ||
                            $arrayDataServicio['descripcionProducto'] == 'INTERNET' || $arrayDataServicio['descripcionProducto'] == 'INTMPLS' 
                            || $arrayDataServicio['descripcionProducto'] == 'INTERNET SDWAN')
                        {
                            $arrayResponse = $serviceReconectarServicio->reactivarServicioTN($arrayDataServicio);

                            $arrayResultadoServicio['status']   = $arrayResponse[0]['status']=="OK" ? 200 : 500;
                            $arrayResultadoServicio['mensaje']  = $arrayResponse[0]['status']=="OK" ?
                                                                  "Se Reactivó el Servicio" : $arrayResponse[0]['mensaje'];
                        }
                        elseif( $arrayDataServicio['descripcionProducto'] == 'INTERNET WIFI' )
                        {
                            $arrayResponse = $serviceReconectarWifi->reconectarServicio($arrayDataServicio);

                            $arrayResultadoServicio['status']   = $arrayResponse[0]['status']=="OK" ? 200 : 500;
                            $arrayResultadoServicio['mensaje']  = $arrayResponse[0]['status']=="OK" ?
                                                                  "Se Reactivó el Servicio" : $arrayResponse[0]['mensaje'];
                        }
                        elseif( $arrayDataServicio['descripcionProducto'] == 'OTROS' )
                        {
                            $arrayDataServicio['usrCreacion']   = $arrayData['data']['usrCreacion'];
                            $arrayDataServicio['clientIp']      = $arrayData['data']['ipCreacion'];
                            $arrayDataServicio['strCodEmpresa'] = $arrayDataServicio['idEmpresa'];
                            $arrayResponse = $serviceReconectarServicio->reactivarServiciosOtros($arrayDataServicio);

                            $arrayResultadoServicio['status']   = $arrayResponse['status']=="OK" ? 200 : 500;
                            $arrayResultadoServicio['mensaje']  = $arrayResponse['status']=="OK" ?
                                                                  "Se Reactivó el Servicio" : $arrayResponse['mensaje'];
                        }

                        $arrayResultadoServicio['servicio'] = $arrayDataServicio['idServicio'];
                        if( $arrayResultadoServicio['status'] == 200 && $arrayDataServicio['prefijoEmpresa'] == 'TN' &&
                            isset($objSolicitud) && is_object($objSolicitud) )
                        {
                            //obtengo el servicio
                            $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayDataServicio['idServicio']);
                            if( is_object($objInfoServicio) )
                            {
                                //actualizo el id de la solicitud con el ultimo registro
                                $objSolicitud->setEstado("Finalizada");
                                $objSolicitud->setObservacion('Se finalizó la solicitud de reactivación del servicio (ProcesoMasivo).');
                                $emComercial->persist($objSolicitud);
                                $emComercial->flush();

                                //registro historial a la solicitud
                                $objDetalleSolHistorial = new InfoDetalleSolHist();
                                $objDetalleSolHistorial->setDetalleSolicitudId($objSolicitud);
                                $objDetalleSolHistorial->setIpCreacion($arrayDataServicio['ipCreacion']);
                                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                                $objDetalleSolHistorial->setUsrCreacion($arrayDataServicio['usrCreacion']);
                                $objDetalleSolHistorial->setEstado($objSolicitud->getEstado());
                                $objDetalleSolHistorial->setObservacion('Se finalizó la solicitud de reactivación '.
                                                                        'del servicio (ProcesoMasivo).');
                                $emComercial->persist($objDetalleSolHistorial);
                                $emComercial->flush();

                                //registro servicio historial
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objInfoServicio);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setUsrCreacion($arrayDataServicio['usrCreacion']);
                                $objServicioHistorial->setIpCreacion($arrayDataServicio['ipCreacion']);
                                $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                                $objServicioHistorial->setObservacion('Se finalizó la solicitud de reactivación del servicio (ProcesoMasivo).');
                                $emComercial->persist($objServicioHistorial);
                                $emComercial->flush();
                            }
                        }
                        elseif( $arrayDataServicio['prefijoEmpresa'] == 'TN' && $arrayResultadoServicio['status'] == 500 )
                        {
                            try
                            {
                                //tipo de region del servicio
                                $strRegionServivio = '';
                                //arreglo de correos para enviar
                                $arrayToMail     = array();
                                //obtengo la empresa
                                $objEmpresa      = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo('TN');
                                //obtengo el servicio
                                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayDataServicio['idServicio']);
                                if( is_object($objInfoServicio) )
                                {
                                    //obtengo el punto del servicio
                                    $objInfoPunto = $objInfoServicio->getPuntoId();

                                    //registro servicio historial
                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objInfoServicio);
                                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorial->setUsrCreacion($arrayDataServicio['usrCreacion']);
                                    $objServicioHistorial->setIpCreacion($arrayDataServicio['ipCreacion']);
                                    $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                                    $objServicioHistorial->setObservacion($arrayResultadoServicio['mensaje']);
                                    $emComercial->persist($objServicioHistorial);
                                    $emComercial->flush();

                                    //obtengo el tipo de distrito del punto
                                    if(is_object($objInfoPunto))
                                    {
                                        //obtengo el sector
                                        $objSector = $objInfoPunto->getSectorId();
                                        if(is_object($objSector))
                                        {
                                            //obtengo la parroquia
                                            $objParroquia = $objSector->getParroquiaId();
                                            if(is_object($objParroquia))
                                            {
                                                //obtengo el canton
                                                $objCanton = $objParroquia->getCantonId();
                                                if(is_object($objCanton))
                                                {
                                                    $strRegionServivio = $objCanton->getRegion();
                                                }
                                            }
                                        }
                                    }

                                    //obtengo el mail del asistente o vendedor
                                    $strMailAsistente = '';
                                    $objInfoPersona   = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->findOneByLogin($objInfoServicio->getUsrCreacion());
                                    if(is_object($objInfoPersona) )
                                    {
                                        $arrayParametroMail                   = array();
                                        $arrayParametroMail['strLogin']       = $objInfoPersona->getLogin();
                                        $arrayParametroMail['intIdEmp']       = $objEmpresa->getId();
                                        $arrayParametroMail['objUtilService'] = $serviceUtil;
                                        $strMailAsistente = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->getMailNaf($arrayParametroMail);
                                    }
                                    if( is_object($objInfoPunto) && empty($strMailAsistente) )
                                    {
                                        $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->findOneByLogin($objInfoPunto->getUsrVendedor());
                                        if(is_object($objInfoPersona))
                                        {
                                            $arrayParametroMail                   = array();
                                            $arrayParametroMail['strLogin']       = $objInfoPersona->getLogin();
                                            $arrayParametroMail['intIdEmp']       = $objEmpresa->getId();
                                            $arrayParametroMail['objUtilService'] = $serviceUtil;
                                            $strMailAsistente = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                            ->getMailNaf($arrayParametroMail);
                                        }
                                    }
                                    if( !empty($strMailAsistente) )
                                    {
                                        $arrayToMail[] = $strMailAsistente;
                                    }
                                }

                                //obtengo el mail del usuario
                                if( isset($objSolicitud) && is_object($objSolicitud) )
                                {
                                    $objProcesoMasivoDet = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                ->findOneBySolicitudId($objSolicitud->getId());
                                    if(is_object($objProcesoMasivoDet))
                                    {
                                        $objInfoPersona   = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                            ->findOneByLogin($objProcesoMasivoDet->getUsrCreacion());
                                        if(is_object($objInfoPersona) )
                                        {
                                            $arrayParametroMail                   = array();
                                            $arrayParametroMail['strLogin']       = $objInfoPersona->getLogin();
                                            $arrayParametroMail['intIdEmp']       = $objEmpresa->getId();
                                            $arrayParametroMail['objUtilService'] = $serviceUtil;
                                            $strMailUsuario = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                            ->getMailNaf($arrayParametroMail);
                                            if( !empty($strMailUsuario) )
                                            {
                                                $arrayToMail[] = $strMailUsuario;
                                            }
                                        }
                                    }
                                }

                                //enviar notificación del error por correo electrónico
                                $objMailer      = $this->get('schema.Mailer');
                                $strTwigMail    = 'tecnicoBundle:InfoServicio:mailerErrorReactivarClienteMasivo.html.twig';
                                $strAsuntoMail  = "Notificación Error: Reactivación Servicio Masivo TN";
                                $strFromMail    = "notificaciones_telcos@telconet.ec";
                                $arrayDatosMail = array(
                                    'strLogin'     => $arrayDataServicio['login'],
                                    'strLoginAux'  => $arrayDataServicio['loginAux'],
                                    'strMensaje'   => $arrayResultadoServicio['mensaje'],
                                );

                                //obtengo los correos para el envío de la notificación
                                $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                            array(  'nombreParametro'   => 'CORREOS_RESPUESTA_CORTE_REACTIVAR_SERVICIO_MASIVO',
                                                                    'estado'            => 'Activo'));
                                if( !is_object($objAdmiParametroCab) )
                                {
                                    throw new \Exception('No se encontraron los correos para las notificaciones '.
                                                         'del reactivar masivo de los servicios en el telcos.');
                                }

                                //verifico que no este vacia la region
                                if(!empty($strRegionServivio))
                                {
                                    $arrayParametrosCorreos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                            array(  "parametroId"   => $objAdmiParametroCab->getId(),
                                                                                    "valor1"        => "REACTIVAR_MASIVO",
                                                                                    "valor3"        => $strRegionServivio,
                                                                                    "estado"        => "Activo"));
                                    foreach($arrayParametrosCorreos as $objParametroCorreo)
                                    {
                                        $arrayToMail[] = $objParametroCorreo->getValor2();
                                    }
                                }

                                //obtengo los correos principales
                                $arrayParametrosCorAll  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                        array(  "parametroId"   => $objAdmiParametroCab->getId(),
                                                                                "valor1"        => "REACTIVAR_MASIVO",
                                                                                "valor3"        => 'PRINCIPAL',
                                                                                "estado"        => "Activo"));
                                foreach($arrayParametrosCorAll as $objParametroCorreo)
                                {
                                    $arrayToMail[] = $objParametroCorreo->getValor2();
                                }

                                $objMailer->sendTwig($strAsuntoMail,
                                                     $strFromMail,
                                                     $arrayToMail,
                                                     $strTwigMail,
                                                     $arrayDatosMail);
                            }
                            catch(\Exception $ex)
                            {
                                $serviceUtil->insertError('Telcos+',
                                                          'ProcesosMasivosWSController.putReactivarServicio',
                                                           $ex->getMessage(),
                                                           $arrayDataServicio['usrCreacion'],
                                                           $arrayDataServicio['ipCreacion']);
                            }
                        }
                    }
                    else
                    {
                        $arrayResultadoServicio['status']   = 200;
                        $arrayResultadoServicio['mensaje']  = "Se Reactivó el Servicio";
                        $arrayResultadoServicio['servicio'] = $arrayDataServicio['idServicio'];
                    }

                    $arrayRespuesta[] = $arrayResultadoServicio;
                }
            }

            $arrayResultado['servicios'] = $arrayRespuesta;
        }
        catch (\Exception $e)
        {
            $arrayResultado = $this->getArrayException($e);
        }

        return $arrayResultado;
    }

    /**
     * Función que sirve para realizar upgrade/downgrade de la capacidad de la interface y generar tareas interna
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 22-06-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 09-11-2020 - Se agrega nueva opción para el control bw masivo y se genera el reporte unificado
     *
     * @param Array  $arrayData
     * @return Array $arrayRespuesta [ status , mensaje, data ]
     */
    private function putControlBwInterface($arrayData)
    {
        $emComercial              = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura        = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral                = $this->getDoctrine()->getManager("telconet_general");
        $serviceNetworkingScripts = $this->get('tecnico.NetworkingScripts');
        $serviceSoporte           = $this->get('soporte.soporteservice');
        $serviceUtil              = $this->get('schema.Util');
        $objMailer                = $this->get('schema.Mailer');

        ini_set('max_execution_time', 400000);

        try
        {
            //obtengo la empresa
            $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo('TN');
            //obtengo la caracteristica del tipo de proceso a ejecutar
            $objCaractTipoProceso   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'TIPO_PROCESO',
                                      "estado"                    => "Activo"));
            //obtengo la caracteristica del historial elemento id
            $objCaractHistorial     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'HISTORIAL_ELEMENTO_ID',
                                      "estado"                    => "Activo"));
            //obtengo la caracteristica del nombre de la ciudad
            $objCaractNombreCiudad  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'NOMBRE_CIUDAD',
                                      "estado"                    => "Activo"));
            //obtengo la caracteristica de la interface elemento id
            $objCaractInterface     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'INTERFACE_ELEMENTO_ID',
                                      "estado"                    => "Activo"));
            //obtengo la caracteristica de la capacidad uno
            $objCaractCapacidadUno  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD1',
                                      "estado"                    => "Activo"));
            //obtengo la caracteristica de la capacidad dos
            $objCaractCapacidadDos  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD2',
                                      "estado"                    => "Activo"));
            //obtengo la caracteristica de la capacidad uno anterior
            $objCaractCapaciUnoAnt  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD1 ANTERIOR',
                                      "estado"                    => "Activo"));
            //obtengo la caracteristica de la capacidad dos anterior
            $objCaractCapaciDosAnt  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD2 ANTERIOR',
                                      "estado"                    => "Activo"));
            //obtengo la característica de ejecución
            $objAdmiCaractIdEjecucion = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                    ->findOneBy(array("descripcionCaracteristica" => 'ID_EJECUCION',
                                      "estado"                    => 'Activo'));
            //obtengo la característica fecha fin de ejecución
            $objAdmiCaractFechaFin  = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                    ->findOneBy(array("descripcionCaracteristica" => 'FECHA_FIN',
                                      "estado"                    => 'Activo'));
            //recorrer las solicitudes
            foreach($arrayData['data']['servicios'] as $intIdSolicitud)
            {
                try
                {
                    //para este proceso el id del servicio corresponde el id de la solicitud
                    //obtengo el detalle de la solicitud
                    $objDetalleSolicitud   = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intIdSolicitud);
                    if( !is_object($objDetalleSolicitud) )
                    {
                        throw new \Exception("No se encontró el detalle de la solicitud, por favor notificar a Sistemas.");
                    }
                    //obtengo el tipo de proceso
                    $objDetalleTipoProceso = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                      "caracteristicaId"   => $objCaractTipoProceso->getId()));
                    if( !is_object($objDetalleTipoProceso) )
                    {
                        throw new \Exception("No se encontró el tipo de proceso a ejecutar, por favor notificar a Sistemas.");
                    }
                    //obtengo el tipo de proceso
                    $strTipoProceso = $objDetalleTipoProceso->getValor();
                    //verifico el tipo de proceso
                    if( $strTipoProceso == 'GENERAR_TAREA_ELEMENTO' )
                    {
                        //obtengo el detalle del historial
                        $objDetalleHistorial    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractHistorial->getId()));
                        if( !is_object($objDetalleHistorial) )
                        {
                            throw new \Exception("No se encontró el historial del elemento, por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle del historial
                        $objDetalleNombreCiudad = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractNombreCiudad->getId()));
                        if( !is_object($objDetalleNombreCiudad) )
                        {
                            throw new \Exception("No se encontró el nombre de la ciudad para generar la tarea, por favor notificar a Sistemas.");
                        }
                        //obtengo el historial del elemento
                        $objInfoHistorialElemento = $emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                                                         ->find($objDetalleHistorial->getValor());
                        if( !is_object($objInfoHistorialElemento) )
                        {
                            throw new \Exception("No se encontró el historial del elemento, por favor notificar a Sistemas.");
                        }
                        //setear observación
                        $strObservacion = $objInfoHistorialElemento->getObservacion();
                        //setear ciudad
                        $strCiudad      = $objDetalleNombreCiudad->getValor();
                        //seteo la respuesta de la tarea
                        $strStatusTarea = '';
                        //generar la tarea interna del elemento
                        try
                        {
                            //creación de la tarea interna
                            $objAdmiParametroCabTarea = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array('nombreParametro' => 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE',
                                                          'estado'          => 'Activo'));
                            if(is_object($objAdmiParametroCabTarea))
                            {
                                $objParametrosTarea   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                        array(  "parametroId" => $objAdmiParametroCabTarea->getId(),
                                                                                "valor1"      => "ELEMENTO",
                                                                                "estado"      => "Activo"));
                                if(is_object($objParametrosTarea))
                                {
                                    $strNombreTarea         = $objParametrosTarea->getValor2();
                                    $strNombreDepartamento  = $objParametrosTarea->getValor3();
                                    $strUsrCreacion         = $objParametrosTarea->getValor4();
                                    $strEmpleado            = $objParametrosTarea->getValor5();
                                    //Se definen los parámetros necesarios para la creación de la tarea
                                    $arrayParametrosTarea   = array(
                                        'strIdEmpresa'          => $objEmpresa->getId(),
                                        'strPrefijoEmpresa'     => $objEmpresa->getPrefijo(),
                                        'strNombreTarea'        => $strNombreTarea,
                                        'strNombreDepartamento' => $strNombreDepartamento,
                                        'strObservacion'        => $strObservacion,
                                        'strEmpleado'           => $strEmpleado,
                                        'strCiudad'             => $strCiudad,
                                        'strUsrCreacion'        => $strUsrCreacion,
                                        'strIp'                 => $arrayData['data']['ipCreacion'],
                                        'strOrigen'             => 'WS',
                                        'strValidacionTags'     => 'NO'
                                    );
                                    $arrayRespTarea = $serviceSoporte->ingresarTareaInterna($arrayParametrosTarea);
                                    //seteo la respuesta de la tarea
                                    $strStatusTarea = $arrayRespTarea['status'];
                                }
                            }
                        }
                        catch(\Exception $exx)
                        {
                            $serviceUtil->insertError('Telcos+',
                                                      'ProcesosMasivosWSController.putControlBwInterface',
                                                      $exx->getMessage(),
                                                      $arrayData['data']['usrCreacion'],
                                                      $arrayData['data']['ipCreacion']);
                        }
                        //actualizo el estado de la ejecución
                        $objDetalleSolCaract = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(),
                                                                          "caracteristicaId"   => $objAdmiCaractIdEjecucion->getId()));
                        if( is_object($objDetalleSolCaract) )
                        {
                            //actualizo el estado de las características de la solicitud
                            if($strStatusTarea == 'OK')
                            {
                                $objDetalleSolCaract->setEstado("Finalizada");
                            }
                            else
                            {
                                $objDetalleSolCaract->setEstado("Fallo");
                            }
                            $emComercial->persist($objDetalleSolCaract);
                            $emComercial->flush();
                        }
                        //seteo la respuesta
                        $arrayResultado[] = array(
                            'status'   => 200,
                            'mensaje'  => "Se Ejecuto la Solicitud de Control BW Masivo",
                            'servicio' => $intIdSolicitud
                        );
                    }
                    else if( $strTipoProceso == 'GENERAR_TAREA_INTERFACE' )
                    {
                        //obtengo el detalle del historial
                        $objDetalleHistorial    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractHistorial->getId()));
                        if( !is_object($objDetalleHistorial) )
                        {
                            throw new \Exception("No se encontró el historial del elemento, por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle del historial
                        $objDetalleNombreCiudad = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractNombreCiudad->getId()));
                        if( !is_object($objDetalleNombreCiudad) )
                        {
                            throw new \Exception("No se encontró el nombre de la ciudad para generar la tarea, por favor notificar a Sistemas.");
                        }
                        //obtengo el historial del elemento
                        $objInfoHistorialElemento = $emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                                                         ->find($objDetalleHistorial->getValor());
                        if( !is_object($objInfoHistorialElemento) )
                        {
                            throw new \Exception("No se encontró el historial del elemento, por favor notificar a Sistemas.");
                        }
                        //setear observación
                        $strObservacion = $objInfoHistorialElemento->getObservacion();
                        //setear ciudad
                        $strCiudad      = $objDetalleNombreCiudad->getValor();
                        //seteo la respuesta de la tarea
                        $strStatusTarea = '';
                        //generar la tarea interna de la interface
                        try
                        {
                            //creación de la tarea interna
                            $objAdmiParametroCabTarea = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array('nombreParametro' => 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE',
                                                          'estado'          => 'Activo'));
                            if(is_object($objAdmiParametroCabTarea))
                            {
                                $objParametrosTarea   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                        array(  "parametroId" => $objAdmiParametroCabTarea->getId(),
                                                                                "valor1"      => "INTERFACE",
                                                                                "estado"      => "Activo"));
                                if(is_object($objParametrosTarea))
                                {
                                    $strNombreTarea         = $objParametrosTarea->getValor2();
                                    $strNombreDepartamento  = $objParametrosTarea->getValor3();
                                    $strUsrCreacion         = $objParametrosTarea->getValor4();
                                    $strEmpleado            = $objParametrosTarea->getValor5();
                                    //Se definen los parámetros necesarios para la creación de la tarea
                                    $arrayParametrosTarea = array(
                                        'strIdEmpresa'          => $objEmpresa->getId(),
                                        'strPrefijoEmpresa'     => $objEmpresa->getPrefijo(),
                                        'strNombreTarea'        => $strNombreTarea,
                                        'strNombreDepartamento' => $strNombreDepartamento,
                                        'strObservacion'        => $strObservacion,
                                        'strEmpleado'           => $strEmpleado,
                                        'strCiudad'             => $strCiudad,
                                        'strUsrCreacion'        => $strUsrCreacion,
                                        'strIp'                 => $arrayData['data']['ipCreacion'],
                                        'strOrigen'             => 'WS',
                                        'strValidacionTags'     => 'NO'
                                    );
                                    $arrayRespTarea = $serviceSoporte->ingresarTareaInterna($arrayParametrosTarea);
                                    //seteo la respuesta de la tarea
                                    $strStatusTarea = $arrayRespTarea['status'];
                                }
                            }
                        }
                        catch(\Exception $exx)
                        {
                            $serviceUtil->insertError('Telcos+',
                                                      'ProcesosMasivosWSController.putControlBwInterface',
                                                      $exx->getMessage(),
                                                      $arrayData['data']['usrCreacion'],
                                                      $arrayData['data']['ipCreacion']);
                        }
                        //actualizo el estado de la ejecución
                        $objDetalleSolCaract = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(),
                                                                          "caracteristicaId"   => $objAdmiCaractIdEjecucion->getId()));
                        if( is_object($objDetalleSolCaract) )
                        {
                            //actualizo el estado de las características de la solicitud
                            if($strStatusTarea == 'OK')
                            {
                                $objDetalleSolCaract->setEstado("Finalizada");
                            }
                            else
                            {
                                $objDetalleSolCaract->setEstado("Fallo");
                            }
                            $emComercial->persist($objDetalleSolCaract);
                            $emComercial->flush();
                        }
                        //seteo la respuesta
                        $arrayResultado[] = array(
                            'status'   => 200,
                            'mensaje'  => "Se Ejecuto la Solicitud de Control BW Masivo",
                            'servicio' => $intIdSolicitud
                        );
                    }
                    else if( $strTipoProceso == 'UPGRADE_DOWNGRADE_BW' || $strTipoProceso == 'UPGRADE_DOWNGRADE_BW_MASIVO' )
                    {
                        //obtengo el detalle de la interface del elemento
                        $objDetalleCapacidadUno = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractCapacidadUno->getId()));
                        if( !is_object($objDetalleCapacidadUno) )
                        {
                            throw new \Exception("No se encontró la capacidad uno de la interface del elemento, por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle de la interface del elemento
                        $objDetalleCapacidadDos = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractCapacidadDos->getId()));
                        if( !is_object($objDetalleCapacidadDos) )
                        {
                            throw new \Exception("No se encontró la capacidad dos de la interface del elemento, por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle de la interface del elemento
                        $objDetalleCapUnoAnt    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractCapaciUnoAnt->getId()));
                        if( !is_object($objDetalleCapUnoAnt) )
                        {
                            throw new \Exception("No se encontró la capacidad uno anterior de la interface del elemento, ".
                                                 "por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle de la interface del elemento
                        $objDetalleCapDosAnt    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractCapaciDosAnt->getId()));
                        if( !is_object($objDetalleCapDosAnt) )
                        {
                            throw new \Exception("No se encontró la capacidad dos anterior de la interface del elemento, ".
                                                 "por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle de la interface del elemento
                        $objDetalleInterface    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractInterface->getId()));
                        if( !is_object($objDetalleInterface) )
                        {
                            throw new \Exception("No se encontró la interface del elemento, por favor notificar a Sistemas.");
                        }
                        //obtengo la interface del elemento
                        $objInfoInterface       = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                            ->find($objDetalleInterface->getValor());
                        if( !is_object($objInfoInterface) )
                        {
                            throw new \Exception("No se encontró la interface del elemento, por favor notificar a Sistemas.");
                        }
                        //obtengo el elemento
                        $objInfoElemento        = $objInfoInterface->getElementoId();

                        //arreglo para setear la capacidad de la interface
                        $arrayPeticionesSetBw               = array();
                        $arrayPeticionesSetBw['url']        = 'configBW';
                        $arrayPeticionesSetBw['accion']     = 'reconectar';
                        $arrayPeticionesSetBw['sw']         = $objInfoElemento->getNombreElemento();
                        $arrayPeticionesSetBw['pto']        = $objInfoInterface->getNombreInterfaceElemento();
                        $arrayPeticionesSetBw['anillo']     = '';
                        $arrayPeticionesSetBw['bw_up']      = $objDetalleCapacidadUno->getValor();
                        $arrayPeticionesSetBw['bw_down']    = $objDetalleCapacidadDos->getValor();
                        $arrayPeticionesSetBw['servicio']   = 'GENERAL';
                        $arrayPeticionesSetBw['login_aux']  = '';
                        $arrayPeticionesSetBw['user_name']  = $arrayData['data']['usrCreacion'];
                        $arrayPeticionesSetBw['user_ip']    = $arrayData['data']['ipCreacion'];
                        //Ejecucion del metodo via WS para realizar la configuracion del SW
                        $arrayRespuestaSetBw = $serviceNetworkingScripts->callNetworkingWebService($arrayPeticionesSetBw);
                        //verifico el resultado
                        if( $arrayRespuestaSetBw['status'] == 'OK' )
                        {
                            //agrego el resultado
                            $arrayResultado[] = array(
                                'status'   => 200,
                                'mensaje'  => "Se Ejecuto la Solicitud de Control BW Masivo",
                                'servicio' => $intIdSolicitud
                            );
                            //seteo la observación
                            $strObservacion = "Se actualizo la capacidad de la interface ".$objInfoInterface->getNombreInterfaceElemento().
                                              ":<br><b>Capacidad Nueva:</b><br><b>Up: </b>".$objDetalleCapacidadUno->getValor().
                                              "<br><b>Down: </b>".$objDetalleCapacidadDos->getValor().
                                              "<br><b>Capacidad Anterior:</b><br><b>Up: </b>".$objDetalleCapUnoAnt->getValor().
                                              "<br><b>Down: </b>".$objDetalleCapDosAnt->getValor();
                            //ingreso el historial del elemento
                            try
                            {
                                //abrir la conexión
                                $emInfraestructura->getConnection()->beginTransaction();
                                //ingreso el historial del elemento
                                $objInfoHistorialElemento = new InfoHistorialElemento();
                                $objInfoHistorialElemento->setElementoId($objInfoElemento);
                                $objInfoHistorialElemento->setObservacion($strObservacion);
                                $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                                $objInfoHistorialElemento->setUsrCreacion($arrayData['data']['usrCreacion']);
                                $objInfoHistorialElemento->setIpCreacion($arrayData['data']['ipCreacion']);
                                $objInfoHistorialElemento->setEstadoElemento($objInfoElemento->getEstado());
                                $emInfraestructura->persist($objInfoHistorialElemento);
                                $emInfraestructura->flush();
                                //guardo los datos de conexión
                                if( $emInfraestructura->getConnection()->isTransactionActive() )
                                {
                                    $emInfraestructura->getConnection()->commit();
                                    $emInfraestructura->getConnection()->close();
                                }
                            }
                            catch(\Exception $exx)
                            {
                                $serviceUtil->insertError('Telcos+',
                                                          'ProcesosMasivosWSController.putControlBwInterface',
                                                          $exx->getMessage(),
                                                          $arrayData['data']['usrCreacion'],
                                                          $arrayData['data']['ipCreacion']);
                                //cerrar la conexión
                                if($emInfraestructura->getConnection()->isTransactionActive())
                                {
                                    $emInfraestructura->getConnection()->rollback();
                                    $emInfraestructura->getConnection()->close();
                                }
                            }
                            //verifico el tipo proceso si es masivo
                            if($strTipoProceso == 'UPGRADE_DOWNGRADE_BW_MASIVO')
                            {
                                $objDetalleSolCaract = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                                ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(),
                                                                                  "caracteristicaId"   => $objAdmiCaractIdEjecucion->getId()));
                                if( is_object($objDetalleSolCaract) )
                                {
                                    //finalizo las características de la solicitud
                                    $objDetalleSolCaract->setEstado("Finalizada");
                                    $emComercial->persist($objDetalleSolCaract);
                                    $emComercial->flush();
                                }
                            }
                        }
                        else
                        {
                            //agrego el resultado
                            $arrayResultado[] = array(
                                'status'   => 500,
                                'mensaje'  => $arrayRespuestaSetBw['mensaje'],
                                'servicio' => $intIdSolicitud
                            );
                            //verifico el tipo proceso si es masivo
                            if($strTipoProceso == 'UPGRADE_DOWNGRADE_BW_MASIVO')
                            {
                                //obtengo la característica de solicitud de ejecución
                                $objDetalleSolCaract = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                                ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(),
                                                                                  "caracteristicaId"   => $objAdmiCaractIdEjecucion->getId()));
                                if( is_object($objDetalleSolCaract) )
                                {
                                    //finalizo las características de la solicitud
                                    $objDetalleSolCaract->setEstado("Fallo");
                                    $emComercial->persist($objDetalleSolCaract);
                                    $emComercial->flush();
                                }
                            }
                        }
                    }
                    else if( $strTipoProceso == 'REPORTE_CORREO_BW_MASIVO' )
                    {
                        //obtengo la característica de solicitud de ejecución
                        $objDetalleCaractEjecucion = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                        ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(),
                                                                          "caracteristicaId"   => $objAdmiCaractIdEjecucion->getId(),
                                                                          "estado"             => 'Pendiente'));
                        if( !is_object($objDetalleCaractEjecucion) )
                        {
                            throw new \Exception("No se encontró la característica id de ejecución en la solicitud, ".
                                                 "por favor notificar a Sistemas.");
                        }
                        //obtengo la solicitud de ejecución
                        $objSolicitudEjecucion  = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                                    ->find($objDetalleCaractEjecucion->getValor());
                        if( !is_object($objSolicitudEjecucion) )
                        {
                            throw new \Exception("No se encontró el detalle de la solicitud de ejecución, ".
                                                 "por favor notificar a Sistemas.");
                        }

                        //seteo el arreglo de datos de resultado
                        $arrayDatosEjecuciones  = array();
                        //obtengo todas las ejecuciones
                        $arrayEjecucionesCaract = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                            ->createQueryBuilder('p')
                                                            ->where("p.detalleSolicitudId  != :detalleSolicitudId")
                                                            ->andWhere("p.caracteristicaId  = :caracteristicaId")
                                                            ->andWhere("p.valor             = :valor")
                                                            ->setParameter('detalleSolicitudId', $objDetalleSolicitud->getId())
                                                            ->setParameter('caracteristicaId',   $objAdmiCaractIdEjecucion->getId())
                                                            ->setParameter('valor',              $objDetalleCaractEjecucion->getValor())
                                                            ->orderBy('p.detalleSolicitudId', 'ASC')
                                                            ->getQuery()
                                                            ->getResult();
                        foreach($arrayEjecucionesCaract as $objEjecucionCaract)
                        {
                            //seteo las variables
                            $strTipoEjecucion      = '';
                            $strObservacion        = '';
                            $strNombreElemento     = '';
                            $strNombreInterface    = '';
                            $strCapacidadUnoAnt    = '';
                            $strCapacidadDosAnt    = '';
                            $strCapacidadUno       = '';
                            $strCapacidadDos       = '';
                            //obtengo el detalle de la solicitud
                            $objEjeSolicitud       = $objEjecucionCaract->getDetalleSolicitudId();
                            //seteo el estado de la ejecución
                            $strEjeEstado          = $objEjecucionCaract->getEstado();
                            //obtengo el tipo de proceso
                            $objDetTipoProcesoSol  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                              "caracteristicaId"   => $objCaractTipoProceso->getId()));
                            if( !is_object($objDetTipoProcesoSol) )
                            {
                                throw new \Exception("No se encontró el tipo de proceso a ejecutar, por favor notificar a Sistemas.");
                            }
                            //obtengo el tipo de proceso
                            $strTipoProcesoSolicitud = $objDetTipoProcesoSol->getValor();
                            if( $strTipoProcesoSolicitud != 'REPORTE_CORREO_BW_MASIVO' )
                            {
                                if( $strTipoProcesoSolicitud == 'GENERAR_TAREA_ELEMENTO' || $strTipoProcesoSolicitud == 'GENERAR_TAREA_INTERFACE' )
                                {
                                    //seteo el tipo de ejecución
                                    $strTipoEjecucion       = 'TAREA INTERNA';
                                    //obtengo el detalle del historial
                                    $objDetalleHistorial    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                                  "caracteristicaId"   => $objCaractHistorial->getId()));
                                    if( !is_object($objDetalleHistorial) )
                                    {
                                        throw new \Exception("No se encontró el historial del elemento, por favor notificar a Sistemas.");
                                    }
                                    //obtengo el historial del elemento
                                    $objHistorialElemento   = $emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                                                                    ->find($objDetalleHistorial->getValor());
                                    if( !is_object($objHistorialElemento) )
                                    {
                                        throw new \Exception("No se encontró el historial del elemento, por favor notificar a Sistemas.");
                                    }
                                    $strObservacion         = $objHistorialElemento->getObservacion();
                                    //seteo el nombre del elemento
                                    $strNombreElemento      = $objHistorialElemento->getElementoId()->getNombreElemento();
            
                                }
                                if($strTipoProcesoSolicitud=='UPGRADE_DOWNGRADE_BW_MASIVO' || $strTipoProcesoSolicitud=='GENERAR_TAREA_INTERFACE')
                                {
                                    //obtengo el detalle de la interface del elemento
                                    $objDetalleInterface    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                                      "caracteristicaId"   => $objCaractInterface->getId()));
                                    if( !is_object($objDetalleInterface) )
                                    {
                                        throw new \Exception("No se encontró la interface del elemento, por favor notificar a Sistemas.");
                                    }
                                    //obtengo la interface del elemento
                                    $objInfoInterface       = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                                        ->find($objDetalleInterface->getValor());
                                    if( !is_object($objInfoInterface) )
                                    {
                                        throw new \Exception("No se encontró la interface del elemento, por favor notificar a Sistemas.");
                                    }
                                    //seteo el nombre de la interface
                                    $strNombreInterface     = $objInfoInterface->getNombreInterfaceElemento();
                                    //seteo el nombre del elemento
                                    $strNombreElemento      = $objInfoInterface->getElementoId()->getNombreElemento();
                                }
                                if( $strTipoProcesoSolicitud == 'UPGRADE_DOWNGRADE_BW_MASIVO' )
                                {
                                    //obtengo el detalle de la capacidad uno nueva
                                    $objEjeCaractCapUno     = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                                      "caracteristicaId"   => $objCaractCapacidadUno->getId()));
                                    if( !is_object($objEjeCaractCapUno) )
                                    {
                                        throw new \Exception("No se encontró la capacidad uno de la interface del elemento, ".
                                                             "por favor notificar a Sistemas.");
                                    }
                                    //obtengo el detalle de la capacidad dos nueva
                                    $objEjeCaractCapDos     = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                                      "caracteristicaId"   => $objCaractCapacidadDos->getId()));
                                    if( !is_object($objEjeCaractCapDos) )
                                    {
                                        throw new \Exception("No se encontró la capacidad dos de la interface del elemento, ".
                                                             "por favor notificar a Sistemas.");
                                    }
                                    //obtengo el detalle de la capacidad dos anterior
                                    $objEjeCaractCapUnoAnt  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                                      "caracteristicaId"   => $objCaractCapaciUnoAnt->getId()));
                                    if( !is_object($objEjeCaractCapUnoAnt) )
                                    {
                                        throw new \Exception("No se encontró la capacidad uno anterior de la interface del elemento, ".
                                                             "por favor notificar a Sistemas.");
                                    }
                                    //obtengo el detalle de la capacidad dos anterior
                                    $objEjeCaractCapDosAnt = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                                      "caracteristicaId"   => $objCaractCapaciDosAnt->getId()));
                                    if( !is_object($objEjeCaractCapDosAnt) )
                                    {
                                        throw new \Exception("No se encontró la capacidad dos anterior de la interface del elemento, ".
                                                             "por favor notificar a Sistemas.");
                                    }
                                    //verifico si finalizo la ejecucion
                                    if($strEjeEstado == 'Fallo')
                                    {
                                        $objEjeMasivoDet   = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                    ->findOneBySolicitudId($objEjeSolicitud->getId());
                                        if(is_object($objEjeMasivoDet))
                                        {
                                            $strObservacion = $objEjeMasivoDet->getObservacion();
                                        }
                                    }
                                    //seteo la variables
                                    $strCapacidadUnoAnt = $objEjeCaractCapUnoAnt->getValor();
                                    $strCapacidadDosAnt = $objEjeCaractCapDosAnt->getValor();
                                    $strCapacidadUno    = $objEjeCaractCapUno->getValor();
                                    $strCapacidadDos    = $objEjeCaractCapDos->getValor();
                                    //seteo el tipo de ejecución
                                    if( $strCapacidadUnoAnt > $strCapacidadUno )
                                    {
                                        $strTipoEjecucion = 'DOWNGRADE';
                                    }
                                    else
                                    {
                                        $strTipoEjecucion = 'UPGRADE';
                                    }
                                }
                                //seteo el arreglo
                                $arrayDatosEjecuciones[] = array(
                                    'strTipo'            => $strTipoEjecucion,
                                    'strNombreElemento'  => $strNombreElemento,
                                    'strNombreInterface' => $strNombreInterface,
                                    'strCapacidadUnoAnt' => $strCapacidadUnoAnt,
                                    'strCapacidadDosAnt' => $strCapacidadDosAnt,
                                    'strCapacidadUno'    => $strCapacidadUno,
                                    'strCapacidadDos'    => $strCapacidadDos,
                                    'strEstado'          => $strEjeEstado,
                                    'strObservacion'     => $strObservacion
                                );
                            }
                        }

                        //crear archivo temporal
                        $strContentsArchivo = null;
                        $objArchivoTemp     = fopen('php://temp/maxmemory:1048576', 'w');
                        if($objArchivoTemp !== false)
                        {
                            //seteo el titulo del archivo
                            $arrayHeaders = array(
                                'Tipo','Switch','Interface','Capacidad Up Anterior','Capacidad Down Anterior',
                                'Capacidad Up Nueva','Capacidad Down Nueva','Estado','Observación'
                            );
                            fputcsv($objArchivoTemp,$arrayHeaders);
                            //agregar los items
                            foreach($arrayDatosEjecuciones as $arrayItem)
                            {
                                fputcsv($objArchivoTemp, array_values($arrayItem));
                            }
                            rewind($objArchivoTemp);
                            $strContentsArchivo = stream_get_contents($objArchivoTemp);
                            fclose($objArchivoTemp);
                        }

                        //datos que se pasan a la vista
                        $arrayDatosMail = array(
                            'arrayDatosEjecuciones' => $arrayDatosEjecuciones,
                        );
                        //enviar notificación de reporte por correo electrónico
                        $arrayToMail    = array();
                        $strTwigMail    = 'tecnicoBundle:InfoServicio:mailerReporteControlBWMasivo.html.twig';
                        $strAsuntoMail  = "Control BW Automático: Reporte Unificado";
                        $strFromMail    = "notificaciones_telcos@telconet.ec";
                        //obtengo los correos para el envío de la notificación
                        $objAdmiParametroCabMail = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                            array('nombreParametro' => 'CORREOS_REPORTE_CONTROL_BW_INTERFACE',
                                                                  'estado'          => 'Activo'));
                        if( is_object($objAdmiParametroCabMail) )
                        {
                            $arrayParametrosDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                    array(  "parametroId"   => $objAdmiParametroCabMail->getId(),
                                                                            "estado"        => "Activo"));
                            foreach($arrayParametrosDet as $objParametro)
                            {
                                $arrayToMail[] = $objParametro->getValor1();
                            }
                        }
                        //verifico si hay correos para el envio de los correos
                        if( !empty($arrayToMail) && $strContentsArchivo !== null )
                        {
                            //enviar correos de notificaciones
                            $arrayParametersMail = array(
                                'strSubject'  => $strAsuntoMail,
                                'strFrom'     => $strFromMail,
                                'arrayTo'     => $arrayToMail,
                                'strTwig'     => $strTwigMail,
                                'arrayParams' => $arrayDatosMail,
                                'strContents' => $strContentsArchivo,
                                'strNameFile' => 'reporte.csv',
                                'strTypeFile' => 'application/csv'
                            );
                            $objMailer->sendTwigWithFileContents($arrayParametersMail);
                        }
                        else if(!empty($arrayToMail))
                        {
                            //enviar correos de notificaciones
                            $objMailer->sendTwig($strAsuntoMail,
                                                $strFromMail,
                                                $arrayToMail,
                                                $strTwigMail,
                                                $arrayDatosMail);
                        }
                        //finalizo la solicitud de ejecución
                        $objSolicitudEjecucion->setEstado("Finalizada");
                        $emComercial->persist($objSolicitudEjecucion);
                        $emComercial->flush();
                        //seteo la fecha fin de la ejecución
                        $objFechaFin            = new \DateTime('now');
                        //ingreso la fecha fin de la ejecución
                        $objSolCaractFechaFin   = new InfoDetalleSolCaract();
                        $objSolCaractFechaFin->setDetalleSolicitudId($objSolicitudEjecucion);
                        $objSolCaractFechaFin->setCaracteristicaId($objAdmiCaractFechaFin);
                        $objSolCaractFechaFin->setEstado("Activo");
                        $objSolCaractFechaFin->setUsrCreacion($arrayData['data']['usrCreacion']);
                        $objSolCaractFechaFin->setFeCreacion(new \DateTime('now'));
                        $objSolCaractFechaFin->setValor($objFechaFin->format('Y-m-d H:i:s'));
                        $emComercial->persist($objSolCaractFechaFin);
                        $emComercial->flush();
                        //seteo la respuesta
                        $arrayResultado[] = array(
                            'status'   => 200,
                            'mensaje'  => "Se Ejecuto la Solicitud de Control BW Masivo",
                            'servicio' => $intIdSolicitud
                        );
                    }

                    //finalizo la solicitud
                    $objDetalleSolicitud->setEstado("Finalizada");
                    $emComercial->persist($objDetalleSolicitud);
                    $emComercial->flush();

                    //agregar historial a la solicitud
                    $objDetalleSolHistorial = new InfoDetalleSolHist();
                    $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHistorial->setUsrCreacion($arrayData['data']['usrCreacion']);
                    $objDetalleSolHistorial->setIpCreacion($arrayData['data']['ipCreacion']);
                    $objDetalleSolHistorial->setEstado($objDetalleSolicitud->getEstado());
                    $objDetalleSolHistorial->setObservacion('Se finalizó la solicitud de Control de BW Interface (ProcesoMasivo).');
                    $emComercial->persist($objDetalleSolHistorial);
                    $emComercial->flush();

                    //obtengo las características de la solicitud
                    $arrayDetalleSolCaract = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                    ->findBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId(),
                                                                   "estado"             => 'Pendiente'));
                    foreach( $arrayDetalleSolCaract as $objDetalleSolCaract )
                    {
                        if( $objDetalleSolCaract->getCaracteristicaId()->getDescripcionCaracteristica() != 'ID_EJECUCION' )
                        {
                            //finalizo las características de la solicitud
                            $objDetalleSolCaract->setEstado("Finalizada");
                            $emComercial->persist($objDetalleSolCaract);
                            $emComercial->flush();
                        }
                    }
                    
                }
                catch (\Exception $ex)
                {
                    //seteo la respuesta
                    $arrayResultado[] = array(
                        'status'   => 500,
                        'mensaje'  => $ex->getMessage(),
                        'servicio' => $intIdSolicitud
                    );
                    $serviceUtil->insertError('Telcos+',
                                              'ProcesosMasivosWSController.putControlBwInterface',
                                               $ex->getMessage(),
                                               $arrayData['data']['usrCreacion'],
                                               $arrayData['data']['ipCreacion']);
                }
            }
            $arrayRespuesta['servicios'] = $arrayResultado;
        }
        catch (\Exception $e)
        {
            //seteo la variable de respuesta
            $arrayRespuesta = $this->getArrayException($e);
            $serviceUtil->insertError('Telcos+',
                                      'ProcesosMasivosWSController.putControlBwInterface',
                                       $e->getMessage(),
                                       $arrayData['data']['usrCreacion'],
                                       $arrayData['data']['ipCreacion']);
        }

        return $arrayRespuesta;
    }
}
