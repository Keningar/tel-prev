<?php

namespace telconet\planificacionBundle\Controller;

use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

class FactibilidadDataCenterController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * @Secure(roles="ROLE_392-1")
     * 
     * Documentación para el método 'indexHousingAction'.
     *
     * Metodo de direccionamiento principal de pantalla 
     * @return render direccinamiento a la pantalla solicitada
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 10-08-2017
     *   
     */
    public function indexHousingAction()
    {
        $arrayRolesPermitidos = array();
        
        if(true === $this->get('security.context')->isGranted('ROLE_392-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_392-1'; 
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_135-94'; 
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-95'))
        {
            $arrayRolesPermitidos[] = 'ROLE_135-95'; 
        }
        
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("392", "1");
        
        return $this->render('planificacionBundle:FactibilidadDataCenter:indexHousing.html.twig', array(
                            'item'            => $entityItemMenu,
                            'rolesPermitidos' => $arrayRolesPermitidos
        ));                
    }   

    /**
     * 
     * Metodo que devuelve las solicitudes de factibilidad de housing y hosting
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 14-05-2018 - Se envia parametro a la consulta que ayuda a verificar si se trata de una consulta de Factibilidad del PAC
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 10/08/2017
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 12-06-2020 - Se obtiene el parámetro correcto del login.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGridAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRespuesta            = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $intCodEmpresa         = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $arrayFechaDesdePlanif = explode('T',$objRequest->get('fechaDesdePlanif'));
        $arrayFechaHastaPlanif = explode('T',$objRequest->get('fechaHastaPlanif'));
        
        $emInfraestructura     = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emComercial           = $this->getDoctrine()->getManager("telconet");
        
        $strRegion             = '';
        $intIdOficina          = $objSession->get('idOficina');
        
        $objOficina = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);
        
        if(is_object($objOficina))
        {
            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            
            if(is_object($objCanton))
            {
                $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
            }
        }
        
        //se agrage modificacion de parametros para realizar consulta de registros de prefactibilidad
        $strLogin = $objRequest->get('login') ? $objRequest->get('login') : $objRequest->get('login2');
        $arrayParametros                             = array();
        $arrayParametros["em"]                       = $emInfraestructura;
        $arrayParametros["start"]                    = $objRequest->get('start');
        $arrayParametros["limit"]                    = $objRequest->get('limit');
        $arrayParametros["search_fechaDesdePlanif"]  = $arrayFechaDesdePlanif[0];
        $arrayParametros["search_fechaHastaPlanif"]  = $arrayFechaHastaPlanif[0];
        $arrayParametros["search_login2"]            = $strLogin;
        $arrayParametros["search_ciudad"]            = '';      
        $arrayParametros["codEmpresa"]               = $intCodEmpresa;
        $arrayParametros["ultimaMilla"]              = "";
        $arrayParametros["validaRechazado"]          = "NO";
        $arrayParametros["tipoSolicitud"]            = "";
        $arrayParametros["nombreTecnico"]            = $objRequest->get('nombreTecnico');
        $arrayParametros["grupo"]                    = 'DATACENTER';
        $arrayParametros["tipoFactibilidad"]         = $objRequest->get('tipoFactibilidad')?$objRequest->get('tipoFactibilidad'):'';
        $arrayParametros["region"]                   = $strRegion;
        
        /* @var $soporteService SoporteService */        
        $arrayParametros["serviceTecnico"]           = $this->get('tecnico.InfoServicioTecnico');
        
        //migracion clientes transtelco - se agrega parametro ultima milla
        $objJson = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                               ->generarJsonPreFactibilidad($arrayParametros);
        
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }        
        
    /**
     * 
     * Metodo que devuelve la informacion comercial basica del cliente e informacion de espacio segun distribucion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 01/09/2017
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 02-07-2020 - Se obtiene la descripción del recurso, en base a las nuevas estructuras.
     *
     * @param int $intIdServicio
     * @return type
     */
    public function factibilidadHousingAction($intIdServicio)
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');

        //Obtener informacion de cliente y de ubicaciones fisicas
        $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
        $objItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("392", "1");

        $objProducto = null;
        $objPunto    = null;
        $objPersona  = null;

        if(is_object($objServicio))
        {
            $objProducto = $objServicio->getProductoId();
            $objPunto    = $objServicio->getPuntoId();
            $objPersona  = $objPunto->getPersonaEmpresaRolId()->getPersonaId();
        }

        //Obtenemos la descripción del recurso en base a las nuevas estrucutras.
        $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                ->findOneByServicioId($objServicio->getId());
        $strDescripcionRecurso     = is_object($objInfoServicioRecursoCab)?$objInfoServicioRecursoCab->getDescripcionRecurso():'';

        //Datos Comerciales
        $arrayInformacion                   = array();
        $arrayInformacion['login']          = is_object($objPunto)?$objPunto->getLogin():'';
        $arrayInformacion['producto']       = is_object($objProducto)?$objProducto->getDescripcionProducto():'';
        $arrayInformacion['cliente']        = is_object($objPersona)?$objPersona->getInformacionPersona():'';
        $arrayInformacion['caracteristica'] = $strDescripcionRecurso;
        $arrayInformacion['cantidad']       = is_object($objServicio)?$objServicio->getCantidad():'';
        $arrayInformacion['servicio']       = is_object($objServicio)?$objServicio->getId():'';

        $strNombreCanton = '';
        $intIdOficina    = $objSession->get('idOficina');

        $objOficina = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

        if(is_object($objOficina))
        {
            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

            if(is_object($objCanton))
            {
                $strNombreCanton = $objCanton->getNombreCanton();
            }
        }

        $arrayFilasJaulas = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->get('FILAS RESERVADAS PARA JAULAS DC', 
                                            'TECNICO', 
                                            '',
                                            $strNombreCanton,//GUAYAQUIL/UIO 
                                            '',
                                            '',
                                            '',
                                            '', 
                                            '', 
                                            $objRequest->getSession()->get('idEmpresa'));

        $arrayParametros                    = array();
        $arrayParametros['strNombreCanton'] = $strNombreCanton;
        $arrayPosiciones = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getArrayInformacionFilaRacks($arrayParametros);

        $arrayInformacion['canton']     = $strNombreCanton;
        $arrayInformacion['posiciones'] = $arrayPosiciones;

        return $this->render('planificacionBundle:FactibilidadDataCenter:factibilidadHousing.html.twig',
                array('item'        => $objItemMenu,
                      'informacion' => $arrayInformacion,
                      'filasJaulas' => $arrayFilasJaulas));
    }

    /**
     * 
     * Metodo encargado para obtener las unidades de rack de un Rack enviado como parametro
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 04/09/2017
     * 
     *
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 2.0
     * @since 15/03/2019
     * 
     * @return \telconet\planificacionBundle\Controller\JsonResponse
     */
    public function ajaxGetInformacionRackAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $this->get('session');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $arrayJson         = [];
        
        $intIdRack     = $objRequest->get('idRack');
        
        $arrayRelacionRackUdRack = $emInfraestructura->getRepository("schemaBundle:InfoRelacionElemento")
                                                     ->findBy(array('estado'      => 'Activo',
                                                                    'elementoIdA' => $intIdRack
                                                                    ));
        
        $objRack = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                     ->find($intIdRack);
        // Se consulta AdmiParametroDet para conocer número de U inicial y número de U final del rack según el modelo
        if(is_object($objRack)) 
            {
                $strMarcaRack = $objRack->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                error_log($strMarcaRack.$objSession->get('idEmpresa')."  error");
                $arrayParametrosResultado = $emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                                    ->getOne('LIMITES DE US DE RACK DC',
                                                                             'TECNICO',
                                                                             '',
                                                                             'LIMITES DE US DE RACK DC',
                                                                             $strMarcaRack,'','','','',
                                                                             $objSession->get('idEmpresa'),
                                                                             null
                                                                             ); 
            } 

        $arrayUnidadesRack = array();
        
        //Recorrer las unidades de rack$objElementoUdRack
        foreach($arrayRelacionRackUdRack as $objRelacionRackUdRack)
        {
            //Se obtiene cada unidad de rack ligada a el rack enviado como parametro
            $objElementoUdRack = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                   ->find($objRelacionRackUdRack->getElementoIdB());

            if(is_object($objElementoUdRack))
            {
                //Buscar si la unidad de rack no se encuentra ocupada
                $objRelacionUdRack = $emInfraestructura->getRepository("schemaBundle:InfoRelacionElemento")
                                                       ->findOneBy(array('estado'      => 'Activo',
                                                                         'elementoIdA' => $objElementoUdRack->getId()
                                                                        ));
                $strEstado = 'Disponible';

                if(is_object($objRelacionUdRack))
                {
                    $strEstado = 'Ocupado';
                }                                    
            }

            $arrayUnidadesRack[]  =  array('idUdRack'     => $objElementoUdRack->getId(),
                                           'nombreUdRack' => $objElementoUdRack->getNombreElemento(),
                                           'estado'       => $strEstado
                                          );
        }
        $arrayJson['arrayUnidadesRack'] = $arrayUnidadesRack;
        $arrayJson['rangoInicial'] = $arrayParametrosResultado['valor2'];
        $arrayJson['rangoFinal'] = $arrayParametrosResultado['valor3'];
        $objResponse    = new JsonResponse();

        $objResponse->setData($arrayJson);
        
        return $objResponse;
    }
    
    /**
     * Metodo encargado de guardar la informacion de factibilidad fisica generada por el BOC segun requerimientos comerciales del cliente
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 06/09/2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1
     * @since 07/03/2018 - Se ajusta para la solucion muestre el detalle en forma Multi Solucion ( NxN )
     *                   - Se ajusta para que se genere tarea automatica al PAC ( Se coloca estado intermedio )
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 27/06/2018 - Se agrega parametro al llamado de la funcion crearTareaRetiroEquipoPorDemo
     * @since 1.1
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 02-07-2020 - Se agrega el llamado al micro-servicio encargado de crear la factibilidad Housing.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGuardarFactibilidadHousingAction()
    {
        $objResponse             = new JsonResponse();
        $strStatus               = 'OK';
        $strMensaje              = 'OK';
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $emComercial             = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura       = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte               = $this->getDoctrine()->getManager('telconet_soporte');
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $intIdServicio           = $objRequest->get('idServicio');
        $strData                 = $objRequest->get('datos');
        $strTipoAsignacion       = $objRequest->get('tipoAsignacion');
        $arrayJsonData           = json_decode($strData);
        $serviceEnvioPlantilla   = $this->get('soporte.EnvioPlantilla');
        $serviceGeneral          = $this->get('tecnico.InfoServicioTecnico');
        $serviceUtil             = $this->get('schema.Util');
        $serviceInfoSolucion     = $this->get('comercial.InfoSolucion');
        $arrayCrearFactibilidad  = array();
        $arrayDetalleRecurso     = array();
        $arrayInformacionCorreo  = array();

        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $objServicio =  $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
            $strEstado   = "PreFactibilidad-Pac";
            $strTipoSol  = "SOLICITUD FACTIBILIDAD";

            if (!is_object($objServicio))
            {
                throw new \Exception('No se encontró el servicio ('.$intIdServicio.')');
            }

            $objTipoSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                    ->findOneByDescripcionSolicitud($strTipoSol);

            if (!is_object($objTipoSolicitud))
            {
                throw new \Exception('No se encontró el tipo de solicitud ('.$strTipoSol.')');
            }

            $objDetalleSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                    ->findOneBy(array('servicioId'      => $intIdServicio,
                                      'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                      'estado'          => 'PreFactibilidad'));

            if (!is_object($objDetalleSolicitud))
            {
                throw new \Exception('No se encontró el detalle de la solicitud de factibilidad.');
            }

            //Obtenemos el id de elementos para la generación de la factibilidad.
            foreach ($arrayJsonData as $objJsonData)
            {
                if (empty($objJsonData->unidadesRack))
                {
                    $strUnidades           = 'Completo';
                    $arrayDetalleRecurso[] = array('elementoId' => $objJsonData->idRack, 'cantidad' => 1);
                }
                else
                {
                    $strUnidades = '';
                    foreach ($objJsonData->unidadesRack as $objJsonUdRack)
                    {
                        $arrayDetalleRecurso[] = array('elementoId' => $objJsonUdRack->idUdRack, 'cantidad' => 1);

                        //Obtener el nombre del Rack para generación de correo.
                        $objUdRack = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($objJsonUdRack->idUdRack);

                        $strUnidades .= is_object($objUdRack) ? '<br>UDR - '.$objUdRack->getNombreElemento() : '';
                    }
                }

                $arrayInformacionCorreo[] = array('fila'     => $objJsonData->nombreFila,
                                                  'rack'     => $objJsonData->nombreRack,
                                                  'cantidad' => $objJsonData->reservados,
                                                  'unidades' => $strUnidades);
            }

            //Se consume el Web-Service encargado de ingresar los datos en las nuevas estructuras de solución.
            $arrayCrearFactibilidad['habilitaCommit'] = true;
            $arrayCrearFactibilidad['usrCreacion']    = $objSession->get('user');
            $arrayCrearFactibilidad['ipCreacion']     = $objRequest->getClientIp();
            $arrayCrearFactibilidad['servicioId']     = $objServicio->getId();
            $arrayCrearFactibilidad['dataRecurso']    = array('estado' => 'Activo',
                                                              'detalle'=>  $arrayDetalleRecurso);
            $arrayCrearFactibilidad['dataSolicitud']  = array('estado'        =>  $strEstado,
                                                              'estadoSolAnt'  => 'PreFactibilidad',
                                                              'tipoSolicitud' =>  $strTipoSol);
            $arrayCrearFactibilidad['dataSolicitud']['historialSolicitud'] = array('observacion' => 'Factibilidad Generada');
            $arrayCrearFactibilidad['dataSolicitud']['servicioHistorial']  = array('observacion' => 'Factibilidad Generada');

            //Llamada al web-service de factbilidad.
            $arrayRespuestaWs = $serviceInfoSolucion->WsPostDc(array('strUser'      =>  $objSession->get('user'),
                                                                     'strIp'        =>  $objRequest->getClientIp(),
                                                                     'strOpcion'    => 'factibilidaddc',
                                                                     'strEndPoint'  => 'guardarFactibilidadEspacioFisico',
                                                                     'arrayRequest' =>  $arrayCrearFactibilidad));

            if (!$arrayRespuestaWs['status'])
            {
                throw new \Exception($arrayRespuestaWs['message']);
            }

            //Proceso adicional.
            $strLogin    = $objServicio->getPuntoId()->getLogin();
            $strSolucion = $serviceGeneral->getNombreGrupoSolucionServicios(array('objServicio'   => $objServicio,
                                                                                  'strCodEmpresa' => $strCodEmpresa));

            //Notificacion general de la informacion de factibilidad generada
            $strAsunto = 'Información de Factibilidad de Housing Generada por BOC para el cliente '.$strLogin;

            //Razon social
            $intIdPersonaRol = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId();
            $objPersonaRol   = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")->find($intIdPersonaRol);

            if(is_object($objPersonaRol))
            {
                $strRazonSocial = $objPersonaRol->getPersonaId()->getInformacionPersona();
            }

            $strEjecutante = '';

            //Obtener usuario ejecutante
            $objPersonaEjecutante = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($objSession->get('user'));

            if(is_object($objPersonaEjecutante))
            {
                $strEjecutante = $objPersonaEjecutante->getInformacionPersona();
            }

            //Determinar la ciudad relacionada de acuerdo a la region para procesos DC
            $strCiudad   = $serviceGeneral->getCiudadRelacionadaPorRegion($objServicio,$strCodEmpresa);
            $objCanton   = $emGeneral->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strCiudad);
            $intIdCanton = 0;

            if(is_object($objCanton))
            {
                $intIdCanton = $objCanton->getId();
            }

            $arrayNotificacion                     = array();
            $arrayNotificacion['login']            = $strLogin;
            $arrayNotificacion['razonSocial']      = $strRazonSocial;
            $arrayNotificacion['tipoHousing']      = $strTipoAsignacion;
            $arrayNotificacion['descripcion']      = $objServicio->getDescripcionPresentaFactura();
            $arrayNotificacion['arrayInformacion'] = $arrayInformacionCorreo;
            $arrayNotificacion['solucion']         = $strSolucion;
            $arrayNotificacion['ejecutante']       = $strEjecutante;
            $arrayNotificacion['fecha']            = new \DateTime('now');

            $serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto,
                                                          array(),
                                                         'INFO-FACT-HS-DC',
                                                          $arrayNotificacion,
                                                          $strCodEmpresa,
                                                          is_object($objCanton)?$objCanton->getId():0,
                                                          '',
                                                          null);

            //Se genera correo al PAC indicando que requieren verificar la Factibilidad del flujo electrico
            $strObservacion = 'Tarea automática: Realizar Revisión de Factibilidad Eléctrica para la factibilidad generada por el '
                            . 'Departamento del <b>BOC</b> para Servicio HOUSING contratado.'
                            . '<br><b>Login : </b> '.$strLogin
                            . '<br>'.$strSolucion;

            //Verificar alias para envio de notificacion al PAC de tarea automática
            $arrayInfoEnvio = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->get('HOUSING TAREAS POR DEPARTAMENTO', 
                          'SOPORTE', 
                          '',
                          'FACTIBILIDAD ENERGIA',
                          $strCiudad, 
                          '',
                          '',
                          '', 
                          '', 
                          $strCodEmpresa);

            $arrayParametrosEnvioPlantilla                      = array();
            $arrayParametrosEnvioPlantilla['strObservacion']    = $strObservacion;
            $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $objSession->get('user');
            $arrayParametrosEnvioPlantilla['strIpCreacion']     = $objRequest->getClientIp();
            $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $objDetalleSolicitud->getId();
            $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
            $arrayParametrosEnvioPlantilla['objPunto']          = $objServicio->getPuntoId();
            $arrayParametrosEnvioPlantilla['strCantonId']       = is_object($objCanton)?$objCanton->getId():0;
            $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $strCodEmpresa;
            $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $objSession->get('prefijoEmpresa');

            foreach($arrayInfoEnvio as $array)
            {
                $objTarea  = $emSoporte->getRepository("schemaBundle:AdmiTarea")->findOneByNombreTarea($array['valor3']);

                $arrayParametrosEnvioPlantilla['arrayCorreos']   = array($array['valor2']);
                $arrayParametrosEnvioPlantilla['intTarea']       = is_object($objTarea)?$objTarea->getId():'';

                //Se obtiene el departamento
                $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                             ->findOneByNombreDepartamento($array['valor4']);

                $arrayParametrosEnvioPlantilla['objDepartamento'] = $objDepartamento;

                $serviceCambiarPlanService = $this->get('tecnico.InfoCambiarPlan');
                $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";
                $serviceCambiarPlanService->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
            }

            //Historial de generación de tarea automatica para factibilidad del PAC.
            $strObservacion = 'Asignación Tarea automática al Departamento del <b>PAC</b> para verificación '.
                              'de Factibilidad eléctrica';
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setIpCreacion($objRequest->getClientIp());
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setUsrCreacion($objSession->get('user'));
            $objServicioHistorial->setEstado($strEstado);
            $objServicioHistorial->setObservacion($strObservacion);
            $emComercial->persist($objServicioHistorial);
            $emComercial->flush();

            $emComercial->commit();
            $emInfraestructura->commit();
        } 
        catch (\Exception $ex) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }

            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }

            $serviceUtil->insertError('Telcos+', 
                                      'ajaxGuardarFactibilidadHousingAction', 
                                       $ex->getMessage(), 
                                       $objSession->get('user'), 
                                       $objRequest->getClientIp());
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al Guardar información de Factibilidad, Notificar a Sistemas';

            $emComercial->close();
            $emInfraestructura->close();
        }
        
        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));
        
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_412-1")
     * 
     * Documentación para el método 'indexPacAction'.
     *
     * Metodo de direccionamiento principal de pantalla 
     * @return render direccinamiento a la pantalla solicitada
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 14-05-2018
     *   
     */
    public function indexPacAction()
    {
        $objRequest           = $this->get('request');
        $arrayRolesPermitidos = array();
        
        if(true === $this->get('security.context')->isGranted('ROLE_412-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_412-1'; 
        }
        
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("412", "1");
        
        //Obtener lista checklist factibilidad del pac
        $arrayInfoEnvio = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('CHECKLIST FACTIBILIDAD PAC', 
                                          'PLANIFICACION', 
                                          '',
                                          '',
                                          '', 
                                          '',
                                          '',
                                          '', 
                                          '', 
                                          $objRequest->getSession()->get('idEmpresa'));
        
        $arrayChecklist = array();
        
        foreach($arrayInfoEnvio as $array)
        {
            $arrayChecklist[] = array('valor' => $array['descripcion'], 'default' => $array['valor1']);
        }
        
        //Obtener los modelo de PDU requeridos
        $objTipoPDU = $emInfraestructura->getRepository("schemaBundle:AdmiTipoElemento")->findOneBy(array('nombreTipoElemento' => 'PDU',
                                                                                                          'estado'             => 'Activo')
                                                                                                     );
        
        $arrayModeloPAC = array();
        
        if(is_object($objTipoPDU))
        {
            $arrayModelosPDU = $emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")->findByTipoElementoId($objTipoPDU->getId());
            
            foreach($arrayModelosPDU as $objModelo)
            {
                $arrayModeloPAC[] = array('valor' => $objModelo->getNombreModeloElemento());
            }
        }
        
        return $this->render('planificacionBundle:FactibilidadDataCenter:indexPac.html.twig', array(
                            'item'            => $entityItemMenu,
                            'rolesPermitidos' => $arrayRolesPermitidos,
                            'arrayChecklist'  => $arrayChecklist,
                            'arrayModeloPdu'  => $arrayModeloPAC
        ));                
    }  
    
    /**
     * 
     * Metodo encargado de editar la fecha de Factibilidad para proceder a realizar el checklist
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 15-05-2018
     * 
     * @return Response
     */
    public function ajaxEditarFechaFactibilidadPACAction()
    {
        $objResponse        = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        
        $intIdSolicitud     = $objRequest->get('idSolicitud');
        $strObservacion     = $objRequest->get('observacion');
        $strFechaProg       = $objRequest->get('fecha');        
        $arrayFecha         = explode("-", $strFechaProg);
        $objFecha           = new \DateTime(date("Y/m/d G:i:s", strtotime($arrayFecha[2] . "-" . $arrayFecha[1] . "-" . $arrayFecha[0])));
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $emComercial        = $this->getDoctrine()->getManager();
        $serviceUtil        = $this->get('schema.Util');
        
        $emComercial->getConnection()->beginTransaction();
        $emSoporte->getConnection()->beginTransaction();

        try
        {
            $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            
            if($objDetalleSolicitud)
            {
                $strEstado   = 'FactibilidadEnProceso-Pac';
                
                //GUARDA INFO SERVICIO
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objDetalleSolicitud->getServicioId());
			
                if(is_object($objFecha))
                {
                    //GUARDA INFO DETALLE SOLICITUD
                    $objDetalleSolicitud->setObservacion($strObservacion);
                    $objDetalleSolicitud->setFeEjecucion($objFecha);
                    $objDetalleSolicitud->setEstado($strEstado);
                    $emComercial->persist($objDetalleSolicitud);
                    $emComercial->flush();
                    
                    if(is_object($objServicio))
                    {
                        $objServicio->setEstado($strEstado);
                        $emComercial->persist($objServicio);
                        $emComercial->flush();
                        
                        $objServicioHistorial = new InfoServicioHistorial();  
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setIpCreacion($objRequest->getClientIp());
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setUsrCreacion($objSession->get('user'));	
                        $objServicioHistorial->setEstado($strEstado);
                        $strFechaTramo = $arrayFecha[2]."/".$arrayFecha[1]."/".$arrayFecha[0];
                        $objServicioHistorial->setObservacion($strObservacion."<br>Fecha Factibilidad Eléctrica (PAC): <b>".$strFechaTramo."</b>"); 
                        $emComercial->persist($objServicioHistorial);
                        $emComercial->flush(); 
                    }
                    
                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                    $objDetalleSolHist->setEstado($strEstado);
                    $objDetalleSolHist->setObservacion($strObservacion);
                    $objDetalleSolHist->setFeIniPlan(new \DateTime('now'));
                    $objDetalleSolHist->setFeFinPlan($objFecha);
                    $emComercial->persist($objDetalleSolHist);
                    $emComercial->flush();

                    //Modificar fecha de la tarea automatica de Factibilidad generada
                    $arrayParametrosDetalle                   = array();
                    $arrayParametrosDetalle['intIdSolicitud'] = $objDetalleSolicitud->getId();
                    $arrayParametrosDetalle['strEstado']      = 'PreFactibilidad-Pac';
                    $arrayParametrosDetalle['strTarea']       = 'PAC';
                    $arrayParametrosDetalle['strTipo']        = 'SOLICITUD FACTIBILIDAD';
                    
                    $objDetalle = $emSoporte->getRepository("schemaBundle:InfoDetalle")
                                            ->getDetalleAsignadoPorSolicitudYTarea($arrayParametrosDetalle);

                    if(is_object($objDetalle))
                    {
                        $objDetalle->setFeSolicitada($objFecha);
                        $emSoporte->persist($objDetalle);
                        $emSoporte->flush();
                        
                        $objUltimoHisto = $emSoporte->getRepository("schemaBundle:InfoDetalleHistorial")
                                                    ->findOneBy(array('detalleId' => $objDetalle->getId()),
                                                                array('id'        => 'DESC'));
                        
                        if(is_object($objUltimoHisto))
                        {
                            $objInfoDetalleHistorial = new InfoDetalleHistorial();
                            $objInfoDetalleHistorial->setDetalleId($objDetalle);
                            $objInfoDetalleHistorial->setObservacion('Se Reprogramó Tarea para el día: '.$strFechaTramo.'<br>'
                                                                   . '<b>Observación:</b> '.$strObservacion);
                            $objInfoDetalleHistorial->setUsrCreacion($objSession->get('user'));
                            $objInfoDetalleHistorial->setAsignadoId($objUltimoHisto->getAsignadoId());
                            $objInfoDetalleHistorial->setAccion('Reprogramada');
                            $objInfoDetalleHistorial->setDepartamentoOrigenId($objUltimoHisto->getDepartamentoOrigenId());
                            $objInfoDetalleHistorial->setMotivo($objUltimoHisto->getMotivo());                        
                            $objInfoDetalleHistorial->setEstado('Reprogramada');                       
                            $objInfoDetalleHistorial->setFeCreacion(new \DateTime('now'));
                            $objInfoDetalleHistorial->setIpCreacion($objRequest->getClientIp());                        
                            $objInfoDetalleHistorial->setPersonaEmpresaRolId($objUltimoHisto->getPersonaEmpresaRolId());
                            $objInfoDetalleHistorial->setDepartamentoDestinoId($objUltimoHisto->getDepartamentoDestinoId());
                            $emSoporte->persist($objInfoDetalleHistorial);
                            $emSoporte->flush();
                            
                            $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
                            $objInfoTareaSeguimiento->setDetalleId($objDetalle->getId());
                            $objInfoTareaSeguimiento->setObservacion('Tarea fue Reprogramada para el '.$strFechaTramo.'<br/>'
                                                                   . '<b>Observación:</b> '.$strObservacion);
                            $objInfoTareaSeguimiento->setUsrCreacion($objSession->get('user'));
                            $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                            $objInfoTareaSeguimiento->setEmpresaCod($objSession->get('idEmpresa'));
                            $objInfoTareaSeguimiento->setEstadoTarea("Reprogramada");
                            $objInfoTareaSeguimiento->setInterno("N");
                            $objInfoTareaSeguimiento->setDepartamentoId($objUltimoHisto->getDepartamentoDestinoId());
                            $objInfoTareaSeguimiento->setPersonaEmpresaRolId($objUltimoHisto->getPersonaEmpresaRolId());
                            $emSoporte->persist($objInfoTareaSeguimiento);
                            $emSoporte->flush();
                        }
                        
                        $objResponse->setContent("OK");
                        
                        $emComercial->commit();
                        $emSoporte->commit();
                    }
                    else
                    {
                        $objResponse->setContent("No existe Tarea automática generada para asignar nueva Fecha, notificar a Sistemas");
                    }
                }
                else
                {
                    $objResponse->setContent("Ingrese la fecha de factibilidad eléctrica.");
                }
            }
            else
            {
                $objResponse->setContent("No existe Solicitud a procesar");
            }
        }
        catch(\Exception $ex)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            if ($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->rollback();
            }
            
            $emComercial->close();
            $emSoporte->close();
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxEditarFechaFactibilidadPACAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            
            $objResponse->setContent('Error al asignar Fecha de Factibilidad Eléctrica, notificar a Sistemas');
        }

        return $objResponse;
    }

    /**
     * Metodo encargado de guardar la confirmación de Factibilidad generada por el departamento del PAC.
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 15-05-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 06-07-2020 - Se agrega el llamado al micro-servicio encargado de crear la factibilidad de energia (Pac).
     *
     * @return JsonResponse
     */
    public function ajaxGuardarFactibilidadPACAction()
    {
        $objResponse         =  new JsonResponse();
        $strStatus           = 'OK';
        $strMensaje          = 'Factibilidad generada correctamente';
        $objRequest          =  $this->get('request');
        $objSession          =  $objRequest->getSession();
        $emComercial         =  $this->getDoctrine()->getManager('telconet');
        $strCodEmpresa       =  $objSession->get('idEmpresa');
        $intIdServicio       =  $objRequest->get('idServicio');
        $intIdSolicitud      =  $objRequest->get('idSolicitud');
        $strData             =  $objRequest->get('data');
        $arrayJsonData       =  json_decode($strData);
        $serviceUtil         =  $this->get('schema.Util');
        $serviceInfoSolucion =  $this->get('comercial.InfoSolucion');
        $arrayCorreo         =  array();

        try
        {
            $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);

            if (!is_object($objServicio))
            {
                throw new \Exception('No se encontró el servicio del cliente.');
            }

            //Se guarda la información de verificación de Factibilidad generada
            $strCaracteristicasPac = '';
            $strObservacion        = 'Se confirmó de manera exitosa la Factibilidad eléctrica por medio del PAC.'
                                    .'<br><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;<b>CheckList de verificación:</b>';

            foreach ($arrayJsonData as $array)
            {
                $strObservacion       .= '<br><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;'.$array->valor;
                $strCaracteristicasPac = $array->valor . "|" . $strCaracteristicasPac;
            }

            //Obtenemos el detalle de la solicitud de factibilidad.
            $objDetalleSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intIdSolicitud);

            if (!is_object($objDetalleSolicitud))
            {
                throw new \Exception('No se encontró la solicitud de factibilidad.');
            }

            //Se consume el Web-Service encargado de ingresar los datos en las nuevas estructuras de solución.
            $arrayCrearFactibilidad['habilitaCommit'] =  true;
            $arrayCrearFactibilidad['usrCreacion']    =  $objSession->get('user');
            $arrayCrearFactibilidad['ipCreacion']     =  $objRequest->getClientIp();
            $arrayCrearFactibilidad['servicioId']     =  $objServicio->getId();
            $arrayCrearFactibilidad['idProducto']     =  $objServicio->getProductoId()->getId();
            $arrayCrearFactibilidad['caracteristica'] = 'FACTIBILIDAD_HOUSING_PAC';

            $arrayCrearFactibilidad['dataFactibilidadPac'] = array('estado' => 'Activo',
                                                                   'valor'  =>  $strCaracteristicasPac);

            $arrayCrearFactibilidad['dataSolicitud']                       = array('estado'             => 'Factible');
            $arrayCrearFactibilidad['dataSolicitud']['solicitud']          = array('idDetalleSolicitud' => $intIdSolicitud);
            $arrayCrearFactibilidad['dataSolicitud']['historialSolicitud'] = array('observacion'        => $strObservacion);
            $arrayCrearFactibilidad['dataSolicitud']['servicioHistorial']  = array('observacion'        => $strObservacion);

            //Llamada al web-service de factbilidad.
            $arrayRespuestaWs = $serviceInfoSolucion->WsPostDc(array ('strUser'      =>  $objSession->get('user'),
                                                                      'strIp'        =>  $objRequest->getClientIp(),
                                                                      'strOpcion'    => 'factibilidaddc',
                                                                      'strEndPoint'  => 'guardarFactibilidadPac',
                                                                      'arrayRequest' =>  $arrayCrearFactibilidad));

            if (!$arrayRespuestaWs['status'])
            {
                throw new \Exception($arrayRespuestaWs['message']);
            }

            //Generacion de notificacion de aprobacion de factibilidad del PAC
            $objVendedor       = $emComercial->getRepository("schemaBundle:InfoPersona")
                    ->findOneBy(array ('login' => $objServicio->getUsrVendedor()));

            $objFormaContacto  = $emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                    ->findOneBy(array ('descripcionFormaContacto' => 'Correo Electronico',
                                       'estado'                   => 'Activo'));

            $objAsistComercial = $emComercial->getRepository("schemaBundle:InfoPersona")
                    ->findOneBy(array ('login' => $objServicio->getUsrCreacion()));

            //Correo del vendedor
            if (is_object($objVendedor) && is_object($objFormaContacto))
            {
                $objInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                        ->findOneBy(array ('personaId'       => $objVendedor->getId(),
                                           'formaContactoId' => $objFormaContacto->getId(),
                                           'estado'          => "Activo"));

                //OBTENGO EL CONTACTO DE LA PERSONA QUE ASIGNADA A LA TAREA
                if ($objInfoPersonaFormaContacto)
                {
                    $arrayCorreo[] = $objInfoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                }
            }

            //Correo de asistente comercial
            if (is_object($objAsistComercial) && is_object($objFormaContacto))
            {
                $objInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                        ->findOneBy(array ('personaId'       => $objAsistComercial->getId(),
                                           'formaContactoId' => $objFormaContacto->getId(),
                                           'estado'          => "Activo"));

                //OBTENGO EL CONTACTO DE LA PERSONA QUE ASIGNADA A LA TAREA
                if ($objInfoPersonaFormaContacto)
                {
                    $arrayCorreo[] = $objInfoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                }
            }

            //Se genera correo de confirmación a área comercial para proceder con la confirmacion de la orden de trabajo
            $strAsunto ="Aprobación de Solicitud de Factibilidad de Instalacion #".$objDetalleSolicitud->getId().' pertenciente a orden '
                      . 'de Servicio con Solución HOUSING del login : '.$objServicio->getPuntoId()->getLogin();

            /*Envío de correo por medio de plantillas**/
            /* @var $envioPlantilla EnvioPlantilla */
            $arrayParametros = array('detalleSolicitud' => $objDetalleSolicitud,'usrAprueba'=>$objSession->get('user'));
            $envioPlantilla  = $this->get('soporte.EnvioPlantilla');
            $envioPlantilla->generarEnvioPlantilla($strAsunto, 
                                                   $arrayCorreo, 
                                                  'APROBAR_FACTIB', 
                                                   $arrayParametros,
                                                   $strCodEmpresa,
                                                   '',
                                                   '',
                                                   null, 
                                                   true,
                                                  'notificaciones_telcos@telconet.ec');
        }
        catch (\Exception $ex) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxGuardarFactibilidadPACAction', 
                                       $ex->getMessage(), 
                                       $objSession->get('user'), 
                                       $objRequest->getClientIp());

            $strStatus  = 'ERROR';
            $strMensaje = 'Error al Guardar información de Factibilidad del PAC, Notificar a Sistemas';
        }

        $objResponse->setData(array ('status' => $strStatus, 'mensaje' => $strMensaje));
        return $objResponse;
    }

    /**
     * 
     * Metodo encargado de obtener los motivo de rechazo para la factibilidad de pac
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 16-05-2018
     * 
     * @return Response
     */
    public function ajaxGetMotivosRechazoAction()
    {
        $objRespuesta      = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRequest        = $this->get('request');
        $strModulo         = $objRequest->get('modulo');
        $strAccion         = $objRequest->get('accion');
        $intRelacionSist   = 0;
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');
        
        $objModulo   = $emSeguridad->getRepository('schemaBundle:SistModulo')->findOneBy(array('nombreModulo' => $strModulo, 'estado' => 'Activo'));
        $objAccion   = $emSeguridad->getRepository('schemaBundle:SistAccion')->findOneBy(array('nombreAccion' => $strAccion, 'estado' => 'Activo'));
        
        if(is_object($objModulo) && is_object($objAccion))
        {
            $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                  ->findOneBy(array("moduloId" => $objModulo->getId(), "accionId" => $objAccion->getId()));
            
            if(is_object($objSeguRelacionSistema))
            {
                $intRelacionSist = $objSeguRelacionSistema->getId();
            }
        }
        
        $objJson = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->generarJson("","Activo","","", $intRelacionSist);
        
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    
    /**
     * 
     * Metodo encargado de rechazar la factibilidad electrica generada para el PAC
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 16-05-2018
     * 
     * @return JsonResponse
     */
    public function ajaxRechazarFactibilidadPACAction()
    {
        $objResponse       = new JsonResponse();
        $strStatus         = 'OK';
        $strMensaje        = 'OK';
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $intIdServicio     = $objRequest->get('idServicio');
        $intIdSolicitud    = $objRequest->get('idSolicitud');
        $intIdMotivo       = $objRequest->get('idMotivo');
        $strObservacion    = $objRequest->get('observacion');
        
        $serviceUtil       = $this->get('schema.Util');
        
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
            $objServicio         = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServicio);
            $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            
            if(is_object($objServicio))
            {
                $objServicio->setEstado('Factible');
                $emComercial->persist($objServicio);
                $emComercial->flush();

                $objServicioHistorial = new InfoServicioHistorial();  
                $objServicioHistorial->setServicioId($objServicio);	
                $objServicioHistorial->setMotivoId($intIdMotivo);
                $objServicioHistorial->setObservacion('Rechazo de Factibilidad del PAC, observación: '.$strObservacion);	
                $objServicioHistorial->setIpCreacion($objRequest->getClientIp());
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setUsrCreacion($objSession->get('user'));	
                $objServicioHistorial->setEstado('Rechazada'); 
                $emComercial->persist($objServicioHistorial);
                $emComercial->flush();  

                $objServicioHistorial = new InfoServicioHistorial();  
                $objServicioHistorial->setServicioId($objServicio);	            
                $objServicioHistorial->setObservacion('Solicitud pasa a estado Factible automáticamente dado que no es necesaria Factibilidad PAC');	
                $objServicioHistorial->setIpCreacion($objRequest->getClientIp());
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setUsrCreacion($objSession->get('user'));	
                $objServicioHistorial->setEstado('Factible'); 
                $emComercial->persist($objServicioHistorial);
                $emComercial->flush();
                
                if(is_object($objDetalleSolicitud))
                {
                    $objDetalleSolicitud->setMotivoId($intIdMotivo);
                    $objDetalleSolicitud->setObservacion('Solicitud pasa a estado Factible automáticamente dado que no es necesaria Factibilidad PAC');	
                    $objDetalleSolicitud->setEstado("Factible");
                    $objDetalleSolicitud->setUsrRechazo($objSession->get('user'));		
                    $objDetalleSolicitud->setFeRechazo(new \DateTime('now'));
                    $emComercial->persist($objDetalleSolicitud);
                    $emComercial->flush();               

                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolHist->setObservacion('Solicitud pasa a estado Factible automáticamente dado que no es necesaria Factibilidad PAC');
                    $objDetalleSolHist->setMotivoId($intIdMotivo);            
                    $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                    $objDetalleSolHist->setEstado('Factible');
                    $emComercial->persist($objDetalleSolHist);
                    $emComercial->flush();

                    $strMensaje = 'Se Rechazó la Factibilidad Eléctrica correctamente';

                    $emComercial->commit();
                }
                else
                {
                    $strStatus  = 'ERROR';
                    $strMensaje = 'No se encontró solicitud de Factibilidad, Notificar a Sistemas';
                }
            }
            else
            {
                $strStatus  = 'ERROR';
                $strMensaje = 'No se encontró información de Servicio, Notificar a Sistemas';
            }
        } 
        catch (\Exception $ex) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxGuardarFactibilidadPACAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al Guardar información de Factibilidad del PAC, Notificar a Sistemas';
                        
            $emComercial->close();
        }
        
        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));
        
        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de obtener la informacion de Espacio Reservado para los clientes HOUSING
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 18-09-2017
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 06-07-2020 - en llamada del proceso 'getArrayInformacionEspacioHousing', se añade el usuario y la ip del usuario.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetInformacionEspacioHousingAction()
    {
        $objResponse       = new JsonResponse();
        $objRequest        = $this->get('request');        
        $intServicioAlq    = $objRequest->get('idServicioAlquiler');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $serviceServicio   = $this->get('comercial.InfoServicio');
        $serviceGeneral    = $this->get('tecnico.InfoServicioTecnico');
        $arrayRespuesta    = array();

        $objServicioAlq = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicioAlq);

        if(is_object($objServicioAlq))
        {
            $strNombreTecnico = $objServicioAlq->getProductoId()->getNombreTecnico();
            
            if($strNombreTecnico == 'HOSTING')//HOSTING
            {
                $objProducto = $objServicioAlq->getProductoId();
                
                //Obtener recursos contratados y de factibilidad
                $objServProdCaractStorage    = $serviceGeneral->getServicioProductoCaracteristica($objServicioAlq,'STORAGE_VALUE',$objProducto);
                $objServProdCaractMemoria    = $serviceGeneral->getServicioProductoCaracteristica($objServicioAlq,'MEMORIA_VALUE',$objProducto);
                $objServProdCaractProcesador = $serviceGeneral->getServicioProductoCaracteristica($objServicioAlq,'PROCESADOR_VALUE',$objProducto);
                $objServProdCaractVCenter    = $serviceGeneral->getServicioProductoCaracteristica($objServicioAlq,'VCENTER',$objProducto);
                $objServProdCaractCluster    = $serviceGeneral->getServicioProductoCaracteristica($objServicioAlq,'CLUSTER',$objProducto);
                
                if(is_object($objServProdCaractStorage) && is_object($objServProdCaractMemoria) && is_object($objServProdCaractProcesador) &&
                   is_object($objServProdCaractVCenter) && is_object($objServProdCaractCluster))
                {
                    $objElementoVCenter = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                            ->find($objServProdCaractVCenter->getValor());
                    
                    $objElementoCluster = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                            ->find($objServProdCaractCluster->getValor());
                    
                    $arrayRespuesta[] = array('storage'    => $objServProdCaractStorage->getValor()." GB",
                                              'memoria'    => $objServProdCaractMemoria->getValor()." GB",
                                              'procesador' => $objServProdCaractProcesador->getValor()." Cores",
                                              'vcenter'    => is_object($objElementoVCenter)?$objElementoVCenter->getNombreElemento():'N/A',
                                              'cluster'    => is_object($objElementoCluster)?$objElementoCluster->getNombreElemento():'N/A',
                                             );
                }
                
            }
            else//HOUSING
            {
                $arrayParametros                   = array();            
                $arrayParametros['objServicioAlq'] = $objServicioAlq;
                $arrayParametros['strUser']        = $objRequest->getSession()->get('user');
                $arrayParametros['strIp']          = $objRequest->getClientIp();
                $arrayRespuesta = $serviceServicio->getArrayInformacionEspacioHousing($arrayParametros);
            }
        }
        
        $objResponse->setData(array('encontrados' => $arrayRespuesta, 'total' => count($arrayRespuesta)));
        
        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de obtener el resumen de servidores contratados para asignarle recursos de factibilidad segun la disponibilidad de storage
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 28-11-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 09-02-2018 - Se ajusta consulta para que obtenga la informacion de recursos en base al esquema multi-caracteristica
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 28-03-2019 - Se añade dos contadores para identificar la cantidad de storage y licencias.
     *                         - Se modifica la manera de obtener los pool de recursos por motivos que se estaban añadiendo recursos que
     *                           no son del servicio.
     * 
     * @author Karen Rodríguez V. <kyrodirguez@telconet.ec>
     * @version 1.3 24-07-2020 - Se mantiene la lódica de programación sin embargo se adapta el código a las nuevas estructuras
     *                          de soluciones.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetInformacionServidoresAlquilerAction()
    {
        $objResponse       = new JsonResponse();
        $objRequest        = $this->get('request');
        $intIdServicio     = $objRequest->get('idServicio');
        $serviceGeneral    = $this->get('tecnico.InfoServicioTecnico');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayElementoServer = array();
        $arrayStorage        = array();
        $arrayLicencias      = array();
        $intContadorLicen    = 0;
        $intContadorStrge    = 0;
        
        $objServicio        = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
        $objInfoSolucionDet = $emComercial->getRepository('schemaBundle:InfoSolucionDet')
                                          ->findOneBy(array('servicioId' => $objServicio->getId(),'estado'=>'Activo'));
        $objInfoSolucionCab = is_object($objInfoSolucionDet) ? $objInfoSolucionDet->getSolucionCabId() : null;
        
        if(is_object($objInfoSolucionCab))
        {
            $intNumerSolucion = $objInfoSolucionCab->getNumeroSolucion();
            
            //Se obtienen los discos contratados por el cliente del pool de recursos.
            $boolEsPoolRecursos = $serviceGeneral->isContieneCaracteristica($objServicio->getProductoId(),'ES_POOL_RECURSOS');
            if($boolEsPoolRecursos)
            {
                $arrayParametrosRecursos                   = array();
                $arrayParametrosRecursos['intIdServicio']  = $objServicio->getId();
                $arrayParametrosRecursos['strTipoRecurso'] = 'DISCO';

                $arrayResult = $emComercial->getRepository("schemaBundle:InfoServicio")
                        ->getArrayCaracteristicasPorTipoYServicio($arrayParametrosRecursos);

                foreach($arrayResult as $array)
                {
                    $intContadorStrge ++;
                    $arrayStorage[] = array('idRecurso'     => $array['idRecurso'],
                                            'nombreRecurso' => $intContadorStrge.' - '.$array['nombreRecurso'].' ('.$array['valor'].' GB )',
                                            'valor'         => $array['valor']);
                }
            }

            //Obtenemos los servicios contratados
            $arrayParametrosSolucion = array('intSecuencial' => $intNumerSolucion);
            $arrayServiciosGrupo     = $emComercial->getRepository("schemaBundle:InfoServicio")
                    ->getArrayServiciosPorGrupoSolucion($arrayParametrosSolucion);

            foreach($arrayServiciosGrupo as $objServiciosGrupo)
            {
                $boolEsAlquilerServidor = $serviceGeneral->isContieneCaracteristica($objServiciosGrupo->getProductoId(),'ES_ALQUILER_SERVIDORES');

                $boolEsLicenciamiento   = $serviceGeneral->isContieneCaracteristica($objServiciosGrupo->getProductoId(),'ES_LICENCIAMIENTO_SO');

                if($boolEsAlquilerServidor)
                {
                    $arrayParametrosRecursos                   = array();
                    $arrayParametrosRecursos['intIdServicio']  = $objServiciosGrupo->getId();
                    $arrayParametrosRecursos['strTipoRecurso'] = 'TIPO ALQUILER SERVIDOR';

                    $arrayServidores = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                   ->getArrayCaracteristicasPorTipoYServicio($arrayParametrosRecursos);
                    
                    if(!empty($arrayServidores))
                    {
                        foreach($arrayServidores as $array)
                        {
                            $objElemento = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                             ->find($array['elementoId']);
                            
                            if(is_object($objElemento))
                            {
                                $arrayElementoServer[] =  array('idElemento'     => $objElemento->getId(),
                                                                'idServicio'     => $objServiciosGrupo->getId(),
                                                                'idRecurso'      => $array['idRecurso'],
                                                                'nombreElemento' => $objElemento->getNombreElemento(),
                                                                'modelo'         => $array['nombreRecurso']
                                                               );
                            }
                        }
                    }
                }

                if($boolEsLicenciamiento)
                {
                    $arrayParametrosRecursos                   = array();
                    $arrayParametrosRecursos['intIdServicio']  = $objServiciosGrupo->getId();
                    $arrayParametrosRecursos['strTipoRecurso'] = 'TIPO LICENCIAMIENTO SERVICE';

                    $arrayResult = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                  ->getArrayCaracteristicasPorTipoYServicio($arrayParametrosRecursos);

                    $arrayLicencias   = null;
                    $intContadorLicen = 0;
                    foreach($arrayResult as $array)
                    {
                        $intContadorLicen ++;
                        $arrayLicencias[] = array(
                                                'idRecurso'     => $array['idRecurso'],
                                                'nombreRecurso' => $intContadorLicen.' - '.$array['nombreRecurso'],
                                                );
                    }
                }
            }
        }

        $arrayRespuesta['arrayServidores'] = $arrayElementoServer;
        $arrayRespuesta['arrayStorage']    = $arrayStorage;
        $arrayRespuesta['arrayLicencias']  = $arrayLicencias;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }
    
/**
     * 
     * Metodo encargado de obtener la cantidad de storage disponible y consumido en flujos de alquiler de servidores
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 08-03-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 29-03-2019 - Se modifica la respuesta del controlador, por motivos que no se estaban añadiendo a un array todo
     *                           los recursos y por tales motivos solo devolvía el último.
     *
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.2 23-07-2020 - Se modifica la programación de la función puesto que con las nuevas estructuras
     *                           de soluciones la información se obtiene directamente desde la base.
     *
     * @return JsonResponse
     */
    public function ajaxGetResumenAlquilerServidoresAction()
    {
        $objResponse       = new JsonResponse();
        $objRequest        = $this->get('request');
        $intIdServicio     = $objRequest->get('idServicio');//id del pool de recursos
        $arrayResult       = array();
        $arrayDatos        = array();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        
        $arrayParametrosRecursos                   = array();
        $arrayParametrosRecursos['intIdServicio']  = $intIdServicio;

        $arrayResult = $emComercial->getRepository("schemaBundle:InfoServicio")
                                   ->getArrayInformacionPoolAlquilerServidores($arrayParametrosRecursos);

        foreach ($arrayResult as $arrayRecursos)
        {
            $arrayDatos[] = $arrayRecursos;
        }
        $objResponse->setData($arrayDatos);
        return $objResponse;
    }

/**
     * 
     * Metodo encargado de guardar la factibilidad generada para escenarios de alquiler de servidores asignando datastore y storage a cada
     * servicio contratado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 28-11-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 14-02-2018 - Guardar factibilidad en base a servcio multi caracteristica
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.2 24-07-2020 - La lógica fue migrada a la base y adaptada a las nuevas estructuras
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGuardarFactibilidadAlquilerServidoresAction()
    {
        $objResponse       = new JsonResponse();
        $strStatus         = 'OK';
        $strMensaje        = 'OK';
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $intIdServicio     = $objRequest->get('idServicio');
        $strData           = $objRequest->get('data');
        $serviceUtil       = $this->get('schema.Util');
        $serviceInfoSolucion     = $this->get('comercial.InfoSolucion');
        
        $emComercial->getConnection()->beginTransaction();        
        
        try
        {
            $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
            
            $arrayJson   = json_decode($strData);
            
            if(is_object($objServicio))
            {                
                $objTipoSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                ->findOneByDescripcionSolicitud('SOLICITUD FACTIBILIDAD');

                if(is_object($objTipoSolicitud))
                {
                    $objDetalleSolicitud =  $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                        ->findOneBy(array( 'servicioId'      => $intIdServicio,
                                                                           'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                                                           'estado'          => 'PreFactibilidad')
                                                                   );
                    if(is_object($objDetalleSolicitud))
                    {                     
                        $arrayServicioHistorial                =  array();
                        $arrayServicioHistorial['observacion'] =  'Se asignó factibilidad';
                        
                        $arraySolicitud                        =  array();
                        $arraySolicitud['idDetalleSolicitud']  =  $objDetalleSolicitud->getId();
                        $arraySolicitud['observacion']         =  'Se asignó factibilidad';
                        
                        $arrayHistorialSolicitud               =  array();
                        $arrayHistorialSolicitud['observacion']=  'Se asignó factibilidad';
                        
                        $arrayDetalleSolicitud                       =  array();
                        $arrayDetalleSolicitud['estado']             =  'Asignada';
                        $arrayDetalleSolicitud['servicioHistorial']  =  $arrayServicioHistorial;
                        $arrayDetalleSolicitud['solicitud']          =  $arraySolicitud;
                        $arrayDetalleSolicitud['historialSolicitud'] =  $arrayHistorialSolicitud;
                        
                        $strObservacion = 'Se generó Factibilidad con las siguientes Descripciones:';
                        $arrayFactibilidad['habilitaCommit'] =  true;
                        $arrayFactibilidad['usrCreacion']    =  $objServicio->getUsrCreacion();
                        $arrayFactibilidad['ipCreacion']     =  $objRequest->getClientIp();
                        $arrayFactibilidad['estado']         =  'Activo';
                        $arrayFactibilidad['servicioId']     =  $objServicio->getId();
                        $arrayFactibilidad['dataSolicitud']  =  $arrayDetalleSolicitud;
                        
                        $arrayDetalleFactibilidad            =  array();
                        $intContador = 0;
                        foreach($arrayJson as $objJson)
                        {
                            //Array disco
                            $arrayDetalleFactibilidad[$intContador]['elementoId']           = $objJson->elementoId;
                            $arrayDetalleFactibilidad[$intContador]['descripcion']          = $objJson->datastore;
                            $arrayDetalleFactibilidad[$intContador]['servicioRecursoCabId'] = $objJson->storage;
                            $arrayDetalleFactibilidad[$intContador]['cantidad']             = $objJson->cantidadStorage;
                            $intContador = $intContador + 1;
                            //Array licenciamiento
                            $arrayDetalleFactibilidad[$intContador]['elementoId']           = $objJson->elementoId;
                            $arrayDetalleFactibilidad[$intContador]['servicioRecursoCabId'] = $objJson->licenciamiento;
                            $arrayDetalleFactibilidad[$intContador]['cantidad']             = 1;
                            $intContador = $intContador + 1;
                        }
                        $arrayFactibilidad['factibilidad']                   =  $arrayDetalleFactibilidad; 
                        //Llamada al web-service de factbilidad.
                        $arrayRespuestaWs = $serviceInfoSolucion->WsPostDc(array('strUser'      =>  $objSession->get('user'),
                                                                                 'strIp'        =>  $objRequest->getClientIp(),
                                                                                 'strOpcion'    => 'factibilidaddc',
                                                                                 'strEndPoint'  => 'crearFactibPoolServidor',
                                                                                 'arrayRequest' =>  $arrayFactibilidad));
                        if (!$arrayRespuestaWs['status'])
                        {
                            throw new \Exception($arrayRespuestaWs['message']);
                        }
                        $strMensaje = 'Se generó la Factibilidad Correctamente';
                    }
                }
            }
            else
            {
                $strMensaje = 'No existe Referencia del Servicio enviado';
            }
        } 
        catch (\Exception $ex) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxGuardarFactibilidadAlquilerServidoresAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al Guardar información de Factibilidad, Notificar a Sistemas';
                        
            $emComercial->close();
        }
        
        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));
        
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_393-1")
     * 
     * Documentación para el método 'indexHostingAction'.
     *
     * Metodo de direccionamiento principal de pantalla 
     * @return render direccinamiento a la pantalla solicitada
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 02-10-2017
     *   
     */
    public function indexHostingAction()
    {
        $arrayRolesPermitidos = array();
        
        if(true === $this->get('security.context')->isGranted('ROLE_393-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_393-1'; 
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_135-94'; 
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-95'))
        {
            $arrayRolesPermitidos[] = 'ROLE_135-95'; 
        }
        
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("393", "1");
        
        return $this->render('planificacionBundle:FactibilidadDataCenter:indexHosting.html.twig', array(
                            'item'            => $entityItemMenu,
                            'rolesPermitidos' => $arrayRolesPermitidos
        ));                
    }
    
    /**
     * 
     * Metodo encargado de obtener los datos necesarios para poder generar la Factibilidad del Pool de Recursos
     * VCenter y Cluster
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 09-11-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 09-02-2018 -  Se envia parametro para obtener la informacion de discos contratados para el uso de la Factibilidad
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetDatosFactibilidadHostingAction()
    {
        $objResponse       = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $intCodEmpresa     = $objSession->get('idEmpresa');
        $strTipoDato       = $objRequest->get('tipoDato');
        $intIdVcenter      = $objRequest->get('idVcenter');
        $strCiudad         = $objRequest->get('ciudad');        
        $intIdServicio     = $objRequest->get('idServicio');  
        $intIdMaquina      = $objRequest->get('maquinaVirtual');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $arrayRespuesta    = array();
        
        //Datos de Factibilidad para Pool recursos completos
        if($strTipoDato == 'DISCO' || $strTipoDato == 'PROCESADOR' || $strTipoDato == 'MEMORIA RAM' || $strTipoDato == 'DATASTORE_DISCO')
        {
            $arrayParametrosRecursos                   = array();
            $arrayParametrosRecursos['intIdServicio']  = $intIdServicio;
            $arrayParametrosRecursos['strTipoRecurso'] = $strTipoDato;
            $arrayParametrosRecursos['intIdMaquina']   = $intIdMaquina;
                        
            $arrayRespuesta = $emComercial->getRepository("schemaBundle:InfoServicio")
                                          ->getArrayCaracteristicasPorTipoYServicio($arrayParametrosRecursos);
        }
        else //Datos de Factibilidad para Virtualizadores
        {
            $objCanton = $emGeneral->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strCiudad);
            $strRegion = '';

            if(is_object($objCanton))
            {
                $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
            }

            $arrayParametros                       = array();
            $arrayParametros['strTipo']            = $strTipoDato;
            $arrayParametros['intIdElementoPadre'] = $intIdVcenter;
            $arrayParametros['intEmpresaCod']      = $intCodEmpresa;
            $arrayParametros['strRegion']          = $strRegion;

            $arrayRespuesta = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                ->getArrayElementosFactibilidadHosting($arrayParametros);
        }
        
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }

    /**
     *
     * Metodo encargado de guardar los datos de Factibilidad generados por el usuario
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 09-11-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 09-02-2018 - Se cambia forma de guardar informacion de Datastore. se asigna uno o n datastore por disco contratado
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.2 25-04-2019 - Se modifica método para que guarde factibilidiad por máquina virtual 
     *                            y se separa en otro método la factibilidad storage y la finalización de solicitud.
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.3 01-06-2020 - Se hace llamado a MS de factibilidad.
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGuardarFactibilidadHostingAction()
    {
        $objResponse       = new JsonResponse();
        $strStatus         = 'OK';
        $strMensaje        = 'OK';
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $intIdServicio     = $objRequest->get('idServicio');
        $intHyperView      = $objRequest->get('hyperview');
        $intVCenter        = $objRequest->get('vcenter');
        $intCluster        = $objRequest->get('cluster');
        $intMaquinaVirtual  = $objRequest->get('maquinaVirtual');
        $strFactibilidadRap = $objRequest->get('factibilidadRap');
        $strDatastore       = $objRequest->get('datastore');
        
        $serviceGeneral    = $this->get('tecnico.InfoServicioTecnico');
        $serviceUtil       = $this->get('schema.Util');
        
        $emComercial->getConnection()->beginTransaction();        
        
        try
        {
            $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
            
            if(is_object($objServicio) || (!empty($strFactibilidadRap) && $strFactibilidadRap=='S'))
            {                
                if((empty($strFactibilidadRap) || $strFactibilidadRap!='S'))
                {
                    $objTipoSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                        ->findOneByDescripcionSolicitud('SOLICITUD FACTIBILIDAD');
                }

                if(is_object($objTipoSolicitud) || (!empty($strFactibilidadRap) && $strFactibilidadRap=='S'))
                {
                    if((empty($strFactibilidadRap) || $strFactibilidadRap!='S'))
                    {
                        $objDetalleSolicitud =  $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                    ->findOneBy(array( 'servicioId'      => $intIdServicio,
                                                                       'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                                                       'estado'          => 'PreFactibilidad')
                                                               );
                    }
                                                                   
                    if(is_object($objDetalleSolicitud) || (!empty($strFactibilidadRap) && $strFactibilidadRap=='S'))
                    {    
                        
                        //Guardar detalle elemento por maquina virtual
                        $arrayDetalle                          =  array();
                        $arrayDetalle [0]['detalleNombre']     =  'HYPERVIEW';
                        $arrayDetalle [0]['detalleValor']      =  $intHyperView;
                        $arrayDetalle [1]['detalleNombre']     =  'VCENTER';
                        $arrayDetalle [1]['detalleValor']      =  $intVCenter;
                        $arrayDetalle [2]['detalleNombre']     =  'CLUSTER';
                        $arrayDetalle [2]['detalleValor']      =  $intCluster;
                        
                        //DATASTORE por disco
                        $arrayDs            = json_decode($strDatastore);
                        $arrayDatastore     = array();
                        $intContador        = 0;
                        foreach($arrayDs as $objJson)
                        {
                            $arrayDatastore [$intContador]['servicioRecursoCabId']   =  $objJson->idRecurso;
                            $arrayDatastore [$intContador]['descripcion']            =  $objJson->datastore;
                            
                            $intContador    = $intContador +1;
                        }

                        $arrayFactibilidad                      =  array();
                        $arrayFactibilidad ['idServicio']       =  $objServicio->getId();
                        $arrayFactibilidad ['usrCreacion']      =  $objSession->get('user');
                        $arrayFactibilidad ['estado']           =  'Activo';
                        $arrayFactibilidad ['ipCreacion']       =  $objRequest->getClientIp();
                        $arrayFactibilidad ['habilitaCommit']   =  true;
                        $arrayFactibilidad ['elementoId']       =  $intMaquinaVirtual;
                        $arrayFactibilidad ['factibilidad']     =  $arrayDetalle;
                        $arrayFactibilidad ['datastore']        =  $arrayDatastore;

                        $serviceSolucion = $this->get('comercial.InfoSolucion');
                        $arrayRespuesta  = $serviceSolucion->WsPostDc(array('strUser'      => $objSession->get('user'),
                                                                            'strIp'        => $objRequest->getClientIp(),
                                                                            'strOpcion'    => 'factibilidaddc',
                                                                            'strEndPoint'  => 'crearFactibilidadMV',
                                                                            'arrayRequest' => $arrayFactibilidad));

                        if (!$arrayRespuesta['status'])
                        {
                            throw new \Exception('Error : '.$arrayRespuesta['message']);
                        }
                        
                        $objElementoVCenter = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intVCenter);
                        $objElementoCluster = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intCluster);
                        $objElementoHyperV  = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intHyperView);
                        $objElementoMaquinaVirtual  = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intMaquinaVirtual);

                        $strObservacion = 'Se generó Factibilidad con las siguientes Descripciones:<br/>';
                        $strObservacion .= '<br/><b>HyperView:</b>'. (is_object($objElementoHyperV)?$objElementoHyperV->getNombreElemento():'N/A');
                        $strObservacion .= '<br/><b>VCenter:</b>'. (is_object($objElementoVCenter)?$objElementoVCenter->getNombreElemento():'N/A');
                        $strObservacion .= '<br/><b>Cluster:</b>'. (is_object($objElementoCluster)?$objElementoCluster->getNombreElemento():'N/A');
                        $strObservacion .= '<br/><b>Maquina Virtual:</b>'. (is_object($objElementoMaquinaVirtual)?$objElementoMaquinaVirtual->getNombreElemento():'N/A');
                        
                        $strDsObs = "";
                        
                        foreach($arrayDs as $objJson)
                        {
                            $strDsObs .= '<br>Disco : '.$objJson->valor.' (GB)&nbsp;<i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp; '
                                            . 'DS: '.$objJson->datastore;
                        }
                        
                        $strObservacion .= 'Se generó Factibilidad de Datastore con las siguientes Descripciones:<br/>';
                        $strObservacion .= '<br/><b>Datastore:</b>'. $strDsObs;
                        $strObservacion .= '<br/><hr>';
                                                                   
                        //Historial de Servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setIpCreacion($objRequest->getClientIp());
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setUsrCreacion($objSession->get('user'));
                        $objServicioHistorial->setEstado("Factible");
                        if(is_object($objDetalleSolicitud) || (!empty($strFactibilidadRap) && $strFactibilidadRap=='S'))
                        { 
                            $objServicioHistorial->setEstado($objServicio->getEstado());
                        }
                        $objServicioHistorial->setObservacion($strObservacion);
                        $emComercial->persist($objServicioHistorial);
                        $emComercial->flush();

                        if((empty($strFactibilidadRap) || $strFactibilidadRap!='S'))
                        {
                            $objDetalleSolHist = new InfoDetalleSolHist();
                            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                            $objDetalleSolHist->setEstado('Factible');
                            $objDetalleSolHist->setObservacion($strObservacion);
                            $emComercial->persist($objDetalleSolHist);
                            $emComercial->flush();
                        }
                        $emComercial->commit();                        
                        
                        $strMensaje    = 'Se generó la Factibilidad de Virtualizadores Correctamente';
                    }
                }
            }
            else
            {
                $strMensaje = 'No existe Referencia del Servicio enviado';
            }
        } 
        catch (\Exception $ex) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxGuardarFactibilidadHostingAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al Guardar información de Factibilidad, Notificar a Sistemas ajaxGuardarFactibilidadHostingAction';
            if (!$arrayRespuesta['status'])
            {
                $strMensaje = $strMensaje .' - '.$arrayRespuesta['message'];
            }
                      
            $emComercial->close();           
        }
        
        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));
        
        return $objResponse;
    }

    /**
     * Metodo encargado de finalizar solicitud hosting con máquinas virtuales
     *
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.0 25-04-2019
     *
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.1 07-04-2020 - Se actualiza transacción en base a las nuevas estructuras
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function finalizarFactibilidadHostingMVAction()
    {
        $objResponse       = new JsonResponse();
        $strStatus         = 'OK';
        $strMensaje        = 'OK';
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        
        $intIdServicio     = $objRequest->get('idServicio');        
        $serviceUtil       = $this->get('schema.Util');
        
        $emComercial->getConnection()->beginTransaction();        
        
        try
        {
            $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
            
            if(is_object($objServicio))
            {
                $objTipoSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                ->findOneByDescripcionSolicitud('SOLICITUD FACTIBILIDAD');

                if(is_object($objTipoSolicitud))
                {
                    $objDetalleSolicitud =  $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                        ->findOneBy(array( 'servicioId'      => $intIdServicio,
                                                                           'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                                                           'estado'          => 'PreFactibilidad')
                                                                   );
                    if(is_object($objDetalleSolicitud))
                    {
                        $strObservacion = 'Se finalizó solicitud de factibilidad <br/>';
                        $strObservacion .= '<br/><hr>';
                        
                        //Creación de arrays para enviarlos al MS
                        
                        $arrayServicioHistorial                  =  array();
                        $arrayServicioHistorial ['servicioId']   =  $objServicio->getId();
                        $arrayServicioHistorial ['observacion']  =  $strObservacion;
                        
                        $arrayDetalleSolicitud                         =  array();
                        $arrayDetalleSolicitud ['idDetalleSolicitud']  =  $objDetalleSolicitud->getId();
                        $arrayDetalleSolicitud ['observacion']         =  $strObservacion;
                        
                        $arrayHistorialSolicitud                 =  array();
                        $arrayHistorialSolicitud ['observacion'] =  $strObservacion;
                        
                        $arraySolicitud                    =  array();
                        $arraySolicitud ['usrCreacion']    =  $objSession->get('user');
                        $arraySolicitud ['ipCreacion']     =  $objRequest->getClientIp();
                        $arraySolicitud ['estado']         =  'Factible';
                        $arraySolicitud ['habilitaCommit'] =  true;
                        $arraySolicitud ['servicioHistorial']       =  $arrayServicioHistorial;
                        $arraySolicitud ['solicitud']               =  $arrayDetalleSolicitud;
                        $arraySolicitud ['historialSolicitud']      =  $arrayHistorialSolicitud;
                        $serviceSolucion  = $this->get('comercial.InfoSolucion');
                        $arrayRespuesta   = $serviceSolucion->WsPostDc(array( 'strUser'      => $objSession->get('user'),
                                                                              'strIp'        => $objRequest->getClientIp(),
                                                                              'strOpcion'    => 'solicitud',
                                                                              'strEndPoint'  => 'actualizarSolcitudSol',
                                                                              'arrayRequest' => $arraySolicitud));

                        if (!$arrayRespuesta['status'])
                        {
                            throw new \Exception('Error : '.$arrayRespuesta['message']);
                        }
                        
                        $strMensaje    = 'Se finalizó solicitud de factibilidad correctamente';
                        
                        $strAsunto     =  "Aprobación de Solicitud de Factibilidad de Instalación #".$objDetalleSolicitud->getId();
                        $arrayCorreos  =  array();
                        
                        if($objServicio->getUsrVendedor())
                        {
                            $arrayFormasContacto =  $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->getContactosByLoginPersonaAndFormaContacto( $objServicio->getUsrVendedor(),
                                                                                                              'Correo Electronico'
                                                                                                             );
                            foreach($arrayFormasContacto as $array)
                            {
                                $arrayCorreos[] = $array['valor'];
                            }
                        }

                        /*Envío de correo por medio de plantillas**/
                        /* @var $envioPlantilla EnvioPlantilla */
                        $arrayParametros        = array('detalleSolicitud' => $objDetalleSolicitud,'usrAprueba'=>$objSession->get('user'));
                        $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
                        $serviceEnvioPlantilla->generarEnvioPlantilla(  $strAsunto, 
                                                                        $arrayCorreos, 
                                                                        'APROBAR_FACTIB', 
                                                                        $arrayParametros,
                                                                        $objSession->get('idEmpresa'),
                                                                        '',
                                                                        '',
                                                                        null, 
                                                                        true,
                                                                        'notificaciones_telcos@telconet.ec'
                                                                      );
                    }
                }
            }
            else
            {
                $strMensaje = 'No existe Referencia del Servicio enviado';
            }
        }
        catch (\Exception $ex) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxFinalizarSolicitudFactibilidadHostingMVAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al finalizar solicitud de factibilidad, Notificar a Sistemas';
                        
            $emComercial->close();           
        }
        
        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));
        
        return $objResponse;
    }
}
