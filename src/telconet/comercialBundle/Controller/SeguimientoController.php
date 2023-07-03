<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoSeguimientoServicio;
/**
 * InfoDetalleSolicitud controller.
 *
 */
class SeguimientoController extends Controller implements TokenAuthenticatedController
{
   
    /* 
     * Documentación para la función 'showAction'.
     *
     * Función de inicio de la ventana de seguimiento.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 26-02-2020
     * 
     * @return Response se retorna los servicios que se mostraran en la ventana de seguimiento.
     *
     */
    public function showAction ()
    {
        $emGeneral = $this->getDoctrine()->getManager();
		$objRequest=$this->get('request');
		//Debo listar todos los servicios del punto
		$objSession=$objRequest->getSession();
		$objCliente=$objSession->get('ptoCliente');
        $arrayEstadoSegui   = array();
        $arrayDatos = array();
        $arrayDatos["estados"]  =   'Todos';
        $arrayDatos["idPunto"]  =   $objCliente['id'];
        $arrayDatos['seguimiento'] = true;        
                $objParametrosCab =   $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                                ->findOneBy(array('nombreParametro'=>"SEGUIMIENTO_PRODUCTOS"));
                $arrayParametrosDet =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->findBy(array('parametroId'=>$objParametrosCab->getId()));
                
        foreach($arrayParametrosDet as $objProductosPermitidos)
        {
            if(!empty($objProductosPermitidos) && is_object($objProductosPermitidos))
            {
            array_push($arrayEstadoSegui,$objProductosPermitidos->getValor1());
            }
        }     
         if(!empty($arrayEstadoSegui))
        {
            $arrayDatos['productosPer'] = $arrayEstadoSegui;
        }
        
		$arrayListado = $emGeneral->getRepository('schemaBundle:InfoServicio')->findServiciosByPuntoAndSeguimiento($arrayDatos);

        $objServicios = $arrayListado['registros'];
        
        if (!$objServicios) 
		{
            throw $this->createNotFoundException('No se encuentran servicios para seguimiento en el Punto.');
        }
        return $this->render('comercialBundle:gestionseguimiento:index.html.twig', array(
                'ListServicios' => $objServicios
        ));
    }
    /* 
     * Documentación para la función 'getFiltroServiciosAction'.
     *
     * Función que filtra solo los servicios permitidos a realizar seguimiento.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 26-02-2020
     * 
     * @return Response se retorna los servicios que se mostraran en la ventana de seguimiento.
     *
     */
    public function getFiltroServiciosAction()
    {
        $emGeneral        = $this->getDoctrine()->getManager();
        $objRequest       = $this->getRequest();
		$objSession       = $objRequest->getSession();
		$objCliente       = $objSession->get('ptoCliente');
		$intEmpresaId     = $objSession->get('idEmpresa');

        $arrayParametros  = array();
        $arrayParametros["ESTADOS"]        = 'Todos';
        $arrayParametros["strFechaDesde"]  = explode('T',$objRequest->get('fechaDesde'))[0];
        $arrayParametros["strFechaHasta"]  = explode('T',$objRequest->get('fechaHasta'))[0];
        $arrayParametros["strProducto"]    = $objRequest->get('producto');
        $arrayParametros["strEstado"]      = $objRequest->get('strEstado');
        $arrayParametros['PUNTO']          = $objCliente['id'];
        $arrayParametros['EMPRESA']        = $intEmpresaId;
        $arrayParametros['START']          = 0;
        $arrayParametros['LIMIT']          = 10;
        $intTotal                          = 0;
        
        
        $arrayListado = $emGeneral->getRepository('schemaBundle:InfoServicio')->getResultadoServiciosPorEmpresaPorPunto($arrayParametros);
        if(!empty($arrayListado))
        {
            foreach($arrayListado as $dato):
                $arrayParametrosDet =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("SEGUIMIENTO_PRODUCTOS", 
                                                                         "COMERCIAL", 
                                                                         "", 
                                                                         "", 
                                                                         $dato->getProductoId()->getDescripcionProducto(), 
                                                                         "", 
                                                                         "",
                                                                         "",
                                                                         "",
                                                                         $intEmpresaId
                                                                       );
                
                if(!empty($arrayParametrosDet))
                {
                if( $intTotal>=1)
                {
                    $strDivPaneles .=',';
                }else
                {
                    $strDivPaneles ='{"data":[{"title":"default","hidden":true},';
                }
                $strTitulo  ="";
                $strDiv     ="";
                $strTitulo  = '{"title"'.':'.'"'.$dato->getProductoId()->getDescripcionProducto().'","id":'.$dato->getId();
                $strDiv     = '"html":"<div  class=seguimiento_content id=seguimiento_content_'.$dato->getId().'></div>';
                $strDiv    .= '<table width=100% cellpadding=1 cellspacing=0  border=0><tr>'
                    . '<td><div overflow=scroll, id=getPanelSeguimiento'.$dato->getId().'></div></td></tr></table>"';
                $strExpand  = 'listeners: { expand: function() {grafica('.$dato->getId().'); '
                    . 'seguimiento_content_'.$dato->getId().'.Update();getPanelSeguimiento'.$dato->getId().'.Update();}}}';
                $strDivPaneles .=$strTitulo.",".$strDiv.",".$strExpand;

                 $intTotal++;
                }
            endforeach;
        if (!empty($strDivPaneles))
        {
            $strDivPaneles .= "]}";
        }
            $arrayArreglo[] = array(
                'datos'                        => $strDivPaneles
                );
        }
        if(!empty($arrayArreglo))
        {
            $objResponse = new Response(json_encode(array('total' => $intTotal, 'servicios' => $arrayArreglo)));
        }
        else
        {
            $arrayArreglo[] = array();
            $objResponse = new Response(json_encode(array('total' => $intTotal, 'servicios' => $arrayArreglo)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
        
        }
    /* 
     * Documentación para la función 'graficaServicioAction'.
     *
     * Función que devuelve los estado por los cuales pasa el servicio.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 26-02-2020
     * 
     * @return Response se retorna los estado del servicio a consultar.
     *
     */
    public function graficaServicioAction ()
    {
        $emGeneral        = $this->getDoctrine()->getManager();
		$objRequest       = $this->get('request');
        $objRequest       = $this->getRequest();
		$objSession       = $objRequest->getSession();
		$intEmpresaId     = $objSession->get('idEmpresa');
        $intServicioId    = $objRequest->get('objServicio');
        $arrayEstado      = array();
        $intTotal         =   0;
        
        if (empty($intServicioId))
        {
            throw $this->createNotFoundException('No existe el Servicio.');
        }
        $objServicio    =   $emGeneral->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
        if (!empty($objServicio) && $objServicio!='')
        {
            $strDescripcionProd =   $objServicio->getProductoId()->getDescripcionProducto();
                if($objServicio->getEstado()!="" &&  ($strDescripcionProd=='L3MPLS' ||
                    $strDescripcionProd=='Internet Dedicado' || $strDescripcionProd=='Internet MPLS') )
                {
                    if(empty($strDescripcionProd) && $strDescripcionProd='')
                    {
                        throw new \Exception("No se encuentra la Descripción del Producto."); 
                    }
                    //CONSULTAMOS EL PRODUCTO Y SUS ESTADOS
                    $arrayParametrosDet =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("SEGUIMIENTO_PRODUCTOS", 
                                                                         "COMERCIAL", 
                                                                         "", 
                                                                         "", 
                                                                         $strDescripcionProd, 
                                                                         "", 
                                                                         "",
                                                                         "",
                                                                         "",
                                                                         $intEmpresaId
                                                                       );
                    if(!is_array($arrayParametrosDet) && empty($arrayParametrosDet))
                    {
                        throw $this->createNotFoundException('Unable to find InfoServicio entity.');
                    }    
                    $strFlujo   =   $arrayParametrosDet['valor2'];
                    $arrayParametrosDetEst =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get("ESTADOS_SEGUIMIENTO_PRODUCTOS", 
                                                                     "COMERCIAL", 
                                                                     "", 
                                                                     "", 
                                                                     "", 
                                                                     $strFlujo, 
                                                                     "",
                                                                     "",
                                                                     "",
                                                                     $intEmpresaId,
                                                                     "valor1"
                                                                   );

                    if(!is_array($arrayParametrosDetEst) && empty($arrayParametrosDetEst))
                    {
                        throw $this->createNotFoundException('No existe el Detalle de Estado de Seguimiento.');
                    }
                    $arrayEstadoSegui   = array();
                    foreach($arrayParametrosDetEst as $arrayEstadoServicio)
                    {
                        if($arrayEstadoServicio['valor7']=='true')
                        {
                        $arrayEstado   =  array ($arrayEstadoServicio['valor3'],$arrayEstadoServicio['valor3'],$arrayEstadoServicio['valor4'],
                                                 $arrayEstadoServicio['valor5'],$arrayEstadoServicio['valor6']);
                        }
                        array_push($arrayEstadoSegui,$arrayEstado);
                        $intTotal++;
                    }    
                    if(!empty($arrayEstadoSegui))
                    {
                        $arrayEstado = $arrayEstadoSegui;
                    }
                    
                }
        }
        $objResponse = new Response(json_encode(array_values($arrayEstado)));
        
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /* 
     * Documentación para la función 'getDetallesServiciosAction'.
     *
     * Función que devuelve los mensajes de los Tooltip a mostrarce en el grafico.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 26-02-2020
     * 
     * @return Response se retorna los tooltip a mostrarce.
     *
     */
    public function getDetallesServiciosAction()
    {
        $objRequest        = $this->get('request');
		$objSession        = $objRequest->getSession();
		$intEmpresaId      = $objSession->get('idEmpresa');
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();

        $emComercial = $this->getDoctrine()->getManager("telconet");

        $arrayParametros['intIdSolicitud'] = $objRequest->get('intIdSolicitud');
        $arrayParametros['strCodigosEstaciones'] = $objRequest->get('strCodigosEstaciones');
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        
        $arrayResultado = array();
        
        try
        {
            if(!empty($arrayParametros['intIdSolicitud']))
            {
                $objDetalleServicio    = $emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['intIdSolicitud']);
                
                
                if(is_object($objDetalleServicio) && !empty($objDetalleServicio))
                {
                    $arrayParametrosDet =   $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("SEGUIMIENTO_PRODUCTOS", 
                                                                         "COMERCIAL", 
                                                                         "", 
                                                                         "", 
                                                                         $objDetalleServicio->getProductoId()->getDescripcionProducto(), 
                                                                         "", 
                                                                         "",
                                                                         "",
                                                                         "",
                                                                         $intEmpresaId
                                                                       );
                    if(!is_array($arrayParametrosDet) && empty($arrayParametrosDet))
                    {
                        throw $this->createNotFoundException('Unable to find InfoServicio entity.');
                    }    
                    $strFlujo   =   $arrayParametrosDet['valor2'];
                    $arrayParametrosEstServ =   $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get("ESTADOS_SEGUIMIENTO_PRODUCTOS", 
                                                                     "COMERCIAL", 
                                                                     "", 
                                                                     "", 
                                                                     "", 
                                                                     $strFlujo, 
                                                                     $objDetalleServicio->getEstado(),
                                                                     "",
                                                                     "",
                                                                     $intEmpresaId,
                                                                     "valor1"
                                                                   );

                    if(!is_array($arrayParametrosEstServ) && empty($arrayParametrosEstServ))
                    {
                        throw $this->createNotFoundException('No existe el Detalle de Estado de Seguimiento.');
                    }
                    
                    $arrayEstados        = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get("ESTADOS_SEGUIMIENTO_PRODUCTOS", 
                                                                     "COMERCIAL", 
                                                                     "", 
                                                                     "", 
                                                                     "", 
                                                                     $strFlujo, 
                                                                     "",
                                                                     "",
                                                                     "",
                                                                     $intEmpresaId,
                                                                     "valor1"
                                                                   );
                    foreach ($arrayEstados as $estacion) 
                    {
                        $strHtml ="";
                        $strEstado =  "inactivo";    
                        switch ($estacion['valor3']) 
                        {
                            case "Pre-servicio":
                                $intEstadoIni = intval($estacion['valor1']);
                                $intValor1 = intval($arrayParametrosEstServ[0]['valor1']);
                                if($intEstadoIni <= $intValor1)
                                {
                                    $strHtml = "<p class='estacionItem ei_success'>La Solicitud fue creada exitosamente!</p>";
                                    $strEstado = "activo";
                                }                              
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "Factible":
                                $arrayFactible = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'Factible'));
                                if($estacion['valor1'] < $arrayParametrosEstServ[0]['valor1'])
                                {
                                    $objPreFactibi = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findOneBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'PreFactibilidad'));
                                    
                                    $objFactibiProceso = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findOneBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'FactibilidadEnProceso'));
                                    
                                    if(!is_object($objPreFactibi) && empty($objPreFactibi) && 
                                        !is_object($objFactibiProceso) && empty($objFactibiProceso))
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Factibilidad Automatica del Servicio!</p>";

                                    }
                                    else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Prefactibilidad Atendida!</p>";
                                        $strHtml .= "<p class='estacionItem ei_success'>FactibilidadEnProceso Atendida!</p>";
                                    }
                                    
                                    $strEstado = "activo";
                                }else
                                {
                                    if($objDetalleServicio->getEstado()=='FactibilidadEnProceso')
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Prefactibilidad Atendida!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>FactibilidadEnProceso Pendiente!</p>";
                                    }
                                    else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_info'>Prefactibilidad Pendiente!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>FactibilidadEnProceso Pendiente!</p>";
                                    }
                                }
                                $strHtml .= "<p class='estacionItem ei_info'>Total de veces Factible: ".count($arrayFactible)."</p>";
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "Planificada":
                                $arrayPrePlanificado = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'PrePlanificada'));
                                    
                                $arrayReplanificado = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'Replanificada'));
                                if($estacion['valor1'] < $arrayParametrosEstServ[0]['valor1'])
                                {
                                    if(empty($arrayReplanificado))
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Preplanificación realizada!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Total de veces Preplanificado: "
                                                                                                                .count($arrayPrePlanificado)."</p>";
                                    }
                                    else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Preplanificación realizada!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Total de veces Preplanificado: "
                                                                                                                .count($arrayPrePlanificado)."</p>";
                                        $strHtml .= "<p class='estacionItem ei_success'>Replanificación Realizada!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Total de veces Replanificado: "
                                                                                                                  .count($arrayReplanificado)."</p>";
                                    }

                                    $strEstado = "activo";
                                }else
                                {
                                    if($objDetalleServicio->getEstado()=='Replanificada')
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Preplanificación Atendida!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Total de veces Preplanificado: "
                                                                                                                .count($arrayPrePlanificado)."</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Replanificación Pendiente!</p>";
                                    }
                                    else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_info'>Preplanificación Pendiente!</p>";
                                    }
                                }
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "AsignadoTarea":
                                $arrayAsignadoTare = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'AsignadoTarea'));
                                if($estacion['valor1'] < $arrayParametrosEstServ[0]['valor1'])
                                {
                                    $strHtml .= "<p class='estacionItem ei_success'>Asignación de Recursos de Red Realizada</p>";
                                    $strEstado = "activo";
                                }else
                                {
                                    $strHtml .= "<p class='estacionItem ei_info'>Asignación de Recursos de Red Pendiente</p>";
                                    
                                }
                                $strHtml .= "<p class='estacionItem ei_info'>Total de veces AsignadoTarea: ".count($arrayAsignadoTare)."</p>";
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "Asignada":
                                
                                if($estacion['valor1'] < $arrayParametrosEstServ[0]['valor1'])
                                {
                                    $strHtml .= "<p class='estacionItem ei_success'>Asignación de Equipo Realizada</p>";
                                    $strEstado = "activo";
                                }else
                                {
                                    $strHtml .= "<p class='estacionItem ei_info'>Asignación de Equipo Pendiente</p>";
                                    
                                }
                                
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "Activo":
                                                            
                                if($estacion['valor1'] <= $arrayParametrosEstServ[0]['valor1'])
                                {
                                    $strHtml .= "<p class='estacionItem ei_success'>Activación Realizada</p>";
                                    $strEstado = "activo";
                                }else
                                {
                                    $strHtml .= "<p class='estacionItem ei_info'>Activación Pendiente</p>";
                                    
                                }
                                
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            default:
                                $strHtml ="<p class='estacionItem ei_error'>Estación no Encontrada</p>";
                                $arrayResultado[$estacion] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                        }                        
                    }
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el código de la solicitud.');
            }
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus(json_encode($arrayResultado));
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    /* 
     * Documentación para la función 'getHistorialSeguimientoAction'.
     *
     * Función que devuelve el historial a mostrarce.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 26-02-2020
     * 
     * @return Response se retorna el historial del servicio a consultar.
     *
     */
    public function getHistorialSeguimientoAction()
    {
        $objRequest          = $this->getRequest();
        $intContador         = 0;
        $emComercial         = $this->getDoctrine()->getManager("telconet");
        $objResponse         = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $intServicioId = $objRequest->get('objServicio');

        $arrayResultado       = array();
        $arrayEncontrados     = array();
            if(!empty($intServicioId) && $intServicioId != 0)
            {
                $arrayParametros    =   array();
                $arrayParametros['intServicioId']    = intval($intServicioId);
                $arrayResultado     = $emComercial->getRepository('schemaBundle:InfoSeguimientoServicio')->historialSeguimiento($arrayParametros);
            }
        if(!empty($arrayResultado) && $arrayResultado['strStatus']=='OK')
        {
            $objSeguimientos = $arrayResultado['arrayData'];
           foreach($objSeguimientos as $data)
           {
               $intMinutosTrans =0;
               $intDiasTrans    =0;
               if(is_null($data->getTiempoTranscurrido()))
               {
                   $objActual = new \DateTime('now');
                   $intMinDiff = $data->getFeCreacion()->diff($objActual);
                   $intMinutosTrans = ( ($intMinDiff->days * 24 ) * 60 ) +($intMinDiff->h  * 60 )+ ( $intMinDiff->i );
                   $intDiasTrans    = number_format($intMinutosTrans/24/60, 2, '.', '');
               }
               else
               {
                   $intMinutosTrans = $data->getTiempoTranscurrido();
                   $intDiasTrans    = number_format($data->getDiasTranscurrido(), 2, '.', '');
               }
               $intContador++;
               $arrayEncontrados[] = array(
                        'usrCreacion'          => $data->getUsrCreacion(),
                        'servicioId'           => $data->getServicioId()->getId(),
                        'observacion'          => $data->getObservacion(),
                        'departamento'         => $data->getDepartamento(),
                        'estado'               => $data->getEstado(),
                        
                        'feCreacion'           => strval(date_format($data->getFeCreacion(),"d/m/Y G:i")),
                        'feModificacion'       => strval(date_format($data->getFeModificacion(),"d/m/Y G:i")),
                        'ipModificacion'       => $data->getIpCreacion(),
                        'tiempoEstimado'       => $data->getTiempoEstimado(),
                        'tiempoTranscurrido'   => $intMinutosTrans,
                        'diasTranscurrido'     => $intDiasTrans
                   );
           }
           $arrayDatos = json_encode($arrayEncontrados);
                $objJson = '{"total":"' . $intContador . '","encontrados":' . $arrayDatos . '}';
        $objResponse->setContent($objJson);
                   
        }
        return $objResponse;
    }
}
